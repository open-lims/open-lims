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
 * Admin IO Class
 * @package base
 */
class AdminIO
{
	private static function home()
	{
		$template = new Template("template/admin/home.html");
		
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("admin_home_box");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			$content = "";
			
			foreach ($module_dialog_array as $key => $value)
			{
				require_once($value['class_path']);
				$content .= $value['class']::$value['method']();
			}
			
			$template->set_var("content", $content);
		}
		else
		{
			$template->set_var("content", "");
		}
		
		$template->output();
	}

	public static function get_navigation()
	{
		$return_array = array();
		$return_array['class_path'] = "core/modules/admin/navigation/admin_navigation.io.php";
		$return_array['class'] = "AdminNavigationIO";
		$return_array['method'] = "navigation";
		return $return_array;
	}
	
	public static function method_handler()
	{
		global $user;
		
		if ($user->is_admin())
		{
			switch($_GET[run]):
				
				case "include_admin":
					require_once("core/modules/base/admin/admin_base_include.io.php");
					AdminBaseIncludeIO::handler();
				break;
				
				case "module_admin":
					require_once("core/modules/base/admin/admin_base_module.io.php");
					AdminBaseModuleIO::handler();
				break;
				
				case "navigation":
					require_once("core/modules/base/admin/admin_base_navigation.io.php");
					AdminBaseNavigationIO::handler();
				break;
			
				case "system_log":
					require_once("core/modules/base/admin/admin_system_log.io.php");
					AdminSystemLogIO::handler();
				break;
				
				case "system_message":
					require_once("core/modules/base/admin/admin_system_message.io.php");
					AdminSystemMessageIO::handler();
				break;
				
				// Organisation
				case("organisation"):
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("organisation_admin", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]($sql);
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				break;
				
				// Modules
				case("module"):
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("module_admin", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]($sql);
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				break;
				
				// Module Value Change
				case("module_value_change"):
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("module_value_change", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]($sql);
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				break;
				
				default:
					self::home();
				break;
				
			endswitch;		
		}
		else
		{
			
			if ($_GET[run] == "organisation_unit" and 
				is_numeric($_GET[id]))
			{
				$organisation_unit = new OrganisationUnit($_GET[id]);
				
				if ($organisation_unit->is_owner_in_organisation_unit($user->get_user_id()) == true)
				{
					if ($_GET[action] == "detail" or 
						$_GET[action] == "add_user" or 
						$_GET[action] == "delete_user" or
						$_GET[action] == "add_group" or
						$_GET[action] == "delete_group")
					{
						require_once("admin_organisation_unit.io.php");
						AdminOrganisationUnitIO::handler();
					}
					else
					{
						$exception = new Exception("", 1);
						$error_io = new Error_IO($exception, 1, 40, 2);
						$error_io->display_error();
					}
				}
				else
				{
					$exception = new Exception("", 1);
					$error_io = new Error_IO($exception, 1, 40, 2);
					$error_io->display_error();
				}	
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 1, 40, 2);
				$error_io->display_error();
			}
		}
		
	}
	
}
	
?>