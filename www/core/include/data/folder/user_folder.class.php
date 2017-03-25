<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
	require_once("events/user_folder_create_event.class.php");
	
	require_once("access/folder_is_user_folder.access.php");
}

/**
 * User Folder Class
 * @package data
 */
class UserFolder extends Folder implements ConcreteFolderCaseInterface, EventListenerInterface
{
	private $user_folder;
	private $user_id;
	
	private $ci_user_id;
  	
  	/**
  	 * @param integer $folder_id
  	 */
	function __construct($folder_id)
	{
		if (is_numeric($folder_id))
  		{
  			parent::__construct($folder_id);
  			$this->user_folder = new FolderIsUserFolder_Access($folder_id);
  			$this->user_id = $this->user_folder->get_user_id();
  		}
  		else
  		{
  			parent::__construct(null);
  			$this->user_folder = null;
  			$this->user_id = null;
  		}
  	}
  	
	function __destruct()
	{
		unset($this->user_folder);
		unset($this->user_id);
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
	 * @return bool
	 */
	public function create()
	{
		if (is_numeric($this->ci_user_id))
		{
			$user = new User($this->ci_user_id);
			
			// Folder
			$user_folder_id = constant("USER_FOLDER_ID");
			$folder = new Folder($user_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($this->ci_user_id);

			parent::ci_set_name($user->get_username());
			parent::ci_set_toid($user_folder_id);
			parent::ci_set_path($path->get_path_string());
			parent::ci_set_owner_id($this->ci_user_id);
			if (($folder_id = parent::create()) != null)
			{
				$folder_is_user_folder_access = new FolderIsUserFolder_Access(null);
				if ($folder_is_user_folder_access->create($this->ci_user_id, $folder_id) == null)
				{
					return false;
				}
				
				// _Public
				$public_path = new Path($path->get_path_string());
				$public_path->add_element("_public");
				
				$public_folder = new Folder(null);
				$public_folder->ci_set_name("_public");
				$public_folder->ci_set_toid($folder_id);
				$public_folder->ci_set_path($public_path->get_path_string());
				$public_folder->ci_set_owner_id($this->ci_user_id);
				if (($public_folder->create()) == null)
				{
					$this->delete();
					return false;
				}
						
				
				// _Private
				
				$private_path = new Path($path->get_path_string());
				$private_path->add_element("_private");
				
				$private_folder = new Folder(null);
				$private_folder->ci_set_name("_private");
				$private_folder->ci_set_toid($folder_id);
				$private_folder->ci_set_path($private_path->get_path_string());
				$private_folder->ci_set_owner_id($this->ci_user_id);
				if (($private_folder->create()) == null)
				{
					$this->delete();
					return false;
				}

				
				// Virtual Folders (Event)
				$user_folder_create_event = new UserFolderCreateEvent($folder_id);
				$event_handler = new EventHandler($user_folder_create_event);
				
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
	 * Injects $user_id into create()
	 * @param integer $user_id
	 */
	public function ci_set_user_id($user_id)
	{
		$this->ci_user_id = $user_id;
	}
	
	/**
	 * @see ConcreteFolderCaseInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->user_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->user_folder->delete() == true)
			{
				if (parent::delete() == true)
				{
					$transaction->commit($transaction_id);
					return true;
				}
				else
				{
					$transaction->rollback($transaction_id);
					return false;
				}
			}
			else
			{
				$transaction->rollback($transaction_id);
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
			$folder_is_user_folder_access = new FolderIsUserFolder_Access($folder_id);
			if ($folder_is_user_folder_access->get_user_id())
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
	 * @param integer $user_id
	 * @return integer
	 */
	public static function get_folder_by_user_id($user_id)
	{
		return FolderIsUserFolder_Access::get_entry_by_user_id($user_id);
	}
	
	/**
	 * @see EventListenerInterface::listen_events()
	 * @param object $event_object
	 * @return bool
	 */
	public static function listen_events($event_object)
	{
		if ($event_object instanceof UserCreateEvent)
    	{
    		$user_folder = new UserFolder(null);
    		$user_folder->ci_set_user_id($event_object->get_user_id());
    		if ($user_folder->create() == false)
    		{
				return false;
    		}
    	}
    	
		if ($event_object instanceof UserDeleteEvent)
    	{
    		$folder_id = UserFolder::get_folder_by_user_id($event_object->get_user_id());
    		if ($folder_id)
    		{
	    		$user_folder = new UserFolder($folder_id);
				$user_folder->di_set_content(true);
				$user_folder->di_set_recursive(true);
				if ($user_folder->delete() == false)
				{
					return false;
				}
    		}
    	}
    	
    	/**
    	 * @todo Delete physical folder on disk, seperate it from database action
    	 */
    	if ($event_object instanceof UserPostDeleteEvent)
    	{
    		
    	}
    	
		if ($event_object instanceof UserRenameEvent)
    	{
    		$user = new User($event_object->get_user_id());
    		$user_folder = new UserFolder(self::get_folder_by_user_id($event_object->get_user_id()));
    		if ($user_folder->set_name($user->get_username()) == false)
    		{
    			return false;
    		}
    	}
    	
		return true;
	}
}