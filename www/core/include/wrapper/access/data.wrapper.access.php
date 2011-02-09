<?php
/**
 * @package data
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
 * Data Wrapper Access Class
 * @package data
 */
class Data_Wrapper_Access
{
	const DATA_ENTITY_TABLE = 'core_data_entities';
	
	const FILE_TABLE = 'core_files';
	const FILE_VERSION_TABLE = 'core_file_versions';
	
	const VALUE_TABLE = 'core_values';
	const VALUE_VERSION_TABLE = 'core_value_versions';
	const VALUE_TYPE_TABLE = 'core_value_types';
	
	const USER_TABLE = 'core_users';
	const USER_PROFILE_TABLE = 'core_user_profiles';
	
	const OLVDL_TEMPLATE_TABLE = 'core_olvdl_templates';
	
	/**
	 * @param integer $folder_id
	 * @param string $search_string
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function search_ffv($folder_id, $search_string, $order_by, $order_method, $start, $end)
	{
		global $db;

		if (is_numeric($folder_id) and $search_string)
		{
			$search_string = str_replace("*", "%", $search_string);
			$search_string = strtolower($search_string);
			
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
					
					case "size":
						$sql_order_by = "ORDER BY ".self::FILE_VERSION_TABLE.".size ".$sql_order_method;
					break;
					
					case "owner":
						$sql_order_by = "ORDER BY CONCAT(user_profile_table_a.surname, user_profile_table_b.surname) ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY datetime";
					break;
				
				endswitch;
			}
			
			$sql = "SELECT ".self::OBJECT_TABLE.".id AS id, " .
						"CONCAT(".self::FILE_VERSION_TABLE.".name, ".self::VALUE_TYPE_TABLE.".name) AS name, " .
						"CONCAT(CAST(".self::FILE_TABLE.".datetime AS text), CAST(".self::VALUE_TABLE.".datetime AS text)) AS datetime, " .
						"CONCAT( " .
								"NAMECONCAT( " .
										"user_profile_table_a.title, " .
										"NAMECONCAT(user_profile_table_a.forename, " .
												"user_profile_table_a.surname " .
												") " .
										"), " .
								"NAMECONCAT(" .
										"user_profile_table_b.title, " .
										"NAMECONCAT(user_profile_table_b.forename, " .
												"user_profile_table_b.surname " .
												") " .
										") " .
								") AS owner," .
						"".self::FILE_TABLE.".id AS file_id, " .
						"".self::VALUE_TABLE.".id AS value_id " .
						"FROM ".self::OBJECT_TABLE." " .
					"LEFT JOIN ".self::FILE_TABLE." 								ON ".self::OBJECT_TABLE.".file_id 	= ".self::FILE_TABLE.".id " .
					"LEFT JOIN ".self::FILE_VERSION_TABLE." 						ON ".self::FILE_TABLE.".id 			= ".self::FILE_VERSION_TABLE.".toid " .
					"LEFT JOIN ".self::VALUE_TABLE." 								ON ".self::OBJECT_TABLE.".value_id 	= ".self::VALUE_TABLE.".id " .
					"LEFT JOIN ".self::VALUE_TYPE_TABLE." 							ON ".self::VALUE_TABLE.".type_id 	= ".self::VALUE_TYPE_TABLE.".id " .
					"LEFT JOIN ".self::USER_PROFILE_TABLE." AS user_profile_table_a	ON ".self::FILE_TABLE.".owner_id 	= user_profile_table_a.id " .
					"LEFT JOIN ".self::USER_PROFILE_TABLE." AS user_profile_table_b	ON ".self::VALUE_TABLE.".owner_id 	= user_profile_table_b.id " .					
					"WHERE (".self::OBJECT_TABLE.".toid = ".$folder_id." OR ".self::OBJECT_TABLE.".toid IN (SELECT * FROM search_get_sub_folders(".$folder_id."))) " .
							"AND (".self::FILE_VERSION_TABLE.".name LIKE '".$search_string."' OR ".self::VALUE_TYPE_TABLE.".name LIKE '".$search_string."') " .
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
	}
	
	/**
	 * @param integer $folder_id
	 * @param string $search_string
	 * @return integer
	 */
	public static function count_search_ffv($folder_id, $search_string)
	{
		global $db;
		
		if (is_numeric($folder_id) and $search_string)
		{
			$search_string = str_replace("*", "%", $search_string);
			$search_string = strtolower($search_string);
			
			$sql = "SELECT COUNT(".self::OBJECT_TABLE.".id) AS result " .
						"FROM ".self::OBJECT_TABLE." " .
					"LEFT JOIN ".self::FILE_TABLE." 		ON ".self::OBJECT_TABLE.".file_id 	= ".self::FILE_TABLE.".id " .
					"LEFT JOIN ".self::FILE_VERSION_TABLE." ON ".self::FILE_TABLE.".id 			= ".self::FILE_VERSION_TABLE.".toid " .
					"LEFT JOIN ".self::VALUE_TABLE." 		ON ".self::OBJECT_TABLE.".value_id 	= ".self::VALUE_TABLE.".id " .
					"LEFT JOIN ".self::VALUE_TYPE_TABLE." 	ON ".self::VALUE_TABLE.".type_id 	= ".self::VALUE_TYPE_TABLE.".id " .
					"WHERE (".self::OBJECT_TABLE.".toid = ".$folder_id." OR ".self::OBJECT_TABLE.".toid IN (SELECT * FROM search_get_sub_folders(".$folder_id."))) " .
							"AND (".self::FILE_VERSION_TABLE.".name LIKE '".$search_string."' OR ".self::VALUE_TYPE_TABLE.".name LIKE '".$search_string."')";
						
						
			$return_array = array();
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			return $data[result];
		}
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_value_templates($order_by, $order_method, $start, $end)
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
							
				default:
					$sql_order_by = "ORDER BY id ".$sql_order_method;
				break;
			
			endswitch;	
		}
		else
		{
			$sql_order_by = "ORDER BY id";
		}
			
		$sql = "SELECT ".self::VALUE_TYPE_TABLE.".id AS id, " .
					"".self::VALUE_TYPE_TABLE.".name AS name," .
					"".self::FILE_VERSION_TABLE.".name AS file " .
					"FROM ".self::VALUE_TYPE_TABLE." " .
					"LEFT JOIN ".self::OLVDL_TEMPLATE_TABLE." 		ON ".self::VALUE_TYPE_TABLE.".template_id 			= ".self::OLVDL_TEMPLATE_TABLE.".id " .
					"LEFT JOIN ".self::DATA_ENTITY_TABLE." 			ON ".self::OLVDL_TEMPLATE_TABLE.".data_entity_id 	= ".self::DATA_ENTITY_TABLE.".id " .
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
	 * @return array
	 */
	public static function count_list_value_templates()
	{
		global $db;
						
		$sql = "SELECT COUNT(".self::VALUE_TYPE_TABLE.".id) AS result " .
					"FROM ".self::VALUE_TYPE_TABLE."";
		
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);

		return $data[result];
	}	
	
}

?>
