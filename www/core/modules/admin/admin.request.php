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
 * Admin Request Class
 * @package base
 */
class AdminRequest
{
	/**
	 * @return array
	 */
	public static function get_navigation()
	{
		$return_array = array();
		$return_array['class_path'] = "core/modules/admin/io/navigation/admin_navigation.io.php";
		$return_array['class'] = "AdminNavigationIO";
		$return_array['method'] = "navigation";
		return $return_array;
	}
	
	/**
	 * @todo IMPORTANT: remove bad dependency
	 */
	public static function io_handler()
	{
		global $user;
		
		if ($user->is_admin())
		{
			switch($_GET[run]):
				
				case "general_admin":
					require_once("core/modules/base/io/admin/admin_general.io.php");
					AdminGeneralIO::handler();
				break;
			
				case "include_admin":
					require_once("core/modules/base/io/admin/admin_base_include.io.php");
					AdminBaseIncludeIO::handler();
				break;
				
				case "module_admin":
					require_once("core/modules/base/io/admin/admin_base_module.io.php");
					AdminBaseModuleIO::handler();
				break;
				
				case "navigation":
					require_once("core/modules/base/io/admin/admin_base_navigation.io.php");
					AdminBaseNavigationIO::handler();
				break;
				
				case "registry":
					require_once("core/modules/base/io/admin/admin_base_registry.io.php");
					AdminBaseRegistryIO::handler();
				break;
			
				case "system_log":
					require_once("core/modules/base/io/admin/admin_system_log.io.php");
					AdminSystemLogIO::handler();
				break;
				
				case "system_message":
					require_once("core/modules/base/io/admin/admin_system_message.io.php");
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
					require_once("io/admin.io.php");
					AdminIO::home();
				break;
				
			endswitch;		
		}
		else
		{
			// Error
		}
		
	}
}