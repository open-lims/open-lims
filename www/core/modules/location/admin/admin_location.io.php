<?php
/**
 * @package location
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
 * Location Admin IO Class
 * @package location
 */
class AdminLocationIO
{	
	private static $home_list_counter = 0;
	
	private static function home_child_list($id, $layer)
	{
		if (is_numeric($id))
		{
			$content_array = array();
			
			$location = new Location($id);
			$location_child_array = $location->get_childs();
			
			if(is_array($location_child_array) and count($location_child_array) >= 1)
			{
				foreach($location_child_array as $key => $value)
				{
					$location = new Location($value);
					
					$content_array[self::$home_list_counter][padding] = 0.5 * $layer;				
					$content_array[self::$home_list_counter][name] = $location->get_name(true);					
					
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
					
					$location_child_array = self::home_child_list($value, $layer+1);
				
					if (is_array($location_child_array))
					{
						$content_array[$temp_counter][show_line] = true;
						$content_array =  $content_array + $location_child_array;
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
		$template = new Template("template/location/admin/location/list.html");	

		$content_array = array();
		
		$location_root_array = Location::list_root_entries();
		
		if(is_array($location_root_array) and count($location_root_array) >= 1)
		{
			foreach($location_root_array as $key => $value)
			{
				$location = new Location($value);
				
				$content_array[self::$home_list_counter][padding] = 0;
				$content_array[self::$home_list_counter][name] = $location->get_name(true);				
				
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
				
				$location_child_array = self::home_child_list($value, 1);
				
				if (is_array($location_child_array))
				{
					$content_array[$temp_counter][show_line] = true;
					$content_array = $content_array + $location_child_array;
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
		
		$template->set_var("location_array", $content_array);
		
		$template->output();
	}
	
	public static function add()
	{
		if (($_GET[action] == "add_child" and $_GET[id]) or $_GET[action] == "add")
		{
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if (!$_POST[name])
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
				$template = new Template("template/location/admin/location/add.html");
				
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

				$type_array = Location::list_types();
				
				$result = array();
				$counter = 0;
				
				if (is_array($type_array) and count($type_array) >= 1)
				{
					foreach($type_array as $key => $value)
					{
						if ($_POST[type_id] == $value[id])
						{
							$result[$counter][selected] = "selected='selected'";
						}
						else
						{
							$result[$counter][selected] = "";
						}
						
						$result[$counter][value] = $value[id];
						$result[$counter][content] = $value[name];
						$counter++;
					}
				}
				
				$template->set_var("type_array",$result);
				
				if ($_POST[name])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					$template->set_var("name", "");
				}
				
				if ($_POST[additional_name])
				{
					$template->set_var("additional_name", $_POST[additional_name]);
				}
				else
				{
					$template->set_var("additional_name", "");
				}
							
				$template->output();
			}
			else
			{				
				$location = new Location(null);
					
				if ($_GET[action] == "add_child" and is_numeric($_GET[id]))
				{
					$toid = $_GET[id];
				}
				else
				{
					$toid = null;
				}
				
				if ($_POST[prefix] == "1")
				{
					$show_prefix = true;
				}
				else
				{
					$show_prefix = false;
				}

				$paramquery = $_GET;
				unset($paramquery[action]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($location->create($toid, $_POST[type_id], $_POST[name], $_POST[additional_name], $show_prefix))
				{
					Common_IO::step_proceed($params, "Add Location", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Add Location", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 60, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function edit()
	{
		if ($_GET[id])
		{
			$location = new Location($_GET[id]);
			
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if (!$_POST[name])
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
				$template = new Template("template/location/admin/location/edit.html");
				
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

				$type_array = Location::list_types();
				
				$result = array();
				$counter = 0;
				
				if ($_POST[type_id])
				{
					$type_id = $_POST[type_id];
				}
				else
				{
					$type_id = $location->get_type_id();
				}
				
				if (is_array($type_array) and count($type_array) >= 1)
				{
					foreach($type_array as $key => $value)
					{
						if ($type_id == $value[id])
						{
							$result[$counter][selected] = "selected='selected'";
						}
						else
						{
							$result[$counter][selected] = "";
						}
						
						$result[$counter][value] = $value[id];
						$result[$counter][content] = $value[name];
						$counter++;
					}
				}
				
				$template->set_var("type_array",$result);
				
				if ($_POST[name])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					$template->set_var("name", $location->get_db_name());
				}
				
				if ($_POST[additional_name])
				{
					$template->set_var("additional_name", $_POST[additional_name]);
				}
				else
				{
					if ($location->get_additional_name())
					{
						$template->set_var("additional_name", $location->get_additional_name());
					}
					else
					{
						$template->set_var("additional_name", "");
					}
				}

				if ($_POST[prefix] == "1")
				{
					$template->set_var("prefix", "checked='checked'");
				}
				else
				{
					if ($location->get_prefix() == true)
					{
						$template->set_var("prefix", "checked='checked'");
					}	
					else
					{
						$template->set_var("prefix", "");
					}				
				}
				
				$template->output();
			}
			else
			{	
				if ($_POST[prefix] == "1")
				{
					$show_prefix = true;
				}
				else
				{
					$show_prefix = false;
				}

				$paramquery = $_GET;
				unset($paramquery[action]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($location->set_type_id($_POST[type_id]) and 
					$location->set_db_name($_POST[name]) and 
					$location->set_additional_name($_POST[additional_name]) and 
					$location->set_prefix($show_prefix))
				{
					Common_IO::step_proceed($params, "Edit Location", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Edit Location", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 60, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function delete()
	{	
		if ($_GET[id])
		{
			if ($_GET[sure] != "true")
			{
				$template = new Template("template/location/admin/location/delete.html");
				
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
				
				$location = new Location($_GET[id]);
				
				if ($location->delete())
				{							
					Common_IO::step_proceed($params, "Delete Location", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Location", "Operation Failed" ,null);
				}		
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 60, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function handler()
	{
		try
		{
			if ($_GET[id])
			{
				if (Location::exist_id($_GET[id]) == false)
				{
					throw new Exception("",1);
				}
			}

			switch($_GET[action]):
				case "add":
				case "add_child":
					self::add();
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
			$error_io = new Error_IO($e, 60, 40, 1);
			$error_io->display_error();
		}
	}
}

?>