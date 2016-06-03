<?php
/**
 * @package item
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
 * Item Listener Interface
 * @package item
 */
interface ItemListenerInterface
{
	/**
	 * Returns a set of parents
	 * Requires instance via get_instance_by_item_id($item_id)
	 * @return string
	 */
	public function get_item_parents();
	
	/**
	 * Returns the ID of the current instance
	 * Requires instance via get_instance_by_item_id($item_id)
	 * @return integer
	 */
	public function get_item_object_id();
		
	/**
	 * Returns the name of the current instance
	 * Requires instance via get_instance_by_item_id($item_id)
	 * @return string
	 */
	public function get_item_object_name();
	
	
	/**
	 * Clones the item and returns its ID
	 * @param integer $item_id
	 * @return integer
	 */
	public static function clone_item($item_id);
	
	/**
	 * Returns entry ID via item ID
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_entry_by_item_id($item_id);
	
	/**
	 * Checks if an item_id is a kind of the current class.
	 * type is an optional variable, if one class handles two or more item-types
	 * @param string $type
	 * @param integer $item_id
	 * @return bool
	 */
	public static function is_kind_of($type, $item_id);
	
	/**
	 * Checks if an item_id is in a category or a type_id. 
	 * $type_id is an internal type of the specific item. It is not the ID of the item-concreation nor the $type of is_kind_of().
	 * This is an optional method (some methods could return false everytime)
	 * @param $category_id
	 * @param integer $type_id
	 * @param integer $item_id
	 * @return bool
	 */
	public static function is_type_or_category($category_id, $type_id, $item_id);
	
	/**
	 * Returns an instance of a specific class by item-id
	 * @param integer $item_id
	 * @param boolean $light_instance Returns an instance without complex permission and dependency calculations
	 * @return object
	 */
	public static function get_instance_by_item_id($item_id, $light_instance = false);
	
	/**
	 * Returns the generic name of the item-type
	 * @param string $type
	 * @param array $type_array
	 * @return string
	 */
	public static function get_generic_name($type, $type_array);
	
	/**
	 * Returns the generic symbol of the item-type
	 * @param string $type
	 * @param integer $id
	 * @return string
	 */
	public static function get_generic_symbol($type, $id);
	
	/**
	 * Returns the generic link of the item-type
	 * @param string $type
	 * @param integer $id
	 * @return string
	 */
	public static function get_generic_link($type, $id);
	
	/**
	 * Returns the SELECT-SQL part for data-search as an array
	 * @param string $type
	 * @return array
	 */
	public static function get_sql_select_array($type);
	
	/**
	 * Returns the JOIN-SQL part for data-search
	 * @param string $type
	 * @return string
	 */
	public static function get_sql_join($type);
	
	/**
	 * Returns the WHERE-SQL part for data-search
	 * @param string $type
	 * @return string
	 */
	public static function get_sql_where($type);
	
	/**
	 * Returns the SELECT-SQL part for fulltext-search as an array
	 * @param string $type
	 * @return array
	 */
	public static function get_sql_fulltext_select_array($type);
	
	/**
	 * Returns the JOIN-SQL part for fulltext-search
	 * @param string $type
	 * @return string
	 */
	public static function get_sql_fulltext_join($type);
	
	/**
	 * Returns the WHERE-SQL part for fulltext-search
	 * @param string $type
	 * @return string
	 */
	public static function get_sql_fulltext_where($type);
	
	/**
	 * Returns the possible dialog-attributes of the requested item-type
	 * 0 => array of possbile types
	 * 1 => standard type
	 * @param string $item_type
	 * @return array
	 */
	public static function get_item_add_dialog($item_type);
	
	/**
	 * Returns the possible occurrence-attributes and the behaviour of the requested item-type
	 * array
	 * 		(
	 * 			once is possible (if false: behaviour like multiple), 
	 * 			multiple is possible (if false: behaviour like once), 
	 * 			behaviour after addgin while occurrence is set to once (possbile values: "deny", "edit")
	 * 		)
	 * @param unknown_type $item_type
	 * @return array
	 */
	public static function get_item_add_occurrence($item_type);
	
	/**
	 * Returns the name and the path of the io-class which handles the add-script
	 * @param string $item_type
	 * @return array
	 */
	public static function get_item_add_script_handling_class($item_type);
}
?>
