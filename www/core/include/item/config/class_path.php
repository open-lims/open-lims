<?php 
/**
 * @package item
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
	$classes['ItemException']						= $path_prefix."core/include/item/exceptions/item.exception.class.php";
	
	$classes['ItemCreateException']					= $path_prefix."core/include/item/exceptions/item_create.exception.class.php";
	$classes['ItemCreateFailedException']			= $path_prefix."core/include/item/exceptions/item_create_failed.exception.class.php";
	
	$classes['ItemIDMissingException']				= $path_prefix."core/include/item/exceptions/item_id_missing.exception.class.php";
	$classes['ItemParentIDMissingException']		= $path_prefix."core/include/item/exceptions/item_parent_id_missing.exception.class.php";
	$classes['ItemParentTypeMissingException']		= $path_prefix."core/include/item/exceptions/item_parent_type_missing.exception.class.php";
	$classes['ItemPositionIDMissingException']		= $path_prefix."core/include/item/exceptions/item_position_id_missing.exception.class.php";
	$classes['ItemHolderIDMissingException']		= $path_prefix."core/include/item/exceptions/item_holder_id_missing.exception.class.php";
	$classes['ItemHolderTypeMissingException']		= $path_prefix."core/include/item/exceptions/item_holder_type_missing.exception.class.php";

	$classes['ItemAddIOException']					= $path_prefix."core/include/item/exceptions/item_add_io.exception.class.php";
	$classes['ItemAddIONotFoundException']			= $path_prefix."core/include/item/exceptions/item_add_io_not_found.exception.class.php";
	$classes['ItemAddIOClassNotFoundException']		= $path_prefix."core/include/item/exceptions/item_add_io_class_not_found.exception.class.php";
	$classes['ItemAddIOFileNotFoundException']		= $path_prefix."core/include/item/exceptions/item_add_io_file_not_found.exception.class.php";
	
	$classes['ItemHandlerException']				= $path_prefix."core/include/item/exceptions/item_handler.exception.class.php";
	$classes['ItemHandlerNotFoundException']		= $path_prefix."core/include/item/exceptions/item_handler_not_found.exception.class.php";
	$classes['ItemHandlerClassNotFoundException']	= $path_prefix."core/include/item/exceptions/item_handler_class_not_found.exception.class.php";
	
	$classes['ItemListenerInterface']				= $path_prefix."core/include/item/interfaces/item_listener.interface.php";
	$classes['ItemHolderInterface']					= $path_prefix."core/include/item/interfaces/item_holder.interface.php";
	$classes['ItemHolderListenerInterface'] 		= $path_prefix."core/include/item/interfaces/item_holder_listener.interface.php";
	
	$classes['ItemTypeException']					= $path_prefix."core/include/item/exceptions/item_type.exception.class.php";
	$classes['ItemTypeRequiredException']			= $path_prefix."core/include/item/exceptions/item_type_required.exception.class.php";
	
	
	$classes['ItemUnlinkEvent']						= $path_prefix."core/include/item/events/item_unlink_event.class.php";
	$classes['ItemAddEvent']						= $path_prefix."core/include/item/events/item_add_event.class.php";
	$classes['ItemAddHolderEvent']					= $path_prefix."core/include/item/events/item_add_holder_event.class.php";
	$classes['ItemHolderAddEvent']					= $path_prefix."core/include/item/events/item_holder_add_event.class.php";
	
	$classes['Item']								= $path_prefix."core/include/item/item.class.php";
	$classes['ItemClass']							= $path_prefix."core/include/item/item_class.class.php";
	$classes['ItemInformation']						= $path_prefix."core/include/item/item_information.class.php";
	
	$classes['Item_Wrapper']						= $path_prefix."core/include/item/item.wrapper.class.php";
?>