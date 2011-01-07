<?php
/**
 * @package method
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
 * Method Admin IO Class
 * @package method
 */
class AdminMethodCatIO
{
	private static $home_list_counter = 0;

	private static function home_child_list($id, $layer)
	{
		if (is_numeric($id))
		{
			$content_array = array();
			
			$method_cat = new MethodCat($id);
			$method_cat_child_array = $method_cat->get_childs();
			
			if(is_array($method_cat_child_array) and count($method_cat_child_array) >= 1)
			{
				foreach($method_cat_child_array as $key => $value)
				{
					$method_cat = new MethodCat($value);
					
					$content_array[self::$home_list_counter][padding] = 0.5 * $layer;				
					$content_array[self::$home_list_counter][name] = $method_cat->get_name();					
					$content_array[self::$home_list_counter][id] = $value;	
					
					$paramquery = $_GET;
					$paramquery[action] = "delete";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][delete_params] = $params;
					
					
					$paramquery = $_GET;
					$paramquery[action] = "add_child";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][create_child_params] = $params;
					
					
					$paramquery = $_GET;
					$paramquery[action] = "edit";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][edit_params] = $params;
					
					$temp_counter = self::$home_list_counter;
					
					self::$home_list_counter++;
					
					$method_cat_child_array = self::home_child_list($value, $layer+1);
				
					if (is_array($method_cat_child_array))
					{
						$content_array =  $content_array + $method_cat_child_array;
					}
					
				}
				return $content_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}

	public static function home()
	{
		$template = new Template("languages/en-gb/template/admin/method_cat/list.html");	

		$content_array = array();
		
		$method_cat_root_array = MethodCat::list_root_entries();
		
		if(is_array($method_cat_root_array) and count($method_cat_root_array) >= 1)
		{
			foreach($method_cat_root_array as $key => $value)
			{
				$method_cat = new MethodCat($value);
				
				$content_array[self::$home_list_counter][padding] = 0;
				$content_array[self::$home_list_counter][name] = $method_cat->get_name();	
				$content_array[self::$home_list_counter][id] = $value;				
				
				$paramquery = $_GET;
				$paramquery[action] = "delete";
				$paramquery[id] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter][delete_params] = $params;
				
				
				$paramquery = $_GET;
				$paramquery[action] = "add_child";
				$paramquery[id] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter][create_child_params] = $params;
				
				
				$paramquery = $_GET;
				$paramquery[action] = "edit";
				$paramquery[id] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter][edit_params] = $params;
				
				$temp_counter = self::$home_list_counter;
				
				self::$home_list_counter++;
				
				$method_cat_child_array = self::home_child_list($value, 1);
				
				if (is_array($method_cat_child_array))
				{
					$content_array = $content_array + $method_cat_child_array;
				}
			}
			$template->set_var("no_entry", false);
		}
		else
		{
			$template->set_var("no_entry", true);
		}
				
		$paramquery = $_GET;
		$paramquery[action] = "add";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
		
		$template->set_var("method_cat_array", $content_array);
		
		$template->output();
	}

	public static function create()
	{
		global $common;
		
		if (($_GET[action] == "add_child" and $_GET[id]) or $_GET[action] == "add")
		{
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST[name])
				{
					if (MethodCat::exist_name($_POST[name]) == true)
					{
						$page_1_passed = false;
						$error = "This name already exists";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must enter a name";
				}
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
	
			if ($page_1_passed == false)
			{
				$template = new Template("languages/en-gb/template/admin/method_cat/add.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				if ($error)
				{
					$template->set_var("error", $error);
				}
				else
				{
					$template->set_var("error", "");	
				}
													 
				if ($_POST[name])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					$template->set_var("name", "");
				}
							
				$template->output();
			}
			else
			{				
				$method_cat = new MethodCat(null);
					
				if ($_GET[action] == "add_child" and is_numeric($_GET[id]))
				{
					$toid = $_GET[id];
				}
				else
				{
					$toid = null;
				}	

				$paramquery = $_GET;
				unset($paramquery[action]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($method_cat->create($toid, $_POST[name]))
				{
					$common->step_proceed($params, "Add Method Category", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Add Method Category", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 4);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function delete()
	{
		global $common;
		
		if ($_GET[id])
		{
			if ($_GET[sure] != "true")
			{
				$template = new Template("languages/en-gb/template/admin/method_cat/delete.html");
				
				$paramquery = $_GET;
				$paramquery[sure] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery[sure]);
				unset($paramquery[action]);
				unset($paramquery[id]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[sure]);
				unset($paramquery[action]);
				unset($paramquery[id]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$method_cat = new MethodCat($_GET[id]);
				
				if ($method_cat->delete())
				{							
					$common->step_proceed($params, "Delete Method Category", "Operation Successful" ,null);
				}
				else
				{							
					$common->step_proceed($params, "Delete Method Category", "Operation Failed" ,null);
				}		
			}
		}
		else
		{
			$exception = new Exception("", 4);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function edit()
	{
		global $common;
		
		if ($_GET[id])
		{
			$method_cat = new MethodCat($_GET[id]);
		
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST[name])
				{
					if (MethodCat::exist_name($_POST[name]) == true and $method_cat->get_name() != $_POST[name])
					{
						$page_1_passed = false;
						$error = "This name already exists";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must enter a name";
				}
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
	
			if ($page_1_passed == false)
			{
				$template = new Template("languages/en-gb/template/admin/method_cat/edit.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				if ($error)
				{
					$template->set_var("error", $error);
				}
				else
				{
					$template->set_var("error", "");	
				}
													 
				if ($_POST[name])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					$template->set_var("name", $method_cat->get_name());
				}
							
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[nextpage]);
				unset($paramquery[action]);
				$params = http_build_query($paramquery);
				
				if ($method_cat->set_name($_POST[name]))
				{
					$common->step_proceed($params, "Edit Method Category", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Edit Method Category", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 4);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function handler()
	{
		try
		{
			if ($_GET[id])
			{
				if (MethodCat::exist_id($_GET[id]) == false)
				{
					throw new Exception("",4);
				}
			}

			switch($_GET[action]):
				case "add":
				case "add_child":
					self::create();
				break;
				
				case "edit":
					self::edit();
				break;
				
				case "delete":
					self::delete();
				break;
							
				default:
					self::home();
				break;
			endswitch;
		}
		catch (Exception $e)
		{
			$error_io = new Error_IO($e, 50, 40, 1);
			$error_io->display_error();
		}
	}
	
}

?>