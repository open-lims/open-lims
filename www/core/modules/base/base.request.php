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
 * Base Request Class
 * @package base
 */
class BaseRequest
{	
	public static function ajax_handler()
	{
		switch($_GET[run]):
			
			case "login":
				require_once("ajax/login.ajax.php");
				echo LoginAjax::login($_POST[username], $_POST[password], $_POST[language]);
			break;
			
			case "logout":
				require_once("ajax/login.ajax.php");
				echo LoginAjax::logout();
			break;
			
			case"left_navigation":
				require_once("ajax/navigation/left_navigation.ajax.php");
				
				switch($_GET['action']):
					case "set_active":
						echo LeftNavigationAjax::set_active($_POST['id']);
					break;
				endswitch;
				
			break;
			
		endswitch;
	}
	
	public static function io_handler()
	{
		switch ($_GET[run]):
					
			// BASE
			case "sysmsg":
				require_once("io/base.io.php");
				BaseIO::list_system_messages();
			break;
			
			case "system_info":
				require_once("io/base.io.php");
				BaseIO::system_info();
			break;
			
			case "software_info":
				require_once("io/base.io.php");
				BaseIO::software_info();
			break;
			
			case "license":
				require_once("io/base.io.php");
				BaseIO::license();
			break;
			
			case "base_user_lists";
				if ($_GET[dialog])
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("base_user_lists", $_GET[dialog]);
					
					if (file_exists($module_dialog[class_path]))
					{
						require_once($module_dialog[class_path]);
						
						if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
						{
							$module_dialog['class']::$module_dialog[method]();
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
			
			
			// USER
			case "user_profile":
				require_once("io/user.io.php");
				UserIO::profile();
			break;
			
			case ("user_details"):
				require_once("io/user.io.php");
				UserIO::details();
			break;
			
			case("user_change_personal"):
				require_once("io/user.io.php");
				UserIO::change_personal();
			break;
			
			case("user_change_my_settings"):
				require_once("io/user.io.php");
				UserIO::change_my_settings();
			break;
			
			case("user_change_password"):
				require_once("io/user.io.php");
				UserIO::change_password();
			break;
			
			case("user_change_language"):
				require_once("io/user.io.php");
				UserIO::change_language();
			break;
			
			case("user_change_timezone"):
				require_once("io/user.io.php");
				UserIO::change_timezone();
			break;		
			
		endswitch;
	}
}
?>