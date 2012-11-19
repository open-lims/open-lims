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
 * 
 */
require_once("interfaces/system_config.interface.php");

/**
 * System Config Class
 * @package base
 */
class SystemConfig implements SystemConfigInterface
{
	private static $database;
	
	public static function load_system_config($file)
	{
		if (file_exists($file))
		{
			require_once($file);
			
			if (is_array($server) and is_array($database))
			{
				self::$database = $database;

				define("BASE_DIR",						$server['main_folder']);
				define("WWW_DIR",						constant("BASE_DIR")."/www");
				define("LOG_DIR",						constant("BASE_DIR")."/logs");
				define("BIN_DIR",						constant("BASE_DIR")."/bin");
				define("INCLUDE_DIR",					constant("WWW_DIR")."/core/include");
				define("MODULES_DIR",					constant("WWW_DIR")."/core/modules");
								
				define("DEBUG", 						$server['behaviour']['debug_mode']);
				define("AVOID_CSS_CACHE", 				$server['behaviour']['avoid_css_cache']);
				define("AVOID_JS_CACHE", 				$server['behaviour']['avoid_js_cache']);
				define("ENABLE_DB_LOG_ON_ROLLBACK",		$server['behaviour']['on_db_rollback']);
				define("ENABLE_DB_LOG_ON_EXP_ROLLBACK",	$server['behaviour']['on_db_expected_rollback']);							
				define("ENABLE_DB_LOG_ON_COMMIT",		$server['behaviour']['on_db_commit']);	
				
				define("SENDMAIL_FROM", 				$mail['from']);
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public static function load_module_config()
	{
		$module_config_dir = constant("WWW_DIR")."/config/modules";
		
		if (is_dir($module_config_dir))
		{
			$module_config_dir_array = scandir($module_config_dir);
			if (is_array($module_config_dir_array) and count($module_config_dir_array) >= 1)
			{
				foreach($module_config_dir_array as $key => $value)
				{
					$config_file = $module_config_dir."/".$value;
					if (is_file($config_file))
					{
						require_once($config_file);
					}
				}
			}
		}
	}
	
	public static function get_database()
	{
		return self::$database;
	}
}