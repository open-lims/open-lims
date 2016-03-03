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
require_once("interfaces/system_fe.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/system_fe.wrapper.access.php");
}

/**
 * Base System Frontend Wrapper Class
 * @package base
 */
class SystemFE_Wrapper implements SystemFE_WrapperInterface
{
    /**
     * @see SystemFE_WrapperInterface::list_system_log()
     * @param integer $type_id
     * @param string $order_by
     * @param string $order_method
     * @param integer $start
     * @param integer $end
     * @return array
     */
    public static function list_system_log($type_id, $order_by, $order_method, $start, $end)
    {
		return SystemFE_Wrapper_Access::list_system_log($type_id, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see SystemFE_WrapperInterface::count_list_system_log()
     * @param integer $type_id
     * @return integer
     */
	public static function count_list_system_log($type_id)
	{
		return SystemFE_Wrapper_Access::count_list_system_log($type_id);
	}
}
?>