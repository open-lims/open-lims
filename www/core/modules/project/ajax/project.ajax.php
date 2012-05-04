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
 * Project AJAX IO Class
 * @package project
 */
class ProjectAjax
{	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_user_related_projects($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$user_id = $argument_array[0][1];
		
		if (is_numeric($user_id))
		{
			$user = new User($user_id);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Project_Wrapper::list_user_related_projects($user_id, $user->is_admin(), $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));

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
					
					if ($list_array[$key][deleted] == "t")
					{
						$list_array[$key][name][content] = "<span class='crossed'>".$list_array[$key][name][content]."</span>";
						$list_array[$key][template][content] = "<span class='crossed'>".$list_array[$key][template][content]."</span>";
						$list_array[$key][status][content] = "<span class='crossed'>".$list_array[$key][status][content]."</span>";
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
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_user_related_projects($json_argument_array)
	{		
		$argument_array = json_decode($json_argument_array);
		
		$user_id = $argument_array[0][1];
		$user = new User($user_id);
		
		if (is_numeric($user_id))
		{
			return Project_Wrapper::count_list_user_related_projects($user_id, $user->is_admin());
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_organisation_unit_related_projects($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		$argument_array = json_decode($json_argument_array);
		
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Project_Wrapper::list_organisation_unit_related_projects($organisation_unit_id, $user->is_admin(), $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
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
					
					if ($list_array[$key][deleted] == "t")
					{
						$list_array[$key][name][content] = "<span class='crossed'>".$list_array[$key][name][content]."</span>";
						$list_array[$key][template][content] = "<span class='crossed'>".$list_array[$key][template][content]."</span>";
						$list_array[$key][status][content] = "<span class='crossed'>".$list_array[$key][status][content]."</span>";
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
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_organisation_unit_related_projects($json_argument_array)
	{
		global $user;
		
		$argument_array = json_decode($json_argument_array);
		
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{
			return Project_Wrapper::count_organisation_unit_related_projects($organisation_unit_id, $user->is_admin());
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_projects_by_item_id($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
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
				$list_array = Project_Wrapper::list_projects_by_item_id($item_id, $user->is_admin(), $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			}
			else
			{
				$list_array = Project_Wrapper::list_projects_by_item_id($item_id, $user->is_admin(), $sortvalue, $sortmethod, 0, null);
			}
			
			$list_request->set_column_array($json_column_array);
						
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
					
					if ($list_array[$key][deleted] == "t")
					{
						$list_array[$key][name][content] = "<span class='crossed'>".$list_array[$key][name][content]."</span>";
						$list_array[$key][template][content] = "<span class='crossed'>".$list_array[$key][template][content]."</span>";
						$list_array[$key][status][content] = "<span class='crossed'>".$list_array[$key][status][content]."</span>";
					}
					
					if ($argument_array[1][1] == true)
					{
						$column_array = json_decode($json_column_array);
						if (is_array($column_array) and count($column_array) >= 1)
						{
							foreach ($column_array as $row_key => $row_value)
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
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_projects_by_item_id($json_argument_array)
	{
		global $user;
		
		$argument_array = json_decode($json_argument_array);
		$item_id = $argument_array[0][1];
		
		if (is_numeric($item_id))
		{
			return Project_Wrapper::count_projects_by_item_id($item_id, $user->is_admin());
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $get_array
	 * @return string
	 */
	public static function get_project_status_bar($get_array)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET[project_id])
		{
			$project = new Project($_GET[project_id]);
			
			$template = new HTMLTemplate("project/ajax/detail_status.html");
		
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
			
			$template->output();
		}
	}
	
	/**
	 * @param string $get_array
	 * @return string
	 */
	public static function get_project_menu($get_array)
	{
		global $project_security;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET[project_id])
		{
			$project = new Project($_GET[project_id]);
			
			$folder_id = ProjectStatusFolder::get_folder_by_project_id_and_project_status_id($_GET[project_id],$project->get_current_status_id());
			
			$template = new HTMLTemplate("project/ajax/detail_menu.html");
			
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
					if ($project_security->is_access(3, false) == true)
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
					if ($project_security->is_access(3, false) == true)
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
			
			$template->set_var("next_status_name",$project->get_next_status_name());
			
			
			if ($project_security->is_access(3, false) == true)
			{
				// Status Buttons
				
				$project_template = new ProjectTemplate($project->get_template_id());
				$current_status_requirements = $project->get_current_status_requirements($project->get_current_status_id());
				
				$result = array();
				$counter = 0;
				
				if (is_array($current_status_requirements) and count($current_status_requirements) >= 1)
				{
					foreach($current_status_requirements as $key => $value)
					{
						switch ($value['element_type']):
						
							case "item":
								$amount = count($value[fulfilled]);
								
								if ($value['display'] == true)
								{
									$paramquery = array();
									$paramquery[username] = $_GET[username];
									$paramquery[session_id] = $_GET[session_id];
									$paramquery[nav] = "project";
									$paramquery[run] = "item_add";
									$paramquery[project_id] = $_GET[project_id];
									$paramquery[dialog] = $value[type];
									$paramquery[key] = $value['pos_id'];
									$paramquery[retrace] = Retrace::create_retrace_string();
									$params = http_build_query($paramquery,'','&#38;');
				
									if ($amount > 1)
									{
										$result[$counter][name] = $value[name]." (".$amount.")";
									}
									else
									{
										$result[$counter][name] = $value[name];
									}
									
									$result[$counter][depends] = false;
									
									$item_handling_cass = $value[handling_class];
									if ($item_handling_cass::get_item_add_type() == 1)
									{										
										$result[$counter][type] = "ajax";
										$ajax_handling_array = $item_handling_cass::get_item_add_script_handling_class();
										require_once("core/modules/".$ajax_handling_array[0]);
										
										$ajax_init_array = $ajax_handling_array[1]::$ajax_handling_array[2]($value['pos_id'], $paramquery, $value[type_id],  $value[category_id], "Project", $_GET['project_id']);
										
										$result[$counter][script] = $ajax_init_array[script];
										$result[$counter][window_title] = $ajax_init_array[window_title];
										$result[$counter][window_id] = $ajax_init_array[window_id];
										$result[$counter][click_id] = $ajax_init_array[click_id];
									}
									else
									{
										$result[$counter][type] = "link";
									}
									
									if (is_array($value[fulfilled]) and $amount >= 1)
									{
										if ($value[occurrence] == "multiple")
										{
											$result[$counter][image] = "add_done";
										}
										else
										{
											$result[$counter][type] = false;
											$result[$counter][image] = "add_done_na";
										}
									}
									else
									{
										$result[$counter][image] = "add";
									}
				
									if ($value[requirement] == "optional")
									{
										$result[$counter][name] = $result[$counter][name]." (optional)";
									}
									
									$result[$counter][params] = $params;					
									
									$counter++;
								}
								
								if (is_array($value['sub_items']) and count($value['sub_items']) >= 1)
								{
									$result[$counter][type] = "line";
									$counter++;
									
									for($i=0; $i<=($amount-1); $i++)
									{
										foreach($value['sub_items'] as $sub_item_key => $sub_item_value)
										{
											if ($sub_item_value['element_type'] == "item")
											{
												$paramquery = array();
												$paramquery['username'] = $_GET[username];
												$paramquery['session_id'] = $_GET[session_id];
												$paramquery['nav'] = "project";
												$paramquery['run'] = "sub_item_add";
												$paramquery['project_id'] = $_GET[project_id];
												$paramquery['dialog'] = $sub_item_value[type];
												$paramquery['key'] = $sub_item_value['pos_id'];
												$paramquery['parent'] = $value[type];
												$paramquery['parent_key'] = $value['pos_id'];
												$paramquery['parent_id'] = $value[fulfilled][$i][id];
												$paramquery['retrace'] = Retrace::create_retrace_string();
												$params = http_build_query($paramquery,'','&#38;');
												
												$item_handling_cass = $sub_item_value[handling_class];
												if ($item_handling_cass::get_item_add_type() == 1)
												{
													$result[$counter][type] = "ajax";
													$ajax_handling_array = $item_handling_cass::get_item_add_script_handling_class();
													require_once("core/modules/".$ajax_handling_array[0]);
													
													$item_holder = Item::get_holder_handling_class_by_name($value[type]);
													$ajax_init_array = $ajax_handling_array[1]::$ajax_handling_array[2]($sub_item_value['pos_id'], $paramquery, $sub_item_value[type_id],  $sub_item_value[category_id], $item_holder, $value[fulfilled][$i][id]);
													
													$result[$counter][script] = $ajax_init_array[script];
													$result[$counter][window_title] = $ajax_init_array[window_title];
													$result[$counter][window_id] = $ajax_init_array[window_id];
													$result[$counter][click_id] = $ajax_init_array[click_id];
												}
												else
												{
													$result[$counter][type] = "link";
												}
												
												if ($value[fulfilled][$i][name])
												{
													$result[$counter][name] = $sub_item_value[name]." (".$value[fulfilled][$i][name].")";
												}
												else
												{
													$result[$counter][name] = $sub_item_value[name];
												}
												
												
												if ($sub_item_value[fulfilled][$i] == true)
												{
													if ($sub_item_value[occurrence] == "multiple")
													{
														$result[$counter][image] = "add_done";
													}
													else
													{
														$result[$counter][type] = false;
														$result[$counter][image] = "add_done_na";
													}
												}
												else
												{
													$result[$counter][image] = "add";
												}
	
												$result[$counter][depends] = true;
												$result[$counter][params] = $params;
	
												$counter++;
											}
										}
										
										$result[$counter][type] = "line";
										$counter++;
									}
								}
							break;
															
							case "extension":
								$paramquery = array();
								$paramquery[username] = $_GET[username];
								$paramquery[session_id] = $_GET[session_id];
								$paramquery[nav] = "project";
								$paramquery[run] = "extension";
								$paramquery[project_id] = $_GET[project_id];
								$paramquery[extension] = $value[extension];
								$paramquery[retrace] = Retrace::create_retrace_string();
								$params = http_build_query($paramquery,'','&#38;');
			
								$result[$counter][name] = "Run ".$value[name];
								$result[$counter][depends] = false;
								$result[$counter][params] = $params;	
								
								if ($value[fulfilled] == 1)
								{
									if ($value[occurrence] == "multiple")
									{
										$result[$counter][type] = "link";
										$result[$counter][image] = "add_extension_done";
									}
									else
									{
										$result[$counter][type] = false;
										$result[$counter][image] = "add_extension_done_na";
									}
								}
								elseif($value[fulfilled] == 0)
								{
									if ($value[occurrence] == "multiple")
									{
										$result[$counter][type] = "link";
										$result[$counter][image] = "add_extension_wait";
									}
									else
									{
										$result[$counter][type] = false;
										$result[$counter][image] = "add_extension_wait_na";
									}
								}
								else
								{
									$result[$counter][type] = "link";
									$result[$counter][image] = "add_extension";
								}
								
								if ($value[requirement] == "optional" and $value[fulfilled] != 0)
								{
									$result[$counter][name] = $result[$counter][name]." (optional)";
								}			
								
								$counter++;
							break;
						
						endswitch;
					}		
				}
				
				if ($counter > 0)
				{
					$template->set_var("status_action",$result);
				}
				else
				{
					$template->set_var("status_action",false);
				}
				
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
	}
	
	/**
	 * @param array $get_array
	 * @return string
	 * @throws ProjectSecurityAccessDeniedException
	 * @throws ProjectIDMissingException
	 */
	public static function get_project_proceed($get_array)
	{
		global $project_security;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(3, false) == true)
			{
				$project = new Project($_GET[project_id]);
				
				if ($project->is_current_status_fulfilled())
				{
					echo "1::;::";
				}
				else
				{
					echo "0::;::";
				}
				
				$template = new HTMLTemplate("project/ajax/proceed.html");
							
				$project_template = new ProjectTemplate($project->get_template_id());
				$current_status_requirements 	= $project->get_current_status_requirements();
				
				$result = array();
				$counter = 0;
				
				if (is_array($current_status_requirements) and count($current_status_requirements) >= 1)
				{
					foreach($current_status_requirements as $key => $value)
					{
						switch ($value['element_type']):
						
							case "item":
								
								$amount = count($value[fulfilled]);
								
								if ($value['display'] == true)
								{
									$result[$counter][name] = $value[name];
									$result[$counter][depends] = false;
									if (is_array($value[fulfilled]) and count($value[fulfilled]) >= 1)
									{
										$result[$counter][status] = "ok";
									}
									else
									{
										if ($value[requirement] != "optional")
										{
											$result[$counter][status] = "cancel";
										}
										else
										{
											$result[$counter][status] = "notice";
										}
									}
									$counter++;
								}
								
								if (is_array($value['sub_items']) and count($value['sub_items']) >= 1)
								{
									$result[$counter][status] = "line";
									$counter++;
									
									for($i=0; $i<=($amount-1); $i++)
									{
										foreach($value['sub_items'] as $sub_item_key => $sub_item_value)
										{
											if ($sub_item_value['element_type'] == "item")
											{
												$result[$counter][depends] = true;
												
												if ($value[fulfilled][$i][name])
												{
													$result[$counter][name] = $sub_item_value[name]." (".$value[fulfilled][$i][name].")";
												}
												else
												{
													$result[$counter][name] = $sub_item_value[name];
												}
												
												if ($sub_item_value[fulfilled][$i] == true)
												{
													$result[$counter][status] = "ok";
												}
												else
												{
													if ($sub_item_value[requirement] != "optional")
													{
														$result[$counter][status] = "cancel";
													}
													else
													{
														$result[$counter][status] = "notice";
													}
												}
												
												$counter++;
											}
										}
										
										$result[$counter][status] = "line";
										$counter++;
									}
								}
							break;
							
							case "extension":
								$result[$counter][name] = $value[name];
								$result[$counter][depends] = false;
								if ($value[fulfilled] == 1)
								{
									$result[$counter][status] = "ok";
								}
								else
								{
									if ($value[requirement] != "optional")
									{
										$result[$counter][status] = "cancel";
									}
									else
									{
										$result[$counter][status] = "notice";
									}
								}
								$counter++;
							break;
							
						endswitch;
					}			
				}
				else
				{
					$result[$counter][icon] = "";
					$result[$counter][name] = "No Requirements";
				}
	
				$template->set_var("status_action",$result);
				
	
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
	 * @param string $get_array
	 * @return string
	 */
	public static function proceed_project($get_array, $comment)
	{
		global $project_security;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET[project_id])
		{
			$project = new Project($_GET[project_id]);
			
			if ($project_security->is_access(3, false) == true)
			{
				if ($comment and $comment != "undefined")
				{
					$project_log = new ProjectLog(null);
					if ($project_log->create($_GET[project_id], $comment) == null)
					{
						throw new ProjectSetNextStatusException();
					}
				}
				
				$project->set_next_status();
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
	}
}
?>