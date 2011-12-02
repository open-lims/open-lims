<?php
/**
 * @package organisation_unit
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
			$organisation_unit_child_array = $organisation_unit->get_organisation_unit_children();
			
			if(is_array($organisation_unit_child_array) and count($organisation_unit_child_array) >= 1)
			{
				foreach($organisation_unit_child_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
									
					$content_array[self::$home_list_counter][padding] = 0.5 * $layer;
					$content_array[self::$home_list_counter][icon] = $organisation_unit->get_icon();
					$content_array[self::$home_list_counter][name] = $organisation_unit->get_name();
					$content_array[self::$home_list_counter][type] = $organisation_unit->get_type_name();				
					
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
				
				
				$content_array[self::$home_list_counter][padding] = 0;
				$content_array[self::$home_list_counter][icon] = $organisation_unit->get_icon();
				$content_array[self::$home_list_counter][name] = $organisation_unit->get_name();
				$content_array[self::$home_list_counter][type] = $organisation_unit->get_type_name();
				
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
		
		$template = new Template("template/organisation_unit/admin/organisation_unit/list.html");
		
		$paramquery = $_GET;
		$paramquery[action] = "add";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
		
		$template->set_var("ou", $content_array);
		
		$template->output();
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function create()
	{
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
				$template = new Template("template/organisation_unit/admin/organisation_unit/add.html");
				
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
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @todo dependency exception
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function delete()
	{
		if ($_GET[id])
		{
			$organisation_unit_id = $_GET[id];
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			
			if ($organisation_unit->check_delete_dependencies() == true)
			{
				if ($_GET[sure] != "true")
				{
					$template = new Template("template/organisation_unit/admin/organisation_unit/delete.html");
					
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
				
			}
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function detail()
	{
		global $user;
		
		if ($_GET[id])
		{
			$organisation_unit_id = $_GET[id];
		
			$template = new Template("template/organisation_unit/admin/organisation_unit/detail.html");
			
			if ($user->is_admin())
			{
				$template->set_var("is_admin", true);
			}
			else
			{
				$template->set_var("is_admin", false);
			}
			
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			
			$template->set_var("name", $organisation_unit->get_name());
			$template->set_var("title", $organisation_unit->get_name());
			
			
			$paramquery = $_GET;
			$paramquery[action] = "rename";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("name_params", $params);	
			
			
			// OWNERS
			
			$organisation_unit_owner_array = $organisation_unit->list_owners(12);
			
			if (is_array($organisation_unit_owner_array) and count($organisation_unit_owner_array) >= 1)
			{
				$ou_owners = null;
				foreach ($organisation_unit_owner_array as $key => $value)
				{
					$owner = new User($value);
					
					if ($ou_owners)
					{
						$ou_owners .= ", ".$owner->get_full_name(true);
					}
					else
					{
						$ou_owners .= $owner->get_full_name(true);	
					}
				}
			}
			else
			{
				$ou_owners = "<span class='italic'>none</span>";
			}
			
			$number_of_owners = $organisation_unit->get_number_of_owners();
			
			if ($number_of_owners > 12)
			{
				$number_of_owners = $number_of_owners - 12;
				$ou_owners .= " (+ ".$number_of_owners." more)";
			}
			
			$template->set_var("owners", $ou_owners);
			
			
			// LEADERS
			
			$organisation_unit_leader_array = $organisation_unit->list_leaders(12);
			
			if (is_array($organisation_unit_leader_array) and count($organisation_unit_leader_array) >= 1)
			{
				$ou_leaders = null;
				foreach ($organisation_unit_leader_array as $key => $value)
				{
					$leader = new User($value);
										
					if ($ou_leaders)
					{
						$ou_leaders .= ", ".$leader->get_full_name(true);
					}
					else
					{
						$ou_leaders .= $leader->get_full_name(true);	
					}
				}
			}
			else
			{
				$ou_leaders = "<span class='italic'>none</span>";
			}
			
			$number_of_leaders = $organisation_unit->get_number_of_leaders();
			
			if ($number_of_leaders > 12)
			{
				$number_of_leaders = $number_of_leaders - 12;
				$ou_leaders .= " (+ ".$number_of_leaders." more)";
			}
			
			$template->set_var("leaders", $ou_leaders);
			
			
			// MEMBERS
			
			$organisation_unit_member_array = $organisation_unit->list_members(12);
			
			if (is_array($organisation_unit_member_array) and count($organisation_unit_member_array) >= 1)
			{
				$ou_members = null;
				foreach ($organisation_unit_member_array as $key => $value)
				{
					$member = new User($value);
					
					if ($ou_members)
					{
						$ou_members .= ", ".$member->get_full_name(true);
					}
					else
					{
						$ou_members .= $member->get_full_name(true);	
					}
				}
			}
			else
			{
				$ou_members = "<span class='italic'>none</span>";
			}

			$number_of_users = $organisation_unit->get_number_of_users();
			
			if ($number_of_users > 12)
			{
				$number_of_users = $number_of_users - 12;
				$ou_members .= " (+ ".$number_of_users." more)";
			}
			
			$template->set_var("members", $ou_members);
			
			
			// QUALITY MANAGERS
			
			$organisation_unit_quality_manager_array = $organisation_unit->list_quality_managers(12);
			
			if (is_array($organisation_unit_quality_manager_array) and count($organisation_unit_quality_manager_array) >= 1)
			{
				$ou_quality_managers = null;
				foreach ($organisation_unit_quality_manager_array as $key => $value)
				{
					$quality_manager = new User($value);
					
					if ($ou_quality_managers)
					{
						$ou_quality_managers .= ", ".$quality_manager->get_full_name(true);
					}
					else
					{
						$ou_quality_managers .= $quality_manager->get_full_name(true);	
					}
				}
			}
			else
			{
				$ou_quality_managers = "<span class='italic'>none</span>";
			}
			
			$number_of_quality_managers = $organisation_unit->get_number_of_quality_managers();
			
			if ($number_of_quality_managers > 12)
			{
				$number_of_quality_managers = $number_of_quality_managers - 12;
				$ou_quality_managers .= " (+ ".$number_of_quality_managers." more)";
			}
			
			$template->set_var("quality_managers", $ou_quality_managers);
			
			
			// GROUPS
			
			$organisation_unit_group_array = $organisation_unit->list_groups(12);
			
			if (is_array($organisation_unit_group_array) and count($organisation_unit_group_array) >= 1)
			{
				$ou_groups = null;
				foreach ($organisation_unit_group_array as $key => $value)
				{
					$group = new Group($value);
					
					if ($ou_groups)
					{
						$ou_groups .= ", ".$group->get_name();
					}
					else
					{
						$ou_groups .= $group->get_name();	
					}
				}
			}
			else
			{
				$ou_groups = "<span class='italic'>none</span>";
			}
			
			$number_of_groups = $organisation_unit->get_number_of_groups();
						
			if ($number_of_groups > 12)
			{
				$number_of_groups = $number_of_groups - 12;
				$ou_groups .= " (+ ".$number_of_groups." more)";
			}
			
			$template->set_var("groups",$ou_groups);
			
			
			
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}

	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function detail_member()
	{
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
			
			require_once("core/modules/base/common/io/user_common.io.php");
			
			$template = new Template("template/organisation_unit/admin/organisation_unit/detail_member.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[id]);
			$template->set_var("ADD_DIALOG", UserCommonIO::user_select_dialog());
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function detail_group()
	{
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
			
			require_once("core/modules/base/io/user.io.php");
			
			$template = new Template("template/organisation_unit/admin/organisation_unit/detail_group.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[id]);
			$template->set_var("ADD_DIALOG", UserIO::group_select_dialog());
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}

	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function detail_owner()
	{
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
			
			require_once("core/modules/base/common/io/user_common.io.php");
			
			$template = new Template("template/organisation_unit/admin/organisation_unit/detail_owner.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[id]);
			$template->set_var("ADD_DIALOG", UserCommonIO::user_select_dialog());
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}	
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function detail_leader()
	{
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
			
			require_once("core/modules/base/common/io/user_common.io.php");
			
			$template = new Template("template/organisation_unit/admin/organisation_unit/detail_leader.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[id]);
			$template->set_var("ADD_DIALOG", UserCommonIO::user_select_dialog());
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function detail_quality_manager()
	{
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
			
			require_once("core/modules/base/common/io/user_common.io.php");
			
			$template = new Template("template/organisation_unit/admin/organisation_unit/detail_quality_manager.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[id]);
			$template->set_var("ADD_DIALOG", UserCommonIO::user_select_dialog());
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function detail_address()
	{
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
			
			$template = new Template("template/organisation_unit/admin/organisation_unit/detail_address.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("CLIENT", SystemHandler::module_exists("client"));
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function rename()
	{
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
				$template = new Template("template/organisation_unit/admin/organisation_unit/rename.html");
				
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
			throw new OrganisationUnitIDMissingException();
		}
	}

	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function upwards()
	{
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($organisation_unit->position_upwards())
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function downwards()
	{
		if ($_GET[id])
		{
			$organisation_unit = new OrganisationUnit($_GET[id]);
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($organisation_unit->position_downwards())
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function toogle_visible()
	{
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
				Common_IO::step_proceed($params, "Upwards", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	public static function handler()
	{
		if ($_GET[id])
		{
			if ($_GET[action] != "delete" and 
				$_GET[action] != "add_child" and 
				$_GET[action] != "upwards"  and 
				$_GET[action] != "downwards")
			{
				$tab_io = new Tab_IO();
			
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				$params = http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("general", "General", $params, false);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail_owner";
				$params = http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("owners", "Owners", $params, false);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail_leader";
				$params = http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("leaders", "Leaders", $params, false);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail_member";
				$params = http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("members", "Members", $params, false);  
				
				$paramquery = $_GET;
				$paramquery[action] = "detail_qm";
				$params = http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("qm", "Q.-Managers", $params, false);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail_group";
				$params = http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("groups", "Groups", $params, false);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail_address";
				$params = http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("addresses", "Addresses", $params, false);
				
				switch($_GET[action]):

					case "detail_owner":
						$tab_io->activate("owners");
					break;
					
					case "detail_leader":
						$tab_io->activate("leaders");
					break;
					
					case "detail_member":
						$tab_io->activate("members");
					break;
					
					case "detail_qm":
						$tab_io->activate("qm");
					break;
					
					case "detail_group":
						$tab_io->activate("groups");
					break;
					
					case "detail_address":
						$tab_io->activate("addresses");
					break;
					
					default:
						$tab_io->activate("general");
					break;
				
				endswitch;
					
				$tab_io->output();
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

			case "detail_owner":
				self::detail_owner();
			break;
			
			case "detail_leader":
				self::detail_leader();
			break;
			
			case "detail_member":
				self::detail_member();
			break;
			
			case "detail_qm":
				self::detail_quality_manager();
			break;
			
			case "detail_group":
				self::detail_group();
			break;
			
			case "detail_address":
				self::detail_address();
			break;
			
			case "rename":
				self::rename();
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
	
	public static function home_dialog()
	{
		$template = new Template("template/organisation_unit/admin/organisation_unit/home_dialog.html");
	
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

	public static function get_icon()
	{
		return "organisation_unit.png";
	}
}

?>