<?php
/**
 * @package organisation_unit
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * 
 */
$GLOBALS['autoload_prefix'] = "../";
require_once("../../base/ajax.php");

/**
 * Organisation Unit AJAX IO Class
 * @package organisation_unit
 */
class AdminOrganisationUnitAjax extends Ajax
{	
	function __construct()
	{
		parent::__construct();
	}
	
	public function list_members($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_members($organisation_unit_id), 20, "OrganisationUnitAdminListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Username","username",true,null,"OrganisationUnitAdminListSortUsername");
			$list->add_row("Fullname","fullname",true,null,"OrganisationUnitAdminListSortFullname");
			$list->add_row("","delete",false,"16px");
			
			if ($page)
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_members($organisation_unit_id, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_members($organisation_unit_id, null, null, ($page*20)-20, ($page*20));
				}				
			}
			else
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_members($organisation_unit_id, $sortvalue, $sortmethod, 0, 20);
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_members($organisation_unit_id, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$user = new User($value['id']);
					$result_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' />";
					$result_array[$key]['username'] = $user->get_username();
					$result_array[$key]['fullname'] = $user->get_full_name(false);
					$result_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$result_array[$key][id]."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
		
	public function delete_member($organisation_unit_id, $user_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($user_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->delete_user_from_organisation_unit($user_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function add_member($organisation_unit_id, $user_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($user_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->create_user_in_organisation_unit($user_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function list_owners($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_owners($organisation_unit_id), 20, "OrganisationUnitAdminListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Username","username",true,null,"OrganisationUnitAdminListSortUsername");
			$list->add_row("Fullname","fullname",true,null,"OrganisationUnitAdminListSortFullname");
			$list->add_row("","delete",false,"16px");
			
			if ($page)
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_owners($organisation_unit_id, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_owners($organisation_unit_id, null, null, ($page*20)-20, ($page*20));
				}				
			}
			else
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_owners($organisation_unit_id, $sortvalue, $sortmethod, 0, 20);
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_owners($organisation_unit_id, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$user = new User($value['id']);
					$result_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' />";
					$result_array[$key]['username'] = $user->get_username();
					$result_array[$key]['fullname'] = $user->get_full_name(false);
					$result_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$result_array[$key][id]."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
	
	public function delete_owner($organisation_unit_id, $user_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($user_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->delete_owner_from_organisation_unit($user_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function add_owner($organisation_unit_id, $user_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($user_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->create_owner_in_organisation_unit($user_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function list_leaders($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_leaders($organisation_unit_id), 20, "OrganisationUnitAdminListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Username","username",true,null,"OrganisationUnitAdminListSortUsername");
			$list->add_row("Fullname","fullname",true,null,"OrganisationUnitAdminListSortFullname");
			$list->add_row("","delete",false,"16px");
			
			if ($page)
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_leaders($organisation_unit_id, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_leaders($organisation_unit_id, null, null, ($page*20)-20, ($page*20));
				}				
			}
			else
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_leaders($organisation_unit_id, $sortvalue, $sortmethod, 0, 20);
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_leaders($organisation_unit_id, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$user = new User($value['id']);
					$result_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' />";
					$result_array[$key]['username'] = $user->get_username();
					$result_array[$key]['fullname'] = $user->get_full_name(false);
					$result_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$result_array[$key][id]."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
	
	public function delete_leader($organisation_unit_id, $user_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($user_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->delete_leader_from_organisation_unit($user_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function add_leader($organisation_unit_id, $user_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($user_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->create_leader_in_organisation_unit($user_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function list_quality_managers($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_quality_managers($organisation_unit_id), 20, "OrganisationUnitAdminListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Username","username",true,null,"OrganisationUnitAdminListSortUsername");
			$list->add_row("Fullname","fullname",true,null,"OrganisationUnitAdminListSortFullname");
			$list->add_row("","delete",false,"16px");
			
			if ($page)
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_quality_managers($organisation_unit_id, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_quality_managers($organisation_unit_id, null, null, ($page*20)-20, ($page*20));
				}				
			}
			else
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_quality_managers($organisation_unit_id, $sortvalue, $sortmethod, 0, 20);
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_quality_managers($organisation_unit_id, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$user = new User($value['id']);
					$result_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' />";
					$result_array[$key]['username'] = $user->get_username();
					$result_array[$key]['fullname'] = $user->get_full_name(false);
					$result_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$result_array[$key][id]."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
	
	public function delete_quality_manager($organisation_unit_id, $user_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($user_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->delete_quality_manager_from_organisation_unit($user_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function add_quality_manager($organisation_unit_id, $user_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($user_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->create_quality_manager_in_organisation_unit($user_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function list_groups($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_groups($organisation_unit_id), 20, "OrganisationUnitAdminListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Groupname","groupname",true,null,"OrganisationUnitAdminListSortGroupname");
			$list->add_row("","delete",false,"16px");
			
			if ($page)
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_groups($organisation_unit_id, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_groups($organisation_unit_id, null, null, ($page*20)-20, ($page*20));
				}				
			}
			else
			{
				if ($sortvalue and $sortmethod)
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_groups($organisation_unit_id, $sortvalue, $sortmethod, 0, 20);
				}
				else
				{
					$result_array = OrganisationUnit_Wrapper::list_organisation_unit_groups($organisation_unit_id, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$group = new Group($value['id']);
					$result_array[$key]['symbol'] = "<img src='images/icons/groups.png' alt='' />";
					$result_array[$key]['groupname'] = $group->get_name();
					$result_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$result_array[$key][id]."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
	
	public function delete_group($organisation_unit_id, $group_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($group_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->delete_group_from_organisation_unit($group_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function add_group($organisation_unit_id, $group_id)
	{
		if (is_numeric($organisation_unit_id) and is_numeric($group_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->create_group_in_organisation_unit($group_id) == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
	
				case "list_members":
					$this->list_members($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "delete_member":
					$this->delete_member($_GET[organisation_unit_id], $_GET[user_id]);
				break;
				
				case "add_member":
					$this->add_member($_GET[organisation_unit_id], $_GET[user_id]);
				break;
				
				case "list_owners":
					$this->list_owners($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;

				case "delete_owner":
					$this->delete_owner($_GET[organisation_unit_id], $_GET[user_id]);
				break;
				
				case "add_owner":
					$this->add_owner($_GET[organisation_unit_id], $_GET[user_id]);
				break;
				
				case "list_leaders":
					$this->list_leaders($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "delete_leader":
					$this->delete_leader($_GET[organisation_unit_id], $_GET[user_id]);
				break;
				
				case "add_leader":
					$this->add_leader($_GET[organisation_unit_id], $_GET[user_id]);
				break;
				
				case "list_quality_managers":
					$this->list_quality_managers($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "delete_quality_manager":
					$this->delete_quality_manager($_GET[organisation_unit_id], $_GET[user_id]);
				break;
				
				case "add_quality_manager":
					$this->add_quality_manager($_GET[organisation_unit_id], $_GET[user_id]);
				break;
				
				case "list_groups":
					$this->list_groups($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "delete_group":
					$this->delete_group($_GET[organisation_unit_id], $_GET[group_id]);
				break;
				
				case "add_group":
					$this->add_group($_GET[organisation_unit_id], $_GET[group_id]);
				break;
				
				default:
				break;
			
			endswitch;
		}
	}
}

$admin_organisation_unit_ajax = new AdminOrganisationUnitAjax;
$admin_organisation_unit_ajax->method_handler();

?>