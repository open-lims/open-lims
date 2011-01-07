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
 * Method Type Admin IO Class
 * @package method
 */
class AdminMethodTypeIO
{
	private static $home_list_counter = 0;

	private static function home_child_list($id, $layer)
	{
		if (is_numeric($id))
		{
			$content_array = array();
			
			$method_type = new MethodType($id);
			$method_type_child_array = $method_type->get_childs();
			
			if(is_array($method_type_child_array) and count($method_type_child_array) >= 1)
			{
				foreach($method_type_child_array as $key => $value)
				{
					$method_type = new MethodType($value);
					
					$content_array[self::$home_list_counter][padding] = 0.5 * $layer;				
					$content_array[self::$home_list_counter][name] = $method_type->get_name();					
					$content_array[self::$home_list_counter][category] = $method_type->get_cat_name();	
					$content_array[self::$home_list_counter][id] = $value;	
					
					
					$paramquery = $_GET;
					$paramquery[action] = "detail";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][detail_params] = $params;
					
					
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
					

					$temp_counter = self::$home_list_counter;
					
					self::$home_list_counter++;
					
					$method_type_child_array = self::home_child_list($value, $layer+1);
				
					if (is_array($method_type_child_array))
					{
						$content_array =  $content_array + $method_type_child_array;
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
		$template = new Template("languages/en-gb/template/admin/method_type/list.html");	

		$content_array = array();
		
		$method_type_root_array = MethodType::list_root_entries();
		
		if(is_array($method_type_root_array) and count($method_type_root_array) >= 1)
		{
			foreach($method_type_root_array as $key => $value)
			{
				$method_type = new MethodType($value);
				
				$content_array[self::$home_list_counter][padding] = 0;
				$content_array[self::$home_list_counter][name] = $method_type->get_name();				
				$content_array[self::$home_list_counter][category] = $method_type->get_cat_name();	
				$content_array[self::$home_list_counter][id] = $value;	
				
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				$paramquery[id] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter][detail_params] = $params;
				
				
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
				
				
				$temp_counter = self::$home_list_counter;
				
				self::$home_list_counter++;
				
				$method_type_child_array = self::home_child_list($value, 1);
				
				if (is_array($method_type_child_array))
				{
					$content_array = $content_array + $method_type_child_array;
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
		
		$template->set_var("method_type_array", $content_array);
		
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
					if (MethodType::exist_name($_POST[name]) == true)
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
				$template = new Template("languages/en-gb/template/admin/method_type/add.html");
				
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
				
				if ($_POST[description])
				{
					$template->set_var("description", $_POST[description]);
				}
				else
				{
					$template->set_var("description", "");
				}
				
				$cat_array = MethodCat::list_entries();
						
				$result = array();
				$counter = 0;
				
				foreach($cat_array as $key => $value)
				{
					$method_cat = new MethodCat($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $method_cat->get_name();
					if ($_POST[cat_id] == $value)
					{
						$result[$counter][selected] = "selected='selected'";
					}
					else
					{
						$result[$counter][selected] = "";
					}
					$counter++;
				}
				
				$template->set_var("option",$result);
							
				$template->output();
			}
			else
			{				
				$method_type = new MethodType(null);
					
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
				
				if ($method_type->create($toid, $_POST[name], $_POST[cat_id], null, $_POST[description]))
				{
					$common->step_proceed($params, "Add Method Type", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Add Method Type", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 5);
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
				$template = new Template("languages/en-gb/template/admin/method_type/delete.html");
				
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
				
				$method_type = new MethodType($_GET[id]);
				
				if ($method_type->delete())
				{							
					$common->step_proceed($params, "Delete Type Category", "Operation Successful" ,null);
				}
				else
				{							
					$common->step_proceed($params, "Delete Type Category", "Operation Failed" ,null);
				}		
			}
		}
		else
		{
			$exception = new Exception("", 5);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function detail()
	{
		global $common;
		
		if ($_GET[id])
		{
			$method_type = new MethodType($_GET[id]);	
					
			$template = new Template("languages/en-gb/template/admin/method_type/detail.html");
			
			$template->set_var("name", $method_type->get_name());
			$template->set_var("category", $method_type->get_cat_name());
			
			if ($method_type->get_description())
			{
				$template->set_var("description", $method_type->get_description());
			}
			else
			{
				$template->set_var("description", "<span class='italic'>none</span>");
			}
			
			$paramquery = $_GET;
			$paramquery[action] = "rename";
			unset($paramquery[nextpage]);
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("rename_params", $params);
				
			$template->output();
		}
		else
		{
			$exception = new Exception("", 5);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function rename()
	{
		global $common;
		
		if ($_GET[id])
		{
			$method_type = new MethodType($_GET[id]);
		
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST[name])
				{
					if (MethodType::exist_name($_POST[name]) == true and $method_type->get_name() != $_POST[name])
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
				$template = new Template("languages/en-gb/template/admin/method_type/rename.html");
				
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
					$template->set_var("name", $method_type->get_name());
				}
							
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[nextpage]);
				$paramquery[action] = "detail";
				$params = http_build_query($paramquery);
				
				if ($method_type->set_name($_POST[name]))
				{
					$common->step_proceed($params, "Rename Method Type", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Rename Method Type", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 5);
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
				if (MethodType::exist_id($_GET[id]) == false)
				{
					throw new Exception("",5);
				}
			}

			switch($_GET[action]):
				case "add":
				case "add_child":
					self::create();
				break;
				
				case "delete":
					self::delete();
				break;
				
				case "detail":
					self::detail();
				break;
				
				case "rename":
					self::rename();
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