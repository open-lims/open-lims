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
	private static function list_user_related_projects($user_id)
	{
		global $user;

		if (!is_numeric($user_id))
		{
			$user_id = $user->get_user_id();
		}

		$argument_array = array();
		$argument_array[0][0] = "user_id";
		$argument_array[0][1] = $user_id;
		
		$list = new List_IO("ProjectUserRelated", "/core/modules/project/project.ajax.php", "list_user_related_projects", "count_user_related_projects", $argument_array, "ProjectAjaxMyProjects");
		
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
	 * @todo Error - No $_GET[ou_id]
	 */
	private static function list_organisation_unit_related_projects()
	{
		if ($_GET['ou_id'])
		{
			$organisation_unit_id = $_GET['ou_id'];
			
			$argument_array = array();
			$argument_array[0][0] = "organisation_unit_id";
			$argument_array[0][1] = $organisation_unit_id;
			
			$list = new List_IO("ProjectOrganisationUnitRelated", "/core/modules/project/project.ajax.php", "list_organisation_unit_related_projects", "count_organisation_unit_related_projects", $argument_array, "ProjectAjaxOrganisationUnit", 12);
		
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
		else
		{
			// ! ERROR !
		}
	}
	
	private static function create()
	{
		$template = new Template("template/projects/create_project.html");	
		
		require_once("core/modules/base/assistant.io.php");
		
		$assistant_io = new AssistantIO("core/modules/project/project_create.ajax.php", "ProjectCreateAssistantField", true);
		
		$assistant_io->add_screen("Organisation Unit");
		$assistant_io->add_screen("Project Information");
		$assistant_io->add_screen("Template");
		$assistant_io->add_screen("Template Specific Information");
		$assistant_io->add_screen("Summary");

		$template->set_var("content", $assistant_io->get_content());
		
		$template->output();
	}
		
	private static function detail()
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
				$template->set_var("size",Misc::calc_size($project->get_filesize()));
				$template->set_var("quota",Misc::calc_size($project->get_quota()));
				
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
							$paramquery[retrace] = Misc::create_retrace_string();
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
				$paramquery[retrace] = Misc::create_retrace_string();
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
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 200, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 200, 40, 3);
			$error_io->display_error();
		}
	}
	
	private static function proceed()
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
						
						if ($project->set_next_status(null,null))
						{
							Common_IO::step_proceed($params, "Proceed to next status", "Operation Successful" ,null);
						}
						else
						{
							Common_IO::step_proceed($params, "Proceed to next status", "Operation Failed" ,null);	
							$project_log->delete();
						}
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 200, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 200, 40, 3);
			$error_io->display_error();
		}
	}
	
	private static function structure()
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
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 200, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 200, 40, 3);
			$error_io->display_error();
		}
	}
	
	/**
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
				$list = new List_IO("ProjectByItem", "/core/modules/project/project.ajax.php", "list_projects_by_item_id", "count_projects_by_item_id", $argument_array, "ProjectParentAjax", 20, true, true);
				
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
				$list = new List_IO("ProjectByItem", "/core/modules/project/project.ajax.php", "list_projects_by_item_id", "count_projects_by_item_id", $argument_array, "ProjectParentAjax", 20, false, false);
				
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
			// Error
		}
			
		if (false == true)
		{
			$list = new ListStat_IO(Project_Wrapper::count_projects_by_item_id($item_id), 20);

			$list->add_row("","symbol",false,16);
			$list->add_row("Name","name",true,null);
			$list->add_row("Date/Time","datetime",true,null);
			$list->add_row("Template","template",true,null);
			$list->add_row("Owner","owner",true,null);
			$list->add_row("Status","status",true,null);

			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Project_Wrapper::list_projects_by_item_id($item_id, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = Project_Wrapper::list_projects_by_item_id($item_id, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Project_Wrapper::list_projects_by_item_id($item_id, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = Project_Wrapper::list_projects_by_item_id($item_id, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				$today_begin = new DatetimeHandler(date("Y-m-d")." 00:00:00");
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");
				
					if ($result_array[$key][owner])
					{
						$user = new User($result_array[$key][owner]);
					}
					else
					{
						$user = new User(1);
					}
					
					$result_array[$key][owner] = $user->get_full_name(true);
					
					if (strlen($result_array[$key][template]) > 25)
					{
						$result_array[$key][template] = substr($result_array[$key][template],0,25)."...";
					}
					else
					{
						$result_array[$key][template] = $result_array[$key][template];
					}
					
					$project_id = $result_array[$key][id];
					$project_security = new ProjectSecurity($sample_id);
					
					if ($project_security->is_access(1, false))
					{
						$paramquery = array();
						$paramquery[username] = $_GET[username];
						$paramquery[session_id] = $_GET[session_id];
						$paramquery[nav] = "project";
						$paramquery[run] = "detail";
						$paramquery[project_id] = $project_id;
						$params = http_build_query($paramquery,'','&#38;');
						
						$result_array[$key][symbol][link]		= $params;
						$result_array[$key][symbol][content] 	= "<img src='images/icons/project.png' alt='' style='border:0;' />";
					
						$project_name = $result_array[$key][name];
						unset($result_array[$key][name]);
						$result_array[$key][name][link] 		= $params;
						$result_array[$key][name][content]		= $project_name;
					}
					else
					{
						$result_array[$key][symbol]	= "<img src='core/images/denied_overlay.php?image=images/icons/project.png' alt='N' border='0' />";
					}
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			$template = new Template("template/projects/list_projects_by_item.html");

			$template->set_var("table", $list->get_list($result_array, $_GET[page]));
			
			$template->output();
		}
		else
		{
			// Error
		}
	}
	
	public static function method_handler()
	{
		global $project_security, $session, $transaction;
		
		try
		{
			if ($_GET[project_id])
			{
				if (Project::exist_project($_GET[project_id]) == false)
				{
					throw new ProjectSecurityException("",1);
				}
				else
				{
					$project_security = new ProjectSecurity($_GET[project_id]);
					
					if ($_GET[run] != "new_subproject")
					{
	 					require_once("project_common.io.php");
	 					ProjectCommon_IO::tab_header();
					}
				}
			}
			else
			{
				$project_security = new ProjectSecurity(null);
			}
			
			switch($_GET[run]):
			
				case ("new"):
					self::create();
				break;
				
				case ("myprojects"):
				case ("workon"):
				case ("accessdata"):
				case ("analyse"):
					self::list_user_related_projects(null);
				break;
				
				case("userprojects"):
					self::list_user_related_projects($_GET[id]);
				break;		
	
				case("organ_unit"):
					self::list_organisation_unit_related_projects();
				break;
	
				case ("new_subproject"):
					self::create();
				break;
				
				case ("detail"):
					self::detail();
				break;
		
				case("proceed"):
					self::proceed();
				break;
	
				case("structure"):
					self::structure();
				break;
				
				// Project Log
				
				case("log"):
					require_once("project_log.io.php");
					ProjectLogIO::list_project_related_logs();
				break;
				
				case("log_detail"):
					require_once("project_log.io.php");
					ProjectLogIO::detail();
				break;
				
				case("log_add"):
					require_once("project_log.io.php");
					ProjectLogIO::add_comment();
				break;
				
				// Tasks and Schedule
				
				case ("add_task"):
					require_once("project_task.io.php");
					ProjectTaskIO::add();
				break;
				
				case ("schedule"):
				case ("show_tasks"):
					require_once("project_task.io.php");
					ProjectTaskIO::show();
				break;
				
				case ("task_detail"):
					require_once("project_task.io.php");
					ProjectTaskIO::detail();
				break;
				
				case ("task_delete"):
					require_once("project_task.io.php");
					ProjectTaskIO::delete();
				break;
				
				case ("task_edit_start"):
					require_once("project_task.io.php");
					ProjectTaskIO::edit_start();
				break;
				
				case ("task_edit_end"):
					require_once("project_task.io.php");
					ProjectTaskIO::edit_end();
				break;
				
							
				// Administration
				
				case("admin"):
					require_once("project_admin.io.php");
					ProjectAdminIO::menu();
				break;
				
				case("admin_delete"):
					require_once("project_admin.io.php");
					ProjectAdminIO::delete();
				break;
				
				case("admin_full_delete"):
					require_once("project_admin.io.php");
					ProjectAdminIO::full_delete();
				break;
				
				case("admin_restore"):
					require_once("project_admin.io.php");
					ProjectAdminIO::restore();
				break;
				
				case("admin_cancel"):
					require_once("project_admin.io.php");
					ProjectAdminIO::cancel();
				break;
				
				case("admin_reactivate"):
					require_once("project_admin.io.php");
					ProjectAdminIO::reactivate();
				break;
				
				case("admin_rename"):
					require_once("project_admin.io.php");
					ProjectAdminIO::rename();
				break;
				
				case("admin_chown"):
					require_once("project_admin.io.php");
					ProjectAdminIO::chown();
				break;
				
				case("admin_move"):
					require_once("project_admin.io.php");
					ProjectAdminIO::move();
				break;
				
				case("admin_quota"):
					require_once("project_admin.io.php");
					ProjectAdminIO::quota();
				break;
				
				// Administration - Permission
				
				case("admin_permission"):
					require_once("project_admin.io.php");
					$project_admin_io = new ProjectAdminIO();
					ProjectAdminIO::permission();
				break;
				
				case("admin_permission_add_user"):
					require_once("project_admin.io.php");
					ProjectAdminIO::permission_add_user();
				break;
				
				case("admin_permission_add_group"):
					require_once("project_admin.io.php");
					ProjectAdminIO::permission_add_group();
				break;
				
				case("admin_permission_add_ou"):
					require_once("project_admin.io.php");
					ProjectAdminIO::permission_add_organisation_unit();
				break;
				
				case("admin_permission_edit"):
					require_once("project_admin.io.php");
					ProjectAdminIO::permission_edit();
				break;
				
				case("admin_permission_delete"):
					require_once("project_admin.io.php");
					ProjectAdminIO::permission_delete();
				break;
								
				// Item Lister
				/**
				 * @todo errors, exceptions
				 */
				case("item_list"):
					if ($project_security->is_access(1, false) == true)
					{
						if ($_GET[dialog])
						{
							if ($_GET[dialog] == "data")
							{
								$path_stack_array = array();
								
								$folder_id = ProjectFolder::get_folder_by_project_id($_GET[project_id]);
						    	$folder = Folder::get_instance($folder_id);
						    	$init_array = $folder->get_object_id_path();
						    	
						    	foreach($init_array as $key => $value)
						    	{
						    		$temp_array = array();
						    		$temp_array[virtual] = false;
						    		$temp_array[id] = $value;
						    		array_unshift($path_stack_array, $temp_array);
						    	}
								
								$session->write_value("stack_array", $path_stack_array, true);
							}
							
							$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_list", $_GET[dialog]);
							
							if (file_exists($module_dialog[class_path]))
							{
								require_once($module_dialog[class_path]);
								
								if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
								{
									$module_dialog['class']::$module_dialog[method]("project", $_GET[project_id], true);
								}
								else
								{
									// Error
								}
							}
							else
							{
								// Error
							}
						}
						else
						{
							// error
						}
					}
					else
					{
						$exception = new Exception("", 1);
						$error_io = new Error_IO($exception, 200, 40, 2);
						$error_io->display_error();
					}
				break;
				
				// Item Add
				case("item_add"):
					if ($project_security->is_access(3, false) == true)
					{
						if ($_GET[dialog])
						{
							$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_add", $_GET[dialog]);
	
							if (is_array($module_dialog) and $module_dialog[class_path])
							{
								if (file_exists($module_dialog[class_path]))
								{
									require_once($module_dialog[class_path]);
									
									if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
									{
										$project = new Project($_GET[project_id]);
										$project_item = new ProjectItem($_GET[project_id]);
										$project_item->set_status_id($project->get_current_status_id());
										$project_item->set_gid($_GET[key]);
										
										$description_required = $project_item->is_description_required();
										$keywords_required = $project_item->is_keywords_required();
										
										if (($description_required and !$_POST[description] and !$_GET[idk_unique_id]) or ($keywords_required and !$_POST[keywords] and !$_GET[idk_unique_id]))
										{
											require_once("core/modules/item/item.io.php");
											ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
										}
										else
										{
											$transaction_id = $transaction->begin();
											
											$current_status_requirements = $project->get_current_status_requirements($project->get_current_status_id());
																						
											$folder_id = ProjectStatusFolder::get_folder_by_project_id_and_project_status_id($_GET[project_id],$project->get_current_status_id());
											
											$sub_folder_id = $project->get_sub_folder($_GET[key], $project->get_current_status_id());
											
											if (is_numeric($sub_folder_id))
											{
												$folder_id = $sub_folder_id;
											}
											
											$return_value = $module_dialog['class']::$module_dialog[method]($current_status_requirements[$_GET[key]][type_id], $current_status_requirements[$_GET[key]][category_id], $project->get_organisation_unit_id(), $folder_id);
											
											if (is_numeric($return_value))
											{
												if ($_GET[retrace])
												{
													$params = http_build_query(Misc::resovle_retrace_string($_GET[retrace]),'','&#38;');
												}
												else
												{
													$paramquery[username] = $_GET[username];
													$paramquery[session_id] = $_GET[session_id];
													$paramquery[nav] = "home";
													$params = http_build_query($paramquery,'','&#38;');
												}
												
												
												if (ProjectItemFactory::create($_GET[project_id], $return_value, $_GET[key], $_POST[keywords], $_POST[description]) == true)
												{
													if ($transaction_id != null)
													{
														$transaction->commit($transaction_id);
													}
													Common_IO::step_proceed($params, "Add Item", "Successful." ,null);
												}
												else
												{
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													Common_IO::step_proceed($params, "Add Item", "Failed." ,null);	
												}
											}
											else
											{
												if ($return_value === false)
												{
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new ModuleDialogFailedException("",1);
												}
												else
												{
													if ($transaction_id != null)
													{
														$transaction->commit($transaction_id);
													}
												}
											}
										}
									}
									else
									{
										throw new ModuleDialogCorruptException(null, null);
									}
								}
								else
								{
									throw new ModuleDialogCorruptException(null, null);
								}
							}
							else
							{
								throw new ModuleDialogNotFoundException(null, null);
							}
						}
						else
						{
							throw new ModuleDialogMissingException(null, null);
						}
					}
					else
					{
						$exception = new Exception("", 1);
						$error_io = new Error_IO($exception, 200, 40, 2);
						$error_io->display_error();
					}
				break;
				
				// Common Dialogs
				/**
				 * @todo errors, exceptions
				 */
				case("common_dialog"):
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("common_dialog", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]();
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				break;
				
				// Search
				/**
				 * @todo errors, exceptions
				 */
				case("search"):
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("search", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]();
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				break;
				
				// Default
				
				default:
					self::list_user_related_projects(null);
				break;
	
			endswitch;
		}
		catch (ProjectSecurityException $e)
		{
			$error_io = new Error_IO($e, 200, 40, 1);
			$error_io->display_error();
		}
	}
	
}
?>
