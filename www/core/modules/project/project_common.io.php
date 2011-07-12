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
		if ($_GET[project_id])
		{
			$tab_io = new Tab_IO();
						
			// Main Page
			
			$paramquery[username] 	= $_GET[username];
			$paramquery[session_id] = $_GET[session_id];
			$paramquery[nav]		= "project";
			$paramquery[run]		= "detail";
			$paramquery[project_id]	= $_GET[project_id];
			$params 				= http_build_query($paramquery,'','&#38;');
			unset($paramquery);
			
			$tab_io->add("main", "Main Page", $params, false);
			
			// Log
			
			$paramquery[username] 	= $_GET[username];
			$paramquery[session_id] = $_GET[session_id];
			$paramquery[nav]		= "project";
			$paramquery[run]		= "log";
			$paramquery[project_id]	= $_GET[project_id];
			$params 				= http_build_query($paramquery,'','&#38;');
			unset($paramquery);
			
			$tab_io->add("log", "Log", $params, false);
			
			
			// Structure
			
			$paramquery[username] 	= $_GET[username];
			$paramquery[session_id] = $_GET[session_id];
			$paramquery[nav]		= "project";
			$paramquery[run]		= "structure";
			$paramquery[project_id]	= $_GET[project_id];
			$params 				= http_build_query($paramquery,'','&#38;');
			unset($paramquery);
			
			$tab_io->add("structure", "Structure", $params, false);
			
			
			// Schedule
			
			$paramquery[username] 	= $_GET[username];
			$paramquery[session_id] = $_GET[session_id];
			$paramquery[nav]		= "project";
			$paramquery[run]		= "schedule";
			$paramquery[project_id]	= $_GET[project_id];
			$params 				= http_build_query($paramquery,'','&#38;');
			unset($paramquery);
			
			$tab_io->add("schedule", "Schedule", $params, false);
			
			
			// Item Lister Dialogs
			
			$module_dialog_array = ModuleDialog::list_dialogs_by_type("item_list");
			
			if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
			{
				foreach ($module_dialog_array as $key => $value)
				{
					$paramquery[username] 	= $_GET[username];
					$paramquery[session_id] = $_GET[session_id];
					$paramquery[nav]		= "project";
					$paramquery[run]		= "item_list";
					$paramquery[project_id]	= $_GET[project_id];
					$paramquery[dialog]		= $value[internal_name];
					$params 				= http_build_query($paramquery,'','&#38;');
					
					$tab_io->add($value[internal_name], $value[display_name], $params, false);
				}
			}
			
			
			// Administration
			
			
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
				$paramquery[nav]		= "project";
				$paramquery[run]		= "admin";
				$paramquery[project_id]	= $_GET[project_id];
				unset($paramquery[dialog]);
				$params 				= http_build_query($paramquery,'','&#38;');
			 	unset($paramquery);
			 
				$tab_io->add("admin", "Administrat.", $params, false, false);
			}
			else
			{
				$tab_io->add("admin", "Administrat.", $params, false, true);
			}
		
			if ($_GET[run] != "item_add" and $_GET[run] != "item_list")
			{
				switch ($_GET[run]):
				
					case "log":
						$tab_io->activate("log");
					break;
					
					case "structure":
						$tab_io->activate("structure");
					break;
					
					case "add_task":
					case "task_delete";
					case "task_detail":
					case "schedule":
					case "show_tasks":
					case "task_edit_start":
					case "task_edit_end":
						$tab_io->activate("schedule");
					break;
					
					
					case "admin":
					case "set_permissions":
					case "add_permissions":
					case "edit_permissions":
					case "delete_permission":
					case "deleteproject":
					case "cancel":
					case "reactivate":
						$tab_io->activate("admin");
					break;
					
					default:
						$tab_io->activate("main");
					break;
				
				endswitch;
			}
			else
			{
				if ($_GET[run] == "item_list" and $_GET[dialog])
				{
					$tab_io->activate($_GET[dialog]);
				}
				else
				{
					$tab_io->activate("main");
				}
			}
			
			$tab_io->output();
		}
	}

}

?>
