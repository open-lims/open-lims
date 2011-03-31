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
	 * Returns the number of failed logins
	 * @return integer
	 * @todo implementation
	 */
 	public static function ip_error_count()
 	{		
		return 0;
 	}

	/**
	 * Checks all GET- and POST-variables
	 * @todo vars of different modules like project_id etc.
	 */
	public static function protect_session()
	{
		global $db;
		
		$module_get_array = array();
		
		$registered_module_array = SystemHandler::get_module_folders();
		if (is_array($registered_module_array) and count($registered_module_array) >= 1)
		{
			foreach($registered_module_array as $key => $value)
			{
				$get_file = $GLOBALS[modules_dir]."/".$value."/config/module_get.php";
				if (file_exists($get_file))
				{
					$get_file = $GLOBALS[modules_dir]."/".$value."/config/module_get.php";
					include($get_file);
					
					if (is_array($get) and count($get) >= 1)
					{
						foreach($get as $key => $value)
						{
							array_push($module_get_array, $value);
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
			// HTML-Entities	
			$_GET[$key] = htmlentities($_GET[$key], ENT_NOQUOTES, "UTF-8", false);
				
			// SQL-Injections
			$_GET[$key] = $db->db_escape_string($_GET[$key]);
			
			// UTF8-Encoding
			$_GET[$key] = mb_convert_encoding($_GET[$key], "UTF-8", "auto");
			
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
			break;
			
			default:
			if (!in_array($key, $module_get_array))
			{
				unset($_GET[$key]);
			}
			break;
			
			endswitch;
			
		}
		
		foreach ($_POST as $key => $value)
		{			
			$_POST[$key] = htmlentities($_POST[$key], ENT_NOQUOTES, "UTF-8", false);
			$_POST[$key] = $db->db_escape_string($_POST[$key]);
			$_POST[$key] = mb_convert_encoding($_POST[$key], "UTF-8", "auto");	
		}
		
	}
	
}

?>
