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
	 */
	public static function protect_session()
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
			
			// GET-Values
			switch($key):
			
			case("ou_id"):
			case("nav"):
			case("vnav"):
			case("username"):
			case("session_id"):
			case("run"):
			case("action"):
			case("aspect"):
			case("id"):
			case("runid"):
			case("vfolder_id"):
			case("folder_id"):
			case("file_id"):
			case("value_id"):
			case("key"):
			case("project_id"):
			case("sample_id"):
			case("version"):
			case("sortmethod"):
			case("sortvalue"):
			case("sure"):
			case("nextpage"):
			case("page"):
			case("pageref");
			case("show"):
			case("change_tab"):
			case("change_nav"):
			case("tpage"):
			case("view"):
			break;
			
			default:
			if ($_GET[nav] == "extensions")
			{
				unset($_GET[$key]);
			}
			else
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
