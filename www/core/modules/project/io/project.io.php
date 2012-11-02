<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
	/**
	 * @param integer $user_id
	 */
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
		
		$list->add_column("", "symbol", false, "16px");
		$list->add_column("Name", "name", true, null);
		$list->add_column("Organisation Unit", "organisation_unit", true, null);
		$list->add_column("Date/Time", "datetime", true, null);
		$list->add_column("Template", "template", true, null);
		$list->add_column("Status", "status", true, null);

		$template = new HTMLTemplate("project/list_user.html");
	
		if ($user_id == $user->get_user_id())
		{
			$template->set_var("title","My Projects");
		}
		else
		{
			$template->set_var("title","Projects of ".$user->get_username());
		}
	
		$paramquery = array();
		$paramquery['username'] = $_GET['username'];
		$paramquery['session_id'] = $_GET['session_id'];
		$paramquery['nav'] = "project";
		$paramquery['run'] = "new";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("new_project_params", $params);
		
		$template->set_var("list", $list->get_list());
	
		$template->output();
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function list_organisation_unit_related_projects()
	{
		if ($_GET['ou_id'])
		{
			$organisation_unit_id = $_GET['ou_id'];
			
			$argument_array = array();
			$argument_array[0][0] = "organisation_unit_id";
			$argument_array[0][1] = $organisation_unit_id;
			
			$list = new List_IO("ProjectOrganisationUnitRelated", "ajax.php?nav=project", "list_organisation_unit_related_projects", "count_organisation_unit_related_projects", $argument_array, "ProjectAjaxOrganisationUnit", 12);
		
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Name","name",true,null);
			$list->add_column("Owner","owner",true,null);
			$list->add_column("Date/Time","datetime",true,null);
			$list->add_column("Template","template",true,null);
			$list->add_column("Status","status",true,null);
		
			require_once("core/modules/organisation_unit/io/organisation_unit.io.php");
			$organisation_unit_io = new OrganisationUnitIO();
			$organisation_unit_io->detail();
			
			$template = new HTMLTemplate("project/list_organisation_unit.html");	

			$template->set_var("list", $list->get_list());
	
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	public static function create()
	{
		global $session;
		
		require_once("core/modules/base/common/io/assistant.io.php");
		
		if ($_GET['run'] == "new_subproject")
		{
			$session->write_value("PROJECT_ADD_ROLE", "direct_sub_project", true);
			$session->write_value("PROJECT_TYPE",3);
			$session->write_value("PROJECT_TOID",$_GET['project_id']);
			
			$assistant_io = new AssistantIO("ajax.php?nav=project&run=create_project", "ProjectCreateAssistantField", 2);
		}
		else
		{
			$session->write_value("PROJECT_ADD_ROLE", "project", true);
			
			$assistant_io = new AssistantIO("ajax.php?nav=project&run=create_project", "ProjectCreateAssistantField", 0);
		}
		
		$assistant_io->add_screen("Organisation Unit");
		$assistant_io->add_screen("Project Information");
		$assistant_io->add_screen("Template");
		$assistant_io->add_screen("Template Specific Information");
		$assistant_io->add_screen("Summary");
		
		$template = new HTMLTemplate("project/new_project.html");	
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
			
				$template = new HTMLTemplate("project/project_detail.html");
				
				$template->set_var("get_array", serialize($_GET));
				
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
	public static function structure()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project = new Project($_GET[project_id]);
				$project_structure_array = $project->get_project_tree();
				
				$template = new HTMLTemplate("project/project_structure.html");
				
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function workflow()
	{
		global $project_security;
		
		if ($_GET['project_id'])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project = new Project($_GET['project_id']);
				
				$template = new HTMLTemplate("project/workflow/project_workflow.html");
				
				$element_string = "";
				
				$workflow = $project->get_all_status_array();	
				$workflow_array = ProjectWorkflow::get_status_list($workflow->get_start_element(), array(), null, null, null);
								
				if (is_array($workflow_array) and count($workflow_array) >= 1)
				{
					$element_space_cache = array();
					
					foreach ($workflow_array as $line_key => $line_value)
					{
						$element_counter = count($line_value);
						$element_space_cache_counter = count($element_space_cache);
						$element_space_temp_cache = array();
						
						$max_columns = max(array_keys($line_value));
						
						if (count($element_space_cache) >= 1)
						{
							$max_element_space_cache = max(array_keys($element_space_cache));
						}
						else
						{
							$max_element_space_cache = 0;
						}
						
						$first_spacer = false;
						
						// Bis zum größeren von $max_columns oder $element_space_cache_counter
						for ($i = 0; $i <= max($max_columns,$max_element_space_cache); $i++)
						{	
														
							$spacer_template = new HTMLTemplate("project/workflow/workflow_horizontal_spacer.html");
							
							if ($element_space_cache[$i] == true or $first_spacer == true)
							{
								// Problem bei paralellen durchgängen
								
								if ($first_spacer == false)
								{
									
									if ($element_counter == 1 and $element_space_cache_counter == 1)
									{
										$spacer_template->set_var("spacer_class", "ProjectWorkflowElementHorizontalSpacerVerticalLine");
									}
									else
									{
										// OB Unterschied
										if (isset($line_value[$i+1]) and isset($element_space_cache[$i+1])
											 or ($i == max(array_keys($line_value)) and $i ==  max(array_keys($element_space_cache))))
										{
											$spacer_template->set_var("spacer_class", "ProjectWorkflowElementHorizontalSpacerVerticalLine");
											$element_counter--;
											$element_space_cache_counter--;
										}
										else
										{
											$first_spacer = true;
											$spacer_template->set_var("spacer_class", "ProjectWorkflowElementHorizontalSpacerLeftT");
										}
									}
								}
								else
								{
									// While Schleife überalle zukünftigen Elemente bis 
									// 1) Oben neues Element ex.
									// 		-> Exisitiert bis dahin unten ein EL. ?
									//			dann: 
									//				-> Existiert unten ein El.
									//				dann: 
									//					-> Ist lertes EL.
									//					dann: TopRightCorner
									//					ansonsten: T
									//				ansonsten: hor-line
									//			ansonsten: nichts
									// 2) Ende der Zeile
									//		-> Prüfung wie 1)
									
									$n = $i;
									$next_top_element = null;
									$last_bottom_element = null;
									
									while ($n <= max($max_columns,$max_element_space_cache))
									{
										if ($element_space_cache[$n] and $i != $n)
										{
											$next_top_element = $n;
											break;
										}
										else
										{
											if ($line_value[$n])
											{
												$last_bottom_element = $n;
											}
										}
										$n = $n+1;	
									}
					
									if ($last_bottom_element == $i or ($next_top_element == ($i+1) and $i <= $max_columns) or ($i == $max_element_space_cache and $i > $max_columns))
									{
										if ($line_value[$i])
										{
											$spacer_template->set_var("corner_spacer_class", "ProjectWorkflowElementCornerSpacerHorizontalLine");
											$spacer_template->set_var("spacer_class", "ProjectWorkflowElementHorizontalSpacerTopRightCorner");
										}
										else
										{
											$spacer_template->set_var("corner_spacer_class", "ProjectWorkflowElementCornerSpacerHorizontalLine");
											$spacer_template->set_var("spacer_class", "ProjectWorkflowElementHorizontalSpacerBottomRightCorner");
										}										
										
										$first_spacer = false;
									}
									else
									{
										if ($line_value[$i])
										{
											$spacer_template->set_var("corner_spacer_class", "ProjectWorkflowElementCornerSpacerHorizontalLine");
											$spacer_template->set_var("spacer_class", "ProjectWorkflowElementHorizontalSpacerT");
										}
										else
										{
											if ($element_space_cache[$i])
											{
												$spacer_template->set_var("corner_spacer_class", "ProjectWorkflowElementCornerSpacerHorizontalLine");
												$spacer_template->set_var("spacer_class", "ProjectWorkflowElementHorizontalSpacerReversedT");
											}
											else
											{
												$spacer_template->set_var("corner_spacer_class", "ProjectWorkflowElementCornerSpacerHorizontalLine");
												$spacer_template->set_var("spacer_class", "ProjectWorkflowElementHorizontalSpacerHorizontalLine");
											}
										}
									}
								}
							}
							
							$element_string .= $spacer_template->get_string();
								
							if ($line_value[$i])
							{
								$element_value = $line_value[$i];	
								
								$element_template = new HTMLTemplate("project/workflow/project_workflow_element.html");
								
								if (is_object($element_value[0]))
								{
									if ($element_value[0] instanceof WorkflowElementActivity)
									{
										$element_template->set_var("name", $element_value[0]->get_attachment("name"));
										$element_template->set_var("class", "ProjectWorkflowElementStatus");
										
										$element_space_temp_cache[$i] = true;
									}
									
									if ($element_value[0] instanceof WorkflowElementOr)
									{
										$element_template->set_var("name", "or");
										$element_template->set_var("class", "ProjectWorkflowElementOr");
										
										$element_space_temp_cache[$i] = true;
									}
								}
								else
								{
									$element_template->set_var("name", "");
									$element_template->set_var("class", "ProjectWorkflowElementLine");
									
									$element_space_temp_cache[$i] = true;
								}
								
								$element_string .= $element_template->get_string();
							}
							else
							{
								$element_template = new HTMLTemplate("project/workflow/project_workflow_empty_element.html");
								$element_string .= $element_template->get_string();
							}
							
							
						}
						
						$element_space_cache = $element_space_temp_cache;
						
						// Break
						
						$element_template = new HTMLTemplate("project/workflow/project_workflow_clear_element.html");
						$element_string .= $element_template->get_string();
						
					}
				}
				
				$template->set_var("elements", $element_string);
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
	 * @param integer $item_id
	 * @param bool $in_assistant
	 * @param bool $form_field_name
	 * @throws ItemIDMissingException
	 */
	public static function list_projects_by_item_id($item_id, $in_assistant = false, $form_field_name = null)
	{		
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
				
				$template = new HTMLTemplate("project/list_projects_by_item.html");
				
				$list->add_column("","symbol",false,16);
				$list->add_column("Name","name",true,null);
				$list->add_column("Date/Time","datetime",true,null);
				$list->add_column("Template","template",true,null);
				$list->add_column("Owner","owner",true,null);
				$list->add_column("Status","status",true,null);
			}
			else
			{
				$list = new List_IO("ProjectByItem", "/core/modules/project/ajax/project.ajax.php", "list_projects_by_item_id", "count_projects_by_item_id", $argument_array, "ProjectParentAjax", 20, false, false);
				
				$template = new HTMLTemplate("project/list_projects_by_item_without_border.html");
				
				$list->add_column("","checkbox",false,"16px", $form_field_name);
				$list->add_column("","symbol",false,16);
				$list->add_column("Name","name",false,null);
				$list->add_column("Date/Time","datetime",false,null);
				$list->add_column("Template","template",false,null);
				$list->add_column("Owner","owner",false,null);
				$list->add_column("Status","status",false,null);
			}
		
			$template->set_var("list", $list->get_list());
			
			$template->output();
		}
		else
		{
			throw new ItemIDMissingException();
		}
	}
		
}
?>
