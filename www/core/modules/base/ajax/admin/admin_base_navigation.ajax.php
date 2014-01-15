<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Admin Base Navigation AJAX IO Class
 * @package base
 */
class AdminBaseNavigationAjax
{
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_navigation($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if ($get_array)
			{
				$_GET = unserialize($get_array);	
			}
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = System_Wrapper::list_base_module_navigation(($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
						
			if (is_array($list_array) and count($list_array) >= 1)
			{		
				foreach($list_array as $key => $value)
				{	
					$paramquery = $_GET;
					$paramquery['id'] = $list_array[$key]['id'];
					$paramquery['action'] = "hide";
					unset($paramquery['sortvalue']);
					unset($paramquery['sortmethod']);
					unset($paramquery['nextpage']);
					$params = http_build_query($paramquery, '', '&#38;');
	
					$list_array[$key]['hide']['link'] = $params;
					
					$list_array[$key]['name'] = Language::get_message($list_array[$key]['name'], "navigation");
					
					if ($list_array[$key]['hidden'] == 't')
					{
						$list_array[$key]['hide']['content'] = "<img src='images/icons/grey_point.png' alt='hide' style='border: 0;' />";
					}
					else
					{
						$list_array[$key]['hide']['content'] = "<img src='images/icons/green_point.png' alt='hide' style='border: 0;' />";
					}
					
					if ($list_array[$key]['position'] != 1)
					{
						$paramquery = $_GET;
						$paramquery['id'] = $list_array[$key]['id'];
						$paramquery['action'] = "upwards";
						unset($paramquery['sortvalue']);
						unset($paramquery['sortmethod']);
						unset($paramquery['nextpage']);
						$params = http_build_query($paramquery, '', '&#38;');
		
						$list_array[$key]['uw']['link'] = $params;
						$list_array[$key]['uw']['content'] = "<img src='images/icons/upward.png' alt='uw' style='border: 0;' />";
					}
					else
					{
						$list_array[$key]['uw'] = "<img src='images/icons/upward_na.png' alt='uw' style='border: 0;' />";
					}
					
					if ($list_array[$key]['position'] != ModuleNavigation::get_highest_position())
					{
						$paramquery = $_GET;
						$paramquery['id'] = $list_array[$key]['id'];
						$paramquery['action'] = "downwards";
						unset($paramquery['sortvalue']);
						unset($paramquery['sortmethod']);
						unset($paramquery['nextpage']);
						$params = http_build_query($paramquery, '', '&#38;');
		
						$list_array[$key]['dw']['link'] = $params;
						$list_array[$key]['dw']['content'] = "<img src='images/icons/downward.png' alt='dw' style='border: 0;' />";
					}
					else
					{
						$list_array[$key]['dw'] = "<img src='images/icons/downward_na.png' alt='dw' style='border: 0;' />";
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
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_navigation($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return System_Wrapper::count_base_module_navigation()+1;
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
}