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
require_once("interfaces/folder.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("events/folder_delete_event.class.php");
	
	require_once("access/folder.access.php");
	require_once("access/folder_concretion.access.php");
}

/**
 * Folder Management Class
 * @package data
 */
class Folder extends DataEntity implements FolderInterface
{
	private static $folder_object_array;
	
	private $folder_id;
	private $folder;

	private $folder_change_permission;
	private $folder_add_folder;
	private $folder_command_folder;
	private $folder_rename_folder;
	
	/**
	 * Get instance via static::get_instance($folder_id)
	 * @param integer $folder_id
	 * @throws FolderNotFoundException
	 */
	function __construct($folder_id)
	{	
		if (is_numeric($folder_id))
		{
			if (Folder_Access::exist_id($folder_id) == true)
			{
				$this->folder_id 			= $folder_id;
				$this->folder				= new Folder_Access($folder_id);
	
				parent::__construct($this->folder->get_data_entity_id());
					
				if ($this->data_entity_permission->is_access(1))
				{
					$this->read_access = true;
				}
				
				if (is_object($this->parent_folder_object))
				{
					if ($this->parent_folder_object->get_inherit_permission() == true)
					{
						if ($this->parent_folder_object->can_change_permission(true) == true)
						{
							$this->folder_change_permission = true;
						}
						else
						{
							$this->folder_change_permission = false;
						}
						
						if ($this->parent_folder_object->can_add_folder(true) == true)
						{
							$this->folder_add_folder = true;
						}
						else
						{
							$this->folder_add_folder = false;
						}
						
						if ($this->parent_folder_object->can_command_folder(true) == true)
						{
							$this->folder_command_folder = true;
						}
						else
						{
							$this->folder_command_folder = false;
						}
						
						if ($this->parent_folder_object->can_rename_folder(true) == true)
						{
							$this->folder_rename_folder = true;
						}
						else
						{
							$this->folder_rename_folder = false;
						}
					}
					else
					{
						$this->folder_change_permission = true;
						$this->folder_add_folder = true;
						$this->folder_command_folder = true;
						$this->folder_rename_folder = true;
					}
				}
			}
			else
			{
				throw new FolderNotFoundException();
			}
		}	
		else
		{
			$this->folder_id 			= null;
			$this->folder				= new Folder_Access(null);
			parent::__construct(null);
		}
	} 
	
	function __destruct()
	{
		// Empty
	}
				
	/**
	 * @see FolderInterface::can_change_permission()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_change_permission($inherit = false)
	{
		return $this->folder_change_permission;
	}
	
	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_add_folder($inherit = false)
	{
		return $this->folder_add_folder;
	}
	
	/**
	 * @see FolderInterface::can_command_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_command_folder($inherit = false)
	{
		return $this->folder_command_folder;
	}
	
	/**
	 * @see FolderInterface::can_rename_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_rename_folder($inherit = false)
	{
		return $this->folder_rename_folder;
	}
	
	/**
	 * Creates a new folder
	 * @param string $name
	 * @param integer $toid
	 * @param string $path
	 * @param integer $owner_id
	 * @param integer $owner_group_id
	 * @return integer
	 * @throws FolderCreateFailedException
	 * @throws FolderCreateFolderAlreadyExistsException
	 * @throws FolderCreatePhysicalCreationFailedException
	 * @throws FolderCreateIDMissingException
	 */
	public function create($name, $toid, $path, $owner_id, $owner_group_id)
	{
		global $transaction;

		if (is_numeric($toid))
		{
			$transaction_id = $transaction->begin();
			
			try
			{
				$folder = new Folder($toid);
				$parent_data_entity_id = $folder->get_data_entity_id();
				
				if (!$path)
				{
					$folder = new Folder($toid);
					$folder_name = str_replace(" ","_",trim($name));
					$path = $folder->get_path()."/".$folder_name;
				}
	
				$data_entity_id = parent::create($owner_id, $owner_group_id);
				parent::set_as_child_of($parent_data_entity_id);
				
				if (($folder_id = $this->folder->create($data_entity_id, $name, $path)) == null)
				{	
					throw new FolderCreateFailedException();
				}
				
				self::__construct($folder_id);
										
				$system_path = constant("BASE_DIR")."/".$path;
					
				if (file_exists($system_path))
				{
					throw new FolderCreateFolderAlreadyExistsException();
					
				}
				
				if (mkdir($system_path) == false)
				{
					throw new FolderCreatePhysicalCreationFailedException();
				}
			}
			catch(BaseException $e)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw $e;
			}
			
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			
			return $folder_id;
		}
		else
		{
			throw new FolderCreateIDMissingException();
		}
	}
		
	/**
	 * @see FolderInterface::exist_folder()
	 * @return bool
	 */
	public function exist_folder()
	{
		if ($this->folder_id != null and $this->folder)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @see FolderInterface::exist_subfolder_name()
	 * @param string $name
	 * @return bool
	 */
	public function exist_subfolder_name($name)
	{
		if ($this->folder_id and $this->folder)
		{
			$data_entity_array = $this->get_children();
			if (is_array($data_entity_array) and count($data_entity_array) >= 1)
			{
				foreach($data_entity_array as $key => $value)
				{
					if (($folder_id = self::get_folder_id_by_data_entity_id($value)) != null)
					{
						$folder = new Folder($folder_id);
						if (trim(strtolower($folder->get_name())) == trim(strtolower($name)))
						{
							return true;
						}
					}
				}
				return false;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * @see FolderInterface::delete()
	 * @param bool $recursive
	 * @param bool $content
	 * @return bool
	 */
	public function delete($recursive, $content)
	{
		global $transaction;

		if ($this->folder_id and $this->folder)
		{
			$transaction_id = $transaction->begin();

			$subfolder_array = $this->get_subfolder_array();
	
			if ((is_array($subfolder_array) and $recursive == false) or ($content == false and $recursive == true))
			{
				return false;
			}
			else
			{
				if ($recursive == true and $content == true)
				{
					if(is_array($subfolder_array))
					{
						foreach($subfolder_array as $key => $value)
						{
							$folder = Folder::get_instance($value);
							if ($folder->delete(true, true) == false)
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
									// Avoids Ghost-Folders
									$transaction->commit($transaction_id);
									$transaction_id = $transaction->begin();
								}
							}
						}
					}
				}
			
				
				// Folder-Content
				$data_entity_array = $this->get_children("without_linked");
				
				if (is_array($data_entity_array) and count($data_entity_array) >= 1)
				{
					foreach ($data_entity_array as $key => $value)
					{
						// Files
						if (($file_id = File::get_file_id_by_data_entity_id($value)) != null)
						{
							$file = File::get_instance($file_id);
							$file_delete = $file->delete();
							if ($file_delete == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
							else
							{
								if ($transaction_id != null) {
									// Avoids Ghost-Files
									$transaction->commit($transaction_id);
									$transaction_id = $transaction->begin();
								}
							}
						}
						// Values
						if (($value_id = Value::get_value_id_by_data_entity_id($value)) != null)
						{
							$value_obj = Value::get_instance($value_id);
							if ($value_obj->delete() == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
						// Virtual Folders
						if (($virtual_folder_id = VirtualFolder::get_virtual_folder_id_by_data_entity_id($value)) != null)
						{
							$virtual_folder = new VirtualFolder($virtual_folder_id);
							if ($virtual_folder->delete() == false)
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
				
				
				// Linked Folder-Content (e.g. from sub-items)
				$data_entity_array = $this->get_children("linked_only");
				
				if (is_array($data_entity_array) and count($data_entity_array) >= 1)
				{
					foreach ($data_entity_array as $key => $value)
					{
						if ($this->unset_child($value) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}
												
				$path = constant("BASE_DIR")."/".$this->folder->get_path();
				
				if (file_exists($path))
				{
					$garbage_file_array = scandir($path);
					
					if (is_array($garbage_file_array) and count($garbage_file_array) >= 3)
					{
						foreach($garbage_file_array as $key => $value)
						{
							if ($key != 0 and $key != 1) 
							{
								unlink($path."/".$value);
							}
						}
					}
				}
				
				$linked_virtual_folder_array = $this->get_parent_virtual_folders();
				if (is_array($linked_virtual_folder_array) and count($linked_virtual_folder_array))
				{
					foreach($linked_virtual_folder_array as $key => $value)
					{
						if ($this->unset_child_of($value) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}	
				
				if (parent::delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				$folder_delete_event = new FolderDeleteEvent($folder_id);
				$event_handler = new EventHandler($folder_delete_event);
	
				if ($event_handler->get_success() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				if (file_exists($path))
				{
					if (rmdir($path))
					{
						if ($this->folder->delete() == true)
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
					if ($this->folder->delete() == true)
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
			return false;
		}
	}
	
	/**
	 * @see FolderInterface::mark_as_deleted()
	 * @return bool
	 */
	public function mark_as_deleted()
	{
		if ($this->folder_id and $this->folder)
		{
			return $this->folder->set_deleted(true);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see FolderInterface::mark_as_undeleted()
	 * @return bool
	 */
	public function mark_as_undeleted()
	{
		if ($this->folder_id and $this->folder)
		{
			return $this->folder->set_deleted(false);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see FolderInterface::move_folder()
	 * @param integer $destination_id
	 * @param bool $force_exist_check
	 * @return bool
	 */
	public function move_folder($destination_id, $force_exist_check)
	{
		global $session, $transaction;
	
		if ($this->folder_id and $this->folder and is_numeric($destination_id))
		{
			$destination_folder = Folder::get_instance($destination_id);
			if ($destination_folder->exist_subfolder_name($this->get_name()) == false or $force_exist_check == true)
			{
				$transaction_id = $transaction->begin();
				
				$current_path = new Path($this->get_path());
				$destination_path = new Path($destination_folder->get_path());
				$destination_path->add_element($current_path->get_last_element());
				
				$new_path = $destination_path->get_path_string();
				
				// create new folder
				if (mkdir(constant("BASE_DIR")."/".$new_path) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				// change database
				if ($this->folder->set_path($new_path) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					rmdir(constant("BASE_DIR")."/".$new_path);
					return false;
				}
				
				if ($this->unset_child_of($this->get_parent_folder()) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					rmdir(constant("BASE_DIR")."/".$new_path);
					return false;
				}
				
				if ($this->set_as_child_of($destination_folder->get_data_entity_id()) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					rmdir(constant("BASE_DIR")."/".$new_path);
					return false;
				}
				
				// subfolder filesystem move
				
				if (($subfolder_array = $this->get_subfolder_array()) != null)
				{	
					if (is_array($subfolder_array) and count($subfolder_array) >= 1)
					{
						foreach($subfolder_array as $key => $value)
						{
							$folder = Folder::get_instance($value);
							if ($folder->move_folder($this->folder_id, true) == false)
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
				
				// Move Files
				
				$handle = opendir(constant("BASE_DIR")."/".$current_path->get_path_string());
				
				while(($file_name = readdir($handle)) !== false)
				{
					if ($file_name != "." and $file_name != "..")
					{
						$current_file = constant("BASE_DIR")."/".$current_path->get_path_string()."/".$file_name;
						$destination_file = constant("BASE_DIR")."/".$new_path."/".$file_name;
						copy($current_file, $destination_file);
						unlink($current_file);
					}
				}

				closedir($handle);
				
				rmdir(constant("BASE_DIR")."/".$current_path->get_path_string());
				
				// Delete Folder Stack
				$session->delete_value("stack_array");
				
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
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
	
	/**
	 * @see FolderInterface::copy_folder()
	 * @param integer $destination_id
	 * @return bool
	 * @todo LATER: Implementation - Copy folder is not supported in current version
	 */
	public function copy_folder($destination_id, $force_exist_check)
	{	

	}

	/**
	 * @see FolderInterface::get_object_path()
	 * @return string
	 */
	public function get_object_path()
	{
		if ($this->folder_id)
		{
			$path = "";
			$folder_id = $this->folder_id;
			$folder_array = array();
			$data_entity = DataEntity::get_instance($this->data_entity_id);
			
			$return_array = array();
			array_push($return_array,$this->folder_id);
			
			if ($this->folder_id != 1)
			{
				$path = trim($this->folder->get_name());	
			}
			else
			{
				$path = "/".$path;
			}
			
			while (($parent_data_entity_id = $data_entity->get_parent_folder()) != null)
			{
				
				if (!in_array($folder_id, $folder_array))
				{
					array_push($folder_array, $folder_id);
				}
				else
				{
					return "database consistency error";
				}
				
				$data_entity = DataEntity::get_instance($parent_data_entity_id);
				$folder_id = Folder_Access::get_entry_by_data_entity_id($parent_data_entity_id);
				$folder_access = new Folder($folder_id);
				
				if ($folder_id != 1)
				{
					$path = trim($folder_access->get_name())."/".$path;
				}
				else
				{
					$path = "/".$path;
				}
			}
			
			return $path;
		}
		else
		{
			return "error";
		}
	}
	
	/**
	 * @see FolderInterface::get_object_id_path()
	 * @return integer
	 */
	public function get_object_id_path()
	{
		if ($this->folder_id)
		{
			$data_entity = DataEntity::get_instance($this->data_entity_id);
			
			$return_array = array();
			array_push($return_array,$this->folder_id);
			
			while (($parent_data_entity_id = $data_entity->get_parent_folder()) != null)
			{
				$data_entity = DataEntity::get_instance($parent_data_entity_id);
				array_push($return_array,Folder_Access::get_entry_by_data_entity_id($parent_data_entity_id));
			}
			
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FolderInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->folder)
		{
			return $this->folder->get_name();			
		}
		else
		{
			return null;
		}
	}

	/**
	 * @see FolderInterface::get_path()
	 * @return string
	 */
	public function get_path()
	{
		if ($this->folder)
		{
			return $this->folder->get_path();
		}
		else
		{
			return null;
		}	
	}

	/**
	 * @see FolderInterface::get_quota_access()
	 * @param integer $user_id
	 * @param integer $filesize
	 * @return bool
	 */
	public function get_quota_access($user_id, $filesize)
	{
		$user_data = new DataUserData($user_id);
		$user_quota = $user_data->get_quota();
		$user_filesize = $user_data->get_filesize();
										
		$new_user_filesize = $user_filesize + $filesize;
		
		if (($user_quota > $new_user_filesize or $user_quota == 0))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see FolderInterface::set_name()
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		if ($name and $this->folder_id and $this->folder)
		{
			return $this->folder->set_name($name);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @see FolderInterface::set_flag()
	 * @param integer $flag
	 * @return bool
	 */
	public function set_flag($flag)
	{
		if (is_numeric($flag) and $this->folder_id and $this->folder)
		{
			return $this->folder->set_flag($flag);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @see FolderInterface::increase_filesize()
	 * @param integer $user_id
	 * @param integer $filesize
	 * @return bool
	 */
	public function increase_filesize($user_id, $filesize)
	{
		$user_data = new DataUserData($user_id);
		$user_filesize = $user_data->get_filesize();
										
		$new_user_filesize = $user_filesize + $filesize;
		
		return $user_data->set_filesize($new_user_filesize);
	}
	
	/**
	 * @see FolderInterface::decrease_filesize()
	 * @param integer $user_id
	 * @param integer $filesize
	 * @return bool
	 */
	public function decrease_filesize($user_id, $filesize)
	{
		if (is_numeric($user_id))
		{
			$user_data = new DataUserData($user_id);
			$user_filesize = $user_data->get_filesize();
											
			$new_user_filesize = $user_filesize - $filesize;
			
			return $user_data->set_filesize($new_user_filesize);
		}
		else
		{
			// nothing happens (system files, etc.)
		}
	}
	
	/**
	 * @see FolderInterface::get_subfolder_array()
	 * @return array
	 */
	public function get_subfolder_array()
	{
		if ($this->folder_id and $this->folder)
		{
			$subfolder_array = array();
			
			$data_entity_array = $this->get_children();
			if (is_array($data_entity_array) and count($data_entity_array) >= 1)
			{
				foreach($data_entity_array as $key => $value)
				{
					if (($folder_id = self::get_folder_id_by_data_entity_id($value)) != null)
					{
						array_push($subfolder_array, $folder_id);
					}
				}
			}
			
			return $subfolder_array;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @see FolderInterface::is_folder_image_content()
	 * @return bool
	 */
	public function is_folder_image_content()
	{
		if ($this->folder and $this->folder_id)
		{
			$data_entity_array = $this->get_children();
			
			if (is_array($data_entity_array) and count($data_entity_array) >= 1)
			{
				foreach ($data_entity_array as $key => $value)
				{
					if (($file_id = File::get_file_id_by_data_entity_id($value)) != null)
					{
						$file = File::get_instance($file_id);
						if ($file->is_image() == true)
						{
							return true;
						}
					}
				}
			}
			return false;
		}
		else
		{
			return false;
		}
	}

	
	
	/**
	 * @see FolderInterface::get_folder_by_path()
	 * @param string $path
	 * @return integer
	 */
	public static function get_folder_by_path($path)
	{
		if ($path)
		{
			return Folder_Access::get_entry_by_path($path);
		}
		else
		{
			return null;
		}
	}

	/**
	 * @see FolderInterface::get_folder_id_by_data_entity_id()
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_folder_id_by_data_entity_id($data_entity_id)
	{	
		return Folder_Access::get_entry_by_data_entity_id($data_entity_id);
	}
	
	/**
	 * @see FolderInterface::get_data_entity_id_by_folder_id()
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_data_entity_id_by_folder_id($folder_id)
	{	
		return Folder_Access::get_data_entity_id_by_folder_id($folder_id);
	}

	/**
	 * @see FolderInterface::get_name_by_id()
	 * @param integer $folder_id
	 * @return string
	 */
	public static function get_name_by_id($folder_id)
	{
		return Folder_Access::get_name_by_id($folder_id);
	}
	
	/**
	 * @see FolderInterface::register_type()
	 * @param string $type
	 * @param string $handling_class
	 * @param integer $include_id
	 * @return bool
	 */
	public static function register_type($type, $handling_class, $include_id)
	{
		$folder_concretion = new FolderConcretion_Access(null);
		if ($folder_concretion->create($type, $handling_class, $include_id) != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see FolderInterface::delete_type_by_include_id()
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_type_by_include_id($include_id)
	{
		return FolderConcretion_Access::delete_by_include_id($include_id);
	}
	
	/**
	 * @see FolderInterface::list_folder()
	 * @return array
	 */
	public static function list_folder()
	{
		return Folder_Access::list_folder();
	}
    
    /**
     * @see FolderInterface::get_instance()
     * @param integer $folder_id
     * @return object
     */
    public static function get_instance($folder_id)
    {    	
    	if (is_numeric($folder_id) and $folder_id > 0)
    	{
			if (self::$folder_object_array[$folder_id])
			{
				return self::$folder_object_array[$folder_id];
			}
			else
			{
				$conrete_folder_array = FolderConcretion_Access::list_entries();
				if (is_array($conrete_folder_array) and count($conrete_folder_array) >= 1)
				{
					foreach($conrete_folder_array as $key => $value)
					{
						if (class_exists($value))
						{
							if ($value::is_case($folder_id))
							{
								$folder = new $value($folder_id);
								self::$folder_object_array[$folder_id] = $folder;
								return $folder;
							}
						}
					}
				}
				$folder = new Folder($folder_id);
				self::$folder_object_array[$folder_id] = $folder;
				return $folder;
			}
    	}
    	else
    	{
    		return new Folder(null);
    	}
    }
}

?>
