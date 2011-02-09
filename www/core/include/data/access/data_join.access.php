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
 * Data Join Access Class
 * @package data
 */
class DataJoin_Access
{
	const FOLDER_TABLE = 'core_folders';
	const FILE_TABLE = 'core_files';
	const FILE_VERSION_TABLE = 'core_file_versions';
	const VALUE_TABLE = 'core_values';
	const VALUE_VERSION_TABLE = 'core_value_versions';
	const VALUE_TYPE_TABLE = 'core_value_types';
	const OBJECT_TABLE = 'core_objects';

	/**
	 * @param integer $toid
	 * @return array
	 */
	public static function get_images_in_folder($toid)
	{
		global $db;
			
		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT ".self::FILE_TABLE.".id FROM ".self::OBJECT_TABLE." " .
					"JOIN ".self::FILE_TABLE." ON ".self::OBJECT_TABLE.".file_id = ".self::FILE_TABLE.".id " .
					"JOIN ".self::FILE_VERSION_TABLE." ON ".self::FILE_TABLE.".id = ".self::FILE_VERSION_TABLE.".toid " .
						"WHERE ".self::OBJECT_TABLE.".file_id IS NOT NULL AND " .
								"".self::OBJECT_TABLE.".value_id IS NULL AND " .
								"".self::FILE_VERSION_TABLE.".current = 't' AND " .
								"".self::OBJECT_TABLE.".toid = ".$toid." AND " .
									"(LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'jpg' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'jpeg' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'png' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'bmp' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'tiff' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'tif' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'gif')";				
			
			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array, $data[id]);
			}
							
			if (count($return_array) >= 1)
			{
				return $return_array;
			}
			else
			{
				return null;
			}	
		}
		else
		{
			return null;
		}
	}

	/**
	 * @todo table names !!!
	 */
	public static function list_data_entity_childs($data_entity_pid, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($data_entity_pid))
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
						$sql_order_by = "ORDER BY core_folders.id, core_virtual_folders.id, core_files.id, core_values.id";
					break;
				
				endswitch;
			}
			
			$sql = "SELECT CONCAT( " .
								"CONCAT(core_folders.name, " .
										"core_virtual_folders.name ), " .
								"CONCAT(core_file_versions.name, " .
										"core_value_types.name) " .
									") AS name, " .
							"current_entity.datetime AS datetime, " .
							"current_entity.owner_id AS owner_id, " .
							"core_file_versions.size AS size, " .
							"core_data_entities.permission, " .
							"core_data_entities.automatic, " .
							"core_folders.id AS folder_id, " .
							"core_files.id AS file_id, " .
							"core_values.id AS value_id, " .
							"core_virtual_folders.id AS virtual_folder_id " .
						 "FROM core_data_entities " .
						"LEFT JOIN core_data_entity_has_data_entities 	ON core_data_entities.id 								= core_data_entity_has_data_entities.data_entity_pid " .
						"JOIN core_data_entities AS current_entity		ON core_data_entity_has_data_entities.data_entity_cid	= current_entity.id " .
						"LEFT JOIN core_folders 						ON core_data_entity_has_data_entities.data_entity_cid	= core_folders.data_entity_id " .
						"LEFT JOIN core_files 							ON core_data_entity_has_data_entities.data_entity_cid 	= core_files.data_entity_id " .
						"LEFT JOIN core_file_versions 					ON core_files.id 										= core_file_versions.toid " .
						"LEFT JOIN core_values 							ON core_data_entity_has_data_entities.data_entity_cid 	= core_values.data_entity_id " . 
						"LEFT JOIN core_value_types 					ON core_values.type_id 									= core_value_types.id " .
						"LEFT JOIN core_value_versions 					ON core_values.id 										= core_value_versions.toid " .
						"LEFT JOIN core_virtual_folders 				ON core_data_entity_has_data_entities.data_entity_cid 	= core_virtual_folders.data_entity_id " .
						"WHERE " .
							"(core_folders.id IS NOT NULL OR " .
							"core_virtual_folders IS NOT NULL OR " .
							"core_file_versions.current = 't' OR " .
							"core_value_versions.current = 't') " .
						"AND " .
						"core_data_entities.id = ".$data_entity_pid." " .
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
	 * @todo table names !!
	 */
	public static function count_list_data_entity_childs($data_entity_pid)
	{
		global $db;
		
		if (is_numeric($data_entity_pid))
		{
			$sql = "SELECT  COUNT(core_data_entities.id) AS resutl" .
						 "FROM core_data_entities " .
						"LEFT JOIN core_data_entity_has_data_entities 	ON core_data_entities.id 								= core_data_entity_has_data_entities.data_entity_pid " .
						"WHERE core_data_entities.id = ".$data_entity_pid."";

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
	 * @todo table names !!
	 */
	public static function list_virtual_folders_by_folder_id($folder_id)
	{
		global $db;
		
		if (is_numeric($folder_id))
		{
			$sql = "SELECT core_virtual_folders.id FROM core_virtual_folders " .
					"JOIN core_data_entity_has_data_entities 	ON core_virtual_folders.data_entity_id 					= core_data_entity_has_data_entities.data_entity_cid " .
					"JOIN core_folders 							ON core_data_entity_has_data_entities.data_entity_pid 	= core_folders.data_entity_id " .
					"WHERE core_folders.id = ".$folder_id."";

			$return_array = array();
			
			$res = $db->db_query($sql);
			while($data = $db->db_fetch_assoc($res))
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
	
}
?>
