<?php
/**
 * @package user
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
 * Group Admin IO Class
 * @package user
 */
class AdminGroupIO
{
	public static function home()
	{
		$content_array = array();
		
		$table_io = new TableIO("OverviewTable");
		
		$table_io->add_row("","symbol",false,16);
		$table_io->add_row("Name","name",false,null);
		$table_io->add_row("Members (User)","user",false,null);
		$table_io->add_row("Members (OU)","ou",false,null);
		$table_io->add_row("D","delete",false,16);
		
		
		$group_array = Group::list_groups();
		
		$group_array_cardinality = count($group_array);
		
		$counter = 0;

		if (!$_GET[page] or $_GET[page] == 1)
		{
			$page = 1;
			$counter_begin = 0;
			if ($group_array_cardinality > 25)
			{
				$counter_end = 24;
			}
			else
			{
				$counter_end = $group_array_cardinality-1;
			}
		}
		else
		{
			if ($_GET[page] >= ceil($group_array_cardinality/25))
			{
				$page = ceil($group_array_cardinality/25);
				$counter_end = $group_array_cardinality;
			}
			else
			{
				$page = $_GET[page];
				$counter_end = (25*$page)-1;
			}
			$counter_begin = (25*$page)-25;
		}
		
		if (is_array($group_array))
		{
			foreach ($group_array as $key => $value)
			{
				if ($counter >= $counter_begin and $counter <= $counter_end)
				{
					$column_array = array();
					
					$group 			= new Group($value);
					
					$paramquery = $_GET;
					$paramquery[action] = "detail";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
										
					$column_array[symbol][link] = $params;	
					$column_array[symbol][content] = "<img src='images/icons/groups.png' alt='' style='border: 0;' />";
					$column_array[name][link] = $params;	
					$column_array[name][content] = $group->get_name();
					$column_array[user] = $group->get_number_of_user_members();
					$column_array[ou] = OrganisationUnit::get_number_of_organisation_units_by_group_id($value);
					
					$paramquery = $_GET;
					$paramquery[action] = "delete";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					if ($value < 100)
					{
						$column_array[delete] = "<img src='images/icons/delete_group_na.png' alt='' style='border: 0;' />";
					}
					else
					{
						$column_array[delete][link] = $params;
						$column_array[delete][content] = "<img src='images/icons/delete_group.png' alt='' style='border: 0;' />";
					}
					array_push($content_array, $column_array);
				}
				$counter++;	
			}
		}
		else
		{
			$content_array = null;
			$table_io->override_last_line("<span class='italic'>No Groups Found!</span>");
		}
		
		$table_io->add_content_array($content_array);
		
		$template = new Template("template/user/admin/group/list.html");
		
		$paramquery = $_GET;
		$paramquery[action] = "add";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
		
		$template->set_var("table", $table_io->get_table($page ,$group_array_cardinality));	
		
		$template->output();
	}
	
	public static function create()
	{
		if ($_GET[nextpage] == 1)
		{
			$page_1_passed = true;
			
			if ($_POST[name])
			{
				if (Group::exist_name($_POST[name]) == true)
				{
					$page_1_passed = false;
					$error = "This groupname already exists";
				}
			}
			else
			{
				$page_1_passed = false;
				$error = "You must enter a groupname";
			}
		}
		else
		{
			$page_1_passed = false;
			$error = "";
		}

		if ($page_1_passed == false)
		{
			$template = new Template("template/user/admin/group/add.html");
			
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
			$paramquery = $_GET;
			unset($paramquery[nextpage]);
			unset($paramquery[action]);
			$params = http_build_query($paramquery);
			
			try
			{	
				$group = new Group($_POST[group]);
				
				$paramquery = $_GET;
				unset($paramquery[action]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($group->create($_POST[name]))
				{
					Common_IO::step_proceed($params, "Add Group", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Add Group", "Operation Failed" ,null);	
				}
			}
			catch (GroupCreationFailedException $e)
			{
				$error_io = new Error_IO($e, 3, 30, 1);
				$error_io->display_error();
			}
			catch (GroupAlreadyExistException $e)
			{
				$error_io = new Error_IO($e, 3, 30, 1);
				$error_io->display_error();
			}
		}
	}
	
	public static function delete()
	{
		if ($_GET[id])
		{
			$group_id = $_GET[id];
			$group = new Group($group_id);
			
			if ($_GET[sure] != "true")
			{
				$template = new Template("template/user/admin/group/delete.html");
				
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
								
				if ($group->delete())
				{							
					Common_IO::step_proceed($params, "Delete Group", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Group", "Operation Failed" ,null);
				}	
			}
		}
		else
		{
			$exception = new Exception("", 2);
			$error_io = new Error_IO($exception, 3, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function detail()
	{
		if ($_GET[id])
		{
			$group_id = $_GET[id];
			
			$template = new Template("template/user/admin/group/detail.html");
			
			$group = new Group($group_id);
			
			if ($group_id < 100)
			{
				$template->set_var("change_name", false);
			}
			else
			{
				$template->set_var("change_name", true);
			}
			
			$paramquery = $_GET;
			$paramquery[action] = "rename";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("name", $group->get_name());
			$template->set_var("rename_params", $params);
			
			
			$paramquery = $_GET;
			$paramquery[action] = "add_user";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("add_user_params", $params);	
			
			
			$user_array = Group::list_group_releated_users($group_id);
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
			$paramquery[action] = "add_organisation_unit";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("add_ou_params", $params);	
			
			$organisation_unit_array = OrganisationUnit::list_entries_by_group_id($group_id);
			$organisation_unit_content_array = array();
			
			$counter = 0;
			
			if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
			{
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
					
					$paramquery = $_GET;
					$paramquery[action] = "delete_organisation_unit";
					$paramquery[key] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$organisation_unit_content_array[$counter][name] = $organisation_unit->get_name();
					$organisation_unit_content_array[$counter][delete_params] = $params;
					
					$counter++;
				}
				$template->set_var("no_ou", false);
			}
			else
			{
				$template->set_var("no_ou", true);
			}
			
			$template->set_var("ou", $organisation_unit_content_array);
			
			
			$template->output();
		}
		else
		{
			$exception = new Exception("", 2);
			$error_io = new Error_IO($exception, 3, 40, 3);
			$error_io->display_error();
		}
	}

	public static function add_user()
	{
		if ($_GET[id])
		{			
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[user]))
				{
					$group = new Group($_POST[user]);
					if ($group->is_user_in_group($_GET[id]) == true)
					{
						$page_1_passed = false;
						$error = "This user is already member of the group.";
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
				$template = new Template("template/user/admin/group/add_user.html");
				
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
				$group = new Group($_GET[id]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($group->create_user_in_group($_POST[user]))
				{
					Common_IO::step_proceed($params, "Add User", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Add User", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 2);
			$error_io = new Error_IO($exception, 3, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function delete_user()
	{
		if ($_GET[id] and $_GET[key])
		{
			if ($_GET[sure] != "true")
			{
				$template = new Template("template/user/admin/group/delete_user.html");
				
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
				
				$group = new Group($_GET[id]);		
						
				if ($group->delete_user_from_group($_GET[key]))
				{							
					Common_IO::step_proceed($params, "Delete User", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete User", "Operation Failed" ,null);
				}			
			}
		}
		else
		{
			$exception = new Exception("", 2);
			$error_io = new Error_IO($exception, 3, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function add_organisation_unit()
	{
		if ($_GET[id])
		{		
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[ou]))
				{
					$organisation_unit = new OrganisationUnit($_POST[ou]);
					if ($organisation_unit->is_group_in_organisation_unit($_GET[id]) == true)
					{
						$page_1_passed = false;
						$error = "This organisation-unit is already member of the group.";
					}
					else
					{
						$page_1_passed = true;
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must select an organisation unit.";
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
				$template = new Template("template/user/admin/group/add_organisation_unit.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$organisation_unit_array = OrganisationUnit::list_entries();
					
				$result = array();
				$counter = 0;
				
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $organisation_unit->get_name();
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$organisation_unit = new OrganisationUnit($_POST[ou]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($organisation_unit->create_group_in_organisation_unit($_GET[id]))
				{
					Common_IO::step_proceed($params, "Add Organisation Unit", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Add Organisation Unit", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 2);
			$error_io = new Error_IO($exception, 3, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function delete_organisation_unit()
	{
		if ($_GET[id] and $_GET[key])
		{
			if ($_GET[sure] != "true")
			{
				$template = new Template("template/user/admin/group/delete_organisation_unit.html");
				
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
				
				$organisation_unit = new OrganisationUnit($_GET[key]);	
						
				if ($organisation_unit->delete_group_from_organisation_unit($_GET[id]))
				{							
					Common_IO::step_proceed($params, "Delete Organisation Unit", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Organisation Unit", "Operation Failed" ,null);
				}			
			}
		}
		else
		{
			$exception = new Exception("", 2);
			$error_io = new Error_IO($exception, 3, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function rename()
	{
		if ($_GET[id])
		{
			$group = new Group($_GET[id]);
						
			if ($_GET[nextpage] == 1)
			{
				if ($_POST[name])
				{
					if (Group::exist_name($_POST[name]) == true)
					{
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
				$template = new Template("template/user/admin/group/rename.html");
				
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
					$template->set_var("name", $group->get_name());
				}
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($group->set_name($_POST[name]))
				{
					Common_IO::step_proceed($params, "Rename User", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Rename User", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			$exception = new Exception("", 2);
			$error_io = new Error_IO($exception, 3, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function handler()
	{
		try
		{
			if ($_GET[id])
			{
				if (Group::exist_group($_GET[id]) == false)
				{
					throw new GroupNotFoundException("",3);
				}
			}
		
			switch($_GET[action]):
				case "add":
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
				
				case "add_organisation_unit":
					self::add_organisation_unit();
				break;
				
				case "delete_organisation_unit":
					self::delete_organisation_unit();
				break;
				
				case "rename":
					self::rename();
				break;
				
				default:
					self::home();
				break;
			endswitch;
		}
		catch (GroupNotFoundException $e)
		{
			$error_io = new Error_IO($e, 3, 40, 1);
			$error_io->display_error();
		}
	}
	
	public static function home_dialog()
	{
		$template = new Template("template/user/admin/group/home_dialog.html");
	
		$paramquery 			= array();
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] 		= $_GET[nav];
		$paramquery[run] 		= "organisation";
		$paramquery[dialog] 	= "groups";
		$paramquery[action] 	= "add";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("group_add_params", $params);
		$template->set_var("group_amount", Group::count_groups());
		
		return $template->get_string();
	}

	public static function get_icon()
	{
		return "groups.png";
	}
}

?>