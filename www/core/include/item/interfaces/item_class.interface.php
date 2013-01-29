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
 * Item Class Management Interface
 * @package item
 */ 	 
interface ItemClassInterface
{
	/**
	 * @param integer $class_id
	 */
	function __construct($class_id);
	
	function __destruct();
	
	/**
     * Creates a new item-class
     * @param string $name
     * @param integer $owner_id
     * @return integer
     */
	public function create($name, $owner_id);
	
	 /**
     * Deletes a item-class
     * @return bool
     */
	public function delete();
	
	/**
     * Links an item to the current item-class
     * @param integer $item_id
     * @return bool
     */
	public function link_item($item_id);
	
	/**
     * Unlinks an item from the current item-class
     * @param integer $item_id
     * @return bool
     */
	public function unlink_item($item_id);
	
	/**
     * List all item of the current item-class
     * @return array
     */
	public function list_items();
	
	/**
     * @return string
     */
	public function get_name();
	
	/**
     * @return integer
     */
	public function get_owner_id();
	
	/**
     * @return string
     */
	public function get_datetime();
	
	/**
     * @return string
     */
	public function get_colour();
	
	/**
     * @param string $name
     * @return bool
     */
	public function set_name($name);
	
	/**
     * @param integer $owner_id
     * @return bool
     */
	public function set_owner_id($owner_id);
	
	/**
     * @param string $colour
     * @return bool
     */
	public function set_colour($colour);
	
	/**
     * @param integer $item_id
     * @return array
     */
	public static function list_classes_by_item_id($item_id);
}

?>
