<?php
/**
 * @package user
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2011 by Roman Konertz
 * @license GPLv3
 * 
 * This file is part of Open-LIMS
 * Available at http://www.open-lims.org
 * 
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;
 * version 3 of the License.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 
 */
require_once("interfaces/group.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/group_already_exist_exception.class.php");
	require_once("exceptions/group_creation_failed_exception.class.php");
	require_once("exceptions/group_not_found_exception.class.php");
	
	require_once("events/group_create_event.class.php");
	require_once("events/group_delete_event.class.php");
	require_once("events/group_post_delete_event.class.php");
	require_once("events/group_rename_event.class.php");
	
	require_once("access/group.access.php");
	require_once("access/group_has_user.access.php");
}

/**
 * Group Management Class
 * @package user
 */
class Group implements GroupInterface
{

	private $group_id;
	private $group;
	
	/**
	 * @param integer $group_id Group-ID
	 */
	function __construct($group_id)
	{
		if ($group_id == null)
		{
			$this->group_id = null;
			$this->group = new Group_Access(null);
		}
		else
		{
			$this->group_id = $group_id;
			$this->group = new Group_Access($group_id);
		}
	}
	

	function __destruct()
	{
		unset($this->group_id);
		unset($this->group);
	}
	
	/**
	 * Creates a new user including all needed dependencies
	 * @param string $name
	 * @return integer
	 * @throws GroupAlreadyExistException
	 * @throws GroupCreationFailedException
	 */
	public function create($name)
	{
		global $transaction;
		
		if ($this->group)
		{
			if ($name)
			{
				$transaction_id = $transaction->begin();

				if (self::exist_name($name) == true)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new GroupAlreadyExistException("",4);
				}
				
				if (($group_id = $this->group->create($name)) != null)
				{
					$this->__construct($group_id);
					
					$group_create_event = new GroupCreateEvent($group_id);
					$event_handler = new EventHandler($group_create_event);
					
					if ($event_handler->get_success() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new GroupCreationFailedException("",1);
					}
					else
					{
						$transaction->commit($transaction_id);
					}
					
					return $group_id;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new GroupCreationFailedException("",3);
				}
			}
			else
			{
				throw new GroupCreationFailedException("",3);
			}
		}
		else
		{
			throw new GroupCreationFailedException("",3);
		}
		
	}
	
	/**
	 * Deletes a group
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->group)
		{
		
			$transaction_id = $transaction->begin();
		
			// User-Connection
			if (GroupHasUser_Access::delete_by_group_id($this->group_id) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			$group_delete_event = new GroupDeleteEvent($this->group_id);
			$event_handler = new EventHandler($group_delete_event);
			
			if ($event_handler->get_success() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
								
			// Group
			if ($this->group->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
				
			$group_post_delete_event = new GroupPostDeleteEvent($this->group_id);
			$event_handler = new EventHandler($group_post_delete_event);
			
			if ($event_handler->get_success() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				return true;
			}
		}
		else
		{
			return false;	
		}		
		
	}
	
	/**
	 * Checks if a user in group
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function is_user_in_group($user_id)
	{
		if (is_numeric($user_id))
		{
			$user_array = GroupHasUser_Access::list_users_by_group_id($this->group_id);
			
			if (in_array($user_id, $user_array))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
		
	}		

	/**
	 * Links a new user to the group
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function create_user_in_group($user_id)
	{
		if ($this->group_id and $this->group)
		{
			if ($this->is_user_in_group($user_id) == false)
			{
				$group_has_user = new GroupHasUser_Access(null);
				if ($group_has_user->create($this->group_id, $user_id) != null)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
	
	/**
	 * Deletes an user from the group
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function delete_user_from_group($user_id)
	{
		if ($this->group_id and $this->group)
		{
			if ($this->is_user_in_group($user_id) == true)
			{
				$primary_key = GroupHasUser_Access::get_pk_by_user_id_and_group_id($user_id, $this->group_id);
				$group_has_user = new GroupHasUser_Access($primary_key);
				if ($group_has_user->delete() == true)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * @return integer Number of Members
	 */
	public function get_number_of_user_members()
	{
		if ($this->group_id)
		{
			$number_of_users = GroupHasUser_Access::count_users_by_group_id($this->group_id);
			if (is_numeric($number_of_users))
			{
				return $number_of_users;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return null;
		}
	}	
	
	/**
	 * @return string Group Name
	 */
	public function get_name()
	{
		if ($this->group)
		{
			if ($this->group->get_name())
			{
				return $this->group->get_name();
			}
			else
			{
				return "System";
			}
		}
		else
		{
			return "System";
		}
	}
	
	/**
	 * @param string $name Group Name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $transaction;
		
		if ($this->group and $this->group_id and $name)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->group->set_name($name) == true)
			{
				$group_rename_event = new GroupRenameEvent($this->group_id);
				$event_handler = new EventHandler($group_rename_event);
				
				if ($event_handler->get_success() == true)
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
		}
		else
		{
			return false;
		}
		
	}
	
	
	/**
	 * @param integer $user_id User-ID
	 * @return integer Number of memberships of an user
	 */
	public static function get_number_of_groups_by_user_id($user_id)
	{
		$number_of_groups = GroupHasUser_Access::count_groups_by_user_id($user_id);
		if (is_numeric($number_of_groups))
		{
			return $number_of_groups;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * @return integer Number of all Groups
	 */
	public static function get_number_of_groups()
	{
		return Group::get_number_of_groups();
	}

	/**
	 * Checks if a group exists by name
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name)
	{
		$name = trim(strtolower($name));
		if (Group_Access::get_group_id_by_name($name) != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/** 
	 * Checks if a group exists by id
	 * @param integer $group_id
	 * @return bool
	 */
	public static function exist_group($group_id)
	{
		return Group_Access::exist_group($group_id);
	}
	
	/**
	 * @return array Array of all Groups
	 */
	public static function list_groups()
	{
		return Group_Access::list_entries();
	}
	
	/**
	 * @param integer $user_id User-ID
	 * @return array Array of all related groups
	 */
	public static function list_user_releated_groups($user_id)
	{
		return GroupHasUser_Access::list_groups_by_user_id($user_id);
	}
	
	/**
	 * @param integer $group_id Group-ID
	 * @return array Array of all related users
	 */
	public static function list_group_releated_users($group_id)
	{
		return GroupHasUser_Access::list_users_by_group_id($group_id);
	}

	/**
	 * Searchs groups via groupname
	 * @param string $groupname
	 * @return array Array of Group-IDs
	 */
	public static function search_groups($groupname)
	{
   		if ($groupname)
   		{
   			$groupname = strtolower($groupname);
   			return Group_Access::search_groups($groupname);
   		}
   		else
   		{
   			return null;
   		}
   	}

	/**
	 * @return integer
	 */
   	public static function count_groups()
   	{
   		return Group_Access::count_groups();
   	}
}
?>