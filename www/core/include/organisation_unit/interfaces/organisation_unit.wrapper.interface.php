<?php
/**
 * @package organisation_unit
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
 * Organisation Unit Wrapper Interface
 * @package organisation_unit
 */ 
interface OrganisationUnit_WrapperInterface
{
	/**
	 * @param integer $organisation_unit_id;
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_organisation_unit_members($organisation_unit_id, $order_by, $order_method, $start, $end);
    
    /**
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_members($organisation_unit_id);
	
	/**
	 * @param integer $organisation_unit_id;
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_organisation_unit_leaders($organisation_unit_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_leaders($organisation_unit_id);
	
	/**
	 * @param integer $organisation_unit_id;
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_organisation_unit_owners($organisation_unit_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_owners($organisation_unit_id);
	
	/**
	 * @param integer $organisation_unit_id;
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_organisation_unit_quality_managers($organisation_unit_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_quality_managers($organisation_unit_id);
	
	/**
	 * @param integer $organisation_unit_id;
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_organisation_unit_groups($organisation_unit_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
    public static function count_organisation_unit_groups($organisation_unit_id);
}
?>
