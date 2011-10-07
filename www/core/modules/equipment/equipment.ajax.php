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
 * 
 */
require_once("../base/ajax.php");

/**
 * Equipment AJAX IO Class
 * @package equipment
 */
class EquipmentAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	private function list_equipment_items($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
	{		
		$argument_array = json_decode($json_argument_array);
		
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
			$list_request->set_row_array($json_row_array);
								
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					if ($argument_array[3][1] == true)
					{
						$row_array = json_decode($json_row_array);
						if (is_array($row_array) and count($row_array) >= 1)
						{
							foreach ($row_array as $row_key => $row_value)
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
	
	private function count_equipment_items($json_argument_array)
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
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
	
				case "list_equipment_items":
					echo $this->list_equipment_items($_POST[row_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "count_equipment_items":
					echo $this->count_equipment_items($_POST[argument_array]);
				break;
				
				default:
				break;
			
			endswitch;
		}
	}
}

$equipment_ajax = new EquipmentAjax;
$equipment_ajax->method_handler();
?>