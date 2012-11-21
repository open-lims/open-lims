<?php
/**
 * @package data
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
 * Data Search AJAX IO Class
 * @package data
 */
class DataSearchAjax 
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
	public static function list_data($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		if (is_array($argument_array))
		{
			$folder_id = $argument_array[0][1];
			$name = $argument_array[1][1];;
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = Data_Wrapper::list_search_ffv($folder_id, $name, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($list_array[$key]['datetime']);
					$list_array[$key]['datetime'] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
					$owner = new User($value['owner']);
					$list_array[$key]['owner'] = $owner->get_full_name(true);
					
					if (is_numeric($value['file_id']))
					{
						$file = File::get_instance($value['file_id']);
						
						$paramquery = $_GET;
						$paramquery['nav'] = "data";
						$paramquery['action'] = "file_detail";
						$paramquery['file_id'] = $value['file_id'];
						unset($paramquery['sortvalue']);
						unset($paramquery['sortmethod']);
						unset($paramquery['nextpage']);
						$params = http_build_query($paramquery, '', '&#38;');
						
						$tmp_name = $value['name'];
						unset($list_array[$key]['name']);
						$list_array[$key]['name']['content'] = $tmp_name;
						
						if ($file->is_read_access() == true)
						{
							$list_array[$key]['symbol']['link'] = $params;
							$list_array[$key]['symbol']['content'] = "<img src='".File::get_icon_by_name($value['name'])."' alt='' style='border:0;' />";
							$list_array[$key]['name']['link'] = $params;
						}
						else
						{
							$list_array[$key]['symbol']['link'] = "";
							$list_array[$key]['symbol']['content'] = "<img src='core/images/denied_overlay.php?image=".File::get_icon_by_name($value['name'])."' alt='' border='0' />";
							$list_array[$key]['name']['link'] = "";
						}
						
						$list_array[$key]['type'] = "File";
						
						$list_array[$key]['version'] = $file->get_version();
						$list_array[$key]['size'] = Convert::convert_byte_1024($file->get_size());
						$list_array[$key]['permission'] = $file->get_permission_string();
					}
					
					if (is_numeric($value['value_id']))
					{
						$value_obj = Value::get_instance($value['value_id']);
						
						$paramquery = $_GET;
						$paramquery['nav'] = "data";
						$paramquery['action'] = "value_detail";
						$paramquery['value_id'] = $value['value_id'];
						unset($paramquery['sortvalue']);
						unset($paramquery['sortmethod']);
						unset($paramquery['nextpage']);
						$params = http_build_query($paramquery, '', '&#38;');
						
						$tmp_name = $value['name'];
						unset($list_array[$key]['name']);
						$list_array[$key]['name']['content'] = $tmp_name;
						
						if ($value_obj->is_read_access() == true)
						{
							$list_array[$key]['symbol']['link'] = $params;
							$list_array[$key]['symbol']['content'] = "<img src='images/fileicons/16/unknown.png' alt='' style='border: 0;'>";
							$list_array[$key]['name']['link'] = $params;
						}
						else
						{
							$list_array[$key]['symbol']['link'] = "";
							$list_array[$key]['symbol']['content'] = "<img src='core/images/denied_overlay.php?image=images/fileicons/16/unknown.png' alt='' border='0' />";
							$list_array[$key]['name']['link'] = "";
						}
						
						$list_array[$key]['type'] = "Value";
						
						$list_array[$key]['version'] = $value_obj->get_version();
						$list_array[$key]['permission'] = $value_obj->get_permission_string();
					}
					
					if (is_numeric($value['folder_id']))
					{
						$folder = Folder::get_instance($value['folder_id']);
						
						$paramquery = $_GET;
						$paramquery['nav'] = "data";
						$paramquery['folder_id'] = $value['folder_id'];
						unset($paramquery['run']);
						unset($paramquery['sortvalue']);
						unset($paramquery['sortmethod']);
						unset($paramquery['nextpage']);
						$params = http_build_query($paramquery, '', '&#38;');
						
						$tmp_name = $value['name'];
						unset($list_array[$key]['name']);
						$list_array[$key]['name']['content'] = $tmp_name;
						
						if ($folder->is_read_access() == true)
						{
							$list_array[$key]['symbol']['link'] = $params;
							$list_array[$key]['symbol']['content'] = "<img src='images/icons/folder.png' alt='' style='border: 0;'>";
							$list_array[$key]['name']['link'] = $params;
						}
						else
						{
							$list_array[$key]['symbol']['link'] = "";
							$list_array[$key]['symbol']['content'] = "<img src='core/images/denied_overlay.php?image=images/icons/folder.png' alt='' border='0' />";
							$list_array[$key]['name']['link'] = "";
						}
						
						$list_array[$key]['type'] = "Folder";
						
						$list_array[$key]['permission'] = $folder->get_permission_string();
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
	 * @return integer
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function count_data($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		if (is_array($argument_array))
		{
			$folder_id = $argument_array[0][1];
			$name = $argument_array[1][1];;
			
			return Data_Wrapper::count_search_ffv($folder_id, $name);
		}
		else
		{
			throw new BaseAjaxArgumentMissingException();
		}
	}
}
?>