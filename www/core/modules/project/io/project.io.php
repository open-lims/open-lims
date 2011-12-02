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
 * Project IO Class
 * @package project
 */
class ProjectIO
{
	public static function list_user_related_projects($user_id)
	{
		global $user;

		if (!is_numeric($user_id))
		{
			$user_id = $user->get_user_id();
		}

		$argument_array = array();
		$argument_array[0][0] = "user_id";
		$argument_array[0][1] = $user_id;
		
		$list = new List_IO("ProjectUserRelated", "ajax.php?nav=project", "list_user_related_projects", "count_user_related_projects", $argument_array, "ProjectAjaxMyProjects");
		
		$list->add_row("", "symbol", false, "16px");
		$list->add_row("Name", "name", true, null);
		$list->add_row("Organisation Unit", "organisation_unit", true, null);
		$list->add_row("Date/Time", "datetime", true, null);
		$list->add_row("Template", "template", true, null);
		$list->add_row("Status", "status", true, null);

		$template = new Template("template/projects/list_user.html");
	
		if ($user_id == $user->get_user_id())
		{
			$template->set_var("title","My Projects");
		}
		else
		{
			$template->set_var("title","Projects of ".$user->get_username());
		}
	
		$template->set_var("list", $list->get_list());
	
		$template->output();
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 * @throws OrganisationUnitNotFoundException
	 */
	public static function list_organisation_unit_related_projects()
	{
		if ($_GET['ou_id'])
		{
			try
			{
				$organisation_unit_id = $_GET['ou_id'];
				
				$argument_array = array();
				$argument_array[0][0] = "organisation_unit_id";
				$argument_array[0][1] = $organisation_unit_id;
				
				$list = new List_IO("ProjectOrganisationUnitRelated", "ajax.php?nav=project", "list_organisation_unit_related_projects", "count_organisation_unit_related_projects", $argument_array, "ProjectAjaxOrganisationUnit", 12);
			
				$list->add_row("","symbol",false,"16px");
				$list->add_row("Name","name",true,null);
				$list->add_row("Owner","owner",true,null);
				$list->add_row("Date/Time","datetime",true,null);
				$list->add_row("Template","template",true,null);
				$list->add_row("Status","status",true,null);
			
				require_once("core/modules/organisation_unit/organisation_unit.io.php");
				$organisation_unit_io = new OrganisationUnitIO;
				$organisation_unit_io->detail();
				
				$template = new Template("template/projects/list_organisation_unit.html");	
	
				$template->set_var("list", $list->get_list());
		
				$template->output();
			}
			catch (OrganisationUnitNotFoundException $e)
			{
				throw $e;
			}
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	public static function create()
	{
		$template = new Template("template/projects/create_project.html");	
		
		require_once("core/modules/base/common/io/assistant.io.php");
		
		$assistant_io = new AssistantIO("core/modules/project/ajax/project_create.ajax.php", "ProjectCreateAssistantField", true);
		
		$assistant_io->add_screen("Organisation Unit");
		$assistant_io->add_screen("Project Information");
		$assistant_io->add_screen("Template");
		$assistant_io->add_screen("Template Specific Information");
		$assistant_io->add_screen("Summary");

		$template->set_var("content", $assistant_io->get_content());
		
		$template->output();
	}

	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function detail()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project = new Project($_GET[project_id]);
				$project_owner = new User($project->get_owner_id());
			
				$template = new Template("template/projects/project_detail.html");
				
				$template->set_var("title", $project->get_name());
				$template->set_var("owner",$project_owner->get_full_name(false));
				$template->set_var("created_at",$project->get_datetime());
				$template->set_var("template",$project->get_template_name());
				$template->set_var("permissions","");
				$template->set_var("size",Convert::convert_byte_1024($project->get_filesize()));
				$template->set_var("quota",Convert::convert_byte_1024($project->get_quota()));
				
				$owner_paramquery = array();
				$owner_paramquery[username] = $_GET[username];
				$owner_paramquery[session_id] = $_GET[session_id];
				$owner_paramquery[nav] = "project";
				$owner_paramquery[run] = "common_dialog";
				$owner_paramquery[dialog] = "user_detail";
				$owner_paramquery[id] = $project->get_owner_id();
				$owner_params = http_build_query($owner_paramquery,'','&#38;');
				
				$template->set_var("owner_params", $owner_params);	
				
				// Status Bar
				$all_status_array = $project->get_all_status_array();				
				$result = array();
				$counter = 0;
				
				if (is_array($all_status_array) and count($all_status_array) >= 1)
				{
					foreach($all_status_array as $key => $value)
					{						
						$project_status = new ProjectStatus($value[id]);
						
						if ($value[optional] == true)
						{
							$result[$counter][name] = $project_status->get_name()." (optional)";
						}
						else
						{
							$result[$counter][name] = $project_status->get_name();	
						}
						
						if ($value[status] == 3)
						{
							$result[$counter][icon] = "<img src='images/icons/status_cancel.png' alt='R' />";
						}
						elseif($value[status] == 2)
						{
							$result[$counter][icon] = "<img src='images/icons/status_ok.png' alt='R' />";
						}elseif($value[status] == 1)
						{
							$result[$counter][icon]	= "<img src='images/icons/status_run.png' alt='R' />";
						}
						else
						{
							$result[$counter][icon]	= "";
						}
						
						if (!($counter % 2))
						{
							$result[$counter][tr_class] = " class='trLightGrey'";
						}
						else
						{
							$result[$counter][tr_class] = "";
						}
						
						$counter++;
					}
					
					$project_status = new ProjectStatus(2);
					$result[$counter][name] = $project_status->get_name();
					
					if ($project->get_current_status_id() == 2)
					{
						$result[$counter][icon] = "<img src='images/icons/status_ok.png' alt='R' />";
					}
					else
					{
						$result[$counter][icon]	= "";
					}
					
					if (!($counter % 2))
					{
						$result[$counter][tr_class] = " class='trLightGrey'";
					}
					else
					{
						$result[$counter][tr_class] = "";
					}
					
					$counter++;
				}
				
				$template->set_var("status",$result);
					
				// Proceed Button
				
				switch ($project->is_next_status_available()):
					case(0):
						if ($project->get_current_status_id() == 0)
						{
							$template->set_var("proceed",3);
						}
						else
						{
							$template->set_var("proceed",4);
						}
					break;
					
					case(1):
						if ($project_security->is_access(2, false) == true)
						{
							if ($project->is_current_status_fulfilled())
							{
								$template->set_var("proceed",1);
							}
							else
							{
								$template->set_var("proceed",2);
							}
						}
					break;
					
					case(2):
						if ($project_security->is_access(2, false) == true)
						{
							if ($project->is_current_status_fulfilled())
							{
								$template->set_var("proceed",5);
							}
							else
							{
								$template->set_var("proceed",6);
							}
						}
					break;
							
					default:
						$template->set_var("proceed",7);
					break;
				endswitch;		
				
				$paramquery = $_GET;
				$paramquery[run] = "proceed";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("proceed_params",$params);
				$template->set_var("next_status_name",$project->get_next_status_name());
				
				
				if ($project_security->is_access(2, false) == true)
				{
					// Status Buttons
					
					$project_template = new ProjectTemplate($project->get_template_id());
					$current_status_requirements 	= $project->get_current_status_requirements($project->get_current_status_id());
					$current_fulfilled_requirements = $project->get_fulfilled_status_requirements();
								
					$result = array();
					$counter = 0;
					
					if (is_array($current_status_requirements) and count($current_status_requirements) >= 1)
					{
						foreach($current_status_requirements as $key => $value)
						{
							$paramquery = array();
							$paramquery[username] = $_GET[username];
							$paramquery[session_id] = $_GET[session_id];
							$paramquery[nav] = "project";
							$paramquery[run] = "item_add";
							$paramquery[project_id] = $_GET[project_id];
							$paramquery[dialog] = $value[type];
							$paramquery[key] = $key;
							$paramquery[retrace] = Retrace::create_retrace_string();
							unset($paramquery[nextpage]);
							$params = http_build_query($paramquery,'','&#38;');
	
							$result[$counter][name] = $value[name];
	
							if ($current_fulfilled_requirements[$key] == true)
							{
								if ($value[occurrence] == "multiple")
								{
									$result[$counter][status] = 2;
								}
								else
								{
									$result[$counter][status] = 0;
								}
							}
							else
							{
								$result[$counter][status] = 1;
							}
	
							if ($value[requirement] == "optional")
							{
								$result[$counter][name] = $result[$counter][name]." (optional)";
							}
							
							$result[$counter][params] = $params;					
							
							$counter++;
						}		
					}
					
					$template->set_var("status_action",$result);
					
					$template->set_var("write",true);
				}
				else
				{
					$template->set_var("write",false);
				}
				
				$paramquery = array();
				$paramquery[username] = $_GET[username];
				$paramquery[session_id] = $_GET[session_id];
				$paramquery[nav] = "project";
				$paramquery[run] = "common_dialog";
				$paramquery[folder_id] = ProjectFolder::get_supplementary_folder($_GET[project_id]);
				$paramquery[dialog] = "file_add";
				$paramquery[retrace] = Retrace::create_retrace_string();
				unset($paramquery[nextpage]);
				$supplementary_params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("supplementary_params",$supplementary_params);
				
				
				$log_paramquery = $_GET;
				$log_paramquery[run] = "log_add";
				unset($log_paramquery[nextpage]);
				$log_params = http_build_query($log_paramquery,'','&#38;');
				
				$template->set_var("log_params",$log_params);
				
				
				$add_task_paramquery = $_GET;
				$add_task_paramquery[run] = "add_task";
				unset($add_task_paramquery[nextpage]);
				$add_task_params = http_build_query($add_task_paramquery,'','&#38;');
				
				$template->set_var("add_task_params",$add_task_params);
				
				
				$show_tasks_paramquery = $_GET;
				$show_tasks_paramquery[run] = "show_tasks";
				unset($show_tasks_paramquery[nextpage]);
				$show_tasks_params = http_build_query($show_tasks_paramquery,'','&#38;');
				
				$template->set_var("show_tasks_params",$show_tasks_params);
				
				
				$subproject_paramquery = $_GET;
				$subproject_paramquery[run] = "new_subproject";
				unset($subproject_paramquery[nextpage]);
				$subproject_params = http_build_query($subproject_paramquery,'','&#38;');
				
				$template->set_var("add_subproject_params",$subproject_params);
				
				$template->output();
			}
			else
			{
				throw new ProjectSecurityAccessDeniedxception();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function proceed()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(3, false) == true)
			{
				$project = new Project($_GET[project_id]);
				
				if (!$_GET[nextpage])
				{
					$template = new Template("template/projects/project_proceed.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 1;
					$params = http_build_query($paramquery);
					
					$template->set_var("params", $params);
		
					$project_template = new ProjectTemplate($project->get_template_id());
					$current_status_requirements 	= $project->get_current_status_requirements();
					$current_fulfilled_requirements = $project->get_fulfilled_status_requirements();
					
					$result = array();
					$counter = 0;
					
					if (is_array($current_status_requirements) and count($current_status_requirements) >= 1)
					{
						foreach($current_status_requirements as $key => $value)
						{
							$result[$counter][name] = $value[name];
							if ($current_fulfilled_requirements[$key] == true)
							{
								$result[$counter][status] = 0;
							}
							else
							{
								if ($value[requirement] != "optional")
								{
									$result[$counter][status] = 1;
								}
								else
								{
									$result[$counter][status] = 2;
								}
							}
							$counter++;
						}			
					}
					else
					{
						$result[$counter][icon] = "";
						$result[$counter][name] = "No Requirements";
					}
		
					$template->set_var("status_action",$result);
					
					if ($project->get_current_status_id() == 1)
					{
						$template->set_var("enable_add_comment",false);
					}
					else
					{
						$template->set_var("enable_add_comment",true);
					}
					
					if ($project->is_current_status_fulfilled())
					{
						$template->set_var("enable_button",true);
					}
					else
					{
						$template->set_var("enable_button",false);
					}
		
					$template->set_var("button_comment","");
		
					$template->output();
				}
				else
				{
					if ($_POST[add_comment] == "1")
					{
						if (!$_POST[comment])
						{
							$comment_page_passed = false;
							if ($_GET[nextpage] == "2")
							{
								$error = "You must enter a comment.";
							}
							else
							{
								$error = "";
							}
						}
						else
						{
							$comment_page_passed = true;
						}
					}
					else
					{
						$comment_page_passed = true;
					}
					
					if ($comment_page_passed == false)
					{
						$template = new Template("template/projects/project_proceed_comment.html");
					
						$paramquery = $_GET;
						$paramquery[nextpage] = 2;
						$params = http_build_query($paramquery);
						
						$template->set_var("params", $params);
					
						$template->set_var("error",$error);
					
						if ($_POST[comment])
						{
							$template->set_var("textarea_content",$_POST[comment]);
						}
						else
						{
							$template->set_var("textarea_content","");	
						}
					
						$template->output();
					}
					else
					{
						$paramquery = $_GET;
						$paramquery[run] = "detail";
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery);
						
						if ($_POST[comment])
						{
							$project_log = new ProjectLog(null);
							$project_log->create($_GET[project_id], $_POST[comment], false, false, md5(rand(0,50000)));
						}
						
						try
						{
							$project->set_next_status(null,null);
							Common_IO::step_proceed($params, "Proceed to next status", "Operation Successful" ,null);
						}
						catch (ProjectSetNextStatusException $e)
						{
							Common_IO::step_proceed($params, "Proceed to next status", "Operation Failed" ,null);	
							$project_log->delete();
						}
					}
				}
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function structure()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project = new Project($_GET[project_id]);
				$project_structure_array = $project->get_project_tree();
				
				$template = new Template("template/projects/project_structure.html");
				
				if (is_array($project_structure_array) and count($project_structure_array) >= 1)
				{
					$result = array();
					$counter = 0;
				
					foreach($project_structure_array as $key => $value)
					{
						$project = new Project($value[id]);
						$project_security = new ProjectSecurity($value[id]);
						$project_owner = new User($project->get_owner_id());
						
						$paramquery[username] = $_GET[username];
						$paramquery[session_id] = $_GET[session_id];
						$paramquery[nav] = "project";
						$paramquery[run] = "detail";
						$paramquery[project_id] = $value[id];
						$params = http_build_query($paramquery, '', '&#38;');
						
						$result[$counter][link] = $params;
						
						$result[$counter][name] 	= $project->get_name();
						$result[$counter][status] 	= $project->get_current_status_name();
						$result[$counter][template] = $project->get_template_name();
						$result[$counter][owner] 	= $project_owner->get_full_name(false);
						
						$involved_array = $project_security->list_involved_users();
						
						if (is_array($involved_array) and count($involved_array) >= 1)
						{
							foreach($involved_array as $involved_key => $involved_value)
							{
								$involved_user = new User($involved_value);
								
								if ($result[$counter][involved] == "")
								{
									$result[$counter][involved] = $involved_user->get_full_name(false);
								}
								else
								{
									$result[$counter][involved] .= ", ".$involved_user->get_full_name(false);
								}
							}
						}
						else
						{
							$result[$counter][involved] 	= "";
						}
		
						$subproject_paramquery = $_GET;
						$subproject_paramquery[run] = "new_subproject";
						$subproject_paramquery[id] = $value[id];
						unset($subproject_paramquery[nextpage]);
						$subproject_params = http_build_query($subproject_paramquery,'','&#38;');
		
						$result[$counter][add_subproject]	= $subproject_params;
						
						$result[$counter][padding]			= $value[layer];
						
						$counter++;
					}
					$template->set_var("structure",$result);
				}
				$template->output();
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @todo specific exception
	 * @param integer $item_id
	 */
	public static function list_projects_by_item_id($item_id, $in_assistant = false, $form_field_name = null)
	{
		if ($GLOBALS['autoload_prefix'])
		{
			$path_prefix = $GLOBALS['autoload_prefix'];
		}
		else
		{
			$path_prefix = "";
		}
		
		if (is_numeric($item_id))
		{
			$argument_array = array();
			$argument_array[0][0] = "item_id";
			$argument_array[0][1] = $item_id;
			$argument_array[1][0] = "in_assistant";
			$argument_array[1][1] = $in_assistant;
			
			if ($in_assistant == false)
			{
				$list = new List_IO("ProjectByItem", "ajax.php?nav=project", "list_projects_by_item_id", "count_projects_by_item_id", $argument_array, "ProjectParentAjax", 20, true, true);
				
				$template = new Template($path_prefix."template/projects/list_projects_by_item.html");
				
				$list->add_row("","symbol",false,16);
				$list->add_row("Name","name",true,null);
				$list->add_row("Date/Time","datetime",true,null);
				$list->add_row("Template","template",true,null);
				$list->add_row("Owner","owner",true,null);
				$list->add_row("Status","status",true,null);
			}
			else
			{
				$list = new List_IO("ProjectByItem", "/core/modules/project/ajax/project.ajax.php", "list_projects_by_item_id", "count_projects_by_item_id", $argument_array, "ProjectParentAjax", 20, false, false);
				
				$template = new Template($path_prefix."template/projects/list_projects_by_item_without_border.html");
				
				$list->add_row("","checkbox",false,"16px", $form_field_name);
				$list->add_row("","symbol",false,16);
				$list->add_row("Name","name",false,null);
				$list->add_row("Date/Time","datetime",false,null);
				$list->add_row("Template","template",false,null);
				$list->add_row("Owner","owner",false,null);
				$list->add_row("Status","status",false,null);
			}
		
			$template->set_var("list", $list->get_list());
			
			$template->output();
		}
		else
		{
			throw new ProjectException();
		}
	}
		
}
?>
