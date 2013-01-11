<?php
/**
 * @package job
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
 * Job Ajax Class
 * @package job
 */
class JobAjax
{
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_jobs($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$list_request = new ListRequest_IO();
		$list_request->set_column_array($json_column_array);
	
		if (!is_numeric($entries_per_page) or $entries_per_page < 1)
		{
			$entries_per_page = 20;
		}
		
		$list_array = Job_Wrapper::list_jobs(null, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));

		if (is_array($list_array) and count($list_array) >= 1)
		{
			foreach($list_array as $key => $value)
			{
				$datetime_handler = new DatetimeHandler($list_array[$key]['created_at']);
				$list_array[$key]['created_at'] = $datetime_handler->get_datetime(false);
				
				$user = new User($list_array[$key]['user_id']);
				$list_array[$key]['user'] = $user->get_full_name(true);
				
				switch($list_array[$key]['status']):
					case "0":
						$list_array[$key]['symbol'] = "<img src='images/icons/job_queue.png' alt='' />";
						$list_array[$key]['status'] = "created";
					break;
					
					case "1":
						$list_array[$key]['symbol'] = "<img src='images/icons/job_queue.png' alt='' />";
						$list_array[$key]['status'] = "in queue";
					break;
					
					case "2":
						$list_array[$key]['symbol'] = "<img src='images/icons/job_running.png' alt='' />";
						$list_array[$key]['status'] = "running";
					break;
					
					case "3":
						$list_array[$key]['symbol'] = "<img src='images/icons/job_finished.png' alt='' />";
						$list_array[$key]['status'] = "finished";
					break;
					
					case "4":
						$list_array[$key]['symbol'] = "<img src='images/icons/job_error.png' alt='' />";
						$list_array[$key]['status'] = "error";
					break;
					
					default:
						$list_array[$key]['symbol'] = "<img src='images/icons/job_unknown.png' alt='' />";
						$list_array[$key]['status'] = "unknow status";
					break;
				endswitch;
			}
		}
		else
		{
			$list_request->empty_message("<span class='italic'>No jobs at the moment</span>");
		}

		$list_request->set_array($list_array);
		
		return $list_request->get_page($page);
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_jobs($json_argument_array)
	{
		return Job_Wrapper::count_jobs(null);
	}

	/**
	 * @return string
	 */
	public static function start_test()
	{
		$template = new HTMLTemplate("job/start_test_window.html");
		$array['content_caption'] = "Start Test Job(s)";
		$array['height'] = 200;
		$array['width'] = 400;

		$array['continue_caption'] = "Start";
		$array['cancel_caption'] = "Cancel";
		$array['content'] = $template->get_string();
		$array['container'] = "#JobTestStartWindow";
		
		$continue_handler_template = new JSTemplate("job/js/start_test.js");
		$continue_handler_template->set_var("session_id", $_GET['session_id']);
		
		$array['continue_handler'] = $continue_handler_template->get_string();
		
		return json_encode($array);
	}
	
	/**
	 * @param integer $number_of_jobs
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseJobInvalidArgumentException
	 */
	public static function start_test_handler($number_of_jobs)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($number_of_jobs) and $number_of_jobs >= 1)
			{
				for ($i=1;$i<=$number_of_jobs;$i++)
				{
					$job = new Job(null);
					$job->create(1);
				}
				return "1";
			}
			else
			{
				throw new BaseJobInvalidArgumentException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
}
