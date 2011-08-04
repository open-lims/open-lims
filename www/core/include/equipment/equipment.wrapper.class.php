<?php
/**
 * @package equipment
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
require_once("interfaces/equipment.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/equipment.wrapper.access.php");
}

/**
 * Equipment Wrapper Class for complex equipment-joins
 * @package equipment
 */
class Equipment_Wrapper implements Equipment_WrapperInterface
{
	/**
	 * Returns a set of equipment in relations of a SQL-String
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_item_equipments($item_sql, $order_by, $order_method, $start, $end)
	{
		return Equipment_Wrapper_Access::list_item_equipments($item_sql, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * Returns the number of equipments in relations of a SQL-String
	 * @param string $item_id
	 * @return integer
	 */
	public static function count_item_equipments($item_sql)
	{
		return Equipment_Wrapper_Access::count_item_equipments($item_sql);
	}
	
	public static function list_organisation_unit_equipments($organisation_unit_id, $order_by, $order_equipment, $start, $end)
	{
		return Equipment_Wrapper_Access::list_organisation_unit_equipments($organisation_unit_id, $order_by, $order_equipment, $start, $end);
	}
	
	public static function count_organisation_unit_equipments($organisation_unit_id)
	{
		return Equipment_Wrapper_Access::count_organisation_unit_equipments($organisation_unit_id);
	}
}
?>