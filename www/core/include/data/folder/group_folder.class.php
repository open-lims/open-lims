<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("events/group_folder_create_event.class.php");
	
	require_once("access/folder_is_group_folder.access.php");
}

/**
 * Group Folder Class
 * @package data
 */
class GroupFolder extends Folder implements ConcreteFolderCaseInterface, EventListenerInterface
{
	private $group_folder;
	private $group_id;
  	
  	/**
  	 * @param integer $folder_id
  	 */
	function __construct($folder_id)
	{
		global $user;
		
		if (is_numeric($folder_id))
  		{
  			parent::__construct($folder_id);
  			$this->group_folder = new FolderIsGroupFolder_Access($folder_id);
  			$this->group_id = $this->group_folder->get_group_id();
  			
  			$group = new Group($this->group_id);
			if ($group->is_user_in_group($user->get_user_id()) == true)
			{
				if ($this->read_access == false)
  				{
					$this->data_entity_permission->set_read_permission();
	  				if ($this->data_entity_permission->is_access(1))
					{
						$this->read_access = true;
					}
  				}
  				
  				if ($this->write_access == false)
  				{
					$this->data_entity_permission->set_write_permission();
	  				if ($this->data_entity_permission->is_access(2))
					{
						$this->write_access = true;
					}
  				}
			}
  		}
  		else
  		{
  			parent::__construct(null);
  			$this->group_folder = null;
  			$this->group_id = null;
  		}
  	}
  	
	function __destruct()
	{
		unset($this->group_folder);
		unset($this->group_id);
		parent::__destruct();
	}
	
	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_change_permission($inherit = false)
	{
		if ($inherit == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_add_folder($inherit = false)
	{
		return true;
	}
	
	/**
	 * @see FolderInterface::can_command_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_command_folder($inherit = false)
	{
		if ($inherit == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see FolderInterface::can_rename_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_rename_folder($inherit = false)
	{
		if ($inherit == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $group_id
	 * @return bool
	 */
	public function create($group_id)
	{
		if (is_numeric($group_id))
		{
			$group = new Group($group_id);
			
			// Folder
			$group_folder_id = constant("GROUP_FOLDER_ID");
			$folder = new Folder($group_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($group_id);
			
			$folder = new Folder(null);
			if (($folder_id = parent::create($group->get_name(), $group_folder_id, $path->get_path_string(), 1, $group_id)) != null)
			{
				$folder_is_group_folder_access = new FolderIsGroupFolder_Access(null);
				if ($folder_is_group_folder_access->create($group_id, $folder_id) == null)
				{
					return false;
				}
														
				// Virtual Folders (Event)
				$group_folder_create_event = new GroupFolderCreateEvent($folder_id);
				$event_handler = new EventHandler($group_folder_create_event);
				
				if ($event_handler->get_success() == false)
				{
					$this->delete();
					return false;
				}
				else
				{
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
	 * @see ConcreteFolderCaseInterface::delete()
	 * @param bool $recursive
	 * @param bool $content
	 * @return bool
	 */
	public function delete($recursive, $content)
	{
		global $transaction;
		
		if ($this->group_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->group_folder->delete() == true)
			{
				if (parent::delete($recursive, $content) == true)
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
	 * @see ConcreteFolderCaseInterface::is_case()
	 * @param integer $folder_id
	 * @return bool
	 */
	public static function is_case($folder_id)
	{
		if (is_numeric($folder_id))
		{
			$folder_is_group_folder_access = new FolderIsGroupFolder_Access($folder_id);
			if ($folder_is_group_folder_access->get_group_id())
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
	 * @param integer $group_id
	 * @return integer
	 */
	public static function get_folder_by_group_id($group_id)
	{
		return FolderIsGroupFolder_Access::get_entry_by_group_id($group_id);
	}
	
	/**
	 * @see EventListenerInterface::listen_events()
	 * @param object $event_object
	 * @return bool
	 */
	public static function listen_events($event_object)
	{
		if ($event_object instanceof GroupCreateEvent)
    	{
    		$group_folder = new GroupFolder(null);
    		if ($group_folder->create($event_object->get_group_id()) == false)
    		{
				return false;
    		}
    	}
    	
		if ($event_object instanceof GroupPostDeleteEvent)
    	{
    		$folder_id = GroupFolder::get_folder_by_group_id($event_object->get_group_id());
    		$group_folder = new GroupFolder($folder_id);
			
			if ($group_folder->delete(true, true) == false)
			{
				return false;
			}
    	}
    	
		if ($event_object instanceof GroupRenameEvent)
    	{
    		$group = new Group($event_object->get_group_id());
    		$group_folder = new GroupFolder(self::get_folder_by_group_id($event_object->get_group_id()));
    		if ($group_folder->set_name($group->get_name()) == false)
    		{
    			return false;
    		}
    	}
    	
		return true;
	}
}