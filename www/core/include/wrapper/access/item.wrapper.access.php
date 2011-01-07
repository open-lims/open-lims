<?php
/**
 * @package item
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
 * Item Wrapper Access Class
 * @package item
 * @todo remove dependencies
 */
class Item_Wrapper_Access
{
	const PROJECT_TABLE = 'core_projects';

	const PROJECT_HAS_ITEM_TABLE = 'core_project_has_items';
	const SAMPLE_HAS_ITEM_TABLE = 'core_sample_has_items';
	
	const ITEM_TABLE = 'core_items';
	const ITEM_INFORMATION_TABLE = 'core_item_information';
	
	const ITEM_IS_OBJECT_TABLE = 'core_item_is_object';
	const ITEM_IS_SAMPLE_TABLE = 'core_item_is_sample';
	const ITEM_IS_METHOD_TABLE = 'core_item_is_method';
	
	const ITEM_HAS_ITEM_CLASS_TABLE = 'core_item_has_item_classes';
	const ITEM_CLASS_HAS_ITEM_INFORMATION_TABLE = 'core_item_class_has_item_information';
	const ITEM_HAS_ITEM_INFORMATION_TABLE = 'core_item_has_item_information';
	
	const OBJECT_TABLE = 'core_objects';
	
	const FILE_TABLE = 'core_files';
	const FILE_VERSION_TABLE = 'core_file_versions';
	
	const VALUE_TABLE = 'core_values';
	const VALUE_VERSION_TABLE = 'core_value_versions';
	const VALUE_TYPE_TABLE = 'core_value_types';
	
	const SAMPLE_TABLE = 'core_samples';
	
	const METHOD_TABLE = 'core_methods';
	const METHOD_TYPE_TABLE = 'core_method_types';
	
	/**
	 * @param array $project_id_array
	 * @param string $search_string
	 * @return integer
	 */
	public static function count_search_project_data($project_id_array, $search_string)
	{
		global $db;
		
		if (is_array($project_id_array) and count($project_id_array) >= 1 and $search_string)
		{
			$search_string = str_replace("*","%",$search_string);
			$search_string = strtolower($search_string);
			
			$sql_project_where = "";
			
			foreach($project_id_array as $key => $value)
			{
				if ($sql_project_where == "")
				{
					$sql_project_where = self::PROJECT_HAS_ITEM_TABLE.".project_id = ".$value."";
				}
				else
				{
					$sql_project_where .= " OR ".self::PROJECT_HAS_ITEM_TABLE.".project_id = ".$value."";
				}	
			}
			
			$sql = "SELECT COUNT(".self::PROJECT_HAS_ITEM_TABLE.".item_id) AS result " .
						 "FROM ".self::PROJECT_HAS_ITEM_TABLE." " .
					"LEFT JOIN ".self::ITEM_IS_OBJECT_TABLE." 	ON ".self::ITEM_IS_OBJECT_TABLE.".item_id 	= ".self::PROJECT_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::ITEM_IS_METHOD_TABLE." 	ON ".self::ITEM_IS_METHOD_TABLE.".item_id 	= ".self::PROJECT_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::ITEM_IS_SAMPLE_TABLE." 	ON ".self::ITEM_IS_SAMPLE_TABLE.".item_id 	= ".self::PROJECT_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::OBJECT_TABLE." 			ON ".self::OBJECT_TABLE.".id 				= ".self::ITEM_IS_OBJECT_TABLE.".object_id " .
					"LEFT JOIN ".self::FILE_TABLE." 			ON ".self::FILE_TABLE.".id 					= ".self::OBJECT_TABLE.".file_id " .
					"LEFT JOIN ".self::FILE_VERSION_TABLE." 	ON ".self::FILE_VERSION_TABLE.".toid 		= ".self::FILE_TABLE.".id " .
					"LEFT JOIN ".self::VALUE_TABLE." 			ON ".self::VALUE_TABLE.".id 				= ".self::OBJECT_TABLE.".value_id " .
					"LEFT JOIN ".self::VALUE_TYPE_TABLE." 		ON ".self::VALUE_TYPE_TABLE.".id 			= ".self::VALUE_TABLE.".type_id " .
					"LEFT JOIN ".self::SAMPLE_TABLE." 			ON ".self::SAMPLE_TABLE.".id 				= ".self::ITEM_IS_SAMPLE_TABLE.".sample_id " .
					"LEFT JOIN ".self::METHOD_TABLE." 			ON ".self::METHOD_TABLE.".id 				= ".self::ITEM_IS_METHOD_TABLE.".method_id " .
					"LEFT JOIN ".self::METHOD_TYPE_TABLE." 		ON ".self::METHOD_TYPE_TABLE.".id 			= ".self::METHOD_TABLE.".type_id " .
					"LEFT JOIN ".self::PROJECT_TABLE."			ON ".self::PROJECT_TABLE.".id 				= ".self::PROJECT_HAS_ITEM_TABLE.".project_id " .
						"WHERE " .
							"(LOWER(".self::FILE_VERSION_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::VALUE_TYPE_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::METHOD_TYPE_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::SAMPLE_TABLE.".name) LIKE '".$search_string."')" .
									" AND " .
									"(".$sql_project_where.")";
			
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
	 * @param array $project_id_array
	 * @param string $search_string
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function search_project_data($project_id_array, $search_string, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_array($project_id_array) and count($project_id_array) >= 1 and $search_string)
		{
			$search_string = str_replace("*","%",$search_string);
			$search_string = strtolower($search_string);
			
			$sql_project_where = "";
			
			foreach($project_id_array as $key => $value)
			{
				if ($sql_project_where == "")
				{
					$sql_project_where = self::PROJECT_HAS_ITEM_TABLE.".project_id = ".$value."";
				}
				else
				{
					$sql_project_where .= " OR ".self::PROJECT_HAS_ITEM_TABLE.".project_id = ".$value."";
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
						$sql_order_by = "ORDER BY project_name ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY ".self::PROJECT_HAS_ITEM_TABLE.".item_id";
					break;
				endswitch;
			}
			
			$sql = "SELECT ".self::PROJECT_HAS_ITEM_TABLE.".item_id, " .
								"CONCAT(".self::SAMPLE_TABLE.".name, " .
									"CONCAT(".self::METHOD_TYPE_TABLE.".name ," .
										"CONCAT(".self::FILE_VERSION_TABLE.".name, ".self::VALUE_TYPE_TABLE.".name))) AS name, " .
								"CONCAT(CAST(".self::SAMPLE_TABLE.".datetime AS text) ," .
									"CONCAT(CAST(".self::METHOD_TABLE.".datetime AS text) ," .
										"CONCAT(CAST(".self::FILE_TABLE.".datetime AS text), CAST(".self::VALUE_TABLE.".datetime AS text)))) AS datetime, " .
						 		"".self::PROJECT_TABLE.".name AS project_name, " .
						 		"".self::PROJECT_TABLE.".id AS project_id, " .
						 		"".self::OBJECT_TABLE.".file_id AS file_id, " .
						 		"".self::OBJECT_TABLE.".value_id AS value_id, " .
						 		"".self::ITEM_IS_METHOD_TABLE.".method_id AS method_id, " .
						 		"".self::ITEM_IS_SAMPLE_TABLE.".sample_id AS sample_id " .
						 "FROM ".self::PROJECT_HAS_ITEM_TABLE." " .
					"LEFT JOIN ".self::ITEM_IS_OBJECT_TABLE." 	ON ".self::ITEM_IS_OBJECT_TABLE.".item_id 	= ".self::PROJECT_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::ITEM_IS_METHOD_TABLE." 	ON ".self::ITEM_IS_METHOD_TABLE.".item_id 	= ".self::PROJECT_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::ITEM_IS_SAMPLE_TABLE." 	ON ".self::ITEM_IS_SAMPLE_TABLE.".item_id 	= ".self::PROJECT_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::OBJECT_TABLE." 			ON ".self::OBJECT_TABLE.".id 				= ".self::ITEM_IS_OBJECT_TABLE.".object_id " .
					"LEFT JOIN ".self::FILE_TABLE." 			ON ".self::FILE_TABLE.".id 					= ".self::OBJECT_TABLE.".file_id " .
					"LEFT JOIN ".self::FILE_VERSION_TABLE." 	ON ".self::FILE_VERSION_TABLE.".toid 		= ".self::FILE_TABLE.".id " .
					"LEFT JOIN ".self::VALUE_TABLE." 			ON ".self::VALUE_TABLE.".id 				= ".self::OBJECT_TABLE.".value_id " .
					"LEFT JOIN ".self::VALUE_TYPE_TABLE." 		ON ".self::VALUE_TYPE_TABLE.".id 			= ".self::VALUE_TABLE.".type_id " .
					"LEFT JOIN ".self::SAMPLE_TABLE." 			ON ".self::SAMPLE_TABLE.".id 				= ".self::ITEM_IS_SAMPLE_TABLE.".sample_id " .
					"LEFT JOIN ".self::METHOD_TABLE." 			ON ".self::METHOD_TABLE.".id 				= ".self::ITEM_IS_METHOD_TABLE.".method_id " .
					"LEFT JOIN ".self::METHOD_TYPE_TABLE." 		ON ".self::METHOD_TYPE_TABLE.".id 			= ".self::METHOD_TABLE.".type_id " .
					"LEFT JOIN ".self::PROJECT_TABLE."			ON ".self::PROJECT_TABLE.".id 				= ".self::PROJECT_HAS_ITEM_TABLE.".project_id " .
						"WHERE " .
							"(LOWER(".self::FILE_VERSION_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::VALUE_TYPE_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::METHOD_TYPE_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::SAMPLE_TABLE.".name) LIKE '".$search_string."')" .
									" AND " .
									"(".$sql_project_where.") " .
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
	 * @param array $sample_id_id
	 * @param string $search_string
	 * @return integer
	 */
	public static function count_search_sample_data($sample_id_array, $search_string)
	{
		global $db;
		
		if (is_array($sample_id_array) and count($sample_id_array) >= 1 and $search_string)
		{
			$search_string = str_replace("*","%",$search_string);
			$search_string = strtolower($search_string);
			
			$sql_sample_where = "";
			
			foreach($sample_id_array as $key => $value)
			{
				if ($sql_sample_where == "")
				{
					$sql_sample_where = self::SAMPLE_HAS_ITEM_TABLE.".sample_id = ".$value."";
				}
				else
				{
					$sql_sample_where .= " OR ".self::SAMPLE_HAS_ITEM_TABLE.".sample_id = ".$value."";
				}	
			}
			
			$sql = "SELECT COUNT(".self::SAMPLE_HAS_ITEM_TABLE.".item_id) AS result " .
						 "FROM ".self::SAMPLE_HAS_ITEM_TABLE." " .
					"LEFT JOIN ".self::ITEM_IS_OBJECT_TABLE." 	ON ".self::ITEM_IS_OBJECT_TABLE.".item_id 	= ".self::SAMPLE_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::ITEM_IS_METHOD_TABLE." 	ON ".self::ITEM_IS_METHOD_TABLE.".item_id 	= ".self::SAMPLE_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::ITEM_IS_SAMPLE_TABLE." 	ON ".self::ITEM_IS_SAMPLE_TABLE.".item_id 	= ".self::SAMPLE_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::OBJECT_TABLE." 			ON ".self::OBJECT_TABLE.".id 				= ".self::ITEM_IS_OBJECT_TABLE.".object_id " .
					"LEFT JOIN ".self::FILE_TABLE." 			ON ".self::FILE_TABLE.".id 					= ".self::OBJECT_TABLE.".file_id " .
					"LEFT JOIN ".self::FILE_VERSION_TABLE." 	ON ".self::FILE_VERSION_TABLE.".toid 		= ".self::FILE_TABLE.".id " .
					"LEFT JOIN ".self::VALUE_TABLE." 			ON ".self::VALUE_TABLE.".id 				= ".self::OBJECT_TABLE.".value_id " .
					"LEFT JOIN ".self::VALUE_TYPE_TABLE." 		ON ".self::VALUE_TYPE_TABLE.".id 			= ".self::VALUE_TABLE.".type_id " .
					"LEFT JOIN ".self::SAMPLE_TABLE." 			ON ".self::SAMPLE_TABLE.".id 				= ".self::ITEM_IS_SAMPLE_TABLE.".sample_id " .
					"LEFT JOIN ".self::METHOD_TABLE." 			ON ".self::METHOD_TABLE.".id 				= ".self::ITEM_IS_METHOD_TABLE.".method_id " .
					"LEFT JOIN ".self::METHOD_TYPE_TABLE." 		ON ".self::METHOD_TYPE_TABLE.".id 			= ".self::METHOD_TABLE.".type_id " .
						"WHERE " .
							"(LOWER(".self::FILE_VERSION_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::VALUE_TYPE_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::METHOD_TYPE_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::SAMPLE_TABLE.".name) LIKE '".$search_string."')" .
									" AND " .
									"(".$sql_sample_where.")";
			
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
	 * @param array $sample_id_array
	 * @param string $search_string
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function search_sample_data($sample_id_array, $search_string, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_array($sample_id_array) and count($sample_id_array) >= 1 and $search_string)
		{
			$search_string = str_replace("*","%",$search_string);
			$search_string = strtolower($search_string);
			
			$sql_sample_where = "";
			
			foreach($sample_id_array as $key => $value)
			{
				if ($sql_sample_where == "")
				{
					$sql_sample_where = self::SAMPLE_HAS_ITEM_TABLE.".sample_id = ".$value."";
				}
				else
				{
					$sql_sample_where .= " OR ".self::SAMPLE_HAS_ITEM_TABLE.".sample_id = ".$value."";
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
					
					case "sample_name":
						$sql_order_by = "ORDER BY sample_name ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY ".self::SAMPLE_HAS_ITEM_TABLE.".item_id";
					break;
				
				endswitch;
			}
			
			$sql = "SELECT ".self::SAMPLE_HAS_ITEM_TABLE.".item_id, " .
								"CONCAT(sample_table_a.name, " .
									"CONCAT(".self::METHOD_TYPE_TABLE.".name ," .
										"CONCAT(".self::FILE_VERSION_TABLE.".name, ".self::VALUE_TYPE_TABLE.".name))) AS name, " .
								"CONCAT(CAST(sample_table_a.datetime AS text) ," .
									"CONCAT(CAST(".self::METHOD_TABLE.".datetime AS text) ," .
										"CONCAT(CAST(".self::FILE_TABLE.".datetime AS text), CAST(".self::VALUE_TABLE.".datetime AS text)))) AS datetime, " .
						 		"sample_table_b.name AS sample_name, " .
						 		"sample_table_b.id AS sample_id, " .
						 		"".self::OBJECT_TABLE.".file_id AS file_id, " .
						 		"".self::OBJECT_TABLE.".value_id AS value_id, " .
						 		"".self::ITEM_IS_METHOD_TABLE.".method_id AS method_id, " .
						 		"".self::ITEM_IS_SAMPLE_TABLE.".sample_id AS sample_id " .
						 "FROM ".self::SAMPLE_HAS_ITEM_TABLE." " .
					"LEFT JOIN ".self::ITEM_IS_OBJECT_TABLE." 	ON ".self::ITEM_IS_OBJECT_TABLE.".item_id 	= ".self::SAMPLE_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::ITEM_IS_METHOD_TABLE." 	ON ".self::ITEM_IS_METHOD_TABLE.".item_id 	= ".self::SAMPLE_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::ITEM_IS_SAMPLE_TABLE." 	ON ".self::ITEM_IS_SAMPLE_TABLE.".item_id 	= ".self::SAMPLE_HAS_ITEM_TABLE.".item_id " .
					"LEFT JOIN ".self::OBJECT_TABLE." 			ON ".self::OBJECT_TABLE.".id 				= ".self::ITEM_IS_OBJECT_TABLE.".object_id " .
					"LEFT JOIN ".self::FILE_TABLE." 			ON ".self::FILE_TABLE.".id 					= ".self::OBJECT_TABLE.".file_id " .
					"LEFT JOIN ".self::FILE_VERSION_TABLE." 	ON ".self::FILE_VERSION_TABLE.".toid 		= ".self::FILE_TABLE.".id " .
					"LEFT JOIN ".self::VALUE_TABLE." 			ON ".self::VALUE_TABLE.".id 				= ".self::OBJECT_TABLE.".value_id " .
					"LEFT JOIN ".self::VALUE_TYPE_TABLE." 		ON ".self::VALUE_TYPE_TABLE.".id 			= ".self::VALUE_TABLE.".type_id " .
					"LEFT JOIN ".self::SAMPLE_TABLE." AS sample_table_a			ON sample_table_a.id 		= ".self::ITEM_IS_SAMPLE_TABLE.".sample_id " .
					"LEFT JOIN ".self::METHOD_TABLE." 			ON ".self::METHOD_TABLE.".id 				= ".self::ITEM_IS_METHOD_TABLE.".method_id " .
					"LEFT JOIN ".self::METHOD_TYPE_TABLE." 		ON ".self::METHOD_TYPE_TABLE.".id 			= ".self::METHOD_TABLE.".type_id " .
					"LEFT JOIN ".self::SAMPLE_TABLE." AS sample_table_b			ON sample_table_b.id 		= ".self::SAMPLE_HAS_ITEM_TABLE.".sample_id " .
						"WHERE " .
							"(LOWER(".self::FILE_VERSION_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::VALUE_TYPE_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(".self::METHOD_TYPE_TABLE.".name) LIKE '".$search_string."' OR " .
								"LOWER(sample_table_a.name) LIKE '".$search_string."')" .
									" AND " .
									"(".$sql_sample_where.") " .
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
	 * @param bool $values
	 * @param bool $project_descriptions
	 * @param bool $sample_descriptions
	 * @param bool $items
	 * @param string $language_name
	 * @param string $search_string
	 * @return integer
	 */
	public static function count_search_fulltext($values, $project_descriptions, $sample_descriptions, $items, $language_name, $search_string)
	{
		global $db;
		
		if ($search_string and ($values or $project_descriptions or $sample_descriptions or $items))
		{
			if ($language_name == null)
			{
				$language_name_insert = "english";
			}
			else
			{
				$language_name_insert = $language_name;
			}
			
			
			$sql_where = "";
			
			if ($values == true and $project_descriptions == true)
			{
				$sql_where = " WHERE ".self::VALUE_VERSION_TABLE.".text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
			}
			else
			{
				if ($values == true)
				{
					$sql_where = " WHERE (".self::VALUE_VERSION_TABLE.".text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."') AND ".self::VALUE_TABLE.".type != 2)";
				}
				else
				{
					$sql_where = " WHERE (".self::VALUE_VERSION_TABLE.".text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."') AND ".self::VALUE_TABLE.".type == 2)";
				}
			}
			
			if ($sample_descriptions == true)
			{
				if ($sql_where)
				{
					$sql_where .= " OR ".self::SAMPLE_TABLE.".comment_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
				}
				else
				{
					$sql_where = " WHERE ".self::SAMPLE_TABLE.".comment_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
				}
			}
			
			if ($items == true)
			{
				if ($sql_where)
				{
					$sql_where .= " OR item_information_table_a.description_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')" .
									" OR item_information_table_a.keywords_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
				}
				else
				{
					$sql_where = " WHERE item_information_table_a.description_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')" .
									" OR item_information_table_a.keywords_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
				}
			}
	
			
			$sql = "SELECT COUNT(".self::ITEM_TABLE.".id) AS result " .
						"FROM ".self::ITEM_TABLE." " .
						"LEFT JOIN ".self::ITEM_IS_OBJECT_TABLE." 									ON ".self::ITEM_IS_OBJECT_TABLE.".item_id 							= ".self::ITEM_TABLE.".id " .
						"LEFT JOIN ".self::ITEM_IS_SAMPLE_TABLE." 									ON ".self::ITEM_IS_SAMPLE_TABLE.".item_id 							= ".self::ITEM_TABLE.".id " .					
						"LEFT JOIN ".self::OBJECT_TABLE." 											ON ".self::OBJECT_TABLE.".id 										= ".self::ITEM_IS_OBJECT_TABLE.".object_id " .
						"LEFT JOIN ".self::VALUE_TABLE." 											ON ".self::VALUE_TABLE.".id 										= ".self::OBJECT_TABLE.".value_id " .
						"LEFT JOIN ".self::VALUE_VERSION_TABLE."									ON ".self::VALUE_VERSION_TABLE.".toid 								= ".self::VALUE_TABLE.".id " .
						"LEFT JOIN ".self::VALUE_TYPE_TABLE." 										ON ".self::VALUE_TYPE_TABLE.".id 									= ".self::VALUE_TABLE.".type_id " .
						"LEFT JOIN ".self::SAMPLE_TABLE." 											ON ".self::SAMPLE_TABLE.".id 										= ".self::ITEM_IS_SAMPLE_TABLE.".sample_id " .
						"LEFT JOIN ".self::ITEM_HAS_ITEM_INFORMATION_TABLE."						ON ".self::ITEM_HAS_ITEM_INFORMATION_TABLE.".item_id 				= ".self::ITEM_TABLE.".id " .
						"LEFT JOIN ".self::ITEM_INFORMATION_TABLE." AS item_information_table_a		ON item_information_table_a.id										= ".self::ITEM_HAS_ITEM_INFORMATION_TABLE.".item_information_id " .
						"LEFT JOIN ".self::ITEM_HAS_ITEM_CLASS_TABLE."								ON ".self::ITEM_HAS_ITEM_CLASS_TABLE.".item_id 						= ".self::ITEM_TABLE.".id " .
						"LEFT JOIN ".self::ITEM_CLASS_HAS_ITEM_INFORMATION_TABLE."					ON ".self::ITEM_CLASS_HAS_ITEM_INFORMATION_TABLE.".item_class_id	= ".self::ITEM_HAS_ITEM_CLASS_TABLE.".item_class_id " .
						"LEFT JOIN ".self::ITEM_INFORMATION_TABLE." AS item_information_table_b 	ON item_information_table_b.id 										= ".self::ITEM_CLASS_HAS_ITEM_INFORMATION_TABLE.".item_information_id " .
							"".$sql_where."";

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
	 * @param bool $values
	 * @param bool $project_descriptions
	 * @param bool $sample_descriptions
	 * @param bool $items
	 * @param string $language_name
	 * @param string $search_string
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function search_fulltext($values, $project_descriptions, $sample_descriptions, $items, $language_name, $search_string, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if ($search_string and ($values or $project_descriptions or $sample_descriptions or $items))
		{
			if ($language_name == null)
			{
				$language_name_insert = "english";
			}
			else
			{
				$language_name_insert = $language_name;
			}
			
			
			$sql_where = "";
			
			if ($values == true and $project_descriptions == true)
			{
				$sql_where = " WHERE ".self::VALUE_VERSION_TABLE.".text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
			}
			else
			{
				if ($values == true)
				{
					$sql_where = " WHERE (".self::VALUE_VERSION_TABLE.".text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."') AND ".self::VALUE_TABLE.".type_id != 2)";
				}
				elseif($project_descriptions == true)
				{
					$sql_where = " WHERE (".self::VALUE_VERSION_TABLE.".text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."') AND ".self::VALUE_TABLE.".type_id = 2)";
				}
			}
			
			if ($sample_descriptions == true)
			{
				if ($sql_where)
				{
					$sql_where .= " OR ".self::SAMPLE_TABLE.".comment_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
				}
				else
				{
					$sql_where = " WHERE ".self::SAMPLE_TABLE.".comment_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
				}
			}
			
			if ($items == true)
			{
				if ($sql_where)
				{
					$sql_where .= " OR item_information_table_a.description_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')" .
									" OR item_information_table_a.keywords_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
				}
				else
				{
					$sql_where = " WHERE item_information_table_a.description_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')" .
									" OR item_information_table_a.keywords_text_search_vector @@ to_tsquery('".$language_name_insert."', '".$search_string."')";
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
				
					case "item_id_name":
						$sql_order_by = "ORDER BY item_id_name ".$sql_order_method;
					break;
				
					case "name":
						$sql_order_by = "ORDER BY name ".$sql_order_method;
					break;
					
					case "rank":
						$sql_order_by = "ORDER BY rank ".$sql_order_method;
					break;
				
					default:
						$sql_order_by = "ORDER BY rank ASC";
					break;
				
				endswitch;
				
			}
			else
			{
				$sql_order_by = "ORDER BY rank DESC";
			}
			
			$sql = "SELECT " .
							"CONCAT (" .
									"' ITEM-ID: ', " .
									"CAST(".self::ITEM_TABLE.".id AS text)" .
									") AS item_id_name, " .
							"CONCAT(" .
										"".self::VALUE_TYPE_TABLE.".name, " .
										"".self::SAMPLE_TABLE.".name " .		
									") AS name, " .
							"CONCAT(" .
									"CAST(ts_rank(".self::VALUE_VERSION_TABLE.".text_search_vector, to_tsquery('".$language_name_insert."', '".$search_string."')) AS text), " .
									"CONCAT(" .
											"CAST(ts_rank(".self::SAMPLE_TABLE.".comment_text_search_vector, to_tsquery('".$language_name_insert."', '".$search_string."')) AS text), " .
											"CAST(HIGHER(" .
													"ts_rank(item_information_table_a.description_text_search_vector, to_tsquery('".$language_name_insert."', '".$search_string."')), " .
													"ts_rank(item_information_table_a.keywords_text_search_vector, to_tsquery('".$language_name_insert."', '".$search_string."'))" .
													") AS text)" .
											")" .
									") AS rank, " .
							"CONCAT(" .
										"CAST(item_information_table_a.id AS text), " .
										"CAST(item_information_table_b.id AS text) " .
									") AS item_information_id, " .
							"".self::VALUE_TABLE.".id AS value_id, " .
							"".self::SAMPLE_TABLE.".id AS sample_id " .
						"FROM ".self::ITEM_TABLE." " .
						"LEFT JOIN ".self::ITEM_IS_OBJECT_TABLE." 									ON ".self::ITEM_IS_OBJECT_TABLE.".item_id 							= ".self::ITEM_TABLE.".id " .
						"LEFT JOIN ".self::ITEM_IS_SAMPLE_TABLE." 									ON ".self::ITEM_IS_SAMPLE_TABLE.".item_id 							= ".self::ITEM_TABLE.".id " .					
						"LEFT JOIN ".self::OBJECT_TABLE." 											ON ".self::OBJECT_TABLE.".id 										= ".self::ITEM_IS_OBJECT_TABLE.".object_id " .
						"LEFT JOIN ".self::VALUE_TABLE." 											ON ".self::VALUE_TABLE.".id 										= ".self::OBJECT_TABLE.".value_id " .
						"LEFT JOIN ".self::VALUE_VERSION_TABLE."									ON ".self::VALUE_VERSION_TABLE.".toid 								= ".self::VALUE_TABLE.".id " .
						"LEFT JOIN ".self::VALUE_TYPE_TABLE." 										ON ".self::VALUE_TYPE_TABLE.".id 									= ".self::VALUE_TABLE.".type_id " .
						"LEFT JOIN ".self::SAMPLE_TABLE." 											ON ".self::SAMPLE_TABLE.".id 										= ".self::ITEM_IS_SAMPLE_TABLE.".sample_id " .
						"LEFT JOIN ".self::ITEM_HAS_ITEM_INFORMATION_TABLE."						ON ".self::ITEM_HAS_ITEM_INFORMATION_TABLE.".item_id 				= ".self::ITEM_TABLE.".id " .
						"LEFT JOIN ".self::ITEM_INFORMATION_TABLE." AS item_information_table_a		ON item_information_table_a.id										= ".self::ITEM_HAS_ITEM_INFORMATION_TABLE.".item_information_id " .
						"LEFT JOIN ".self::ITEM_HAS_ITEM_CLASS_TABLE."								ON ".self::ITEM_HAS_ITEM_CLASS_TABLE.".item_id 						= ".self::ITEM_TABLE.".id " .
						"LEFT JOIN ".self::ITEM_CLASS_HAS_ITEM_INFORMATION_TABLE."					ON ".self::ITEM_CLASS_HAS_ITEM_INFORMATION_TABLE.".item_class_id	= ".self::ITEM_HAS_ITEM_CLASS_TABLE.".item_class_id " .
						"LEFT JOIN ".self::ITEM_INFORMATION_TABLE." AS item_information_table_b 	ON item_information_table_b.id 										= ".self::ITEM_CLASS_HAS_ITEM_INFORMATION_TABLE.".item_information_id " .
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
		else
		{
			return null;
		}
	}
	
}

?>
