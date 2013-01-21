<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Project Home IO Class
 * @package project
 */
class ProjectHomeIO
{
	public static function running_projects()
	{
		global $user;
		
		$sum_projects = Project_Wrapper::count_user_projects($user->get_user_id());
		$sum_running_projects = Project_Wrapper::count_user_running_projects($user->get_user_id());
		
		$template = new HTMLTemplate("project/home/summary/my_running_projects.html");
		$template->set_var("running_projects",$sum_running_projects."/".$sum_projects);
		return $template->get_string();
	}
	
	public static function finished_projects()
	{
		global $user;
		
		$sum_projects = Project_Wrapper::count_user_projects($user->get_user_id());
		$sum_finished_projects = Project_Wrapper::count_user_finished_projects($user->get_user_id());
		
		$template = new HTMLTemplate("project/home/summary/my_finished_projects.html");
		$template->set_var("finished_projects",$sum_finished_projects."/".$sum_projects);
		return $template->get_string();
	}
}
?>