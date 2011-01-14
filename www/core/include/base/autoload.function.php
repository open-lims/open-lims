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
 * Autoload-Function
 * Loads required classes
 * @param string $classname
 */
function __autoload($classname)
{
	if ($GLOBALS[autoload_prefix])
	{
		$path_prefix = $GLOBALS[autoload_prefix];
	}
	else
	{
		$path_prefix = "";
	}
	
	$classes['EventListenerInterface']		= $path_prefix."core/include/base/interfaces/event_listener.interface.php"; 
	
	$classes['AuthForgotPasswordSendFailedException']	= $path_prefix."core/include/base/exceptions/auth_forgot_password_send_failed_exception.class.php";
	$classes['AuthUserNotFoundException']				= $path_prefix."core/include/base/exceptions/auth_user_not_found_exception.class.php";
	$classes['IdMissingException']						= $path_prefix."core/include/base/exceptions/id_missing_exception.class.php";
	
	$classes['Auth'] 						= $path_prefix."core/include/base/auth.class.php";
	$classes['Communicator']				= $path_prefix."core/include/base/communicator.class.php";
	$classes['DatetimeHandler']				= $path_prefix."core/include/base/datetime_handler.class.php";
	$classes['ExceptionHandler']			= $path_prefix."core/include/base/exception_handler.class.php";
	$classes['Regional']					= $path_prefix."core/include/base/regional.class.php";
	$classes['System']						= $path_prefix."core/include/base/system.class.php";
	$classes['SystemMessage']				= $path_prefix."core/include/base/system_message.class.php";

	$registered_include_array = SystemHandler::get_include_folders();
	if (is_array($registered_include_array) and count($registered_include_array) >= 1)
	{
		foreach($registered_include_array as $key => $value)
		{
			$config_file = $GLOBALS[include_dir]."/".$value."/config/include_info.php";
			include($config_file);
			if ($no_class_path != true)
			{
				$class_path_file = $GLOBALS[include_dir]."/".$value."/config/class_path.php";
				include($class_path_file);
			}
			unset($no_class_path);
		}
	}
	
	if (isset($classes[$classname])) {
		require_once($classes[$classname]);
	}
	
}

?>