<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * Base Wrapper Interface
 * @package base
 */ 		 
interface Base_WrapperInterface
{
	public static function list_system_log($type_id, $order_by, $order_method, $start, $end);
	public static function count_list_system_log($type_id);
	public static function list_base_module_navigation($start, $end);
	public static function count_base_module_navigation();
	public static function list_base_module($order_by, $order_method, $start, $end);
	public static function count_base_module();
	public static function list_base_include($order_by, $order_method, $start, $end);
	public static function count_base_include();
}
?>