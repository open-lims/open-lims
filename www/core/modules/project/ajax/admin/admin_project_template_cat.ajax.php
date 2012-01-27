<?php
/**
 * @package project
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
 * Admin Project Template Category AJAX IO Class
 * @package project
 */
class AdminProjectTemplateCatAjax
{
	public static function list_categories($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
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
					
		$list_array = Project_Wrapper::list_project_template_categories($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
		if (is_array($list_array) and count($list_array) >= 1)
		{		
			foreach($list_array as $key => $value)
			{
				$paramquery = $_GET;
				$paramquery[id] = $list_array[$key][id];
				$paramquery[action] = "edit";
				unset($paramquery[sortvalue]);
				unset($paramquery[sortmethod]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery, '', '&#38;');

				$list_array[$key][edit][link] = $params;
				$list_array[$key][edit][content] = "edit";
				
				
				$paramquery = $_GET;
				$paramquery[id] = $list_array[$key][id];
				$paramquery[action] = "delete";
				unset($paramquery[sortvalue]);
				unset($paramquery[sortmethod]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery, '', '&#38;');

				$list_array[$key][delete][link] = $params;
				$list_array[$key][delete][content] = "delete";
			}
		}
		else
		{
			$list_request->empty_message("<span class='italic'>No results found!</span>");
		}
		
		$list_request->set_array($list_array);
			
		return $list_request->get_page($page);
	}
	
	public static function count_categories($json_argument_array)
	{
		return Project_Wrapper::count_list_project_template_categories();
	}
}
?>