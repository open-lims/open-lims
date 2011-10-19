<?php
/**
 * @package base
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
 * Base System Access Class
 * @package base
 */
class System_Wrapper_Access
{
	/**
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_base_module_navigation($start, $end)
	{
		global $db;
	
		$sql = "SELECT ".constant("BASE_MODULE_NAVIGATION_TABLE").".id, " .
							"".constant("BASE_MODULE_NAVIGATION_TABLE").".display_name AS name, " .
							"".constant("BASE_MODULE_NAVIGATION_TABLE").".colour AS colour, " .
							"".constant("BASE_MODULE_NAVIGATION_TABLE").".hidden AS hidden, " .
							"".constant("BASE_MODULE_NAVIGATION_TABLE").".position AS position, " .
							"".constant("BASE_MODULE_TABLE").".name AS module " .
					 "FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." " .
				"LEFT JOIN ".constant("BASE_MODULE_TABLE")." ON ".constant("BASE_MODULE_NAVIGATION_TABLE").".module_id = ".constant("BASE_MODULE_TABLE").".id " .
					"ORDER BY ".constant("BASE_MODULE_NAVIGATION_TABLE").".position ASC";
		
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
	 * @return integer
	 */
	public static function count_base_module_navigation()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("BASE_MODULE_NAVIGATION_TABLE").".id) AS result " .
					 "FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")."";
			
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);

		return $data[result];
	}

	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_base_module($order_by, $order_method, $start, $end)
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
				
				case "dialogs":
					$sql_order_by = "ORDER BY dialogs ".$sql_order_method;
				break;
				
				case "links":
					$sql_order_by = "ORDER BY links ".$sql_order_method;
				break;
			
				default:
					$sql_order_by = "ORDER BY name ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY name ASC";
		}
		
		$sql = "SELECT ".constant("BASE_MODULE_TABLE").".id, " .
							"".constant("BASE_MODULE_TABLE").".name AS name, " .
							"".constant("BASE_MODULE_TABLE").".disabled AS disabled, " .
							"COUNT(DISTINCT ".constant("BASE_MODULE_LINK_TABLE").".id) AS links, " .
							"COUNT(DISTINCT ".constant("BASE_MODULE_DIALOG_TABLE").".id) AS dialogs " .
					 "FROM ".constant("BASE_MODULE_TABLE")." " .
					"LEFT JOIN ".constant("BASE_MODULE_LINK_TABLE")." ON ".constant("BASE_MODULE_TABLE").".id = ".constant("BASE_MODULE_LINK_TABLE").".module_id " .
					"LEFT JOIN ".constant("BASE_MODULE_DIALOG_TABLE")." ON ".constant("BASE_MODULE_TABLE").".id = ".constant("BASE_MODULE_DIALOG_TABLE").".module_id " .
					"GROUP BY ".constant("BASE_MODULE_TABLE").".name, ".constant("BASE_MODULE_TABLE").".id, ".constant("BASE_MODULE_TABLE").".disabled " .
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
	 * @return integer
	 */
	public static function count_base_module()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("BASE_MODULE_TABLE").".id) AS result " .
					 "FROM ".constant("BASE_MODULE_TABLE")."";
			
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);

		return $data[result];
	}
	
	/**
	 * @todo implementation
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_base_include($order_by, $order_method, $start, $end)
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
				
				case "folder":
					$sql_order_by = "ORDER BY folder ".$sql_order_method;
				break;
				
				case "eventlisteners":
					$sql_order_by = "ORDER BY eventlisteners ".$sql_order_method;
				break;
			
				default:
					$sql_order_by = "ORDER BY name ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY name ASC";
		}
		
		$sql = "SELECT ".constant("BASE_INCLUDE_TABLE").".id, " .
							"".constant("BASE_INCLUDE_TABLE").".name AS name, " .
							"".constant("BASE_INCLUDE_TABLE").".folder AS folder, " .
							"COUNT(DISTINCT ".constant("BASE_EVENT_LISTENER_TABLE").".id) AS eventlisteners " .
					 "FROM ".constant("BASE_INCLUDE_TABLE")." " .
					"LEFT JOIN ".constant("BASE_EVENT_LISTENER_TABLE")." ON ".constant("BASE_INCLUDE_TABLE").".id = ".constant("BASE_EVENT_LISTENER_TABLE").".include_id " .
					"GROUP BY ".constant("BASE_INCLUDE_TABLE").".name, ".constant("BASE_INCLUDE_TABLE").".id, ".constant("BASE_INCLUDE_TABLE").".folder " .
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
	 * @todo implementation
	 * @return integer
	 */
	public static function count_base_include()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("BASE_INCLUDE_TABLE").".id) AS result " .
					 "FROM ".constant("BASE_INCLUDE_TABLE")."";
			
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);

		return $data[result];
	}
}