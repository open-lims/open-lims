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
 * 
 */
require_once("interfaces/base.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base.wrapper.access.php");
}

/**
 * Base Wrapper Class
 * @package base
 */
class Base_Wrapper implements Base_WrapperInterface
{
    /**
     * @see Base_WrapperInterface::list_system_log()
     * @param integer $type_id
     * @param string $order_by
     * @param string $order_method
     * @param integer $start
     * @param integer $end
     * @return array
     */
    public static function list_system_log($type_id, $order_by, $order_method, $start, $end)
    {
		return Base_Wrapper_Access::list_system_log($type_id, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Base_WrapperInterface::count_list_system_log()
     * @param integer $type_id
     * @return integer
     */
	public static function count_list_system_log($type_id)
	{
		return Base_Wrapper_Access::count_list_system_log($type_id);
	}

	/**
	 * @see Base_WrapperInterface::list_base_module_navigation()
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_base_module_navigation($start, $end)
	{
		return Base_Wrapper_Access::list_base_module_navigation($start, $end);
	}
	
	/**
	 * @see Base_WrapperInterface::count_base_module_navigation()
	 * @return integer
	 */
	public static function count_base_module_navigation()
	{
		return Base_Wrapper_Access::count_base_module_navigation();
	}

	/**
	 * @see Base_WrapperInterface::list_base_module()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_base_module($order_by, $order_method, $start, $end)
	{
		return Base_Wrapper_Access::list_base_module($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Base_WrapperInterface::count_base_module()
	 * @return integer
	 */
	public static function count_base_module()
	{
		return Base_Wrapper_Access::count_base_module();
	}
	
	/**
	 * @see Base_WrapperInterface::list_base_include()
	 * @todo implementation
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_base_include($order_by, $order_method, $start, $end)
	{
		return Base_Wrapper_Access::list_base_include($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Base_WrapperInterface::count_base_include()
	 * @todo implementation
	 * @return integer
	 */
	public static function count_base_include()
	{
		return Base_Wrapper_Access::count_base_include();
	}
	
	/**
	 * @see Base_WrapperInterface::list_languages()
	 * @todo implementation
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_languages($order_by, $order_method, $start, $end)
	{
		return null;
	}
	
	/**
	 * @see Base_WrapperInterface::count_languages()
	 * @todo implementation
	 * @return integer
	 */
	public static function count_languages()
	{
		return null;
	}
	
	/**
	 * @see Base_WrapperInterface::list_timezones()
	 * @todo implementation
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_timezones($order_by, $order_method, $start, $end)
	{
		return null;
	}
	
	/**
	 * @see Base_WrapperInterface::count_timezones()
	 * @todo implementation
	 * @return integer
	 */
	public static function count_timezones()
	{
		return null;
	}
	
	/**
	 * @see Base_WrapperInterface::list_paper_sizes()
	 * @param string $order_by
	 * @param string $order_by
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_paper_sizes($order_by, $order_method, $start, $end)
	{
		return Base_Wrapper_Access::list_paper_sizes($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Base_WrapperInterface::count_paper_sizes()
	 * @return integer
	 */
	public static function count_paper_sizes()
	{
		return Base_Wrapper_Access::count_paper_sizes();
	}
	
	/**
	 * @see Base_WrapperInterface::list_measuring_units()
	 * @todo implementation
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_measuring_units($order_by, $order_method, $start, $end)
	{
		return null;
	}
	
	/**
	 * @see Base_WrapperInterface::count_measuring_units()
	 * @todo implementation
	 * @return integer
	 */
	public static function count_measuring_units()
	{
		return null;
	}
	
	/**
	 * @see Base_WrapperInterface::list_currencies()
	 * @todo implementation
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_currencies($order_by, $order_method, $start, $end)
	{
		return null;
	}
	
	/**
	 * @see Base_WrapperInterface::count_currencies()
	 * @todo implementation
	 * @return integer
	 */
	public static function count_currencies()
	{
		return null;
	}
}
?>