<?php
/**
 * @package project
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
	
}

/**
 * Project Folder Class
 * @package project
 */
class ProjectFolder extends Folder implements ConcreteFolderCaseInterface
{
  	private $project_id
  	private $project_folder;
  	
  	/**
  	 * @param integer $folder_id
  	 */
	function __construct($folder_id)
	{
  		if (is_numeric($folder_id))
  		{
  			
  		}
  		else
  		{
  			$this->project_id = null;
  		}
		// evtl parent::__construct($folder_id);
  	}
  	
	function __destruct()
	{
		parent::__destruct();
	}

	/**
	 * @return bool
	 */
	public function is_read_access()
	{
		parent::is_read_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_write_access()
	{
		parent::is_write_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_delete_access()
	{
		parent::is_delete_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_control_access()
	{
		parent::is_control_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_change_permission()
	{
		parent::is_flag_change_permission();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_add_folder()
	{
		parent::is_flag_add_folder();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_cmd_folder()
	{
		parent::is_flag_cmd_folder();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_rename_folder()
	{
		parent::is_flag_rename_folder();
	}
	
	/**
	 * Creates a new Project Folder including Folder
	 * @param integer $project_id
	 * @return bool
	 * @todo: remove v-folder
	 */
	public function create($project_id)
	{
		if (is_numeric($project_id))
		{
			$project = new Project($project_id);
			
			// Folder
			$project_folder_id = $GLOBALS[project_folder_id];
			$folder = new Folder($project_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($project_id);
			
			$folder = new Folder(null);
			if (($folder_id = $folder->create($project->get_name(), $project_folder_id, false, $path->get_path_string(), $project->get_owner_id(), null)) != null)
			{
				$project_has_folder_access = new ProjectHasFolder_Access(null);
				if ($project_has_folder_access->create($project_id, $folder_id) == null)
				{
					return false;
				}
				if ($folder->set_flag(16) == false)
				{
					$folder->delete(true, true);
					return false;
				}
				
				// Sample - Virtual Folder
				
				$virtual_folder = new VirtualFolder(null);
				if ($virtual_folder->create($folder_id, "samples") == null)
				{
					$folder->delete();
					return false;
				}
				if ($virtual_folder->set_sample_vfolder() == false)
				{
					$folder->delete();
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
	
	// Wird über konkretisierung automatisch über Folder ausgeführt
	/**
	 * @param bool $recursive
	 * @param bool $content
	 * @return bool
	 */
	public function delete($recursive, $content)
	{
		global $transaction;
		
		if ($this->project_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($project_folder->delete() == true)
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
	 * Checks if $folder_id is a case of Project Folder
	 * @param integer $folder_id
	 * @return bool
	 */
	public static function is_case($folder_id)
	{
		if (is_numeric($folder_id))
		{
			$project_has_folder_access = new ProjectHasFolder_Access($folder_id);
			if ($project_has_folder_access->get_project_id())
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
?>