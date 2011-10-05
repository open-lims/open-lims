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
 * Project AJAX IO Class
 * @package project
 */
class ProjectAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	private function list_projects_by_item_id($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		$item_id = $argument_array[0][1];
		
		if (is_numeric($item_id))
		{
			$list_request = new ListRequest_IO();
			
			if ($argument_array[2][1] == true)
			{	
				$list_array = Project_Wrapper::list_projects_by_item_id($item_id, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
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
					$tmp_name = $list_array[$key][name];
					unset($list_array[$key][name]);
					
					if (strlen($tmp_name) > 35)
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = substr($tmp_name,0,35)."...";
					}
					else
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = $tmp_name;
					}

					$tmp_template = $list_array[$key][template];
					unset($list_array[$key][template]);
					
					if (strlen($list_array[$key][template]) > 25)
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = substr($tmp_template,0,25)."...";
					}
					else
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = $tmp_template;
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
							$list_array[$key][checkbox] = "<input type='checkbox' name='parent-project-".$list_array[$key][id]."' value='1' class='".$checkbox_class."' checked='checked' />";
						}
						else
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='parent-project-".$list_array[$key][id]."' value='1' checked='checked' />";
						}
						
						$list_array[$key][symbol] 	= "<img src='images/icons/project.png' alt='' style='border:0;' />";
					}
					else
					{
						$project_id = $list_array[$key][id];
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
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
				
				case "list_projects_by_item_id":
					echo $this->list_projects_by_item_id($_POST[row_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				default:
				break;
			
			endswitch;
		}
	}
}

$project_ajax = new ProjectAjax;
$project_ajax->method_handler();
?>