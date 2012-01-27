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
 * 
 */
	$classes['EquipmentException']						= $path_prefix."core/include/equipment/exceptions/equipment.exception.class.php";	
	$classes['EquipmentNotFoundException']				= $path_prefix."core/include/equipment/exceptions/equipment_not_found.exception.class.php";
	$classes['EquipmentIDMissingException']				= $path_prefix."core/include/equipment/exceptions/equipment_id_missing.exception.class.php";
	$classes['EquipmentTypeException']					= $path_prefix."core/include/equipment/exceptions/equipment_type.exception.class.php";
	$classes['EquipmentTypeNotFoundException']			= $path_prefix."core/include/equipment/exceptions/equipment_type_not_found.exception.class.php";
	$classes['EquipmentTypeIDMissingException']			= $path_prefix."core/include/equipment/exceptions/equipment_type_id_missing.exception.class.php";
	$classes['EquipmentCategoryException']				= $path_prefix."core/include/equipment/exceptions/equipment_category.exception.class.php";
	$classes['EquipmentCategoryNotFoundException']		= $path_prefix."core/include/equipment/exceptions/equipment_category_not_found.exception.class.php";
	$classes['EquipmentCategoryIDMissingException']		= $path_prefix."core/include/equipment/exceptions/equipment_category_id_missing.exception.class.php";
	
	$classes['Equipment']					= $path_prefix."core/include/equipment/equipment.class.php";
	$classes['EquipmentCat']				= $path_prefix."core/include/equipment/equipment_cat.class.php";
	$classes['EquipmentType']				= $path_prefix."core/include/equipment/equipment_type.class.php";
	
	$classes['Equipment_Wrapper']			= $path_prefix."core/include/equipment/equipment.wrapper.class.php";
?>