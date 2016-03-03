<?php
/**
 * @package base
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
 * 
 */
require_once("interfaces/environment.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/environment.wrapper.access.php");
}

/**
 * Base Environment Wrapper Class
 * @package base
 */
class Environment_Wrapper implements Environment_WrapperInterface
{
	/**
	 * @see SystemFE_WrapperInterface::list_languages()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_languages($order_by, $order_method, $start, $end)
	{
		return Environment_Wrapper_Access::list_languages($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see SystemFE_WrapperInterface::count_languages()
	 * @return integer
	 */
	public static function count_languages()
	{
		return Environment_Wrapper_Access::count_languages();
	}
	
	/**
	 * @see SystemFE_WrapperInterface::list_timezones()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_timezones($order_by, $order_method, $start, $end)
	{
		return Environment_Wrapper_Access::list_timezones($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see SystemFE_WrapperInterface::count_timezones()
	 * @return integer
	 */
	public static function count_timezones()
	{
		return Environment_Wrapper_Access::count_timezones();
	}
	
	/**
	 * @see SystemFE_WrapperInterface::list_paper_sizes()
	 * @param string $order_by
	 * @param string $order_by
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_paper_sizes($order_by, $order_method, $start, $end)
	{
		return Environment_Wrapper_Access::list_paper_sizes($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see SystemFE_WrapperInterface::count_paper_sizes()
	 * @return integer
	 */
	public static function count_paper_sizes()
	{
		return Environment_Wrapper_Access::count_paper_sizes();
	}
	
	/**
	 * @see SystemFE_WrapperInterface::list_measuring_units()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_measuring_units($order_by, $order_method, $start, $end)
	{
		return Environment_Wrapper_Access::list_measuring_units($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see SystemFE_WrapperInterface::count_measuring_units()
	 * @return integer
	 */
	public static function count_measuring_units()
	{
		return Environment_Wrapper_Access::count_measuring_units();
	}
	
	/**
	 * @see SystemFE_WrapperInterface::list_measuring_unit_ratios()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_measuring_unit_ratios($order_by, $order_method, $start, $end)
	{
		return Environment_Wrapper_Access::list_measuring_unit_ratios($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see SystemFE_WrapperInterface::count_measuring_unit_ratios()
	 * @return integer
	 */
	public static function count_measuring_unit_ratios()
	{
		return Environment_Wrapper_Access::count_measuring_unit_ratios();
	}
	
	/**
	 * @see SystemFE_WrapperInterface::list_measuring_unit_categories()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_measuring_unit_categories($order_by, $order_method, $start, $end)
	{
		return Environment_Wrapper_Access::list_measuring_unit_categories($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see SystemFE_WrapperInterface::count_measuring_unit_categories()
	 * @return integer
	 */
	public static function count_measuring_unit_categories()
	{
		return Environment_Wrapper_Access::count_measuring_unit_categories();
	}
	
	/**
	 * @see SystemFE_WrapperInterface::list_currencies()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_currencies($order_by, $order_method, $start, $end)
	{
		return Environment_Wrapper_Access::list_currencies($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see SystemFE_WrapperInterface::count_currencies()
	 * @return integer
	 */
	public static function count_currencies()
	{
		return Environment_Wrapper_Access::count_currencies();
	}
}