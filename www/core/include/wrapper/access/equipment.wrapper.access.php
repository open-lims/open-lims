<?php
/**
 * @package equipment
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
	public static function list_item_equipments($item_sql, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if ($item_sql)
		{
			if ($order_by and $order_method)
			{
				if ($order_method == "asc")
				{
					$sql_order_method = "ASC";
				}
				else
				{
					$sql_order_method = "DESC";
				}
				
				switch($order_by):

					case "name":
						$sql_order_by = "ORDER BY ".constant("METHOD_TYPE_TABLE").".name ".$sql_order_method;
					break;
					
					case "category":
						$sql_order_by = "ORDER BY ".constant("METHOD_CAT_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("METHOD_TABLE").".datetime ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("METHOD_TABLE").".datetime ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("METHOD_TABLE").".datetime";
			}
				
			$sql = "SELECT ".constant("METHOD_TABLE").".id AS id, " .
							"".constant("METHOD_TYPE_TABLE").".name AS name, " .
							"".constant("METHOD_CAT_TABLE").".name AS category, " .
							"".constant("METHOD_TABLE").".datetime AS datetime " .
							"FROM ".constant("METHOD_TABLE")." " .
							"LEFT JOIN ".constant("METHOD_IS_ITEM_TABLE")." 	ON ".constant("METHOD_TABLE").".id 			= ".constant("METHOD_IS_ITEM_TABLE").".method_id " .
							"LEFT JOIN ".constant("METHOD_TYPE_TABLE")." 		ON ".constant("METHOD_TABLE").".type_id 	= ".constant("METHOD_TYPE_TABLE").".id " .
							"LEFT JOIN ".constant("METHOD_CAT_TABLE")." 		ON ".constant("METHOD_TYPE_TABLE").".cat_id = ".constant("METHOD_CAT_TABLE").".id " .
							"WHERE ".constant("METHOD_IS_ITEM_TABLE").".item_id IN (".$item_sql.") " .
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
	
	public static function count_item_equipments($item_sql)
	{
		global $db;
		
		if ($item_sql)
		{	
			$sql = "SELECT COUNT(".constant("METHOD_IS_ITEM_TABLE").".method_id) AS result " .
							"FROM ".constant("METHOD_IS_ITEM_TABLE")." " .
							"WHERE ".constant("METHOD_IS_ITEM_TABLE").".item_id IN (".$item_sql.")";
			
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