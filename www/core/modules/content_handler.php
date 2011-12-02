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
 * Content Handler Class
 * @package base
 */
class ContentHandler_IO
{
	public static function io()
	{
		global $session, $user, $transaction;

		$template = new Template("template/index_header.html");
	
		if (file_exists(constant("WWW_DIR")))
		{
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
								$unique_id = uniqid();
								$index_css .= "<link rel='stylesheet' type='text/css' href='css/".$value."?".$unique_id."' title='Style' />\n";
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
								$index_js .= "<script type='text/javascript' src='js/lib/".$value."?".$unique_id."'></script>\n";
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
								$index_js .= "<script type='text/javascript' src='js/modules/".$value."?".$unique_id."'></script>\n";
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
			$GLOBALS['fatal_error'] = "Main folder not found!";
		}
		
	 	$template->set_var("INDEX_TITLE",constant("HTML_TITLE"));
	
		$template->output();
		
		if ($GLOBALS['fatal_error'] == null)
		{
			if (Security::ip_error_count() < constant("MAX_IP_ERRORS"))
			{
		 		if ($session->is_valid() == true)
		 		{
					$template = new Template("template/main_header.html");
					
					$template->set_var("release",constant("PRODUCT")." ".constant("PRODUCT_VERSION"));
					$template->set_var("user",constant("PRODUCT_USER"));
					$template->set_var("servertype",constant("PRODUCT_FUNCTION"));

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
 						try {
							if ($_GET[nav])
							{
								if($_GET[nav] == "home")
								{
									include("core/modules/base/io/home.io.php");
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
												$module_request_handler = "core/modules/".$value[folder]."/".$value[name].".request.php";
												if (file_exists($module_request_handler))
												{
													require_once($module_request_handler);
													$value['class']::io_handler();
													$module_found = true;
												}
												else
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
									}
									else
									{
										include("core/modules/base/io/home.io.php");
									}
									
									if ($module_found == false)
									{
										// throw exception
									}
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
			 		
			 		$template = new Template("template/main_footer.html");
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
			Error_IO::fatal_error($GLOBALS['fatal_error']);	
		}
		
		$template = new Template("template/index_footer.html");
		$template->output();
	}
	
	/**
	 * @todo Login
	 */
	public static function ajax()
	{
		global $session;
		
		if ($session->is_valid() == true)
		{
			$module_array = SystemHandler::list_modules();
			
			if (is_array($module_array) and count($module_array) >= 1)
			{
				foreach($module_array as $key => $value)
				{
					if ($_GET[nav] == $value[name])
					{
						$module_request_handler = "core/modules/".$value[folder]."/".$value[name].".request.php";
						if (file_exists($module_request_handler))
						{
							require_once($module_request_handler);
							$value['class']::ajax_handler();
						}
					}
				}
			}
		}
		else
		{
			if ($_GET[run] == "login")
			{
				require_once("core/modules/base/base.request.php");
				BaseRequest::ajax_handler();
			}
		}
		
	}
}

?>
