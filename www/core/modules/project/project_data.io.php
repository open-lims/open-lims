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
 * Data IO Class
 * @package project
 */
class ProjectDataIO
{
	public static function get_used_project_space()
	{
		return Misc::calc_size(Project::get_used_project_space());
	}
	
	/**
	 * @param integer $user_id
	 */
	public static function get_user_module_detail_setting($user_id)
	{
		if ($user_id)
		{
			$project_user_data = new ProjectUserData($user_id);
			
			$paramquery = $_GET;
			$paramquery[run] = "module_value_change";
			$paramquery[dialog] = "project_quota";
			$paramquery[retrace] = Misc::create_retrace_string();
			$params = http_build_query($paramquery, '', '&#38;');
			
			$return_array = array();
			$return_array[value] = Misc::calc_size($project_user_data->get_quota());
			$return_array[params] = $params;
			return $return_array;	
		}
		else
		{
			return null;
		}
	}
	
	public static function change()
	{
		if ($_GET[id])
		{
			$user = new User($_GET[id]);
			$project_data = new ProjectUserData($_GET[id]);
						
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[quota]))
				{
					$page_1_passed = true;
				}
				else
				{
					$page_1_passed = false;
					$error = "You must enter a valid quota.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new Template("template/projects/admin/user/change_project_quota.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				$template->set_var("error",$error);
				
				if ($_POST[quota])
				{
					$template->set_var("mail", $_POST[quota]);
				}
				else
				{
					$template->set_var("quota", $project_data->get_quota());	
				}
				$template->output();
			}
			else
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
												
				if ($project_data->set_quota($_POST[quota]))
				{
					Common_IO::step_proceed($params, "Change Project Quota", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Change Project Quota", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			// $error_io = new Error_IO($exception, 3, 40, 3);
			// $error_io->display_error();
		}
	}
}
	
?>