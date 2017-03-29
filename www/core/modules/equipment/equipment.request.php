<?php
/**
 * @package equipment
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Equipment Request Class
 * @package equipment
 */
class EquipmentRequest
{	
	/**
	 * @param string $alias
	 */
	public static function ajax_handler($alias)
	{
		switch(System::get_get("run")):
			
			case "list_equipment_items":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::list_equipment_items(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_equipment_items":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::count_equipment_items(System::get_post("argument_array"));
			break;
			
			case "list_organisation_unit_related_equipment":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::list_organisation_unit_related_equipment(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_organisation_unit_related_equipment":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::count_organisation_unit_related_equipment(System::get_post("argument_array"));
			break;
			
			case "equipment_add_as_item_window":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::add_as_item_window(System::get_post("get_array"), 
														System::get_post("type_array"), 
														System::get_post("category_array"));
			break;
			
			case "equipment_add_as_item":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::add_as_item(System::get_post("get_array"), System::get_post("type_id"));
			break;
				
		endswitch;
	}
	
	/**
	 * @param string $alias
	 */
	public static function io_handler($alias)
	{

	}
}
?>