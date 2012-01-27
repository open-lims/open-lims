<?php
/**
 * @package organisation_unit
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
 * Organisation Unit Wrapper Access Class
 * @package organisation_unit
 */
class OrganisationUnit_Wrapper_Access
{
	/**
	 * @param integer $organisation_unit_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_organisation_unit_members($organisation_unit_id, $order_by, $order_method, $start, $end)
    {
    	global $db;

    	if (is_numeric($organisation_unit_id))
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
					case "username":
						$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
					break;
					
					case "fullname":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
					break;
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY id";
			}
			
			$sql = "SELECT ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id AS id " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." " .			
						"LEFT JOIN ".constant("USER_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id = ".constant("USER_TABLE").".id " .
						"LEFT JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id = ".constant("USER_PROFILE_TABLE").".id " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".organisation_unit_id = ".$organisation_unit_id." ".
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
	public static function count_organisation_unit_members($organisation_unit_id)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $db;
						
			$sql = "SELECT COUNT(".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id) AS result " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".organisation_unit_id = ".$organisation_unit_id."";
			
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
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_organisation_unit_leaders($organisation_unit_id, $order_by, $order_method, $start, $end)
    {
    	global $db;

    	if (is_numeric($organisation_unit_id))
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
					case "username":
						$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
					break;
					
					case "fullname":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
					break;
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY id";
			}
			
			$sql = "SELECT ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".leader_id AS id " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE")." " .			
						"LEFT JOIN ".constant("USER_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".leader_id = ".constant("USER_TABLE").".id " .
						"LEFT JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".leader_id = ".constant("USER_PROFILE_TABLE").".id " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".organisation_unit_id = ".$organisation_unit_id." ".
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
	public static function count_organisation_unit_leaders($organisation_unit_id)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $db;
						
			$sql = "SELECT COUNT(".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".leader_id) AS result " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE")." " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".organisation_unit_id = ".$organisation_unit_id."";
			
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
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_organisation_unit_owners($organisation_unit_id, $order_by, $order_method, $start, $end)
    {
    	global $db;

    	if (is_numeric($organisation_unit_id))
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
					case "username":
						$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
					break;
					
					case "fullname":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
					break;
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY id";
			}
			
			$sql = "SELECT ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".owner_id AS id " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." " .			
						"LEFT JOIN ".constant("USER_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".owner_id = ".constant("USER_TABLE").".id " .
						"LEFT JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".owner_id = ".constant("USER_PROFILE_TABLE").".id " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".organisation_unit_id = ".$organisation_unit_id." ".
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
	public static function count_organisation_unit_owners($organisation_unit_id)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $db;
						
			$sql = "SELECT COUNT(".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".owner_id) AS result " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".organisation_unit_id = ".$organisation_unit_id."";
			
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
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_organisation_unit_quality_managers($organisation_unit_id, $order_by, $order_method, $start, $end)
    {
    	global $db;

    	if (is_numeric($organisation_unit_id))
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
					case "username":
						$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
					break;
					
					case "fullname":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY ".constant("USER_TABLE").".username ".$sql_order_method;
					break;
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY id";
			}
			
			$sql = "SELECT ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".quality_manager_id AS id " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." " .			
						"LEFT JOIN ".constant("USER_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".quality_manager_id = ".constant("USER_TABLE").".id " .
						"LEFT JOIN ".constant("USER_PROFILE_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".quality_manager_id = ".constant("USER_PROFILE_TABLE").".id " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".organisation_unit_id = ".$organisation_unit_id." ".
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
	public static function count_organisation_unit_quality_managers($organisation_unit_id)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $db;
						
			$sql = "SELECT COUNT(".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".quality_manager_id) AS result " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".organisation_unit_id = ".$organisation_unit_id."";
			
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
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_organisation_unit_groups($organisation_unit_id, $order_by, $order_method, $start, $end)
    {
    	global $db;

    	if (is_numeric($organisation_unit_id))
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
					case "groupname":
						$sql_order_by = "ORDER BY ".constant("GROUP_TABLE").".name ".$sql_order_method;
					break;
									
					default:
						$sql_order_by = "ORDER BY ".constant("GROUP_TABLE").".name ".$sql_order_method;
					break;
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY id";
			}
			
			$sql = "SELECT ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id AS id " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." " .			
						"LEFT JOIN ".constant("GROUP_TABLE")." ON ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id = ".constant("GROUP_TABLE").".id " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id = ".$organisation_unit_id." ".
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
	public static function count_organisation_unit_groups($organisation_unit_id)
	{
		if (is_numeric($organisation_unit_id))
		{
			global $db;
						
			$sql = "SELECT COUNT(".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id) AS result " .
						"FROM ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id = ".$organisation_unit_id."";
			
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
	 * @param integer $user_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_organisation_units_by_user_id($user_id, $order_by, $order_method, $start, $end)
    {
    	global $db;

    	if (is_numeric($user_id))
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
						$sql_order_by = "ORDER BY ".constant("ORGANISATION_UNIT_TABLE").".name ".$sql_order_method;
					break;
									
					default:
						$sql_order_by = "ORDER BY ".constant("ORGANISATION_UNIT_TABLE").".name ".$sql_order_method;
					break;
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY id";
			}
			
			$sql = "SELECT DISTINCT ".constant("ORGANISATION_UNIT_TABLE").".id AS id, " .
						"".constant("ORGANISATION_UNIT_TABLE").".name AS name, " .
						"".constant("ORGANISATION_UNIT_TYPE_TABLE").".icon AS symbol, " .
						"".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".organisation_unit_id AS is_member, " .
						"".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".organisation_unit_id AS is_owner, " .
						"".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".organisation_unit_id AS is_leader, " .
						"".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".organisation_unit_id AS is_quality_manager " .
						"FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 			ON (".constant("ORGANISATION_UNIT_TABLE").".id = ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".organisation_unit_id AND ".$user_id." = ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id) " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." 			ON (".constant("ORGANISATION_UNIT_TABLE").".id = ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".organisation_unit_id AND ".$user_id." = ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".owner_id) " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE")." 			ON (".constant("ORGANISATION_UNIT_TABLE").".id = ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".organisation_unit_id AND ".$user_id." = ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".leader_id) " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." 	ON (".constant("ORGANISATION_UNIT_TABLE").".id = ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".organisation_unit_id AND ".$user_id." = ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".quality_manager_id) " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_TYPE_TABLE")."					ON ".constant("ORGANISATION_UNIT_TABLE").".type_id = ".constant("ORGANISATION_UNIT_TYPE_TABLE").".id " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id = ".$user_id." OR " .
						"".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".owner_id = ".$user_id." OR " .
						"".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".leader_id = ".$user_id." OR " .
						"".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".quality_manager_id = ".$user_id." ".
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
     * @param integer $user_id
     * @return integer
	 */
	public static function count_organisation_units_by_user_id($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$sql = "SELECT COUNT(DISTINCT ".constant("ORGANISATION_UNIT_TABLE").".id) AS result " .
						"FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 			ON ".constant("ORGANISATION_UNIT_TABLE").".id = ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".organisation_unit_id " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." 			ON ".constant("ORGANISATION_UNIT_TABLE").".id = ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".organisation_unit_id " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE")." 			ON ".constant("ORGANISATION_UNIT_TABLE").".id = ".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".organisation_unit_id " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." 	ON ".constant("ORGANISATION_UNIT_TABLE").".id = ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".organisation_unit_id " .
						"WHERE ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id = ".$user_id." OR " .
						"".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE").".owner_id = ".$user_id." OR " .
						"".constant("ORGANISATION_UNIT_HAS_LEADER_TABLE").".leader_id = ".$user_id." OR " .
						"".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE").".quality_manager_id = ".$user_id."";
			
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