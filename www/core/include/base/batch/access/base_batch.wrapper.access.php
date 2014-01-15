<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Base Batch Wrapper Access Class
 * @package base
 */
class BaseBatch_Wrapper_Access
{	
	/**
	 * @param string $create_datetime
	 * @param string $end_datetime
	 * @param string $order_by
	 * @param string $order_equipment
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_batches($create_datetime, $end_datetime, $order_by, $order_method, $start, $end)
	{
		global $db;
		
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
				
				case "status":
					$sql_order_by = "ORDER BY status ".$sql_order_method;
				break;
				
				case "created_at":
					$sql_order_by = "ORDER BY create_datetime ".$sql_order_method;
				break;
				
				case "user":
					$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
				break;
				
				default:
					$sql_order_by = "ORDER BY create_datetime ".$sql_order_method;
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY create_datetime";
		}
		
		if ($create_datetime != null and $end_datetime != null)
		{
			$sql_where = "WHERE create_datetime >= '".$create_datetime."' AND end_datetime >= '".$end_datetime."'";
		}
		else
		{
			if ($create_datetime != null)
			{
				$sql_where = "WHERE end_datetime >= '".$create_datetime."'";
			}
			elseif($end_datetime != null)
			{
				$sql_where = "WHERE end_datetime >= '".$end_datetime."'";
			}
			else
			{
				$sql_where = "";
			}
		}
		
		$sql = "SELECT ".constant("BASE_BATCH_RUN_TABLE").".id AS id, " .
						"".constant("BASE_BATCH_TYPE_TABLE").".name AS name, " .
						"".constant("BASE_BATCH_RUN_TABLE").".status AS status, " .
						"".constant("BASE_BATCH_RUN_TABLE").".create_datetime AS created_at, " .
						"".constant("BASE_BATCH_RUN_TABLE").".user_id AS user_id " .
						"FROM ".constant("BASE_BATCH_RUN_TABLE")." " .
						"JOIN ".constant("BASE_BATCH_TYPE_TABLE")." ON ".constant("BASE_BATCH_RUN_TABLE").".type_id = ".constant("BASE_BATCH_TYPE_TABLE").".id " .
						"JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("BASE_BATCH_RUN_TABLE").".user_id = ".constant("USER_PROFILE_TABLE").".id " .
						"".$sql_where." " .
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
	
	/**
	 * @param string $create_datetime
	 * @param string $end_datetime
	 * @return integer
	 */
	public static function count_batches($create_datetime, $end_datetime)
	{
		global $db;
		
		if ($create_datetime != null and $end_datetime != null)
		{
			$sql_where = "WHERE create_datetime >= '".$create_datetime."' AND end_datetime >= '".$end_datetime."'";
		}
		else
		{
			if ($create_datetime != null)
			{
				$sql_where = "WHERE end_datetime >= '".$create_datetime."'";
			}
			elseif($end_datetime != null)
			{
				$sql_where = "WHERE end_datetime >= '".$end_datetime."'";
			}
			else
			{
				$sql_where = "";
			}
		}
		
		$sql = "SELECT COUNT(".constant("BASE_BATCH_RUN_TABLE").".id) AS result " .
						"FROM ".constant("BASE_BATCH_RUN_TABLE")." " .
						"".$sql_where."";
		
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);

		return $data['result'];
	}
}
?>
	