<?php
/**
 * @package equipment
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Equipment Wrapper Access Class
 * @package equipment
 */
class Equipment_Wrapper_Access
{	
	/**
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_equipment
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_item_equipments($item_sql, $order_by, $order_equipment, $start, $end)
	{
		global $db;
		
		if ($item_sql)
		{
			if ($order_by and $order_equipment)
			{
				if ($order_equipment == "asc")
				{
					$sql_order_equipment = "ASC";
				}
				else
				{
					$sql_order_equipment = "DESC";
				}
				
				switch($order_by):

					case "name":
						$sql_order_by = "ORDER BY ".constant("EQUIPMENT_TYPE_TABLE").".name ".$sql_order_equipment;
					break;
					
					case "category":
						$sql_order_by = "ORDER BY ".constant("EQUIPMENT_CAT_TABLE").".name ".$sql_order_equipment;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("EQUIPMENT_TABLE").".datetime ".$sql_order_equipment;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("EQUIPMENT_TABLE").".datetime ".$sql_order_equipment;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("EQUIPMENT_TABLE").".datetime";
			}
				
			$sql = "SELECT ".constant("EQUIPMENT_TABLE").".id AS id, " .
							"".constant("EQUIPMENT_TYPE_TABLE").".name AS name, " .
							"".constant("EQUIPMENT_CAT_TABLE").".name AS category, " .
							"".constant("EQUIPMENT_TABLE").".datetime AS datetime, " .
							"".constant("EQUIPMENT_IS_ITEM_TABLE").".item_id AS item_id " .
							"FROM ".constant("EQUIPMENT_TABLE")." " .
							"LEFT JOIN ".constant("EQUIPMENT_IS_ITEM_TABLE")." 	ON ".constant("EQUIPMENT_TABLE").".id 			= ".constant("EQUIPMENT_IS_ITEM_TABLE").".equipment_id " .
							"LEFT JOIN ".constant("EQUIPMENT_TYPE_TABLE")." 		ON ".constant("EQUIPMENT_TABLE").".type_id 	= ".constant("EQUIPMENT_TYPE_TABLE").".id " .
							"LEFT JOIN ".constant("EQUIPMENT_CAT_TABLE")." 		ON ".constant("EQUIPMENT_TYPE_TABLE").".cat_id = ".constant("EQUIPMENT_CAT_TABLE").".id " .
							"WHERE ".constant("EQUIPMENT_IS_ITEM_TABLE").".item_id IN (".$item_sql.") " .
							"".$sql_order_by."";
			
			$return_array = array();
			
			$res = $db->db_query($sql);
			
			if (is_numeric($start) and is_numeric($end))
			{
				for ($i = 0; $i<=$end-1; $i++)
				{
					if (($data = $db->db_fetch_assoc($res)) == null)
					{
						break;
					}
					
					if ($i >= $start)
					{
						array_push($return_array, $data);
					}
				}
			}
			else
			{
				while ($data = $db->db_fetch_assoc($res))
				{
					array_push($return_array, $data);
				}
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $item_sql
	 * @return integer
	 */
	public static function count_item_equipments($item_sql)
	{
		global $db;
		
		if ($item_sql)
		{	
			$sql = "SELECT COUNT(".constant("EQUIPMENT_IS_ITEM_TABLE").".equipment_id) AS result " .
							"FROM ".constant("EQUIPMENT_IS_ITEM_TABLE")." " .
							"WHERE ".constant("EQUIPMENT_IS_ITEM_TABLE").".item_id IN (".$item_sql.")";
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
	
			return $data[result];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param string $order_by
	 * @param string $order_equipment
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_organisation_unit_equipments($organisation_unit_id, $order_by, $order_equipment, $start, $end)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			if ($order_by and $order_equipment)
			{
				if ($order_equipment == "asc")
				{
					$sql_order_equipment = "ASC";
				}
				else
				{
					$sql_order_equipment = "DESC";
				}
				
				switch($order_by):

					case "name":
						$sql_order_by = "ORDER BY ".constant("EQUIPMENT_TYPE_TABLE").".name ".$sql_order_equipment;
					break;
					
					case "category":
						$sql_order_by = "ORDER BY ".constant("EQUIPMENT_CAT_TABLE").".name ".$sql_order_equipment;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("EQUIPMENT_TYPE_TABLE").".name ".$sql_order_equipment;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("EQUIPMENT_TYPE_TABLE").".name";
			}
				
			$sql = "SELECT ".constant("EQUIPMENT_TYPE_TABLE").".id AS id, " .
							"".constant("EQUIPMENT_TYPE_TABLE").".name AS name, " .
							"".constant("EQUIPMENT_CAT_TABLE").".name AS category, " .
							"".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE").".organisation_unit_id AS organisation_unit_id " .
							"FROM ".constant("EQUIPMENT_TYPE_TABLE")." " .
							"JOIN ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE")." 	ON ".constant("EQUIPMENT_TYPE_TABLE").".id 		= ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE").".equipment_id " .
							"JOIN ".constant("EQUIPMENT_CAT_TABLE")." 						ON ".constant("EQUIPMENT_TYPE_TABLE").".cat_id 	= ".constant("EQUIPMENT_CAT_TABLE").".id " .
							"WHERE ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE").".organisation_unit_id IN (SELECT * FROM get_organisation_unit_childs(".$organisation_unit_id.")) " .
							"".$sql_order_by."";
			
			$return_array = array();
			
			$res = $db->db_query($sql);
			
			if (is_numeric($start) and is_numeric($end))
			{
				for ($i = 0; $i<=$end-1; $i++)
				{
					if (($data = $db->db_fetch_assoc($res)) == null)
					{
						break;
					}
					
					if ($i >= $start)
					{
						array_push($return_array, $data);
					}
				}
			}
			else
			{
				while ($data = $db->db_fetch_assoc($res))
				{
					array_push($return_array, $data);
				}
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_equipments($organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{	
			$sql = "SELECT COUNT(".constant("EQUIPMENT_TYPE_TABLE").".id) AS result " .
							"FROM ".constant("EQUIPMENT_TYPE_TABLE")." " .
							"JOIN ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE")." 	ON ".constant("EQUIPMENT_TYPE_TABLE").".id 		= ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE").".equipment_id " .
							"WHERE ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE").".organisation_unit_id IN (SELECT * FROM get_organisation_unit_childs(".$organisation_unit_id."))";

			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);

			return $data[result];
		}
		else
		{
			return null;
		}
	}
}

?>