<?php
/**
 * @package organisation_unit
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
require_once("interfaces/organisation_unit.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/organisation_unit_already_exist_exception.class.php");
	require_once("exceptions/organisation_unit_creation_failed_exception.class.php");
	require_once("exceptions/organisation_unit_not_found_exception.class.php");
	
	require_once("events/organisation_unit_create_event.class.php");
	require_once("events/organisation_unit_delete_event.class.php");
	require_once("events/organisation_unit_delete_precheck_event.class.php");
	require_once("events/organisation_unit_post_delete_event.class.php");
	require_once("events/organisation_unit_rename_event.class.php");
	require_once("events/organisation_unit_change_owner_event.class.php");
	require_once("events/organisation_unit_leader_create_event.class.php");
	require_once("events/organisation_unit_leader_delete_event.class.php");
	require_once("events/organisation_unit_quality_manager_create_event.class.php");
	require_once("events/organisation_unit_quality_manager_delete_event.class.php");
	require_once("events/organisation_unit_group_create_event.class.php");
	require_once("events/organisation_unit_group_delete_event.class.php");
	
	require_once("access/organisation_unit.access.php");
	require_once("access/organisation_unit_has_group.access.php");
	require_once("access/organisation_unit_has_leader.access.php");
	require_once("access/organisation_unit_has_member.access.php");
	require_once("access/organisation_unit_has_owner.access.php");
	require_once("access/organisation_unit_has_quality_manager.access.php");
	require_once("access/organisation_unit_type.access.php");
}

/**
 * Organisation Unit Class
 * @package organisation_unit
 * @todo LATER: Super-Class = split
 */
class OrganisationUnit implements OrganisationUnitInterface, EventListenerInterface
{
	private $organisation_unit_id;
	private $organisation_unit;

	/**
	 * @param integer $organisation_unit_id Organisation-Unit-ID
	 */
	function __construct($organisation_unit_id)
	{
		if ($organisation_unit_id == null)
		{
    		$this->organisation_unit_id = null;
			$this->organisation_unit	= new OrganisationUnit_Access(null);
		}
		else
		{		
			$this->organisation_unit_id = $organisation_unit_id;
			$this->organisation_unit	= new OrganisationUnit_Access($organisation_unit_id);
    	}
	}
	
	function __destruct()
	{
		unset($this->organisation_unit_id);
		unset($this->organisation_unit);
	}
	
	/**
	 * Creates a new Organisation-Unit including all dependencies
	 * @param integer $toid Parent-ID
	 * @param string $name
	 * @param integer $type_id
	 * @param bool $stores_data
	 * @return integer
	 * @throws OrganisationUnitAlreadyExistException
	 * @throws OrganisationUnitCreationFailedException
	 */
	public function create($toid, $name, $type_id, $stores_data) 
	{
		global $transaction, $session;
		
		if ($this->organisation_unit)
		{
			if ($name and is_numeric($type_id) and is_bool($stores_data))
			{
				$transaction_id = $transaction->begin();

				if (self::exist_name($name) == true)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new OrganisationUnitAlreadyExistException("",2);
				}
				
				$current_highest_position_ou_id = OrganisationUnit_Access::get_highest_position_by_toid($toid);
				$current_highest_position_ou = new OrganisationUnit_Access($current_highest_position_ou_id);
				
				if ($current_highest_position_ou->get_position())
				{
					$new_highest_position = $current_highest_position_ou->get_position()+1;
				}
				else
				{
					$new_highest_position = 1;
				}
				
				if (($organisation_unit_id = $this->organisation_unit->create($toid, $name, $type_id, $stores_data, $new_highest_position)) != null)
				{
					$this->__construct($organisation_unit_id);
					
					// Create Owner
					if ($this->create_owner_in_organisation_unit($session->get_user_id(), true) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new OrganisationUnitCreationFailedException("",1);
					}
					
					// Create Leader
					if ($this->create_leader_in_organisation_unit($session->get_user_id()) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new OrganisationUnitCreationFailedException("",1);
					}
					
					$organisation_unit_create_event = new OrganisationUnitCreateEvent($organisation_unit_id, $stores_data);
					$event_handler = new EventHandler($organisation_unit_create_event);
					
					if ($event_handler->get_success() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new OrganisationUnitCreationFailedException("",1);
					}
					else
					{
						$transaction->commit($transaction_id);
					}
					
					return $organisation_unit_id;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new OrganisationUnitCreationFailedException("",1);
				}
			}
			else
			{
				throw new OrganisationUnitCreationFailedException("",1);
			}
		}
		else
		{
			throw new OrganisationUnitCreationFailedException("",1);
		}
		
	}
	
	/**
	 * Deletes an Organisation-Unit
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->organisation_unit)
		{
			if ($this->check_delete_dependencies() == true)
			{
				$transaction_id = $transaction->begin();
			
				// User-Connection
				if ($this->delete_members() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				
				// Group-Connection
				if ($this->delete_groups() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				// Organisation Unit Position
				if ($this->organisation_unit->get_next_position() != $this->organisation_unit_id)
				{
					
					$tmp_organisation_unit = $this->organisation_unit;
					$tmp_organisation_unit_id = $this->organisation_unit_id;
					$tmp_position = $this->organisation_unit->get_position();
					
					while(($next_organisation_unit_id = $tmp_organisation_unit->get_next_position()) != $tmp_organisation_unit_id)
					{
						$next_organisation_unit = new OrganisationUnit_Access($next_organisation_unit_id);
						$next_position = $next_organisation_unit->get_position();
						if ($next_organisation_unit->set_position($tmp_position) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
						else
						{
							$tmp_organisation_unit = $next_organisation_unit;
							$tmp_organisation_unit_id = $next_organisation_unit_id;
							$tmp_position = $next_position;
						}
					}
					
				}
				
				// Event
				$stores_data = $this->organisation_unit->get_stores_data();
				$organisation_unit_delete_event = new OrganisationUnitDeleteEvent($this->organisation_unit_id, $stores_data);
				$event_handler = new EventHandler($organisation_unit_delete_event);
				
				if ($event_handler->get_success() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}			
													
				if ($this->organisation_unit->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}

				$organisation_unit_post_delete_event = new OrganisationUnitPostDeleteEvent($this->organisation_unit_id, $stores_data);
				$event_handler = new EventHandler($organisation_unit_post_delete_event);
				
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
		else
		{
			return false;	
		}		
		
	}
	
	/**
	 * Checks dependencies before Organisation-Unit deletion.
	 * @return bool
	 */
	public function check_delete_dependencies()
	{
		if ($this->organisation_unit_id)
		{
		
			$organisation_unit_delete_precheck_event = new OrganisationUnitDeletePrecheckEvent($this->organisation_unit_id);
			$event_handler = new EventHandler($organisation_unit_delete_precheck_event);
			
			if ($event_handler->get_success() == false)
			{
				return false;
			}
			
			// Organisation-Unit Childs	
			$organisation_unit_array = OrganisationUnit_Access::list_entries_by_toid($this->organisation_unit_id);
			if (is_array($organisation_unit_array))
			{
				if (count($organisation_unit_array) >= 1)
				{
					return false;
				}
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks permissions on Organisation-Unit
	 * @param integer $user_id
	 * @return bool
	 */
	public function is_permission($user_id)
	{
		global $user;
		
		if ($this->organisation_unit)
		{
			if ($user->is_admin())
			{
				return true;
			}
			else
			{
				if ($this->is_leader_in_organisation_unit($user_id) == true)
				{
					return true;
				}
				else
				{
					$organisation_unit_has_member_array = OrganisationUnitHasMember_Access::list_members_by_organisation_unit_id($this->organisation_unit_id, 0);
					foreach($organisation_unit_has_member_array as $key => $value)
					{
						if ($value == $user_id)
						{
							return true;
						}
					}				
				}
			}
		}
		return false;
	}
	
	/**
	 * Checks if the OU is on the upper position
	 * @return bool
	 */
	public function is_upper_position()
	{
		if ($this->organisation_unit and $this->organisation_unit_id)
		{
			if ($this->organisation_unit->get_previous_position() == $this->organisation_unit_id)
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
	 * Checks if the OU is on the lower position
	 * @return bool
	 */
	public function is_lower_position()
	{
		if ($this->organisation_unit)
		{
			if ($this->organisation_unit->get_next_position() == $this->organisation_unit_id)
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
	 * Moves the OU one position upwards
	 * @return bool
	 */
	public function position_upwards()
	{
		global $transaction;
		
		if ($this->organisation_unit and $this->organisation_unit_id)
		{
			if ($this->is_upper_position() == false)
			{
				$upper_position = $this->organisation_unit->get_position()-1;
				
				if ($this->organisation_unit->get_is_root() == true)
				{
					$id = OrganisationUnit_Access::get_id_by_position_and_toid($upper_position, null);
				}
				else
				{
					$id = OrganisationUnit_Access::get_id_by_position_and_toid($upper_position, $this->organisation_unit->get_toid());
				}
				
				if (is_numeric($id))
				{
					$transaction_id = $transaction->begin();
					
					$change_organisation_unit = new OrganisationUnit_Access($id);
					if ($change_organisation_unit->set_position($this->organisation_unit->get_position()) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->organisation_unit->set_position($upper_position) == false)
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
	 * Moves the OU one position downwards
	 * @return bool
	 */
	public function position_downwards()
	{
		global $transaction;
		
		if ($this->organisation_unit and $this->organisation_unit_id)
		{
			if ($this->is_lower_position() == false)
			{
				$lower_position = $this->organisation_unit->get_position()+1;
				
				if ($this->organisation_unit->get_is_root() == true)
				{
					$id = OrganisationUnit_Access::get_id_by_position_and_toid($lower_position, null);
				}
				else
				{
					$id = OrganisationUnit_Access::get_id_by_position_and_toid($lower_position, $this->organisation_unit->get_toid());
				}
				
				if (is_numeric($id))
				{
					$transaction_id = $transaction->begin();
					
					$change_organisation_unit = new OrganisationUnit_Access($id);
					if ($change_organisation_unit->set_position($this->organisation_unit->get_position()) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->organisation_unit->set_position($lower_position) == false)
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
	 * Checks if an User is in the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function is_user_in_organisation_unit($user_id)
	{
		if (is_numeric($user_id))
		{
			$user_array = OrganisationUnitHasMember_Access::list_members_by_organisation_unit_id($this->organisation_unit_id, 0);
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
	 * Creates an User in the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function create_user_in_organisation_unit($user_id)
	{
		if ($this->organisation_unit_id)
		{
			if ($this->is_user_in_organisation_unit($user_id) == false)
			{
				$organisation_unit_has_user = new OrganisationUnitHasMember_Access(null, null);
				if ($organisation_unit_has_user->create($this->organisation_unit_id, $user_id) == true)
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
		else
		{
			return false;
		}
	}
	
	/**
	 * Deletes an User from the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function delete_user_from_organisation_unit($user_id)
	{
		if ($this->organisation_unit_id)
		{
			if ($this->is_user_in_organisation_unit($user_id) == true)
			{
				$organisation_unit_has_user = new OrganisationUnitHasMember_Access($this->organisation_unit_id, $user_id);
				if ($organisation_unit_has_user->delete() == true)
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
		else
		{
			return false;
		}
	}

	/**
	 * @return integer
	 */
	public function get_number_of_users()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasMember_Access::count_members_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Checks if a Group is in the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function is_group_in_organisation_unit($group_id)
	{
		if (is_numeric($group_id))
		{
			$group_array = OrganisationUnitHasGroup_Access::list_groups_by_organisation_unit_id($this->organisation_unit_id, 0);
			if (in_array($group_id, $group_array))
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
	 * Creates a Group in the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function create_group_in_organisation_unit($group_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->is_group_in_organisation_unit($group_id) == false)
			{
				$organisation_unit_has_group = new OrganisationUnitHasGroup_Access(null, null);
				if ($organisation_unit_has_group->create($this->organisation_unit_id, $group_id) == true)
				{
					$organisation_unit_group_create_event = new OrganisationUnitGroupCreateEvent($this->organisation_unit_id, $group_id);
					$event_handler = new EventHandler($organisation_unit_group_create_event);
					
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
	 * Deletes a Group from the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function delete_group_from_organisation_unit($group_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->is_group_in_organisation_unit($group_id) == true)
			{
				$organisation_unit_has_group = new OrganisationUnitHasGroup_Access($this->organisation_unit_id, $group_id);
				
				if ($organisation_unit_has_group->delete() == true)
				{
					$organisation_unit_group_delete_event = new OrganisationUnitGroupDeleteEvent($this->organisation_unit_id, $group_id);
					$event_handler = new EventHandler($organisation_unit_group_delete_event);
					
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
	 * @return integer
	 */
	public function get_number_of_groups()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasGroup_Access::count_groups_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Checks if a Leader is in the OU
	 * @param integer $leader_id
	 * @return bool
	 */
	public function is_leader_in_organisation_unit($leader_id)
	{
		if (is_numeric($leader_id))
		{
			$leader_array = OrganisationUnitHasLeader_Access::list_leaders_by_organisation_unit_id($this->organisation_unit_id, 0);
			if (in_array($leader_id, $leader_array))
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
	 * Creates a Leader in the OU
	 * @param integer $leader_id
	 * @return bool
	 */
	public function create_leader_in_organisation_unit($leader_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->is_leader_in_organisation_unit($leader_id) == false)
			{
				if (defined("OU_GROUP_LEADER_GROUP"))
				{
					$group = new Group(constant("OU_GROUP_LEADER_GROUP"));
					
					if (self::is_leader($leader_id) == false)
					{
						if ($group->is_user_in_group($leader_id) == false)
						{
							if($group->create_user_in_group($leader_id) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
					}
				}
				
				$organisation_unit_has_leader = new OrganisationUnitHasLeader_Access(null, null);
				if ($organisation_unit_has_leader->create($this->organisation_unit_id, $leader_id) == true)
				{
					$organisation_unit_leader_create_event = new OrganisationUnitLeaderCreateEvent($this->organisation_unit_id, $leader_id);
					$event_handler = new EventHandler($organisation_unit_leader_create_event);
					
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
	 * Deletes a Leader from the OU
	 * @param integer $leader_id
	 * @return bool
	 */
	public function delete_leader_from_organisation_unit($leader_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->is_leader_in_organisation_unit($leader_id) == true)
			{
				$organisation_unit_has_leader = new OrganisationUnitHasLeader_Access($this->organisation_unit_id, $leader_id);
				
				if ($organisation_unit_has_leader->delete() == true)
				{
					$organisation_unit_leader_delete_event = new OrganisationUnitLeaderDeleteEvent($this->organisation_unit_id, $leader_id);
					$event_handler = new EventHandler($organisation_unit_leader_delete_event);
					
					if ($event_handler->get_success() == true)
					{
						if (defined("OU_GROUP_LEADER_GROUP"))
						{
							$group = new Group(constant("OU_GROUP_LEADER_GROUP"));
							
							if (self::is_leader($leader_id) == false)
							{
								if ($group->is_user_in_group($leader_id) == true)
								{
									if($group->delete_user_from_group($leader_id) == false)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										return false;
									}
								}
							}
						}
						
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
	 * @return integer
	 */
	public function get_number_of_leaders()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasLeader_Access::count_leaders_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Checks if an Owner is in the OU
	 * @param integer $owner_id
	 * @return bool
	 */
	public function is_owner_in_organisation_unit($owner_id)
	{
		if (is_numeric($owner_id))
		{
			$owner_array = OrganisationUnitHasOwner_Access::list_owners_by_organisation_unit_id($this->organisation_unit_id, 0);
			if (in_array($owner_id, $owner_array))
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
	 * @param integer $owner_id
	 * @param bool $master_owner
	 * @return bool
	 */
	public function create_owner_in_organisation_unit($owner_id, $master_owner = null)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			if ($this->is_owner_in_organisation_unit($owner_id) == false)
			{
				$transaction_id = $transaction->begin();
				
				$organisation_unit_has_owner = new OrganisationUnitHasOwner_Access(null, null);
				if ($organisation_unit_has_owner->create($this->organisation_unit_id, $owner_id) == true)
				{
					if (OrganisationUnitHasOwner_Access::count_owners_by_organisation_unit_id($this->organisation_unit_id) == 1)
					{
						if ($organisation_unit_has_owner->set_master_owner(true) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
						else
						{
							$organisation_unit_change_owner_event = new OrganisationUnitChangeOwnerEvent($this->organisation_unit_id);
							$event_handler = new EventHandler($organisation_unit_change_owner_event);
							
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
									$transaction->commit($transaction_id);
								}
								return false;
							}
						}
					}
					else
					{
						if ($master_owner == true)
						{
							$master_owner_id = OrganisationUnitHasOwner_Access::get_master_owner_id_by_organisation_unit_id($this->organisation_unit_id);
							if (is_numeric($master_owner_id))
							{
								$current_master_owner = new OrganisationUnitHasOwner_Access($this->organisation_unit_id, $master_owner_id);
								
								if ($current_master_owner->set_master_owner(false) == false)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									return false;
								}
								else
								{
									if ($organisation_unit_has_owner->set_master_owner(true) == false)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										return false;
									}
									else
									{
										$organisation_unit_change_owner_event = new OrganisationUnitChangeOwnerEvent($this->organisation_unit_id);
										$event_handler = new EventHandler($organisation_unit_change_owner_event);
										
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
								}
							}
							else
							{
								if ($organisation_unit_has_owner->set_master_owner(true) == false)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									return false;
								}
								else
								{
									$organisation_unit_change_owner_event = new OrganisationUnitChangeOwnerEvent($this->organisation_unit_id);
									$event_handler = new EventHandler($organisation_unit_change_owner_event);
									
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
							}
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
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public function delete_owner_from_organisation_unit($owner_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			if ($this->is_owner_in_organisation_unit($owner_id) == true)
			{
				$transaction_id = $transaction->begin();
				
				$organisation_unit_has_owner = new OrganisationUnitHasOwner_Access($this->organisation_unit_id, $owner_id);
				
				if ($organisation_unit_has_owner->get_master_owner() == true)
				{
					$master_owner = true;
				}
				else
				{
					$master_owner = false;
				}
				
				if ($organisation_unit_has_owner->delete() == true)
				{
					if ($master_owner == false)
					{
						if ($transaction_id != null)
						{
							$transaction->commit($transaction_id);
						}
						return true;
					}
					else
					{
						if (OrganisationUnitHasOwner_Access::count_owners_by_organisation_unit_id($this->organisation_unit_id) == 0)
						{
							$organisation_unit_change_owner_event = new OrganisationUnitChangeOwnerEvent($this->organisation_unit_id);
							$event_handler = new EventHandler($organisation_unit_change_owner_event);
							
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
							$next_master_user = OrganisationUnitHasOwner_Access::get_first_owner_id_by_organisation_unit_id($this->organisation_unit_id);
							
							$organisation_unit_has_owner = new OrganisationUnitHasOwner_Access($this->organisation_unit_id, $next_master_user);
							
							if ($organisation_unit_has_owner->set_master_owner(true) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
							else
							{
								$organisation_unit_change_owner_event = new OrganisationUnitChangeOwnerEvent($this->organisation_unit_id);
								$event_handler = new EventHandler($organisation_unit_change_owner_event);
								
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
						}
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
		else
		{
			return false;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_number_of_owners()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasOwner_Access::count_owners_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Checks if a Quality-Manager is in the OU
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public function is_quality_manager_in_organisation_unit($quality_manager_id)
	{
		if (is_numeric($quality_manager_id))
		{
			$quality_manager_array = OrganisationUnitHasQualityManager_Access::list_quality_managers_by_organisation_unit_id($this->organisation_unit_id, 0);
			if (in_array($quality_manager_id, $quality_manager_array))
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
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public function create_quality_manager_in_organisation_unit($quality_manager_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->is_quality_manager_in_organisation_unit($quality_manager_id) == false)
			{
				if (defined("OU_QUALITY_MANAGER_GROUP"))
				{
					$group = new Group(constant("OU_QUALITY_MANAGER_GROUP"));
					
					if (self::is_quality_manager($quality_manager_id) == false)
					{
						if ($group->is_user_in_group($quality_manager_id) == false)
						{
							if($group->create_user_in_group($quality_manager_id) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
					}
				}
				
				$organisation_unit_has_quality_manager = new OrganisationUnitHasQualityManager_Access(null, null);
				if ($organisation_unit_has_quality_manager->create($this->organisation_unit_id, $quality_manager_id) == true)
				{
					$organisation_unit_quality_manager_create_event = new OrganisationUnitQualityManagerCreateEvent($this->organisation_unit_id, $quality_manager_id);
					$event_handler = new EventHandler($organisation_unit_quality_manager_create_event);
					
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
	 * Deletes a Quality-Manager from the OU
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public function delete_quality_manager_from_organisation_unit($quality_manager_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->is_quality_manager_in_organisation_unit($quality_manager_id) == true)
			{
				$organisation_unit_has_quality_manager = new OrganisationUnitHasQualityManager_Access($this->organisation_unit_id, $quality_manager_id);
				
				if ($organisation_unit_has_quality_manager->delete() == true)
				{
					$organisation_unit_quality_manager_delete_event = new OrganisationUnitQualityManagerDeleteEvent($this->organisation_unit_id, $quality_manager_id);
					$event_handler = new EventHandler($organisation_unit_quality_manager_delete_event);
					
					if ($event_handler->get_success() == true)
					{
						if (defined("OU_QUALITY_MANAGER_GROUP"))
						{
							$group = new Group(constant("OU_QUALITY_MANAGER_GROUP"));
							
							if (self::is_quality_manager($quality_manager_id) == false)
							{
								if ($group->is_user_in_group($quality_manager_id) == true)
								{
									if($group->delete_user_from_group($quality_manager_id) == false)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										return false;
									}
								}
							}
						}
						
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
	 * @return integer
	 */
	public function get_number_of_quality_managers()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasQualityManager_Access::count_quality_managers_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string Type-Name
	 */
	public function get_type_name()
	{
		if ($this->organisation_unit)
		{
			$organisation_unit_type = new OrganisationUnitType_Access($this->organisation_unit->get_type_id());
			return $organisation_unit_type->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string OU-Name
	 */
	public function get_name()
	{
		if ($this->organisation_unit)
		{
			return $this->organisation_unit->get_name();
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return string Icon
	 */
	public function get_icon()
	{
		if ($this->organisation_unit)
		{
			$organisation_unit_type = new OrganisationUnitType_Access($this->organisation_unit->get_type_id());
			return $organisation_unit_type->get_icon();
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return integer
	 */
	public function get_master_owner_id()
	{
		if ($this->organisation_unit_id)
		{
			$master_owner_id = OrganisationUnitHasOwner_Access::get_master_owner_id_by_organisation_unit_id($this->organisation_unit_id);
			if ($master_owner_id)
			{
				return $master_owner_id;
			}
			else
			{
				return 1;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool Contains-Projects
	 */
	public function get_stores_data()
	{
		if ($this->organisation_unit)
		{
			return $this->organisation_unit->get_stores_data();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool Hidden
	 */
	public function get_hidden()
	{
		if ($this->organisation_unit)
		{
			return $this->organisation_unit->get_hidden();
		}
		else
		{
			return null;
		}
	}

	/**
	 * Return the status of an user
	 * @param integer $user_id
	 * @return string status
	 */
	public function get_user_status($user_id)
	{
		if ($this->is_leader_in_organisation_unit($user_id) == true)
		{			
			return "Leader";
		}
		else
		{
			return "Member";
		}		
	}

	/**
	 * @return array Array of childs
	 */
	public function get_organisation_unit_childs()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnit_Access::list_entries_by_toid($this->organisation_unit_id);
		}
		else
		{
			return null;	
		}
	}
		
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_members($limit = 0)
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasMember_Access::list_members_by_organisation_unit_id($this->organisation_unit_id, $limit);	
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_groups($limit = 0)
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasGroup_Access::list_groups_by_organisation_unit_id($this->organisation_unit_id, $limit);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_leaders($limit = 0)
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasLeader_Access::list_leaders_by_organisation_unit_id($this->organisation_unit_id, $limit);	
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_owners($limit = 0)
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasOwner_Access::list_owners_by_organisation_unit_id($this->organisation_unit_id, $limit);	
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_quality_managers($limit = 0)
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasQualityManager_Access::list_quality_managers_by_organisation_unit_id($this->organisation_unit_id, $limit);	
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $name New OU-Name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $transaction;
		
		if ($this->organisation_unit and $this->organisation_unit_id and $name)
		{
			$transaction_id = $transaction->begin();

			if ($this->organisation_unit->set_name($name) == true)
			{
				$organisation_unit_rename_event = new OrganisationUnitRenameEvent($this->organisation_unit_id);
				$event_handler = new EventHandler($organisation_unit_rename_event);
				
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
	 * @param bool $hidden
	 * @return bool
	 */
	public function set_hidden($hidden)
	{
		if ($this->organisation_unit and $this->organisation_unit_id and is_bool($hidden))
		{
			return $this->organisation_unit->set_hidden($hidden);
		}
		else
		{
			return false;
		}
	}
	

	/**
	 * Deletes all members of an OU
	 * @return bool
	 */
	private function delete_members()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasMember_Access::delete_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Deletes all groups of an OU
	 * @return bool
	 */
	private function delete_groups()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasGroup_Access::delete_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return false;
		}
	}
	
	
	
	/**
	 * @param integer $user_id User-ID
	 * @return integer
	 */
	public static function get_number_of_organisation_units_by_user_id($user_id)
	{
		$number_of_organisation_units = OrganisationUnitHasMember_Access::count_organisation_units_by_member_id($user_id);
		if (is_numeric($number_of_organisation_units))
		{
			return $number_of_organisation_units;
		}
		else
		{
			return 0;
		}
	}	
	
	/**
	 * @param integer $group_id Group-ID
	 * @return integer
	 */
	public static function get_number_of_organisation_units_by_group_id($group_id)
	{
		$number_of_organisation_units = OrganisationUnitHasGroup_Access::count_organisation_units_by_group_id($group_id);
		if (is_numeric($number_of_organisation_units))
		{
			return $number_of_organisation_units;
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * @param interger $type_id
	 * @return string Name of the Type
	 */
	public static function get_name_by_type_id($type_id)
	{
		if (is_numeric($type_id))
		{
			$organisation_unit_type = new OrganisationUnitType_Access($type_id);
			return $organisation_unit_type->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Checks if an OU exists
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name)
	{
		$name = trim(strtolower($name));
		if (OrganisationUnit_Access::get_organisation_unit_id_by_name($name) != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Checks if an OU exists
	 * @param integer $id Organisation-Unit-ID
	 * @return bool
	 */
	public static function exist_organisation_unit($id)
	{
		return OrganisationUnit_Access::exist_organisation_unit($id);
	}
	
	/**
	 * @return array List of root OUs
	 */		
	public static function list_organisation_unit_roots()
	{
		return OrganisationUnit_Access::list_root_entries();
	}
	
	/**
	 * @return array List of OUs
	 */
	public static function list_entries()
	{
		return OrganisationUnit_Access::list_entries();
	}
	
	/**
	 * @param integer $user_id User-ID
	 * @return array List of user OUs
	 */
	public static function list_entries_by_user_id($user_id)
	{
		return OrganisationUnitHasMember_Access::list_organisation_units_by_member_id($user_id);
	}
	
	/**
	 * @param integer $group_id Group-ID
	 * @return array List of group OUs
	 */
	public static function list_entries_by_group_id($group_id)
	{
		return OrganisationUnitHasGroup_Access::list_organisation_units_by_group_id($group_id);
	}
	
	/**
	 * @return array Array of OU-Types
	 */
	public static function list_types()
	{
		return OrganisationUnitType_Access::list_entries();
	}
	
	/**
	 * Checks if an User is an owner of any OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */	
	private static function is_owner($user_id)
	{
		if (is_numeric($user_id))
		{
			$organisaiton_unit_array = OrganisationUnitHasOwner_Access::list_organisation_units_by_owner_id($user_id);
			if (is_array($organisaiton_unit_array) and count($organisaiton_unit_array) >= 1)
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
	 * Checks if an User is a leader of any OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */	
	private static function is_leader($user_id)
	{
		if (is_numeric($user_id))
		{
			$organisaiton_unit_array = OrganisationUnitHasLeader_Access::list_organisation_units_by_leader_id($user_id);
			if (is_array($organisaiton_unit_array) and count($organisaiton_unit_array) >= 1)
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
	 * Checks if an User is a quality manager of any OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */	
	private static function is_quality_manager($user_id)
	{
		if (is_numeric($user_id))
		{
			$organisaiton_unit_array = OrganisationUnitHasQualityManager_Access::list_organisation_units_by_quality_manager_id($user_id);
			if (is_array($organisaiton_unit_array) and count($organisaiton_unit_array) >= 1)
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
   	 * @return integer
   	 */
   	public static function count_organisation_units()
   	{
   		return OrganisationUnit_Access::count_organisation_units();
   	}
	
	// --- TREE REC BLOCK BEGIN ---

	/**
	 * Creates an Tree of OUs recursively
	 * @param integer $id Organisation-Unit-ID
	 * @return array Array of Childs
	 */
	private static function make_organisation_unit_tree_path_rec($id)
	{
		if ($id)
		{
			$return_array = array();
			array_unshift($return_array,$id);
			
			$organisation_unit_access = new OrganisationUnit_Access($id);
			
			if ($organisation_unit_access->get_is_root() == false)
			{
				$return_array = array_merge(self::make_organisation_unit_tree_path_rec($organisation_unit_access->get_toid()),$return_array);
			}
						
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Create an Full Tree of OUs recursively
	 * @param interger $layer Layer of current run
	 * @param array $array Creates OU tree
	 * @return array
	 */
	private static function make_full_organisation_unit_tree_rec($layer, $array)
	{
		if (is_array($array))
		{
			$return_array = array();
			
			$organisation_unit_access = new OrganisationUnit_Access(null);
			$organisation_unit_access_array = $organisation_unit_access->list_entries_by_toid($array[$layer]);
			
			foreach($organisation_unit_access_array as $key => $value)
			{			
				if ($value != $array[$layer])
				{	
					$data_array = array();
					$data_array[id] = $value;
					$data_array[layer] = $layer+1;	
						
					array_push($return_array,$data_array);
					
					unset($data_array);
					
					if ($value == $array[$layer+1])
					{
						$return_array = array_merge($return_array,self::make_full_organisation_unit_tree_rec($layer+1, $array));
					}
				}
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Return the recursively created tree
	 * @return array
	 */
	public function get_organisation_unit_tree()
	{
		$tree_array = $this->make_organisation_unit_tree_path_rec($this->organisation_unit_id);

		$return_array = array();
	
		$organisation_unit_access_array = OrganisationUnit_Access::list_root_entries();
			
		foreach($organisation_unit_access_array as $key => $value)
		{
			$data_array = array();
			$data_array[id] = $value;
			$data_array[layer] = 0;
			
			array_push($return_array, $data_array);
			
			unset($data_array);
			if (is_array($tree_array))
			{
				if (in_array($value,$tree_array))
				{
					$return_array = array_merge($return_array,self::make_full_organisation_unit_tree_rec(0, $tree_array));
				}
			}
		}
		return $return_array;
	}
	
	// --- TREE REC BLOCK END ---
	   
    /**
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeletePrecheckEvent)
    	{
			if (self::is_owner($event_object->get_user_id()))
			{
				if (count($owner_array) >= 1)
				{
					return false;
				}
			}

			if (self::is_leader($event_object->get_user_id()))
			{
				if (count($leader_array) >= 1)
				{
					return false;
				}
			}
    	}
    	
    	if ($event_object instanceof UserDeleteEvent)
    	{
			if (OrganisationUnitHasMember_Access::delete_by_member_id($event_object->get_user_id()) == false)
			{
				return false;
			}
    	}
    	
   		if ($event_object instanceof GroupDeleteEvent)
    	{
			if (OrganisationUnitHasGroup_Access::delete_by_group_id($event_object->get_group_id()) == false)
			{
				return false;
			}
    	}

    	return true;
    }
    
}
?>