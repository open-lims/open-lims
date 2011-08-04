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
 * Organiser IO Class
 * @package organisation_unit
 */
class OrganisationUnitIO
{
	public static function detail()
	{
		global $user;
		
		if ($_GET[ou_id])
		{
			$organisation_unit = new OrganisationUnit($_GET[ou_id]);
			
			$template = new Template("template/organisation_unit/organisation_unit_detail.html");
			
			$template->set_var("title",$organisation_unit->get_name());
			
			
			if ($user->is_admin() == true)
			{
				$paramquery = $_GET;
				$paramquery[nav] = "administration";
				$paramquery[run] = "organisation_unit";
				$paramquery[action] = "detail";
				$paramquery[id] = $_GET[ou_id];
				unset($paramquery[ou_id]);
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
						$paramquery[nav] = "administration";
						$paramquery[run] = "organisation_unit";
						$paramquery[action] = "detail";
						$paramquery[id] = $_GET[ou_id];
						unset($paramquery[ou_id]);
						$params = http_build_query($paramquery,'','&#38;');
						
						$template->set_var("admin_params", $params);
						
						$template->set_var("is_owner", true);
					}
					
					$owner = new User($value);
					
					$owner_paramquery = $_GET;
					$owner_paramquery[run] = "common_dialog";
					$owner_paramquery[dialog] = "user_detail";
					$owner_paramquery[id] = $value;
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
			$owner_list_paramquery[run] = "common_dialog";
			$owner_list_paramquery[dialog] = "ou_detail";
			$owner_list_paramquery[action] = "list_owners";
			$owner_list_paramquery[ou_id] = $_GET[ou_id];
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
					$leader_paramquery[run] = "common_dialog";
					$leader_paramquery[dialog] = "user_detail";
					$leader_paramquery[id] = $value;
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
			$leader_list_paramquery[run] = "common_dialog";
			$leader_list_paramquery[dialog] = "ou_detail";
			$leader_list_paramquery[action] = "list_leaders";
			$leader_list_paramquery[ou_id] = $_GET[ou_id];
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
					$member_paramquery[run] = "common_dialog";
					$member_paramquery[dialog] = "user_detail";
					$member_paramquery[id] = $value;
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
			$member_list_paramquery[run] = "common_dialog";
			$member_list_paramquery[dialog] = "ou_detail";
			$member_list_paramquery[action] = "list_members";
			$member_list_paramquery[ou_id] = $_GET[ou_id];
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
					$quality_manager_paramquery[run] = "common_dialog";
					$quality_manager_paramquery[dialog] = "user_detail";
					$quality_manager_paramquery[id] = $value;
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
			$quality_manager_list_paramquery[run] = "common_dialog";
			$quality_manager_list_paramquery[dialog] = "ou_detail";
			$quality_manager_list_paramquery[action] = "list_quality_managers";
			$quality_manager_list_paramquery[ou_id] = $_GET[ou_id];
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
					$group_paramquery[run] = "common_dialog";
					$group_paramquery[dialog] = "group_detail";
					$group_paramquery[id] = $value;
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
			$group_list_paramquery[run] = "common_dialog";
			$group_list_paramquery[dialog] = "ou_detail";
			$group_list_paramquery[action] = "list_groups";
			$group_list_paramquery[ou_id] = $_GET[ou_id];
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
					$button_template = new Template("template/".$value[file]);
				
					$button_paramquery = array();
					$button_paramquery[username] = $_GET[username];
					$button_paramquery[session_id] = $_GET[session_id];
					
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
					
					$content_array[$counter][content] = $button_template->get_string();
					$counter++;
				}
				
				$template->set_var("OU_FOOTER_ARRAY" ,$content_array);
			}
	
			$template->output();
			
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}

	public static function list_user_related_organisation_units()
	{
		global $user;
	
		$content_array = array();
		
		$table_io = new TableIO("OverviewTable");
		
		$table_io->add_row("","symbol",false,16);
		$table_io->add_row("Name","name",false,null);
		$table_io->add_row("My Status","mystatus",false,null);
		
		$organisation_unit_array = OrganisationUnit::list_entries_by_user_id($user->get_user_id());
		
		$organisation_unit_array_cardinality = count($organisation_unit_array);
		
		$counter = 0;

		if (!$_GET[page] or $_GET[page] == 1)
		{
			$page = 1;
			$counter_begin = 0;
			if ($organisation_unit_array_cardinality > 25)
			{
				$counter_end = 24;
			}
			else
			{
				$counter_end = $organisation_unit_array_cardinality-1;
			}
		}
		else
		{
			if ($_GET[page] >= ceil($organisation_unit_array_cardinality/25))
			{
				$page = ceil($organisation_unit_array_cardinality/25);
				$counter_end = $organisation_unit_array_cardinality;
			}
			else
			{
				$page = $_GET[page];
				$counter_end = (25*$page)-1;
			}
			$counter_begin = (25*$page)-25;
		}
		
		if (is_array($organisation_unit_array))
		{
			$module_link_array = ModuleLink::list_links_by_type("ou_navigation");
			
			foreach ($organisation_unit_array as $key => $value)
			{
				if ($counter >= $counter_begin and $counter <= $counter_end)
				{
					$column_array = array();

					$organisation_unit 	= new OrganisationUnit($value);
					
					$paramquery['username'] = $_GET['username'];
					$paramquery['session_id'] = $_GET['session_id'];
					
					if (is_array($module_link_array[0]['array']) and count($module_link_array[0]['array']) >= 1)
					{
						foreach ($module_link_array[0]['array'] as $array_key => $array_value)
						{
							if ($array_value == "%OU_ID%")
							{
								$paramquery['ou_id'] = $value;
							}
							else
							{
								$paramquery[$array_key] = $array_value;
							}
						}
					}
					
					$params = http_build_query($paramquery, '', '&#38;');
					
					
					$column_array[symbol][link] = $params;
					$column_array[symbol][content] = "<img src='images/icons/".$organisation_unit->get_icon()."' alt='N' border='0' />";
					$column_array[name][link] = $params;
					$column_array[name][content] = $organisation_unit->get_name();
					$column_array[mystatus] = $organisation_unit->get_user_status($user->get_user_id());
	
					array_push($content_array, $column_array);
				}
				$counter++;	
			}
		}
		else
		{
			$content_array = null;
			$table_io->override_last_line("<span class='italic'>No Organisation Units Found!</span>");
		}
		
		$template = new Template("template/organisation_unit/user_related_organisation_units.html");
		
		$table_io->add_content_array($content_array);	
			
		$template->set_var("table", $table_io->get_table($page ,$organisation_unit_array_cardinality));		

		$template->output();
	}
	
	public static function list_owners()
	{
		if ($_GET[ou_id])
		{
			$organisation_unit = new OrganisationUnit($_GET[ou_id]);
			
			$template = new Template("template/organisation_unit/list_owners.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[ou_id]);
			$template->output();
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function list_leaders()
	{
		if ($_GET[ou_id])
		{
			$organisation_unit = new OrganisationUnit($_GET[ou_id]);
			
			$template = new Template("template/organisation_unit/list_leaders.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[ou_id]);
			$template->output();
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function list_members()
	{
		if ($_GET[ou_id])
		{
			$organisation_unit = new OrganisationUnit($_GET[ou_id]);
			
			$template = new Template("template/organisation_unit/list_members.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[ou_id]);
			$template->output();
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function list_quality_managers()
	{
		if ($_GET[ou_id])
		{
			$organisation_unit = new OrganisationUnit($_GET[ou_id]);
			
			$template = new Template("template/organisation_unit/list_quality_managers.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[ou_id]);
			$template->output();
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function list_groups()
	{
		if ($_GET[ou_id])
		{
			$organisation_unit = new OrganisationUnit($_GET[ou_id]);
			
			$template = new Template("template/organisation_unit/list_groups.html");
			$template->set_var("TITLE", "(".$organisation_unit->get_name().")");
			$template->set_var("ORGANISATION_UNIT_ID", $_GET[ou_id]);
			$template->output();
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 40, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function detail_handler()
	{
		switch($_GET[action]):
		
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
