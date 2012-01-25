<?php
/**
 * @package equipment
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
 * Equipment AJAX IO Class
 * @package equipment
 */
class EquipmentAjax
{	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public function list_equipment_items($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
	{		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}	
		
		if ($json_argument_array)
		{
			$argument_array = json_decode($json_argument_array);
		}
		
		$handling_class = Item::get_holder_handling_class_by_name($argument_array[0][1]);
		if ($handling_class)
		{
			$sql = $handling_class::get_item_list_sql($argument_array[1][1]);
		}
		
		if ($sql)
		{			
			$list_request = new ListRequest_IO();
			
			if ($argument_array[2][1] == true)
			{	
				$list_array = Equipment_Wrapper::list_item_equipments($sql, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
			}
			else
			{	
				$list_array = Equipment_Wrapper::list_item_equipments($sql, $sortvalue, $sortmethod, 0, null);
			}
			$list_request->set_column_array($json_column_array);
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					if ($argument_array[3][1] == true)
					{
						$column_array = json_decode($json_column_array);
						if (is_array($column_array) and count($column_array) >= 1)
						{
							foreach ($column_array as $row_key => $row_value)
							{
								if ($row_value[1] == "checkbox")
								{
									if ($row_value[4])
									{
										$checkbox_class = $row_value[4];
										break;
									}
								}
							}
						}
						
						if ($checkbox_class)
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='equipment-".$list_array[$key][item_id]."' value='1' class='".$checkbox_class."' checked='checked' />";
						}
						else
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='equipment-".$list_array[$key][item_id]."' value='1' checked='checked' />";
						}
						
						$list_array[$key][symbol]	= "<img src='images/icons/equipment.png' alt='N' border='0' />";					}
					else
					{
						$paramquery = $_GET;
						$paramquery[action] = "detail";
						$paramquery[id] = $list_array[$key][id];
						$params = http_build_query($paramquery,'','&#38;');
						
						$list_array[$key][symbol][link]		= $params;
						$list_array[$key][symbol][content] 	= "<img src='images/icons/equipment.png' alt='N' border='0' />";
					
						$equipment_name = $list_array[$key][name];
						unset($list_array[$key][name]);
						$list_array[$key][name][link] 		= $params;
						$list_array[$key][name][content]		= $equipment_name;
					}
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No Equipment found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			// Error
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public function count_equipment_items($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$handling_class = Item::get_holder_handling_class_by_name($argument_array[0][1]);
		if ($handling_class)
		{
			$sql = $handling_class::get_item_list_sql($argument_array[1][1]);
		}
		
		if ($sql)
		{
			return Equipment_Wrapper::count_item_equipments($sql);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public function list_organisation_unit_related_equipment($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = Equipment_Wrapper::list_organisation_unit_equipments($organisation_unit_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$paramquery = $_GET;
					$paramquery[action] = "detail";
					$paramquery[id] = $list_array[$key][id];
					$params = http_build_query($paramquery,'','&#38;');
					
					$list_array[$key][symbol][link]		= $params;
					$list_array[$key][symbol][content] 	= "<img src='images/icons/equipment.png' alt='N' border='0' />";
				
					if ($list_array[$key][organisation_unit_id] != $_GET[ou_id])
					{
						$equipment_name = $list_array[$key][name];
						unset($list_array[$key][name]);
						$list_array[$key][name][link] 		= $params;
						$list_array[$key][name][content]		= $equipment_name." (CH)";
					}
					else
					{
						$equipment_name = $list_array[$key][name];
						unset($list_array[$key][name]);
						$list_array[$key][name][link] 		= $params;
						$list_array[$key][name][content]		= $equipment_name;
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
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public function count_organisation_unit_related_equipment($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{
			return Equipment_Wrapper::count_organisation_unit_equipments($organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
}
?>