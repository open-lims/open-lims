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
 * Project Log AJAX IO Class
 * @package project
 */
class ProjectLogAjax
{	
	/**
	 * @param string $get_array
	 * @return string
	 */
	public static function create($get_array)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			$project = new Project($_GET['project_id']);

			$template = new HTMLTemplate("project/log_create_window.html");
			$array['content_caption'] = "Create New Log Entry";
			$array['height'] = 430;
			$array['width'] = 400;

			$array['continue_caption'] = "Create";
			$array['cancel_caption'] = "Cancel";
			$array['content'] = $template->get_string();
			$array['container'] = "#ProjectLogCreateWindow";
			
			$continue_handler_template = new JSTemplate("project/js/log_create.js");
			$continue_handler_template->set_var("session_id", $_GET['session_id']);
			$continue_handler_template->set_var("get_array", $get_array);
			
			$array['continue_handler'] = $continue_handler_template->get_string();
			
			return json_encode($array);
		}
	}
	
	/**
	 * @param array $get_array
	 * @param string $comment
	 * @param string $important
	 * @return string
	 */
	public static function create_handler($get_array, $comment, $important)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			$project_log = new ProjectLog(null);
			$project_log->create($_GET['project_id'], $comment);
		}
	}
}