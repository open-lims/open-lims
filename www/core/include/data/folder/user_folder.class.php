<?php
/**
 * @package data
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
if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
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
	 * @return bool
	 */
	public function is_read_access()
	{
		return parent::is_read_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_write_access()
	{
		return parent::is_write_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_delete_access()
	{
		return parent::is_delete_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_control_access()
	{
		return parent::is_control_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_change_permission()
	{
		return parent::is_flag_change_permission();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_add_folder()
	{
		return parent::is_flag_add_folder();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_cmd_folder()
	{
		return parent::is_flag_cmd_folder();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_rename_folder()
	{
		return parent::is_flag_rename_folder();
	}
	
	/**
	 * @todo: remove v-folder
	 */
	public function create($user_id)
	{
		if (is_numeric($user_id))
		{
			$user = new User($user_id);
			
			// Folder
			$user_folder_id = $GLOBALS[user_folder_id];
			$folder = new Folder($user_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($user_id);
			
			if (($folder_id = parent::create($user->get_username(), $user_folder_id, false, $path->get_path_string(), $user_id, null)) != null)
			{
				$folder_is_user_folder_access = new FolderIsUserFolder_Access(null);
				if ($folder_is_user_folder_access->create($user_id, $folder_id) == null)
				{
					return false;
				}
				if ($this->set_flag(2) == false)
				{
					$this->delete(true, true);
					return false;
				}
				
				// _Public
				$public_path = new Path($path->get_path_string());
				$public_path->add_element("_public");
				
				$public_folder = new Folder(null);
				if (($public_folder->create("_public", $folder_id, false, $public_path->get_path_string(), $user_id, null)) == null)
				{
					$this->delete();
					return false;
				}
				
				if ($public_folder->set_flag(512) == false)
				{
					$this->delete();
					return false;
				}
				
				
				// _Private
				
				$private_path = new Path($path->get_path_string());
				$private_path->add_element("_private");
				
				$private_folder = new Folder(null);
				if (($private_folder->create("_private", $folder_id, false, $private_path->get_path_string(), $user_id, null)) == null)
				{
					$this->delete();
					return false;
				}
				
				if ($private_folder->set_flag(512) == false)
				{
					$this->delete();
					return false;
				}
				
				// Sample - Virtual Folder
				
				$virtual_folder = new VirtualFolder(null);
				if ($virtual_folder->create($folder_id, "samples") == null)
				{
					$this->delete();
					return false;
				}
				if ($virtual_folder->set_sample_vfolder() == false)
				{
					$this->delete();
					return false;
				}
				
				
				// Project - Virtual Folder
				
				$virtual_folder = new VirtualFolder(null);
				if ($virtual_folder->create($folder_id, "projects") == null)
				{
					$this->delete();
					return false;
				}
				if ($virtual_folder->set_project_vfolder() == false)
				{
					$this->delete();
					return false;
				}
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
	
	// Wird über konkretisierung automatisch über Folder ausgeführt,
	// kann aber auch direkt ausgeführt werden (wenn Klasse bekannt)
	/**
	 * @param bool $recursive
	 * @param bool $content
	 * @return bool
	 */
	public function delete($recursive, $content)
	{
		global $transaction;
		
		if ($this->user_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->user_folder->delete() == true)
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
	 * Checks if $folder_id is a case of Sample Folder
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
	
	public static function get_folder_by_user_id($user_id)
	{
		return FolderIsUserFolder_Access::get_entry_by_user_id($user_id);
	}
	
	public static function listen_events($event_object)
	{
		if ($event_object instanceof UserCreateEvent)
    	{
    		$user_folder = new UserFolder(null);
    		if ($user_folder->create($event_object->get_user_id()) == false)
    		{
				return false;
    		}
    	}
    	
		if ($event_object instanceof UserPostDeleteEvent)
    	{
    		$folder_id = UserFolder::get_folder_by_user_id($event_object->get_user_id());
    		$user_folder = new UserFolder($folder_id);
			
			if ($user_folder->delete(true, true) == false)
			{
				return false;
			}
    	}
    	
		return true;
	}
}