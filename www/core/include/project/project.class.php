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
require_once("interfaces/project.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
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
class Project implements ProjectInterface, EventListenerInterface, ItemHolderInterface
{
	private $project_id;

	private $project;
	
	private $template_data_type;
	private $template_data_type_id;
	private $template_data_array;
	
	private $fulfilled_datetime_array;

	private static $project_delete_array = array();
	
	/**
	 * @see ProjectInterface::__construct()
	 * @param integer $project_id
	 * @throws ProjectNotFoundException
	 */
	function __construct($project_id)
	{
		if (is_numeric($project_id))
		{
			if (Project_Access::exist_project_by_project_id($project_id) == true)
			{
				$this->project_id = $project_id;
				$this->project = new Project_Access($project_id);
			}
			else
			{
				if (in_array($project_id, self::$project_delete_array) == false)
				{
					throw new ProjectNotFoundException();
				}
			}
		}
		else
		{
			$this->project_id = null;
			$this->project = new Project_Access(null);
		}
	}
	
	function __destruct()
	{
		unset($this->project_id);
		unset($this->project);
	}

	/**
	 * @see ProjectInterface::set_template_data()
	 * @param string $type
	 * @param integer $type_id
	 * @param array $array
	 * @return bool
	 */
  	public function set_template_data($type, $type_id, $array)
  	{
    	if (is_array($array))
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
	 * @see ProjectInterface::create()
	 * @param integer $organisation_unit_id
	 * @param integer $parent_project_id
	 * @param string $name
	 * @param integer $owner_id
	 * @param integer $template_id
	 * @param string $description
	 * @return integer
	 * @throws ProjectCreateException
	 * @throws ProjectCreateProjectExistsException
	 * @throws ProjectCreateStatusException
	 * @throws ProjectCreateFolderException
	 * @throws ProjectCreateStatusFolderException
	 * @throws ProjectCreateStatusSubFolderException
	 * @throws ProjectCreateSupplementaryFolderException
	 * @throws ProjectCreateDescriptionException
	 * @throws ProjectCreateMasterDataException
	 * @throws ProjectCreatePermissionUserException
	 * @throws ProjectCreatePermissionLeaderException
	 * @throws ProjectCreatePermissionGroupException
	 * @throws ProjectCreatePermissionOrganisationUnitException
	 * @throws ProjectCreatePermissionQualityManagerException
	 */
	public function create($organisation_unit_id, $parent_project_id, $name, $owner_id, $template_id, $description)
	{
		global $transaction;
		
		if ($organisation_unit_id xor $parent_project_id)
		{
			if ($name and $owner_id and $template_id and $description and $this->project)
			{
				if ($organisation_unit_id)
				{
					$organisation_unit = new OrganisationUnit($organisation_unit_id);
					
					if ($organisation_unit->is_permission($owner_id))
					{						
						if (self::exist_project_name($organisation_unit_id, null, $name) == true)
						{
							throw new ProjectCreateProjectExistsException("Project already exists in this Organisation Unit");
						}
					}
					else
					{
						throw new ProjectCreateException("Cannot access on this Organisation Unit");
					}
				}
				else
				{	
					if (self::exist_project_name(null, $parent_project_id , $name) == true)
					{
						throw new ProjectCreateProjectExistsException("Project already exists in this Project");
					}
				}
				
				$transaction_id = $transaction->begin();
				
				$user = new User($owner_id);
				$project_user = new ProjectUserData($owner_id);
				$project_quota = $project_user->get_quota();
		
				// Create Project
				if (($project_id = $this->project->create($organisation_unit_id, $parent_project_id, $name, $owner_id, $template_id, $project_quota)) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id, false);
					}
					throw new ProjectCreateException("Could not create Project in DB");
				}
				else
				{
					$this->__construct($project_id);
					
					$project_template = new ProjectTemplate($template_id);
					$project_all_status_array = $project_template->get_all_status();
					
					$project_has_project_status = new ProjectHasProjectStatus_Access(null);
					if ($project_has_project_status->create($project_id,$project_all_status_array[0]) != true)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id, false);
						}
						throw new ProjectCreateStatusException("Could not create status");
					}
					
					if ($organisation_unit_id)
					{
						$base_folder_id = constant("PROJECT_FOLDER_ID");
					}
					else
					{
						$base_folder_id = ProjectFolder::get_folder_by_project_id($parent_project_id);
					}
					
					$base_folder = Folder::get_instance($base_folder_id);

					$path = new Path($base_folder->get_path());
					$path->add_element($project_id);
					
					$project_folder = new ProjectFolder(null);
					if (($folder_id = $project_folder->create($project_id, $base_folder_id)) == null)
					{
						$project_folder->delete(true, true);
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id, false);
						}
						throw new ProjectCreateFolderException("Could not create main folder");
					}
					
					// Create Supplementary Folder
					$supplementary_path = new Path($path->get_path_string());
					$supplementary_path->add_element("supplementary");

					$supplementary_folder = Folder::get_instance(null);
					if (($supplementary_folder->create("supplementary", $folder_id, $supplementary_path->get_path_string(), $owner_id, null)) == null)
					{
						$project_folder->delete();
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id, false);
						}
						throw new ProjectCreateSupplementaryFolderException("Could not create supplementary folder");
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
						$projet_status_folder = new ProjectStatusFolder(null);
						if (($status_folder_id = $projet_status_folder->create($project_id, $value)) == null)
						{
							$project_folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id, false);
							}
							throw new ProjectCreateStatusFolderException("Could not create status folder");
						}

						$project_status = new ProjectStatus($value);
						$project_status_requirements = $project_template->get_status_requirements($value);
						
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
	    										
								$folder_path = new Path($projet_status_folder->get_path());
								$folder_path->add_element($folder_name);
								
								$sub_folder = Folder::get_instance(null);
								if ($sub_folder->create($sub_value, $status_folder_id, $folder_path->get_path_string(), $user->get_user_id(), null) == null)
								{
									$project_folder->delete(true, true);
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id, false);
									}
									throw new ProjectCreateStatusSubFolderException("Could not create status sub folder");
								}
	    					}
	    				}	
					}			
					
					// Create Project Description
					$value = Value::get_instance(null);
					if ($value->create($folder_id, $owner_id, 2, $description) == null)
					{
						$project_folder->delete(true, true);
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id, false);
						}
						throw new ProjectCreateDescriptionException("Could not create description value");
					}
					
					$project_item = new ProjectItem($project_id);
					$project_item->set_gid(1);
					$project_item->set_status_id(1);
					$project_item->set_item_id($value->get_item_id());
					
					if ($project_item->link_item() == false)
					{
						$project_folder->delete(true, true);
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id, false);
						}
						throw new ProjectCreateDescriptionException("Could not create description item link");
					}
					
					if ($project_item->set_required(true) == false)
					{
						$project_folder->delete(true, true);
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id, false);
						}
						throw new ProjectCreateDescriptionException("Could not create description item role");
					}
					
					// Create Project Master Data
					if ($this->template_data_array and is_numeric($this->template_data_type_id))
					{
						$value = Value::get_instance(null);				
						
						if ($value->create($folder_id, $owner_id, $this->template_data_type_id, $this->template_data_array) == null)
						{
							$project_folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id, false);
							}
							throw new ProjectCreateMasterDataException("Could not create master-data value");
						}
						
						$project_item = new ProjectItem($project_id);
						$project_item->set_gid(1);
						$project_item->set_status_id(1);
						$project_item->set_item_id($value->get_item_id());
						
						if ($project_item->link_item() == false)
						{
							$project_folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id, false);
							}	
							throw new ProjectCreateMasterDataException("Could not create master-data item link");
						}
						
						if ($project_item->set_required(true) == false)
						{
							$project_folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id, false);
							}
							throw new ProjectCreateMasterDataException("Could not create master-data item role");
						}
					}

					// Permissions
					if ($organisation_unit_id)
					{
						$organisation_unit = new OrganisationUnit($organisation_unit_id);
	
						try
						{
							$project_permission = new ProjectPermissionUser(null);
							$project_permission->create($owner_id, $project_id, constant("PROJECT_USER_STD_PERMISSION"), null, 1);
						}
						catch (ProjectPermissionUserException $e)
						{
							$project_folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id, false);
							}
							throw new ProjectCreatePermissionUserException("Could not create user/owner permission");
						}
					
						$leader_array = $organisation_unit->list_leaders();
						
						if(is_array($leader_array) and count($leader_array) >= 1)
						{
							foreach($leader_array as $key => $value)
							{
								try
								{
									$project_permission = new ProjectPermissionUser(null);
									$project_permission->create($value, $project_id, constant("PROJECT_LEADER_STD_PERMISSION"), null, 2);
								}
								catch (ProjectPermissionUserException $e)
								{
									$project_folder->delete(true, true);
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id, false);
									}
									throw new ProjectCreatePermissionLeaderException("Could not create leader permission");
								}
							}
						}

						try
						{
							$project_permission = new ProjectPermissionOrganisationUnit(null);
							$project_permission->create($organisation_unit_id, $project_id, constant("PROJECT_OU_STD_PERMISSION"), null, 3);
						}
						catch (ProjectPermissionOrganisationUnitException $e)
						{
							$project_folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id, false);
							}
							throw new ProjectCreatePermissionOrganisationUnitException("Could not create Organisation Unit permission");
						}
					
						
						$quality_manager_array = $organisation_unit->list_quality_managers();
						
						if(is_array($quality_manager_array) and count($quality_manager_array) >= 1)
						{
							foreach($quality_manager_array as $key => $value)
							{
								try
								{
									$project_permission = new ProjectPermissionUser(null);
									$project_permission->create($value, $project_id, constant("PROJECT_QM_STD_PERMISSION"), null, 5);
								}
								catch (ProjectPermissionUserException $e)
								{
									$project_folder->delete(true, true);
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id, false);
									}
									throw new ProjectCreatePermissionQualityManagerException("Could not create quality-manager permission");
								}
							}
						}
						
						$group_array = $organisation_unit->list_groups();
						
						if(is_array($group_array) and count($group_array) >= 1)
						{
							foreach($group_array as $key => $value)
							{
								try
								{
									$project_permission = new ProjectPermissionGroup(null);
									$project_permission->create($value, $project_id, constant("PROJECT_GROUP_STD_PERMISSION"), null, 4);
								}
								catch (ProjectPermissionGroupException $e)
								{
									$project_folder->delete(true, true);
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id, false);
									}
									throw new ProjectCreatePermissionGroupException("Could not create group permissions");
								}
							}
						}
					}
							
					$this->__construct($project_id);
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return $project_id;
				}
			}
			else
			{
				throw new ProjectCreateException("Needed values are missing");
			}
		}
		else
		{
			throw new ProjectCreateException("Project target is ambiguous");
		}
    }

	/**
	 * @see ProjectInterface::delete()
	 * @return bool
	 * @throws ProjectDeleteException
	 * @throws ProjectDeleteContainsSubProjectsException
	 * @throws ProjectDeleteFolderException
	 * @throws ProjectDeleteItemException
	 * @throws ProjectDeleteLinkException
	 * @throws ProjectDeleteLogException
	 * @throws ProjectDeletePermissionException
	 * @throws ProjectDeleteStatusException
	 * @throws ProjectDeleteTaskException
	 */
    public function delete()
    {
    	global $transaction;
    	
    	if ($this->project_id)
    	{
    		$transaction_id = $transaction->begin();

    		array_push(self::$project_delete_array, $this->project_id);
    		
    		if ($this->exist_subproject() == true)
    		{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
					throw new ProjectDeleteContainsSubProjectsException();
				}
    		}
    		
    		$tmp_project_id = $this->project_id;

    		// Permissions
    		$project_permission_array = ProjectPermission::list_entries_by_project_id($tmp_project_id);
    		
    		if (is_array($project_permission_array) and count($project_permission_array) >= 1)
    		{
	    		foreach ($project_permission_array as $key => $value)
	    		{
	    			try
	    			{
	    				$project_permission = ProjectPermission::get_instance($value);
	    				$project_permission->delete();
	    			}
	    			catch (ProjectPermissionException $e)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectDeletePermissionException();
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
	    			
	    			try
	    			{
	    				$project_log->delete();
	    			}
	    			catch (ProjectLogDeleteException $e)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectDeleteLogException();
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
	    			
	    			try
	    			{
	    				$project_has_project_status->delete();
	    			}
	    			catch (ProjectStatusDeleteException $e)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectDeleteStatusException();
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
						throw new ProjectDeleteLinkException();
	    			}
	    		}
    		}
    		    	
    		// Methods and Remaining Item-Links (including Sample Links)
    		$project_item = new ProjectItem($tmp_project_id);
			$item_array = $project_item->get_project_items();
			if (is_array($item_array) and count($item_array) >= 1)
			{
				foreach($item_array as $item_key => $item_value)
				{
					$project_item = new ProjectItem($tmp_project_id);
					$project_item->set_item_id($item_value);
					if ($project_item->unlink_item() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectDeleteItemException();
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
						throw new ProjectDeleteTaskException();
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
				throw new ProjectDeleteException("Database delete failed");
    		}
    		else
    		{
    			$this->__destruct();
	    		$project_folder_id = ProjectFolder::get_folder_by_project_id($tmp_project_id);
	    		$project_folder = new ProjectFolder($project_folder_id);
	    		if ($project_folder->delete(true, true) == false)
	    		{
	    			if ($transaction_id != null)
	    			{
						$transaction->rollback($transaction_id);
					}
					throw new ProjectDeleteFolderException();
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
    		throw new ProjectDeleteException("Project ID was missing.");
    	}
    }
   
    /**
     * @see ProjectInterface::exist_subproject()
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
	 * @see ProjectInterface::is_next_status_available()
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
     * @see ProjectInterface::is_current_status_fulfilled()
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
     * @see ProjectInterface::get_all_status_array()
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
     * @see ProjectInterface::get_current_status_id()
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
     * @see ProjectInterface::get_current_status_name()
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
     * @see ProjectInterface::get_current_status_requirements()
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
				
				if (is_array($return_array) and count($return_array) >= 1)
				{
					foreach($return_array as $key => $value)
					{
						if (!$value[name] and $value[type])
						{
							$item_handling_class = Item::get_handling_class_by_type($value[type]);
							if ($item_handling_class)
							{
								$return_array[$key][name] = "Add ".$item_handling_class::get_generic_name($value[type], $value[type_id]);
							}
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
     * @see ProjectInterface::get_fulfilled_status_requirements()
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
				$item_type_array = Item::list_types();
				
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
						
						$project_item = new ProjectItem($this->project_id);
						$item_array = $project_item->get_project_status_items($this->get_current_status_id());
						if (is_array($item_array) and count($item_array) >= 1)
						{
							foreach($item_array as $item_key => $item_value)
							{
								$item_gid = ProjectItem::get_gid_by_item_id_and_project_id($item_value, $this->project_id,  $this->get_current_status_id());
								
								if (is_array($item_type_array) and count($item_type_array) >= 1)
								{
									foreach ($item_type_array as $item_type => $item_handling_class)
									{
										if (class_exists($item_handling_class))
										{
											if ($item_handling_class::is_kind_of($item_type, $item_value) == true  and $item_gid == $gid)
											{
												$fulfilled_array[$key] = true;
											}
										}
									}
								}
							}
						}
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
	 * @see ProjectInterface::get_fulfilled_status_datetimes()
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
     * @see ProjectInterface::get_next_status_id()
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
     * @see ProjectInterface::get_next_status_name()
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
     * @see ProjectInterface::set_next_status()
     * @param string $checksum
     * @param string $comment Optional Comment
     * @return bool
     * @throws ProjectSetNextStatusException
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
					throw new ProjectSetNextStatusException();
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
						throw new ProjectSetNextStatusException();
					}
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new ProjectSetNextStatusException();
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new ProjectSetNextStatusException();
			}
		}
		else
		{
			throw new ProjectSetNextStatusException();
		}
    }
    
    /**
     * @see ProjectInterface::get_status_requirements()
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
     * @see ProjectInterface::get_sub_folder()
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
	    		$folder_id = ProjectStatusFolder::get_folder_by_project_id_and_project_status_id($this->project_id, $status_id);
    			$folder = Folder::get_instance($folder_id);
    		
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
     * @see ProjectInterface::move_to_organisation_unit()
     * @param integer $organisation_unit_id
     * @return bool
     * @throws ProjectMoveException
     * @throws ProjectMoveProjectExistsException
     * @throws ProjectMovePermissionException
     * @throws ProjectMoveFolderException
     * @todo OrganisationUnitSecuriyException werfen
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
					
					$folder_id = ProjectFolder::get_folder_by_project_id($this->project_id);
					$folder = new Folder($folder_id);
					$destination_id = constant("PROJECT_FOLDER_ID");
					if ($folder->move_folder($destination_id, false) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMoveFolderException();
					}
					
					if ($this->project->set_toid_project(null) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMoveException();
					}
					
					if ($this->project->set_toid_organ_unit($organisation_unit_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMoveException();
					}			
					
					$project_security = new ProjectSecurity($this->project_id);
			
					if ($project_security->change_owner_permission($this->get_owner_id()) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMovePermissionException();
					}
					
					if ($project_security->change_ou_user_permission($organisation_unit_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMovePermissionException();
					}
					
					if ($project_security->change_organisation_unit_permission($organisation_unit_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMovePermissionException();
					}
					
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}
				else
				{
					throw new ProjectMoveProjectExistsException();
				}
			}
			else
			{
				// replace with OU exception
				throw new ProjectMoveException();
			}		
		}
		else
		{
			throw new ProjectMoveException();
		}
	}
	
	/**
     * @see ProjectInterface::move_to_project()
     * @param integer $organisation_unit_id
     * @return bool
     * @throws ProjectMoveException
     * @throws ProjectMoveProjectExistsException
     * @throws ProjectMovePermissionException
     * @throws ProjectMoveFolderException
     * @throws ProjectSecurityAccessDeniedException
     */   
	public function move_to_project($project_id)
	{
		global $user, $transaction;
	
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
							try
							{
								$project_permission = ProjectPermission::get_instance($value);
								$project_permission->delete();
							}
							catch (ProjectPermissionException $e)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ProjectMovePermissionException();
							}
						}
					}
					
					if ($this->project->set_toid_organ_unit(null) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMoveException();
					}
					
					if ($this->project->set_toid_project($project_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMoveException();
					}
					
					$folder_id = ProjectFolder::get_folder_by_project_id($this->project_id);
					$folder = new Folder($folder_id);
					$destination_id = ProjectFolder::get_folder_by_project_id($project_id);
					if ($folder->move_folder($destination_id, false) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ProjectMoveFolderException();
					}
					
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}
				else
				{
					throw new ProjectMoveProjectExistsException();
				}
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectMoveException();
		}		
	}
    
    /**
     * @see ProjectInterface::mark_as_deleted()
     * @return bool
     */   
    public function mark_as_deleted()
    {
    	if ($this->project_id and $this->project)
    	{			
			$project_log = new ProjectLog(null);
			$project_log->create($this->project_id, "Project Deleted", false, true, md5(rand(0,32768)));
			
			$folder_id = ProjectFolder::get_folder_by_project_id($this->project_id);
			$folder = Folder::get_instance($folder_id);
			$folder->mark_as_deleted();
			
			return $this->project->set_deleted(true);
		}
		else
		{
			return false;
		}
    }
    
    /**
     * @see ProjectInterface::mark_as_undeleted()
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
     * @see ProjectInterface::mark_as_canceled()
     * @param string $comment
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
     * @see ProjectInterface::mark_as_reactivated()
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
     * @see ProjectInterface::list_project_related_projects()
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
     * @see ProjectInterface::get_project_tree()
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
     * @see ProjectInterface::get_master_project_id()
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
     * @see ProjectInterface::get_name()
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
     * @see ProjectInterface::get_datetime()
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
     * @see ProjectInterface::get_owner_id()
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
     * @see ProjectInterface::get_organisation_unit_id()
     * @return integer
     * @todo recursive?
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
     * @see ProjectInterface::get_project_toid()
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
	 * @see ProjectInterface::get_description()
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
				foreach($item_array as $item_key => $item_value)
				{
					if (DataEntity::is_kind_of("value", $item_value) == true)
					{
						$data_entity_id = DataEntity::get_entry_by_item_id($item_value);
						$value_id = Value::get_value_id_by_data_entity_id($data_entity_id);
						if (Value::is_entry_type_of($value_id, 2) == true)
						{
							$description_id = $value_id;
						}
					}
				}
			}
			
			$value = Value::get_instance($description_id);
			if ($value->get_type_id() == 2)
			{
				return unserialize($value->get_value());
			}
		}
		else
		{
			return null;
		}		
	}

	/**
	 * @see ProjectInterface::get_template_id()
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
	 * @see ProjectInterface::get_template_name()
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
     * @see ProjectInterface::get_quota()
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
     * @see ProjectInterface::get_filesize()
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
     * @see ProjectInterface::get_deleted()
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
	 * @see ProjectInterface::set_quota()
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
	 * @see ProjectInterface::set_filesize()
	 * @param integer $filesize
	 * @return bool
	 */
    public function set_filesize($filesize)
    {
    	if ($this->project_id and $this->project)
    	{
    		if ($filesize == null or !is_numeric($filesize))
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
	 * @see ProjectInterface::set_name()
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
    		
    		$folder_id = ProjectFolder::get_folder_by_project_id($this->project_id);
    		$folder = Folder::get_instance($folder_id);
    		
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
	 * @see ProjectInterface::set_owner_id()
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
     * @see ProjectInterface::list_user_related_projects()
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
	 * @see ProjectInterface::list_organisation_unit_related_projects()
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
     * @see ProjectInterface::list_entries_by_template_id()
     * @param integer $template_id
     * @return array
     */  
    public static function list_entries_by_template_id($template_id)
    {
    	return Project_Access::list_entries_by_template_id($template_id);
    }
    
    /**
     * @see ProjectInterface::exist_project_name()
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
     * @see ProjectInterface::exist_project()
     * @param integer $project_id
     * @return bool
     */
   	public static function exist_project($project_id)
   	{
   		return Project_Access::exist_project_by_project_id($project_id);
   	}
         
    /**
     * @see ProjectInterface::get_used_project_space()
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
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeletePrecheckEvent)
    	{
    		$project_array = self::list_user_related_projects($event_object->get_user_id(), false);
    		
    		if (is_array($project_array))
			{
				if (count($project_array) >= 1)
				{
					return false;
				}
			}
    	}
    	
    	if ($event_object instanceof OrganisationUnitDeletePrecheckEvent)
    	{
    		$project_array = self::list_organisation_unit_related_projects($event_object->get_organisation_unit_id(), true);
    		
    		if (is_array($project_array))
			{
				if (count($project_array) >= 1)
				{
					return false;
				}
			}
    	}
    	
    	if ($event_object instanceof FileUploadPrecheckEvent)
    	{
    		$folder_id = $event_object->get_folder_id();
    		if (($project_id = ProjectFolder::get_project_id_by_folder_id($folder_id)) != null)
    		{
				$project = new Project($project_id);
				$project_quota = $project->get_quota();
				$project_filesize = $project->get_filesize();
												
				$new_project_filesize = $project_filesize + $event_object->get_filesize();
				
				if (($project_quota <= $new_project_filesize and $project_quota != 0))
				{
					return false;
				}	
    		}
    	}
    	
   		if ($event_object instanceof FileUploadEvent)
    	{
    		$folder_id = $event_object->get_folder_id();
    		if (($project_id = ProjectFolder::get_project_id_by_folder_id($folder_id)) != null)
    		{
    			$project = new Project($project_id);							
				$new_project_filesize = $project->get_filesize() + $event_object->get_filesize();
				
				if ($project->set_filesize($new_project_filesize) == false)
				{
					return false;
				}
    		}
    	}
    	
    	if ($event_object instanceof FileDeleteEvent)
    	{
    		$folder_id = $event_object->get_folder_id();
    		if (($project_id = ProjectFolder::get_project_id_by_folder_id($folder_id)) != null)
    		{
    			$project = new Project($project_id);							
				$new_project_filesize = $project->get_filesize() - $event_object->get_filesize();

				if ($project->set_filesize($new_project_filesize) == false)
				{
					return false;
				}
    		}
    	}
    	
    	return true;
    }

    /**
     * @see ItemHolderInterface::get_item_list_sql()
	 * @param integer $holder_id
	 * @return string
	 */
	public static function get_item_list_sql($holder_id)
	{
		return " SELECT item_id FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE project_id = ".$holder_id."";
	}
}

?>