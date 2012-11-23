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
 * Project Task AJAX IO Class
 * @package project
 */
class ProjectTaskAjax
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
	 * @throws ProjectIDMissingException
	 */
	public static function list_project_tasks($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$project_id = $argument_array[0][1];
		
		if (is_numeric($project_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Project_Wrapper::list_project_tasks($project_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));

			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$start_date = new DatetimeHandler($list_array[$key]['start_date']);
					$end_date = new DatetimeHandler($list_array[$key]['end_date']." ".$list_array[$key]['end_time']);
					
					$list_array[$key]['start_date'] = $start_date->get_formatted_string("jS M Y");
					$list_array[$key]['end_date'] = $end_date->get_formatted_string("jS M Y");
					$list_array[$key]['end_time'] = $end_date->get_formatted_string("H:i");
					
					if ($list_array[$key]['whole_day'] == "t")
					{
						$list_array[$key]['end_time'] = "whole day";
					}
					
					if ($list_array[$key]['is_status_process'] or $list_array[$key]['is_process'])
					{
						$project_task = new ProjectTask($list_array[$key]['id']);
					}
									
					if ($list_array[$key]['is_status_process'])
					{
						$list_array[$key]['type'] = "Status Rel. Task";
						$list_array[$key]['name'] =  $project_task->get_name();
					}
					
					if ($list_array[$key]['is_process'])
					{
						$list_array[$key]['type'] = "Task";
					}
					
					if ($list_array[$key]['is_milestone'])
					{
						$list_array[$key]['type'] = "Milestone";
					}
					else
					{
						$list_array[$key]['progress'] = "<img src='core/images/status_bar.php?length=100&height=15&linecolor=A0A0A0&color=".$project_task->get_color()."&value=".$project_task->get_progress()."' />";
					}
					
					$paramquery = array();
					$paramquery['session_id'] = $_GET['session_id'];
					$paramquery['username'] = $_GET['username'];
					$paramquery['nav'] = "project";
					$paramquery['run'] = "task_detail";
					$paramquery['id'] = $list_array[$key]['id'];
					$params = http_build_query($paramquery,'','&#38;');
					
					$tmp_name = $list_array[$key]['name'];
					unset($list_array[$key]['name']);
					$list_array[$key]['name']['content'] = $tmp_name;
					$list_array[$key]['name']['link'] = $params;
				}
				
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No Project tasks found!</span>");
			}

			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws ProjectIDMissingException
	 */
	public static function count_project_tasks($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$project_id = $argument_array[0][1];
		
		if (is_numeric($project_id))
		{
			return Project_Wrapper::count_project_tasks($project_id);
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
}
?>