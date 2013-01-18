<?php
/**
 * @package base
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
 * System Log Admin AJAX IO Class
 * @package base
 */
class AdminSystemLogAjax
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
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function list_system_log($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if ($get_array)
			{
				$_GET = unserialize($get_array);	
			}
			
			$argument_array = json_decode($json_argument_array);
		
			if (is_array($argument_array))
			{
				$type_id = $argument_array[1];
	
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = SystemFE_Wrapper::list_system_log($type_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
				
				if (is_array($list_array) and count($list_array) >= 1)
				{		
					foreach($list_array as $key => $value)
					{	
						$datetime_handler = new DatetimeHandler($list_array[$key]['datetime']);
						$list_array[$key]['datetime'] = $datetime_handler->get_datetime(false);

						$paramquery = $_GET;
						$paramquery['id'] = $list_array[$key]['id'];
						$paramquery['action'] = "detail";
						unset($paramquery['sortvalue']);
						unset($paramquery['sortmethod']);
						unset($paramquery['nextpage']);
						$params = http_build_query($paramquery, '', '&#38;');
		
						$tmp_datetime = $list_array[$key]['datetime'];
						unset($list_array[$key]['datetime']);
						$list_array[$key]['datetime']['link'] = $params;
						$list_array[$key]['datetime']['content'] = $tmp_datetime;
						
						
						$paramquery = $_GET;
						$paramquery['id'] = $list_array[$key]['ip'];
						$paramquery['action'] = "ip_info";
						unset($paramquery['sortvalue']);
						unset($paramquery['sortmethod']);
						unset($paramquery['nextpage']);
						$params = http_build_query($paramquery, '', '&#38;');
		
						$tmp_ip = $list_array[$key]['ip'];
						unset($list_array[$key]['ip']);				
						$list_array[$key]['ip']['link'] = $params;
						$list_array[$key]['ip']['content'] = $tmp_ip;
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
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function count_system_log($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			if (is_array($argument_array))
			{
				$type_id = $argument_array[1];
				return SystemFE_Wrapper::count_list_system_log($type_id);
			}
			else
			{
				throw new BaseAjaxArgumentMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
}