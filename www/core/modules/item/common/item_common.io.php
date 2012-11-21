<?php
/**
 * @package item
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
 * Item Common IO Class
 * @package item
 */
class ItemCommonIO
{
	/**
	 * Generates an item menu entry with sub-items
	 * @param array $element_array
	 * @param integer $key
	 * @param integer $counter
	 * @param array $link_base
	 * @param string $holder_class
	 * @param string $holder_id
	 * @return array
	 */
	public static function get_menu_element($element_array, $key, $counter, $link_base, $holder_class, $holder_id)
	{		
		$result = array();
		
		$amount = count($element_array['fulfilled']);
		
		if ($element_array['display'] == true)
		{
			if ($element_array['occurrence'] == "multiple" and $amount > 0)
			{
				$result[$counter]['name'] = $element_array['name']." (".$amount.")";
			}
			else
			{
				$result[$counter]['name'] = $element_array['name'];
			}
			
			$result[$counter]['depends'] = false;
			
			$item_handling_cass = $element_array['handling_class'];
			
			$paramquery = $link_base;
			$paramquery['run'] = "item_add";
			$paramquery['dialog'] = $element_array['type'];
			$paramquery['key'] = $key;
			$paramquery['retrace'] = Retrace::create_retrace_string();
			

			$item_add_occurrence_array = $item_handling_cass::get_item_add_occurrence($element_array['type']);
			
			if (!is_array($item_add_occurrence_array) or count($item_add_occurrence_array) != 3)
			{
				$item_add_occurrence_array = array(true, true, "deny");
			}
			
			
			if ($element_array['occurrence'] == "once" and $item_add_occurrence_array[0] == true and $item_add_occurrence_array[2] == "edit" and is_array($element_array['fulfilled']) and $amount >= 1)
			{
				$paramquery['run'] = "item_edit";
			}
			
			
			$item_add_dialog_array = $item_handling_cass::get_item_add_dialog($element_array['type']);
			
			if (is_array($item_add_dialog_array))
			{
				if (in_array($element_array['dialog'], $item_add_dialog_array[0]))
				{
					$item_array_type = $element_array['dialog'];
				}
				else
				{
					$item_array_type = $item_add_dialog_array[1];
				}
				
				if (trim($item_array_type) == "window")
				{
					$result[$counter]['type'] = "ajax";
					$ajax_handling_array = $item_handling_cass::get_item_add_script_handling_class($element_array['type']);
					require_once("core/modules/".$ajax_handling_array[0]);
					
					$ajax_init_array = $ajax_handling_array[1]::$ajax_handling_array[2]($element_array['pos_id'], $paramquery, $element_array['type_id'],  $element_array['category_id'], $holder_class, $holder_id);
					
					$result[$counter]['script'] = $ajax_init_array['script'];
					$result[$counter]['window_title'] = $ajax_init_array['window_title'];
					$result[$counter]['window_id'] = $ajax_init_array['window_id'];
					$result[$counter]['click_id'] = $ajax_init_array['click_id'];
				}
				else
				{
					$result[$counter]['type'] = "link";
				}
			}
			else
			{
				$result[$counter]['type'] = "link";
			}
			
			
			if (is_array($element_array['fulfilled']) and $amount >= 1)
			{
				if (($element_array['occurrence'] == "multiple" and $item_add_occurrence_array[1] == true) or 
					($element_array['occurrence'] == "once" and $item_add_occurrence_array[0] == false) or 
					($element_array['occurrence'] == "once" and $item_add_occurrence_array[0] == true and $item_add_occurrence_array[2] == "edit"))
				{
					$result[$counter]['image'] = "add_done";
				}
				else
				{
					$result[$counter]['type'] = false;
					$result[$counter]['image'] = "add_done_na";
				}
			}
			else
			{
				$result[$counter]['image'] = "add";
			}

			if ($element_array['requirement'] == "optional")
			{
				$result[$counter]['name'] = $result[$counter]['name']." (optional)";
			}
			
			$params = http_build_query($paramquery,'','&#38;');
			$result[$counter]['params'] = $params;					

			$counter++;
		}
		
		if (is_array($element_array['sub_items']) and count($element_array['sub_items']) >= 1)
		{
			$result[$counter]['type'] = "line";
			$counter++;
			
			$sub_item_irgnore_array = array();

			foreach($element_array['sub_items'] as $sub_item_key => $sub_item_value)
			{
				foreach($sub_item_value as $sub_sub_item_key => $sub_sub_item_value)
				{
					if (!in_array($sub_sub_item_key, $sub_item_irgnore_array))
					{
						if ($sub_sub_item_value['element_type'] == "item")
						{
							$paramquery = $link_base;
							$paramquery['run'] = "sub_item_add";
							$paramquery['dialog'] = $sub_sub_item_value['type'];
							$paramquery['key'] = $sub_sub_item_value['pos_id'];
							$paramquery['parent'] = $element_array['type'];
							$paramquery['parent_key'] = $element_array['pos_id'];
							
							if ($sub_sub_item_value['takeover'] == false)
							{
								$paramquery['parent_id'] = $element_array['fulfilled'][$sub_item_key]['id'];
							}
							
							$paramquery['retrace'] = Retrace::create_retrace_string();
							
							
							$item_handling_cass = $sub_sub_item_value['handling_class'];
							
							$item_add_occurrence_array = $item_handling_cass::get_item_add_occurrence($sub_sub_item_value['type']);
			
							if (!is_array($item_add_occurrence_array) or count($item_add_occurrence_array) != 3)
							{
								$item_add_occurrence_array = array(true, true, "deny");
							}
							
							
							if ($sub_sub_item_value['occurrence'] == "once" and $item_add_occurrence_array[0] == true and $item_add_occurrence_array[2] == "edit" and is_array($sub_sub_item_value['fulfilled']) and count($sub_sub_item_value['fulfilled']) >= 1)
							{
								$paramquery['run'] = "sub_item_edit";
							}
							
							$item_add_dialog_array = $item_handling_cass::get_item_add_dialog($sub_sub_item_value['type']);
							
							if (is_array($item_add_dialog_array))
							{
								if (in_array($sub_sub_item_value['dialog'], $item_add_dialog_array[0]))
								{
									$item_array_type = $sub_sub_item_value['dialog'];
								}
								else
								{
									$item_array_type = $item_add_dialog_array[1];
								}
								
								if (trim($item_array_type) == "window")
								{
									$result[$counter]['type'] = "ajax";
									$ajax_handling_array = $item_handling_cass::get_item_add_script_handling_class($sub_sub_item_value['type']);
									require_once("core/modules/".$ajax_handling_array[0]);
									
									$item_holder = Item::get_holder_handling_class_by_name($element_array['type']); // Type of the ItemHolder
									$ajax_init_array = $ajax_handling_array[1]::$ajax_handling_array[2]($sub_sub_item_value['pos_id'], $paramquery, $sub_sub_item_value['type_id'],  $sub_sub_item_value['category_id'], $item_holder, $element_array['fulfilled'][$sub_item_key]['id']);
									
									$result[$counter]['script'] = $ajax_init_array['script'];
									$result[$counter]['window_title'] = $ajax_init_array['window_title'];
									$result[$counter]['window_id'] = $ajax_init_array['window_id'];
									$result[$counter]['click_id'] = $ajax_init_array['click_id'];
								}
								else
								{
									$result[$counter]['type'] = "link";
								}
							}
							else
							{
								$result[$counter]['type'] = "link";
							}
							
							
							if ($sub_sub_item_value['takeover'] == true)
							{
								$result[$counter]['name'] = $sub_sub_item_value['name']." (all)";
								array_push($sub_item_irgnore_array, $sub_sub_item_key);
							}
							else
							{
								if ($element_array['fulfilled'][$sub_item_key]['name'])
								{
									$result[$counter]['name'] = $sub_sub_item_value['name']." (".$element_array['fulfilled'][$sub_item_key]['name'].")";
								}
								else
								{
									$result[$counter]['name'] = $sub_sub_item_value['name'];
								}
							}

							
							if (is_array($sub_sub_item_value['fulfilled']))
							{
								if (($sub_sub_item_value['occurrence'] == "multiple" and $item_add_occurrence_array[1] == true) or 
									($sub_sub_item_value['occurrence'] == "once" and $item_add_occurrence_array[0] == false) or 
									($sub_sub_item_value['occurrence'] == "once" and $item_add_occurrence_array[0] == true and $item_add_occurrence_array[2] == "edit"))
								{
									$result[$counter]['image'] = "add_done";
								}
								else
								{
									$result[$counter]['type'] = false;
									$result[$counter]['image'] = "add_done_na";
								}
							}
							else
							{
								$result[$counter]['image'] = "add";
							}

							$params = http_build_query($paramquery,'','&#38;');
							$result[$counter]['depends'] = true;
							$result[$counter]['params'] = $params;

							$counter++;
						}
					}
				}

				if ($result[$counter-1]['type'] != "line")
				{
					$result[$counter]['type'] = "line";
					$counter++;
				}
			}
		}
				
		return array(0 => $result, 1 => $counter);
	}
}