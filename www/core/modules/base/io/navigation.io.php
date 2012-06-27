<?php
/**
 * @package base
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
 * Navigation IO Class
 * @package base
 */
class Navigation_IO
{
	public static function main()
	{
		global $user;
		
		// Tabs
		
		$template = new HTMLTemplate("base/navigation/main/main_navigation_header.html");
		$template->output();

		$module_navigation_array = ModuleNavigation::list_module_navigations_entries();
		
		if (is_array($module_navigation_array) and count($module_navigation_array) >= 1)
		{
			$module_tab_string = "";
			$module_tab_active = false;
			
			foreach($module_navigation_array as $key => $value)
			{
				$module_name = SystemHandler::get_module_name_by_module_id($value[module_id]);
				
				if (($module_name == "admin" and $user->is_admin()) or $module_name != "admin")
				{
				
					$paramquery[username] = $_GET[username];
					$paramquery[session_id] = $_GET[session_id];
					
					if ($value[alias])
					{
						$active_alias = $module_name.".".$value[alias];
						$paramquery[nav] = $module_name.".".$value[alias];
					}
					else
					{
						$active_alias = $module_name;
						$paramquery[nav] = $module_name;
					}
					
					$params = http_build_query($paramquery,'','&#38;');
					
					if (!$_GET['nav'] and $active_alias == "base")
					{
						$_GET['nav'] = "base";
					}
					
					switch ($value[colour]):
					
						case "blue":
							if ($_GET[nav] == $active_alias)
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/blue_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value[colour];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/blue_tab.html");
							}
						break;
						
						case "green":
							if ($_GET[nav] == $active_alias)
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/green_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value[colour];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/green_tab.html");
							}
						break;
						
						case "orange";
							if ($_GET[nav] == $active_alias)
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/orange_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value[colour];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/orange_tab.html");
							}
						break;
						
						case "lightgreen":
							if ($_GET[nav] == $active_alias)
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/lightgreen_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value[colour];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/lightgreen_tab.html");
							}
						break;
							
						case "lightblue":
							if ($_GET[nav] == $active_alias)
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/lightblue_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value[colour];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/lightblue_tab.html");
							}
						break;
					
						default:
							if ($_GET[nav] == $active_alias)
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/grey_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value[colour];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/grey_tab.html");
							}
						break;
						
					endswitch;
					
					
					
					$template->set_var("params", $params);
					$template->set_var("title", Language::get_message($value[language_address], "navigation"));
					$module_tab_string .= $template->get_string();
				}
			}
		}
		
		echo $module_tab_string;
				
		$info_paramquery[username] = $_GET[username];
		$info_paramquery[session_id] = $_GET[session_id];
		$info_paramquery[nav] = "base";
		$info_paramquery[run] = "system_info";
		$info_params = http_build_query($info_paramquery,'','&#38;');
		
		$logout_paramquery[username] = $_GET[username];
		$logout_paramquery[session_id] = $_GET[session_id];
		$logout_paramquery[run] = "logout";
		$logout_params = http_build_query($logout_paramquery,'','&#38;');
			
		$template = new HTMLTemplate("base/navigation/main/main_navigation_middle.html");
		$template->set_var("info_params", $info_params);
		$template->set_var("logout_params", $logout_params);
		$template->output();
		
		
		// Submenu
		
		if ($_GET[nav] == "base" or !$_GET[nav] or $module_tab_active == false)
		{
			$template = new HTMLTemplate("base/navigation/main/sub/blue.html");
			
			$sub_menu = array();
			
			$my_profile_paramquery[username] = $_GET[username];
			$my_profile_paramquery[session_id] = $_GET[session_id];
			$my_profile_paramquery[nav] = "base";
			$my_profile_paramquery[run] = "user_profile";
			$my_profile_params = http_build_query($my_profile_paramquery,'','&#38;');
			
			$sub_menu[0][params] = $my_profile_params;
			$sub_menu[0][title] = "My Profile";
						
			$system_messages_paramquery[username] = $_GET[username];
			$system_messages_paramquery[session_id] = $_GET[session_id];
			$system_messages_paramquery[nav] = "base";
			$system_messages_paramquery[run] = "sysmsg";
			$system_messages_params = http_build_query($system_messages_paramquery,'','&#38;');
			
			$sub_menu[1][params] = $system_messages_params;
			$sub_menu[1][title] = "System Messages";
			
			// Dialogs
			
			$module_dialog_array = ModuleDialog::list_dialogs_by_type("base_user_lists");
			
			if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
			{
				foreach ($module_dialog_array as $key => $value)
				{
					$paramquery[username] 	= $_GET[username];
					$paramquery[session_id] = $_GET[session_id];
					$paramquery[nav]		= "base";
					$paramquery[run]		= "base_user_lists";
					$paramquery[dialog]		= $value[internal_name];
					$params 				= http_build_query($paramquery,'','&#38;');
					
					$temp_array = array();
					$temp_array[params] = $params;
					$temp_array[title] = $value[display_name];
					array_push($sub_menu, $temp_array);
					unset($temp_array);
				}
			}

			
			$template->set_var("sub_menu", $sub_menu);
			$template->set_var("search_bar", false);
			
			unset($sub_menu);
			
			$template->output();
		}
		else
		{
			if ($current_color)
			{
				$template = new HTMLTemplate("base/navigation/main/sub/".$current_color.".html");
				
				$config_folder = "core/modules/".SystemHandler::get_module_folder_by_module_name($_GET[nav])."/config";
				if (is_dir($config_folder))
				{
					$subnavigation_file = $config_folder."/module_subnavigation.php";
					if (is_file($subnavigation_file))
					{
						include($subnavigation_file);
					}
					else
					{
						$template->set_var("sub_menu", false);
						$template->set_var("search_bar", false);
					}
				}
				else
				{
					$template->set_var("sub_menu", false);
					$template->set_var("search_bar", false);
				}
	
				$template->output();
			}
			else
			{
				$template = new HTMLTemplate("base/navigation/main/sub/blue.html");
				$template->set_var("sub_menu", false);
				$template->set_var("search_bar", false);
				$template->output();
			}
		}
		
		$template = new HTMLTemplate("base/navigation/main/main_navigation_footer.html");
		$template->output();
	}
	
	private static function get_left_standard_navigation()
	{
		$dialog_array = ModuleDialog::list_dialogs_by_type("standard_navigation");
		if (count($dialog_array) == 1)
		{
			if (file_exists($dialog_array[0]['class_path']))
			{
				require_once($dialog_array[0]['class_path']);
				$dialog_array[0]['class']::$dialog_array[0]['method']();
			}
			else
			{
				// Exception
			}
		}
		else
		{
			// Exception
		}
	}
	
	public static function left()
	{
		if ($_GET[nav] and $_GET[nav] != "base")
		{
			$module_controller_array = SystemHandler::get_module_controller($_GET[nav]);
								
			$module_controller_path = "core/modules/".$module_controller_array['path'];
			
			if (file_exists($module_controller_path))
			{
				require_once($module_controller_path);
				if (method_exists($module_controller_array['class'], get_navigation))
				{
					if (($navigation_array = $module_controller_array['class']::get_navigation()) !== false)
					{
						if ($navigation_array['class'] and $navigation_array['method'] and $navigation_array['class_path'])
						{
							if (file_exists($navigation_array['class_path']))
							{
								require_once($navigation_array['class_path']);
								$navigation_array['class']::$navigation_array['method']();
							}
							else
							{
								self::get_left_standard_navigation();
							}
						}
						else
						{
							self::get_left_standard_navigation();
						}
					}
					else
					{
						self::get_left_standard_navigation();
					}
				}
				else
				{
					self::get_left_standard_navigation();
				}
			}
			else
			{
				self::get_left_standard_navigation();
			}					
		}
		else
		{
			self::get_left_standard_navigation();
		}
	}
}
?>
