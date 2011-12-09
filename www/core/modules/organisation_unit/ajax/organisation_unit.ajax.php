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
$GLOBALS['autoload_prefix'] = "../";
require_once("../../base/ajax.php");

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
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 */
	public function list_members($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new ListStat_IO(OrganisationUnit_Wrapper::count_organisation_unit_members($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_column("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
			
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
		
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 */
	public function list_owners($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new ListStat_IO(OrganisationUnit_Wrapper::count_organisation_unit_owners($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_column("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
			
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
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 */
	public function list_leaders($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new ListStat_IO(OrganisationUnit_Wrapper::count_organisation_unit_leaders($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_column("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
			
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
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 */
	public function list_quality_managers($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new ListStat_IO(OrganisationUnit_Wrapper::count_organisation_unit_quality_managers($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Username","username",true,null,"OrganisationUnitListSortUsername");
			$list->add_column("Fullname","fullname",true,null,"OrganisationUnitListSortFullname");
			
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
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 */
	public function list_groups($organisation_unit_id, $page, $sortvalue, $sortmethod)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $user;
			
			$list = new ListStat_IO(OrganisationUnit_Wrapper::count_organisation_unit_groups($organisation_unit_id), 20, "OrganisationUnitListPage");
	
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Groupname","groupname",true,null,"OrganisationUnitListSortGroupname");
			
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
	
	public function list_organisation_units_by_user_id($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}	
		
		$argument_array = json_decode($json_argument_array);
		
		$user_id = $argument_array[0][1];
		
		if (is_numeric($user_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = OrganisationUnit_Wrapper::list_organisation_units_by_user_id($user_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));

			if (is_array($list_array) and count($list_array) >= 1)
			{
				$module_link_array = ModuleLink::list_links_by_type("ou_navigation");
				
				foreach($list_array as $key => $value)
				{	
					$paramquery['username'] = $_GET['username'];
					$paramquery['session_id'] = $_GET['session_id'];
					
					if (is_array($module_link_array[0]['array']) and count($module_link_array[0]['array']) >= 1)
					{
						foreach ($module_link_array[0]['array'] as $array_key => $array_value)
						{
							if ($array_value == "%OU_ID%")
							{
								$paramquery['ou_id'] = $list_array[$key][id];
							}
							else
							{
								$paramquery[$array_key] = $array_value;
							}
						}
					}
					
					$params = http_build_query($paramquery, '', '&#38;');
					
					$tmp_symbol = $list_array[$key][symbol];
					unset($list_array[$key][symbol]);
					$list_array[$key][symbol][link] = $params;
					$list_array[$key][symbol][content] = "<img src='images/icons/".$tmp_symbol."' alt='N' border='0' />";
					
					$tmp_name = $list_array[$key][name];
					unset($list_array[$key][name]);
					$list_array[$key][name][link] = $params;
					$list_array[$key][name][content] = $tmp_name;
					
					if ($list_array[$key][is_member])
					{
						$my_status_string = "Member";
					}
					
					if ($list_array[$key][is_owner])
					{
						if ($my_status_string)
						{
							$my_status_string .= ", Owner";
						}
						else
						{
							$my_status_string = "Owner";
						}
					}
					
					if ($list_array[$key][is_leader])
					{
						if ($my_status_string)
						{
							$my_status_string .= ", Leader";
						}
						else
						{
							$my_status_string = "Leader";
						}
					}
					
					if ($list_array[$key][is_quality_manager])
					{
						if ($my_status_string)
						{
							$my_status_string .= ", Quality Manager";
						}
						else
						{
							$my_status_string = "Quality Manager";
						}
					}
					
					$list_array[$key][mystatus] = $my_status_string;
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No Organisation Unit found!</span>");
			}

			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
	}
	
	public function count_organisation_units_by_user_id($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$user_id = $argument_array[0][1];
		
		if (is_numeric($user_id))
		{
			return OrganisationUnit_Wrapper::count_organisation_units_by_user_id($user_id);
		}
		else
		{
			return null;
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
				
				case "list_organisation_units_by_user_id":
					echo $this->list_organisation_units_by_user_id($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "count_organisation_units_by_user_id":
					echo $this->count_organisation_units_by_user_id($_POST[argument_array]);
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