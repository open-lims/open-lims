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
 * Content Handler Class
 * @package base
 */
class ContentHandler_IO
{
	public static function includer()
	{
		global $session, $user, $common, $misc;
		
		switch($_GET[nav]):
	 		
	 		case("projects"):
	 			require_once("core/modules/project/project.io.php");
	 			ProjectIO::method_handler();
	 		break;
	 		
	 		case("method"):
	 			require_once("core/modules/method/method.io.php");
	 			MethodIO::method_handler();
	 		break;
	 		
	 		case("samples"):
	 			require_once("core/modules/sample/sample.io.php");
	 			SampleIO::method_handler();
	 		break;
	 		
	 		case("data"):
	 			require_once("core/modules/data/data.io.php");
				DataIO::method_handler();
	 		break;
	 		
	 		case("folder"):
	 			require_once("core/modules/data/folder.io.php");
				FolderIO::method_handler();
	 		break;
	 		
	 		case("file"):
	 			require_once("core/modules/data/file.io.php");
				FileIO::method_handler();
	 		break;
	 		
	 		case("value"):
	 			require_once("core/modules/data/value.io.php");
				ValueIO::method_handler();
	 		break;
	 		
	 		case("item"):
	 			require_once("core/modules/item/item.io.php");
				ItemIO::method_handler();
	 		break;
	 		
	 		case("search"):
	 			require_once("core/modules/search/search.io.php");
				SearchIO::method_handler();
	 		break;
	 			 		
	 		case("organiser"):
	 			require_once("core/modules/organiser/organiser.io.php");
				OrganiserIO::method_handler();
	 		break;
	 		
	 		case("user"):
	 			require_once("core/modules/user/user.io.php");
				UserIO::method_handler();
	 		break;
	 		
	 		case("administration"):
	 			require_once("core/modules/admin/admin.io.php");
				AdminIO::method_handler();
	 		break;
	 		
	 		case ("static"):
	 			require_once("core/modules/base/base.io.php");
				BaseIO::method_handler();
	 		break;
	 		
	 		default:
				include("core/modules/base/home.io.php");
			break;
	 		
	 	endswitch;	
	}
	
	/**
	 * @todo Remove HTML Statements
	 * @todo Implement IP-Blocking
	 */
	public static function main()
	{
		global $session;

		if ($GLOBALS[con_run] == true)
		{
			if (Security::ip_error_count() < $GLOBALS[max_ip_errors])
			{
				$template = new Template("languages/en-gb/template/index_header.html");
	
				$index_css = "";
				
				if ($session->is_valid() == false or $_GET[run] == "logout")
				{
					$index_css .= "<link rel='stylesheet' type='text/css' href='css/login.css' title='Style' />";
				}
				
				
				$template->set_var("INDEX_CSS",$index_css);
			 	$template->set_var("INDEX_TITLE",$GLOBALS[htmltitle]);
			
				$template->output();
				
		 		if ($session->is_valid() == true and $_GET[run] != "logout")
		 		{
					$template = new Template("languages/en-gb/template/structure_main.html");
					
					$template->set_var("release",$GLOBALS[product_name]." ".$GLOBALS[major_release]."".$GLOBALS[minor_release]);
					$template->set_var("user",$GLOBALS[product_user]);
					$template->set_var("servertype",$GLOBALS[server_info]);

					$template->output();

					// HNAV
					require_once("navigation/main_navigation.io.php");
					$main_navigation_io = new MainNavigation_IO();
					$main_navigation_io->output();

					echo "</div>";	

					// VNAV
					echo "<div class='clearbox'></div><div id='navigation'>";

					require_once("navigation/left_navigation.io.php");
					LeftNavigation_IO::handler();
			
					echo "</div>" .
						"<div id='content'>";
 		
 					if ($session->read_value("must_change_password") == true)
 					{
 						require_once("core/modules/user/user.io.php");
						UserIO::change_password_on_login();
 					}
 					else
 					{
 						// Project Tab Bar
	 					if ($main_navigation_io->is_in_project() == true)
	 					{
	 						require_once("project/project_common.io.php");
	 						ProjectCommon_IO::tab_header();
	 					}
	 					
	 					// Sample Tab Bar
	 					if ($main_navigation_io->is_in_sample() == true)
	 					{
	 						require_once("sample/sample_common.io.php");
	 						SampleCommon_IO::tab_header();
	 					}
	 					
				 		self::includer();
 					}
			 		
			 		echo "</div>";
		 		}
		 		else
		 		{
		 			require_once("base/login.io.php");
		 			Login_IO::output();	 			
		 		}
		 		
		 		$template = new Template("languages/en-gb/template/index_footer.html");
				$template->output();
			}
			else
			{
				// IP Blocked by Server
			}	
		}
		else
		{
			// SQL Connection Failed
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 2, 10, 1);
			$error_io->display_outside_error();			
		}
	}
	
}

?>
