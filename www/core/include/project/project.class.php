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
require_once("interfaces/project.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/project_creation_failed_exception.class.php");
	
	require_once("access/project.access.php");
	
	require_once("access/project_has_item.access.php");
	require_once("access/project_link.access.php");
	require_once("access/project_status.access.php");
	require_once("access/project_has_project_status.access.php");
	require_once("access/project_template_cat.access.php");
	require_once("access/project_template.access.php");
}

/**
 * Project Management Class
 * @package project
 */
class Project implements ProjectInterface, EventListenerInterface
{
	private $project_id;

	private $project;
	
	private $template_data_type;
	private $template_data_type_id;
	private $template_data_array;
	
	private $fulfilled_datetime_array;

	/**
	 * @param integer $project_id
	 */
	function __construct($project_id)
	{
		if ($project_id == null)
		{
			$this->project_id = null;
			$this->project = new Project_Access(null);
		}
		else
		{
			$this->project_id = $project_id;
			$this->project = new Project_Access($project_id);
		}
	}
	
	function __destruct()
	{
		unset($this->project_id);
		unset($this->project);
	}

	/**
	 * Sets the template data for project creation
	 * @param string $type
	 * @param integer $type_id
	 * @param array $array
	 * @return bool
	 */
  	public function set_template_data($type, $type_id, $array)
  	{
    	if (($type == "sample" or $type == "value") and is_array($array))
    	{
    		$this->template_data_type = $type;
    		$this->template_data_type_id = $type_id;
    		$this->template_data_array = $array;
    		return true;
    	}
    	else
    	{
    		return false;
    	} 	
    }

	/**
	 * Creates a new Project
	 * @todo Check project-permissions on sub-project creation
	 * @param integer $organisation_unit_id
	 * @param integer $parent_project_id
	 * @param string $name
	 * @param integer $owner_id
	 * @param integer $template_id
	 * @param string $description
	 * @return integer
	 */
	public function create($organisation_unit_id, $parent_project_id, $name, $owner_id, $template_id, $description)
	{
		global $transaction;

		if ($organisation_unit_id xor $parent_project_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($name and $owner_id and $template_id and $description and $this->project)
			{
				if ($organisation_unit_id)
				{
					$organisation_unit = new OrganisationUnit($organisation_unit_id);
					
					if ($organisation_unit->is_permission($owner_id))
					{						
						if (self::exist_project_name($organisation_unit_id, null, $name) == true)
						{
							throw new ProjectCreationFailedException("",1);
						}
					}
					else
					{
						throw new ProjectCreationFailedException("",1);
					}
				}
				else
				{	
					// !! Check Permissions !!
					if (true)
					{
						if (self::exist_project_name(null, $parent_project_id , $name) == true)
						{
							throw new ProjectCreationFailedException("",1);
						}
					}
					else
					{
						throw new ProjectCreationFailedException("",1);
					}
				}
				
				$user = new User($owner_id);
				$project_quota = $user->get_project_quota();
		
				// Create Project
				$project_id = $this->project->create($organisation_unit_id, $parent_project_id, $name, $owner_id, $template_id, $project_quota);
				
				if ($project_id)
				{
					$this->__construct($project_id);
					
					$project_template = new ProjectTemplate($template_id);
					$project_all_status_array = $project_template->get_all_status();
					
					$project_has_project_status = new ProjectHasProjectStatus_Access(null);
					if ($project_has_project_status->create($project_id,$project_all_status_array[0]) != true)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectCreationFailedException("",1);
					}
					
					// Create Projectfolder
					if ($organisation_unit_id)
					{
						$project_folder_id = $GLOBALS[project_folder_id];
						$folder = new Folder($project_folder_id);
					}
					else
					{
						$project_folder_id = Folder::get_project_folder_by_project_id($parent_project_id);
						$folder = new Folder($project_folder_id);
					}
					
					$path = new Path($folder->get_path());
					$path->add_element($project_id);
					
					$folder = new Folder(null);
					if (($folder_id = $folder->create($name, $project_folder_id, false, $path->get_path_string(), $owner_id, null)) != null)
					{
						if ($folder->create_project_folder($project_id) == false)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
						if ($folder->set_flag(16) == false)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
						
						// Create Supplementary Folder
						
						$supplementary_path = new Path($path->get_path_string());
						$supplementary_path->add_element("supplementary");
						
						$supplementary_folder = new Folder(null);
						if (($supplementary_folder->create("supplementary", $folder_id, false, $supplementary_path->get_path_string(), $owner_id, null)) == null)
						{
							$folder->delete();
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
						
						if ($supplementary_folder->set_flag(128) == false)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
						
						// Status Folder
						
						$folder_array = array();
						
						foreach($project_all_status_array as $key => $value)
						{
							$project_status_array = $project_template->get_status_requirements($value);

							if (is_array($project_status_array) and count($project_status_array) >= 1)
							{
								foreach($project_status_array as $status_key => $status_value)
								{
									if ($status_value[type] == "file" or $status_value[type] == "value")
									{
										if (!in_array($value, $folder_array))
										{
											array_push($folder_array, $value);
										}
									}
								}
							}	
						}	
						
						foreach($folder_array as $key => $value)
						{
							$project_status = new ProjectStatus($value);
							$project_status_requirements = $project_template->get_status_requirements($value);
							
							$status_path = new Path($path->get_path_string());
							$status_path->add_element("status-".$value);
							
							$status_folder = new Folder(null);
							if (($status_folder_id = $status_folder->create($project_status->get_name(), $folder_id, false, $status_path->get_path_string(), $owner_id, null)) == null)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectCreationFailedException("",1);
							}
							
							if ($status_folder->create_project_status_folder($value, $project_id) == false)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectCreationFailedException("",1);
							}
							
							if ($status_folder->set_flag(256) == false)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectCreationFailedException("",1);
							}
							
							// Create Subfolder
							$sub_folder_array = array();
							
							foreach($project_status_requirements as $sub_key => $sub_value)
		    				{
		    					if (($sub_value[type] == "file" or $sub_value[type] == "value") and $sub_value[folder])
		    					{
									if (array_search(trim($sub_value[folder]), $sub_folder_array) === false)
									{
										array_push($sub_folder_array, trim($sub_value[folder]));
									}
								}
		    					
		    				}
		    				
		    				if (is_array($sub_folder_array) and count($sub_folder_array) >= 1)
		    				{
		    					foreach($sub_folder_array as $sub_key => $sub_value)
		    					{
		    						$folder_name = strtolower(trim($sub_value));
		    						$folder_name = str_replace(" ","-",$folder_name);
		    										
									$folder_path = new Path($status_folder->get_path());
									$folder_path->add_element($folder_name);
									
									$sub_folder = new Folder(null);
									if ($sub_folder->create($sub_value, $status_folder_id, false, $folder_path->get_path_string(), $user->get_user_id(), null) == null)
									{
										$folder->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new ProjectCreationFailedException("",1);
									}
									
									if ($sub_folder->set_flag(2048) == false)
									{
										$folder->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new ProjectCreationFailedException("",1);
									}
		    					}
		    				}	
						}			
						
						// Create Project Description
						$value = new Value(null);
						if ($value->create($folder_id, $owner_id, 2, $description, false) == null)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
						
						$project_item = new ProjectItem($project_id);
						$project_item->set_gid(1);
						$project_item->set_status_id(1);
						$project_item->set_item_id($value->get_item_id());
						
						if ($project_item->link_item() == false)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
						
						if ($project_item->set_required(true) == false)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
						
						// Create Project Master Data
						if ($this->template_data_array and is_numeric($this->template_data_type_id))
						{
							$value = new Value(null);				
							
							if ($value->create($folder_id, $owner_id, $this->template_data_type_id, $this->template_data_array, false) == null)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectCreationFailedException("",1);
							}
							
							$project_item = new ProjectItem($project_id);
							$project_item->set_gid(1);
							$project_item->set_status_id(1);
							$project_item->set_item_id($value->get_item_id());
							
							if ($project_item->link_item() == false)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}	
								throw new ProjectCreationFailedException("",1);
							}
							
							if ($project_item->set_required(true) == false)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectCreationFailedException("",1);
							}
						}

						if ($organisation_unit_id)
						{
							$organisation_unit = new OrganisationUnit($organisation_unit_id);
		
							// Project Permissions
							$project_permission = new ProjectPermission(null);
							if ($project_permission->create($owner_id, null, null, $project_id, $GLOBALS[std_perm_user], null, 1) == null)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectCreationFailedException("",1);
							}
						
							$project_permission = new ProjectPermission(null);
							if ($project_permission->create($organisation_unit->get_leader_id(), null, null, $project_id, $GLOBALS[std_perm_organ_leader], null, 2) == null)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectCreationFailedException("",1);
							}
							
							
							// Virtual-Folder von OrganUnits und Groups
						
							$project_permission = new ProjectPermission(null);
							if ($project_permission->create(null, $organisation_unit_id, null, $project_id, $GLOBALS[std_perm_organ_unit], null, 3) == null)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectCreationFailedException("",1);
							}
						
							$group_array = $organisation_unit->list_groups();
							
							if(is_array($group_array) and count($group_array) >= 1)
							{
								foreach($group_array as $key => $value)
								{
									$project_permission = new ProjectPermission(null);
									if ($project_permission->create(null, null, $value, $project_id, $GLOBALS[std_perm_organ_group], null, 4) == null)
									{
										$folder->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new ProjectCreationFailedException("",1);
									}
								}
							}
						}
						
						// Create Virtual Sample Folder
						// !! Problematic Dependency
						$virtual_folder = new VirtualFolder(null);
						if ($virtual_folder->create($folder_id, "samples") == null)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
						if ($virtual_folder->set_sample_vfolder() == false)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ProjectCreationFailedException("",1);
						}
			
						$this->__construct($project_id);
						if ($transaction_id != null)
						{
							$transaction->commit($transaction_id);
						}
						return $project_id;
						
					}
					else
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectCreationFailedException("",1);
					}
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new ProjectCreationFailedException("",1);
				}
			}
			else
			{
				throw new ProjectCreationFailedException("",1);
			}
		}
		else
		{
			throw new ProjectCreationFailedException("",1);
		}
    }

	/**
	 * Deletes a project
	 * @return bool
	 */
    public function delete()
    {
    	global $transaction;
    	
    	if ($this->project_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		if ($this->exist_subproject() == true)
    		{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
					return false;
				}
    		}
    		
    		$tmp_project_id = $this->project_id;

    		// Permissions
    		$project_permission_array = ProjectPermission::list_entries_by_project_id($tmp_project_id);
    		
    		if (is_array($project_permission_array) and count($project_permission_array) >= 1)
    		{
	    		foreach ($project_permission_array as $key => $value)
	    		{
	    			$project_permission = new ProjectPermission($value);
	    			if ($project_permission->delete() == false)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return false;
	    			}
	    		}
    		}
    			    		
    		// Log Entries
    		$project_log_array = ProjectLog::list_entries_by_project_id($tmp_project_id);
    		
    		if (is_array($project_log_array) and count($project_log_array) >= 1)
    		{
	    		foreach ($project_log_array as $key => $value)
	    		{
	    			$project_log = new ProjectLog($value);
	    			if ($project_log->delete() == false)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return false;
	    			}
	    		}
    		}

    		// Project Status
    		$project_has_project_status_array = ProjectHasProjectStatus_Access::list_entries_by_project_id($tmp_project_id);
    		
    		if (is_array($project_has_project_status_array) and count($project_has_project_status_array) >= 1)
    		{
	    		foreach ($project_has_project_status_array as $key => $value)
	    		{
	    			$project_has_project_status = new ProjectHasProjectStatus_Access($value);
	    			if ($project_has_project_status->delete() == false)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return false;
	    			}
	    		}
    		}
    		
    		// Project Links
    		$project_link_array = ProjectLink_Access::list_entries_by_project_id($tmp_project_id);
    		
    		if (is_array($project_link_array) and count($project_link_array) >= 1)
    		{
	    		foreach ($project_link_array as $key => $value)
	    		{
	    			$project_link = new ProjectLink_Access($value);
	    			if ($project_link->delete() == false)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return false;
	    			}
	    		}
    		}
    		    		
    		// Methods and Remaining Item-Links (including Sample Links)
    		$project_item = new ProjectItem($tmp_project_id);
			$item_array = $project_item->get_project_items();
    		
    		if (is_array($item_array) and count($item_array) >= 1)
    		{
				foreach($item_array as $key => $value)
				{
					$item = new Item($value);
				
					if (($method_id = $item->get_method_id()) == true)
					{
						$method = new Method($method_id);
						if ($method->delete() == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
					elseif($item->get_sample_id() != null)
					{
						$project_item_sample = new ProjectItem($tmp_project_id);
						$project_item_sample->set_item_id($value);
						$project_item_sample->unlink_item_full();
					}
					else
					{
						if ($item->delete() == false)
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
    		
    		// Project Tasks
    		$project_task_array = ProjectTask::list_tasks($tmp_project_id);
    		
    		if (is_array($project_task_array) and count($project_task_array) >= 1)
    		{
    			foreach($project_task_array as $key => $value)
    			{
    				$project_task = new ProjectTask($value);
    				if ($project_task->delete() == false)
    				{
    					if ($transaction_id != null)
    					{
							$transaction->rollback($transaction_id);
						}
						return false;
    				}
    			}
    		} 
    		
    		// Project DB Entry
    		if ($this->project->delete() == false)
    		{
    			if ($transaction_id != null)
    			{
					$transaction->rollback($transaction_id);
				}
				return false;
    		}
    		else
    		{
    			$this->__destruct();
	    		$folder_id = Folder::get_project_folder_by_project_id($tmp_project_id);
	    		$folder = new Folder($folder_id);
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
   
    /**
     * @return bool
     */   
   	public function exist_subproject()
    {		
		if ($this->project_id and $this->project)
		{
			$subproject_array = Project_Access::list_entries_by_toid_project($this->project_id);
			
			if (count($subproject_array) >= 1)
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
	 * Checks, if the next status is available
	 * @return integer
	 */
    public function is_next_status_available()
    {
		if ($this->project_id and $this->project)
		{
	    	$next_status_id = $this->get_next_status_id();
	    	$current_status_id = $this->get_current_status_id();
	    	
	    	if ($current_status_id == 2 or $current_status_id == 0)
	    	{
	    		return 0;
	    	}
	    	else
	    	{
	    		$project_status = new ProjectStatus($current_status_id);
	    		
	    		if ($project_status->get_blocked() == true)
	    		{
	    			return 2;
	    		}
	    		else
	    		{
	    			return 1;
	    		}
	    	}
		}
		else
		{
			return null;
		}
    }
    
    /**
     * Checks the fulfillment of the current project status
     * @return bool
     */
    public function is_current_status_fulfilled()
    {
    	$current_status_requirements 	= $this->get_current_status_requirements();
		$current_fulfilled_requirements = $this->get_fulfilled_status_requirements();

		if (is_array($current_status_requirements) and count($current_status_requirements) >= 1)
		{
			$not_fulfilled = false;
		
			foreach($current_status_requirements as $key => $value)
			{
				if ($current_fulfilled_requirements[$key] == false and $current_status_requirements[$key][requirement] != "optional")
				{
					$not_fulfilled = true;
				}
			}
			
			if ($not_fulfilled == true)
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
    
    /**
     * Returns an array with all project status
     * @return array
     */
    public function get_all_status_array()
    {
    	global $runtime_data;
    	
    	if ($this->project_id and $this->project)
    	{
			if ($runtime_data->is_object_data($this, "PROJECT_ALL_STATUS_ARRAY") == true)
			{
				return $runtime_data->read_object_data($this, "PROJECT_ALL_STATUS_ARRAY");	
			}
			else
			{
	    		$return_array = array();
	    		
	    		$project_template = new ProjectTemplate($this->project->get_template_id());
	    		$all_status_array = $project_template->get_all_status();
	    		
	    		if (is_array($all_status_array) and count($all_status_array) >= 1)
	    		{
					$project_canceled 	= false;
					$status_found 		= false;	
					$datetime 			= null;	
		
	    			foreach($all_status_array as $key => $value)
	    			{
	    				$temp_array = array();
	    				
	    				$project_has_status_array = ProjectHasProjectStatus_Access::list_entries_by_project_id($this->project_id);
	    				
	    				if (is_array($project_has_status_array) and count($project_has_status_array) >= 1)
	    				{
	    					foreach($project_has_status_array as $status_key => $status_value)
	    					{
	    						$project_has_project_status = new ProjectHasProjectStatus_Access($status_value);
	    						
	    						if ($status_found == true and $datetime == null)
	    						{
	    							$datetime = $project_has_project_status->get_datetime();
	    						}
	    						
	    						if ($project_has_project_status->get_status_id() == $value)
	    						{
	    							$status_found = true;
	    						}
	    						
	    						if ($project_has_project_status->get_status_id() == 0)
	    						{
	    							$project_canceled = true;
	    						}
	    					}
	    				}
	    				
	    				$temp_array[id] = $value;
	    				
	    				$status_attribute_array = $project_template->get_status_attributes($value);
	    				
	    				if ($status_attribute_array[requirement] == "optional")
	    				{
	    					$temp_array[optional] = true;
	    				}
	    				else
	    				{
	    					$temp_array[optional] = false;
	    				}
	    				
	    				if ($status_found == true)
	    				{
	    					if ($datetime == null)
	    					{
	    						$temp_array[datetime] = date("Y-m-d H:i:s");
	    					}
	    					else
	    					{
	    						$temp_array[datetime] = $datetime;
	    						$datetime = null;
	    					}
	    					
							if ($project_canceled == true)
							{
								$temp_array[status] = 3;
							}
							else
							{
								$temp_array[status] = 1;	
							}
	    					
	    					if (($last_element = array_pop($return_array)) != null)
	    					{
	    						$last_element[status] = 2;
	    						array_push($return_array, $last_element);
	    					}
	    					
	    					$status_found = false;
	    					$datetime = null;
	    				}
	    				else
	    				{
	    					$temp_array[status] = 0;
	    				}
						array_push($return_array, $temp_array);
	    			}
	    			
	    			if ($this->get_current_status_id() == 2)
	    			{
	    				if (($last_element = array_pop($return_array)) != null)
	    				{
							$last_element[status] = 2;
							array_push($return_array, $last_element);
						}
	    			}
	    			$runtime_data->write_object_data($this, "PROJECT_ALL_STATUS_ARRAY", $return_array);
	    			return $return_array;
	    		}
	    		else
	    		{
	    			return null;
	    		}
			}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Returns the id of the current status
     * @return integer
     */
    public function get_current_status_id()
    {
    	if ($this->project_id)
    	{
    		$project_has_project_status_array = ProjectHasProjectStatus_Access::list_entries_by_project_id($this->project_id);
    		
    		if (is_array($project_has_project_status_array) and count($project_has_project_status_array) >= 1)
    		{
    			$project_has_project_status_pk = array_pop($project_has_project_status_array);
    			$project_has_project_status = new ProjectHasProjectStatus_Access($project_has_project_status_pk);  	
    			return $project_has_project_status->get_status_id();	
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
     * Returns the name of the current status
     * @return string
     */
    public function get_current_status_name()
    {
    	if ($this->project_id)
    	{
    		$current_status_id = $this->get_current_status_id();
    		$project_status = new ProjectStatus($current_status_id);
    		return $project_status->get_name();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Returns an array with all requirements of the current status
     * @return array
     */
    public function get_current_status_requirements()
    {
    	global $runtime_data;
    	
    	if ($this->project_id and $this->project)
    	{
    		if ($runtime_data->is_object_data($this, "PROJECT_CURRENT_STATUS_REQUIREMENTS") == true)
    		{
				return $runtime_data->read_object_data($this, "PROJECT_CURRENT_STATUS_REQUIREMENTS");	
			}
			else
			{
		    	$project_template = new ProjectTemplate($this->project->get_template_id());
		    	
		    	$status_attribute_array = $project_template->get_status_attributes($this->get_current_status_id());
		    	$requirements_array = $project_template->get_status_requirements($this->get_current_status_id());
				
				if ($status_attribute_array[requirement] == "optional")
				{
					$requirement_default = "optional";
				}
				else
				{
					$requirement_default = "force";
				}
				
				$return_array = array();
				$counter = 0;
				$type_counter = 0;
				$category_counter = 0;
	
				if (is_array($requirements_array) and count($requirements_array) >= 1)
				{
					foreach($requirements_array as $key => $value)
					{
						if ($value[xml_element] == "item" and !$value[close])
						{
							$in_item = true;
							$return_array[$counter][type] = $value[type];
							$return_array[$counter][name] = $value[name];
							
							if ($value[requirement] and $status_attribute_array[requirement] != "optional")
							{
								$return_array[$counter][requirement] = $value[requirement];
							}
							else
							{
								$return_array[$counter][requirement] = $requirement_default;
							}
							
							if ($value[occurrence])
							{
								$return_array[$counter][occurrence] = $value[occurrence];
							}
							else
							{
								$return_array[$counter][occurrence] = "once";
							}
						}
						
						if ($value[xml_element] == "item" and $value[close] == "1")
						{
							$counter++;
							$type_counter = 0;
							$category_counter = 0;
							$in_item = false;
						}
						
						if ($value[xml_element] == "type" and !$value[close] and $in_item == true and is_numeric($value[id]))
						{
							$return_array[$counter][type_id][$type_counter] = $value[id];
							$type_counter++;
						}
						
						if ($value[xml_element] == "category" and !$value[close] and $in_item == true and is_numeric($value[id]))
						{
							$return_array[$counter][category_id][$category_counter] = $value[id];
							$category_counter++;
						}					
					}
				}
				$runtime_data->write_object_data($this, "PROJECT_CURRENT_STATUS_REQUIREMENTS", $return_array);	
				return $return_array;
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Retruns an array with all fulfilled requirements of the current status
     * @return array
     */
    public function get_fulfilled_status_requirements()
    {
    	global $runtime_data;
    	
    	if ($this->project_id and $this->project)
    	{
    		if ($runtime_data->is_object_data($this, "PROJECT_FULFILLED_STATUS_REQUIREMENTS") == true)
    		{
				return $runtime_data->read_object_data($this, "PROJECT_FULFILLED_STATUS_REQUIREMENTS");	
			}
			else
			{
		    	$requirements_array = $this->get_current_status_requirements();			
				$fulfilled_array = array();
				
				if (is_array($requirements_array) and count($requirements_array) >= 1)
				{
					foreach($requirements_array as $key => $value)
					{
						if ($value[gid])
						{
							$gid = $value[gid];
						}
						else
						{
							$gid = $key;
						}
						
						switch($value[type]):
							case("file"):
								$project_item = new ProjectItem($this->project_id);
								$item_array = $project_item->get_project_items();
								if (is_array($item_array) and count($item_array) >= 1)
								{
									foreach($item_array as $item_key => $item_value)
									{
										$item_has_project_status = new ItemHasProjectStatus($item_value, $this->get_current_status_id());
										if ($item_has_project_status->is_object() == true and $item_has_project_status->get_gid() == $gid)
										{
											$item = new Item($item_value);
											$object = new Object($item->get_object_id());
											
											if ($object->get_file_id() != null)
											{
												$fulfilled_array[$key] = true;
												$this->fulfilled_datetime_array[$key] = $item->get_datetime();
											}	
										}
									}
								}
							break;
								
							case("value"):
								$project_item = new ProjectItem($this->project_id);
								$item_array = $project_item->get_project_items();
								if (is_array($item_array) and count($item_array) >= 1)
								{
									foreach($item_array as $item_key => $item_value)
									{
										$item_has_project_status = new ItemHasProjectStatus($item_value, $this->get_current_status_id());
										if ($item_has_project_status->is_object() == true and $item_has_project_status->get_gid() == $gid)
										{
											$item = new Item($item_value);
											$object = new Object($item->get_object_id());
	
											if(($value_id = $object->get_value_id()) != null)
											{
												$value_obj = new Value($value_id);
												if (is_array($value[type_id]))
												{
													if (in_array($value_obj->get_type_id(),$value[type_id]))
													{
														$fulfilled_array[$key] = true;	
														$this->fulfilled_datetime_array[$key] = $item->get_datetime();
													}
												}
												else
												{
													$fulfilled_array[$key] = true;	
													$this->fulfilled_datetime_array[$key] = $item->get_datetime();
												}											
											}
										}
									}
								}
							break;
							
							case("method"):
								$project_item = new ProjectItem($this->project_id);
								$item_array = $project_item->get_project_items();
								if (is_array($item_array) and count($item_array) >= 1)
								{
									foreach($item_array as $item_key => $item_value)
									{
										$item_has_project_status = new ItemHasProjectStatus($item_value, $this->get_current_status_id());
										
										if ($item_has_project_status->is_method() == true and $item_has_project_status->get_gid() == $gid)
										{
											$fulfilled_array[$key] = true;	
											$item = new Item($item_value);
											$this->fulfilled_datetime_array[$key] = $item->get_datetime();
										}
									}
								}
							break;
							
							case("sample"):
								$project_item = new ProjectItem($this->project_id);
								$item_array = $project_item->get_project_items();
								if (is_array($item_array) and count($item_array) >= 1)
								{
									foreach($item_array as $item_key => $item_value)
									{
										$item_has_project_status = new ItemHasProjectStatus($item_value, $this->get_current_status_id());
										
										if ($item_has_project_status->is_sample() == true)
										{
											$item_has_project_status = new ItemHasProjectStatus($item_value, $this->get_current_status_id());
											
											if ($item_has_project_status->is_sample() == true and $item_has_project_status->get_gid() == $gid)
											{
												$fulfilled_array[$key] = true;	
												$item = new Item($item_value);
												$this->fulfilled_datetime_array[$key] = $item->get_datetime();
											}	
										}
									}
								}
							break;
						endswitch;
					}
					$runtime_data->write_object_data($this, "PROJECT_FULFILLED_STATUS_DATETIMES", $this->fulfilled_datetime_array);
					$runtime_data->write_object_data($this, "PROJECT_FULFILLED_STATUS_REQUIREMENTS", $fulfilled_array);
	    		
					return $fulfilled_array;
				}
				else
				{
					return null;
				}
    		}
    	}
    	else
    	{
    		return null;
    	}
    }

	/**
	 * @return array
	 */
	public function get_fulfilled_status_datetimes()
	{
		global $runtime_data;
		
		if ($runtime_data->is_object_data($this, "PROJECT_FULFILLED_STATUS_DATETIMES") == true)
		{
			return $runtime_data->read_object_data($this, "PROJECT_FULFILLED_STATUS_DATETIMES");	
		}
		else
		{
			return $this->fulfilled_datetime_array;
		}
	}
    
    /**
     * Returns the ID of the next status of the current project
     * @return integer
     */
    public function get_next_status_id()
    {    	
		if ($this->project)
		{
			$project_template = new ProjectTemplate($this->project->get_template_id());
			return $project_template->get_next_status($this->get_current_status_id());
		}
		else
		{
			return null;
		}
    }
    
    /**
     * Returns the name of the next status of the current project
     * @return string
     */
    public function get_next_status_name()
    {
		if ($this->is_next_status_available())
		{
			$next_status_id = $this->get_next_status_id();
			$project_status = new ProjectStatus($next_status_id);
			
    		return $project_status->get_name();
		}
		else
		{
			return null;
		}	
    }
    
    /**
     * Proceed the current project to the next status
     * @param string $checksum
     * @param string $comment Optional Comment
     * @return bool
     */
    public function set_next_status($checksum, $comment)
    {
    	global $transaction;

		if ($this->project_id)
		{
			$transaction_id = $transaction->begin();
	
			$current_status_id = $this->get_current_status_id();
			$next_status_id = $this->get_next_status_id();
			
			$project_has_project_status = new ProjectHasProjectStatus_Access(null);
			if ($project_has_project_status->create($this->project_id, $next_status_id) != null)
			{
				if (ProjectTask::check_over_time_tasks($this->project_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				$project_log = new ProjectLog(null);
				if ($project_log->create($this->project_id, null, false, false, md5(rand(0,32768))) != null)
				{
					if ($project_log->link_status($next_status_id) == true)
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
     * Returns the requirements of a given status
     * @param integer $status_id
     * @return array
     */
    public function get_status_requirements($status_id)
    {
    	if ($this->project_id and $this->project and is_numeric($status_id))
    	{
	    	$project_template = new ProjectTemplate($this->project->get_template_id());
	    	
	    	$status_attribute_array = $project_template->get_status_attributes($status_id);
	    	$requirements_array = $project_template->get_status_requirements($status_id);
			
			if ($status_attribute_array[requirement] == "optional")
			{
				$requirement_default = "optional";
			}
			else
			{
				$requirement_default = "force";
			}
			
			$return_array = array();
			$counter = 0;
			$type_counter = 0;

			if (is_array($requirements_array) and count($requirements_array) >= 1)
			{
				foreach($requirements_array as $key => $value)
				{
					if ($value[xml_element] == "item" and !$value[close])
					{
						$in_item = true;
						$return_array[$counter][type] = $value[type];
						$return_array[$counter][name] = $value[name];
						
						if ($value[requirement] and $status_attribute_array[requirement] != "optional")
						{
							$return_array[$counter][requirement] = $value[requirement];
						}
						else
						{
							$return_array[$counter][requirement] = $requirement_default;
						}
						
						if ($value[occurrence])
						{
							$return_array[$counter][occurrence] = $value[occurrence];
						}
						else
						{
							$return_array[$counter][occurrence] = "once";
						}
					}
					
					if ($value[xml_element] == "item" and $value[close] == "1")
					{
						$counter++;
						$type_counter = 0;
						$in_item = false;
					}
					
					if ($value[xml_element] == "type" and !$value[close] and $in_item == true and is_numeric($value[id]))
					{
						$return_array[$counter][type_id][$type_counter] = $value[id];
						$type_counter++;
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
     * Returns subfolder of a given gid
     * @param integer $folder_id Folder-ID
     * @param integer $gid 
     * @param integer $status_id
     * @return string Sub-Folder-Path
     */
    public function get_sub_folder($gid, $status_id)
    {
    	if ($this->project_id and $this->project)
    	{
	    	if (is_numeric($gid) and is_numeric($status_id))
	    	{
	    		$folder_id = Folder::get_project_status_folder_by_status_id($this->project_id, $status_id);
	    		
    			$folder = new Folder($folder_id);
    		
    			$project_template = new ProjectTemplate($this->project->get_template_id());
    			$attribute_array = $project_template->get_gid_attributes($gid, $status_id);
    			
    			if ($attribute_array[folder])
    			{
    				$folder_name = strtolower(trim($attribute_array[folder]));
    				$folder_name = str_replace(" ","-",$folder_name);
    				
    				$folder_path = new Path($folder->get_path());
					$folder_path->add_element($folder_name);
    				return Folder::get_folder_by_path($folder_path->get_path_string());	    				
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
    	else
    	{
    		return null;
    	}
    } 
    
    /**
     * Moves the project to an organisation unit (sub-project will be normal-projects)
     * @param integer $organisation_unit_id
     * @return bool
     */    
	public function move_to_organisation_unit($organisation_unit_id)
	{
		global $user, $transaction;
		
		if ($this->project_id and $this->project and is_numeric($organisation_unit_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
						
			if ($organisation_unit->is_permission($user->get_user_id()))
			{
				if (self::exist_project_name($organisation_unit_id, null, $this->project->get_name()) == false)
				{
					$transaction_id = $transaction->begin();
					
					$project_security = new ProjectSecurity($this->project_id);
			
					if ($project_security->change_owner_permission($this->get_owner_id()) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
					}
					
					if ($project_security->change_leader_permission($organisation_unit->get_leader_id()) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($project_security->change_organisation_unit_permission($organisation_unit_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->project->set_toid_project(null) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->project->set_toid_organ_unit($organisation_unit_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}			
					
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
		else
		{
			return false;
		}
	}
	
	/**
     * Moves the project to another project (normal-projects will be sub-projects)
     * @param integer $organisation_unit_id
     * @return bool
     */   
	public function move_to_project($project_id)
	{
		global $user;
	
		if ($this->project_id and $this->project and is_numeric($project_id))
		{
			$project_security = new ProjectSecurity($this->project_id);
		
			if ($project_security->is_access(3, false))
			{
				if (self::exist_project_name(null, $project_id , $this->project->get_name()) == false)
				{
					$transaction_id = $transaction->begin();
					
					$project_permission_array = ProjectPermission::list_entries_by_project_id($this->project_id);
					
					if (is_array($project_permission_array) and count($project_permission_array) >= 1)
					{
						foreach($project_permission_array as $key => $value)
						{
							$project_permission = new ProjectPermission($value);
							if ($project_permission->delete() == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
					}
					
					if ($this->project->set_toid_organ_unit(null) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->project->set_toid_project($project_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
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
    	if ($this->project_id and $this->project)
    	{			
			$project_log = new ProjectLog(null);
			$project_log->create($this->project_id, "Project Deleted", false, true, md5(rand(0,32768)));
			
			$folder_id = Folder::get_project_folder_by_project_id($this->project_id);
			$folder = new Folder($folder_id);
			$folder->mark_as_deleted();
			
			return $this->project->set_deleted(true);
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
    	if ($this->project_id and $this->project)
    	{	
    		$project_log = new ProjectLog(null);
			$project_log->create($this->project_id, "Project Restored", false, true, md5(rand(0,32768)));
    				
			return $this->project->set_deleted(false);
		}
		else
		{
			return false;
		}
    } 
    
    /**
     * @return bool
     */
    public function mark_as_canceled($comment)
    {
    	if ($this->project_id and $this->project)
    	{					
			$project_has_project_status = new ProjectHasProjectStatus_Access(null);
			if ($project_has_project_status->create($this->project_id, 0) != null)
			{
				$project_log = new ProjectLog(null);
				$project_log->create($this->project_id, null, true, true, md5(rand(0,32768)));
				$project_log->link_status(0);
			
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
     * @return bool
     */
    public function mark_as_reactivated()
    {
    	if ($this->project_id and $this->project)
    	{	
			$project_has_project_status_pk = ProjectHasProjectStatus_Access::list_entries_by_project_id($this->project_id);
			
			$project_has_project_status	= new ProjectHasProjectStatus_Access($project_has_project_status_pk[count($project_has_project_status_pk)-1]);
			if ($project_has_project_status->delete() == true)
			{	
				$project_log = new ProjectLog(null);
				$project_log->create($this->project_id, "Project Reactivated", false, true, md5(rand(0,32768)));
				$project_log->link_status(0);
			
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
     * Returns a list of related projects
     * @return array
     */	
	public function list_project_related_projects()
	{
		if ($this->project_id and $this->project)
		{
			return Project_Access::list_entries_by_toid_project($this->project_id);
		}
		else
		{
			return null;
		}
	}
    
    /**
     * Returns the project-tree (as an array) of the current project
     * @return array
     */
    public function get_project_tree()
    {
    	if ($this->project_id)
    	{
    		$project_id = $this->get_master_project_id();
    		
    		if ($project_id)
    		{
	    		$return_array = self::make_project_tree_rec(1, $project_id);
	    		
	    		$data_array = array();
	    		$data_array[id] = $project_id;
	    		$data_array[layer] = 0;
	    		
	    		array_unshift($return_array, $data_array);
	    	
	    		return $return_array;
    		}
    		else
    		{
    			return null;
    		}
    	}
    	else
    	{
    		$project_array = self::list_user_related_projects(null, false);
    		
    		if (is_array($project_array) and count($project_array) >= 1)
    		{
    			$return_array = array();
    			
    			foreach($project_array as $key => $value)
    			{
    				$tmp_array = self::make_project_tree_rec(1, $value);
	    		
		    		$data_array = array();
		    		$data_array[id] = $value;
		    		$data_array[layer] = 0;
		    		
		    		array_unshift($tmp_array, $data_array);
		    		$return_array = array_merge($return_array, $tmp_array);
    			}
    			return $return_array;
    		}
    		else
    		{
    			return null;
    		}
    	}
    }
    
    /**
     * Returns the id of the master-project
     * @return integer
     */
    public function get_master_project_id()
    {
    	if ($this->project_id)
    	{
			$project_id = $this->project_id;
			
			do
			{
				$project = new Project($project_id);
    			
    			if ($project->get_project_toid() != null)
    			{
    				$project_id = $project->get_project_toid();
    			}
    			
    			if ($project->get_project_toid() == $project_id)
    			{
    				continue;
    			}
			}
			while($project->get_project_toid() != null);

			if ($project_id)
			{
				return $project_id;
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
     * @return string
     */
    public function get_name()
    {
		if ($this->project_id and $this->project)
		{
    		return trim($this->project->get_name());
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
    	if ($this->project_id and $this->project)
    	{
    		return $this->project->get_datetime();
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
    	if ($this->project_id and $this->project)
    	{
    		return $this->project->get_owner_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return integer
     */
    public function get_organisation_unit_id()
    {
    	if ($this->project_id and $this->project)
    	{
    		return $this->project->get_toid_organ_unit();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return integer
     */
    public function get_project_toid()
    {
    	if ($this->project_id and $this->project)
    	{
    		return $this->project->get_toid_project();
    	}
    	else
    	{
    		return null;
    	}
    }

	/**
	 * @return string
	 */
	public function get_description()
	{
		if ($this->project_id)
		{
			$project_item = new ProjectItem($this->project_id);
			$item_array = $project_item->get_project_items();
			
			if (is_array($item_array) and count($item_array) >= 1)
			{
				foreach($item_array as $key => $value)
				{
					$item = new Item($value);
										
					if (($object_id = $item->get_object_id()) != null)
					{
						$object = new Object($object_id);
						
						if (($value_id = $object->get_value_id()) != null)
						{
							$value = new Value($value_id);
							if ($value->get_type_id() == 2)
							{
								return unserialize($value->get_value());
							}
						}
					}
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
	 * @return integer
	 */
    public function get_template_id()
    {
		if ($this->project_id and $this->project)
		{
    		return $this->project->get_template_id();
    	}
    	else
    	{
    		return null;
    	}
    }

	/**
	 * @return string
	 */
    public function get_template_name()
    {
		if ($this->project_id and $this->project)
		{
			$project_template = new ProjectTemplate($this->project->get_template_id());
			return $project_template->get_name();
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return integer
     */
    public function get_quota()
    {
    	if ($this->project_id and $this->project)
    	{
    		return $this->project->get_quota();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return integer
     */
    public function get_filesize()
    {
    	if ($this->project_id and $this->project)
    	{
    		return $this->project->get_filesize();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return bool
     */
    public function get_deleted()
    {
    	if ($this->project_id and $this->project)
    	{
    		return $this->project->get_deleted();
    	}
    	else
    	{
    		return null;
    	}
    }

	/**
	 * @param integer $quota
	 * @return bool
	 */
    public function set_quota($quota)
   	{
    	if ($this->project_id and $this->project and $quota)
    	{
    		if ($quota == null)
    		{
    			$quota = "0";
    		}
    		return $this->project->set_quota($quota);  		
    	}
    	else
    	{
    		return false;
    	}		
    	
    }

	/**
	 * @param integer $filesize
	 * @return bool
	 */
    public function set_filesize($filesize)
    {
    	if ($this->project_id and $this->project and $filesize)
    	{
    		if ($filesize == null)
    		{
    			$filesize = "0";
    		}
    		return $this->project->set_filesize($filesize);      		
    	}
    	else
    	{
    		return false;
    	}
    }

	/**
	 * @todo Transaction
	 * @todo Folder Name
	 * @param string $name
	 * @return bool
	 */
    public function set_name($name)
    {
    	global $transaction;
    	
    	if ($this->project_id and $this->project and $name)
    	{
    		if ($this->project->get_toid_organ_unit())
    		{
    			if (self::exist_project_name($this->project->get_toid_organ_unit(), null, $name) == true) {
					return false;
				}
    		}
    		else
    		{
    			if (self::exist_project_name(null, $this->project->get_toid_project() , $name) == true) {
					return false;
				}
    		}
    		
    		$transaction_id = $transaction->begin();
    		
    		$folder_id = Folder::get_project_folder_by_project_id($this->project_id);
    		$folder = new Folder($folder_id);
    		
    		if ($folder->set_name($name) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			if ($this->project->set_name($name) == false)
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

	/**
	 * @param integer $owner_id
	 * @return bool
	 */
    public function set_owner_id($owner_id)
    {
    	if ($this->project_id and $this->project and is_numeric($owner_id))
    	{
    		$project_security = new ProjectSecurity($this->project_id);
    		$project_security->change_owner_permission($owner_id);
    		
    		return $this->project->set_owner_id($owner_id);
    	}
    	else
    	{
    		return false;
    	}
    } 
      
    
    /**
     * Returns a list of all user related projects
     * @param integer $user_id
     * @param bool $analysis_only
     * @return array
     */
	public static function list_user_related_projects($user_id, $analysis_only)
	{
		global $user;
		
		if ($user_id == null)
		{
			$user_id = $user->get_user_id();
		}
				
		$return_array = array();
		$project_array = Project_Access::list_root_entries();
		
		if (is_array($project_array) and count($project_array) >=1)
		{
			foreach($project_array as $key => $value)
			{
				$project = new Project($value);
				$project_security = new ProjectSecurity($value);
				$project_security->set_user_id($user_id);
				
				if ($project_security->is_access(3, true) or 
					$project_security->is_access(4, true) or 
					$project_security->is_access(7, true))
				{
					if ($project->get_deleted() == false)
					{	
						array_push($return_array, $value);
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
	 * Returns a list of all organisation-unit related projects
	 * @param integer $organisation_unit_id
	 * @param bool $include_deleted
	 * @return array
	 */
	public static function list_organisation_unit_related_projects($organisation_unit_id, $include_deleted)
	{	
		$project_array = Project_Access::list_entries_by_toid_organ_unit($organisation_unit_id);
	
		if ($include_deleted == false)
		{
			if (is_array($project_array) and count($project_array) >= 1)
			{
				$return_array = array();
				
				foreach($project_array as $key => $value)
				{
					$project = new Project($value);
					if ($project->get_deleted() == false)
					{
						array_push($return_array, $value);
					}
				}
				return $return_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return $project_array;
		}				
	}
    
    /**
     * @return array
     */  
    public static function list_entries_by_template_id($template_id)
    {
    	return Project_Access::list_entries_by_template_id($template_id);
    }
    
    /**
     * @param integer $toid_organ_unit
     * @param integer $toid_project
     * @param string $name
     * @return bool
     */
	public static function exist_project_name($toid_organ_unit, $toid_project, $name)
	{	
		if ($toid_project xor $toid_organ_unit)
		{
			if ($toid_organ_unit)
			{
				return Project_Access::exist_project_by_name_and_toid_organ_unit($name, $toid_organ_unit);
			}
			else
			{
				return Project_Access::exist_project_by_name_and_toid_project($name, $toid_project);
			}			
		}
		else
		{
			return true;
		}
	}
    
    /**
     * @param integer $project_id
     * @return bool
     */
   	public static function exist_project($project_id)
   	{
   		return Project_Access::exist_project_by_project_id($project_id);
   	}
      
    /**
     * @todo move to wrapper-class
     * @param string $name
     * @param array $organisation_unit_array
     * @param array $template_array
     * @param bool $sub_projects
     * @return array
     */
    public static function search_projects($name, $organisation_unit_array, $template_array, $sub_projects)
    {
    	if (($name or ((is_array($template_array) and count($template_array) >= 1))) and 
    		is_array($organisation_unit_array) and 
    		count($organisation_unit_array) >= 1)
    	{
			if ($name)
			{
				$name = strtolower($name);
			}

    		if ($sub_projects == true)
    		{
    			return Project_Access::search_projects_with_subprojects($name, $template_array, $organisation_unit_array);
    		}
    		else
    		{
    			return Project_Access::search_projects_without_subprojects($name, $template_array, $organisation_unit_array);
    		}
    	}
    	else
    	{
    		return null;
    	}
    } 
    
    /**
     * @return integer
     */
    public static function get_used_project_space()
    {
    	Project_Access::get_used_project_space();
    }
    
    /**
     * @param integer $layer
     * @param integer $id
     * @return array
     */
    private static function make_project_tree_rec($layer, $id)
    {
    	$return_array = array();
    	
    	$project = new Project($id);
    	$project_array = $project->list_project_related_projects();
    	
    	if (is_array($project_array) and count($project_array) >= 1)
    	{
	    	foreach($project_array as $key => $value)
	    	{
	    		$temp_array = array();
	    		$temp_array[id] = $value;
	    		$temp_array[layer] = $layer;
	    		
	    		array_push($return_array, $temp_array);
	    		
	    		$return_array = array_merge($return_array,self::make_project_tree_rec($layer+1, $value));	
	    	}
    		return $return_array;
    	}
    	else
    	{
    		return array();
    	}
    }
    
    /**
     * @todo implementation
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeletePrecheckEvent)
    	{
    		
    	}
    	
    	return true;
    }
       
}

?>