<?php
/**
 * @package base
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
 * User Join Access Class
 * @package base
 */
class User_Wrapper_Access
{
	/**
	 * @param string $username
	 * @return array
	 */
	public static function list_search_users($username, $order_by, $order_method, $start, $end)
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
									
				case "username":
					$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
				break;
				
				case "fullname":
					$sql_order_by = "ORDER BY fullname ".$sql_order_method;
				break;
				
				default:
					$sql_order_by = "ORDER BY ".constant("USER_TABLE").".id ".$sql_order_method;
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY ".constant("USER_TABLE").".id";
		}
			
		if ($username)
   		{	
			$username = strtolower(trim($username));
   			$username = str_replace("*","%",$username);
   			
   			$return_array = array();
   				
   			$sql = "SELECT ".constant("USER_TABLE").".id AS id, " .
   					"".constant("USER_TABLE").".username AS username, " .
   					"nameconcat(".constant("USER_PROFILE_TABLE").".forename, ".constant("USER_PROFILE_TABLE").".surname) AS fullname ".
   					"FROM ".constant("USER_TABLE")." " .
   					"JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("USER_TABLE").".id = ".constant("USER_PROFILE_TABLE").".id " .
   					"WHERE LOWER(username) LIKE '".$username."' OR " .
   							"LOWER(forename) LIKE '".$username."' OR " .
   							"LOWER(surname) LIKE '".$username."'" .
   					"".$sql_order_by."";  
   		}
   		else
   		{
   			$sql = "SELECT ".constant("USER_TABLE").".id AS id, " .
   				"".constant("USER_TABLE").".username AS username, " .
   				"nameconcat(".constant("USER_PROFILE_TABLE").".forename, ".constant("USER_PROFILE_TABLE").".surname) AS fullname ".
   				"FROM ".constant("USER_TABLE")." " .
   				"JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("USER_TABLE").".id = ".constant("USER_PROFILE_TABLE").".id " .
   				"".$sql_order_by."";
   		}
   						
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
   	 * @param string $username
   	 * @return integer
   	 */
	public static function count_search_users($username)
	{
		global $db;
   		
   		if ($username)
   		{
			$username = strtolower(trim($username));
   			$username = str_replace("*","%",$username);
   			
   			$sql = "SELECT COUNT(".constant("USER_TABLE").".id) AS result " .
   					"FROM ".constant("USER_TABLE")." " .
   					"JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("USER_TABLE").".id = ".constant("USER_PROFILE_TABLE").".id " .
   					"WHERE LOWER(username) LIKE '".$username."' OR " .
   							"LOWER(forename) LIKE '".$username."' OR " .
   							"LOWER(surname) LIKE '".$username."'";  
   		}
   		else
   		{
   			$sql = "SELECT COUNT(".constant("USER_TABLE").".id) AS result " .
   				"FROM ".constant("USER_TABLE").""; 
   		}
   						
   		$res = $db->db_query($sql);
   		$data = $db->db_fetch_assoc($res);
	
		return $data['result'];
   	}
   	
	/**
	 * @param string $groupname
	 * @return array
	 */
	public static function list_search_groups($groupname, $order_by, $order_method, $start, $end)
	{
   		global $db;
   		
   		if ($groupname)
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
						$sql_order_by = "ORDER BY ".constant("GROUP_TABLE").".name ".$sql_order_method;
					break;
					
					case "users":
						$sql_order_by = "ORDER BY users ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("GROUP_TABLE").".id ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("GROUP_TABLE").".id";
			}
			
			$groupname = strtolower(trim($groupname));
   			$groupname = str_replace("*","%",$groupname);
   			
   			$return_array = array();
   				
   			$sql = "SELECT ".constant("GROUP_TABLE").".id AS id, " .
   					"".constant("GROUP_TABLE").".name AS name, " .
   					"COUNT(".constant("GROUP_HAS_USER_TABLE").".group_id) AS users ".
   					"FROM ".constant("GROUP_TABLE")." " .
   					"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." ON ".constant("GROUP_TABLE").".id = ".constant("GROUP_HAS_USER_TABLE").".group_id " .
   					"WHERE LOWER(name) LIKE '".$groupname."'" .
   					"GROUP BY ".constant("GROUP_TABLE").".name, ".constant("GROUP_TABLE").".id " .
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
   	 * @param string $groupname
   	 * @return integer
   	 */
	public static function count_search_groups($groupname)
	{
		global $db;
   		
   		if ($groupname)
   		{
			$groupname = strtolower(trim($groupname));
   			$groupname = str_replace("*","%",$groupname);
   				
   			$sql = "SELECT COUNT(".constant("GROUP_TABLE").".id) AS result " .
   					"FROM ".constant("GROUP_TABLE")." " .
   					"WHERE LOWER(name) LIKE '".$groupname."'";
   						
   			$res = $db->db_query($sql);
   			$data = $db->db_fetch_assoc($res);
	
			return $data['result'];
   		}
   		else
   		{
   			return null;
   		}
   	}
	
   	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
   	public static function list_users($order_by, $order_method, $start, $end)
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
									
				case "username":
					$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
				break;
				
				case "fullname":
					$sql_order_by = "ORDER BY fullname ".$sql_order_method;
				break;
				
				default:
					$sql_order_by = "ORDER BY ".constant("USER_TABLE").".id ".$sql_order_method;
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY ".constant("USER_TABLE").".id";
		}
   			
   		$return_array = array();
   				
   		$sql = "SELECT ".constant("USER_TABLE").".id AS id, " .
   				"".constant("USER_TABLE").".username AS username, " .
   				"nameconcat(".constant("USER_PROFILE_TABLE").".forename, ".constant("USER_PROFILE_TABLE").".surname) AS fullname ".
   				"FROM ".constant("USER_TABLE")." " .
   				"JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("USER_TABLE").".id = ".constant("USER_PROFILE_TABLE").".id " .
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
   	public static function count_users()
   	{
   		global $db;
   				
   		$sql = "SELECT COUNT(".constant("USER_TABLE").".id) AS result " .
   				"FROM ".constant("USER_TABLE")."";  
   						
   		$res = $db->db_query($sql);
   		$data = $db->db_fetch_assoc($res);
	
		return $data['result'];
   	}
   	
   	/**
   	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
   	public static function list_groups($order_by, $order_method, $start, $end)
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
					$sql_order_by = "ORDER BY ".constant("GROUP_TABLE").".name ".$sql_order_method;
				break;
				
				default:
					$sql_order_by = "ORDER BY ".constant("GROUP_TABLE").".id ".$sql_order_method;
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY ".constant("GROUP_TABLE").".id";
		}
   			
   		$return_array = array();
   				
   		$sql = "SELECT ".constant("GROUP_TABLE").".id AS id, " .
   				"".constant("GROUP_TABLE").".name AS name " .
   				"FROM ".constant("GROUP_TABLE")." " .
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
   	public static function count_groups()
   	{
   		global $db;
   				
   		$sql = "SELECT COUNT(".constant("GROUP_TABLE").".id) AS result " .
   				"FROM ".constant("GROUP_TABLE")."";  
   						
   		$res = $db->db_query($sql);
   		$data = $db->db_fetch_assoc($res);
	
		return $data['result'];
   	}
   		
	/**
	 * @return integer
	 */
	public static function count_administrators()
	{
		global $db;
											
		$sql = "SELECT COUNT(".constant("USER_TABLE").".id) AS result FROM ".constant("USER_TABLE")." " .
				"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." ON ".constant("USER_TABLE").".id = ".constant("GROUP_HAS_USER_TABLE").".user_id " .
				"WHERE ".constant("GROUP_HAS_USER_TABLE").".group_id = 1";
				
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data['result'])
		{
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
}

?>