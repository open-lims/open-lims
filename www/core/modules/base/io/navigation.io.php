<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
		
		$template_header = new HTMLTemplate("base/navigation/main/main_navigation_header.html");

		$module_navigation_array = ModuleNavigation::list_module_navigations_entries();
		
		if (is_array($module_navigation_array) and count($module_navigation_array) >= 1)
		{
			$module_tab_string = "";
			$module_tab_active = false;
			
			foreach($module_navigation_array as $key => $value)
			{
				$module_name = SystemHandler::get_module_name_by_module_id($value['module_id']);
				
				if (($module_name == "base" and $value['alias'] == "admin" and $user->is_admin()) 
					or $module_name != "base" 
					or ($module_name == "base" and $value['alias'] != "admin"))
				{
				
					$paramquery['username'] = $_GET['username'];
					$paramquery['session_id'] = $_GET['session_id'];
					
					if ($value['alias'])
					{
						$active_alias = $module_name.".".$value['alias'];
						$paramquery['nav'] = $module_name.".".$value['alias'];
					}
					else
					{
						$active_alias = $module_name;
						$paramquery['nav'] = $module_name;
					}
					
					$params = http_build_query($paramquery,'','&#38;');
					
					if (!$_GET['nav'] and $active_alias == "base")
					{
						$_GET['nav'] = "base";
					}
					
					switch ($value['colour']):
					
						case "blue":
							if ($_GET['nav'] == $active_alias)
							{
								$background_color_class = "NavigationBackgroundBlue";
								$template = new HTMLTemplate("base/navigation/main/tabs/blue_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value['colour'];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/blue_tab.html");
							}
						break;
						
						case "green":
							if ($_GET['nav'] == $active_alias)
							{
								$background_color_class = "NavigationBackgroundGreen";
								$template = new HTMLTemplate("base/navigation/main/tabs/green_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value['colour'];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/green_tab.html");
							}
						break;
						
						case "orange";
							if ($_GET['nav'] == $active_alias)
							{
								$background_color_class = "NavigationBackgroundOrange";
								$template = new HTMLTemplate("base/navigation/main/tabs/orange_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value['colour'];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/orange_tab.html");
							}
						break;
						
						default:
							if ($_GET['nav'] == $active_alias)
							{
								$background_color_class = "NavigationBackgroundGrey";
								$template = new HTMLTemplate("base/navigation/main/tabs/grey_tab_active.html");
								$current_module = $active_alias;
								$current_color = $value['colour'];
								$module_tab_active = true;
							}
							else
							{
								$template = new HTMLTemplate("base/navigation/main/tabs/grey_tab.html");
							}
						break;
						
					endswitch;
					
					
					
					$template->set_var("params", $params);
					$template->set_var("title", Language::get_message($value['language_address'], "navigation"));
					
					$config_folder = "core/modules/".SystemHandler::get_module_folder_by_module_name($module_name)."/config";
					if (is_dir($config_folder))
					{
						$subnavigation_file = $config_folder."/module_subnavigation.php";
						if (is_file($subnavigation_file))
						{
							require_once($subnavigation_file);
							if (is_array($sub_menu) and count($sub_menu) >= 1)
							{
								$template->set_var("down", true);
								$template->set_var("link", $sub_menu);
							}
							else
							{
								$template->set_var("down", false);
							}
							unset($sub_menu);
						}
						else
						{
							$template->set_var("down", false);
						}
					}
					else
					{
						$template->set_var("down", false);
					}
					
					
					$module_tab_string .= $template->get_string();
				}
			}
		}

		$template_header->set_var("background_class", $background_color_class);
		$template_header->output();

		echo $module_tab_string;
		
		$template = new HTMLTemplate("base/navigation/main/main_navigation_footer.html");
		$template->output();
	}
	
	/**
	 * @throws BaseModuleDialogMethodNotFoundException
	 * @throws BaseModuleDialogClassNotFoundException
	 * @throws BaseModuleDialogFileNotFoundException
	 * @throws BaseModuleDialogNotFoundException
	 */
	private static function get_left_standard_navigation()
	{
		$dialog_array = ModuleDialog::list_dialogs_by_type("standard_navigation");
		
		if (count($dialog_array) == 0)
		{
			return;
		}
		elseif (count($dialog_array) == 1)
		{
			if (file_exists($dialog_array[0]['class_path']))
			{
				require_once($dialog_array[0]['class_path']);
				
				if (class_exists($dialog_array[0]['class']))
				{
					if (method_exists($dialog_array[0]['class'], $dialog_array[0]['method']))
					{
						$dialog_array[0]['class']::$dialog_array[0]['method']();
					}
					else
					{
						throw new BaseModuleDialogMethodNotFoundException();
					}
				}
				else
				{
					throw new BaseModuleDialogClassNotFoundException();
				}
			}
			else
			{
				throw new BaseModuleDialogFileNotFoundException();
			}
		}
		else
		{
			throw new BaseModuleDialogNotFoundException();
		}
	}
	
	public static function left()
	{
		if ($_GET['nav'] and $_GET['nav'] != "base")
		{
			$module_controller_array = SystemHandler::get_module_controller($_GET['nav']);
								
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
