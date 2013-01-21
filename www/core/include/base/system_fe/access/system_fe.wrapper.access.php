<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Base System Frontend Access Class
 * @package base
 */
class SystemFE_Wrapper_Access
{
	/**
	 * @param integer $type_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
 	public static function list_system_log($type_id, $order_by, $order_method, $start, $end)
 	{
		global $db;
		
		if (is_numeric($type_id))
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
				
					case "user":
						$sql_order_by = "ORDER BY user ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY datetime ".$sql_order_method;
					break;
					
					case "ip":
						$sql_order_by = "ORDER BY ip ".$sql_order_method;
					break;
					
					case "info":
						$sql_order_by = "ORDER BY info ".$sql_order_method;
					break;
					
					case "file":
						$sql_order_by = "ORDER BY file ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY datetime DESC";
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY datetime DESC";
			}
			
			$sql = "SELECT ".constant("SYSTEM_LOG_TABLE").".id, " .
								"".constant("SYSTEM_LOG_TABLE").".ip AS ip, " .
								"".constant("SYSTEM_LOG_TABLE").".file AS file, " .
								"".constant("SYSTEM_LOG_TABLE").".content_string AS info, " .
								"".constant("SYSTEM_LOG_TABLE").".datetime AS datetime, " .
								"".constant("USER_TABLE").".username AS user " .
						 "FROM ".constant("SYSTEM_LOG_TABLE")." " .
					"LEFT JOIN ".constant("USER_TABLE")." ON ".constant("USER_TABLE").".id = ".constant("SYSTEM_LOG_TABLE").".user_id " .
						"WHERE " .
							"type_id = ".$type_id. " " .
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
	 * @param integer $type_id
	 * @return integer
	 */
	public static function count_list_system_log($type_id)
	{
		global $db;
		
		if (is_numeric($type_id))
		{
			$sql = "SELECT COUNT(".constant("SYSTEM_LOG_TABLE").".id) AS result " .
						 "FROM ".constant("SYSTEM_LOG_TABLE")." " .
						"WHERE " .
							"type_id = ".$type_id;
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);

			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	

}

?>
