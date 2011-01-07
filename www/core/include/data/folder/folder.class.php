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
require_once("interfaces/folder.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/folder.access.php");
	
	require_once("access/folder_join.access.php");
	
	require_once("access/folder_is_group_folder.access.php");
	require_once("access/folder_is_home_folder.access.php");
	require_once("access/folder_is_organisation_unit_folder.access.php");
	require_once("access/folder_is_project_folder.access.php");
	require_once("access/folder_is_project_status_folder.access.php");
	require_once("access/folder_is_sample_folder.access.php");
	
	require_once("access/virtual_folder.access.php");
	require_once("access/virtual_folder_has_folder.access.php");
}

/**
 * Folder Management Class
 * @package data
 */
class Folder implements FolderInterface
{
	private $folder_id;
	
	private $folder;
	
	private $read_access;
	private $write_access;
	private $delete_access;
	private $control_access;

	/**
	 * @param integer $folder_id
	 */
	function __construct($folder_id)
	{		
		if ($folder_id == null)
		{
			$this->folder_id 			= null;
			$this->folder				= new Folder_Access(null);
		}
		else
		{				
			$this->folder_id 			= $folder_id;
			$this->folder				= new Folder_Access($folder_id);
			
			if ($this->folder->get_id() != null)
			{	
				$object_permission = new ObjectPermission($this->folder->get_permission(), $this->folder->get_automatic(), $this->folder->get_owner_id(), $this->folder->get_owner_group_id());
				$object_permission->set_folder_flag($this->folder->get_flag());
				
				if (($project_id = $this->is_child_of_project_folder()) != null)
				{
					$object_permission->set_project_id($project_id);
				}
				
				if (($sample_id = $this->is_child_of_sample_folder()) != null)
				{
					$object_permission->set_sample_id($sample_id);
				}
				
				if ($object_permission->is_access(1))
				{
					$this->read_access = true;
				}
				else
				{
					$this->read_access = false;
				}
				
				if ($object_permission->is_access(2))
				{
					$this->write_access = true;
				}
				else
				{
					$this->write_access = false;
				}
				
				if ($object_permission->is_access(3))
				{
					$this->delete_access = true;
				}
				else
				{
					$this->delete_access = false;
				}
				
				if ($object_permission->is_access(4))
				{
					$this->control_access = true;
				}
				else
				{
					$this->control_access = false;
				}	
			}
			else
			{
				$this->folder_id 			= null;
				$this->folder				= new Folder_Access(null);
			}
		}
	} 
	
	function __destruct()
	{
		if ($this->folder_id)
		{
			unset($this->folder_id);
		
			unset($this->folder);
			
			unset($this->read_access);
			unset($this->write_access);
			unset($this->delete_access);
			unset($this->control_access);
		}
	}
	
	/**
	 * @return bool
	 */
	public function is_read_access()
	{
		return $this->read_access;
	}
	
	/**
	 * @return bool
	 */
	public function is_write_access()
	{
		if ($this->folder and $this->folder_id)
		{
			$flag = $this->folder->get_flag();
			if ($flag)
			{
				if ($flag == 16)
				{
					return false;
				}
				else
				{
					return $this->write_access;
				}
			}
			else
			{
				return $this->write_access;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function is_delete_access()
	{
		return $this->delete_access;
	}
	
	/**
	 * @return bool
	 */
	public function is_control_access()
	{
		return $this->control_access;
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_change_permission()
	{
		if ($this->folder and $this->folder_id)
		{
			if ($this->is_control_access() == true)
			{
				$flag = $this->folder->get_flag();
				
				if ($flag)
				{
					if ($flag == 1 or
						$flag == 2 or
						$flag == 4 or
						$flag == 8 or
						$flag == 16 or
						$flag == 32 or
						$flag == 512)
					{
						return false;
					}
					else
					{
						return true;
					}
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
	 * @return bool
	 */
	public function is_flag_add_folder()
	{
		global $user;
		
		// CMD = Copy, Move, Delete
		
		if ($this->folder and $this->folder_id)
		{
			if ($this->is_write_access() == true)
			{
				$flag = $this->folder->get_flag();
				
				if ($flag)
				{
					if ($flag == 64 or
						$flag == 128 or
						$flag == 512)
					{
						return true;
					}
					else
					{
						if ($user->is_admin())
						{
							return true;
						}
						else
						{
							return false;
						}
					}
					
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
	 * @return bool
	 */
	public function is_flag_cmd_folder()
	{
		global $user;
		
		// CMD = Copy, Move, Delete
		
		if ($this->folder and $this->folder_id)
		{
			if ($this->is_delete_access() == true)
			{
				$flag = $this->folder->get_flag();
				
				if ($flag)
				{
					if ($flag == 0 or
						$flag == 64)
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
	 * @return bool
	 */
	public function is_flag_rename_folder()
	{
		global $user;
		
		if ($this->folder and $this->folder_id)
		{
			if ($this->is_delete_access() == true)
			{
				$flag = $this->folder->get_flag();
				
				if ($flag)
				{
					if ($flag == 64)
					{
						return true;
					}
					else
					{
						if ($user->is_admin())
						{
							return true;
						}
						else
						{
							return false;
						}
					}
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
	 * Creates a new folder
	 * @param string $name
	 * @param integer $toid
	 * @param bool $root
	 * @param string $path
	 * @param integer $owner_id
	 * @param integer $owner_group_id
	 * @return integer
	 */
	public function create($name, $toid, $root, $path, $owner_id, $owner_group_id)
	{
		global $transaction;

		if (is_numeric($toid) and isset($root))
		{
			$transaction_id = $transaction->begin();
			
			if (!$path and $name)
			{
				$folder = new Folder($toid);
				$folder_name = str_replace(" ","_",trim($name));
				$path = $folder->get_path()."/".$folder_name;
			}
			elseif(!$path and !$name)
			{
				return null;
			}

			$folder_id = $this->folder->create($name, $toid, $root, $path, $owner_id, $owner_group_id);
			
			if ($folder_id)
			{
				$this->__construct($folder_id);
				
				$system_path = $GLOBALS[base_dir]."/".$path;
					
				if (!file_exists($system_path))
				{
					if (mkdir($system_path) == true)
					{
						if ($transaction_id != null)
						{
							$transaction->commit($transaction_id);
						}
						return $folder_id;
					}
					else
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return null;
					}
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Sets the current folder to a group folder
	 * @todo extrat method from class due to loose dependency
	 * @param integer $group_id
	 * @return bool
	 */
	public function create_group_folder($group_id)
	{
		if ($this->folder_id and $group_id)
		{	
			$folder_is_group_folder_access = new FolderIsGroupFolder_Access(null);
			$result = $folder_is_group_folder_access->create($group_id, $this->folder_id);
			
			if ($result != null)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	/**
	 * Sets the current folder to an user (home) folder
	 * @todo extrat method from class due to loose dependency
	 * @param integer $user_id
	 * @return bool
	 */
	public function create_home_folder($user_id)
	{
		if ($this->folder_id and $user_id)
		{
			$folder_is_home_folder_access = new FolderIsHomeFolder_Access(null);
			$result = $folder_is_home_folder_access->create($user_id, $this->folder_id);
			
			if ($result != null)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	/**
	 * Sets the current folder to an organisation unit folder
	 * @todo extrat method from class due to loose dependency
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public function create_organisation_unit_folder($organisation_unit_id)
	{
		if ($this->folder_id and $organisation_unit_id)
		{	
			$folder_is_organisation_unit_folder_access = new FolderIsOrganisationUnitFolder_Access(null);
			$result = $folder_is_organisation_unit_folder_access->create($organisation_unit_id, $this->folder_id);
			
			if ($result != null)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	/**
	 * Sets the current folder to a project folder
	 * @todo extrat method from class due to loose dependency
	 * @param integer $project_id
	 * @return bool
	 */
	public function create_project_folder($project_id)
	{
		if ($this->folder_id and $project_id)
		{
			$folder_is_project_folder_access = new FolderIsProjectFolder_Access(null);
			$result = $folder_is_project_folder_access->create($project_id, $this->folder_id);
			
			if ($result != null)
			{
				return true;
			}
			else
			{
				return false;
			}				
		}
	}
	
	/**
	 * Sets the current folder to a project status folder
	 * @todo extrat method from class due to loose dependency
	 * @param integer $status_id
	 * @param integer $project_id
	 * @return bool
	 */
	public function create_project_status_folder($status_id, $project_id)
	{
		if ($this->folder_id and $status_id and $project_id)
		{				
			$folder_is_project_status_folder_access = new FolderIsProjectStatusFolder_Access(null);
			$result = $folder_is_project_status_folder_access->create($status_id, $project_id, $this->folder_id);
			
			if ($result != null)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Sets the current folder to a sample folder
	 * @todo extrat method from class due to loose dependency
	 * @param integer $sample_id
	 * @return bool
	 */
	public function create_sample_folder($sample_id)
	{
		if ($this->folder_id and $sample_id)
		{
			$folder_is_sample_folder_access = new FolderIsSampleFolder_Access(null);
			$result = $folder_is_sample_folder_access->create($sample_id, $this->folder_id);
			
			if ($result != null)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	/**
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
	 * @param string $name
	 * @return bool
	 */
	public function exist_subfolder_name($name)
	{
		if ($this->folder_id and $this->folder)
		{
			$subfolder_array = $this->get_subfolder_array();
			
			if (is_array($subfolder_array) and count($subfolder_array) >= 1)
			{
				foreach($subfolder_array as $key => $value)
				{
					if ($value[type] == 0)
					{
						$folder = new Folder($value[id]);
						
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
	 * @param string $name
	 * @return bool
	 */
	public function exist_file($name)
	{
		if ($this->folder_id and $this->folder and $name)
		{
			$folder_join_access = new FolderJoin_Access();
			if ($folder_join_access->get_existing_file_id_in_folder($this->folder_id, $name) != null)
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
	 * @param integer $value_type_id
	 * @return bool
	 */
	public function exist_value($value_type_id)
	{
		if ($this->folder_id and $this->folder and is_numeric($value_type_id))
		{	
			$value_array = Object::get_value_array($this->folder_id);
	    	
	    	if (is_array($value_array) and count($value_array) >= 1)
	    	{
	    		foreach($value_array as $fe_key => $fe_value)
	    		{
	    			$value = new Value($fe_value);
	    			
	    			if (trim($value->get_type_id()) == $value_type_id)
	    			{
	    				return true;
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
			return false;
		}
	}
	
	/**
	 * Deletes a folder including sub-folders and content
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
							if ($value[type] == 0)
							{
								$folder = new Folder($value[id]);
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
				}
			
				$file_array = Object::get_file_array($this->folder_id);
			
				if ($content == false and is_array($file_array) and count($file_array) >= 1)
				{
					return false;
				}
				else
				{
					if (is_array($file_array) and count($file_array) >= 1)
					{
						foreach ($file_array as $key => $value)
						{
							$file = new File($value);
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
					}
				}
				
				$value_array = Object::get_value_array($this->folder_id);
			
				if ($content == false and is_array($value_array) and count($value_array) >= 1)
				{
					return false;
				}
				else
				{
					if (is_array($value_array) and count($value_array) >= 1)
					{
						foreach ($value_array as $key => $value)
						{
							$value_obj = new Value($value);
							$value_delete = $value_obj->delete();
							if ($value_delete == false)
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
				
				$path = $GLOBALS[base_dir]."/".$this->folder->get_path();
				
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
				
				$linked_vfolder_array = VirtualFolderHasFolder_Access::list_entries_by_folder_id($this->folder_id);
				
				if (is_array($linked_vfolder_array) and count($linked_vfolder_array) >= 1)
				{
					foreach($linked_vfolder_array as $key => $value)
					{
						$virtual_folder_has_folder = new VirtualFolderHasFolder_Access($value);
						$virtual_folder_has_folder->delete();
					}
				}
				
				$vfolder_array = VirtualFolder::list_entries_by_folder_id($this->folder_id);
				
				if (is_array($vfolder_array) and count($vfolder_array) >= 1)
				{
					foreach($vfolder_array as $key => $value)
					{
						$virtual_folder = new VirtualFolder($value);
						$virtual_folder->delete();
					}
				}
				
				$this->unset_group_folder();
				$this->unset_home_folder();
				$this->unset_organisation_unit_folder();
				$this->unset_project_folder();
				$this->unset_project_status_folder();
				$this->unset_sample_folder();
				
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
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function unset_group_folder()
	{
		if ($this->folder_id)
		{	
			$pk = FolderIsGroupFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($pk)
			{
				$folder_is_group_folder_access = new FolderIsGroupFolder_Access($pk);
				
				if ($folder_is_group_folder_access->delete())
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
				return true;
			}
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function unset_home_folder()
	{
		if ($this->folder_id)
		{
			$pk = FolderIsHomeFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($pk)
			{
				$folder_is_home_folder_access = new FolderIsHomeFolder_Access($pk);
				
				if ($folder_is_home_folder_access->delete())
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
				return true;
			}
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function unset_organisation_unit_folder()
	{
		if ($this->folder_id)
		{
			$pk = FolderIsOrganisationUnitFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($pk)
			{
				$folder_is_organisation_unit_folder_access = new FolderIsOrganisationUnitFolder_Access($pk);
				
				if ($folder_is_organisation_unit_folder_access->delete()){
					return true;
				}else{
					return false;
				}
			}
			else
			{
				return true;
			}
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function unset_project_folder()
	{
		if ($this->folder_id)
		{
			$pk = FolderIsProjectFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($pk)
			{
				$folder_is_project_folder_access = new FolderIsProjectFolder_Access($pk);
				
				if ($folder_is_project_folder_access->delete())
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
				return true;
			}
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function unset_project_status_folder()
	{
		if ($this->folder_id)
		{
			$pk = FolderIsProjectStatusFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($pk) 
			{
				$folder_is_project_status_folder_access = new FolderIsProjectStatusFolder_Access($pk);
				
				if ($folder_is_project_status_folder_access->delete())
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
				return true;
			}
		}	
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function unset_sample_folder()
	{
		if ($this->folder_id)
		{	
			$pk = FolderIsSampleFolder_Access::get_entry_by_folder_id($this->folder_id);
				
			if ($pk)
			{
				$folder_is_sample_folder_access = new FolderIsSampleFolder_Access($pk);
				
				if ($folder_is_sample_folder_access->delete()){
					return true;
				}else{
					return false;
				}
			}
			else
			{
				return true;
			}
		}
	}
	
	/**
	 * Returns project-id of a project folder
	 * @todo extrat method from class due to loose dependency
	 * @return integer
	 */
	public function is_in_project()
	{	
		if ($this->folder_id)
		{
			$folder_is_project_folder_access_primary_key = FolderIsProjectFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($folder_is_project_folder_access_primary_key != null)
			{
				$folder_is_project_folder_access = new FolderIsProjectFolder_Access($folder_is_project_folder_access_primary_key);
				return $folder_is_project_folder_access->get_project_id();
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
	 * Returns sample-id of a sample folder
	 * @todo extrat method from class due to loose dependency
	 * @return integer
	 */
	public function is_in_sample()
	{
		if ($this->folder_id)
		{
			$folder_is_sample_folder_access_primary_key = FolderIsSampleFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($folder_is_sample_folder_access_primary_key != null)
			{
				$folder_is_sample_folder_access = new FolderIsSampleFolder_Access($folder_is_sample_folder_access_primary_key);
				return $folder_is_sample_folder_access->get_sample_id();
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
	 * Checeks if the folder is a group folder
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function is_group_folder()
	{
		if ($this->folder_id)
		{
			$folder_is_group_folder_access_primary_key = FolderIsGroupFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($folder_is_group_folder_access_primary_key != null)
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
	 * Checks if the folder is an user (home) folder
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function is_home_folder()
	{
		if ($this->folder_id)
		{
			$folder_is_home_folder_access_primary_key = FolderIsHomeFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($folder_is_home_folder_access_primary_key != null)
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
	 * Checks if the folder is an organisation unit folder
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function is_organisation_unit_folder()
	{
		if ($this->folder_id)
		{
			$folder_is_organisation_unit_folder_access_primary_key = FolderIsOrganisationUnitFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($folder_is_organisation_unit_folder_access_primary_key != null)
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
	 * Checks if the folder is a project folder
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function is_project_folder()
	{
		if ($this->folder_id)
		{
			$folder_is_project_folder_access_primary_key = FolderIsProjectFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($folder_is_project_folder_access_primary_key != null)
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
	 * Checks if the folder is a project status folder
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function is_project_status_folder()
	{
		if ($this->folder_id)
		{
			$folder_is_project_status_folder_access_primary_key = FolderIsProjectStatusFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($folder_is_project_status_folder_access_primary_key != null)
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
	 * Checks if the folder is a sample folder
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function is_sample_folder()
	{
		if ($this->folder_id)
		{
			$folder_is_sample_folder_access_primary_key = FolderIsSampleFolder_Access::get_entry_by_folder_id($this->folder_id);
			
			if ($folder_is_sample_folder_access_primary_key != null)
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
	 * Checks if the current folder is a child of a project folder
	 * @todo extrat method from class due to loose dependency
	 * @return integer
	 */
	public function is_child_of_project_folder()
	{
		if ($this->folder_id)
		{
			$folder_id = $this->folder_id;
			$project_id = null;
				
			do
			{
				$folder = new Folder_Access($folder_id);
		
				$folder_is_project_folder_access_primary_key = FolderIsProjectFolder_Access::get_entry_by_folder_id($folder_id);
				if ($folder_is_project_folder_access_primary_key != null)
				{
					$folder_is_project_folder_access = new FolderIsProjectFolder_Access($folder_is_project_folder_access_primary_key);
					$project_id = $folder_is_project_folder_access->get_project_id();
				}
				else
				{
					$folder_id = $folder->get_toid();
				}
			}
			while($folder_id != 1 and $project_id == null);
		
			return $project_id;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Checks if the current folder is a child of a sample folder
	 * @todo extrat method from class due to loose dependency
	 * @return integer
	 */
	public function is_child_of_sample_folder()
	{
		if ($this->folder_id)
		{
			$folder_id = $this->folder_id;
			$sample_id = null;
				
			do
			{
				$folder = new Folder_Access($folder_id);
		
				$folder_is_sample_folder_access_primary_key = FolderIsSampleFolder_Access::get_entry_by_folder_id($folder_id);
				
				if ($folder_is_sample_folder_access_primary_key != null)
				{
					$folder_is_sample_folder_access = new FolderIsSampleFolder_Access($folder_is_sample_folder_access_primary_key);
					$sample_id = $folder_is_sample_folder_access->get_sample_id();
				}
				else
				{
					$folder_id = $folder->get_toid();
				}
			}
			while($folder_id != 1 and $sample_id == null);
		
			return $sample_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Moves a folder to another location
	 * @param integer $destination_id
	 * @return bool
	 */
	public function move_folder($destination_id)
	{
		global $session;
	
		if ($this->folder_id and $this->folder and is_numeric($destination_id))
		{
			$destination_folder = new Folder($destination_id);
			if ($destination_folder->exist_subfolder_name($this->get_name()) == false)
			{
				$current_path = new Path($this->get_path());
				$destination_path = new Path($destination_folder->get_path());
				$destination_path->add_element($current_path->get_last_element());
				
				$new_path = $destination_path->get_path_string();
				
				// create new folder
				mkdir($GLOBALS[base_dir]."/".$new_path);
				
				// change path
				$this->folder->set_path($new_path);
				
				// subfolder filesystem move
				
				if (($subfolder_array = $this->get_subfolder_array()) != null)
				{	
					if (is_array($subfolder_array) and count($subfolder_array) >= 1)
					{
						foreach($subfolder_array as $key => $value)
						{
							if ($value[type] == 0)
							{
								$folder = new Folder($value[id]);
								$folder->move_folder($this->folder_id);
							}							
						}
					}
				}
				
				// Move Files
				
				$handle = opendir($GLOBALS[base_dir]."/".$current_path->get_path_string());
				
				while(($file_name = readdir($handle)) !== false)
				{
					if ($file_name != "." and $file_name != "..")
					{
						$current_file = $GLOBALS[base_dir]."/".$current_path->get_path_string()."/".$file_name;
						$destination_file = $GLOBALS[base_dir]."/".$new_path."/".$file_name;
						copy($current_file, $destination_file);
						unlink($current_file);
					}
				}
				
				$this->folder->set_toid($destination_id);
				
				closedir($handle);
				
				rmdir($GLOBALS[base_dir]."/".$current_path->get_path_string());
				
				// Delete Folder Stack
				$session->delete_value("stack_array");
				
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
	 * Copies a folder to another location
	 * @param integer $destination_id
	 * @return bool
	 * @todo Implementation - Copy folder is not supported in current version
	 */
	public function copy_folder($destination_id)
	{	

	}

	/**
	 * @return string
	 */
	public function get_object_path()
	{
		if ($this->folder_id)
		{
			$folder_id = $this->folder_id;
			
			$is_root = false;
			$path = "";
			$folder_array = array();
			
			while($is_root == false)
			{
				$folder_access = new Folder_Access($folder_id);
			
				if (!in_array($folder_id, $folder_array))
				{
					array_push($folder_array, $folder_id);
				}
				else
				{
					return "database consistency error";
				}
				
				if ($folder_access->get_is_root() == false)
				{
					$is_root = false;
					$folder_id = $folder_access->get_toid();
				}
				else
				{
					$is_root = true;
				}

				if ($path)
				{
					if ($folder_access->get_name() == "/")
					{
						$path = "/".$path;
					}
					else
					{
						$path = trim($folder_access->get_name())."/".$path;
					}
				}
				else
				{
					$path = trim($folder_access->get_name());	
				}
			}
			
			if (!$path)
			{
				return "/";
			}
			else
			{
				return $path;
			}
		}
		else
		{
			return "error";
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_object_id_path()
	{
		if ($this->folder_id)
		{
			$return_array = array();
			$is_root = $this->folder->get_is_root();
			$toid = $this->folder->get_toid();
			
			array_push($return_array,$this->folder_id);
			
			while ($is_root == false)
			{
				$folder = new Folder($toid);
				
				$is_root = $folder->get_is_root();
				
				if ($toid != $folder->get_toid())
				{
					array_push($return_array,$toid);
				}
				
				$toid = $folder->get_toid();
			}
			return $return_array;
		}
	}
	
	/**
	 * @return integer
	 */		
	public function get_toid()
	{
		if ($this->folder)
		{
			return $this->folder->get_toid();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_is_root()
	{
		if ($this->folder)
		{
			return $this->folder->get_is_root();
		}
		else
		{
			return null;
		}
	}
	
	/**
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
	 * @return string
	 */
	public function get_path()
	{
		if ($this->folder->get_path())
		{
			return $this->folder->get_path();
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_datetime()
	{
		if ($this->folder)
		{
			return $this->folder->get_datetime();
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_owner_id()
	{
		if ($this->folder)
		{
			return $this->folder->get_owner_id();
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_owner_group_id()
	{
		if ($this->folder)
		{
			return $this->folder->get_owner_group_id();
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_permission()
	{
		if ($this->folder)
		{
			return $this->folder->get_permission();
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_permission_string()
	{
		if ($this->folder)
		{
			$object_permission = new ObjectPermission($this->folder->get_permission(), $this->folder->get_automatic(), $this->folder->get_owner_id(), $this->folder->get_owner_group_id());
			return $object_permission->get_permission_string();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_automatic()
	{
		if ($this->folder)
		{
			return $this->folder->get_automatic();
		}
		else
		{
			return null;
		}	
	}

	/**
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
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id)
	{
		if (is_numeric($owner_id) and $this->folder_id and $this->folder)
		{
			return $this->folder->set_owner_id($owner_id);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public function set_owner_group_id($owner_group_id)
	{
		if (is_numeric($owner_group_id) and $this->folder_id and $this->folder)
		{
			return $this->folder->set_owner_group_id($owner_group_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $permission
	 * @return bool
	 */
	public function set_permission($permission)
	{
		if (isset($permission) and $this->folder)
		{
			return $this->folder->set_permission($permission);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param bool $automatic
	 * @return bool
	 */
	public function set_automatic($automatic)
	{
		if (isset($automatic) and $this->folder)
		{
			return $this->folder->set_automatic($automatic);
		}
		else
		{
			return false;
		}
	}

	/**
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
	 * Returns an array with all subfolders
	 * @return array
	 */
	public function get_subfolder_array()
	{
		if ($this->folder_id and $this->folder)
		{
			$return_array = array();
			
			$folder_array = $this->folder->list_entries_by_toid($this->folder_id);
			
			$virtual_folder_array = VirtualFolder_Access::list_entries_by_folder_id($this->folder_id);
			
			$counter = 0;
			
			if (is_array($folder_array) and count($folder_array) > 0)
			{
				foreach($folder_array as $key => $value)
				{
					$return_array[$counter][id]		= $value;
					$return_array[$counter][type]	= 0;
					
					$counter++;
				}
			}
			
			if (is_array($virtual_folder_array) and count($virtual_folder_array) > 0)
			{
				foreach($virtual_folder_array as $key => $value)
				{
					$return_array[$counter][id]		= $value;
					$return_array[$counter][type]	= 1;
					
					$counter++;
				}
			}
			
			if (is_array($return_array) and count($return_array) > 0)
			{
				return $return_array;
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
	 * Checks if images are in the current folder
	 * @return bool
	 */
	public function is_folder_image_content()
	{
		if ($this->folder and $this->folder_id)
		{
			$object_array = Object::get_file_array($this->folder_id);
			
			if (is_array($object_array) and count($object_array) >= 1)
			{
				foreach ($object_array as $key => $value)
				{
					$file = new File($value);
					if ($file->is_image() == true)
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


	/**
	 * @todo extrat method from class due to loose dependency
	 * @param integer $project_id
	 * @return integer
	 */
	public static function get_project_supplementary_folder($project_id)
	{
		if ($project_id)
		{
			$project_folder_id = self::get_project_folder_by_project_id($project_id);
			
			$folder_array = Folder_Access::list_entries_by_toid($project_folder_id);
			
			foreach($folder_array as $key => $value)
			{
				$folder_access = new Folder_Access($value);
				
				$path = new Path($folder_access->get_path());
				$path_array = $path->get_path_elements();
				
				if ($path_array[$path->get_path_length()] == "supplementary")
				{   // If supplement-folder is found
					return $value;	
				}	
			}
			return null;	
		}
		else
		{
			return null;
		}
	}

	/**
	 * @todo extrat method from class due to loose dependency
	 * @param integer $group_id
	 * @return integer
	 */
	public static function get_group_folder_by_group_id($group_id)
	{
		if (is_numeric($group_id))
		{
			$folder_is_group_folder_pk = FolderIsGroupFolder_Access::get_entry_by_group_id($group_id);
			$folder_is_group_folder = new FolderIsGroupFolder_Access($folder_is_group_folder_pk);
			return $folder_is_group_folder->get_folder_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @param integer $user_id
	 * @return integer
	 */
	public static function get_home_folder_by_user_id($user_id)
	{
		if (is_numeric($user_id))
		{
			$folder_is_home_folder_pk = FolderIsHomeFolder_Access::get_entry_by_user_id($user_id);
			$folder_is_home_folder = new FolderIsHomeFolder_Access($folder_is_home_folder_pk);
			return $folder_is_home_folder->get_folder_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function get_organisation_unit_folder_by_organisation_unit_id($organisation_unit_id)
	{
		if (is_numeric($organisation_unit_id))
		{
			$folder_is_organisation_unit_folder_pk = FolderIsOrganisationUnitFolder_Access::get_entry_by_organisation_unit_id($organisation_unit_id);
			$folder_is_organisation_unit_folder = new FolderIsOrganisationUnitFolder_Access($folder_is_organisation_unit_folder_pk);
			return $folder_is_organisation_unit_folder->get_folder_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @param integer $project_id
	 * @return integer
	 */
	public static function get_project_folder_by_project_id($project_id)
	{
		if (is_numeric($project_id))
		{
			$folder_is_project_folder_pk = FolderIsProjectFolder_Access::get_entry_by_project_id($project_id);
			$folder_is_project_folder = new FolderIsProjectFolder_Access($folder_is_project_folder_pk);
			return $folder_is_project_folder->get_folder_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @param integer $project_id
	 * @param integer $status_id
	 * @return integer
	 */
	public static function get_project_status_folder_by_status_id($project_id, $status_id)
	{
		if (is_numeric($project_id) and is_numeric($status_id))
		{
			$folder_is_project_status_folder_pk = FolderIsProjectStatusFolder_Access::get_entry_by_status_id_and_project_id($status_id, $project_id);
			$folder_is_project_status_folder = new FolderIsProjectStatusFolder_Access($folder_is_project_status_folder_pk);
			return $folder_is_project_status_folder->get_folder_id();
		}
		else
		{
			return null;
		}
	}

	/**
	 * @todo extrat method from class due to loose dependency
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function get_sample_folder_by_sample_id($sample_id)
	{
		if (is_numeric($sample_id))
		{
			$folder_is_sample_folder_pk = FolderIsSampleFolder_Access::get_entry_by_sample_id($sample_id);
			$folder_is_sample_folder = new FolderIsSampleFolder_Access($folder_is_sample_folder_pk);
			return $folder_is_sample_folder->get_folder_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
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
	 * @return array
	 */
	public static function list_folder()
	{
		return Folder_Access::list_folder();
	}

	/**
	 * Sets the owner_id on null, where owner_id = $owner_id
	 * @param integer $owner_id
	 * @return bool
	 */
	public static function set_owner_id_on_null($owner_id)
	{
		return Folder_Access::set_owner_id_on_null($owner_id);
	}
	
	/**
	 * Sets the owner_id on null, where owner_id = $owner_id
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public static function set_owner_group_id_on_null($owner_group_id)
	{
		return Folder_Access::set_owner_group_id_on_null($owner_group_id);
	}

}

?>
