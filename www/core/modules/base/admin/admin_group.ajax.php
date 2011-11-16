<?php
/**
 * @package base
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
 * Group Admin AJAX IO Class
 * @package base
 */
class AdminGroupAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	private function list_groups($json_row_array, $json_argument_array,  $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}	
		
		$list_request = new ListRequest_IO();
		$list_request->set_row_array($json_row_array);
	
		if (!is_numeric($entries_per_page) or $entries_per_page < 1)
		{
			$entries_per_page = 20;
		}
		
		$list_array = User_Wrapper::list_groups($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));

		if (is_array($list_array) and count($list_array) >= 1)
		{
			foreach($list_array as $key => $value)
			{
				$group 	= new Group($list_array[$key][id]);
					
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				$paramquery[id] = $list_array[$key][id];
				$params = http_build_query($paramquery,'','&#38;');

				unset($list_array[$key][name]);
				$list_array[$key][symbol][link] = $params;	
				$list_array[$key][symbol][content] = "<img src='images/icons/groups.png' alt='' style='border: 0;' />";
				$list_array[$key][name][link] = $params;	
				$list_array[$key][name][content] = $group->get_name();
				$list_array[$key][users] = $group->get_number_of_user_members();
				
				$paramquery = $_GET;
				$paramquery[action] = "delete";
				$paramquery[id] = $list_array[$key][id];
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($list_array[$key][id] < 100)
				{
					$list_array[$key][delete] = "<img src='images/icons/delete_group_na.png' alt='' style='border: 0;' />";
				}
				else
				{
					$list_array[$key][delete][link] = $params;
					$list_array[$key][delete][content] = "<img src='images/icons/delete_group.png' alt='' style='border: 0;' />";
				}
			}
		}
		else
		{
			$list_request->empty_message("<span class='italic'>No groups found!</span>");
		}

		$list_request->set_array($list_array);
		
		return $list_request->get_page($page);
	}
	
	private function count_groups($json_argument_array)
	{
		return User_Wrapper::count_groups();
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
				
				case "list_groups":
					echo $this->list_groups($_POST[row_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "count_groups":
					echo $this->count_groups($_POST[argument_array]);
				break;
							
				default:
				break;
			
			endswitch;
		}
	}
}

$admin_group_ajax = new AdminGroupAjax;
$admin_group_ajax->method_handler();
?>