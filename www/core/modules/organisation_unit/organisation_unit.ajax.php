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
 * 
 */
require_once("../base/ajax.php");

/**
 * Organisation Unit AJAX IO Class
 * @package organisation_unit
 */
class OrganisationUnitAjax extends Ajax
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
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_members($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_row("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
			
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
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
		
	public function list_owners($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_owners($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_row("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
			
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
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
	
	public function list_leaders($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_leaders($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_row("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
			
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
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
	
	public function list_quality_managers($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_quality_managers($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_row("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
			
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
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
		}
	}
	
	public function list_groups($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new List_IO(OrganisationUnit_Wrapper::count_organisation_unit_groups($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_row("","symbol",false,"16px");
			$list->add_row("Groupname","groupname",true,null,"OrganisationUnitListSortGroupname");
			
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
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			echo $list->get_list($result_array, $page);
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
				
				case "list_owners":
					$this->list_owners($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "list_leaders":
					$this->list_leaders($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "list_quality_managers":
					$this->list_quality_managers($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;

				case "list_groups":
					$this->list_groups($_GET[organisation_unit_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				default:
				break;
			
			endswitch;
		}
	}
}

$organisation_unit_ajax = new OrganisationUnitAjax;
$organisation_unit_ajax->method_handler();

?>