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
 * Equipment Request Class
 * @package equipment
 */
class EquipmentRequest
{	
	public static function ajax_handler()
	{
		switch($_GET[run]):
			
			case "list_equipment_items":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::list_equipment_items($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "count_equipment_items":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::count_equipment_items($_POST[argument_array]);
			break;
			
			case "list_organisation_unit_related_equipment":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::list_organisation_unit_related_equipment($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "count_organisation_unit_related_equipment":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::count_organisation_unit_related_equipment($_POST[argument_array]);
			break;
			
			case "equipment_item_add_window":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::item_add_window($_POST[get_array]);
			break;
			
			case "equipment_item_add_action":
				require_once("ajax/equipment.ajax.php");
				echo EquipmentAjax::item_add_window($_POST[get_array]);
			break;
				
		endswitch;
	}
	
	public static function io_handler()
	{

	}

}
?>