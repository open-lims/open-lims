<?php
/**
 * @package data
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
 * Data Search Wrapper Interface
 * @package data
 */ 		 
interface Data_WrapperInterface
{
	/**
	 * @param integer $folder_id
	 * @param string $name
	 * @return bool
	 */
	public static function is_file_in_folder($folder_id, $name);
	
	/**
	 * @param integer $folder_id
	 * @return bool
	 */
	public static function has_folder_children($folder_id);
	
	/**
	 * @param integer $folder_id
	 * @param string $search_string
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_search_ffv($folder_id, $search_string, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $folder_id
	 * @param string $search_string
	 * @return integer
	 */
	public static function count_search_ffv($folder_id, $search_string);
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_value_templates($order_by, $order_method, $start, $end);
	
	/**
     * @return integer
     */
	public static function count_list_value_templates();
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_parameter_templates($order_by, $order_method, $start, $end);
	
	/**
     * @return integer
     */
	public static function count_list_parameter_templates();
	
	/**
	 * @param integer $value_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_value_versions($value_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $value_id
	 * @return integer
	 */
	public static function count_value_versions($value_id);
	
	/**
	 * @param integer $file_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_file_versions($file_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $file_id
	 * @return integer
	 */
	public static function count_file_versions($file_id);
	
	/**
	 * @param string $sql
	 * @return array
	 */
	public static function list_item_files($sql, $order_by, $order_method, $start, $end);
	
	/**
	 * @param string $sql
	 * @return array
	 */
	public static function list_item_values($sql);
	
	/**
	 * @param string $sql
	 * @return array
	 */
	public static function list_item_parameters($sql);
}
?>
