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
 * Organiser IO Class
 * @package organisation_unit
 */
class OrganisationUnitIO
{
	/**
	 * @todo error
	 */
	public static function detail()
	{
		global $user;
		
		if ($_GET[ou_id])
		{
			$organisation_unit = new OrganisationUnit($_GET[ou_id]);
			
			$owner = new User($organisation_unit->get_owner_id());
			$leader = new User($organisation_unit->get_leader_id());
			
			$template = new Template("languages/en-gb/template/organisation_unit/organisation_unit_detail.html");
			
			if ($user->get_user_id() == $organisation_unit->get_owner_id())
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
			
			$template->set_var("title",$organisation_unit->get_name());
			$template->set_var("owner",$owner->get_full_name(true));
			$template->set_var("leader",$leader->get_full_name(true));
			
			$organisation_unit_member_array = $organisation_unit->list_members();
			
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
			
			$template->set_var("members",$ou_members);
			
			$organisation_unit_group_array = $organisation_unit->list_groups();
			
			if (is_array($organisation_unit_group_array) and count($organisation_unit_group_array) >= 1)
			{
				$ou_groups = null;
				foreach ($organisation_unit_group_array as $key => $value)
				{
					$group = new Group($value);
					if ($ou_groups)
					{
						$ou_groups .= ",".$group->get_name();
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
			
			$template->set_var("groups",$ou_groups);
			
			$paramquery = $_GET;
			$paramquery[run] = "list_ou_equipment";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("ou_equipment_params", $params);
			
			$template->output();
		}
		else
		{
			// ! ERROR !
		}
	}

	public function list_user_related_organisation_units()
	{
		global $user;
	
		$content_array = array();
		
		$table_io = new TableIO("OverviewTable");
		
		$table_io->add_row("","symbol",false,16);
		$table_io->add_row("Name","name",false,null);
		$table_io->add_row("Leader","leader",false,null);
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
			foreach ($organisation_unit_array as $key => $value)
			{
				if ($counter >= $counter_begin and $counter <= $counter_end)
				{
					$column_array = array();

					$organisation_unit 	= new OrganisationUnit($value);
					$leader = new User($organisation_unit->get_leader_id);
					
					$paramquery[username] 	= $_GET[username];
					$paramquery[session_id] = $_GET[session_id];
					$paramquery[nav] 		= "projects";
					$paramquery[run] 		= "organ_unit";
					$paramquery[ou_id] 		= $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					
					$column_array[symbol][link] = $params;
					$column_array[symbol][content] = "<img src='images/icons/".$organisation_unit->get_icon()."' alt='N' border='0' />";
					$column_array[name][link] = $params;
					$column_array[name][content] = $organisation_unit->get_name();
					$column_array[leader] = $leader->get_full_name(false);
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
		
		$template = new Template("languages/en-gb/template/organisation_unit/user_related_organisation_units.html");
		
		$table_io->add_content_array($content_array);	
			
		$template->set_var("table", $table_io->get_table($page ,$organisation_unit_array_cardinality));		

		$template->output();
	}
	
}
?>
