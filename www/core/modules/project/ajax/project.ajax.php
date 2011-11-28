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
 * Project AJAX IO Class
 * @package project
 */
class ProjectAjax
{	
	public static function list_user_related_projects($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$user_id = $argument_array[0][1];
		
		if (is_numeric($user_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_row_array($json_row_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Project_Wrapper::list_user_related_projects($user_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));

			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$tmp_name = trim($list_array[$key][name]);
					unset($list_array[$key][name]);
					
					if (strlen($tmp_name) > 28)
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = substr($tmp_name,0,28)."...";
					}
					else
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = $tmp_name;
					}

					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					
					if (strlen($tmp_template) > 20)
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = substr($tmp_template,0,20)."...";
					}
					else
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = $tmp_template;
					}
					
					$tmp_status= trim($list_array[$key][status]);
					unset($list_array[$key][status]);
					
					if (strlen($tmp_status) > 15)
					{
						$list_array[$key][status][label] = $tmp_status;
						$list_array[$key][status][content] = substr($tmp_status,0,15)."...";
					}
					else
					{
						$list_array[$key][status][label] = $tmp_status;
						$list_array[$key][status][content] = $tmp_status;
					}
					
					$list_array[$key][symbol] = "<img src='images/icons/project.png' alt='N' border='0' />";
					
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
					$proejct_paramquery = array();
					$project_paramquery[username] = $_GET[username];
					$project_paramquery[session_id] = $_GET[session_id];
					$project_paramquery[nav] = "project";
					$project_paramquery[run] = "detail";
					$project_paramquery[project_id] = $value[id];
					$project_params = http_build_query($project_paramquery, '', '&#38;');
					
					$list_array[$key][name][link] = $project_params;
				}
				
			}
			else
			{
				$list_request->empty_message("<span class='italic'>You have no Projects at the moment!</span>");
			}

			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
	}
	
	public static function count_user_related_projects($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$user_id = $argument_array[0][1];
		
		if (is_numeric($user_id))
		{
			return Project_Wrapper::count_list_user_related_projects($user_id);
		}
		else
		{
			return null;
		}
	}
	
	public static function list_organisation_unit_related_projects($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_row_array($json_row_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Project_Wrapper::list_organisation_unit_related_projects($organisation_unit_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
				$tmp_name = trim($list_array[$key][name]);
					unset($list_array[$key][name]);
					
					if (strlen($tmp_name) > 28)
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = substr($tmp_name,0,28)."...";
					}
					else
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = $tmp_name;
					}

					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					
					if (strlen($tmp_template) > 20)
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = substr($tmp_template,0,20)."...";
					}
					else
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = $tmp_template;
					}
					
					$tmp_status= trim($list_array[$key][status]);
					unset($list_array[$key][status]);
					
					if (strlen($tmp_status) > 15)
					{
						$list_array[$key][status][label] = $tmp_status;
						$list_array[$key][status][content] = substr($tmp_status,0,15)."...";
					}
					else
					{
						$list_array[$key][status][label] = $tmp_status;
						$list_array[$key][status][content] = $tmp_status;
					}
					
					$list_array[$key][symbol] = "<img src='images/icons/project.png' alt='N' border='0' />";
					
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
					$proejct_paramquery = array();
					$project_paramquery[username] = $_GET[username];
					$project_paramquery[session_id] = $_GET[session_id];
					$project_paramquery[nav] = "project";
					$project_paramquery[run] = "detail";
					$project_paramquery[project_id] = $value[id];
					$project_params = http_build_query($project_paramquery, '', '&#38;');
					
					$list_array[$key][name][link] = $project_params;
					
					if ($list_array[$key][owner_id])
					{
						$user = new User($list_array[$key][owner_id]);
					}
					else
					{
						$user = new User(1);
					}
					
					$list_array[$key][owner] = $user->get_full_name(true);
				}
				
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No Projects found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			// Error
		}
	}
	
	public static function count_organisation_unit_related_projects($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{
			return Project_Wrapper::count_organisation_unit_related_projects($organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
	
	public static function list_projects_by_item_id($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		$item_id = $argument_array[0][1];
		
		if (is_numeric($item_id))
		{
			$list_request = new ListRequest_IO();
			
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			if ($argument_array[2][1] == true)
			{	
				$list_array = Project_Wrapper::list_projects_by_item_id($item_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			}
			else
			{
				$list_array = Project_Wrapper::list_projects_by_item_id($item_id, $sortvalue, $sortmethod, 0, null);
			}
			
			$list_request->set_row_array($json_row_array);
						
			if (is_array($list_array) and count($list_array) >= 1)
			{				
				foreach($list_array as $key => $value)
				{
					$tmp_name = trim($list_array[$key][name]);
					unset($list_array[$key][name]);
					
					if (strlen($tmp_name) > 28)
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = substr($tmp_name,0,28)."...";
					}
					else
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = $tmp_name;
					}

					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					
					if (strlen($tmp_template) > 20)
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = substr($tmp_template,0,20)."...";
					}
					else
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = $tmp_template;
					}
					
					$tmp_status= trim($list_array[$key][status]);
					unset($list_array[$key][status]);
					
					if (strlen($tmp_status) > 15)
					{
						$list_array[$key][status][label] = $tmp_status;
						$list_array[$key][status][content] = substr($tmp_status,0,15)."...";
					}
					else
					{
						$list_array[$key][status][label] = $tmp_status;
						$list_array[$key][status][content] = $tmp_status;
					}
					
					if ($argument_array[1][1] == true)
					{
						$row_array = json_decode($json_row_array);
						if (is_array($row_array) and count($row_array) >= 1)
						{
							foreach ($row_array as $row_key => $row_value)
							{
								if ($row_value[1] == "checkbox")
								{
									if ($row_value[4])
									{
										$checkbox_class = $row_value[4];
										break;
									}
								}
							}
						}
						
						if ($checkbox_class)
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='parent-project-".$list_array[$key][id]."' value='1' class='".$checkbox_class."' />";
						}
						else
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='parent-project-".$list_array[$key][id]."' value='1' />";
						}
						
						$list_array[$key][symbol] 	= "<img src='images/icons/project.png' alt='' style='border:0;' />";
					}
					else
					{
						$project_id = $list_array[$key][id];
						$project_security = new ProjectSecurity($project_id);
						
						if ($project_security->is_access(1, false))
						{
							$paramquery = array();
							$paramquery[username] = $_GET[username];
							$paramquery[session_id] = $_GET[session_id];
							$paramquery[nav] = "project";
							$paramquery[run] = "detail";
							$paramquery[project_id] = $project_id;
							$params = http_build_query($paramquery,'','&#38;');
							
							$list_array[$key][symbol][link]		= $params;
							$list_array[$key][symbol][content] 	= "<img src='images/icons/project.png' alt='' style='border:0;' />";
						
							$list_array[$key][name][link] 		= $params;
						}
						else
						{
							$list_array[$key][symbol]	= "<img src='core/images/denied_overlay.php?image=images/icons/project.png' alt='N' border='0' />";
						}
					}
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");
				
					if ($list_array[$key][owner])
					{
						$user = new User($list_array[$key][owner]);
					}
					else
					{
						$user = new User(1);
					}
					
					$list_array[$key][owner] = $user->get_full_name(true);
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No Projects found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			// Error
		}
	}
	
	public static function count_projects_by_item_id($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		$item_id = $argument_array[0][1];
		
		if (is_numeric($item_id))
		{
			return Project_Wrapper::count_projects_by_item_id($item_id);
		}
		else
		{
			return null;
		}
	}
}
?>