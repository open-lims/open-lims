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
require_once("interfaces/file.interface.php");
 
if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("events/file_delete_event.class.php");
	require_once("events/file_upload_event.class.php");
	require_once("events/file_upload_precheck_event.class.php");
	
	require_once("access/file.access.php");
	require_once("access/file_version.access.php");
}

/**
 * File Class for Management of Files in Folders
 * @package data
 */
class File extends DataEntity implements FileInterface, EventListenerInterface
{
	private static $file_object_array;
	
	private $file_id;
	private $file_version_id;
	
	private $file;
	private $file_version;
	
	private $item_id_array = array();

	private $name;
	private $folder_id;
	private $path;
	
	/**
	 * @see FileInterface::__construct()
	 * @param integer $file_id
	 * @throws FileNotFoundException
	 */
	function __construct($id)
	{
		if (is_numeric($id))
		{
			if (File_Access::exist_file_by_file_id($id) == true)
			{
				$this->file_id = $file_id;
				$this->file = new File_Access($id);
				
				$this->file_version_id = FileVersion_Access::get_current_entry_by_toid($id);
				$this->file_version = new FileVersion_Access($this->file_version_id);
	
				parent::__construct($this->file->get_data_entity_id());
			}
			else
			{
				throw new FileNotFoundException();
			}
		}
		else
		{
			$this->file_id = null;
			$this->file = new File_Access(null);
			$this->file_version = new FileVersion_Access(null);
			parent::__construct(null);
		}
	}
	
	/**
	 * Destructor of File Class
	 */
	function __destruct()
	{
		parent::__destruct();
		unset($this->file_id);
		unset($this->file);
		unset($this->file_version);
		unset($this->item_id_array);
	}
	
 	/**
 	 * @see DataEntityInterface::can_set_automatic()
 	 * @return bool
	 */
	public function can_set_automatic()
	{
		if ($this->file_id)
		{
			$parent_folder = Folder::get_instance($this->get_parent_folder_id());
			return $parent_folder->can_set_automatic();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see DataEntityInterface::can_set_data_entity()
	 * @return bool
	 */
	public function can_set_data_entity()
	{
		if ($this->file_id)
		{
			$parent_folder = Folder::get_instance($this->get_parent_folder_id());
			return $parent_folder->can_set_data_entity();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see DataEntityInterface::can_set_control()
	 * @return bool
	 */
	public function can_set_control()
	{
		if ($this->file_id)
		{
			$parent_folder = Folder::get_instance($this->get_parent_folder_id());
			return $parent_folder->can_set_control();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see DataEntityInterface::can_set_remain()
	 * @return bool
	 */
	public function can_set_remain()
	{
		if ($this->file_id)
		{
			$parent_folder = Folder::get_instance($this->get_parent_folder_id());
			return $parent_folder->can_set_remain();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Checks if current version has the same md5-checksum like $checksum
	 * @param string $checksum
	 * @return bool
	 */
	private function compare_with_current_version($checksum)
	{
		if ($checksum and $this->file_version)
		{
			if (trim($this->file_version->get_checksum()) == trim($checksum))
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
	 * @see FileInterface::open_internal_revision()
	 * @param integer $internal_revision
	 * @return bool
	 * @throws FileVersionNotFoundException
	 */
	public function open_internal_revision($internal_revision)
	{
		if (is_numeric($internal_revision) and $this->file_id)
		{
			if (FileVersion_Access::exist_internal_revision($this->file_id, $internal_revision) == true)
			{
				$file_version_id =FileVersion_Access::get_entry_by_toid_and_internal_revision($this->file_id, $internal_revision);
				$this->file_version = new FileVersion_Access($file_version_id);
				return true;
			}
			else
			{
				throw new FileVersionNotFoundException();
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Opens another version of the file with file version id (primary key)
	 * @param integer $file_version_id
	 * @return bool
	 */
	private function open_file_version_id($file_version_id)
	{
		if (is_numeric($file_version_id) and $this->file_id)
		{
			$this->file_version = new FileVersion_Access($file_version_id);
			return true;
		}
		else
		{
			return false;
		}
	}

	private function create_shape($folder_id, $owner_id)
	{
		global $user, $transaction;
		
		if (is_numeric($folder_id))
		{
			$transaction_id = $transaction->begin();
			
			try
			{
				if ($owner_id == null)
				{
					$owner_id = $user->get_user_id();
				}
				
				$folder = Folder::get_instance($folder_id);
				
				$data_entity_id = parent::create();
				parent::set_as_child_of($folder->get_data_entity_id());
					
				
				$this->file = new File_Access(null);
				if (($file_id = $this->file->create($data_entity_id)) == null)
				{
					throw new FileCreateFailedException();
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
			
			return $file_id;
		}
		else
		{
			throw new FileCreateIDMissingException();
		}
	}
	
	/**
	 * @see FileInterface::create()
	 * @param string $name
	 * @param integer $folder_id
	 * @param string $path
	 * @param integer $owner_id
	 * @return integer
	 */
	public function create()
	{
		global $user, $transaction;
		
		if ($this->name and $this->folder_id and $this->path)
		{
			$transaction_id = $transaction->begin();
			
			try
			{
				if ($this->owner_id == null)
				{
					$this->owner_id = $user->get_user_id();
				}
				
				if (substr_count($this->path, constant("BASE_DIR")) != 1)
				{
					$this->path = constant("BASE_DIR")."/".$this->path;
				}
	
				$size = filesize($this->path);
				$checksum = md5_file($this->path);
				
				$file_id = $this->create_shape($this->folder_id, $this->owner_id);
					
	
				$file_version_access = new FileVersion_access(null);
				if ($file_version_access->create($file_id, $this->name, 1, $size, $checksum, null, null, 1, true, $this->owner_id) == null)
				{
					throw new FileCreateVersionCreateFailedException();
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
			
			self::__construct($file_id);
			
			return $file_id;
		}
		else
		{
			throw new FileCreateIDMissingException();
		}
	}
	
	/**
	 * Injects $name into create()
	 * @param string $name
	 */
	public function ci_set_name($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Injects $path into create()
	 * @param string $path
	 */
	public function ci_set_path($path)
	{
		$this->path = $path;
	}
	
	/**
	 * Injects $folder_id into create()
	 * @param integer $folder_id
	 */
	public function ci_set_folder_id($folder_id)
	{
		$this->folder_id = $folder_id;
	}
		
	/**
	 * @see FileInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if (($this->file_id != null) and $this->file and $this->file_version)
		{
			$transaction_id = $transaction->begin();
			
			$folder_id = $this->get_parent_folder_id();
			$folder = Folder::get_instance($folder_id);

			$file_delete_event = new FileDeleteEvent($this->file_id, $folder_id, 0);
			$event_handler = new EventHandler($file_delete_event);

			if ($event_handler->get_success() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			$file_version_array = FileVersion_Access::list_entries_by_toid($this->file_id);
			
			if (is_array($file_version_array) and count($file_version_array) >= 1)
			{
				$file_size = 0;
				$file_path_array = array();
				$temp_file_id = $this->file_id;
				
				foreach($file_version_array as $key => $value)
				{
					$file_version_access = new FileVersion_Access($value);
					$file_size = $file_size + $file_version_access->get_size();
					
					$extension_array = explode(".",$file_version_access->get_name());
					$extension_array_length = substr_count($file_version_access->get_name(),".");
								
					if ($extension_array_length == 0)
					{
						$extension = "";
					}
					else
					{
						$extension = ".".$extension_array[$extension_array_length];
					}
					
					$path = constant("BASE_DIR")."/".$folder->get_path()."/".$this->data_entity_id."-".$file_version_access->get_internal_revision()."".$extension."";
				
					if ($file_version_access->delete())
					{
						array_push($file_path_array, $path);
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
				
				$folder->decrease_filesize($this->get_owner_id(), $file_size);				
			
				$file_delete = $this->file->delete();
				
				if ($file_delete == true)
				{
					// Data Entity Delete
					if (parent::delete() == true)
					{
						if (is_array($file_path_array) and count($file_path_array) >= 1)
						{
							foreach ($file_path_array as $key => $value)
							{
								if (is_file($value))
								{
									if (is_writable($value))
									{
										$delete_success = unlink($value);
										if ($delete_success == false)
										{
											if ($transaction_id != null)
											{
												$transaction->rollback($transaction_id);
											}
											$this->file->__consturct($temp_file_id);
											$this->file->set_flag(-1); // Corrupt
											return false;
										}
									}
									else
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										$this->file->__consturct($temp_file_id);
										$this->file->set_flag(-1); // Corrupt
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
	 * @see FileInterface::delete_version()
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function delete_version($internal_revision)
	{	
		global $transaction;
		
		if ($this->file_id and $this->file and $this->file_version)
		{
			if (is_numeric($internal_revision))
			{
				$transaction_id = $transaction->begin();
				
				$folder_id = $this->get_parent_folder_id();
				$folder = Folder::get_instance($folder_id);
			
				$this->open_internal_revision($internal_revision);
				
				$number_of_root_major_versions = FileVersion_Access::get_number_of_root_major_versions_by_toid($this->file_id);
				
				if ($number_of_root_major_versions > 1)
				{
					$file_version_id = FileVersion_Access::get_entry_by_toid_and_internal_revision($this->file_id, $internal_revision);

					$file_delete_event = new FileVersionDeleteEvent($file_version_id, $folder_id, $this->file_version->get_size());
					$event_handler = new EventHandler($file_delete_event);
					
					if ($event_handler->get_success() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return 0;
					}
					
					$minor_file_array = FileVersion_Access::list_entries_by_previous_version_id($file_version_id);
					
					if (is_array($minor_file_array) and count($minor_file_array) >= 1)
					{
						foreach($minor_file_array as $key => $value)
						{
							$file = new File($this->file_id);
							$file->open_file_version_id($value);
							if ($file->delete_version($file->get_internal_revision()) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return 0;
							}											
						}	
					}
					
					if ($this->file_version->get_current() == true)
					{
						$next_current_file_version_id = FileVersion_Access::get_last_uploaded_version_entry_by_toid($this->file_id, $internal_revision);
						$file_version_access = new FileVersion_Access($next_current_file_version_id);
						$file_version_access->set_current(true);
					}
	
					// Minimise Filesize
					$folder->decrease_filesize($this->file_version->get_owner_id(), $this->file_version->get_size());
					
					// Datei L�schen
							
					$extension_array = explode(".",$this->file_version->get_name());
					$extension_array_length = substr_count($this->file_version->get_name(),".");
								
					if ($extension_array_length == 0)
					{
						$extension = "";
					}
					else
					{
						$extension = ".".$extension_array[$extension_array_length];
					}
					
					$path = constant("BASE_DIR")."/".$folder->get_path()."/".$this->date_entity_id."-".$this->file_version->get_internal_revision()."".$extension."";
					
					if ($this->file_version->delete())
					{
						if (is_file($path))
						{
							if (is_writable($path))
							{
								$delete_success = unlink($path);
								if ($delete_success == false)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									$this->file->set_flag(-1); // Corrupt
									return 0;
								}
							}
							else
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								$this->file->set_flag(-1); // Corrupt
								return 0;
							}
						}
						else
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							$this->file->set_flag(-1); // Corrupt
							return 0;
						}	
					}
					else
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return 0;
					}
					
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return 1;
				}
				else
				{
					if ($this->delete())
					{
						if ($transaction_id != null)
						{
							$transaction->commit($transaction_id);
						}
						return 2;
					}
					else
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return 0;
					}
				}
			}
			else
			{
				return 0;	
			}		
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * @see FileInterface::exist_file_version()
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function exist_file_version($internal_revision)
	{
		if ($this->file_id and $this->file_version)
		{
   			return $this->file_version->exist_file_version_by_internal_revision($internal_revision);
   		}
   		else
   		{
   			return false;
   		}
	}	
	
	/**
	 * @see FileInterface::upload_file_stack()
	 * @param integer $file_amount
	 * @param integer $folder_id
	 * @param array $file_array
	 * @param string $unique_id
	 * @return bool
	 */
	public function upload_file_stack($file_amount, $folder_id, $file_array, $unique_id)
	{
		global $user, $session;
		
		$minium_success = false;
		
		$session_file_array = array();
		
		for ($i=1;$i<=$file_amount;$i++)
		{
			$session_file_array[$i] = 0;
		}
		
		$session->write_value("FILE_UPLOAD_".$unique_id, $session_file_array, true);
		
		if ($session->is_value("FILE_UPLOAD_".$unique_id))
		{
			$folder = Folder::get_instance($folder_id);
	
			for ($i=1;$i<=$file_amount;$i++)
			{			
				$file_value = "file-".$i;
				$return_value = true;
	
				if ($file_array[$file_value] and !empty($file_array[$file_value]['name']))
				{
					$session_file_array[$i] = $this->upload_file($folder_id, $file_array[$file_value]);				
					if ($session_file_array[$i] == 1)
					{
						$minium_success = true;
						if ($this->get_item_id())
						{
							array_push($this->item_id_array, $this->get_item_id());
						}
					}
					$session->write_value("FILE_UPLOAD_".$unique_id, $session_file_array, true);
				}
				else
				{
					$session_file_array[$i] = 1;
					$session->write_value("FILE_UPLOAD_".$unique_id, $session_file_array, true);
				}	
			}
		}
		return $minium_success;
	}	
	
	/**
	 * @see FileInterface::upload_file()
	 * @param integer $folder_id
	 * @param array $file_array
	 * @return integer
	 */
	public function upload_file($folder_id, $file_array)
	{
		global $user, $transaction;
		
		if ($folder_id)
		{
			$transaction_id = $transaction->begin();

			$user_data = new DataUserData($user->get_user_id());
			$folder = Folder::get_instance($folder_id);	    	

			if ($folder->is_write_access() == true)
			{
				// check is file exists in folder via DataEntity
				if (Data_Wrapper::is_file_in_folder($folder_id, $file_array['name']) == false)
				{
					$target = constant("BASE_DIR")."/".$folder->get_path()."/".$file_array['name'];
		
					if((!empty($file_array['name']) or !file_exists($target)))
					{
						$file_name_array = explode(".",$file_array['name']);
						$file_name_array_length = substr_count($file_array['name'],".");
						
						// Forbidden file-types
						if ($file_name_array[$file_name_array_length] == "php" or 
							$file_name_array[$file_name_array_length] == "php3" or 
							$file_name_array[$file_name_array_length] == "php4" or 
							$file_name_array[$file_name_array_length] == "php5" or 
							$file_name_array[$file_name_array_length] == "phtml" or 
							$file_name_array[$file_name_array_length] == "phtm" or
							$file_name_array[$file_name_array_length] == "html" or 
							$file_name_array[$file_name_array_length] == "htm" or 
							$file_name_array[$file_name_array_length] == "cgi" or 
							$file_name_array[$file_name_array_length] == "pl" or 
							$file_name_array[$file_name_array_length] == "asp")
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return 7;
						}
						else
						{
							// Upload new file
							move_uploaded_file($file_array['tmp_name'],$target);
								
							if (($file_upload_error_no = $file_array['error']) == 0)
							{
		 						$file_size = filesize($target);
		 						$checksum = md5_file($target);
								
								$file_upload_precheck_event = new FileUploadPrecheckEvent($folder_id, $file_size);
								$event_handler = new EventHandler($file_upload_precheck_event);
								
								if ($event_handler->get_success() == false)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									return 6;
								}
		 						
								if ($folder->get_quota_access($user->get_user_id(), $file_size) == true)
								{
									$folder->increase_filesize($user->get_user_id(), $file_size);
			
									$file_upload_event = new FileUploadEvent($folder_id, $file_size);
									$event_handler = new EventHandler($file_upload_event);
									
									if ($event_handler->get_success() == false)
									{
										// Nothing happens
									}
									
			 						// Create File
			 						/**
			 						 * @todo Exception in whole method
			 						 */
			 						try
			 						{
			 							$file_id = $this->create($file_array['name'], $folder_id, $target, $user->get_user_id());
			 						}
			 						catch(BaseException $e)
			 						{
			 							if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										return 2;
			 						}
			 						$data_entity_id = $this->get_data_entity_id();
			 													
									// Rename File
									$extension_array = explode(".",$target);
									$extension_array_length = substr_count($target,".");
									
									if ($extension_array_length == 0)
									{
										$extension = "";
									}
									else
									{
										$extension = ".".$extension_array[$extension_array_length];
									}
									
									$new_filename = constant("BASE_DIR")."/".$folder->get_path()."/".$data_entity_id."-1".$extension;
									
									// Rename file with the object id
									if (rename($target, $new_filename) == true)
									{
										if ($transaction_id != null)
										{
											$transaction->commit($transaction_id);
										}
										return 1;
									}
									else
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										return 2;
									}
		 						}
		 						else
		 						{
		 							// Delete File - Overquota
		 							
		 							if (unlink($target))
		 							{
		 								if ($transaction_id != null)
		 								{
											$transaction->rollback($transaction_id);
										}
		 								return 6;
		 							}
		 							else
		 							{
		 								if ($transaction_id != null)
		 								{
											$transaction->rollback($transaction_id);
										}
										// Write Log - Delete Error
		 								return 6; 								
		 							}
		 						} 	
							}
							else
							{
								if ($file_upload_error_no == 1 or $file_upload_error_no == 2)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									return 4;
								}
								else
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									return 2;
								}
							}
						} // End If/Else - Forbidden File-Types
					}
					else
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return 5;
					}
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return 5;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return 8;
			}
		}
		else
		{
			return 3;
		}
	}
	
	/**
	 * @todo better transaction
	 * @see FileInterface::update_file()
	 * @param array $file_array
	 * @param integer $previous_version_id
	 * @param bool $major
	 * @param bool $current
	 * @return integer
	 */
	public function update_file($file_array, $previous_version_id, $major, $current)
	{
		global $user, $session, $transaction;

		if ($this->file and $this->file_id and $file_array)
		{
			$transaction_id = $transaction->begin();
	
			$user_data = new DataUserData($user->get_user_id());
			$folder = Folder::get_instance($this->get_parent_folder_id());
			$folder_path = $folder->get_path();
	
			if ($folder->is_write_access() == true)
			{
				$target = constant("BASE_DIR")."/".$folder_path."/".$file_array['name'];
				
				if(!empty($file_array['name']))
				{
					$file_name_array = explode(".",$file_array['name']);
					$file_name_array_length = substr_count($file_array['name'],".");
					
					// Forbidden file-types
					if ($file_name_array[$file_name_array_length] == "php" or 
						$file_name_array[$file_name_array_length] == "php3" or 
						$file_name_array[$file_name_array_length] == "php4" or 
						$file_name_array[$file_name_array_length] == "php5" or 
						$file_name_array[$file_name_array_length] == "phtml" or 
						$file_name_array[$file_name_array_length] == "phtm" or
						$file_name_array[$file_name_array_length] == "html" or 
						$file_name_array[$file_name_array_length] == "htm" or 
						$file_name_array[$file_name_array_length] == "cgi" or 
						$file_name_array[$file_name_array_length] == "pl" or 
						$file_name_array[$file_name_array_length] == "asp")
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return 7;
					}
					else
					{
						// Upload new file
						move_uploaded_file($file_array['tmp_name'],$target);
						
						if (($file_upload_error_no = $file_array['error']) == 0)
						{
							$file_size = filesize($target);
							$checksum = md5_file($target);
							
							if ($this->compare_with_current_version($checksum) == false)
							{ 
								
								$file_upload_precheck_event = new FileUploadPrecheckEvent($this->get_parent_folder_id(), $file_size);
								$event_handler = new EventHandler($file_upload_precheck_event);
								
								if ($event_handler->get_success() == false)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									return 6;
								}
			
								if ($folder->get_quota_access($user->get_user_id(), $file_size) == true)
								{
									$folder->increase_filesize($user->get_user_id(), $file_size);
			 						
									$file_upload_event = new FileUploadEvent($this->get_parent_folder_id(), $file_size);
									$event_handler = new EventHandler($file_upload_event);
									
									if ($event_handler->get_success() == false)
									{
										// Nothing happens
									}
									
									// Rename Old File
									$current_file_version_id = FileVersion_Access::get_current_entry_by_toid($this->file_id);
									$current_file_version = new FileVersion_Access($current_file_version_id);
									
									$extension_array = explode(".",$current_file_version->get_name());
									$extension_array_length = substr_count($current_file_version->get_name(),".");
												
									$new_internal_revision = $current_file_version->get_internal_revision()+1;
									
									if ($major == true)
									{										
										if ($previous_version_id == null)
										{
											$new_version = $current_file_version->get_version()+1;
											$previous_version_pk_id = null;
										}
										else
										{
											$major_file_version_id = FileVersion_Access::get_entry_by_toid_and_internal_revision($this->file_id, $previous_version_id);
											$major_file_version = new FileVersion_Access($major_file_version_id);
											
											if ($major_file_version->get_previous_version_id() == $major_file_version->get_id())
											{
												$previous_version_pk_id = null;
											}
											else
											{
												$previous_version_pk_id = $major_file_version->get_previous_version_id();
											}
											
											$major_file_version_id = FileVersion_Access::get_highest_major_version_entry_by_toid_and_previous_version_id($major_file_version->get_toid(), $previous_version_pk_id);
											$major_file_version = new FileVersion_Access($major_file_version_id);
											
											$new_version = $major_file_version->get_version()+1;
										}
									}
									else
									{
										$major_file_version_id = FileVersion_Access::get_entry_by_toid_and_internal_revision($this->file_id, $previous_version_id);
										$current_minor_version_id = FileVersion_Access::get_highest_minor_version_entry_by_id($major_file_version_id);
										
										if ($current_minor_version_id)
										{
											$current_minor_version = new FileVersion_Access($current_minor_version_id);
											$new_version = $current_minor_version->get_version() + 1;
										}
										else
										{
											$new_version = 1;
										}								

										$previous_version_pk_id = $major_file_version_id;
									}
			
									if ($current == true)
									{
				 						$file_version = new FileVersion_Access(null);
				 						$file_version->create($this->file_id, $file_array['name'], $new_version, $file_size, $checksum, null, $previous_version_pk_id, $new_internal_revision, true, $user->get_user_id());							
										$current_file_version->set_current(false);
									}
									else
									{
										$file_version = new FileVersion_Access(null);
				 						$file_version->create($this->file_id, $file_array['name'], $new_version, $file_size, $checksum, null, $previous_version_pk_id, $new_internal_revision, false, $user->get_user_id());
									}
									
									// Rename File
									$extension_array = explode(".",$target);
									$extension_array_length = substr_count($target,".");
									
									if ($extension_array_length == 0)
									{
										$extension_array[0] = "";
									}
									
									$new_filename = constant("BASE_DIR")."/".$folder_path."/".$this->data_entity_id."-".$new_internal_revision.".".$extension_array[$extension_array_length];
									
									// Rename file with the object id
									if (rename($target, $new_filename) == true)
									{
										if ($transaction_id != null)
										{
											$transaction->commit($transaction_id);
										}
										return 1;
									}
									else
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										return 2;
									}
								}
								else
								{
									// Delete File - Overquota
									if (unlink($target))
									{
		 								if ($transaction_id != null)
		 								{
											$transaction->rollback($transaction_id);
										}
		 								return 6;
		 							}
		 							else
		 							{
		 								if ($transaction_id != null)
		 								{
											$transaction->rollback($transaction_id);
										}
								 		// Write Log - Delete Error (nach Rollback)
		 								return 6; 								
		 							}
								}
							}
							else
							{
								// Delete File - Equals previous version
								if (unlink($target))
								{
	 								if ($transaction_id != null)
	 								{
										$transaction->rollback($transaction_id);
									}
	 								return 5;
	 							}
	 							else
	 							{
	 								if ($transaction_id != null)
	 								{
										$transaction->rollback($transaction_id);
									}
	 								// Write Log - Delete Error
	 								return 5; 								
	 							}
							}
						}
						else
						{
							if ($file_upload_error_no == 1 or $file_upload_error_no == 2)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return 4;
							}
							else
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return 2;
							}	
						}
					} // End If/Else - Forbidden File-Types
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return 3;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return 8;
			}
		}
		else
		{
			return 3;
		}
	}
	
	/**
	 * @see FileInterface::move()
	 * @todo LATER: implementation - Move file is not supported in current version
	 * @param integer $folder_id
	 * @return bool
	 */
	public function move($folder_id)
	{
		
	}
	
	/**
	 * @todo copy all versions, transaction, exceptions
	 * @see FileInterface::copy()
	 * @param integer $folder_id
	 * @return bool
	 */
	public function copy($folder_id, $all_versions = false)
	{
		global $transaction, $user;
		
		if (is_numeric($folder_id) and $this->file and $this->file_version and $this->file_id)
		{
			$old_data_entity_id = $this->data_entity_id;
			$old_file_name = $this->get_name();
			
			$source_folder = Folder::get_instance($this->get_parent_folder_id());			
			$folder = Folder::get_instance($folder_id);
			
			// Create File
 			$file_id = $this->create_shape($folder_id, $user->get_user_id());
 			$data_entity_id = $this->get_data_entity_id();

 			// Alle Versionen kopieren
 			$current_file_version_id = FileVersion_Access::get_current_entry_by_toid($this->file_id);
 			$current_file_version = new FileVersion_Access($current_file_version_id);
 			
 			$extension_array = explode(".",$current_file_version->get_name());
			$extension_array_length = substr_count($current_file_version->get_name(),".");
			
			if ($extension_array_length == 0)
			{
				$extension = "";
			}
			else
			{
				$extension = ".".$extension_array[$extension_array_length];
			}
 			
 			$current_file_path = constant("BASE_DIR")."/".$source_folder->get_path()."/".$old_data_entity_id."-".$current_file_version->get_internal_revision()."".$extension;
 			
 			$size = filesize($current_file_path);
			$checksum = md5_file($current_file_path);
 			
			if ($file_id != null)
			{
				$file_version_access = new FileVersion_access(null);
				$file_version_id = $file_version_access->create($file_id, $old_file_name, 1, $size, $checksum, null, null, 1, true, $user->get_user_id());
				
				if ($file_version_id == null)
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
			
			$new_file_path = constant("BASE_DIR")."/".$folder->get_path()."/".$data_entity_id."-1".$extension;
			
			
			// Rename file with the object id
			if (copy($current_file_path, $new_file_path) == true)
			{
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				self::__construct($file_id);
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
			return false;
		}
	}
	
	/**
	 * @see FileInterface::is_current()
	 * @return bool
	 */
	public function is_current()
	{
		if ($this->file_id and $this->file_version)
		{
			if ($this->file_version->get_current() == true)
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
	 * @see FileInterface::is_image()
	 * @return bool
	 */
	public function is_image()
	{
		if ($this->file_id)
		{
			if (FileVersion_Access::get_file_extension_by_toid($this->file_id) == "jpg" or 
				FileVersion_Access::get_file_extension_by_toid($this->file_id) == "jpeg" or 
				FileVersion_Access::get_file_extension_by_toid($this->file_id) == "tiff" or
				FileVersion_Access::get_file_extension_by_toid($this->file_id) == "tif" or 
				FileVersion_Access::get_file_extension_by_toid($this->file_id) == "png" or 
				FileVersion_Access::get_file_extension_by_toid($this->file_id) == "gif" or 
				FileVersion_Access::get_file_extension_by_toid($this->file_id) == "bmp")
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
	 * @see FileInterface::get_file_content()
	 * @return string
	 */
	public function get_file_content()
	{
		if ($this->file_id)
		{			
			$folder = Folder::get_instance($this->get_parent_folder_id());

			$extension_array = explode(".",$this->file_version->get_name());
			$extension_array_length = substr_count($this->file_version->get_name(),".");
								
			if ($extension_array_length == 0)
			{
				$extension = "";
			}
			else
			{
				$extension = ".".$extension_array[$extension_array_length];
			}
			
			$path = constant("BASE_DIR")."/".$folder->get_path()."/".$this->data_entity_id."-".$this->file_version->get_internal_revision()."".$extension."";
		
			$size = filesize($path);
			$handle = fopen($path, "r");
			$content = fread($handle, $size);
			
			return $content;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_file_internal_revisions()
	 * @return array
	 */
	public function get_file_internal_revisions()
	{
		if ($this->file_id)
		{
			$file_version_array = FileVersion_Access::list_entries_by_toid($this->file_id);
			$return_array = array();
	
			foreach($file_version_array as $key => $value)
			{
				$file_version = new FileVersion_Access($value);
				array_push($return_array, $file_version->get_internal_revision());
			}
			
			if (count($return_array) > 0)
			{
				return $return_array;
			}
			else
			{
				return null;
			}		
		}
	}
	
	/**
	 * @see FileInterface::get_item_id_array()
	 * @return array
	 */
	public function get_item_id_array()
	{
		if ($this->item_id_array)
		{
			return $this->item_id_array;
		}
		else
		{
			return null;
		}
	}
		
	/**
	 * @see FileInterface::get_icon()
	 * @return string
	 */
	public function get_icon()
	{
		if ($this->file_version)
		{
			$filename = $this->file_version->get_name();
			
			if ($filename)
			{
				$name_array = explode(".",$filename);
				$name_array_length = substr_count($filename,".");
				
				$ext = $name_array[$name_array_length];
				
				$path = $GLOBALS['autoload_prefix']."images/fileicons/16/";
				
				$file = $path."".$ext.".png";
				
				if (file_exists($file))
				{
					return $file;
				}
				else
				{
					return "images/fileicons/16/unknown.png";
				}
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_mime_type()
	 * @return string
	 */
	public function get_mime_type()
	{
		if ($this->file_version)
		{
			$filename = $this->file_version->get_name();
			if ($filename)
			{
				$filenameArray = explode(".",$filename);
				$filenameArrayLen = substr_count($filename,".");
				
				switch ($filenameArray[$filenameArrayLen]):
					case "zip": $mime="application/zip"; break;
					case "ez":  $mime="application/andrew-inset"; break;
					case "hqx": $mime="application/mac-binhex40"; break;
					case "cpt": $mime="application/mac-compactpro"; break;
					case "doc": $mime="application/msword"; break;
					case "bin": $mime="application/octet-stream"; break;
					case "dms": $mime="application/octet-stream"; break;
					case "lha": $mime="application/octet-stream"; break;
					case "lzh": $mime="application/octet-stream"; break;
					case "exe": $mime="application/octet-stream"; break;
					case "class": $mime="application/octet-stream"; break;
					case "so":  $mime="application/octet-stream"; break;
					case "dll": $mime="application/octet-stream"; break;
					case "oda": $mime="application/oda"; break;
					case "pdf": $mime="application/pdf"; break;
					case "ai":  $mime="application/postscript"; break;
					case "eps": $mime="application/postscript"; break;
					case "ps":  $mime="application/postscript"; break;
					case "smi": $mime="application/smil"; break;
					case "smil": $mime="application/smil"; break;
					case "xls": $mime="application/vnd.ms-excel"; break;
					case "ppt": $mime="application/vnd.ms-powerpoint"; break;
					case "wbxml": $mime="application/vnd.wap.wbxml"; break;
					case "wmlc": $mime="application/vnd.wap.wmlc"; break;
					case "wmlsc": $mime="application/vnd.wap.wmlscriptc"; break;
					case "bcpio": $mime="application/x-bcpio"; break;
					case "vcd": $mime="application/x-cdlink"; break;
					case "pgn": $mime="application/x-chess-pgn"; break;
					case "cpio": $mime="application/x-cpio"; break;
					case "csh": $mime="application/x-csh"; break;
					case "dcr": $mime="application/x-director"; break;
					case "dir": $mime="application/x-director"; break;
					case "dxr": $mime="application/x-director"; break;
					case "dvi": $mime="application/x-dvi"; break;
					case "spl": $mime="application/x-futuresplash"; break;
					case "gtar": $mime="application/x-gtar"; break;
					case "hdf": $mime="application/x-hdf"; break;
					case "js":  $mime="application/x-javascript"; break;
					case "skp": $mime="application/x-koan"; break;
					case "skd": $mime="application/x-koan"; break;
					case "skt": $mime="application/x-koan"; break;
					case "skm": $mime="application/x-koan"; break;
					case "latex": $mime="application/x-latex"; break;
					case "nc":  $mime="application/x-netcdf"; break;
					case "cdf": $mime="application/x-netcdf"; break;
					case "sh":  $mime="application/x-sh"; break;
					case "shar": $mime="application/x-shar"; break;
					case "swf": $mime="application/x-shockwave-flash"; break;
					case "sit": $mime="application/x-stuffit"; break;
					case "sv4cpio": $mime="application/x-sv4cpio"; break;
					case "sv4crc": $mime="application/x-sv4crc"; break;
					case "tar": $mime="application/x-tar"; break;
					case "tcl": $mime="application/x-tcl"; break;
					case "tex": $mime="application/x-tex"; break;
					case "texinfo": $mime="application/x-texinfo"; break;
					case "texi": $mime="application/x-texinfo"; break;
					case "t":   $mime="application/x-troff"; break;
					case "tr":  $mime="application/x-troff"; break;
					case "roff": $mime="application/x-troff"; break;
					case "man": $mime="application/x-troff-man"; break;
					case "me":  $mime="application/x-troff-me"; break;
					case "ms":  $mime="application/x-troff-ms"; break;
					case "ustar": $mime="application/x-ustar"; break;
					case "src": $mime="application/x-wais-source"; break;
					case "xhtml": $mime="application/xhtml+xml"; break;
					case "xht": $mime="application/xhtml+xml"; break;
					case "zip": $mime="application/zip"; break;
					case "au":  $mime="audio/basic"; break;
					case "snd": $mime="audio/basic"; break;
					case "mid": $mime="audio/midi"; break;
					case "midi": $mime="audio/midi"; break;
					case "kar": $mime="audio/midi"; break;
					case "mpga": $mime="audio/mpeg"; break;
					case "mp2": $mime="audio/mpeg"; break;
					case "mp3": $mime="audio/mpeg"; break;
					case "aif": $mime="audio/x-aiff"; break;
					case "aiff": $mime="audio/x-aiff"; break;
					case "aifc": $mime="audio/x-aiff"; break;
					case "m3u": $mime="audio/x-mpegurl"; break;
					case "ram": $mime="audio/x-pn-realaudio"; break;
					case "rm":  $mime="audio/x-pn-realaudio"; break;
					case "rpm": $mime="audio/x-pn-realaudio-plugin"; break;
					case "ra":  $mime="audio/x-realaudio"; break;
					case "wav": $mime="audio/x-wav"; break;
					case "pdb": $mime="chemical/x-pdb"; break;
					case "xyz": $mime="chemical/x-xyz"; break;
					case "bmp": $mime="image/bmp"; break;
					case "gif": $mime="image/gif"; break;
					case "ief": $mime="image/ief"; break;
					case "jpeg": $mime="image/jpeg"; break;
					case "jpg": $mime="image/jpeg"; break;
					case "jpe": $mime="image/jpeg"; break;
					case "png": $mime="image/png"; break;
					case "tiff": $mime="image/tiff"; break;
					case "tif": $mime="image/tiff"; break;
					case "djvu": $mime="image/vnd.djvu"; break;
					case "djv": $mime="image/vnd.djvu"; break;
					case "wbmp": $mime="image/vnd.wap.wbmp"; break;
					case "ras": $mime="image/x-cmu-raster"; break;
					case "pnm": $mime="image/x-portable-anymap"; break;
					case "pbm": $mime="image/x-portable-bitmap"; break;
					case "pgm": $mime="image/x-portable-graymap"; break;
					case "ppm": $mime="image/x-portable-pixmap"; break;
					case "rgb": $mime="image/x-rgb"; break;
					case "xbm": $mime="image/x-xbitmap"; break;
					case "xpm": $mime="image/x-xpixmap"; break;
					case "xwd": $mime="image/x-xwindowdump"; break;
					case "igs": $mime="model/iges"; break;
					case "iges": $mime="model/iges"; break;
					case "msh": $mime="model/mesh"; break;
					case "mesh": $mime="model/mesh"; break;
					case "silo": $mime="model/mesh"; break;
					case "wrl": $mime="model/vrml"; break;
					case "vrml": $mime="model/vrml"; break;
					case "css": $mime="text/css"; break;
					case "html": $mime="text/html"; break;
					case "htm": $mime="text/html"; break;
					case "asc": $mime="text/plain"; break;
					case "txt": $mime="text/plain"; break;
					case "rtx": $mime="text/richtext"; break;
					case "rtf": $mime="text/rtf"; break;
					case "sgml": $mime="text/sgml"; break;
					case "sgm": $mime="text/sgml"; break;
					case "tsv": $mime="text/tab-separated-values"; break;
					case "wml": $mime="text/vnd.wap.wml"; break;
					case "wmls": $mime="text/vnd.wap.wmlscript"; break;
					case "etx": $mime="text/x-setext"; break;
					case "xml": $mime="text/xml"; break;
					case "xsl": $mime="text/xml"; break;
					case "mpeg": $mime="video/mpeg"; break;
					case "mpg": $mime="video/mpeg"; break;
					case "mpe": $mime="video/mpeg"; break;
					case "qt":  $mime="video/quicktime"; break;
					case "mov": $mime="video/quicktime"; break;
					case "mxu": $mime="video/vnd.mpegurl"; break;
					case "avi": $mime="video/x-msvideo"; break;
					case "movie": $mime="video/x-sgi-movie"; break;
					case "asf": $mime="video/x-ms-asf"; break;
					case "asx": $mime="video/x-ms-asf"; break;
					case "wm":  $mime="video/x-ms-wm"; break;
					case "wmv": $mime="video/x-ms-wmv"; break;
					case "wvx": $mime="video/x-ms-wvx"; break;
					case "ice": $mime="x-conference/x-cooltalk"; break;
					default: $mime="unknow"; break;
				endswitch;
				
				return $mime;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->file_version)
		{
			return $this->file_version->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_version()
	 * @return integer
	 */
	public function get_version()
	{
		if ($this->file_version)
		{
			if ($this->file_version->get_id() == $this->file_version->get_previous_version_id())
			{
				return $this->file_version->get_version();
			}
			else
			{
				$tmp_file_version_id = $this->file_version->get_id();
				$tmp_internal_revision = $this->file_version->get_internal_revision();
				$tmp_previous_version_id = $this->file_version->get_previous_version_id();
				
				$return_string = $this->file_version->get_version();
				
				do
				{
					$tmp_file_version = new FileVersion_Access($tmp_previous_version_id);
					$return_string = $tmp_file_version->get_version().".".$return_string;
					$tmp_previous_version_id = $tmp_file_version->get_previous_version_id();
				}
				while($tmp_file_version->get_id() != $tmp_file_version->get_previous_version_id());
			
				return $return_string;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_internal_revision()
	 * @return integer
	 */
	public function get_internal_revision()
	{
		if ($this->file_version)
		{
			return $this->file_version->get_internal_revision();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_version_datetime()
	 * @return string
	 */
	public function get_version_datetime()
	{
		if ($this->file_version)
		{
			return $this->file_version->get_datetime();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_size()
	 * @return integer
	 */
	public function get_size()
	{
		if ($this->file_version)
		{
			return $this->file_version->get_size();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_checksum()
	 * @return string
	 */
	public function get_checksum()
	{
		if ($this->file_version)
		{
			return $this->file_version->get_checksum();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see FileInterface::get_file_version_id();
	 * @return integer
	 */
	public function get_file_version_id()
	{
		if ($this->file_version_id)
		{
			return $this->file_version_id;
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 * @see FileInterface::exist_file()
	 * @param integer $file_id
	 * @return bool
	 */
	public static function exist_file($file_id)
	{
		if (is_numeric($file_id))
		{
   			return File_Access::exist_file_by_file_id($file_id);
   		}
   		else
   		{
   			return false;
   		}
	}
	
	/**
	 * @see FileInterface::get_file_id_by_data_entity_id()
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_file_id_by_data_entity_id($data_entity_id)
	{	
		return File_Access::get_entry_by_data_entity_id($data_entity_id);
	}
	
	/**
	 * @see FileInterface::get_icon_by_name()
	 * @param string $name
	 * @return string
	 */
	public static function get_icon_by_name($name)
	{	
		if ($name)
		{
			$name_array = explode(".",$name);
			$name_array_length = substr_count($name,".");
			
			$ext = $name_array[$name_array_length];
			
			$path = "images/fileicons/16/";
			
			$file = $path."".$ext.".png";
			
			if (file_exists($file))
			{
				return $file;
			}
			else
			{
				return "images/fileicons/16/unknown.png";
			}
		}
		else
		{
			return null;
		}
	}

 	/**
 	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeleteEvent)
    	{
    		if (FileVersion_Access::set_owner_id_on_null($event_object->get_user_id()) == false)
    		{
    			return false;
    		}
    	}
    	
    	return true;
    }
    
	/**
     * @see FileInterface::get_instance()
     * @param integer $file_id
     * @return object
     */
    public static function get_instance($file_id, $force_new_instance = false)
    {    
    	if (is_numeric($file_id) and $file_id > 0)
    	{
    		if ($force_new_instance == true)
    		{
    			return new File($file_id);
    		}
    		else
    		{
				if (self::$file_object_array[$file_id])
				{
					return self::$file_object_array[$file_id];
				}
				else
				{
					$file = new File($file_id);
					self::$file_object_array[$file_id] = $file;
					return $file;
				}
    		}
    	}
    	else
    	{
    		return new File(null);
    	}
    }
}
?>