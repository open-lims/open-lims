<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * User Search Ajax Class
 * @package base
 */
class UserSearchAjax
{
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function list_users($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		if (is_array($argument_array))
		{
			$string = $argument_array[1];
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = User_Wrapper::list_search_users($string, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach ($list_array as $key => $value)
				{
					$list_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='N' border='0' />";

					$user_paramquery = array();
					$user_paramquery['username'] = $_GET['username'];
					$user_paramquery['session_id'] = $_GET['session_id'];
					$user_paramquery['nav'] = "search";
					$user_paramquery['run'] = "common_dialog";
					$user_paramquery['dialog'] = "user_detail";
					$user_paramquery['id'] = $value['id'];
					$user_params = http_build_query($user_paramquery, '', '&#38;');
					
					$tmp_username = $list_array[$key]['username'];
					unset($list_array[$key]['username']);
					$list_array[$key]['username']['content'] = $tmp_username;
					$list_array[$key]['username']['link'] = $user_params;
					
					if ($list_array[$key]['id'] == 1)
					{
						$list_array[$key]['fullname'] = "Administrator";
					}
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseAjaxArgumentMissingException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return string
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function count_users($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		if (is_array($argument_array))
		{
			$string = $argument_array[1];
			
			return User_Wrapper::count_search_users($string);
		}
		else
		{
			throw new BaseAjaxArgumentMissingException();
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function list_groups($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		if (is_array($argument_array))
		{
			$string = $argument_array[1];
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = User_Wrapper::list_search_groups($string, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach ($list_array as $key => $value)
				{
					$list_array[$key]['symbol'] = "<img src='images/icons/groups.png' alt='N' border='0' />";

					$group_paramquery = array();
					$group_paramquery['username'] = $_GET['username'];
					$group_paramquery['session_id'] = $_GET['session_id'];
					$group_paramquery['nav'] = "search";
					$group_paramquery['run'] = "common_dialog";
					$group_paramquery['dialog'] = "group_detail";
					$group_paramquery['id'] = $value['id'];
					$group_params = http_build_query($group_paramquery, '', '&#38;');
					
					$tmp_name = $list_array[$key]['name'];
					unset($list_array[$key]['name']);
					$list_array[$key]['name']['content'] = $tmp_name;
					$list_array[$key]['name']['link'] = $group_params;
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseAjaxArgumentMissingException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function count_groups($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		if (is_array($argument_array))
		{
			$string = $argument_array[1];
			
			return User_Wrapper::count_search_groups($string);
		}
		else
		{
			throw new BaseAjaxArgumentMissingException();
		}
	}
}