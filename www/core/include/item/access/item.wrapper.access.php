<?php
/**
 * @package item
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
 * Item Wrapper Access Class
 * @package item
 */
class Item_Wrapper_Access
{
   	public static function list_fulltext_search($string, $item_select_sql_array, $item_join_sql, $item_where_sql, $order_by, $order_method, $start, $end)
   	{
   		global $db, $user;

   		if (is_array($item_select_sql_array))
   		{
   			if (count($item_select_sql_array) >= 2)
   			{
	   			$name_select_sql 		= "";
	   			$datetime_select_sql 	= "";
	   			$rank_select_sql 	= "";
	   			
	   			$item_select_sql_array_length = count($item_select_sql_array);
	   			
   				for ($i=0;$i<=($item_select_sql_array_length-2);$i++)
   				{
   					$name_select_sql 		.= "CONCAT(".$item_select_sql_array[$i][name].",";
   					$datetime_select_sql 	.= "CONCAT(CAST(".$item_select_sql_array[$i][datetime]." AS TEXT),";
   					$rank_select_sql 	.= "CONCAT(CAST(".$item_select_sql_array[$i][rank]." AS TEXT),";
   				}
   				
   				$name_select_sql 		.= $item_select_sql_array[($item_select_sql_array_length-1)][name];
   				$datetime_select_sql	.= "CAST(".$item_select_sql_array[($item_select_sql_array_length-1)][datetime]." AS TEXT)";
   				$rank_select_sql	.= "CAST(".$item_select_sql_array[($item_select_sql_array_length-1)][rank]." AS TEXT)";
   				
   				for ($i=0;$i<=($item_select_sql_array_length-2);$i++)
   				{
   					$name_select_sql 		.= ")";
   					$datetime_select_sql 	.= ")";
   					$rank_select_sql 	.= ")";
   				}
   				
   				for ($i=0;$i<=($item_select_sql_array_length-1);$i++)
   				{
   					if ($type_select_sql == "")
   					{
   						$type_select_sql .= $item_select_sql_array[$i][type_id];
   					}
   					else
   					{
   						$type_select_sql .= ",".$item_select_sql_array[$i][type_id];
   					}
   					
   				}
   			}
   			elseif (count($item_select_sql_array) == 1)
   			{
   				$name_select_sql = $item_select_sql_array[0][name];
   				$type_select_sql = $item_select_sql_array[0][type_id];
   				$datetime_select_sql = $item_select_sql_array[0][datetime];
   				$rank_select_sql = $item_select_sql_array[0][rank];
   			}
   		}


   		
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
					$sql_order_by = "ORDER BY name ".$sql_order_method;
				break;
				
				case "datetime":
					$sql_order_by = "ORDER BY datetime ".$sql_order_method;
				break;
				
				case "rank":
					$sql_order_by = "ORDER BY rank ".$sql_order_method;
				break;
				
				default:
					$sql_order_by = "ORDER BY rank ".$sql_order_method;
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY rank DESC";
		}
   		
   		$sql = "SELECT ".constant("ITEM_TABLE").".id AS id, " .
   							"".$name_select_sql." AS name, " .
   							"".$type_select_sql.", " .
   							"".$datetime_select_sql." AS datetime, " .
   							"".$rank_select_sql." AS rank " .
							"FROM ".constant("ITEM_TABLE")." " .
   							"".$item_join_sql." " .
   							"WHERE (".$item_where_sql.") " .
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
	
   	public static function count_fulltext_search($string, $item_select_sql_array, $item_join_sql, $item_where_sql)
   	{
   		global $db, $user;
   	
   		$sql = "SELECT COUNT(".constant("ITEM_TABLE").".id) AS result " .
							"FROM ".constant("ITEM_TABLE")." " .
							"".$item_join_sql."" .
   							"WHERE (".$item_where_sql.") " .
   							"".$sql_order_by."";
   		
   		$res = $db->db_query($sql);
   		$data = $db->db_fetch_assoc($res);
	
		return $data[result];
   	}
}

?>