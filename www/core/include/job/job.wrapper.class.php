<?php
/**
 * @package job
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
// require_once("interfaces/job.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/job.wrapper.access.php");
}

/**
 * Job Wrapper Class
 * @package job
 */
class Job_Wrapper // implements Job_WrapperInterface
{
	/**
	 * @see Job_WrapperInterface::list_jobs()
	 * @param string $datetime
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_jobs($datetime, $order_by, $order_method, $start, $end)
	{
		return Job_Wrapper_Access::list_jobs($datetime, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Job_WrapperInterface::count_jobs()
	 * @param string $datetime
	 * @return integer
	 */
	public static function count_jobs($datetime)
	{
		return Job_Wrapper_Access::count_jobs($datetime);
	}
}
?>