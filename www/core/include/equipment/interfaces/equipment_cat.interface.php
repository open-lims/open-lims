<?php
/**
 * @package equipment
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Equipment Category Management Interface
 * @package equipment
 */ 		 
interface EquipmentCatInterface
{
	/**
	 * @param integer $equipment_cat_id
	 */
	function __construct($equipment_cat_id);
	
	function __destruct();
	
	/**
	 * Creates a equipment-category
	 * @param integer $toid
	 * @param string $name
	 * @return integer
	 */
	public function create($toid, $name);
	
	/**
	 * Deletes a equipment-category
	 * @return bool
	 */
	public function delete();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
	 * Returns the children of the current equipment-category
	 * @return array
	 */
	public function get_children();
	
	/**
     * @param integer $id
     * @return bool
     */
	public static function exist_id($id);
	
	/**
     * @param string $name
     * @return bool
     */
	public static function exist_name($name);
	
	/**
     * @return array
     */
	public static function list_root_entries();
	
	/**
     * @return array
     */
	public static function list_entries();
}

?>
