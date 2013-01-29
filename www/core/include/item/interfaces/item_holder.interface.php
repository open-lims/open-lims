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
 * Item Holder Interface
 * @package item
 */
interface ItemHolderInterface
{	
	/**
	 * @param integer $id
	 * @return array
	 */
	public function get_item_add_information($id = null);
	
	/**
	 * @param string $address
	 * @return mixed
	 */
	public function get_item_holder_value($address, $position_id = null);
	
	
	/**
	 * @param integer $holder_id
	 * @return string
	 */
	public static function get_item_list_sql($holder_id);
	
	/**
	 * @param integer $item_id
	 * @return array
	 */
	public static function list_item_holders_by_item_id($item_id);
}
?>
