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
 * Equipment Management Interface
 * @package equipment
 */ 		 
interface EquipmentInterface
{
	/**
	 * @param integer $equipment_id
	 */
	function __construct($equipment_id);
	
	/**
	 * Creates a new equipment
	 * @param integer $type_id
	 * @param integer $owner_id
	 * @return integer
	 */
	public function create($type_id, $owner_id);
	
	/**
	 * Deletes a equipment
	 * @return bool
	 */
	public function delete();
	
	/**
	 * @return integer
	 */
	public function get_type_id();
	
	/**
	 * @return integer
	 */
	public function get_owner_id();
	
	/**
	 * @return string
	 */
	public function get_datetime();
	
	/**
	 * @return array
	 */
	public static function list_entries_by_user_id($user_id);
	
	/**
	 * @param integer $type_id
	 * @return array
	 */
	public static function list_entries_by_type_id($type_id);
}

?>
