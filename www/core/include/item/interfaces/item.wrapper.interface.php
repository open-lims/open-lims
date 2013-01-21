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
interface Item_WrapperInterface
{
	/**
	 * @param string $string
	 * @param array $item_type_array
	 * @param integer $language_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_fulltext_search($string, $item_type_array, $language_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param string $string
	 * @param array $item_type_array
	 * @param integer $language_id
	 * @return integer
	 */
	public static function count_fulltext_search($string, $item_type_array, $language_id);
}

?>