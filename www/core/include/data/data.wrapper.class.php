<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * 
 */
require_once("interfaces/data.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{ 
	require_once("access/data.wrapper.access.php");
}

/**
 * Data Wrapper Class
 * @package data
 */
class Data_Wrapper implements Data_WrapperInterface
{
	/**
	 * @param integer $folder_id
	 * @param string $name
	 * @return bool
	 */
	public static function is_file_in_folder($folder_id, $name)
	{
		return Data_Wrapper_Access::is_file_in_folder($folder_id, $name);
	}
	
	/**
	 * @param integer $folder_id
	 * @param string $search_string
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_search_ffv($folder_id, $search_string, $order_by, $order_method, $start, $end)
	{
		return Data_Wrapper_Access::list_search_ffv($folder_id, $search_string, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * @param integer $folder_id
	 * @param string $search_string
	 * @return integer
	 */
	public static function count_search_ffv($folder_id, $search_string)
	{
		return Data_Wrapper_Access::count_search_ffv($folder_id, $search_string);
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_value_templates($order_by, $order_method, $start, $end)
    {
    	return Data_Wrapper_Access::list_value_templates($order_by, $order_method, $start, $end);
    }
    
    /**
     * @return integer
     */
    public static function count_list_value_templates()
    {
    	return Data_Wrapper_Access::count_list_value_templates();
    }  
}
?>