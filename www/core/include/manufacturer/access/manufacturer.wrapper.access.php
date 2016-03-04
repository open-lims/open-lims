<?php
/**
 * @package manufacturer
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
 * Manufacturer Wrapper Access Class
 * @package manufacturer
 */
class Manufacturer_Wrapper_Access
{
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */	
	public static function list_manufacturers($order_by, $order_method, $start, $end)
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
			
				case "name":
					$sql_order_by = "ORDER BY name ".$sql_order_method;
				break;
			
				case "user":
					$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
				break;
			
				default:
					$sql_order_by = "ORDER BY id ".$sql_order_method;
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY id";
		}
			
		$sql = "SELECT ".constant("MANUFACTURER_TABLE").".id AS id, " .
					"".constant("MANUFACTURER_TABLE").".name AS name," .
					"".constant("MANUFACTURER_TABLE").".user_id AS user_id " .
					"FROM ".constant("MANUFACTURER_TABLE")." " .
					"LEFT JOIN ".constant("USER_PROFILE_TABLE")." 	ON ".constant("MANUFACTURER_TABLE").".user_id 		= ".constant("USER_PROFILE_TABLE").".id " .
					"".$sql_order_by."";
		
		$return_array = array();
		
		$res = $db->db_query($sql);
		
		if (is_numeric($start) and is_numeric($end))
		{
			for ($i = 0; $i<=$end-1; $i++)
			{
				if (($data = $db->fetch($res)) == null)
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
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		}
		return $return_array;
	}
	
	/**
	 * @return integer
	 */
	public static function count_manufacturers()
	{
		global $db;
						
		$sql = "SELECT COUNT(".constant("MANUFACTURER_TABLE").".id) AS result " .
					"FROM ".constant("MANUFACTURER_TABLE")."";
		
		$res = $db->db_query($sql);
		$data = $db->fetch($res);

		return $data['result'];
	}
}
?>