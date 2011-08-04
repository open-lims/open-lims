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
 * 
 */
require_once("interfaces/security.interface.php");

/**
 * Security Class
 * Secures System agains SQL-Injections and XSS
 * @package base
 */
class Security implements SecurityInterface
{
	/**
	 * @see SecurityInterface::ip_error_count()
	 * @return integer
	 */
 	public static function ip_error_count()
 	{	
 		$ip = $_SERVER['REMOTE_ADDR'];
 		$lead_time = date("Y-m-d H:i:s", (mktime()-constant("IP_ERROR_LEAD_TIME")));
 		
 		return SystemLog::count_ip_failed_logins_with_begin($ip, $lead_time);
 	}

 	/**
	 * @see SecurityInterface::filter_var()
	 */
 	public static function filter_var()
 	{	
 		global $db;
 		
		foreach ($_GET as $key => $value)
		{
			// HTML-Entities	
			$_GET[$key] = htmlentities($_GET[$key], ENT_NOQUOTES, "UTF-8", false);
				
			// SQL-Injections
			$_GET[$key] = $db->db_escape_string($_GET[$key]);
			
			// UTF8-Encoding
			$_GET[$key] = mb_convert_encoding($_GET[$key], "UTF-8", "auto");
		}
 		
 		foreach ($_POST as $key => $value)
		{			
			$_POST[$key] = htmlentities($_POST[$key], ENT_NOQUOTES, "UTF-8", false);
			$_POST[$key] = $db->db_escape_string($_POST[$key]);
			$_POST[$key] = mb_convert_encoding($_POST[$key], "UTF-8", "auto");	
		}
 	}
 	
	/**
	 * @see SecurityInterface::protect_session()
	 */
	public static function protect_session()
	{		
		$module_get_array = array();
		
		$registered_module_array = SystemHandler::get_module_folders();
		if (is_array($registered_module_array) and count($registered_module_array) >= 1)
		{
			foreach($registered_module_array as $key => $value)
			{
				$get_file = constant("MODULES_DIR")."/".$value."/config/module_get.php";
				if (file_exists($get_file))
				{
					$get_file = constant("MODULES_DIR")."/".$value."/config/module_get.php";
					include($get_file);
					
					if (is_array($get) and count($get) >= 1)
					{
						foreach($get as $key => $value)
						{
							if (!in_array($value, $module_get_array))
							{
								array_push($module_get_array, $value);
							}
						}
					}
				}
			}
		}
		
		if (isset($classes[$classname])) {
			require_once($classes[$classname]);
		}
		
		foreach ($_GET as $key => $value)
		{			
			// GET-Values
			switch($key):

			case("nav"):
			case("vnav"):
			case("username"):
			case("session_id"):
			case("run"):
			case("dialog"):
			case("retrace");
				
			case("action"):
			case("aspect"):
			case("id"):
			case("runid"):
			case("key"):
			case("version"):
			case("sortmethod"):
			case("sortvalue"):
			case("sure"):
			case("nextpage"):
			case("selectpage");
			case("page"):
			case("pageref");
			case("show"):
			case("change_nav"):
			case("tpage"):
			case("view"):
			case("clear");
			break;
			
			default:
			if (!in_array($key, $module_get_array))
			{
				unset($_GET[$key]);
			}
			break;
			
			endswitch;
		}
		
		self::filter_var();	
	}
	
}

?>
