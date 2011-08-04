<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_status_has_folder.access.php");
}

/**
 * Project Status Folder Class
 * @package project
 */
class ProjectStatusFolder extends Folder implements ConcreteFolderCaseInterface
{
  	private $project_status_folder;
	private $project_status_id;
	private $project_id;
  	
  	/**
  	 * @param integer $folder_id
  	 */
	function __construct($folder_id)
	{
		if (is_numeric($folder_id))
  		{
  			parent::__construct($folder_id);
  			$this->project_status_folder = new ProjectStatusHasFolder_Access($folder_id);
  			$this->project_status_id = $this->project_status_folder->get_project_status_id();
  			$this->project_id = $this->project_status_folder->get_project_id();
  		}
  		else
  		{
  			parent::__construct(null);
  			$this->project_status_folder = null;
  			$this->project_status_id = null;
  			$this->project_id = null;
  		}
  	}
  	
	function __destruct()
	{
		unset($this->project_status_folder);
		unset($this->project_status_id);
		unset($this->project_id);
		parent::__destruct();
	}
	
	/**
	 * Creates a new Project Folder including Folder
	 * @param integer $project_id
	 * @return integer
	 */
	public function create($project_id, $project_status_id)
	{
		if (is_numeric($project_id) and is_numeric($project_status_id))
		{			
			$project_status = new ProjectStatus($project_status_id);
			$project = new Project($project_id);
			
			$project_folder_id = ProjectFolder::get_folder_by_project_id($project_id);
			$folder = new Folder($project_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element("status-".$project_status_id);
			
			if (($folder_id = parent::create($project_status->get_name(), $project_folder_id, $path->get_path_string(), $project->get_owner_id(), null)) != null)
			{
				$project_status_has_folder_access = new ProjectStatusHasFolder_Access(null);
				if ($project_status_has_folder_access->create($project_id, $project_status_id, $folder_id) == null)
				{
					return null;
				}
				if ($this->set_flag(256) == false)
				{
					$this->delete(true, true);
					return null;
				}
				
				return $folder_id;
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
		
		if ($this->project_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->project_status_folder->delete() == true)
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
			$project_status_has_folder_access = new ProjectStatusHasFolder_Access($folder_id);
			if ($project_status_has_folder_access->get_project_id())
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
	
	public static function get_folder_by_project_id_and_project_status_id($project_id, $project_status_id)
	{
		return ProjectStatusHasFolder_Access::get_entry_by_project_id_and_project_status_id($project_id, $project_status_id);
	}
}
?>