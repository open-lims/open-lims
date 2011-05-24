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
	/**
	 * @todo Remove HTML Statements
	 * @todo Implement IP-Blocking
	 * @todo catch ModuleDataCorruptExeception
	 */
	public static function main()
	{
		global $session, $user, $misc, $transaction;

		if ($GLOBALS['con_run'] == true)
		{
			if (Security::ip_error_count() < constant("MAX_IP_ERRORS"))
			{
				$template = new Template("languages/en-gb/template/index_header.html");
	
				$css_directory = constant("WWW_DIR")."/css";
				$css_directory_array = scandir($css_directory);
				
				if (is_array($css_directory_array))
				{
					$index_css = "";
					
					foreach($css_directory_array as $key => $value)
					{
						if ((strpos(strrev($value),"ssc.") === 0) and (strpos(strrev($value),"ssc.gubed") === false) and ($value != "main.css"))
						{
							if (is_file($css_directory."/".$value))
							{
								$index_css .= "<link rel='stylesheet' type='text/css' href='css/".$value."' title='Style' />\n";
							}	
						}
					}	
				}
				
				$template->set_var("INDEX_CSS",$index_css);
							
			 	$template->set_var("INDEX_TITLE",constant("HTML_TITLE"));
			
				$template->output();
				
		 		if ($session->is_valid() == true and $_GET[run] != "logout")
		 		{
					$template = new Template("languages/en-gb/template/structure_main.html");
					
					$template->set_var("release",constant("PRODUCT")." ".constant("PRODUCT_VERSION"));
					$template->set_var("user",constant("PRODUCT_USER"));
					$template->set_var("servertype",constant("PRODUCT_FUNCTION"));

					$template->output();

					// Navigation
					require_once("base/navigation.io.php");
					Navigation_IO::main();

					echo "</div>";	

					// VNAV
					echo "<div class='clearbox'></div><div id='navigation'>";
					Navigation_IO::left();
			
					echo "</div>" .
						"<div id='content'>";
 		
 					if ($session->read_value("must_change_password") == true)
 					{
 						require_once("core/modules/user/user.io.php");
						UserIO::change_password_on_login();
 					}
 					else
 					{
 						try {
							if ($_GET[nav])
							{
								if($_GET[nav] == "home")
								{
									include("core/modules/base/home.io.php");
								}
								elseif($_GET[nav] == "static")
								{
									require_once("core/modules/base/base.io.php");
									BaseIO::method_handler();
								}
								else
								{
									$module_found = false;
									$module_array = SystemHandler::list_modules();
									
									if (is_array($module_array) and count($module_array) >= 1)
									{
										foreach($module_array as $key => $value)
										{
											if ($_GET[nav] == $value[name])
											{
												$module_path = "core/modules/".$value[folder]."/".$value[name].".io.php";
												if (file_exists($module_path))
												{
													require_once($module_path);
													$value['class']::method_handler();
													$module_found = true;
												}
												else
												{
													throw new ModuleDataCorruptExeception(null, null);
												}
											}
										}
									}
									else
									{
										include("core/modules/base/home.io.php");
									}
									
									if ($module_found == false)
									{
										$error_io = new Error_IO($e, 0, 0, 0);
										$error_io->display_error();
									}
								}
							}
							else
							{
								include("core/modules/base/home.io.php");
							}
						}
						catch(ModuleDialogCorruptException $e)
						{
							/**
							 * @todo Error-Code
							 */
							$error_io = new Error_IO($e, 0, 0, 0);
							$error_io->display_error();
						}
						catch(ModuleDialogNotFoundException $e)
						{
							/**
							 * @todo Error-Code
							 */
							$error_io = new Error_IO($e, 0, 0, 0);
							$error_io->display_error();
						}
						catch(ModuleDialogMissingException $e)
						{
							/**
							 * @todo Error-Code
							 */
							$error_io = new Error_IO($e, 0, 0, 0);
							$error_io->display_error();
						}
						catch(DatabaseQueryFailedException $e)
						{
							$transaction->force_rollback();
							$error_io = new Error_IO($e, 2, 10, 1);
							$error_io->display_error();
						}
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
