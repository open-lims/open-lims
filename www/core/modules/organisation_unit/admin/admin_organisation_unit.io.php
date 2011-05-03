<?php
/**
 * @package organisation_unit
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
 * Organisation Unit IO Class
 * @package organisation_unit
 */
class AdminOrganisationUnitIO
{
	private static $home_list_counter = 0;

	private static function home_child_list($id, $layer)
	{
		if (is_numeric($id))
		{
			$content_array = array();
			
			$organisation_unit = new OrganisationUnit($id);
			$organisation_unit_child_array = $organisation_unit->get_organisation_unit_childs();
			
			if(is_array($organisation_unit_child_array) and count($organisation_unit_child_array) >= 1)
			{
				foreach($organisation_unit_child_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
				
					$owner = new User($organisation_unit->get_owner_id());
					$leader = new User($organisation_unit->get_leader_id());
					
					$content_array[self::$home_list_counter][padding] = 0.5 * $layer;
					$content_array[self::$home_list_counter][icon] = $organisation_unit->get_icon();
					$content_array[self::$home_list_counter][name] = $organisation_unit->get_name();
					$content_array[self::$home_list_counter][type] = $organisation_unit->get_type_name();
					$content_array[self::$home_list_counter][owner] = $owner->get_full_name(true);
					$content_array[self::$home_list_counter][leader] = $leader->get_full_name(true);
					
					
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
					
					$content_array[self::$home_list_counter][add_child_params] = $params;
				
				
					$paramquery = $_GET;
					$paramquery[action] = "toogle_visible";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][hide_params] = $params;
				
					
					if ($organisation_unit->is_upper_position() == true)
					{
						$content_array[self::$home_list_counter][upwards_icon] = "upward_na.png";
						$content_array[self::$home_list_counter][upwards_params] = "#";
					}
					else
					{
						$paramquery = $_GET;
						$paramquery[action] = "upwards";
						$paramquery[id] = $value;
						$params = http_build_query($paramquery,'','&#38;');
						
						$content_array[self::$home_list_counter][upwards_params] = "index.php?".$params;
						$content_array[self::$home_list_counter][upwards_icon] = "upward.png";
					}
					
					if ($organisation_unit->is_lower_position() == true)
					{
						$content_array[self::$home_list_counter][downwards_icon] = "downward_na.png";
						$content_array[self::$home_list_counter][downwards_params] = "#";
					}
					else
					{
						$paramquery = $_GET;
						$paramquery[action] = "downwards";
						$paramquery[id] = $value;
						$params = http_build_query($paramquery,'','&#38;');
						
						$content_array[self::$home_list_counter][downwards_params] = "index.php?".$params;
						$content_array[self::$home_list_counter][downwards_icon] = "downward.png";
					}
					
					if ($organisation_unit->get_hidden() == true)
					{
						$content_array[self::$home_list_counter][hide_icon] = "grey_point.png";
					}
					else
					{
						$content_array[self::$home_list_counter][hide_icon] = "green_point.png";
					}
					
					$content_array[self::$home_list_counter][show_line] = false;
					
					$temp_counter = self::$home_list_counter;
					$last_counter = self::$home_list_counter;
					
					self::$home_list_counter++;
					
					$organisation_unit_child_array = self::home_child_list($value, $layer+1);
				
					if (is_array($organisation_unit_child_array))
					{
						$content_array[$temp_counter][show_line] = true;
						$content_array =  $content_array + $organisation_unit_child_array;
					}
				}
				$content_array[$last_counter][show_line] = true;
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
		$content_array = array();
		
		$organisation_unit_root_array = OrganisationUnit::list_organisation_unit_roots();
		
		if(is_array($organisation_unit_root_array) and count($organisation_unit_root_array) >= 1)
		{
			foreach($organisation_unit_root_array as $key => $value)
			{
				$organisation_unit = new OrganisationUnit($value);
				
				$owner = new User($organisation_unit->get_owner_id());
				$leader = new User($organisation_unit->get_leader_id());
				
				$content_array[self::$home_list_counter][padding] = 0;
				$content_array[self::$home_list_counter][icon] = $organisation_unit->get_icon();
				$content_array[self::$home_list_counter][name] = $organisation_unit->get_name();
				$content_array[self::$home_list_counter][type] = $organisation_unit->get_type_name();
				$content_array[self::$home_list_counter][owner] = $owner->get_full_name(true);
				$content_array[self::$home_list_counter][leader] = $leader->get_full_name(true);
				
				if ($organisation_unit->is_upper_position() == true)
				{
					$content_array[self::$home_list_counter][upwards_icon] = "upward_na.png";
					$content_array[self::$home_list_counter][upwards_params] = "#";
				}
				else
				{
					$paramquery = $_GET;
					$paramquery[action] = "upwards";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][upwards_params] = "index.php?".$params;
					$content_array[self::$home_list_counter][upwards_icon] = "upward.png";
				}
				
				if ($organisation_unit->is_lower_position() == true)
				{
					$content_array[self::$home_list_counter][downwards_icon] = "downward_na.png";
					$content_array[self::$home_list_counter][downwards_params] = "#";
				}
				else
				{
					$paramquery = $_GET;
					$paramquery[action] = "downwards";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][downwards_params] = "index.php?".$params;
					$content_array[self::$home_list_counter][downwards_icon] = "downward.png";
				}
				
				if ($organisation_unit->get_hidden() == true)
				{
					$content_array[self::$home_list_counter][hide_icon] = "grey_point.png";
				}
				else
				{
					$content_array[self::$home_list_counter][hide_icon] = "green_point.png";
				}
				
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
				
				$content_array[self::$home_list_counter][add_child_params] = $params;
				
				
				$paramquery = $_GET;
				$paramquery[action] = "toogle_visible";
				$paramquery[id] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter][hide_params] = $params;
				
				
				$content_array[self::$home_list_counter][show_line] = false;
				
				$temp_counter = self::$home_list_counter;
				$last_counter = self::$home_list_counter;
				
				self::$home_list_counter++;
				
				$organisation_unit_child_array = self::home_child_list($value, 1);
				
				if (is_array($organisation_unit_child_array))
				{
					$content_array[$temp_counter][show_line] = true;
					$content_array = $content_array + $organisation_unit_child_array;
				}
			}
			$content_array[$last_counter][show_line] = true;
		}
		
		$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/list.html");
		
		$paramquery = $_GET;
		$paramquery[action] = "add";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
		
		$template->set_var("ou", $content_array);
		
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
					if (OrganisationUnit::exist_name($_POST[name]) == true)
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
				$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/add.html");
				
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
						
				if ($_GET[action] == "add_child" and is_numeric($_GET[id]))
				{
					$organisation_unit = new OrganisationUnit($_GET[id]);
					$template->set_var("parent", $organisation_unit->get_name());
				}
				else
				{
					$template->set_var("parent", "on root");
				}		
							 
				if ($_POST[name])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					$template->set_var("name", "");
				}
				
				$type_array = OrganisationUnit::list_types();
						
				$result = array();
				$counter = 0;
				
				foreach($type_array as $key => $value)
				{
					$result[$counter][value] = $value;
					$result[$counter][content] = OrganisationUnit::get_name_by_type_id($value);
					$counter++;
				}
				
				$template->set_var("option",$result);
							
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[nextpage]);
				unset($paramquery[action]);
				$params = http_build_query($paramquery);
				
				try
				{
					$organisation_unit = new OrganisationUnit(null);
					
					if ($_GET[action] == "add_child" and is_numeric($_GET[id]))
					{
						$toid = $_GET[id];
					}
					else
					{
						$toid = null;
					}	
					
					if ($_POST[contains_projects] == "1")
					{
						$stores_data = true;
					}
					else
					{
						$stores_data = false;
					}
				
					$paramquery = $_GET;
					unset($paramquery[action]);
					unset($paramquery[nextpage]);
					$params = http_build_query($paramquery,'','&#38;');
					
					if ($organisation_unit->create($toid, $_POST[name], $_POST[type], $stores_data))
					{
						$common->step_proceed($params, "Add Organisation Unit", "Operation Successful", null);
					}
					else
					{
						$common->step_proceed($params, "Add Organisation Unit", "Operation Failed" ,null);	
					}
				}
				catch (OrganisationUnitAlreadyExistException $e)
				{
					$error_io = new Error_IO($e, 40, 30, 1);
					$error_io->display_error();
				}
				catch (OrganisationUnitCreationFailedException $e)
				{
					$error_io = new Error_IO($e, 40, 30, 1);
					$error_io->display_error();
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function delete()
	{
		global $common;
		
		if ($_GET[id])
		{
			$organisation_unit_id = $_GET[id];
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			
			if ($organisation_unit->check_delete_dependencies() == true)
			{
				if ($_GET[sure] != "true")
				{
					$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/delete.html");
					
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
					
					if ($organisation_unit->delete())
					{							
						$common->step_proceed($params, "Delete Organisation Unit", "Operation Successful" ,null);
					}
					else
					{							
						$common->step_proceed($params, "Delete Organisation Unit", "Operation Failed" ,null);
					}		
				}
			}
			else
			{
				$exception = new Exception("", 2);
				$error_io = new Error_IO($exception, 40, 40, 1);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function detail()
	{
		global $user;
		
		if ($_GET[id])
		{
			$organisation_unit_id = $_GET[id];
		
			$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/detail.html");
			
			if ($user->is_admin())
			{
				$template->set_var("is_admin", true);
			}
			else
			{
				$template->set_var("is_admin", false);
			}
			
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			$owner = new User($organisation_unit->get_owner_id());
			$leader = new USer($organisation_unit->get_leader_id());
			
			$template->set_var("name", $organisation_unit->get_name());
			$template->set_var("owner", $owner->get_full_name(false));
			$template->set_var("leader", $leader->get_full_name(false));
			
			
			$paramquery = $_GET;
			$paramquery[action] = "rename";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("name_params", $params);	
			
			
			$paramquery = $_GET;
			$paramquery[action] = "change_owner";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("owner_params", $params);	
			
			
			$paramquery = $_GET;
			$paramquery[action] = "change_leader";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("leader_params", $params);	
			
			
			$paramquery = $_GET;
			$paramquery[action] = "add_user";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("add_user_params", $params);	
			
			
			$user_array = $organisation_unit->list_members();
			$user_content_array = array();
			
			$counter = 0;
			
			if (is_array($user_array) and count($user_array) >= 1)
			{
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					
					$paramquery = $_GET;
					$paramquery[action] = "delete_user";
					$paramquery[key] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$user_content_array[$counter][username] = $user->get_username();
					$user_content_array[$counter][fullname] = $user->get_full_name(false);
					$user_content_array[$counter][delete_params] = $params;
					
					$counter++;
				}
				$template->set_var("no_user", false);
			}
			else
			{
				$template->set_var("no_user", true);
			}
			
			$template->set_var("user", $user_content_array);
			
			
			$paramquery = $_GET;
			$paramquery[action] = "add_group";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("add_group_params", $params);	
			
			
			$group_array = $organisation_unit->list_groups();
			$group_content_array = array();
			
			$counter = 0;
			
			if (is_array($group_array) and count($group_array) >= 1)
			{
				foreach($group_array as $key => $value)
				{
					$group = new Group($value);
					
					$paramquery = $_GET;
					$paramquery[action] = "delete_group";
					$paramquery[key] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$group_content_array[$counter][name] = $group->get_name();
					$group_content_array[$counter][delete_params] = $params;
					
					$counter++;
				}
				$template->set_var("no_group", false);
			}
			else
			{
				$template->set_var("no_group", true);
			}
			$template->set_var("group", $group_content_array);
			$template->output();
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function add_user()
	{
		global $common;
		
		if ($_GET[id])
		{			
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[user]))
				{
					$organisation_unit = new OrganisationUnit($_GET[id]);
					if ($organisation_unit->is_user_in_organisation_unit($_POST[user]) == true)
					{
						$page_1_passed = false;
						$error = "The user is already member of this organisation unit.";
					}
					else
					{
						$page_1_passed = true;
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must select an user.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/add_user.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$user_array = User::list_entries();
					
				$result = array();
				$counter = 0;
				
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $user->get_username()." (".$user->get_full_name(false).")";
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$organisation_unit = new OrganisationUnit($_GET[id]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($organisation_unit->create_user_in_organisation_unit($_POST[user]))
				{
					$common->step_proceed($params, "Add Organisation Unit", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Add Organisation Unit", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function delete_user()
	{
		global $common;
		
		if ($_GET[id] and $_GET[key])
		{
			if ($_GET[sure] != "true")
			{
				$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/delete_user.html");
				
				$paramquery = $_GET;
				$paramquery[sure] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery[key]);
				$paramquery[action] = "detail";
				$params = http_build_query($paramquery);
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[key]);
				unset($paramquery[sure]);
				$paramquery[action] = "detail";
				$params = http_build_query($paramquery);
				
				$organisation_unit = new OrganisationUnit($_GET[id]);	
						
				if ($organisation_unit->delete_user_from_organisation_unit($_GET[key]))
				{							
					$common->step_proceed($params, "Delete Organisation Unit", "Operation Successful" ,null);
				}
				else
				{							
					$common->step_proceed($params, "Delete Organisation Unit", "Operation Failed" ,null);
				}			
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function add_group()
	{
		global $common;
		
		if ($_GET[id])
		{		
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[group]))
				{
					$organisation_unit = new OrganisationUnit($_GET[id]);
					if ($organisation_unit->is_group_in_organisation_unit($_POST[group]) == true)
					{
						$page_1_passed = false;
						$error = "This group is already member of this organisation-unit.";
					}
					else
					{
						$page_1_passed = true;
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must select a group.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/add_group.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$group_array = Group::list_groups();
					
				$result = array();
				$counter = 0;
				
				foreach($group_array as $key => $value)
				{
					$group = new Group($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $group->get_name();
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$organisation_unit = new OrganisationUnit($_GET[id]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($organisation_unit->create_group_in_organisation_unit($_POST[group]))
				{
					$common->step_proceed($params, "Add Organisation Unit", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Add Organisation Unit", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function delete_group()
	{
		global $common;
		
		if ($_GET[id] and $_GET[key])
		{
			if ($_GET[sure] != "true")
			{	
				$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/delete_group.html");
				
				$paramquery = $_GET;
				$paramquery[sure] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery[key]);
				$paramquery[action] = "detail";
				$params = http_build_query($paramquery);
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[key]);
				unset($paramquery[sure]);
				$paramquery[action] = "detail";
				$params = http_build_query($paramquery);
				
				$organisation_unit = new OrganisationUnit($_GET[id]);	
						
				if ($organisation_unit->delete_group_from_organisation_unit($_GET[key]))
				{							
					$common->step_proceed($params, "Delete Organisation Unit", "Operation Successful" ,null);
				}
				else
				{							
					$common->step_proceed($params, "Delete Organisation Unit", "Operation Failed" ,null);
				}			
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function rename()
	{
		global $common;
		
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
						
			if ($_GET[nextpage] == 1)
			{
				if ($_POST[name])
				{
					if (OrganisationUnit::exist_name($_POST[name]) == true) {
						$page_1_passed = false;
						$error = "This name is already allocated.";
					}
					else
					{
						$page_1_passed = true;
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must enter a name.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/rename.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				$template->set_var("error",$error);
				
				if ($_POST[username])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					$template->set_var("name", $organisation_unit->get_name());
				}
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($organisation_unit->set_name($_POST[name]))
				{
					$common->step_proceed($params, "Rename User", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Rename User", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function change_owner()
	{
		global $common;
		
		if ($_GET[id])
		{		
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[user]))
				{
					$page_1_passed = true;
				}
				else
				{
					$page_1_passed = false;
					$error = "You must select an user.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/change_owner.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$user_array = User::list_entries();
					
				$result = array();
				$counter = 0;
				
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $user->get_username()." (".$user->get_full_name(false).")";
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$organisation_unit = new OrganisationUnit($_GET[id]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($organisation_unit->set_owner_id($_POST[user]))
				{
					$common->step_proceed($params, "Change Owner", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Change Owner", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function change_leader()
	{
		global $common;
		
		if ($_GET[id])
		{		
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[user]))
				{
					$page_1_passed = true;
				}
				else
				{
					$page_1_passed = false;
					$error = "You must select an user.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/change_leader.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$user_array = User::list_entries();
					
				$result = array();
				$counter = 0;
				
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $user->get_username()." (".$user->get_full_name(false).")";
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$organisation_unit = new OrganisationUnit($_GET[id]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($organisation_unit->set_leader_id($_POST[user]))
				{
					$common->step_proceed($params, "Change Leader", "Operation Successful", null);
				}
				else
				{
					$common->step_proceed($params, "Change Leader", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function upwards()
	{
		global $common;
	
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($organisation_unit->position_upwards())
			{
				$common->step_proceed($params, "Upwards", "Operation Successful", null);
			}
			else
			{
				$common->step_proceed($params, "Upwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function downwards()
	{
		global $common;
	
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($organisation_unit->position_downwards())
			{
				$common->step_proceed($params, "Upwards", "Operation Successful", null);
			}
			else
			{
				$common->step_proceed($params, "Upwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function toogle_visible()
	{
		global $common;
	
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($organisation_unit->get_hidden() == true)
			{
				$hidden = false;
			}
			else
			{
				$hidden = true;
			}
			
			if ($organisation_unit->set_hidden($hidden))
			{
				$common->step_proceed($params, "Upwards", "Operation Successful", null);
			}
			else
			{
				$common->step_proceed($params, "Upwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function handler()
	{
		try
		{
			if ($_GET[id])
			{
				if (OrganisationUnit::exist_organisation_unit($_GET[id]) == false)
				{
					throw new OrganisationUnitNotFoundException("",1);
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
								
				case "add_user":
					self::add_user();
				break;
				
				case "delete_user":
					self::delete_user();
				break;
				
				case "add_group":
					self::add_group();
				break;
				
				case "delete_group":
					self::delete_group();
				break;
				
				case "rename":
					self::rename();
				break;
				
				case "change_owner":
					self::change_owner();
				break;
				
				case "change_leader":
					self::change_leader();
				break;
				
				case "upwards":
					self::upwards();
				break;
				
				case "downwards":
					self::downwards();
				break;
				
				case "toogle_visible":
					self::toogle_visible();
				break;
				
				default:
					self::home();
				break;
			endswitch;
		}
		catch (OrganisationUnitNotFoundException $e)
		{
			$error_io = new Error_IO($e, 40, 40, 1);
			$error_io->display_error();
		}
	}
	
	public static function home_dialog()
	{
		$template = new Template("languages/en-gb/template/organisation_unit/admin/organisation_unit/home_dialog.html");
	
		$paramquery 			= array();
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] 		= $_GET[nav];
		$paramquery[run] 		= "organisation";
		$paramquery[dialog] 	= "organisation_units";
		$paramquery[action] 	= "add";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("ou_add_params", $params);
		$template->set_var("ou_amount", OrganisationUnit::count_organisation_units());
		
		return $template->get_string();
	}
}

?>