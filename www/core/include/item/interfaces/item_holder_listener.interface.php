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
 * Item Holder Interface for Items which are Item Listeners too
 * implement ItemListenerInterface too
 * @package item
 */
interface ItemHolderListenerInterface extends ItemHolderInterface
{
	/**
	 * Returns the items of the item holder
	 * @param integer $position_id
	 * @return array
	 */
	public function get_item_holder_items($position_id);
	
	/**
	 * Returns the I/O logic handling class for adding an item via link
	 * @return array
	 */
	public static function get_item_add_io_handling_class();
}
?>
