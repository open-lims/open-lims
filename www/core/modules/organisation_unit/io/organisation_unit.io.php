<?php
/**
 * @package organisation_unit
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
 * Organiser IO Class
 * @package organisation_unit
 */
class OrganisationUnitIO
{
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function detail()
	{
		global $user;
		
		if ($_GET['ou_id'])
		{
			$organisation_unit = new OrganisationUnit($_GET['ou_id']);
			
			$template = new HTMLTemplate("organisation_unit/organisation_unit_detail.html");
			
			$template->set_var("title",$organisation_unit->get_name());
			
			
			if ($user->is_admin() == true)
			{
				$paramquery = $_GET;
				$paramquery['nav'] = "administration";
				$paramquery['run'] = "organisation_unit";
				$paramquery['action'] = "detail";
				$paramquery['id'] = $_GET['ou_id'];
				unset($paramquery['ou_id']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("admin_params", $params);
				
				$template->set_var("is_owner", true);
			}
			else
			{
				$template->set_var("is_owner", false);
			}
			
			
			// OWNERS
			
			$organisation_unit_owner_array = $organisation_unit->list_owners(12);
			
			if (is_array($organisation_unit_owner_array) and count($organisation_unit_owner_array) >= 1)
			{
				$ou_owners = null;
				foreach ($organisation_unit_owner_array as $key => $value)
				{
					if ($value == $user->get_user_id())
					{
						$paramquery = $_GET;
						$paramquery['nav'] = "administration";
						$paramquery['run'] = "organisation_unit";
						$paramquery['action'] = "detail";
						$paramquery['id'] = $_GET['ou_id'];
						unset($paramquery['ou_id']);
						$params = http_build_query($paramquery,'','&#38;');
						
						$template->set_var("admin_params", $params);
						
						$template->set_var("is_owner", true);
					}
					
					$owner = new User($value);
					
					$owner_paramquery = $_GET;
					$owner_paramquery['run'] = "common_dialog";
					$owner_paramquery['dialog'] = "user_detail";
					$owner_paramquery['id'] = $value;
					$owner_params = http_build_query($owner_paramquery, '', '&#38;');
					
					if ($ou_owners)
					{
						$ou_owners .= ", <a href='index.php?".$owner_params."'>".$owner->get_full_name(true)."</a>";
					}
					else
					{
						$ou_owners .= "<a href='index.php?".$owner_params."'>".$owner->get_full_name(true)."</a>";	
					}
				}
			}
			else
			{
				$ou_owners = "<span class='italic'>none</span>";
			}
			
			$number_of_owners = $organisation_unit->get_number_of_owners();
			
			$owner_list_paramquery = $_GET;
			$owner_list_paramquery['run'] = "common_dialog";
			$owner_list_paramquery['dialog'] = "ou_detail";
			$owner_list_paramquery['action'] = "list_owners";
			$owner_list_paramquery['ou_id'] = $_GET['ou_id'];
			$owner_list_params = http_build_query($owner_list_paramquery, '', '&#38;');
			
			if ($number_of_owners > 12)
			{
				$number_of_owners = $number_of_owners - 12;
				$ou_owners .= " (+ <a href='index.php?".$owner_list_params."'>".$number_of_owners." more</a>)";
			}
			else
			{
				$ou_owners .= " (<a href='index.php?".$owner_list_params."'>list</a>)";
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
					
					$leader_paramquery = $_GET;
					$leader_paramquery['run'] = "common_dialog";
					$leader_paramquery['dialog'] = "user_detail";
					$leader_paramquery['id'] = $value;
					$leader_params = http_build_query($leader_paramquery, '', '&#38;');
					
					if ($ou_leaders)
					{
						$ou_leaders .= ", <a href='index.php?".$leader_params."'>".$leader->get_full_name(true)."</a>";
					}
					else
					{
						$ou_leaders .= "<a href='index.php?".$leader_params."'>".$leader->get_full_name(true)."</a>";	
					}
				}
			}
			else
			{
				$ou_leaders = "<span class='italic'>none</span>";
			}
			
			$number_of_leaders = $organisation_unit->get_number_of_leaders();
			
			$leader_list_paramquery = $_GET;
			$leader_list_paramquery['run'] = "common_dialog";
			$leader_list_paramquery['dialog'] = "ou_detail";
			$leader_list_paramquery['action'] = "list_leaders";
			$leader_list_paramquery['ou_id'] = $_GET['ou_id'];
			$leader_list_params = http_build_query($leader_list_paramquery, '', '&#38;');
			
			if ($number_of_leaders > 12)
			{
				$number_of_leaders = $number_of_leaders - 12;
				$ou_leaders .= " (+ <a href='index.php?".$leader_list_params."'>".$number_of_leaders." more</a>)";
			}
			else
			{
				$ou_leaders .= " (<a href='index.php?".$leader_list_params."'>list</a>)";
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
					
					$member_paramquery = $_GET;
					$member_paramquery['run'] = "common_dialog";
					$member_paramquery['dialog'] = "user_detail";
					$member_paramquery['id'] = $value;
					$member_params = http_build_query($member_paramquery, '', '&#38;');
					
					if ($ou_members)
					{
						$ou_members .= ", <a href='index.php?".$member_params."'>".$member->get_full_name(true)."</a>";
					}
					else
					{
						$ou_members .= "<a href='index.php?".$member_params."'>".$member->get_full_name(true)."</a>";	
					}
				}
			}
			else
			{
				$ou_members = "<span class='italic'>none</span>";
			}
			
			$number_of_users = $organisation_unit->get_number_of_users();
			
			$member_list_paramquery = $_GET;
			$member_list_paramquery['run'] = "common_dialog";
			$member_list_paramquery['dialog'] = "ou_detail";
			$member_list_paramquery['action'] = "list_members";
			$member_list_paramquery['ou_id'] = $_GET['ou_id'];
			$member_list_params = http_build_query($member_list_paramquery, '', '&#38;');
			
			if ($number_of_users > 12)
			{
				$number_of_users = $number_of_users - 12;
				$ou_members .= " (+ <a href='index.php?".$member_list_params."'>".$number_of_users." more</a>)";
			}
			else
			{
				$ou_members .= " (<a href='index.php?".$member_list_params."'>list</a>)";
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
					
					$quality_manager_paramquery = $_GET;
					$quality_manager_paramquery['run'] = "common_dialog";
					$quality_manager_paramquery['dialog'] = "user_detail";
					$quality_manager_paramquery['id'] = $value;
					$quality_manager_params = http_build_query($quality_manager_paramquery, '', '&#38;');
					
					if ($ou_quality_managers)
					{
						$ou_quality_managers .= ", <a href='index.php?".$quality_manager_params."'>".$quality_manager->get_full_name(true)."</a>";
					}
					else
					{
						$ou_quality_managers .= "<a href='index.php?".$quality_manager_params."'>".$quality_manager->get_full_name(true)."</a>";	
					}
				}
			}
			else
			{
				$ou_quality_managers = "<span class='italic'>none</span>";
			}
			
			$number_of_quality_managers = $organisation_unit->get_number_of_quality_managers();
			
			$quality_manager_list_paramquery = $_GET;
			$quality_manager_list_paramquery['run'] = "common_dialog";
			$quality_manager_list_paramquery['dialog'] = "ou_detail";
			$quality_manager_list_paramquery['action'] = "list_quality_managers";
			$quality_manager_list_paramquery['ou_id'] = $_GET['ou_id'];
			$quality_manager_list_params = http_build_query($quality_manager_list_paramquery, '', '&#38;');
			
			if ($number_of_quality_managers > 12)
			{
				$number_of_quality_managers = $number_of_quality_managers - 12;
				$ou_quality_managers .= " (+ <a href='index.php?".$quality_manager_list_params."'>".$number_of_quality_managers." more</a>)";
			}
			else
			{
				$ou_quality_managers .= " (<a href='index.php?".$quality_manager_list_params."'>list</a>)";
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
					
					$group_paramquery = $_GET;
					$group_paramquery['run'] = "common_dialog";
					$group_paramquery['dialog'] = "group_detail";
					$group_paramquery['id'] = $value;
					$group_params = http_build_query($group_paramquery, '', '&#38;');
					
					if ($ou_groups)
					{
						$ou_groups .= ", <a href='index.php?".$group_params."'>".$group->get_name()."</a>";
					}
					else
					{
						$ou_groups .= "<a href='index.php?".$group_params."'>".$group->get_name()."</a>";	
					}
				}
			}
			else
			{
				$ou_groups = "<span class='italic'>none</span>";
			}
			
			$number_of_groups = $organisation_unit->get_number_of_groups();
			
			$group_list_paramquery = $_GET;
			$group_list_paramquery['run'] = "common_dialog";
			$group_list_paramquery['dialog'] = "ou_detail";
			$group_list_paramquery['action'] = "list_groups";
			$group_list_paramquery['ou_id'] = $_GET['ou_id'];
			$group_list_params = http_build_query($group_list_paramquery, '', '&#38;');
			
			if ($number_of_groups > 12)
			{
				$number_of_groups = $number_of_groups - 12;
				$ou_groups .= " (+ <a href='index.php?".$group_list_params."'>".$number_of_groups." more</a>)";
			}
			else
			{
				$ou_groups .= " (<a href='index.php?".$group_list_params."'>list</a>)";
			}
			
			$template->set_var("groups",$ou_groups);
			
			
			$module_link_array = ModuleLink::list_links_by_type("ou_detail_buttons");
			
			if (is_array($module_link_array) and count($module_link_array) >= 1)
			{
				$content_array = array();
				$counter = 0;
				
				foreach ($module_link_array as $key => $value)
				{
					$button_template = new HTMLTemplate($value['file']);
				
					$button_paramquery = array();
					$button_paramquery['username'] = $_GET['username'];
					$button_paramquery['session_id'] = $_GET['session_id'];
					
					if (is_array($value['array']) and count($value['array']) >= 1)
					{
						foreach ($value['array'] as $array_key => $array_value)
						{
							if (strpos($array_value, "%") === 0 and strpos($array_value, "%", 1) !== false)
							{
								$array_value_key = strtolower(str_replace("%","", $array_value));
								if ($_GET[$array_value_key])
								{
									$button_paramquery[$array_key] = $_GET[$array_value_key];
								}
							}
							else
							{
								$button_paramquery[$array_key] = $array_value;
							}
						}
					}
					
					$button_params = http_build_query($button_paramquery,'','&#38;');
					$button_template->set_var("params", $button_params);
					
					$content_array[$counter]['content'] = $button_template->get_string();
					$counter++;
				}
				
				$template->set_var("OU_FOOTER_ARRAY" ,$content_array);
			}
	
			$template->output();
			
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}

	public static function list_user_related_organisation_units()
	{
		global $user;
	
		$argument_array = array();
		$argument_array[0][0] = "user_id";
		$argument_array[0][1] = $user->get_user_id();;
		
		$list = new List_IO("OrganisationUnitUserHasOUList", "ajax.php?nav=organisation_unit", "list_organisation_units_by_user_id", "count_organisation_units_by_user_id", $argument_array, "OrganisationUnitUserHasOU");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column("Name","name",true,null);
		$list->add_column("My Status/Role","mystatus",false,null);
		
		$template = new HTMLTemplate("organisation_unit/user_related_organisation_units.html");
		
		$template->set_var("list", $list->get_list());

		$template->output();
	}
	
	/**
	 * @todo rebuild with List and JS operations
	 * @todo move to admin
	 * @throws UserIDMissingException
	 */
	public static function list_user_admin_organisation_units($user_id)
	{
		if (is_numeric($user_id))
		{
			$template = new HTMLTemplate("organisation_unit/admin/dialog/list_user_admin.html");
			
			$current_user = new User($user_id);
			$template->set_var("username", $current_user->get_username());
			$template->set_var("fullname", $current_user->get_full_name(false));
			
			$paramquery = $_GET;
			$paramquery['action'] = "add_organisation_unit";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("add_ou_params", $params);	
			
			$organisation_unit_array = OrganisationUnit::list_entries_by_user_id($user_id);
			$organisation_unit_content_array = array();
			
			$counter = 0;
			
			if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
			{
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
					
					$paramquery = $_GET;
					$paramquery['action'] = "delete_organisation_unit";
					$paramquery['key'] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$organisation_unit_content_array[$counter]['name'] = $organisation_unit->get_name();
					$organisation_unit_content_array[$counter]['delete_params'] = $params;
					
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
			throw new UserIDMissingException();
		}
	}
	
	/**
	 * @todo rebuild with List and JS operations
	 * @todo move to admin
	 * @throws GroupIDMissingException
	 */
	public static function list_group_admin_organisation_units($group_id)
	{
		if (is_numeric($group_id))
		{
			$template = new HTMLTemplate("organisation_unit/admin/dialog/list_group_admin.html");
			
			$paramquery = $_GET;
			$paramquery['action'] = "add_organisation_unit";
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
					$paramquery['action'] = "delete_organisation_unit";
					$paramquery['key'] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$organisation_unit_content_array[$counter]['name'] = $organisation_unit->get_name();
					$organisation_unit_content_array[$counter]['delete_params'] = $params;
					
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
			throw new GroupIDMissingException();
		}
	}
	
	/**
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function list_owners()
	{
		if ($_GET['ou_id'])
		{
			$organisation_unit = new OrganisationUnit($_GET['ou_id']);
			
			$argument_array = array();
			$argument_array[0][0] = "organisation_unit_id";
			$argument_array[0][1] = $_GET['ou_id'];
			
			$list = new List_IO("OrganisationUnitListOwners", "ajax.php?nav=organisation_unit", "list_owners", "count_owners", $argument_array, "OrganisationUnitListOwners");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_column("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
				
			$template = new HTMLTemplate("organisation_unit/list_owners.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
				
			$template->set_var("list", $list->get_list());
		
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
	public static function list_leaders()
	{
		if ($_GET['ou_id'])
		{
			$organisation_unit = new OrganisationUnit($_GET['ou_id']);
			
			$argument_array = array();
			$argument_array[0][0] = "organisation_unit_id";
			$argument_array[0][1] = $_GET['ou_id'];
			
			$list = new List_IO("OrganisationUnitListLeaders", "ajax.php?nav=organisation_unit", "list_leaders", "count_leaders", $argument_array, "OrganisationUnitListLeaders");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_column("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
				
			$template = new HTMLTemplate("organisation_unit/list_leaders.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
				
			$template->set_var("list", $list->get_list());
		
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
	public static function list_members()
	{
		if ($_GET['ou_id'])
		{
			$organisation_unit = new OrganisationUnit($_GET['ou_id']);
			
			$argument_array = array();
			$argument_array[0][0] = "organisation_unit_id";
			$argument_array[0][1] = $_GET['ou_id'];
			
			$list = new List_IO("OrganisationUnitListMembers", "ajax.php?nav=organisation_unit", "list_members", "count_members", $argument_array, "OrganisationUnitListMembers");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_column("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
				
			$template = new HTMLTemplate("organisation_unit/list_members.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
				
			$template->set_var("list", $list->get_list());
		
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
	public static function list_quality_managers()
	{
		if ($_GET['ou_id'])
		{
			$organisation_unit = new OrganisationUnit($_GET['ou_id']);
			
			$argument_array = array();
			$argument_array[0][0] = "organisation_unit_id";
			$argument_array[0][1] = $_GET['ou_id'];
			
			$list = new List_IO("OrganisationUnitListQualityManagers", "ajax.php?nav=organisation_unit", "list_quality_managers", "count_quality_managers", $argument_array, "OrganisationUnitListQualityManagers");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_column("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
				
			$template = new HTMLTemplate("organisation_unit/list_quality_managers.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
				
			$template->set_var("list", $list->get_list());
		
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
	public static function list_groups()
	{
		if ($_GET['ou_id'])
		{
			$organisation_unit = new OrganisationUnit($_GET['ou_id']);
			
			$argument_array = array();
			$argument_array[0][0] = "organisation_unit_id";
			$argument_array[0][1] = $_GET['ou_id'];
			
			$list = new List_IO("OrganisationUnitListGroups", "ajax.php?nav=organisation_unit", "list_groups", "count_groups", $argument_array, "OrganisationUnitListGroups");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Groupname","groupname",true,null,"OrganisationUnitListSortGroupname");
				
			$template = new HTMLTemplate("organisation_unit/list_groups.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
				
			$template->set_var("list", $list->get_list());
		
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	public static function detail_handler()
	{
		switch($_GET['action']):
		
			case "list_owners":
				self::list_owners();
			break;
			
			case "list_leaders":
				self::list_leaders();
			break;
			
			case "list_members":
				self::list_members();
			break;
			
			case "list_quality_managers":
				self::list_quality_managers();
			break;
			
			case "list_groups":
				self::list_groups();
			break;
			
			default:
				self::detail();
			break;
			
		endswitch;
	}
}
?>
