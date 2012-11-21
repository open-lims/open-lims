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
 * Group Admin IO Class
 * @package base
 */
class AdminGroupIO
{
	public static function home()
	{
		$list = new List_IO("GroupAdministration", "ajax.php?nav=base", "admin_list_groups", "admin_count_groups", null, "GroupAdministration");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column("Name","name",true,null);
		$list->add_column("Users","users",false,null);
		$list->add_column("D","delete",false,"16px");
		
		$template = new HTMLTemplate("base/user/admin/group/list.html");
		
		$paramquery = $_GET;
		$paramquery['action'] = "add";
		unset($paramquery['nextpage']);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function create()
	{
		if ($_GET['nextpage'] == 1)
		{
			$page_1_passed = true;
			
			if ($_POST['name'])
			{
				if (Group::exist_name($_POST['name']) == true)
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
			$template = new HTMLTemplate("base/user/admin/group/add.html");
			
			$paramquery = $_GET;
			$paramquery['nextpage'] = "1";
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
						
			if ($_POST['name'])
			{
				$template->set_var("name", $_POST['name']);
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
			unset($paramquery['nextpage']);
			unset($paramquery['action']);
			$params = http_build_query($paramquery);
			
				
			$group = new Group($_POST['group']);
			
			$paramquery = $_GET;
			unset($paramquery['action']);
			unset($paramquery['nextpage']);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($group->create($_POST['name']))
			{
				Common_IO::step_proceed($params, "Add Group", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Add Group", "Operation Failed" ,null);	
			}
		}
	}
	
	/**
	 * @throws GroupIDMissingException
	 */
	public static function delete()
	{
		if ($_GET['id'])
		{
			$group_id = $_GET['id'];
			$group = new Group($group_id);
			
			if ($_GET['sure'] != "true")
			{
				$template = new HTMLTemplate("base/user/admin/group/delete.html");
				
				$paramquery = $_GET;
				$paramquery['sure'] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery['sure']);
				unset($paramquery['action']);
				unset($paramquery['id']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery['sure']);
				unset($paramquery['action']);
				unset($paramquery['id']);
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
			throw new GroupIDMissingException();
		}
	}
	
	/**
	 * @throws BaseModuleDialogMethodNotFoundException
	 * @throws BaseModuleDialogClassNotFoundException
	 * @throws BaseModuleDialogFileNotFoundException
	 * @throws GroupIDMissingException
	 */
	public static function detail()
	{
		if ($_GET['id'])
		{
			$tab_io = new Tab_IO();
	
			$paramquery = $_GET;
			unset($paramquery['tab']);
			$params = http_build_query($paramquery,'','&#38;');
			
			$tab_io->add("detail", "Group Details", $params, false);
			
			
			$paramquery = $_GET;
			$paramquery['tab'] = "users";
			$params = http_build_query($paramquery,'','&#38;');
			
			$tab_io->add("users", "Users", $params, false);
			
			
			$module_dialog_array = ModuleDialog::list_dialogs_by_type("group_admin_detail");
			
			if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
			{
				foreach ($module_dialog_array as $key => $value)
				{
					$paramquery = $_GET;
					$paramquery['tab']			= "dialog";
					$paramquery['sub_dialog']	= $value['internal_name'];
					$params 					= http_build_query($paramquery,'','&#38;');
					
					$tab_io->add($value['internal_name'], $value['display_name'], $params, false);
				}
			}
			
			switch($_GET['tab']):
				
				case "users":
					$tab_io->activate("users");
				break;
			
				case "dialog":
					$tab_io->activate($_GET['sub_dialog']);
				break;
				
				default:
					$tab_io->activate("detail");
				break;
			
			endswitch;
				
			$tab_io->output();
			
			
			switch($_GET['tab']):

				case "users":
					self::detail_users();
				break;
				
				case "dialog":
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("group_admin_detail", $_GET['sub_dialog']);
						
					if (file_exists($module_dialog['class_path']))
					{
						require_once($module_dialog['class_path']);
						
						if (class_exists($module_dialog['class']))
						{
							if (method_exists($module_dialog['class'], $module_dialog['method']))
							{
								$module_dialog['class']::$module_dialog['method']($_GET['id']);
							}
							else
							{
								throw new BaseModuleDialogMethodNotFoundException();
							}
						}
						else
						{
							throw new BaseModuleDialogClassNotFoundException();
						}
					}
					else
					{
						throw new BaseModuleDialogFileNotFoundException();
					}
				break;
				
				default:
					self::detail_home();
				break;
				
			endswitch;
		}
		else
		{
			throw new GroupIDMissingException();
		}
	}

	private static function detail_home()
	{
		$group_id = $_GET['id'];
		
		$template = new HTMLTemplate("base/user/admin/group/detail.html");
		
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
		$paramquery['action'] = "rename";
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("name", $group->get_name());
		$template->set_var("rename_params", $params);

		$template->output();
	}
	
	/**
	 * @todo rebuild with List and JS operations
	 */
	private static function detail_users()
	{
		$group_id = $_GET['id'];
		$group = new Group($group_id);
		
		$template = new HTMLTemplate("base/user/admin/group/detail_user.html");
		
		$template->set_var("name", $group->get_name());
		
		$paramquery = $_GET;
		$paramquery['action'] = "add_user";
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
				$paramquery['action'] = "delete_user";
				$paramquery['key'] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$user_content_array[$counter]['username'] = $user->get_username();
				$user_content_array[$counter]['fullname'] = $user->get_full_name(false);
				$user_content_array[$counter]['delete_params'] = $params;
				
				$counter++;
			}
			$template->set_var("no_user", false);
		}
		else
		{
			$template->set_var("no_user", true);
		}
		
		$template->set_var("user", $user_content_array);
		
		$template->output();
	}
	
	/**
	 * @throws GroupIDMissingException
	 */
	public static function add_user()
	{
		if ($_GET['id'])
		{			
			if ($_GET['nextpage'] == 1)
			{
				if (is_numeric($_POST['user']))
				{
					$group = new Group($_GET['id']);
					if ($group->is_user_in_group($_POST['user']) == true)
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
			elseif($_GET['nextpage'] > 1)
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
				$template = new HTMLTemplate("base/user/admin/group/add_user.html");
				
				$paramquery = $_GET;
				$paramquery['nextpage'] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$user_array = User::list_entries();
					
				$result = array();
				$counter = 0;
				
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					$result[$counter]['value'] = $value;
					$result[$counter]['content'] = $user->get_username()." (".$user->get_full_name(false).")";
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$group = new Group($_GET['id']);
				
				$paramquery = $_GET;
				$paramquery['action'] = "detail";
				unset($paramquery['nextpage']);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($group->create_user_in_group($_POST['user']))
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
			throw new GroupIDMissingException();
		}
	}
	
	/**
	 * @todo new exception for missing key (or rebuild)
	 * @throws GroupIDMissingException
	 */
	public static function delete_user()
	{
		if ($_GET['id'])
		{
			if ($_GET['key'])
			{
				if ($_GET['sure'] != "true")
				{
					$template = new HTMLTemplate("base/user/admin/group/delete_user.html");
					
					$paramquery = $_GET;
					$paramquery['sure'] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery['key']);
					$paramquery['action'] = "detail";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery['key']);
					unset($paramquery['sure']);
					$paramquery['action'] = "detail";
					$params = http_build_query($paramquery);
					
					$group = new Group($_GET['id']);		
							
					if ($group->delete_user_from_group($_GET['key']))
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
				
			}
		}
		else
		{
			throw new GroupIDMissingException();
		}
	}
	
	/**
	 * @todo IMPORTANT: remove bad dependency, replace with JS
	 */
	public static function add_organisation_unit()
	{
		if ($_GET['id'])
		{		
			if ($_GET['nextpage'] == 1)
			{
				if (is_numeric($_POST['ou']))
				{
					$organisation_unit = new OrganisationUnit($_POST['ou']);
					if ($organisation_unit->is_group_in_organisation_unit($_GET['id']) == true)
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
			elseif($_GET['nextpage'] > 1)
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
				$template = new HTMLTemplate("base/user/admin/group/add_organisation_unit.html");
				
				$paramquery = $_GET;
				$paramquery['nextpage'] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$organisation_unit_array = OrganisationUnit::list_entries();
					
				$result = array();
				$counter = 0;
				
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
					$result[$counter]['value'] = $value;
					$result[$counter]['content'] = $organisation_unit->get_name();
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$organisation_unit = new OrganisationUnit($_POST['ou']);
				
				$paramquery = $_GET;
				$paramquery['action'] = "detail";
				unset($paramquery['nextpage']);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($organisation_unit->create_group_in_organisation_unit($_GET['id']))
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
			throw new GroupIDMissingException();
		}
	}
	
	/**
	 * @todo IMPORTANT: remove bad dependency, replace with JS
	 */
	public static function delete_organisation_unit()
	{
		if ($_GET['id'] and $_GET['key'])
		{
			if ($_GET['sure'] != "true")
			{
				$template = new HTMLTemplate("base/user/admin/group/delete_organisation_unit.html");
				
				$paramquery = $_GET;
				$paramquery['sure'] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery['key']);
				$paramquery['action'] = "detail";
				$params = http_build_query($paramquery);
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery['key']);
				unset($paramquery['sure']);
				$paramquery['action'] = "detail";
				$params = http_build_query($paramquery);
				
				$organisation_unit = new OrganisationUnit($_GET['key']);	
						
				if ($organisation_unit->delete_group_from_organisation_unit($_GET['id']))
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
			throw new GroupIDMissingException();
		}
	}
	
	/**
	 * @throws GroupIDMissingException
	 */
	public static function rename()
	{
		if ($_GET['id'])
		{
			$group = new Group($_GET['id']);
						
			if ($_GET['nextpage'] == 1)
			{
				if ($_POST['name'])
				{
					if (Group::exist_name($_POST['name']) == true)
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
			elseif($_GET['nextpage'] > 1)
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
				$template = new HTMLTemplate("base/user/admin/group/rename.html");
				
				$paramquery = $_GET;
				$paramquery['nextpage'] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				$template->set_var("error",$error);
				
				if ($_POST['username'])
				{
					$template->set_var("name", $_POST['name']);
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
				$paramquery['action'] = "detail";
				unset($paramquery['nextpage']);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($group->set_name($_POST['name']))
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
			throw new GroupIDMissingException();
		}
	}
	
	public static function handler()
	{		
		switch($_GET['action']):
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
	
	public static function home_dialog()
	{
		$template = new HTMLTemplate("base/user/admin/group/home_dialog.html");
	
		$paramquery 				= array();
		$paramquery['username'] 	= $_GET['username'];
		$paramquery['session_id'] 	= $_GET['session_id'];
		$paramquery['nav'] 			= $_GET['nav'];
		$paramquery['run'] 			= "organisation";
		$paramquery['dialog'] 		= "groups";
		$paramquery['action'] 		= "add";
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