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
			
		$list = new List_IO(Project_Wrapper::count_list_user_related_projects($user_id), 20);
		
		$list->add_row("", "symbol", false, "16px");
		$list->add_row("Name", "name", true, null);
		$list->add_row("Organisation Unit", "organisation_unit", true, null);
		$list->add_row("Date/Time", "datetime", true, null);
		$list->add_row("Template", "template", true, null);
		$list->add_row("Status", "status", true, null);

		if ($_GET[page])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = Project_Wrapper::list_user_related_projects($user_id, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
			}
			else
			{
				$result_array = Project_Wrapper::list_user_related_projects($user_id, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
			}				
		}
		else
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = Project_Wrapper::list_user_related_projects($user_id, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
			}
			else
			{
				$result_array = Project_Wrapper::list_user_related_projects($user_id, null, null, 0, 20);
			}	
		}
		
		if (is_array($result_array) and count($result_array) >= 1)
		{
			foreach($result_array as $key => $value)
			{
				
				if (true == true) {
					$result_array[$key][symbol] = "<img src='images/icons/projects.png' alt='S' border='0' />";	
				}else{
					$result_array[$key][symbol] = "<img src='images/icons/project.png' alt='N' border='0' />";
				}
				
				$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
				$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
				
				$proejct_paramquery = array();
				$project_paramquery[username] = $_GET[username];
				$project_paramquery[session_id] = $_GET[session_id];
				$project_paramquery[nav] = "project";
				$project_paramquery[run] = "detail";
				$project_paramquery[project_id] = $value[id];
				$project_params = http_build_query($project_paramquery, '', '&#38;');
				
				$tmp_project_name = $result_array[$key][name];
				unset($result_array[$key][name]);
				$result_array[$key][name][content] = $tmp_project_name;
				$result_array[$key][name][link] = $project_params;
						

				if (strlen($value[template]) > 20) {
					$result_array[$key][template] = substr($result_array[$key][template],0,20).".";
				}

			}
			
		}else{
			$list->override_last_line("<span class='italic'>No results found!</span>");
		}
		
		
		$template = new Template("template/projects/user_related_projects.html");

		$template->set_var("title","My Projects");

		$template->set_var("table", $list->get_list($result_array, $_GET[page]));	

		$template->output();
	}
	
	/**
	 * @todo Error - No $_GET[ou_id]
	 */
	private static function list_organisation_unit_related_projects()
	{
		if ($_GET[ou_id])
		{
			$content_array = array();
		
			$table_io = new TableIO("OverviewTable");
		
			$table_io->add_row("","symbol",false,16);
			$table_io->add_row("Name","name",false,null);
			$table_io->add_row("Owner","owner",false,null);
			$table_io->add_row("Date/Time","datetime",false,null);
			$table_io->add_row("Template","template",false,null);
			$table_io->add_row("Status","status",false,null);
		
			$project_array = Project::list_organisation_unit_related_projects($_GET[ou_id], false);
		
			$project_array_cardinality = count($project_array);
		
			$counter = 0;
	
			if (!$_GET[page] or $_GET[page] == 1)
			{
				$page = 1;
				$counter_begin = 0;
				if ($project_array_cardinality > 25)
				{
					$counter_end = 24;
				}
				else
				{
					$counter_end = $project_array_cardinality-1;
				}
			}
			else
			{
				if ($_GET[page] >= ceil($project_array_cardinality/25))
				{
					$page = ceil($project_array_cardinality/25);
					$counter_end = $project_array_cardinality;
				}
				else
				{
					$page = $_GET[page];
					$counter_end = (25*$page)-1;
				}
				$counter_begin = (25*$page)-25;
			}
	
			if (is_array($project_array) and count($project_array) >= 1)
			{	
				$content_array = array();

				foreach ($project_array as $key => $value)
				{	
					if ($counter >= $counter_begin and $counter <= $counter_end)
					{
						$column_array = array();
				
						$project = new Project($value);
						$project_security = new ProjectSecurity($value);
						
						if ($project_security->is_access(1, false) == true)
						{					
							if ($value[subprojects] == true)
							{
								$symbol = "<img src='images/icons/projects.png' alt='S' border='0' />";	
							}
							else
							{
								$symbol = "<img src='images/icons/project.png' alt='N' border='0' />";
							}
						}
						else
						{
							if ($value[subprojects] == true)
							{
								$symbol = "<img src='core/images/denied_overlay.php?image=images/icons/projects.png' alt='S' border='0' />";	
							}
							else
							{
								$symbol = "<img src='core/images/denied_overlay.php?image=images/icons/project.png' alt='N' border='0' />";
							}
						}
						
						if (strlen($project->get_template_name()) > 20)
						{
							$project_template = substr($project->get_template_name(),0,20).".";
						}
						else
						{
							$project_template = $project->get_template_name();;
						}
	
						$paramquery = $_GET;
						$paramquery[username] = $_GET[username];
						$paramquery[session_id] = $_GET[session_id];
						$paramquery[nav] = "project";
						$paramquery[run] = "detail";
						$paramquery[project_id] = $value;
						$params = http_build_query($paramquery,'','&#38;');
						
						$owner = new User($project->get_owner_id());
						
						if ($project_security->is_access(1, false) == true)
						{	
							$column_array[symbol][link] = $params;
							$column_array[name][link] = $params;
						}
						else
						{
							$column_array[symbol][link] = "";
							$column_array[name][link] = "";
						}
						
						$column_array[symbol][content] = $symbol;
						$column_array[name][content] = $project->get_name();
						$column_array[owner] = $owner->get_full_name(true);
						$column_array[datetime] = $project->get_datetime();
						$column_array[template] = $project_template;
						$column_array[status] = $project->get_current_status_name();
						
						array_push($content_array, $column_array);
					}
					$counter++;
				}
				$table_io->add_content_array($content_array);
			}
			else
			{			
				$table_io->override_last_line("<span class='italic'>No Projects Found!</span>");
			}
	
			require_once("core/modules/organisation_unit/organisation_unit.io.php");
			$organisation_unit_io = new OrganisationUnitIO;
			$organisation_unit_io->detail();

			$template = new Template("template/projects/organisation_unit_related_projects.html");	

			$template->set_var("table", $table_io->get_table($page ,$project_array_cardinality));		
			
			$template->output();
			
		}
		else
		{
			// ! ERROR !
		}
	}
	
	private static function create()
	{
		global $user, $session, $project_security;
		
		try
		{
			if (!$_GET[nextpage])
			{
				$session->delete_value("PROJECT_LAST_SCREEN");
				$session->delete_value("PROJECT_CURRENT_SCREEN");
				
				$session->delete_value("PROJECT_TYPE");
				$session->delete_value("PROJECT_ORGAN_UNIT");
				$session->delete_value("PROJECT_TOID");
				$session->delete_value("PROJECT_NAME");
				$session->delete_value("PROJECT_DESC");
				$session->delete_value("PROJECT_TEMPLATE");
				$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE");
				$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE_ID");	
				$session->delete_value("PROJECT_TEMPLATE_DATA_ARRAY");	
	
				if($_GET[run] == "new_subproject")
				{
					if ($project_security->is_access(3, false) == false)
					{
						throw new ProjectException("",1);	
					}
					else
					{
						$project_type = 3;
						if ($_GET[id])
						{
							$project_toid = $_GET[id];
						}
						else
						{
							$project_toid = $_GET[project_id];
						}
						$session->write_value("PROJECT_TYPE", $project_type, true);
						$session->write_value("PROJECT_TOID", $project_toid, true);
						$session->write_value("PROJECT_CURRENT_SCREEN", 2, true);
					}
				}
			}
			else
			{	
				$project_template = $session->read_value("PROJECT_TEMPLATE");
				if ($_POST[template])
				{
					$project_template_obj = new ProjectTemplate($_POST[template]);
				}
				else
				{
					$project_template_obj = new ProjectTemplate($project_template);
				}
				
				if ($project_template_obj->is_required_requirements() == true)
				{
					$project_template_specific_information = true;
				}
				else
				{
					$project_template_specific_information = false;
				}
			}
			
			switch ($_GET[nextpage]):
				case 0:
					if (is_numeric($_POST[type]))
					{
						$session->write_value("PROJECT_CURRENT_SCREEN", 1, true);
						$session->write_value("PROJECT_TYPE",$_POST[type],true);
					}
				break;
			
				case 1:
					if (!$_GET[tpage])
					{
						if ($session->read_value("PROJECT_TYPE") == 1 or $session->read_value("PROJECT_TYPE") == 2)
						{
							if (is_numeric($_POST[organunit]) and $_POST[organunit] != 0)
							{
								$session->write_value("PROJECT_CURRENT_SCREEN", 2, true);
								$session->write_value("PROJECT_ORGAN_UNIT",$_POST[organunit],true);
							}
							else
							{
								$error[0] = "Select an orangisation unit!";
							}
						}
						else
						{
							if (is_numeric($_POST[project_toid]) and $_POST[project_toid] != 0)
							{
								$session->write_value("PROJECT_CURRENT_SCREEN", 2, true);
								$session->write_value("PROJECT_TOID",$_POST[project_toid],true);
							}
							else
							{
								$error[0] = "Select a project!";
							}
						}
					}
					else
					{
						$session->write_value("PROJECT_CURRENT_SCREEN", $_GET[tpage], true);
						if (is_numeric($_POST[organunit]))
						{
							$session->write_value("PROJECT_ORGAN_UNIT",$_POST[organunit], true);	
						}
						if (is_numeric($_POST[project_toid])) {
							$session->write_value("PROJECT_TOID",$_POST[project_toid],true);
						}
						unset($_GET[tpage]);
					}
				break;
				
				case 2:
					if (!$_GET[tpage])
					{
						if ($_POST[submitbutton] == "previous")
						{
							$session->write_value("PROJECT_CURRENT_SCREEN", 1, true);
							if ($_POST[name])
							{
								$session->write_value("PROJECT_NAME",$_POST[name],true);
							}
							if ($_POST[desc])
							{
								$session->write_value("PROJECT_DESC",$_POST[desc],true);
							}
						}
						else
						{
							if ($_POST[name] and $_POST[desc])
							{
								$session->write_value("PROJECT_CURRENT_SCREEN", 3, true);
							}
							else
							{
								if (!$_POST[name])
								{
									$error[0] = "Enter a name!";	
								}
								else
								{
									if (is_numeric($project_toid))
									{
										if (Project::exist_project_name(null,$session->read_value("PROJECT_TOID"),$_POST[name]) == true)
										{
											$error[0] = "This Project already exists!";	
										}
									}
									else
									{
										if (Project::exist_project_name($session->read_value("PROJECT_ORGAN_UNIT"),null,$_POST[name]) == true)
										{
											$error[0] = "This Project already exists!";	
										}
									}
								}
								if (!$_POST[desc])
								{
									$error[1] = "Enter a description!"	;
								}
							}
							if ($_POST[name])
							{
								$session->write_value("PROJECT_NAME",$_POST[name],true);
							}
							if ($_POST[desc])
							{
								$session->write_value("PROJECT_DESC",$_POST[desc],true);
							}
						}
					}
					else
					{
						$session->write_value("PROJECT_CURRENT_SCREEN", $_GET[tpage], true);
						if ($_POST[name])
						{
							$session->write_value("PROJECT_NAME",$_POST[name],true);
						}
						if ($_POST[desc])
						{
							$session->write_value("PROJECT_DESC",$_POST[desc],true);
						}
						unset($_GET[tpage]);
					}
				break;
				
				case 3:
					if (!$_GET[tpage])
					{
						if ($_POST[submitbutton] == "previous")
						{
							$session->write_value("PROJECT_CURRENT_SCREEN", 2, true);
							if (is_numeric($_POST[template]))
							{
								$session->write_value("PROJECT_TEMPLATE",$_POST[template],true);
							}
						}
						else
						{
							if (is_numeric($_POST[template]) and $_POST[template] != 0)
							{
								if ($project_template_specific_information == true)
								{
									$session->write_value("PROJECT_CURRENT_SCREEN", 4, true);
								}
								else
								{
									$session->write_value("PROJECT_CURRENT_SCREEN", 5, true);	
								}	
								
								$session->write_value("PROJECT_TEMPLATE",$_POST[template],true);
							}
							else
							{
								if (!$_POST[template])
								{
									$error[0] = "Select a template!"	;
								}
							}
						}
					}
					else
					{
						$session->write_value("PROJECT_CURRENT_SCREEN", $_GET[tpage], true);
						if (is_numeric($_POST[name]))
						{
							$session->write_value("PROJECT_TEMPLATE",$_POST[template],true);
						}
						unset($_GET[tpage]);
					}
				break;
				
				case 4:
					if (!$_GET[tpage])
					{
						if ($_POST[submitbutton] == "previous")
						{
							$session->write_value("PROJECT_CURRENT_SCREEN", 3, true);
							if ($_POST[template_data_type])
							{
								$session->write_value("PROJECT_TEMPLATE_DATA_TYPE", $_POST[template_data_type], true);	
								
								$template_data_array = array();
								
								foreach($_POST as $key => $value)
								{
									if (strpos($key, "-vartype") === false and $key != "submitbutton" and $key != "template_data_type")
									{
										$template_data_array[$key] = $value;
									}
									else
									{
										// type-check
									}
								}
								
								$session->write_value("PROJECT_TEMPLATE_DATA_TYPE_ID", $_POST[template_data_type_id], true);
								$session->write_value("PROJECT_TEMPLATE_DATA_ARRAY", $template_data_array, true);
							}
						}
						else
						{
							if ($_POST[template_data_type])
							{
								$session->write_value("PROJECT_CURRENT_SCREEN", 5, true);
								
								$session->write_value("PROJECT_TEMPLATE_DATA_TYPE", $_POST[template_data_type], true);	
								
								$template_data_array = array();
								
								foreach($_POST as $key => $value)
								{
									if ($key != "template_data_type_id" and $key != "submitbutton" and $key != "template_data_type") {
										$template_data_array[$key] = $value;
									}
									else
									{
										// type-check
									}
								}
								$session->write_value("PROJECT_TEMPLATE_DATA_TYPE_ID", $_POST[template_data_type_id], true);
								$session->write_value("PROJECT_TEMPLATE_DATA_ARRAY", $template_data_array, true);
							}
						}
					}
					else
					{
						$session->write_value("PROJECT_CURRENT_SCREEN", $_GET[tpage], true);
						
						if ($_POST[template_data_type])
						{
							$session->write_value("PROJECT_TEMPLATE_DATA_TYPE", $_POST[template_data_type], true);	
									
							$template_data_array = array();
							
							foreach($_POST as $key => $value)
							{
								if (strpos($key, "-vartype") === false and $key != "submitbutton" and $key != "template_data_type") {
									$template_data_array[$key] = $value;
								}
								else
								{
									// type-check
								}
							}
							
							$session->write_value("PROJECT_TEMPLATE_DATA_TYPE_ID", $_POST[template_data_type_id], true);
							$session->write_value("PROJECT_TEMPLATE_DATA_ARRAY", $template_data_array, true);
						}
						unset($_GET[tpage]);
					}
				break;
				
				case 5:
					if (!$_GET[tpage])
					{
						if ($_POST[submitbutton] == "previous")
						{
							if ($project_template_specific_information == true)
							{
								$session->write_value("PROJECT_CURRENT_SCREEN", 4, true);
							}
							else
							{
								$session->write_value("PROJECT_CURRENT_SCREEN", 3, true);	
							}	
						}
						elseif ($_POST[submitbutton] == "finish")
						{
							$session->write_value("PROJECT_CURRENT_SCREEN", 6, true);
						}
					}
					else
					{
						$session->write_value("PROJECT_CURRENT_SCREEN", $_GET[tpage], true);
						unset($_GET[tpage]);
					}
				break;
			
			endswitch;
			
			if ($session->is_value("PROJECT_CURRENT_SCREEN"))
			{
				$current_screen = $session->read_value("PROJECT_CURRENT_SCREEN");
			}
			else
			{
				$current_screen = 0;
				$session->write_value("PROJECT_CURRENT_SCREEN", 0, true);
			}
			
			if ($session->is_value("PROJECT_LAST_SCREEN"))
			{
				$last_screen = $session->read_value("PROJECT_LAST_SCREEN");
			}
			else
			{
				$session->write_value("PROJECT_LAST_SCREEN", 0, true);
				$last_screen = 0;
			}
			
			if ($_GET[nextpage])
			{
				$project_type 					= $session->read_value("PROJECT_TYPE");
				$project_organ_unit 			= $session->read_value("PROJECT_ORGAN_UNIT");
				$project_toid 					= $session->read_value("PROJECT_TOID");
				$project_name 					= $session->read_value("PROJECT_NAME");
				$project_desc 					= $session->read_value("PROJECT_DESC");
				$project_template 				= $session->read_value("PROJECT_TEMPLATE");
				$project_template_data_type  	= $session->read_value("PROJECT_TEMPLATE_DATA_TYPE");	
				$project_template_data_type_id	= $session->read_value("PROJECT_TEMPLATE_DATA_TYPE_ID");	
				$project_template_data_array	= $session->read_value("PROJECT_TEMPLATE_DATA_ARRAY");	

				if (($session->read_value("PROJECT_TYPE") == 3 or $session->read_value("PROJECT_TYPE") == 4) and !$project_desc)
				{
					if ($project_toid)
					{
						$parent_project = new Project($project_toid);
						$project_desc = $parent_project->get_description();
					}
				}
			}
			elseif ($_GET[run] == "new_subproject")
			{
				if ($_GET[project_id])
				{
					$parent_project = new Project($_GET[project_id]);
					$project_desc = $parent_project->get_description();
				}
			}
			
			switch ($current_screen):
				case 1:
					// PAGE 1
					if ($session->read_value("PROJECT_LAST_SCREEN") < 1)
					{
						$session->write_value("PROJECT_LAST_SCREEN", 1, true);
						$last_screen = 1;
					}
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 1;
					$params = http_build_query($paramquery,'','&#38;');
				
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					$assistant_bar_io->add_screen(2, "Project Information", $paramquery);
					$assistant_bar_io->add_screen(3, "Template", $paramquery);
					
					if ($project_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen) {
							$assistant_bar_io->set_visited($i);
						}else{
							$assistant_bar_io->set_active($i);
						}
					}
				
					$template = new Template("template/projects/new_project_page_1.html");	
					$template->set_var("bar",$assistant_bar_io->get_content());
					$template->set_var("link",$params);	
					
					if ($session->read_value("PROJECT_TYPE") == 1 or $session->read_value("PROJECT_TYPE") == 2)
					{
						$template->set_var("organunit", true);
						
						$result = array();
						$counter = 0;
							
						$organisation_unit_array = OrganisationUnit::list_entries();
						
						if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
						{
							foreach($organisation_unit_array as $key => $value)
							{
								$organisation_unit = new OrganisationUnit($value);
						
								if ($organisation_unit->is_permission($user->get_user_id()) and $organisation_unit->get_stores_data() == true)
								{
									$result[$counter][value] = $value;
									$result[$counter][content] = $organisation_unit->get_name();		
				
									if ($project_organ_unit == $value)
									{
										$result[$counter][selected] = "selected";
									}
									else
									{
										$result[$counter][selected] = "";
									}
				
									$counter++;
								}
							}
						}
						
						if (!$result)
						{
							$result[$counter][value] = "0";
							$result[$counter][content] = "NO ORGANISATION UNIT FOUND!";	
						}
						$template->set_var("option",$result);
					}
					else
					{
						$template->set_var("organunit", false);
						
						$result = array();
						$counter = 0;
							
						$project = new Project(null);
						$project_array = $project->get_project_tree();
						
						if (is_array($project_array) and count($project_array) >= 1)
						{
							foreach($project_array as $key => $value)
							{
								$project = new Project($value[id]);
		
								for($i=1;$i<=$value[layer];$i++)
								{
									$pre_content .= "&nbsp;";
								}
						
								$result[$counter][value] = $value[id];
								$result[$counter][content] = $pre_content."".$project->get_name();		
			
								if ($project_toid == $value[id])
								{
									$result[$counter][selected] = "selected";
								}
								else
								{
									$result[$counter][selected] = "";
								}
			
								$counter++;
								
								unset($pre_content);
							}
						}
						else
						{
							$result[$counter][value] = "0";
							$result[$counter][content] = "NO PROJECT FOUND!";
						}
						$template->set_var("option",$result);
					}
					
					if ($error[0])
					{
						$template->set_var("error",$error[0]);
					}
					else
					{
						$template->set_var("error","");
					}
					
					$template->output();
				break;
		
				case 2:
					// PAGE 2
					if ($session->read_value("PROJECT_LAST_SCREEN") < 2)
					{
						$session->write_value("PROJECT_LAST_SCREEN", 2, true);
						$last_screen = 2;
					}
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 2;
					$params = http_build_query($paramquery,'','&#38;');
				
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					$assistant_bar_io->add_screen(2, "Project Information", $paramquery);
					$assistant_bar_io->add_screen(3, "Template", $paramquery);
					
					if ($project_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
				
					$template = new Template("template/projects/new_project_page_2.html");	
					$template->set_var("bar",$assistant_bar_io->get_content());
					$template->set_var("link",$params);	
				
					if ($project_name)
					{
						$template->set_var("name",$project_name);
					}
					else
					{
						$template->set_var("name","");
					}
					
					if ($project_desc)
					{
						$template->set_var("desc",$project_desc);
					}
					else
					{
						$template->set_var("desc","");
					}
	
					if ($error[0])
					{
						$template->set_var("error0",$error[0]);
					}
					else
					{
						$template->set_var("error0","");	
					}
					
					if ($error[1])
					{
						$template->set_var("error1",$error[1]);
					}
					else
					{
						$template->set_var("error1","");
					}
	
					$template->output();
				break;
				
				case 3:
					// PAGE 3
					if ($session->read_value("PROJECT_LAST_SCREEN") < 3)
					{
						$session->write_value("PROJECT_LAST_SCREEN", 3, true);
						$last_screen = 3;
					}
				
					$paramquery = $_GET;
					$paramquery[nextpage] = 3;
					$params = http_build_query($paramquery,'','&#38;');
				
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					$assistant_bar_io->add_screen(2, "Project Information", $paramquery);
					$assistant_bar_io->add_screen(3, "Template", $paramquery);
					
					if ($project_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
				
					$template = new Template("template/projects/new_project_page_3.html");	
					$template->set_var("bar",$assistant_bar_io->get_content());
					$template->set_var("link",$params);	
				
					$result = array();
					$counter = 0;
						
					$project_template_array = ProjectTemplateCat::list_entries();
					
					if (is_array($project_template_array))
					{
						foreach($project_template_array as $key => $value)
						{
							$project_template_cat = new ProjectTemplateCat($value);
							$result[$counter][value] = "0";
							$result[$counter][content] = $project_template_cat->get_name();		
							$result[$counter][selected] = "";
		
							$counter++;
							
							$project_template_sub_array = ProjectTemplate::list_entries_by_cat_id($value);
							
							if (is_array($project_template_sub_array))
							{
								foreach($project_template_sub_array as $sub_key => $sub_value)
								{
									$project_sub_template = new ProjectTemplate($sub_value);
									
									if (($session->read_value("PROJECT_TYPE") == 1 or 
										 $session->read_value("PROJECT_TYPE") == 3) and
										($project_sub_template->get_parent_template() == false))
									{
										$result[$counter][value] = $sub_value;
										$result[$counter][content] = "&nbsp;".$project_sub_template->get_name();		
					
										
										if ($project_template == $sub_value)
										{
											$result[$counter][selected] = "selected";
										}
										else
										{
											$result[$counter][selected] = "";
										}
					
										$counter++;
									}
									elseif (($session->read_value("PROJECT_TYPE") == 2 or 
										 	  $session->read_value("PROJECT_TYPE") == 4) and
										   	 ($project_sub_template->get_parent_template() == true))
									{
										$result[$counter][value] = $sub_value;
										$result[$counter][content] = "&nbsp;".$project_sub_template->get_name();		
					
										if ($project_template == $sub_value)
										{
											$result[$counter][selected] = "selected";
										}
										else
										{
											$result[$counter][selected] = "";
										}
					
										$counter++;
									}
								}
							}
							unset($project_template_sub_array);
						}
					}
					else
					{
						$result[$counter][value] = "0";
						$result[$counter][content] = "NO TEMPLATES FOUND!";		
					}
			
					$template->set_var("option",$result);
				
					if ($error[0])
					{
						$template->set_var("error",$error[0]);
					}
					else
					{
						$template->set_var("error","");
					}
				
					$template->output();
				break;
				
				case 4:
					// Page 4
					if ($session->read_value("PROJECT_LAST_SCREEN") < 4)
					{
						$session->write_value("PROJECT_LAST_SCREEN", 4, true);
						$last_screen = 4;
					}
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 4;
					$params = http_build_query($paramquery,'','&#38;');
				
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					$assistant_bar_io->add_screen(2, "Project Information", $paramquery);
					$assistant_bar_io->add_screen(3, "Template", $paramquery);
					
					if ($project_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
				
					$project_template_obj = new ProjectTemplate($project_template);
					$required_array = $project_template_obj->get_required_requirements();
				
					if (is_array($required_array) and count($required_array) >= 1)
					{
						$value_type_id = 0;
						$sample_count = 0;
						$is_value = false;
						$is_sample = false;
						
						foreach($required_array as $key => $value)
						{						
							if ($value[xml_element] == "item")
							{
								if ($value[type] == "value")
								{
									$is_value = true;
								}
							}
							
							if ($value[xml_element] == "type" and !$value[close] and $is_value == true)
							{
								$value_type_id = $value[id];
							}
						} 
						
						if ($is_value == true)
						{
							$template = new Template("template/projects/new_project_page_4_value.html");
							$template->set_var("bar",$assistant_bar_io->get_content());
							$template->set_var("link",$params);	
							
							$value_obj = new Value(null);
							if ($project_template_data_type == "value")
							{
								$value_obj->set_content_array($project_template_data_array);
							}	
							$value_html = $value_obj->get_html_form(null, $value_type_id, null);
							$template->set_var("content",$value_html);
							
							$template->set_var("template_data_type_id", $value_type_id);
							$template->output();
						}
						else
						{
							$template = new Template("template/projects/new_project_page_4_error.html");
							$template->set_var("bar",$assistant_bar_io->get_content());
							$template->set_var("link",$params);	
							$template->output();
						}
					}
					else
					{
						$template = new Template("template/projects/new_project_page_4_error.html");
						$template->set_var("bar",$assistant_bar_io->get_content());
						$template->set_var("link",$params);	
						$template->output();
					}			
				break;
				
				case 5:
					// Page 5
					if ($session->read_value("PROJECT_LAST_SCREEN") < 5)
					{
						$session->write_value("PROJECT_LAST_SCREEN", 5, true);
						$last_screen = 5;
					}
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 5;
					$params = http_build_query($paramquery,'','&#38;');
					
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					$assistant_bar_io->add_screen(2, "Project Information", $paramquery);
					$assistant_bar_io->add_screen(3, "Template", $paramquery);
					
					if ($project_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Template Specific Information", $paramquery);
					}else{
						$assistant_bar_io->add_screen(4, "Template Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
					
					$template = new Template("template/projects/new_project_page_5.html");	
					$template->set_var("bar",$assistant_bar_io->get_content());
					$template->set_var("link",$params);	
					
					$project_template = new ProjectTemplate($session->read_value("PROJECT_TEMPLATE"));
					
					$template->set_var("name", $session->read_value("PROJECT_NAME"));
					$template->set_var("template", $project_template->get_name());
					$template->set_var("desc", $session->read_value("PROJECT_DESC"));
					
					$template->set_var("content","");
					
					$template->output();			
				break;
				
				case 6:
					$project_owner = $user->get_user_id();
					
					try
					{
						$project = new Project(null);
						
						$project->set_template_data($project_template_data_type, $project_template_data_type_id, $project_template_data_array);
														
						if ($project_type and $project_organ_unit and $project_name and $project_desc and $project_template)
						{
							$new_project_id = $project->create($project_organ_unit, null, $project_name, $project_owner, $project_template, $project_desc);
							
							$session->delete_value("PROJECT_LAST_SCREEN");
							$session->delete_value("PROJECT_CURRENT_SCREEN");
							
							$session->delete_value("PROJECT_TYPE");
							$session->delete_value("PROJECT_ORGAN_UNIT");
							$session->delete_value("PROJECT_TOID");
							$session->delete_value("PROJECT_NAME");
							$session->delete_value("PROJECT_DESC");
							$session->delete_value("PROJECT_TEMPLATE");
							$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE");
							$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE_ID");	
							$session->delete_value("PROJECT_TEMPLATE_DATA_ARRAY");	
							
							$paramquery = $_GET;
							unset($paramquery[nextpage]);
							$paramquery[run] = "detail";
							$paramquery[project_id] = $new_project_id;
							$params = http_build_query($paramquery, '', '&#38;');
							
							Common_IO::step_proceed($params, "Create New Project", "Operation Successful", null);		
						}
						elseif($project_type and $project_toid and $project_name and $project_desc and $project_template)
						{
							$new_project_id = $project->create(null, $project_toid, $project_name, $project_owner, $project_template, $project_desc);
							
							$session->delete_value("PROJECT_LAST_SCREEN");
							$session->delete_value("PROJECT_CURRENT_SCREEN");
							
							$session->delete_value("PROJECT_TYPE");
							$session->delete_value("PROJECT_ORGAN_UNIT");
							$session->delete_value("PROJECT_TOID");
							$session->delete_value("PROJECT_NAME");
							$session->delete_value("PROJECT_DESC");
							$session->delete_value("PROJECT_TEMPLATE");
							$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE");
							$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE_ID");	
							$session->delete_value("PROJECT_TEMPLATE_DATA_ARRAY");	
							
							$paramquery = $_GET;
							unset($paramquery[nextpage]);
							$paramquery[run] = "detail";
							$paramquery[project_id] = $new_project_id;
							$params = http_build_query($paramquery, '', '&#38;');
							
							Common_IO::step_proceed($params, "Create New Project", "Operation Successful", null);
						}
						else
						{
							$paramquery = $_GET;
							unset($paramquery[nextpage]);
							unset($paramquery[run]);
							$params = http_build_query($paramquery);	
		
							Common_IO::step_proceed($params, "Create New Project", "Operation Failed", null);
						}
					}
					catch (ProjectCreationFailedException $e)
					{
						$error_io = new Error_IO($e, 200, 30, 1);
						$error_io->display_error();
					}					
				break;
				
				case 0:
				default:
					// Page 0
					if ($session->read_value("SAMPLE_LAST_SCREEN") < 0)
					{
						$session->write_value("SAMPLE_LAST_SCREEN", 0, true);
						$last_screen = 0;
					}
				
					$paramquery = $_GET;
					$paramquery[nextpage] = 0;
					$params = http_build_query($paramquery,'','&#38;');
				
					$template = new Template("template/projects/new_project_page_0.html");	
					$template->set_var("link",$params);	
		
					$template->output();
				break;
			endswitch;
			
		}
		catch (ProjectException $e)
		{
			$error_io = new Error_IO($e, 200, 40, 2);
			$error_io->display_error();
		}
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
	
	public static function list_projects_by_item_id($item_id)
	{
		if (is_numeric($item_id))
		{
			$list = new List_IO(Project_Wrapper::count_projects_by_item_id($item_id), 20);

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
							
							$sql = " SELECT item_id FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE project_id = ".$_GET[project_id]."";
							$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_list", $_GET[dialog]);
							
							if (file_exists($module_dialog[class_path]))
							{
								require_once($module_dialog[class_path]);
								
								if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
								{
									$module_dialog['class']::$module_dialog[method]($sql);
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
													Common_IO::step_proceed($params, "Add Item", "Succeed." ,null);
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
