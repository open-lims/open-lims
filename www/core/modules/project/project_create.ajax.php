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
	
	private function get_target()
	{
		
	}
	
	private function get_content($page)
	{
		global $session;
		
		switch ($page):

			case "0":						
				$template = new Template("../../../template/projects/new_project_page_0.html");	
				return $template->get_string();
			break;
		
			case "1":
				$template = new Template("../../../template/projects/new_project_page_1.html");	
				
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
						$result[$counter][selected] = "";
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
				
				return $template->get_string();
			break;
		
			case "2":
				$template = new Template("../../../template/projects/new_project_page_2.html");	
			
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

				return $template->get_string();
			break;
			
			case "3":
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
			
				return $template->get_string();
			break;
			
			case "4":
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
						$template = new Template("../../template/projects/new_project_page_4_value.html");
						
						$value_obj = new Value(null);
						if ($project_template_data_type == "value")
						{
							$value_obj->set_content_array($project_template_data_array);
						}	
						$value_html = $value_obj->get_html_form(null, $value_type_id, null);
						$template->set_var("content",$value_html);
						
						$template->set_var("template_data_type_id", $value_type_id);
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
				
				$template->set_var("name", $session->read_value("PROJECT_NAME"));
				$template->set_var("template", $project_template->get_name());
				$template->set_var("desc", $session->read_value("PROJECT_DESC"));
				
				$template->set_var("content","");
				
				return $template->get_string();		
			break;
			
			default:
				return "Error: The requested page does not exist!";
			break;
			
		endswitch;

	}

	private function set_data($page, $data)
	{
		
	}
	
	private function run()
	{
		
	}
	
	public function handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET['run']):
			
				case "get_target":
				
				break;
			
				case "get_content":
					echo $this->get_content($_GET['page']);
				break;
				
				case "set_data":
					echo $this->set_data($_POST['page'], $_POST['data']);
				break;
				
			endswitch;
		}
	}
}

$project_create_ajax = new ProjectCreateAjax();
$project_create_ajax->handler();

?>