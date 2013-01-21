<?php 
/**
 * @package equipment
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
 * 
 */
	$dialog[0]['type']				= "item_list";
	$dialog[0]['class_path']		= "core/modules/equipment/io/equipment.io.php";
	$dialog[0]['class']				= "EquipmentIO";
	$dialog[0]['method']			= "list_equipment_item_handler";
	$dialog[0]['internal_name']		= "equipment";
	$dialog[0]['language_address']	= "EquipmentDialogItemEquipmentList";
	$dialog[0]['weight']			= 200;
		
	$dialog[1]['type']				= "module_admin";
	$dialog[1]['class_path']		= "core/modules/equipment/io/admin/admin_equipment_cat.io.php";
	$dialog[1]['class']				= "AdminEquipmentCatIO";
	$dialog[1]['method']			= "handler";
	$dialog[1]['internal_name']		= "equipment_cat";
	$dialog[1]['language_address']	= "EquipmentDialogAdminMenuCat";
	$dialog[1]['weight']			= 10000;
	
	$dialog[2]['type']				= "module_admin";
	$dialog[2]['class_path']		= "core/modules/equipment/io/admin/admin_equipment_type.io.php";
	$dialog[2]['class']				= "AdminEquipmentTypeIO";
	$dialog[2]['method']			= "handler";
	$dialog[2]['internal_name']		= "equipment_type";
	$dialog[2]['language_address']	= "EquipmentDialogAdminMenuTypes";
	$dialog[2]['weight']			= 10100; 
	
	$dialog[3]['type']				= "common_dialog";
	$dialog[3]['class_path']		= "core/modules/equipment/io/equipment.io.php";
	$dialog[3]['class']				= "EquipmentIO";
	$dialog[3]['method']			= "list_organisation_unit_related_equipment_handler";
	$dialog[3]['internal_name']		= "list_ou_equipment";
	
	$dialog[4]['type']				= "item_report";
	$dialog[4]['class_path']		= "core/modules/equipment/report/equipment_report.io.php";
	$dialog[4]['class']				= "EquipmentReportIO";
	$dialog[4]['method']			= "get_equipment_item_report";
	$dialog[4]['internal_name']		= "equipment_item_report";
	$dialog[4]['weight']			= 500;
	
	$dialog[5]['type']				= "item_assistant_list";
	$dialog[5]['class_path']		= "core/modules/equipment/io/equipment.io.php";
	$dialog[5]['class']				= "EquipmentIO";
	$dialog[5]['method']			= "list_equipment_items";
	$dialog[5]['internal_name']		= "equipment";
	$dialog[5]['weight']			= 200;
?>