<?php
/**
 * @package project
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
 * Project Wrapper Access Class
 * @package project
 */
class Project_Wrapper_Access
{
	const PROJECT_TABLE = 'core_projects';
	const PROJECT_STATUS_TABLE = 'core_project_status';
	const PROJECT_TEMPLATE_TABLE = 'core_project_templates';
	const PROJECT_TEMPLATE_CAT_TABLE = 'core_project_template_cats';
	const PROJECT_HAS_PROJECT_STATUS_TABLE = 'core_project_has_project_status';
	
	const OLDL_TEMPLATE_TABLE = 'core_oldl_templates';
	
	const DATA_ENTITY_TABLE = 'core_data_entities';
	const FILE_TABLE = 'core_files';
	const FILE_VERSION_TABLE = 'core_file_versions';
	
	const GROUP_HAS_USER_TABLE = 'core_group_has_users';
	
	const ORGANISATION_UNIT_TABLE = 'core_organisation_units';
	const ORGANISATION_UNIT_HAS_GROUP_TABLE = 'core_organisation_unit_has_groups';
	const ORGANISATION_UNIT_HAS_MEMBER_TABLE = 'core_organisation_unit_has_members';

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
			
		$sql = "SELECT ".self::PROJECT_STATUS_TABLE.".id AS id, " .
					"".self::PROJECT_STATUS_TABLE.".name AS name " .
					 "FROM ".self::PROJECT_STATUS_TABLE." " .
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
			
		$sql = "SELECT COUNT(".self::PROJECT_STATUS_TABLE.".id) AS result " .
					 "FROM ".self::PROJECT_STATUS_TABLE."";
		
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
			
		$sql = "SELECT ".self::PROJECT_TEMPLATE_TABLE.".id AS id, " .
					"".self::PROJECT_TEMPLATE_TABLE.".name AS name," .
					"".self::FILE_VERSION_TABLE.".name AS file, " .
					"".self::PROJECT_TEMPLATE_CAT_TABLE.".name AS category, " .
					"".self::PROJECT_TEMPLATE_TABLE.".parent_template AS parent " .
					"FROM ".self::PROJECT_TEMPLATE_TABLE." " .
					"LEFT JOIN ".self::PROJECT_TEMPLATE_CAT_TABLE." ON ".self::PROJECT_TEMPLATE_TABLE.".cat_id 		= ".self::PROJECT_TEMPLATE_CAT_TABLE.".id " .
					"LEFT JOIN ".self::OLDL_TEMPLATE_TABLE." 		ON ".self::PROJECT_TEMPLATE_TABLE.".template_id = ".self::OLDL_TEMPLATE_TABLE.".id " .
					"LEFT JOIN ".self::DATA_ENTITY_TABLE." 			ON ".self::OLDL_TEMPLATE_TABLE.".data_entity_id 	= ".self::DATA_ENTITY_TABLE.".id " .
					"LEFT JOIN ".self::FILE_TABLE." 				ON ".self::DATA_ENTITY_TABLE.".id 					= ".self::FILE_TABLE.".data_entity_id " .
					"LEFT JOIN ".self::FILE_VERSION_TABLE." 		ON ".self::FILE_TABLE.".id 							= ".self::FILE_VERSION_TABLE.".toid " .					
					"WHERE ".self::FILE_VERSION_TABLE.".internal_revision = 1 " .
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
						
		$sql = "SELECT COUNT(".self::PROJECT_TEMPLATE_TABLE.".id) AS result " .
					"FROM ".self::PROJECT_TEMPLATE_TABLE."";
		
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
		
		$sql = "SELECT ".self::PROJECT_TEMPLATE_CAT_TABLE.".id AS id, " .
					"".self::PROJECT_TEMPLATE_CAT_TABLE.".name AS name " .
					"FROM ".self::PROJECT_TEMPLATE_CAT_TABLE." " .
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
						
		$sql = "SELECT COUNT(".self::PROJECT_TEMPLATE_CAT_TABLE.".id) AS result " .
					"FROM ".self::PROJECT_TEMPLATE_CAT_TABLE."";
		
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
			$sql = "SELECT COUNT(DISTINCT ".self::PROJECT_TABLE.".id) AS result " .
					"FROM ".self::PROJECT_TABLE." " .
					"LEFT JOIN ".self::GROUP_HAS_USER_TABLE." ON ".$user_id." = ".self::GROUP_HAS_USER_TABLE.".user_id " .
					"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE." ON ".self::PROJECT_TABLE.".toid_organ_unit = ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".organisation_unit_id " .
					"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE." ON ".self::PROJECT_TABLE.".toid_organ_unit = ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE.".user_id " .
					"WHERE (owner_id = ".$user_id." OR " .
							"(SELECT * FROM project_permission_user(".self::PROJECT_TABLE.".id, ".$user_id.")) = TRUE OR " .
							"(SELECT * FROM project_permission_organisation_unit(".self::PROJECT_TABLE.".id, ".self::PROJECT_TABLE.".toid_organ_unit)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::GROUP_HAS_USER_TABLE.".group_id)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".group_id)) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL";
				
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
			$sql = "SELECT COUNT(DISTINCT ".self::PROJECT_TABLE.".id) AS result " .
					"FROM ".self::PROJECT_TABLE." " .
					"LEFT JOIN ".self::GROUP_HAS_USER_TABLE." 				ON ".$user_id." 							= ".self::GROUP_HAS_USER_TABLE.".user_id " .
					"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE." 	ON ".self::PROJECT_TABLE.".toid_organ_unit 	= ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".organisation_unit_id " .
					"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE." 	ON ".self::PROJECT_TABLE.".toid_organ_unit 	= ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE.".user_id " .
					"WHERE (owner_id = ".$user_id." OR " .
							"(SELECT * FROM project_permission_user(".self::PROJECT_TABLE.".id, ".$user_id.")) = TRUE OR " .
							"(SELECT * FROM project_permission_organisation_unit(".self::PROJECT_TABLE.".id, ".self::PROJECT_TABLE.".toid_organ_unit)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::GROUP_HAS_USER_TABLE.".group_id)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".group_id)) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL " .
							"AND ".self::PROJECT_TABLE.".id NOT IN " .
									"(SELECT project_id FROM ".self::PROJECT_HAS_PROJECT_STATUS_TABLE." WHERE status_id = 2 OR status_id = 0 ORDER BY datetime DESC)";

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
			$sql = "SELECT COUNT(DISTINCT ".self::PROJECT_TABLE.".id) AS result " .
					"FROM ".self::PROJECT_TABLE." " .
					"LEFT JOIN ".self::GROUP_HAS_USER_TABLE." 				ON ".$user_id." 							= ".self::GROUP_HAS_USER_TABLE.".user_id " .
					"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE." 	ON ".self::PROJECT_TABLE.".toid_organ_unit 	= ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".organisation_unit_id " .
					"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE." 	ON ".self::PROJECT_TABLE.".toid_organ_unit 	= ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE.".user_id " .
					"LEFT JOIN ".self::PROJECT_HAS_PROJECT_STATUS_TABLE." 	ON ".self::PROJECT_TABLE.".id 				= ".self::PROJECT_HAS_PROJECT_STATUS_TABLE.".project_id " .
					"WHERE (owner_id = ".$user_id." OR " .
							"(SELECT * FROM project_permission_user(".self::PROJECT_TABLE.".id, ".$user_id.")) = TRUE OR " .
							"(SELECT * FROM project_permission_organisation_unit(".self::PROJECT_TABLE.".id, ".self::PROJECT_TABLE.".toid_organ_unit)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::GROUP_HAS_USER_TABLE.".group_id)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".group_id)) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL " .
							"AND ".self::PROJECT_HAS_PROJECT_STATUS_TABLE.".status_id = 2 ";

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
	public static function list_user_related_projects($user_id, $order_by, $order_method, $start, $end)
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
				
			$sql = "SELECT ".self::PROJECT_TABLE.".id AS id, " .
					"".self::PROJECT_TABLE.".name AS name, " .
					"".self::PROJECT_TABLE.".datetime AS datetime," .
					"".self::ORGANISATION_UNIT_TABLE.".name AS organisation_unit, " .
					"".self::PROJECT_TEMPLATE_TABLE.".name AS template, " .
					"".self::PROJECT_STATUS_TABLE.".name AS status, " .
					"".self::PROJECT_STATUS_TABLE.".id AS status_id " .
					"FROM ".self::PROJECT_TABLE." " .
					"JOIN ".self::ORGANISATION_UNIT_TABLE." 			ON ".self::PROJECT_TABLE.".toid_organ_unit 					= ".self::ORGANISATION_UNIT_TABLE.".id " .
					"JOIN ".self::PROJECT_TEMPLATE_TABLE." 				ON ".self::PROJECT_TABLE.".template_id 						= ".self::PROJECT_TEMPLATE_TABLE.".id " .
					"JOIN ".self::PROJECT_HAS_PROJECT_STATUS_TABLE." 	ON ".self::PROJECT_TABLE.".id 								= ".self::PROJECT_HAS_PROJECT_STATUS_TABLE.".project_id " .
					"JOIN ".self::PROJECT_STATUS_TABLE." 				ON ".self::PROJECT_HAS_PROJECT_STATUS_TABLE.".status_id 	= ".self::PROJECT_STATUS_TABLE.".id " .					
					"WHERE " .
							"".self::PROJECT_TABLE.".id IN (" .
									"SELECT DISTINCT ".self::PROJECT_TABLE.".id AS id " .
									"FROM ".self::PROJECT_TABLE." " .
									"LEFT JOIN ".self::GROUP_HAS_USER_TABLE." ON ".$user_id." = ".self::GROUP_HAS_USER_TABLE.".user_id " .
									"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE." ON ".self::PROJECT_TABLE.".toid_organ_unit = ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".organisation_unit_id " .
									"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE." ON ".self::PROJECT_TABLE.".toid_organ_unit = ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE.".user_id " .
									"WHERE (owner_id = ".$user_id." OR " .
											"(SELECT * FROM project_permission_user(".self::PROJECT_TABLE.".id, ".$user_id.")) = TRUE OR " .
											"(SELECT * FROM project_permission_organisation_unit(".self::PROJECT_TABLE.".id, ".self::PROJECT_TABLE.".toid_organ_unit)) = TRUE OR " .
											"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::GROUP_HAS_USER_TABLE.".group_id)) = TRUE OR " .
											"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".group_id)) = TRUE)" .
											"AND toid_organ_unit IS NOT NULL)".
							"AND ".self::PROJECT_HAS_PROJECT_STATUS_TABLE.".datetime = " .
									"(SELECT MAX(datetime) FROM ".self::PROJECT_HAS_PROJECT_STATUS_TABLE." WHERE ".self::PROJECT_HAS_PROJECT_STATUS_TABLE.".project_id = ".self::PROJECT_TABLE.".id)" .
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
	public static function count_list_user_related_projects($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$sql = "SELECT COUNT(DISTINCT ".self::PROJECT_TABLE.".id) AS result " .
					"FROM ".self::PROJECT_TABLE." " .
					"LEFT JOIN ".self::GROUP_HAS_USER_TABLE." ON ".$user_id." = ".self::GROUP_HAS_USER_TABLE.".user_id " .
					"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE." ON ".self::PROJECT_TABLE.".toid_organ_unit = ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".organisation_unit_id " .
					"LEFT JOIN ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE." ON ".self::PROJECT_TABLE.".toid_organ_unit = ".self::ORGANISATION_UNIT_HAS_MEMBER_TABLE.".user_id " .
					"WHERE (owner_id = ".$user_id." OR " .
							"(SELECT * FROM project_permission_user(".self::PROJECT_TABLE.".id, ".$user_id.")) = TRUE OR " .
							"(SELECT * FROM project_permission_organisation_unit(".self::PROJECT_TABLE.".id, ".self::PROJECT_TABLE.".toid_organ_unit)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::GROUP_HAS_USER_TABLE.".group_id)) = TRUE OR " .
							"(SELECT * FROM project_permission_group(".self::PROJECT_TABLE.".id, ".self::ORGANISATION_UNIT_HAS_GROUP_TABLE.".group_id)) = TRUE)" .
							"AND toid_organ_unit IS NOT NULL";
				
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
	 * NEW
	 */
	public static function list_projects_by_item_id($item_id, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($item_id))
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
						"".constant("PROJECT_TABLE").".owner_id AS owner " .
						"FROM ".constant("PROJECT_TABLE")." " .
						"JOIN ".constant("PROJECT_HAS_ITEM_TABLE")." 				ON ".constant("PROJECT_TABLE").".id 										= ".constant("PROJECT_HAS_ITEM_TABLE").".project_id ".
						"JOIN ".constant("PROJECT_TEMPLATE_TABLE")." 				ON ".constant("PROJECT_TABLE").".template_id 								= ".constant("PROJECT_TEMPLATE_TABLE").".id " .
						"JOIN ".constant("USER_PROFILE_TABLE")." 					ON ".constant("PROJECT_TABLE").".owner_id								 	= ".constant("USER_PROFILE_TABLE").".id " .
						"JOIN ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." 		ON ".constant("PROJECT_TABLE").".id 										= ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".project_id " .
						"JOIN ".constant("PROJECT_STATUS_TABLE")." 					ON ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE").".status_id 				= ".constant("PROJECT_STATUS_TABLE").".id " .					
						"WHERE ".constant("PROJECT_HAS_ITEM_TABLE").".item_id = ".$item_id." " .
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
	 * NEW
	 */
	public static function count_projects_by_item_id($item_id)
	{
		global $db;
		
		if (is_numeric($item_id))
		{
			$sql = "SELECT COUNT(DISTINCT ".self::PROJECT_TABLE.".id) AS result " .
						"FROM ".constant("PROJECT_TABLE")." " .
						"JOIN ".constant("PROJECT_HAS_ITEM_TABLE")." ON ".constant("PROJECT_TABLE").".id = ".constant("PROJECT_HAS_ITEM_TABLE").".project_id ".
						"WHERE ".constant("PROJECT_HAS_ITEM_TABLE").".item_id = ".$item_id." ";
						
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
