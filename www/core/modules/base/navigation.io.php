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
 * Navigation IO Class
 * @package base
 */
class Navigation_IO
{
	/**
	 * @todo JQUERY: use JQuery drop-down menu, if more than 8 entries
	 */
	public static function main()
	{
		global $user;
		
		// Tabs
		
		$template = new Template("languages/en-gb/template/base/navigation/main/main_navigation_header.html");
		$template->output();

		// HOME
		$paramquery[username] = $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] = "home";
		$params = http_build_query($paramquery,'','&#38;');

		if ($_GET[nav] == "home" or !$_GET[nav])
		{
			$template = new Template("languages/en-gb/template/base/navigation/main/tabs/blue_tab_active.html");
			$template->set_var("params", $params);
			$template->set_var("title", "Home");
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/base/navigation/main/tabs/blue_tab.html");
			$template->set_var("params", $params);
			$template->set_var("title", "Home");
			$template->output();
		}
		
		$module_navigation_array = ModuleNavigation::list_module_navigations_entries();
		
		if (is_array($module_navigation_array) and count($module_navigation_array) >= 1)
		{
			foreach($module_navigation_array as $key => $value)
			{
				$module_name = SystemHandler::get_module_name_by_module_id($value[module_id]);
				
				if (($module_name == "admin" and $user->is_admin()) or $module_name != admin)
				{
				
					$paramquery[username] = $_GET[username];
					$paramquery[session_id] = $_GET[session_id];
					$paramquery[nav] = $module_name;
					$params = http_build_query($paramquery,'','&#38;');
					
					switch ($value[colour]):
					
						case "blue":
							if ($_GET[nav] == $module_name)
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/blue_tab_active.html");
								$current_module = $module_name;
								$current_color = $value[colour];
							}
							else
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/blue_tab.html");
							}
						break;
						
						case "green":
							if ($_GET[nav] == $module_name)
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/green_tab_active.html");
								$current_module = $module_name;
								$current_color = $value[colour];
							}
							else
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/green_tab.html");
							}
						break;
						
						case "orange";
							if ($_GET[nav] == $module_name)
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/orange_tab_active.html");
								$current_module = $module_name;
								$current_color = $value[colour];
							}
							else
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/orange_tab.html");
							}
						break;
						
						case "lightgreen":
							if ($_GET[nav] == $module_name)
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/lightgreen_tab_active.html");
								$current_module = $module_name;
								$current_color = $value[colour];
							}
							else
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/lightgreen_tab.html");
							}
						break;
							
						case "lightblue":
							if ($_GET[nav] == $module_name)
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/lightblue_tab_active.html");
								$current_module = $module_name;
								$current_color = $value[colour];
							}
							else
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/lightblue_tab.html");
							}
						break;
					
						default:
							if ($_GET[nav] == $module_name)
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/grey_tab_active.html");
								$current_module = $module_name;
								$current_color = $value[colour];
							}
							else
							{
								$template = new Template("languages/en-gb/template/base/navigation/main/tabs/grey_tab.html");
							}
						break;
						
					endswitch;
					
					$template->set_var("params", $params);
					$template->set_var("title", $value[display_name]);
					$template->output();
				}
			}
		}
				
		$info_paramquery[username] = $_GET[username];
		$info_paramquery[session_id] = $_GET[session_id];
		$info_paramquery[nav] = "static";
		$info_paramquery[run] = "system_info";
		$info_params = http_build_query($info_paramquery,'','&#38;');
		
		$logout_paramquery[username] = $_GET[username];
		$logout_paramquery[session_id] = $_GET[session_id];
		$logout_paramquery[run] = "logout";
		$logout_params = http_build_query($logout_paramquery,'','&#38;');
			
		$template = new Template("languages/en-gb/template/base/navigation/main/main_navigation_middle.html");
		$template->set_var("info_params", $info_params);
		$template->set_var("logout_params", $logout_params);
		$template->output();
		
		
		// Submenu
		
		if ($_GET[nav] == "home")
		{
			$template = new Template("languages/en-gb/template/base/navigation/main/sub/blue.html");
			
			$sub_menu = array();
			
			$my_profile_paramquery[username] = $_GET[username];
			$my_profile_paramquery[session_id] = $_GET[session_id];
			$my_profile_paramquery[nav] = "user";
			$my_profile_params = http_build_query($my_profile_paramquery,'','&#38;');
			
			$sub_menu[0][params] = $my_profile_params;
			$sub_menu[0][title] = "My Profile";
			
			$my_organisation_units_paramquery[username] = $_GET[username];
			$my_organisation_units_paramquery[session_id] = $_GET[session_id];
			$my_organisation_units_paramquery[nav] = "static";
			$my_organisation_units_paramquery[run] = "myorgan";
			$my_organisation_units_params = http_build_query($my_organisation_units_paramquery,'','&#38;');
			
			$sub_menu[1][params] = $my_organisation_units_params;
			$sub_menu[1][title] = "My Organisation Units";
			
			$system_messages_paramquery[username] = $_GET[username];
			$system_messages_paramquery[session_id] = $_GET[session_id];
			$system_messages_paramquery[nav] = "static";
			$system_messages_paramquery[run] = "sysmsg";
			$system_messages_params = http_build_query($system_messages_paramquery,'','&#38;');
			
			$sub_menu[2][params] = $system_messages_params;
			$sub_menu[2][title] = "System Messages";
			
			$template->set_var("sub_menu", $sub_menu);
			$template->set_var("search_bar", false);
			
			unset($sub_menu);
			
			$template->output();
		}
		else
		{
			if ($current_color)
			{
				$template = new Template("languages/en-gb/template/base/navigation/main/sub/".$current_color.".html");
				
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
				$template = new Template("languages/en-gb/template/base/navigation/main/sub/blue.html");
				$template->set_var("sub_menu", false);
				$template->set_var("search_bar", false);
				$template->output();
			}
		}
		
		$template = new Template("languages/en-gb/template/base/navigation/main/main_navigation_footer.html");
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
	
	/**
	 * @todo ambiguous std. navigation exception
	 */
	public static function left()
	{
		if ($_GET[nav] and $_GET[nav] != "static" and $_GET[nav] != "home")
		{
			$module_array = SystemHandler::list_modules();
			
			if (is_array($module_array) and count($module_array) >= 1)
			{
				foreach($module_array as $key => $value)
				{
					if ($_GET[nav] == $value[name])
					{
						$module_path = "core/modules/".$value[folder]."/".$value[name].".io.php";
						if (file_exists($module_path))
						{
							require_once($module_path);
							if (method_exists($value['class'], get_navigation))
							{
								if (($navigation_array = $value['class']::get_navigation()) !== false)
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
					}
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
