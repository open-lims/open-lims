<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * Project Common IO Class
 * @package project
 */
class ProjectCommon_IO
{
	public static function tab_header()
	{
		$template = new Template("languages/en-gb/template/projects/tabs/small_tab_header.html");
		$template->output();
		
		if ($_GET[nav] == "projects")
		{
			switch ($_GET[run]):
			
				case "log":
					$current_tab = 2;
				break;
				
				case "structure":
					$current_tab = 3;
				break;
				
				case "add_task":
				case "task_delete";
				case "task_detail":
				case "schedule":
				case "show_tasks":
				case "task_edit_start":
				case "task_edit_end":
					$current_tab = 4;
				break;
				
				case "samples":
					$current_tab = 5;
				break;
				
				case "admin":
				case "set_permissions":
				case "add_permissions":
				case "edit_permissions":
				case "delete_permission":
				case "deleteproject":
				case "cancel":
				case "reactivate":
					$current_tab = 8;
				break;
				
				default:
					$current_tab = 1;
				break;
			
			endswitch;
		}
		elseif($_GET[nav] == "method")
		{
			$current_tab = 6;
		}
		elseif($_GET[nav] == "samples")
		{
			
		}
		elseif ($_GET[nav] == "data" or
				 $_GET[nav] == "value" or
				 $_GET[nav] == "file" or
				 $_GET[nav] == "folder")
		{
			$current_tab = 7;	
		}
		else
		{
			$current_tab = 0;
		}

		
		// Main Page
		
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "projects";
		$paramquery[run]		= "detail";
		$paramquery[project_id]	= $_GET[project_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 1)
		{ 
			$template = new Template("languages/en-gb/template/projects/tabs/main_active.html");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/projects/tabs/main.html");
			$template->set_var("params", $params);
			$template->output();
		}
		
		
		// Log
		
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "projects";
		$paramquery[run]		= "log";
		$paramquery[project_id]	= $_GET[project_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 2)
		{ 
			$template = new Template("languages/en-gb/template/projects/tabs/log_active.html");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/projects/tabs/log.html");
			$template->set_var("params", $params);
			$template->output();
		}
		
		
		// Structure
		
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "projects";
		$paramquery[run]		= "structure";
		$paramquery[project_id]	= $_GET[project_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 3)
		{ 
			$template = new Template("languages/en-gb/template/projects/tabs/structure_active.html");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/projects/tabs/structure.html");
			$template->set_var("params", $params);
			$template->output();
		}
		
		
		// Schedule
		
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "projects";
		$paramquery[run]		= "schedule";
		$paramquery[project_id]	= $_GET[project_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 4)
		{ 
			$template = new Template("languages/en-gb/template/projects/tabs/schedule_active.html");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/projects/tabs/schedule.html");
			$template->set_var("params", $params);
			$template->output();
		}
		
		
		// Samples
		
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "projects";
		$paramquery[run]		= "samples";
		$paramquery[project_id]	= $_GET[project_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 5)
		{ 
			$template = new Template("languages/en-gb/template/projects/tabs/samples_active.html");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/projects/tabs/samples.html");
			$template->set_var("params", $params);
			$template->output();
		}
		
		
		// Methods
		
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "method";
		$paramquery[run]		= "project_related_methods";
		$paramquery[project_id]	= $_GET[project_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 6)
		{ 
			$template = new Template("languages/en-gb/template/projects/tabs/methods_active.html");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/projects/tabs/methods.html");
			$template->set_var("params", $params);
			$template->output();
		}
		
		
		// Data
		
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "data";
		$paramquery[run]		= "project_folder";
		$paramquery[project_id]	= $_GET[project_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 7)
		{ 
			$template = new Template("languages/en-gb/template/projects/tabs/data_active.html");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/projects/tabs/data.html");
			$template->set_var("params", $params);
			$template->output();
		}
		
		
		// Administration
		
		if ($_GET[project_id])
		{
			$project_security = new ProjectSecurity($_GET[project_id]);	

			if ($project_security->is_access(2,false) or 
				$project_security->is_access(3,false) or 
				$project_security->is_access(4,false) or 
				$project_security->is_access(5,false) or 
				$project_security->is_access(6,false) or 
				$project_security->is_access(7,false))
			{
				$paramquery[username] 	= $_GET[username];
				$paramquery[session_id] = $_GET[session_id];
				$paramquery[nav]		= "projects";
				$paramquery[run]		= "admin";
				$paramquery[project_id]	= $_GET[project_id];
				$params 				= http_build_query($paramquery,'','&#38;');
			 	unset($paramquery);
			 
				if ($current_tab == 8)
				{ 
					$template = new Template("languages/en-gb/template/projects/tabs/administration_active.html");
					$template->set_var("params", $params);
					$template->output();
				}
				else
				{
					$template = new Template("languages/en-gb/template/projects/tabs/administration.html");
					$template->set_var("params", $params);
					$template->output();
				}
			}
			else
			{
				$template = new Template("languages/en-gb/template/projects/tabs/administration_inactive.html");
				$template->output();
			}
		}
		else
		{
			$template = new Template("languages/en-gb/template/projects/tabs/administration_inactive.html");
			$template->output();
		}
		$template = new Template("languages/en-gb/template/projects/tabs/small_tab_footer.html");
		$template->output();
	}

}

?>
