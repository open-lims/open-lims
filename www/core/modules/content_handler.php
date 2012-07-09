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
 * Content Handler Class
 * @package base
 */
class ContentHandler_IO
{
	public static function io()
	{
		global $session, $user, $transaction;

		$template = new HTMLTemplate("index_header.html");
	
		if (file_exists(constant("WWW_DIR")))
		{
			$unique_id = uniqid();
			
			$css_directory = constant("WWW_DIR")."/css";
			if (file_exists($css_directory))
			{
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
								if (constant("AVOID_CSS_CACHE") == true)
								{
									$index_css .= "<link rel='stylesheet' type='text/css' href='css/".$value."?".$unique_id."' title='Style' />\n";
								}
								else
								{
									$index_css .= "<link rel='stylesheet' type='text/css' href='css/".$value."' title='Style' />\n";
								}
							}	
						}
					}	
				}
				
				$template->set_var("INDEX_CSS",$index_css);
			}
			
			$index_js = "";
			
			$js_lib_directory = constant("WWW_DIR")."/js/lib";
			if (file_exists($js_lib_directory))
			{
				$js_lib_directory_array = scandir($js_lib_directory);
				
				if (is_array($js_lib_directory_array))
				{
					$index_js = "";
					
					foreach($js_lib_directory_array as $key => $value)
					{
						if ((strpos(strrev($value),"sj.") === 0))
						{
							if (is_file($js_lib_directory."/".$value))
							{
								if (constant("AVOID_JS_CACHE") == true)
								{
									$index_js .= "<script type='text/javascript' src='js/lib/".$value."?".$unique_id."'></script>\n";
								}
								else
								{
									$index_js .= "<script type='text/javascript' src='js/lib/".$value."'></script>\n";
								}
							}	
						}
					}	
				}
			}
			
			$js_modules_directory = constant("WWW_DIR")."/js/modules";
			if (file_exists($js_modules_directory))
			{
				$js_modules_directory_array = scandir($js_modules_directory);
				
				if (is_array($js_modules_directory_array))
				{
					foreach($js_modules_directory_array as $key => $value)
					{						
						if ((strpos(strrev($value),"sj.") === 0))
						{
							if (is_file($js_modules_directory."/".$value))
							{
								if (constant("AVOID_JS_CACHE") == true)
								{
									$index_js .= "<script type='text/javascript' src='js/modules/".$value."?".$unique_id."'></script>\n";
								}
								else
								{
									$index_js .= "<script type='text/javascript' src='js/modules/".$value."'></script>\n";
								}
							}	
						}
					}	
				}
			}
			
			if ($index_js)
			{
				$template->set_var("INDEX_JS",$index_js);
			}
			else
			{
				$template->set_var("INDEX_JS"," ");
			}
		}
		else
		{
			$template->set_var("INDEX_CSS","<link rel=\"stylesheet\" type=\"text/css\" href=\"css/base.css\" title=\"Style\" />\n<link rel=\"stylesheet\" type=\"text/css\" href=\"css/login.css\" title=\"Style\" />");
			$template->set_var("INDEX_JS","");
			$GLOBALS['fatal_error'] = "Main folder not found!";
		}

		if ($GLOBALS['fatal_error'] == null)
		{
			$template->set_var("INDEX_TITLE",Registry::get_value("base_html_title"));
		
			if (Cron::check() == true)
			{
				$template->set_var("CRON",true);
			}
			else
			{
				$template->set_var("CRON",false);
			}
			
			$template->output();
			
			$max_ip_errors = (int)Registry::get_value(base_max_ip_failed_logins);
			
			if (!is_numeric($max_ip_errors) or $max_ip_errors < 3)
			{
				$max_ip_errors = 3;
			}
			
			if (Security::ip_error_count() < $max_ip_errors)
			{
		 		if ($session->is_valid() == true)
		 		{
					$template = new HTMLTemplate("main_header.html");
					
					$template->set_var("release",constant("PRODUCT")." ".constant("PRODUCT_VERSION"));
					
					$product_user = Registry::get_value("base_product_user");
					$product_function = Registry::get_value("base_product_function");
					
					$template->set_var("user",$product_user);
					$template->set_var("servertype",$product_function);

					$template->output();
					
					// Navigation
					require_once("base/io/navigation.io.php");
					Navigation_IO::main();
					Navigation_IO::left();
					
					
					/**
					 * @todo remove
					 */
					echo "<div id='content'>";
					
 					if ($session->read_value("must_change_password") == true)
 					{
 						require_once("core/modules/base/io/user.io.php");
						UserIO::change_password_on_login();
 					}
 					else
 					{
 						try
 						{
							if ($_GET[nav])
							{
								$module_controller_array = SystemHandler::get_module_controller($_GET[nav]);
								
								$module_controller_path = "core/modules/".$module_controller_array['path'];

								if (file_exists($module_controller_path))
								{
									require_once($module_controller_path);
									$module_controller_array['class']::io_handler($module_controller_array['alias']);
								}
								else
								{
									throw new ModuleDataCorruptExeception();
								}							
							}
							else
							{
								include("core/modules/base/io/home.io.php");
							}
						}
						catch(DatabaseQueryFailedException $e)
						{
							$transaction->force_rollback();
							$error_io = new Error_IO($e);
							$error_io->display_error();
						}
 						catch(BaseException $e)
						{
							$error_io = new Error_IO($e);
							$error_io->display_error();
						}
 					}
			 		
			 		$template = new HTMLTemplate("main_footer.html");
			 		$template->output();
		 		}
		 		else
		 		{
		 			require_once("base/io/login.io.php");
		 			Login_IO::output();	 			
		 		}
			}
			else
			{
				Error_IO::security_out_of_box_error("Your IP was blocked by server!");	
			}	
		}
		else
		{
			$template->output();
			Error_IO::fatal_error($GLOBALS['fatal_error']);	
		}
		
		$template = new HTMLTemplate("index_footer.html");
		$template->output();
	}
	
	public static function ajax()
	{
		global $session;
		
		try
 		{
			if ($session->is_valid() == true)
			{
				if ($_GET['nav'])
				{				
					$module_controller_array = SystemHandler::get_module_controller($_GET[nav]);
								
					$module_controller_path = "core/modules/".$module_controller_array['path'];
					
					if (file_exists($module_controller_path))
					{
						require_once($module_controller_path);
						$module_controller_array['class']::ajax_handler($module_controller_array['alias']);
					}
					else
					{
						throw new ModuleDataCorruptExeception();
					}
				}
				elseif($_GET['extension'])
				{
					$extension_id = Extension::get_id_by_identifer($_GET['extension']);
					if (is_numeric($extension_id))
					{
						$extension = new Extension($extension_id);
						$extension_class = $extension->get_class();
						$extension_file = constant("EXTENSION_DIR")."/".$extension->get_folder()."/".$extension->get_main_file();
						if (file_exists($extension_file))
						{
							require_once($extension_file);
							$extension_class::ajax();
						}
					}
					else
					{
						// Exception
					}
				}
				else
				{
					// Exception
				}
			}
			else
			{
				if ($_GET['run'] == "login" or $_GET['run'] == "cron")
				{
					require_once("core/modules/base/base.request.php");
					BaseRequest::ajax_handler(null);
				}
			}
 		}
 		catch(DatabaseQueryFailedException $e)
		{
			echo "EXCEPTION: DatabaseQueryFailedException";
		}
 		catch(BaseException $e)
		{
			$error_io = new Error_IO($e);
			echo "EXCEPTION: ".$error_io->get_error_message();
		}
	}
}

?>
