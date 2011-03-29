<?php
/**
 * @package base
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
 * Left Navigation IO Class
 * @package base
 */
class LeftNavigation_IO
{
	private static function administration()
	{
		$template = new Template("languages/en-gb/template/navigation/left/administration.html");
		
		
		$paramquery[username] = $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] = "admin";
		$paramquery[run] = "user";
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("user_params", $params);
		
		
		$paramquery[username] = $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] = "admin";
		$paramquery[run] = "group";
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("group_params", $params);
		
		
		$paramquery[username] = $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] = "admin";
		$paramquery[run] = "organisation_unit";
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("organisation_unit_params", $params);

		
		$paramquery[username] = $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] = "admin";
		$paramquery[run] = "system_log";
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("system_log_params", $params);
		
		
		$paramquery[username] = $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] = "admin";
		$paramquery[run] = "system_message";
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("system_message_params", $params);
		
		
		$module_admin_navigation_array = array();
		$counter = 0;
		
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("admin");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			foreach ($module_dialog_array as $key => $value)
			{
				$paramquery[username] 	= $_GET[username];
				$paramquery[session_id] = $_GET[session_id];
				$paramquery[nav]		= "admin";
				$paramquery[run]		= "module";
				$paramquery[dialog]		= $value[internal_name];
				$params 				= http_build_query($paramquery,'','&#38;');
				
				/**
				 * @todo icon
				 */
				$module_admin_navigation_array[$counter][icon] = "equipment.png";
				$module_admin_navigation_array[$counter][params] = $params;
				$module_admin_navigation_array[$counter][title] = $value[display_name];
				$counter++;
			}
		}
		
		$template->set_var("module_admin", $module_admin_navigation_array);
		
		$template->output();
	}
	
	private static function folder()
	{
		$template = new Template("languages/en-gb/template/navigation/left/folder.html");
		$template->output();
	}
	
	private static function organisation_unit()
	{
		$template = new Template("languages/en-gb/template/navigation/left/organisation_unit.html");
		$template->output();
	}
	
	private static function project()
	{
		$template = new Template("languages/en-gb/template/navigation/left/project.html");
		$template->output();
	}
	
	public static function handler()
	{
		global $user, $session;
		
		if ($_GET[nav] != "admin" or !$user->is_admin())
		{
			if ($session->is_value("CURRENT_NAVIGATION"))
			{
				$current_navigation = $session->read_value("CURRENT_NAVIGATION");
				if ($_GET[change_nav])
				{
					if ($_GET[change_nav] != $current_navigation)
					{
						switch($_GET[change_nav]):
						
							case "FOLDER":
								$session->write_value("CURRENT_NAVIGATION", "FOLDER", true);
								$current_navigation = "FOLDER";
							break;
							
							case "ORGANISATION_UNIT":
								$session->write_value("CURRENT_NAVIGATION", "ORGANISATION_UNIT", true);
								$current_navigation = "ORGANISATION_UNIT";
							break;
							
							case "PROJECT":
								$session->write_value("CURRENT_NAVIGATION", "PROJECT", true);
								$current_navigation = "PROJECT";
							break;
						
						endswitch;
					}
					unset($_GET[change_nav]);
				}
			}
			else
			{
				$session->write_value("CURRENT_NAVIGATION", "ORGANISATION_UNIT", true);
				$current_navigation = "ORGANISATION_UNIT";
			}
			
			$template = new Template("languages/en-gb/template/navigation/left/header.html");
			
			if ($session->is_value("CURRENT_IN_PROJECT"))
			{
				if ($session->read_value("CURRENT_IN_PROJECT") == true)
				{
					$template->set_var("project_enabled", true);	
				}
				else
				{
					$template->set_var("project_enabled", false);
				}				
			}
			else
			{
				$template->set_var("project_enabled", false);
			}
			
			if ($current_navigation == "FOLDER")
			{
				$template->set_var("folder_li_class", "class='active'");
				$template->set_var("title", "Folder");
			}
			else
			{
				$template->set_var("folder_li_class", "");	
			}
			
			if ($current_navigation == "ORGANISATION_UNIT")
			{
				$template->set_var("organisation_unit_li_class", "class='active'");
				$template->set_var("title", "Organisation Unit");
			}
			else
			{
				$template->set_var("organisation_unit_li_class", "");	
			}
			
			if ($current_navigation == "PROJECT")
			{
				$template->set_var("project_li_class", "class='active'");
				$template->set_var("title", "Project");
			}
			else
			{
				$template->set_var("project_li_class", "");	
			}
			
			$folder_paramquery = $_GET;
			$folder_paramquery[change_nav] = "FOLDER";
			$folder_params = http_build_query($folder_paramquery, '', '&#38;');
			$template->set_var("folder_params", $folder_params);
			
			$organisation_unit_paramquery = $_GET;
			$organisation_unit_paramquery[change_nav] = "ORGANISATION_UNIT";
			$organisation_unit_params = http_build_query($organisation_unit_paramquery, '', '&#38;');
			$template->set_var("organisation_unit_params", $organisation_unit_params);
			
			$project_paramquery = $_GET;
			$project_paramquery[change_nav] = "PROJECT";
			$project_params = http_build_query($project_paramquery, '', '&#38;');
			$template->set_var("project_params", $project_params);
			
			$template->output();
			
			switch ($current_navigation):
			
				case "FOLDER":
					self::folder();
				break;
				
				case "ORGANISATION_UNIT":
					self::organisation_unit();
				break;
				
				case "PROJECT":
					self::project();
				break;
				
				default:
					self::organisation_unit();
				break;
			
			endswitch;
			
			$template = new Template("languages/en-gb/template/navigation/left/footer.html");
			$template->output();
		}
		else
		{
			self::administration();
		}	
	}
	
}

?>
