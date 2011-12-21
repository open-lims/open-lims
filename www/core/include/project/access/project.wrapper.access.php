<?php
/**
 * @package project
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
 * Project Wrapper Access Class
 * @package project
 */
class Project_Wrapper_Access
{
	/**
	 * @param integer $user_id
	 * @param string $today
	 * @return array
	 */
	public static function list_not_finished_over_time_project_tasks_by_user_id($user_id, $date)
	{
		global $db;
		
		if (is_numeric($user_id) and $date)
		{	
			$sql = "SELECT ".constant("PROJECT_TASK_TABLE").".id FROM " .
					"".constant("PROJECT_TASK_TABLE")." " .
						"JOIN ".constant("PROJECT_TABLE")." 					ON ".constant("PROJECT_TABLE").".id 					= ".constant("PROJECT_TASK_TABLE").".project_id " .
						"LEFT JOIN ".constant("PROJECT_PERMISSION_TABLE")." 	ON ".constant("PROJECT_PERMISSION_TABLE").".project_id	= ".constant("PROJECT_TASK_TABLE").".project_id " .
							"WHERE ".constant("PROJECT_PERMISSION_TABLE").".user_id IS NOT NULL " .
								"AND ".constant("PROJECT_PERMISSION_TABLE").".user_id = ".$user_id." " .
								"AND ".constant("PROJECT_PERMISSION_TABLE").".permission > 1 " .
								"AND ".constant("PROJECT_TASK_TABLE").".end_date < '".$date."' " .
								"AND ".constant("PROJECT_TABLE").".deleted = 'f'";
			
			$return_array = array();
				
			$res = $db->db_query($sql);

			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array, $data[id]);
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}
		
	/**
	 * @param integer $leader_id
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public static function change_leader_permission_by_organisation_unit_id($leader_id, $organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($leader_id) and is_numeric($organisation_unit_id))
		{	
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." " .
					"SET user_id = ".$leader_id." " .
					"WHERE ".constant("PROJECT_PERMISSION_TABLE").".intention = 2 " .
						"AND ".constant("PROJECT_PERMISSION_TABLE").".project_id = " .
							"(SELECT ".constant("PROJECT_TABLE").".id FROM ".constant("PROJECT_TABLE")." WHERE toid_organ_unit = ".$organisation_unit_id.")";

			$res = $db->db_query($sql);
			
			if ($res !== false)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_project_status($order_by, $order_method, $start, $end)
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
				
				case "id":
					$sql_order_by = "ORDER BY id ".$sql_order_method;
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
			
		$sql = "SELECT ".constant("PROJECT_STATUS_TABLE").".id AS id, " .
					"".constant("PROJECT_STATUS_TABLE").".name AS name " .
					 "FROM ".constant("PROJECT_STATUS_TABLE")." " .
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
				
				if ($i >= $start) {
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
	public static function count_list_project_status()
	{
		global $db;
			
		$sql = "SELECT COUNT(".constant("PROJECT_STATUS_TABLE").".id) AS result " .
					 "FROM ".constant("PROJECT_STATUS_TABLE")."";
		
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
	public static function list_project_templates($order_by, $order_method, $start, $end)
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
				case "id":
					$sql_order_by = "ORDER BY id ".$sql_order_method;
				break;
			
				case "name":
					$sql_order_by = "ORDER BY name ".$sql_order_method;
				break;
			
				case "file":
					$sql_order_by = "ORDER BY file ".$sql_order_method;
				break;
				
				case "category":
					$sql_order_by = "ORDER BY category ".$sql_order_method;
				break;
				
				case "parent":
					$sql_order_by = "ORDER BY parent ".$sql_order_method;
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
			
		$sql = "SELECT ".constant("PROJECT_TEMPLATE_TABLE").".id AS id, " .
					"".constant("PROJECT_TEMPLATE_TABLE").".name AS name," .
					"".constant("FILE_VERSION_TABLE").".name AS file, " .
					"".constant("PROJECT_TEMPLATE_CAT_TABLE").".name AS category, " .
					"".constant("PROJECT_TEMPLATE_TABLE").".parent_template AS parent " .
					"FROM ".constant("PROJECT_TEMPLATE_TABLE")." " .
					"LEFT JOIN ".constant("PROJECT_TEMPLATE_CAT_TABLE")." 	ON ".constant("PROJECT_TEMPLATE_TABLE").".cat_id 		= ".constant("PROJECT_TEMPLATE_CAT_TABLE").".id " .
					"LEFT JOIN ".constant("OLDL_TEMPLATE_TABLE")." 			ON ".constant("PROJECT_TEMPLATE_TABLE").".template_id 	= ".constant("OLDL_TEMPLATE_TABLE").".id " .
					"LEFT JOIN ".constant("DATA_ENTITY_TABLE")." 			ON ".constant("OLDL_TEMPLATE_TABLE").".data_entity_id 	= ".constant("DATA_ENTITY_TABLE").".id " .
					"LEFT JOIN ".constant("FILE_TABLE")." 					ON ".constant("DATA_ENTITY_TABLE").".id 				= ".constant("FILE_TABLE").".data_entity_id " .
					"LEFT JOIN ".constant("FILE_VERSION_TABLE")." 			ON ".constant("FILE_TABLE").".id 						= ".constant("FILE_VERSION_TABLE").".toid " .					
					"WHERE ".constant("FILE_VERSION_TABLE").".internal_revision = 1 " .
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
	public static function count_list_project_templates()
	{
		global $db;
						
		$sql = "SELECT COUNT(".constant("PROJECT_TEMPLATE_TABLE").".id) AS result " .
					"FROM ".constant("PROJECT_TEMPLATE_TABLE")."";
		
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
	public static function list_project_template_categories($order_by, $order_method, $start, $end)
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
			
				default:
					$sql_order_by = "ORDER BY name ".$sql_order_method;
				break;
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY id";
		}
		
		$sql = "SELECT ".constant("PROJECT_TEMPLATE_CAT_TABLE").".id AS id, " .
					"".constant("PROJECT_TEMPLATE_CAT_TABLE").".name AS name " .
					"FROM ".constant("PROJECT_TEMPLATE_CAT_TABLE")." " .
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
	public static function count_list_project_template_categories()
	{
		global $db;
						
		$sql = "SELECT COUNT(".constant("PROJECT_TEMPLATE_CAT_TABLE").".id) AS result " .
					"FROM ".constant("PROJECT_TEMPLATE_CAT_TABLE")."";
		
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);

		return $data[result];
	}
	
	/**
	 * @return integer
	 */
	public static function count_user_projects($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$sql = "SELECT COUNT(DISTINCT ".constant("PROJECT_TABLE").".id) AS result " .
					"FROM ".constant("PROJECT_TABLE")." " .
					"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." 				ON ".$user_id." 									= ".constant("GROUP_HAS_USER_TABLE").".user_id " .
					"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
					"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
					"WHERE (owner_id = ".$user_id." OR " .
							"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user_id.")) = TRUE OR " .
							"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL " .
							"AND ".constant("PROJECT_TABLE").".deleted = 'f'";	
			
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
	 * @return integer
	 */
	public static function count_user_running_projects($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$sql = "SELECT COUNT(DISTINCT ".constant("PROJECT_TABLE").".id) AS result " .
					"FROM ".constant("PROJECT_TABLE")." " .
					"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." 				ON ".$user_id." 									= ".constant("GROUP_HAS_USER_TABLE").".user_id " .
					"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
					"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
					"WHERE (owner_id = ".$user_id." OR " .
							"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user_id.")) = TRUE OR " .
							"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL " .
							"AND ".constant("PROJECT_TABLE").".id NOT IN " .
									"(SELECT project_id FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE status_id = 2 OR status_id = 0 ORDER BY datetime DESC) " .
							"AND ".constant("PROJECT_TABLE").".deleted = 'f'";	
			
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
	 * @return integer
	 */
	public static function count_user_finished_projects($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$sql = "SELECT COUNT(DISTINCT ".constant("PROJECT_TABLE").".id) AS result " .
					"FROM ".constant("PROJECT_TABLE")." " .
					"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." 				ON ".$user_id." 									= ".constant("GROUP_HAS_USER_TABLE").".user_id " .
					"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
					"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
					"LEFT JOIN ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." 	ON ".constant("PROJECT_TABLE").".id 				= ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id " .
					"WHERE (owner_id = ".$user_id." OR " .
							"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user_id.")) = TRUE OR " .
							"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL " .
							"AND ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".status_id = 2 " .
							"AND ".constant("PROJECT_TABLE").".deleted = 'f'";	

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
	 * @param bool $admin
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_user_related_projects($user_id, $admin, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			if ($admin == true)
			{
				$admin_sql = "";
			}
			else
			{
				$admin_sql = "AND ".constant("PROJECT_TABLE").".deleted = 'f'";
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
						$sql_order_by = "ORDER BY name ".$sql_order_method. "";
					break;
					
					case "organisation_unit":
						$sql_order_by = "ORDER BY organisation_unit ".$sql_order_method. "";
					break;
				
					case "datetime":
						$sql_order_by = "ORDER BY datetime ".$sql_order_method. "";
					break;
					
					case "template":
						$sql_order_by = "ORDER BY template ".$sql_order_method. "";
					break;
					
					case "status":
						$sql_order_by = "ORDER BY status ".$sql_order_method. "";
					break;
				
					default:
						$sql_order_by = "ORDER BY name ".$sql_order_method. "";
					break;
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY name ASC";
			}
				
			$sql = "SELECT ".constant("PROJECT_TABLE").".id AS id, " .
					"".constant("PROJECT_TABLE").".name AS name, " .
					"".constant("PROJECT_TABLE").".datetime AS datetime," .
					"".constant("ORGANISATION_UNIT_TABLE").".name AS organisation_unit, " .
					"".constant("PROJECT_TEMPLATE_TABLE").".name AS template, " .
					"".constant("PROJECT_STATUS_TABLE").".name AS status, " .
					"".constant("PROJECT_STATUS_TABLE").".id AS status_id, " .
					"".constant("PROJECT_TABLE").".deleted AS deleted " .
					"FROM ".constant("PROJECT_TABLE")." " .
					"JOIN ".constant("ORGANISATION_UNIT_TABLE")." 			ON ".constant("PROJECT_TABLE").".toid_organ_unit 				= ".constant("ORGANISATION_UNIT_TABLE").".id " .
					"JOIN ".constant("PROJECT_TEMPLATE_TABLE")." 			ON ".constant("PROJECT_TABLE").".template_id 					= ".constant("PROJECT_TEMPLATE_TABLE").".id " .
					"JOIN ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." 	ON ".constant("PROJECT_TABLE").".id 							= ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id " .
					"JOIN ".constant("PROJECT_STATUS_TABLE")." 				ON ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".status_id 	= ".constant("PROJECT_STATUS_TABLE").".id " .					
					"WHERE " .
							"".constant("PROJECT_TABLE").".id IN (" .
									"SELECT DISTINCT ".constant("PROJECT_TABLE").".id AS id " .
									"FROM ".constant("PROJECT_TABLE")." " .
									"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." 				ON ".$user_id." 									= ".constant("GROUP_HAS_USER_TABLE").".user_id " .
									"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
									"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
									"WHERE (owner_id = ".$user_id." OR " .
											"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user_id.")) = TRUE OR " .
											"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
											"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
											"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
											"AND toid_organ_unit IS NOT NULL)".
							"AND ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".datetime = " .
									"(SELECT MAX(datetime) FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id = ".constant("PROJECT_TABLE").".id)" .
					"".$admin_sql." ";
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
	 * @param bool $admin
	 * @return integer
	 */
	public static function count_list_user_related_projects($user_id, $admin)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			if ($admin == true)
			{
				$admin_sql = "";
			}
			else
			{
				$admin_sql = "AND ".constant("PROJECT_TABLE").".deleted = 'f'";
			}
			
			$sql = "SELECT COUNT(DISTINCT ".constant("PROJECT_TABLE").".id) AS result " .
					"FROM ".constant("PROJECT_TABLE")." " .
					"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." ON ".$user_id." = ".constant("GROUP_HAS_USER_TABLE").".user_id " .
					"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." ON ".constant("PROJECT_TABLE").".toid_organ_unit = ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
					"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." ON ".constant("PROJECT_TABLE").".toid_organ_unit = ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
					"WHERE (owner_id = ".$user_id." OR " .
							"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user_id.")) = TRUE OR " .
							"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL " .
							"".$admin_sql."";
				
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
	 * @return integer
	 */
	public static function count_organisation_unit_related_projects($organisation_unit_id, $admin)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			if ($admin == true)
			{
				$admin_sql = "";
			}
			else
			{
				$admin_sql = "AND ".constant("PROJECT_TABLE").".deleted = 'f'";
			}
			
			$sql = "SELECT COUNT(DISTINCT ".constant("PROJECT_TABLE").".id) AS result " .
					"FROM ".constant("PROJECT_TABLE")." " .
					"WHERE (toid_organ_unit = ".$organisation_unit_id." OR " .
							"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".$organisation_unit_id.")) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL ".
							"".$admin_sql.""; 
				
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
	public static function list_organisation_unit_related_projects($organisation_unit_id, $admin, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			if ($admin == true)
			{
				$admin_sql = "";
			}
			else
			{
				$admin_sql = "AND ".constant("PROJECT_TABLE").".deleted = 'f'";
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
						$sql_order_by = "ORDER BY name ".$sql_order_method. "";
					break;
					
					case "owner":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method. "";
					break;
				
					case "datetime":
						$sql_order_by = "ORDER BY datetime ".$sql_order_method. "";
					break;
					
					case "template":
						$sql_order_by = "ORDER BY template ".$sql_order_method. "";
					break;
					
					case "status":
						$sql_order_by = "ORDER BY status ".$sql_order_method. "";
					break;
				
					default:
						$sql_order_by = "ORDER BY name ".$sql_order_method. "";
					break;
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY name ASC";
			}
				
			$sql = "SELECT ".constant("PROJECT_TABLE").".id AS id, " .
					"".constant("PROJECT_TABLE").".name AS name, " .
					"".constant("PROJECT_TABLE").".datetime AS datetime," .
					"".constant("PROJECT_TABLE").".owner_id AS owner_id," .
					"".constant("PROJECT_TEMPLATE_TABLE").".name AS template, " .
					"".constant("PROJECT_STATUS_TABLE").".name AS status, " .
					"".constant("PROJECT_STATUS_TABLE").".id AS status_id, " .
					"".constant("PROJECT_TABLE").".deleted AS deleted " .
					"FROM ".constant("PROJECT_TABLE")." " .
					"JOIN ".constant("USER_PROFILE_TABLE")." 				ON ".constant("PROJECT_TABLE").".owner_id						= ".constant("USER_PROFILE_TABLE").".id " .
					"JOIN ".constant("PROJECT_TEMPLATE_TABLE")." 			ON ".constant("PROJECT_TABLE").".template_id 					= ".constant("PROJECT_TEMPLATE_TABLE").".id " .
					"JOIN ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." 	ON ".constant("PROJECT_TABLE").".id 							= ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id " .
					"JOIN ".constant("PROJECT_STATUS_TABLE")." 				ON ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".status_id 	= ".constant("PROJECT_STATUS_TABLE").".id " .					
					"WHERE " .
							"".constant("PROJECT_TABLE").".id IN (" .
									"SELECT DISTINCT ".constant("PROJECT_TABLE").".id AS id " .
									"FROM ".constant("PROJECT_TABLE")." " .
									"WHERE (toid_organ_unit = ".$organisation_unit_id." OR " .
											"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".$organisation_unit_id.")) = TRUE) " .
											"AND toid_organ_unit IS NOT NULL)".
							"AND ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".datetime = " .
									"(SELECT MAX(datetime) FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id = ".constant("PROJECT_TABLE").".id)" .
					"".$admin_sql." " .
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
	 * @param integer $item_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_projects_by_item_id($item_id, $admin, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($item_id))
		{
			if ($admin == true)
			{
				$admin_sql = "";
			}
			else
			{
				$admin_sql = "AND ".constant("PROJECT_TABLE").".deleted = 'f'";
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
						$sql_order_by = "ORDER BY ".constant("PROJECT_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("PROJECT_TABLE").".datetime ".$sql_order_method;
					break;
					
					case "template":
						$sql_order_by = "ORDER BY ".constant("PROJECT_TEMPLATE_TABLE").".name ".$sql_order_method;
					break;
					
					case "status":
						$sql_order_by = "ORDER BY ".constant("PROJECT_STATUS_TABLE").".name ".$sql_order_method;
					break;
					
					case "owner":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("PROJECT_TABLE").".datetime ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("PROJECT_TABLE").".datetime";
			}
				
			$sql = "SELECT ".constant("PROJECT_TABLE").".id AS id, " .
						"".constant("PROJECT_TABLE").".name AS name," .
						"".constant("PROJECT_TABLE").".datetime AS datetime," .
						"".constant("PROJECT_TEMPLATE_TABLE").".name AS template, " .
						"".constant("PROJECT_STATUS_TABLE").".name AS status, " .
						"".constant("PROJECT_TABLE").".owner_id AS owner, " .
						"".constant("PROJECT_TABLE").".deleted AS deleted " .
						"FROM ".constant("PROJECT_TABLE")." " .
						"JOIN ".constant("PROJECT_HAS_ITEM_TABLE")." 				ON ".constant("PROJECT_TABLE").".id 										= ".constant("PROJECT_HAS_ITEM_TABLE").".project_id ".
						"JOIN ".constant("PROJECT_TEMPLATE_TABLE")." 				ON ".constant("PROJECT_TABLE").".template_id 								= ".constant("PROJECT_TEMPLATE_TABLE").".id " .
						"JOIN ".constant("USER_PROFILE_TABLE")." 					ON ".constant("PROJECT_TABLE").".owner_id								 	= ".constant("USER_PROFILE_TABLE").".id " .
						"JOIN ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." 		ON ".constant("PROJECT_TABLE").".id 										= ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id " .
						"JOIN ".constant("PROJECT_STATUS_TABLE")." 					ON ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".status_id 				= ".constant("PROJECT_STATUS_TABLE").".id " .					
						"WHERE ".constant("PROJECT_HAS_ITEM_TABLE").".item_id = ".$item_id." " .
						"AND ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".datetime = " .
									"(SELECT MAX(datetime) FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id = ".constant("PROJECT_TABLE").".id)" .
						"".$admin_sql." " .
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
	 * @param integer $item_id
	 * @return itneger
	 */
	public static function count_projects_by_item_id($item_id, $admin)
	{
		global $db;
		
		if (is_numeric($item_id))
		{
			if ($admin == true)
			{
				$admin_sql = "";
			}
			else
			{
				$admin_sql = "AND ".constant("PROJECT_TABLE").".deleted = 'f'";
			}
			
			$sql = "SELECT COUNT(DISTINCT ".constant("PROJECT_TABLE").".id) AS result " .
						"FROM ".constant("PROJECT_TABLE")." " .
						"JOIN ".constant("PROJECT_HAS_ITEM_TABLE")." ON ".constant("PROJECT_TABLE").".id = ".constant("PROJECT_HAS_ITEM_TABLE").".project_id ".
						"WHERE ".constant("PROJECT_HAS_ITEM_TABLE").".item_id = ".$item_id." " .
						"".$admin_sql."";
						
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
	 * @param integer $project_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_permissions($project_id, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($project_id))
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
						$sql_order_by = "ORDER BY name ".$sql_order_method;
					break;
					
					case "type":
						$sql_order_by = "ORDER BY ".constant("PROJECT_PERMISSION_TABLE").".user_id ".$sql_order_method." , " .
											"".constant("PROJECT_PERMISSION_TABLE").".group_id ".$sql_order_method." , " .
											"".constant("PROJECT_PERMISSION_TABLE").".organisation_unit_id ".$sql_order_method;
					break;
					
					case "fullname":
						$sql_order_by = "ORDER BY fullname ".$sql_order_method;
					break;
					
					case "createdby":
						$sql_order_by = "ORDER BY owner_table.surname ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("PROJECT_PERMISSION_TABLE").".id ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("PROJECT_PERMISSION_TABLE").".id";
			}
				
			$sql = "SELECT ".constant("PROJECT_PERMISSION_TABLE").".id AS id, " .
						"CONCAT( " .
								"CONCAT ( " .
										"".constant("USER_TABLE").".username ," .
										"".constant("GROUP_TABLE").".name" .
										"), " .
								"".constant("ORGANISATION_UNIT_TABLE").".name" .
								") AS name, " .
						"CONCAT( " .
								"CONCAT ( " .
										"user_table.surname ," .
										"".constant("GROUP_TABLE").".name" .
										"), " .
								"".constant("ORGANISATION_UNIT_TABLE").".name" .
								") AS fullname " .
						"FROM ".constant("PROJECT_PERMISSION_TABLE")." " .
						"LEFT JOIN ".constant("USER_TABLE")." 							ON ".constant("PROJECT_PERMISSION_TABLE").".user_id 				= ".constant("USER_TABLE").".id ".
						"LEFT JOIN ".constant("GROUP_TABLE")." 							ON ".constant("PROJECT_PERMISSION_TABLE").".group_id 				= ".constant("GROUP_TABLE").".id " .
						"LEFT JOIN ".constant("ORGANISATION_UNIT_TABLE")." 				ON ".constant("PROJECT_PERMISSION_TABLE").".organisation_unit_id	= ".constant("ORGANISATION_UNIT_TABLE").".id " .
						"LEFT JOIN ".constant("USER_PROFILE_TABLE")." AS user_table 	ON ".constant("PROJECT_PERMISSION_TABLE").".user_id					= user_table.id " .
						"LEFT JOIN ".constant("USER_PROFILE_TABLE")." AS owner_table 	ON ".constant("PROJECT_PERMISSION_TABLE").".owner_id				= owner_table.id " .
						"WHERE ".constant("PROJECT_PERMISSION_TABLE").".project_id = ".$project_id." " .
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
	 * @param integer $project_id
	 * @return integer
	 */
	public static function count_project_permissions($project_id)
	{
		global $db;
		
		if (is_numeric($project_id))
		{
			$sql = "SELECT COUNT(DISTINCT ".constant("PROJECT_PERMISSION_TABLE").".id) AS result " .
						"FROM ".constant("PROJECT_PERMISSION_TABLE")." " .
						"WHERE ".constant("PROJECT_PERMISSION_TABLE").".project_id = ".$project_id." ";
						
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
	 * @param integer $project_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_tasks($project_id, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($project_id))
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
						$sql_order_by = "ORDER BY name ".$sql_order_method;
					break;
					
					case "type":
						$sql_order_by = "ORDER BY ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE").".task_id ".$sql_order_method.", ". 
													"".constant("PROJECT_TASK_PROCESS_TABLE").".task_id ".$sql_order_method."," .
													"".constant("PROJECT_TASK_MILESTONE_TABLE").".task_id ".$sql_order_method;
					break;
					
					case "start_date":
						$sql_order_by = "ORDER BY start_date ".$sql_order_method;
					break;
					
					case "end_date":
						$sql_order_by = "ORDER BY end_date ".$sql_order_method;
					break;
					
					case "end_time":
						$sql_order_by = "ORDER BY end_time ".$sql_order_method.", whole_day";
					break;
					
					default:
						$sql_order_by = "ORDER BY start_date ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY start_date ".$sql_order_method;
			}
				
			$sql = "SELECT ".constant("PROJECT_TASK_TABLE").".id AS id, " .
						"CONCAT(" .
							"CONCAT(" .
								"".constant("PROJECT_TASK_PROCESS_TABLE").".name, " .
								"".constant("PROJECT_TASK_MILESTONE_TABLE").".name " .
							")," .
							"".constant("PROJECT_STATUS_TABLE").".name" .
						") AS name, " .
						"".constant("PROJECT_TASK_TABLE").".start_date AS start_date, " .
						"".constant("PROJECT_TASK_TABLE").".end_date AS end_date, " .
						"".constant("PROJECT_TASK_TABLE").".end_time AS end_time, " .
						"".constant("PROJECT_TASK_TABLE").".whole_day AS whole_day, " .
						"".constant("PROJECT_TASK_STATUS_PROCESS_TABLE").".task_id AS is_status_process, " .
						"".constant("PROJECT_TASK_PROCESS_TABLE").".task_id AS is_process, " .
						"".constant("PROJECT_TASK_MILESTONE_TABLE").".task_id AS is_milestone " .
 						"FROM ".constant("PROJECT_TASK_TABLE")." " .
						"LEFT JOIN ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE")." 	ON ".constant("PROJECT_TASK_TABLE").".id 							= ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE").".task_id ".
						"LEFT JOIN ".constant("PROJECT_STATUS_TABLE")." 				ON ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE").".end_status_id 	= ".constant("PROJECT_STATUS_TABLE").".id ".
						"LEFT JOIN ".constant("PROJECT_TASK_PROCESS_TABLE")." 			ON ".constant("PROJECT_TASK_TABLE").".id 							= ".constant("PROJECT_TASK_PROCESS_TABLE").".task_id ".
						"LEFT JOIN ".constant("PROJECT_TASK_MILESTONE_TABLE")." 		ON ".constant("PROJECT_TASK_TABLE").".id 							= ".constant("PROJECT_TASK_MILESTONE_TABLE").".task_id ".
						"WHERE ".constant("PROJECT_TASK_TABLE").".project_id = ".$project_id." " .
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
	 * @param integer $project_id
	 * @return integer
	 */
	public static function count_project_tasks($project_id)
	{
		global $db;
		
		if (is_numeric($project_id))
		{
			$sql = "SELECT COUNT(".constant("PROJECT_TASK_TABLE").".id) AS result " .
						"FROM ".constant("PROJECT_TASK_TABLE")." " .
						"WHERE ".constant("PROJECT_TASK_TABLE").".project_id = ".$project_id." ";
						
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
   	 * @param string $name
   	 * @param array $template_id
   	 * @param array $organisation_unit_array
   	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
   	 * @return array
   	 */
   	public static function list_search_projects_without_subprojects($name, $template_array, $organisation_unit_array, $order_by, $order_method, $start, $end)
   	{
   		global $db, $user;
   		
   		if (($name or (is_array($template_array) and count($template_array) >= 1)) and 
   			(is_array($organisation_unit_array) and count($organisation_unit_array) >= 1))
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
						$sql_order_by = "ORDER BY ".constant("PROJECT_TABLE").".name ".$sql_order_method;
					break;
					
					case "organisation_unit":
						$sql_order_by = "ORDER BY ".constant("ORGANISATION_UNIT_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("PROJECT_TABLE").".datetime ".$sql_order_method;
					break;
					
					case "template":
						$sql_order_by = "ORDER BY ".constant("PROJECT_TEMPLATE_TABLE").".name ".$sql_order_method;
					break;
					
					case "status":
						$sql_order_by = "ORDER BY ".constant("PROJECT_STATUS_TABLE").".name ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("PROJECT_TABLE").".id ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("PROJECT_TABLE").".id";
			}

   			$base_sql = $sql = "SELECT ".constant("PROJECT_TABLE").".id AS id, " .
								"".constant("PROJECT_TABLE").".name AS name, " .
								"".constant("PROJECT_TABLE").".datetime AS datetime," .
								"".constant("ORGANISATION_UNIT_TABLE").".name AS organisation_unit, " .
								"".constant("PROJECT_TEMPLATE_TABLE").".name AS template, " .
								"".constant("PROJECT_STATUS_TABLE").".name AS status, " .
								"".constant("PROJECT_STATUS_TABLE").".id AS status_id " .
								"FROM ".constant("PROJECT_TABLE")." " .
								"JOIN ".constant("ORGANISATION_UNIT_TABLE")." 			ON ".constant("PROJECT_TABLE").".toid_organ_unit 				= ".constant("ORGANISATION_UNIT_TABLE").".id " .
								"JOIN ".constant("PROJECT_TEMPLATE_TABLE")." 			ON ".constant("PROJECT_TABLE").".template_id 					= ".constant("PROJECT_TEMPLATE_TABLE").".id " .
								"JOIN ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." 	ON ".constant("PROJECT_TABLE").".id 							= ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id " .
								"JOIN ".constant("PROJECT_STATUS_TABLE")." 				ON ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".status_id 	= ".constant("PROJECT_STATUS_TABLE").".id " .
   								"WHERE (";					
			   			
   			if ($name)
   			{
   				$name = strtolower(trim($name));
   				$name = str_replace("*","%",$name);
   				$add_sql = " LOWER(".constant("PROJECT_TABLE").".name) LIKE '".$name."'";
   			}
   			else
   			{
   				$add_sql = "";
   			}	
   			
   			if (is_array($template_array) and count($template_array) >= 1)
   			{
   				if ($add_sql)
   				{
					$add_sql .= " AND (";
				}
				else
				{
					$add_sql .= " (";
				}
				
				$template_sql = "";
   				
   				foreach($template_array as $key => $value)
   				{
   					if ($template_sql)
   					{
   						$template_sql .= " OR ".constant("PROJECT_TABLE").".template_id = '".$value."'";
   					}
   					else
   					{
   						$template_sql .= "".constant("PROJECT_TABLE").".template_id = '".$value."'";
   					}
   				}
   				$add_sql .= $template_sql.")";
   			}

			if ($add_sql)
			{
				$add_sql .= " AND (";
			}
			else
			{
				$add_sql .= " (";
			}
			
			$organisation_unit_sql = "";
			
			foreach($organisation_unit_array as $key => $value)
			{
				if ($organisation_unit_sql)
				{
					$organisation_unit_sql .= " OR ".constant("PROJECT_TABLE").".toid_organ_unit = '".$value."'";
				}
				else
				{
					$organisation_unit_sql .= "".constant("PROJECT_TABLE").".toid_organ_unit = '".$value."'";
				}
			}
			
			$add_sql .= $organisation_unit_sql.") AND ".constant("PROJECT_TABLE").".toid_project IS NULL";
			
   			
   			$sql = $base_sql."".$add_sql.") AND ".constant("PROJECT_TABLE").".id IN (" .
									"SELECT DISTINCT ".constant("PROJECT_TABLE").".id AS id " .
									"FROM ".constant("PROJECT_TABLE")." " .
									"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." 				ON ".$user->get_user_id()." 						= ".constant("GROUP_HAS_USER_TABLE").".user_id " .
									"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
									"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
									"WHERE (owner_id = ".$user->get_user_id()." OR " .
											"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user->get_user_id().")) = TRUE OR " .
											"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
											"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
											"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
											"AND toid_organ_unit IS NOT NULL)".
							"AND ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".datetime = " .
									"(SELECT MAX(datetime) FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id = ".constant("PROJECT_TABLE").".id)" .
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
   	 * @param string $name
   	 * @param array $template_id
   	 * @param array $organisation_unit_array
   	 * @return integer
   	 */
   	public static function count_search_projects_without_subprojects($name, $template_array, $organisation_unit_array)
   	{
   		global $db, $user;
   		
   		if (($name or (is_array($template_array) and count($template_array) >= 1)) and 
   			(is_array($organisation_unit_array) and count($organisation_unit_array) >= 1))
   		{
   			$base_sql = $sql = "SELECT COUNT(".constant("PROJECT_TABLE").".id) AS result " .
								"FROM ".constant("PROJECT_TABLE")." " .
   								"WHERE (";					
			   			
   			if ($name)
   			{
   				$name = strtolower(trim($name));
   				$name = str_replace("*","%",$name);
   				$add_sql = " LOWER(".constant("PROJECT_TABLE").".name) LIKE '".$name."'";
   			}
   			else
   			{
   				$add_sql = "";
   			}	
   			
   			if (is_array($template_array) and count($template_array) >= 1)
   			{
   				if ($add_sql)
   				{
					$add_sql .= " AND (";
				}
				else
				{
					$add_sql .= " (";
				}
				
				$template_sql = "";
   				
   				foreach($template_array as $key => $value)
   				{
   					if ($template_sql)
   					{
   						$template_sql .= " OR ".constant("PROJECT_TABLE").".template_id = '".$value."'";
   					}
   					else
   					{
   						$template_sql .= "".constant("PROJECT_TABLE").".template_id = '".$value."'";
   					}
   				}
   				$add_sql .= $template_sql.")";
   			}

			if ($add_sql)
			{
				$add_sql .= " AND (";
			}
			else
			{
				$add_sql .= " (";
			}
			
			$organisation_unit_sql = "";
			
			foreach($organisation_unit_array as $key => $value)
			{
				if ($organisation_unit_sql)
				{
					$organisation_unit_sql .= " OR ".constant("PROJECT_TABLE").".toid_organ_unit = '".$value."'";
				}
				else
				{
					$organisation_unit_sql .= "".constant("PROJECT_TABLE").".toid_organ_unit = '".$value."'";
				}
			}
			
			$add_sql .= $organisation_unit_sql.") AND ".constant("PROJECT_TABLE").".toid_project IS NULL";
			
   			
   			$sql = $base_sql."".$add_sql.") AND ".constant("PROJECT_TABLE").".id IN (" .
									"SELECT DISTINCT ".constant("PROJECT_TABLE").".id AS id " .
									"FROM ".constant("PROJECT_TABLE")." " .
									"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." 				ON ".$user->get_user_id()." 						= ".constant("GROUP_HAS_USER_TABLE").".user_id " .
									"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
									"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
									"WHERE (owner_id = ".$user->get_user_id()." OR " .
											"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user->get_user_id().")) = TRUE OR " .
											"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
											"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
											"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
											"AND toid_organ_unit IS NOT NULL)";
   			

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
   	 * @param string $string
   	 * @param array $project_id_array
   	 * @param array $item_select_sql_array
   	 * @param string $item_join_sql
   	 * @param string $item_where_sql
   	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
   	 * @return array
   	 */
   	public static function list_data_search($string, $project_id_array, $item_select_sql_array, $item_join_sql, $item_where_sql, $order_by, $order_method, $start, $end)
   	{
   		global $db, $user;

   		if (is_array($item_select_sql_array))
   		{
   			if (count($item_select_sql_array) >= 2)
   			{
	   			$name_select_sql 		= "";
	   			$datetime_select_sql 	= "";
	   			
	   			$item_select_sql_array_length = count($item_select_sql_array);
	   			
   				for ($i=0;$i<=($item_select_sql_array_length-2);$i++)
   				{
   					$name_select_sql 		.= "CONCAT(".$item_select_sql_array[$i][name].",";
   					$datetime_select_sql 	.= "CONCAT(CAST(".$item_select_sql_array[$i][datetime]." AS TEXT),";
   				}
   				
   				$name_select_sql 		.= $item_select_sql_array[($item_select_sql_array_length-1)][name];
   				$datetime_select_sql	.= "CAST(".$item_select_sql_array[($item_select_sql_array_length-1)][datetime]." AS TEXT)";
   				
   				for ($i=0;$i<=($item_select_sql_array_length-2);$i++)
   				{
   					$name_select_sql 		.= ")";
   					$datetime_select_sql 	.= ")";
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
   			}
   		}

   		if (is_array($project_id_array) and count($project_id_array) >= 1)
   		{
   			$project_where_sql = "";
   			
   			foreach ($project_id_array as $key => $value)
   			{
   				if ($project_where_sql == "")
   				{
   					$project_where_sql .= "".constant("PROJECT_HAS_ITEM_TABLE").".project_id = ".$value."";
   				}
   				else
   				{
   					$project_where_sql .= " OR ".constant("PROJECT_HAS_ITEM_TABLE").".project_id = ".$value."";
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
					
					case "project_name":
						$sql_order_by = "ORDER BY project_name ".$sql_order_method.", name";
					break;
					
					default:
						$sql_order_by = "ORDER BY name ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY project_name, name";
			}
	   		
	   		$sql = "SELECT ".constant("PROJECT_HAS_ITEM_TABLE").".item_id AS id, " .
	   							"".$name_select_sql." AS name, ".
	   							"".$type_select_sql.", ".
	   							"".$datetime_select_sql." AS datetime, ".
	   							"".constant("PROJECT_TABLE").".id AS project_id, " .
	   							"".constant("PROJECT_TABLE").".name AS project_name " .
								"FROM ".constant("PROJECT_HAS_ITEM_TABLE")." " .
								"JOIN ".constant("ITEM_TABLE")." 	ON ".constant("PROJECT_HAS_ITEM_TABLE").".item_id 		= ".constant("ITEM_TABLE").".id " .
	   							"JOIN ".constant("PROJECT_TABLE")." ON ".constant("PROJECT_HAS_ITEM_TABLE").".project_id 	= ".constant("PROJECT_TABLE").".id " .
	   							"".$item_join_sql."" .
	   							"WHERE (".$item_where_sql.") AND (".$project_where_sql.") AND " . 
	   								"".constant("PROJECT_TABLE").".id IN (" .
										"SELECT DISTINCT ".constant("PROJECT_TABLE").".id AS id " .
										"FROM ".constant("PROJECT_TABLE")." " .
										"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." 				ON ".$user->get_user_id()." 						= ".constant("GROUP_HAS_USER_TABLE").".user_id " .
										"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
										"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
										"WHERE (owner_id = ".$user->get_user_id()." OR " .
												"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user->get_user_id().")) = TRUE OR " .
												"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
												"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
												"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
												"AND toid_organ_unit IS NOT NULL) ".
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
   	 * @param string $string
   	 * @param array $project_id_array
   	 * @param array $item_select_sql_array
   	 * @param string $item_join_sql
   	 * @param string $item_where_sql
   	 * @return integer
   	 */
   	public static function count_data_search($string, $project_id_array, $item_select_sql_array, $item_join_sql, $item_where_sql)
   	{
   		global $db, $user;

   		if (is_array($project_id_array) and count($project_id_array) >= 1)
   		{
   			$project_where_sql = "";
   			
   			foreach ($project_id_array as $key => $value)
   			{
   				if ($project_where_sql == "")
   				{
   					$project_where_sql .= "".constant("PROJECT_HAS_ITEM_TABLE").".project_id = ".$value."";
   				}
   				else
   				{
   					$project_where_sql .= " OR ".constant("PROJECT_HAS_ITEM_TABLE").".project_id = ".$value."";
   				}
   			}
   	
	   		$sql = "SELECT COUNT(".constant("PROJECT_HAS_ITEM_TABLE").".item_id) AS result " .
								"FROM ".constant("PROJECT_HAS_ITEM_TABLE")." " .
								"JOIN ".constant("ITEM_TABLE")." 	ON ".constant("PROJECT_HAS_ITEM_TABLE").".item_id 		= ".constant("ITEM_TABLE").".id " .
	   							"".$item_join_sql."" .
	   							"WHERE (".$item_where_sql.") AND (".$project_where_sql.") AND " .
	   								"".constant("PROJECT_HAS_ITEM_TABLE").".project_id IN (" .
										"SELECT DISTINCT ".constant("PROJECT_TABLE").".id AS id " .
										"FROM ".constant("PROJECT_TABLE")." " .
										"LEFT JOIN ".constant("GROUP_HAS_USER_TABLE")." 				ON ".$user->get_user_id()." 						= ".constant("GROUP_HAS_USER_TABLE").".user_id " .
										"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".organisation_unit_id " .
										"LEFT JOIN ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE")." 	ON ".constant("PROJECT_TABLE").".toid_organ_unit 	= ".constant("ORGANISATION_UNIT_HAS_MEMBER_TABLE").".member_id " .
										"WHERE (owner_id = ".$user->get_user_id()." OR " .
												"(SELECT * FROM project_permission_user(".constant("PROJECT_TABLE").".id, ".$user->get_user_id().")) = TRUE OR " .
												"(SELECT * FROM project_permission_organisation_unit(".constant("PROJECT_TABLE").".id, ".constant("PROJECT_TABLE").".toid_organ_unit)) = TRUE OR " .
												"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("GROUP_HAS_USER_TABLE").".group_id)) = TRUE OR " .
												"(SELECT * FROM project_permission_group(".constant("PROJECT_TABLE").".id, ".constant("ORGANISATION_UNIT_HAS_GROUP_TABLE").".group_id)) = TRUE)" .
												"AND toid_organ_unit IS NOT NULL) ". 
	   							"".$sql_order_by."";
	   		
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
