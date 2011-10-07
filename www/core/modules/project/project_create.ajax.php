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
require_once("../base/ajax.php");

/**
 * Project Create AJAX IO Class
 * @package project
 */
class ProjectCreateAjax extends Ajax
{	
	function __construct()
	{
		parent::__construct();
	}
	
	private function get_content($page)
	{
		global $session, $user;
		
		switch ($page):

			case "0":						
				$template = new Template("../../../template/projects/new_project_page_0.html");	
				return $template->get_string();
			break;
		
			case "1":
				$template = new Template("../../../template/projects/new_project_page_1.html");	
				
				if ($session->read_value("PROJECT_TYPE") == 1 or $session->read_value("PROJECT_TYPE") == 2)
				{
					$project_organ_unit = $session->read_value("PROJECT_ORGANISATION_UNIT");
					
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
					$project_toid = $session->read_value("PROJECT_TOID");
					
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
						$result[$counter][selected] = "";
					}
					$template->set_var("option",$result);
				}

				return $template->get_string();
			break;
		
			case "2":
				$project_name = $session->read_value("PROJECT_NAME");
				$project_desc = $session->read_value("PROJECT_DESCRIPTION");
				
				$template = new Template("../../../template/projects/new_project_page_2.html");	
			
				if ($project_name)
				{
					$template->set_var("project_name",$project_name);
				}
				else
				{
					$template->set_var("project_name","");
				}
				
				if ($project_desc)
				{
					$template->set_var("project_description",$project_desc);
				}
				else
				{
					$template->set_var("project_description","");
				}

				return $template->get_string();
			break;
			
			case "3":
				$project_template = $session->read_value("PROJECT_TEMPLATE");
				
				$template = new Template("../../../template/projects/new_project_page_3.html");	
			
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
						$result[$counter][disabled] = "disabled='disabled'";
	
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
				
									$result[$counter][disabled] = "";
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
				
									$result[$counter][disabled] = "";
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
					$result[$counter][disabled] = "disabled='disabled'";
				}
		
				$template->set_var("option",$result);
				
				return $template->get_string();
			break;
			
			case "4":
				$project_template = $session->read_value("PROJECT_TEMPLATE");
				$project_template_data_type = $session->read_value("PROJECT_TEMPLATE_DATA_TYPE");
				$project_template_data_array = $session->read_value("PROJECT_TEMPLATE_DATA_ARRAY");	
				
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
						$template = new Template("../../../template/projects/new_project_page_4_value.html");
						
						require_once("../../../core/modules/data/value_form.io.php");
						$value_form_io = new ValueFormIO(null, $value_type_id, null, $project_template_data_array);
						$value_form_io->set_field_class("ProjectCreateAssistantField");
						$template->set_var("content",$value_form_io->get_content());
						
						$template->set_var("project_template_data_type_id", $value_type_id);
						return $template->get_string();
					}
					else
					{
						$template = new Template("../../../template/projects/new_project_page_4_error.html");
						return $template->get_string();
					}
				}
				else
				{
					$template = new Template("../../../template/projects/new_project_page_4_error.html");
					return $template->get_string();
				}			
			break;
			
			case "5":
				$template = new Template("../../../template/projects/new_project_page_5.html");	
				
				$project_template = new ProjectTemplate($session->read_value("PROJECT_TEMPLATE"));
				
				$template->set_var("project_name", $session->read_value("PROJECT_NAME"));
				$template->set_var("project_template", $project_template->get_name());
				$template->set_var("project_description", $session->read_value("PROJECT_DESCRIPTION"));
				
				$template->set_var("content","");
				
				return $template->get_string();		
			break;
			
			default:
				return "Error: The requested page does not exist!";
			break;
			
		endswitch;

	}

	private function get_next_page($page)
	{
		global $session;
		
		if ($page == 3)
		{
			$project_template = $session->read_value("PROJECT_TEMPLATE");
			$project_template_obj = new ProjectTemplate($project_template);
			
			if ($project_template_obj->is_required_requirements() == true)
			{
				return 4;
			}
			else
			{
				return 5;
			}
		}
		else
		{
			return ($page+1);
		}
	}
	
	private function get_previous_page($page)
	{
		global $session;
		
		if ($page == 5)
		{
			$project_template = $session->read_value("PROJECT_TEMPLATE");
			$project_template_obj = new ProjectTemplate($project_template);
			
			if ($project_template_obj->is_required_requirements() == true)
			{
				return 4;
			}
			else
			{
				return 3;
			}
		}
		else
		{
			return ($page-1);
		}
	}
	
	private function set_data($page, $data)
	{
		global $session;
		
		$data_array = json_decode($data);
		
		if (is_array($data_array) and count($data_array) >= 1)
		{
			switch($page):
				case "0":
					foreach($data_array as $key => $value)
					{
						if ($value[0] == "project_type")
						{
							$session->write_value("PROJECT_TYPE",$value[1],true);
						}
					}
				break;
				
				case "1":
					if ($session->read_value("PROJECT_TYPE") == 1 or $session->read_value("PROJECT_TYPE") == 2)
					{
						foreach($data_array as $key => $value)
						{
							if ($value[0] == "project_organisation_unit")
							{
								$session->write_value("PROJECT_ORGANISATION_UNIT",$value[1],true);
							}
						}
					}
					else
					{
						foreach($data_array as $key => $value)
						{
							if ($value[0] == "project_toid")
							{
								$session->write_value("PROJECT_TOID",$value[1],true);
							}
						}
					}
				break;
				
				case "2":
					foreach($data_array as $key => $value)
					{
						if ($value[0] == "project_name")
						{
							$session->write_value("PROJECT_NAME",$value[1],true);
						}
						if ($value[0] == "project_description")
						{
							$session->write_value("PROJECT_DESCRIPTION",$value[1],true);
						}
					}
				break;
				
				case "3":
					foreach($data_array as $key => $value)
					{
						if ($value[0] == "project_template")
						{
							$session->write_value("PROJECT_TEMPLATE",$value[1],true);
						}
					}
				break;
				
				case "4":
					foreach($data_array as $key => $value)
					{
						switch($value[0]):
							
							case "project_template_data_type_id":
								$session->write_value("PROJECT_TEMPLATE_DATA_TYPE_ID", $value[1], true);	
							break;
							
							case "project_template_data_type":
								$session->write_value("PROJECT_TEMPLATE_DATA_TYPE", $value[1], true);	
							break;
								
							default:
								if (strpos($value[0], "-vartype") === false)
								{
									$template_data_array[$value[0]] = $value[1];
								}
							break;
						
						endswitch;
					}
					
					if (is_array($template_data_array) and count($template_data_array) >= 1)
					{
						$session->write_value("PROJECT_TEMPLATE_DATA_ARRAY", $template_data_array, true);
					}
				break;
			endswitch;
		}
	}
	
	private function check_name($name)
	{
		global $session;
		
		$project_toid = $session->read_value("PROJECT_TOID");
		
		if (is_numeric($project_toid))
		{
			if (Project::exist_project_name(null,$session->read_value("PROJECT_TOID"),$name) == true)
			{
				return "1";
			}
		}
		else
		{
			if (Project::exist_project_name($session->read_value("PROJECT_ORGANISATION_UNIT"),null,$name) == true)
			{
				return "1";
			}
		}
		
		return "0";
	}
	
	private function run($username, $session_id)
	{
		global $session, $user;
		
		$project_owner = $user->get_user_id();

		$project_type 					= $session->read_value("PROJECT_TYPE");
		$project_organ_unit 			= $session->read_value("PROJECT_ORGANISATION_UNIT");
		$project_toid 					= $session->read_value("PROJECT_TOID");
		$project_name 					= $session->read_value("PROJECT_NAME");
		$project_desc 					= $session->read_value("PROJECT_DESCRIPTION");
		$project_template 				= $session->read_value("PROJECT_TEMPLATE");
		$project_template_data_type  	= $session->read_value("PROJECT_TEMPLATE_DATA_TYPE");	
		$project_template_data_type_id	= $session->read_value("PROJECT_TEMPLATE_DATA_TYPE_ID");	
		$project_template_data_array	= $session->read_value("PROJECT_TEMPLATE_DATA_ARRAY");	
		
		try
		{
			$project = new Project(null);
			
			$project->set_template_data($project_template_data_type, $project_template_data_type_id, $project_template_data_array);
											
			if ($project_type and $project_organ_unit and $project_name and $project_desc and $project_template)
			{
				$new_project_id = $project->create($project_organ_unit, null, $project_name, $project_owner, $project_template, $project_desc);
				
				$session->delete_value("PROJECT_TYPE");
				$session->delete_value("PROJECT_ORGANISATION_UNIT");
				$session->delete_value("PROJECT_NAME");
				$session->delete_value("PROJECT_DESCRIPTION");
				$session->delete_value("PROJECT_TEMPLATE");
				$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE");
				$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE_ID");	
				$session->delete_value("PROJECT_TEMPLATE_DATA_ARRAY");		
				
				$paramquery = array();
				$paramquery['username'] = $username;
				$paramquery['session_id'] = $session_id;
				$paramquery['nav'] = "project";
				$paramquery['run'] = "detail";
				$paramquery['project_id'] = $new_project_id;
				$params = http_build_query($paramquery, '', '&');
				
				return "index.php?".$params;
			}
			elseif($project_type and $project_toid and $project_name and $project_desc and $project_template)
			{
				$new_project_id = $project->create(null, $project_toid, $project_name, $project_owner, $project_template, $project_desc);
				
				$session->delete_value("PROJECT_LAST_SCREEN");
				$session->delete_value("PROJECT_CURRENT_SCREEN");
				
				$session->delete_value("PROJECT_TYPE");
				$session->delete_value("PROJECT_TOID");
				$session->delete_value("PROJECT_NAME");
				$session->delete_value("PROJECT_DESCRIPTION");
				$session->delete_value("PROJECT_TEMPLATE");
				$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE");
				$session->delete_value("PROJECT_TEMPLATE_DATA_TYPE_ID");	
				$session->delete_value("PROJECT_TEMPLATE_DATA_ARRAY");	
				
				$paramquery = array();
				$paramquery['username'] = $username;
				$paramquery['session_id'] = $session_id;
				$paramquery['nav'] = "project";
				$paramquery['run'] = "detail";
				$paramquery['project_id'] = $new_project_id;
				$params = http_build_query($paramquery, '', '&');
				
				return "index.php?".$params;
			}
			else
			{
				return 0;
			}
		}
		catch (ProjectCreationFailedException $e)
		{
			return 0;
		}					
	}
	
	public function handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET['run']):
			
				case "get_content":
					echo $this->get_content($_GET['page']);
				break;
				
				case "get_next_page":
					echo $this->get_next_page($_GET['page']);
				break;
				
				case "get_previous_page":
					echo $this->get_previous_page($_GET['page']);
				break;
				
				case "set_data":
					echo $this->set_data($_POST['page'], $_POST['data']);
				break;
				
				case "check_data":
					echo $this->check_name($_GET['name']);
				break;
				
				case "run":
					echo $this->run($_GET['username'], $_GET['session_id']);
				break;
				
			endswitch;
		}
	}
}

$project_create_ajax = new ProjectCreateAjax();
$project_create_ajax->handler();

?>