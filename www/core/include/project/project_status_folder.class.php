<?php
/**
 * @package project
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
	
	private $ci_project_id;
	private $ci_project_status_id;
  	
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
	 * @return bool
	 */
	protected function get_inherit_permission()
	{
		return true;
	}
	
	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_change_permission($inherit = false)
	{
		return true;
	}
	
	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_add_folder($inherit = false)
	{
		return false;
	}
	
	/**
	 * @see FolderInterface::can_command_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_command_folder($inherit = false)
	{
		return true;
	}
	
	/**
	 * @see FolderInterface::can_rename_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_rename_folder($inherit = false)
	{
		return false;
	}
	
	/**
	 * Creates a new Project Status Folder including Folder
	 * @return integer
	 */
	public function create()
	{
		if (is_numeric($this->ci_project_id) and is_numeric($this->ci_project_status_id))
		{			
			$project_status = new ProjectStatus($this->ci_project_status_id);
			$project = new Project($this->ci_project_id);
			
			$project_folder_id = ProjectFolder::get_folder_by_project_id($this->ci_project_id);
			$folder = new Folder($project_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element("status-".$this->ci_project_status_id);
			
			parent::ci_set_name($project_status->get_name());
			parent::ci_set_toid($project_folder_id);
			parent::ci_set_path($path->get_path_string());
			parent::ci_set_owner_id($project->get_owner_id());
			if (($folder_id = parent::create()) != null)
			{
				$project_status_has_folder_access = new ProjectStatusHasFolder_Access(null);
				if ($project_status_has_folder_access->create($this->ci_project_id, $this->ci_project_status_id, $folder_id) == null)
				{
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
	
	/**
	 * Injects $project_id into create()
	 * @param integer $project_id
	 */
	public function ci_set_project_id($project_id)
	{
		$this->ci_project_id = $project_id;
	}
	
	/**
	 * Injects $project_status_id into create()
	 * @param integer $project_status_id
	 */
	public function ci_set_project_status_id($project_status_id)
	{
		$this->ci_project_status_id = $project_status_id;
	}
	
	/**
	 * @see ConcreteFolderCaseInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->project_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->project_status_folder->delete() == true)
			{
				if (parent::delete() == true)
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
	
	/**
	 * @param integer $project_id
	 * @param integer $project_status_id
	 * @return integer
	 */
	public static function get_folder_by_project_id_and_project_status_id($project_id, $project_status_id)
	{
		return ProjectStatusHasFolder_Access::get_entry_by_project_id_and_project_status_id($project_id, $project_status_id);
	}
}
?>