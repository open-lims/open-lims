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
 * Item Management Interface
 * @package item
 */ 	 
interface ItemInterface
{	
	/**
	 * @return integer
	 */
	public function get_item_id();
	
	/**
	 * Checks if the current item is classified
	 * @return bool
	 */
	public function is_classified();
	
	/**
	 * Returns the class-ids of the current item
	 * @return integer
	 */
	public function get_class_ids();
	
	/**
	 * @return string
	 */
	public function get_information();
	
	/**
	 * @return string
	 */
	public function get_datetime();
	
	/**
	 * @param string $type
	 * @param string $handling_class
	 * @param integer $include_id
	 * @return bool
	 */
	public static function register_type($type, $handling_class, $include_id);
	
	/**
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_type_by_include_id($include_id);
	
	/**
	 * @return array
	 */
	public static function list_types();
	
	/**
	 * @return array
	 */
	public static function list_holders();
	
	/**
	 * Returns the handling class by the type
	 * @param string $type
	 * @return string
	 */
	public static function get_handling_class_by_type($type);
	
	/**
	 * @param string $name
	 * @param string $handling_class
	 * @param integer $include_id
	 * @return bool
	 */
	public static function register_holder($name, $handling_class, $include_id);
	
	/**
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_holder_by_include_id($include_id);
	
	/**
	 * @param string $name
	 * @return string
	 */
	public static function get_holder_handling_class_by_name($name);
}

?>
