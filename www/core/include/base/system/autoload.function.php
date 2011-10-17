<?php
/**
 * @package base
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
 * Autoload-Function
 * Loads required classes
 * @param string $classname
 */
function __autoload($classname)
{
	if ($GLOBALS['autoload_prefix'])
	{
		$path_prefix = $GLOBALS['autoload_prefix'];
	}
	else
	{
		$path_prefix = "";
	}
	
	$classes['Communicator']				= $path_prefix."core/include/base/communicator.class.php";
	$classes['ExceptionHandler']			= $path_prefix."core/include/base/exception_handler.class.php";
	
	
	// Neu
	
	// Environment
	$classes['DatetimeHandler']				= $path_prefix."core/include/base/environment/datetime_handler.class.php";
	$classes['Language']					= $path_prefix."core/include/base/environment/language.class.php";
	$classes['PaperSize']					= $path_prefix."core/include/base/environment/paper_size.class.php";
	$classes['Regional']					= $path_prefix."core/include/base/environment/regional.class.php";
	
	$classes['Environment_Wrapper']			= $path_prefix."core/include/base/environment/environment.wrapper.class.php";
	
	
	// Security
	$classes['AuthForgotPasswordSendFailedException']	= $path_prefix."core/include/base/security/exceptions/auth_forgot_password_send_failed_exception.class.php";
	$classes['AuthUserNotFoundException']				= $path_prefix."core/include/base/security/exceptions/auth_user_not_found_exception.class.php";
	
	$classes['Auth'] 						= $path_prefix."core/include/base/security/auth.class.php";
	
	
	// System
	$classes['EventListenerInterface']		= $path_prefix."core/include/base/system/interfaces/event_listener.interface.php"; 
	
	$classes['IdMissingException']			= $path_prefix."core/include/base/system/exceptions/id_missing_exception.class.php";
	
	$classes['EventHandler']				= $path_prefix."core/include/base/system/event_handler.class.php";
	$classes['System']						= $path_prefix."core/include/base/system/system.class.php";
	$classes['ModuleDialog']				= $path_prefix."core/include/base/system/module_dialog.class.php";
	$classes['ModuleLink']					= $path_prefix."core/include/base/system/module_link.class.php";
	$classes['ModuleNavigation']			= $path_prefix."core/include/base/system/module_navigation.class.php";
	
	
	// System Frontend
	$classes['SystemMessage']				= $path_prefix."core/include/base/system_fe/system_message.class.php";
	
	$classes['SystemFE_Wrapper']			= $path_prefix."core/include/base/system_fe/system_fe.wrapper.class.php";
	
	
	// User
	$classes['User'] 						= $path_prefix."core/include/base/user/user.class.php";
	$classes['Group'] 						= $path_prefix."core/include/base/user/group.class.php";
	
	$classes['User_Wrapper'] 				= $path_prefix."core/include/base/user/user.wrapper.class.php";
	
	
	$registered_include_array = SystemHandler::get_include_folders();
	if (is_array($registered_include_array) and count($registered_include_array) >= 1)
	{
		foreach($registered_include_array as $key => $value)
		{
			$config_file = constant("INCLUDE_DIR")."/".$value."/config/include_info.php";
			if (file_exists($config_file))
			{
				include($config_file);
				if ($no_class_path != true)
				{
					$class_path_file = constant("INCLUDE_DIR")."/".$value."/config/class_path.php";
					include($class_path_file);
				}
				unset($no_class_path);
			}
		}
	}
	
	if (isset($classes[$classname])) {
		require_once($classes[$classname]);
	}
	
}

?>