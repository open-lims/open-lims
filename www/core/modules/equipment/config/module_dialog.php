<?php 
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
	$dialog[0][type]			= "item_list";
	$dialog[0][class_path]		= "core/modules/equipment/equipment.io.php";
	$dialog[0]['class']			= "EquipmentIO";
	$dialog[0][method]			= "list_equipment_item_handler";
	$dialog[0][internal_name]	= "equipment";
	$dialog[0][display_name]	= "Equipment";
	$dialog[0][weight]			= 200;
	
	$dialog[1][type]			= "item_add";
	$dialog[1][class_path]		= "core/modules/equipment/equipment.io.php";
	$dialog[1]['class']			= "EquipmentIO";
	$dialog[1][method]			= "add_equipment_item";
	$dialog[1][internal_name]	= "equipment";
	$dialog[1][display_name]	= "Equipment";
	
	$dialog[2][type]			= "module_admin";
	$dialog[2][class_path]		= "core/modules/equipment/admin/admin_equipment_cat.io.php";
	$dialog[2]['class']			= "AdminEquipmentCatIO";
	$dialog[2][method]			= "handler";
	$dialog[2][internal_name]	= "equipment_cat";
	$dialog[2][display_name]	= "Equipment Categories";
	$dialog[2][weight]			= 10000;
	
	$dialog[3][type]			= "module_admin";
	$dialog[3][class_path]		= "core/modules/equipment/admin/admin_equipment_type.io.php";
	$dialog[3]['class']			= "AdminEquipmentTypeIO";
	$dialog[3][method]			= "handler";
	$dialog[3][internal_name]	= "equipment_type";
	$dialog[3][display_name]	= "Equipment Types";
	$dialog[3][weight]			= 10100; 
?>