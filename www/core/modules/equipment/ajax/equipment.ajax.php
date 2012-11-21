<?php
/**
 * @package equipment
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
	public static function list_equipment_items($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
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
							$list_array[$key]['checkbox'] = "<input type='checkbox' name='equipment-".$list_array[$key]['item_id']."' value='1' class='".$checkbox_class."' checked='checked' />";
						}
						else
						{
							$list_array[$key]['checkbox'] = "<input type='checkbox' name='equipment-".$list_array[$key]['item_id']."' value='1' checked='checked' />";
						}
						
						$list_array[$key]['symbol']	= "<img src='images/icons/equipment.png' alt='N' border='0' />";					}
					else
					{
						$paramquery = $_GET;
						$paramquery['action'] = "detail";
						$paramquery['id'] = $list_array[$key]['id'];
						$params = http_build_query($paramquery,'','&#38;');
						
						$list_array[$key]['symbol']['link']		= $params;
						$list_array[$key]['symbol']['content'] 	= "<img src='images/icons/equipment.png' alt='N' border='0' />";
					
						$equipment_name = $list_array[$key]['name'];
						unset($list_array[$key]['name']);
						$list_array[$key]['name']['link'] 		= $params;
						$list_array[$key]['name']['content']		= $equipment_name;
					}
					
					$datetime_handler = new DatetimeHandler($list_array[$key]['datetime']);
					$list_array[$key]['datetime'] = $datetime_handler->get_formatted_string("dS M Y H:i");
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
	public static function count_equipment_items($json_argument_array)
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
	public static function list_organisation_unit_related_equipment($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
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
					$paramquery['action'] = "detail";
					$paramquery['id'] = $list_array[$key]['id'];
					$params = http_build_query($paramquery,'','&#38;');
					
					$list_array[$key]['symbol']['link']		= $params;
					$list_array[$key]['symbol']['content'] 	= "<img src='images/icons/equipment.png' alt='N' border='0' />";
				
					if ($list_array[$key]['organisation_unit_id'] != $_GET['ou_id'])
					{
						$equipment_name = $list_array[$key]['name'];
						unset($list_array[$key]['name']);
						$list_array[$key]['name']['link'] 		= $params;
						$list_array[$key]['name']['content']	= $equipment_name." (CH)";
					}
					else
					{
						$equipment_name = $list_array[$key]['name'];
						unset($list_array[$key]['name']);
						$list_array[$key]['name']['link'] 		= $params;
						$list_array[$key]['name']['content']	= $equipment_name;
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
	public static function count_organisation_unit_related_equipment($json_argument_array)
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
	
	/**
	 * @param integer $gid
	 * @param array $link
	 * @param array $type_array
	 * @param array $category_array
	 * @param string $holder_class
	 * @param integer $holder_id
	 * @return array
	 */
	public static function add_as_item_window_init($gid, $link, $type_array, $category_array, $holder_class, $holder_id)
	{		
		if ($link['parent'] and is_numeric($link['parent_id']))
		{
			$array['window_id'] = "EquipmentItemAddWindow".$link['parent_key']."-".$link['parent_id']."-".$gid;
			$array['click_id'] = "EquipmentItemAddButton".$link['parent_key']."-".$link['parent_id']."-".$gid;
		}
		else
		{
			$array['window_id'] = "EquipmentItemAddWindow".$gid;
			$array['click_id'] = "EquipmentItemAddButton".$gid;
		}
						
		if ($type_array)
		{
			$type_array_serialized = serialize($type_array);
		}
		
		if ($category_array)
		{
			$category_array_serialized = serialize($category_array);
		}
		
		$script_template = new JSTemplate("equipment/js/add_item_window_preclick.js");
		$script_template->set_var("window_id", $array['window_id']);
		$script_template->set_var("session_id", $_GET['session_id']);
		$script_template->set_var("get_array", serialize($link));
		$script_template->set_var("type_array", $type_array_serialized);
		$script_template->set_var("category_array", $category_array_serialized);
		$script_template->set_var("click_id", $array['click_id']);
		
		$array['script'] = $script_template->get_string();
		
		return $array;
	}
	
	/**
	 * @param string $get_array
	 * @param string $type_array
	 * @param string $category_array
	 * @return string
	 */
	public static function add_as_item_window($get_array, $type_array, $category_array)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($type_array)
		{
			$type_array = unserialize($type_array);	
		}
		
		if ($category_array)
		{
			$category_array = unserialize($category_array);	
		}

		$template = new HTMLTemplate("equipment/add_item_window.html");
		
		$equipment_array = EquipmentType::list_entries();
		
		$result = array();
		$hit_array = array();
		$counter = 0;
		
		if (is_array($type_array) and count($type_array) >= 1)
		{
			if (is_array($equipment_array) and count($equipment_array) >= 1)
			{
				foreach($equipment_array as $key => $value)
				{
					if (in_array($value, $type_array) or $value <= 3)
					{
						$equipment_type = new EquipmentType($value);
					
						$result[$counter]['value'] = $value;
						$result[$counter]['disabled'] = "";
						$result[$counter]['content'] = $equipment_type->get_name()." (".$equipment_type->get_cat_name().")";
						
						$counter++;
						array_push($hit_array, $value);
					}
				}
			}
			
			if (is_array($category_array) and count($category_array) >= 1)
			{
				foreach ($category_array as $key => $value)
				{
					$equipment_cat_array = EquipmentType::list_entries_by_cat_id($value);
					
					if (is_array($equipment_cat_array) and count($equipment_cat_array) >= 1)
					{
						foreach ($equipment_cat_array as $key => $value)
						{
							if (!in_array($value, $hit_array))
							{
								$equipment_type = new EquipmentType($value);
						
								$result[$counter]['value'] = $value;
								$result[$counter]['disabled'] = "";
								$result[$counter]['content'] = $equipment_type->get_name()." (".$equipment_type->get_cat_name().")";
								
								$counter++;
								array_push($hit_array, $value);
							}
						} 
					}
				}
			}
		}
		else
		{
			if (is_array($category_array) and count($category_array) >= 1)
			{
				foreach ($category_array as $key => $value)
				{
					$equipment_cat_array = EquipmentType::list_entries_by_cat_id($value);
	
					if (is_array($equipment_cat_array) and count($equipment_cat_array) >= 1)
					{
						if (!in_array(1, $equipment_cat_array))
						{
							$equipment_cat_array[] = 1;
						}
						
						if (!in_array(2, $equipment_cat_array))
						{
							$equipment_cat_array[] = 2;
						}
						
						if (!in_array(3, $equipment_cat_array))
						{
							$equipment_cat_array[] = 3;
						}
						
						foreach ($equipment_cat_array as $key => $value)
						{
							if (!in_array($value, $hit_array))
							{
								$equipment_type = new EquipmentType($value);
						
								$result[$counter]['value'] = $value;
								$result[$counter]['disabled'] = "";
								$result[$counter]['content'] = $equipment_type->get_name()." (".$equipment_type->get_cat_name().")";
								
								$counter++;
								array_push($hit_array, $value);
							}
						} 
					}
				}
			}
			else
			{
				if (is_array($equipment_array) and count($equipment_array) >= 1)
				{
					foreach($equipment_array as $key => $value)
					{
						$equipment_type = new EquipmentType($value);
						
						$result[$counter]['value'] = $value;
						$result[$counter]['disabled'] = "";
						$result[$counter]['content'] = $equipment_type->get_name()." (".$equipment_type->get_cat_name().")";
						
						$counter++;
					}
				}
			}
		}

		if ($counter == 0)
		{
			$result[0]['value'] = "0";
			$result[0]['disabled'] = "disabled='disabled'";
			$result[0]['content'] = "NO EQUIPMENT FOUND!";	
		}
		
		$template->set_var("select",$result);
		
		if ($_GET['parent'] and is_numeric($_GET['parent_id']))
		{
			$array['container'] = "#EquipmentItemAddWindow".$_GET['parent_key']."-".$_GET['parent_id']."-".$_GET['key'];
			$container_value_select = "EquipmentItemAddValueField".$_GET['parent_key']."-".$_GET['parent_id']."-".$_GET['key'];
		}
		else
		{
			$array['container'] = "#EquipmentItemAddWindow".$_GET['key'];
			$container_value_select = "EquipmentItemAddValueField".$_GET['key'];
		}
		
		$template->set_var("container_value_select_id",$container_value_select);
		
		$array['continue_caption'] = "Add";
		$array['cancel_caption'] = "Cancel";
		$array['content_caption'] = "Add Equipment";
		$array['height'] = 350;
		$array['width'] = 400;
		$array['content'] = $template->get_string();
		
		$continue_handler_template = new JSTemplate("equipment/js/add_item_window.js");
		$continue_handler_template->set_var("session_id", $_GET['session_id']);
		$continue_handler_template->set_var("get_array", $get_array);
		$continue_handler_template->set_var("container_id", $array['container']);
		$continue_handler_template->set_var("container_value_select_id", $container_value_select);

		$array['continue_handler'] = $continue_handler_template->get_string();
		
		return json_encode($array);
	}
	
	/**
	 * @param array $get_array
	 * @param integer $type_id
	 */
	public static function add_as_item($get_array, $type_id)
	{
		global $user, $transaction;
		
		if ($get_array and is_numeric($type_id))
		{
			$transaction_id = $transaction->begin();
			
			$equipment = new Equipment(null);
			$equipment_add_successful = $equipment->create($type_id, $user->get_user_id());
	
			if ($equipment_add_successful)
			{
				$item_id = $equipment->get_item_id();
				
				$item_add_event = new ItemAddEvent($item_id, unserialize($get_array), null);
				$event_handler = new EventHandler($item_add_event);
				if ($event_handler->get_success() == true)
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return "1";
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new EquipmentCreateException();
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new EquipmentCreateException();
			}
		}
		else
		{
			throw new EquipmentIDMissingException();
		}
	}
}
?>