<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
 * User Wrapper Interface
 * @package base
 */ 	 
interface User_WrapperInterface
{
	/**
	 * @param string $username
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_search_users($username, $order_by, $order_method, $start, $end);
    
    /**
     * @param string $username
	 * @return integer
	 */
	public static function count_search_users($username);
	
	/**
	 * @param string $groupname
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_search_groups($groupname, $order_by, $order_method, $start, $end);
    
    /**
	 * @param string $groupname
	 * @return integer
	 */
	public static function count_search_groups($groupname);
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_users($order_by, $order_method, $start, $end);
	
	/**
   	 * @return integer
   	 */
   	public static function count_users();
   	
   /**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_groups($order_by, $order_method, $start, $end);
	
	/**
   	 * @return integer
   	 */
   	public static function count_groups();
}
?>
