<?php
/**
 * @package data
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
 * Parameter Template Admin Ajax IO Class
 * @package data
 */
class AdminParameterTemplateAjax
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
	public static function list_templates($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
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
						
			$list_array = Data_Wrapper::list_parameter_templates($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
			if (is_array($list_array) and count($list_array) >= 1)
			{	
				foreach($list_array as $key => $value)
				{
					$user = new User($list_array[$key]['created_by']);
					$list_array[$key]['created_by'] = $user->get_full_name(true);
					
					$datetime_handler = new DatetimeHandler($list_array[$key]['datetime']);
					$list_array[$key]['datetime'] = $datetime_handler->get_datetime(false);
					
					$paramquery = $_GET;
					$paramquery['id'] = $list_array[$key]['id'];
					$paramquery['action'] = "delete";
					unset($paramquery['sortvalue']);
					unset($paramquery['sortmethod']);
					unset($paramquery['nextpage']);
					$params = http_build_query($paramquery, '', '&#38;');
	
					$list_array[$key]['delete']['link'] = $params;
					$list_array[$key]['delete']['content'] = "delete";
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
	public static function count_templates($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Data_Wrapper::count_list_parameter_templates();
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	public static function add_template($name, $internal_name, $json_object_string)
	{
		if ($name and $internal_name and $json_object_string)
		{			
			$json_object = json_decode($json_object_string, true);
			
			$parameter_template = new ParameterTemplate();
			
			if ($parameter_template->create($name, $internal_name, $json_object) !== null)
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
}
?>