<?php
/**
 * @package sample
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
 * Sample Wrapper Access Class
 * @package sample
 */
class Sample_Wrapper_Access
{
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */	
	public static function list_sample_templates($order_by, $order_method, $start, $end)
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
			
				default:
					$sql_order_by = "ORDER BY id ".$sql_order_method;
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY id";
		}
			
		$sql = "SELECT ".constant("SAMPLE_TEMPLATE_TABLE").".id AS id, " .
					"".constant("SAMPLE_TEMPLATE_TABLE").".name AS name," .
					"".constant("FILE_VERSION_TABLE").".name AS file, " .
					"".constant("SAMPLE_TEMPLATE_CAT_TABLE").".name AS category " .
					"FROM ".constant("SAMPLE_TEMPLATE_TABLE")." " .
					"LEFT JOIN ".constant("SAMPLE_TEMPLATE_CAT_TABLE")." 	ON ".constant("SAMPLE_TEMPLATE_TABLE").".cat_id 		= ".constant("SAMPLE_TEMPLATE_CAT_TABLE").".id " .
					"LEFT JOIN ".constant("OLDL_TEMPLATE_TABLE")." 			ON ".constant("SAMPLE_TEMPLATE_TABLE").".template_id 	= ".constant("OLDL_TEMPLATE_TABLE").".id " .
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
	public static function count_list_sample_templates()
	{
		global $db;
						
		$sql = "SELECT COUNT(".constant("SAMPLE_TEMPLATE_TABLE").".id) AS result " .
					"FROM ".constant("SAMPLE_TEMPLATE_TABLE")."";
		
		$res = $db->db_query($sql);
		$data = $db->fetch($res);

		return $data['result'];
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */	
	public static function list_sample_template_categories($order_by, $order_method, $start, $end)
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
			
		$sql = "SELECT ".constant("SAMPLE_TEMPLATE_CAT_TABLE").".id AS id, " .
					"".constant("SAMPLE_TEMPLATE_CAT_TABLE").".name AS name " .
					"FROM ".constant("SAMPLE_TEMPLATE_CAT_TABLE")." " .
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
	public static function count_list_sample_template_categories()
	{
		global $db;
						
		$sql = "SELECT COUNT(".constant("SAMPLE_TEMPLATE_CAT_TABLE").".id) AS result " .
					"FROM ".constant("SAMPLE_TEMPLATE_CAT_TABLE")."";
		
		$res = $db->db_query($sql);
		$data = $db->fetch($res);

		return $data['result'];
	}
	
	/**
	 * @param integer $user_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_user_samples($user_id, $order_by, $order_method, $start, $end)
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
				
					case "id":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id ".$sql_order_method;
					break;
						
					case "name":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime ".$sql_order_method;
					break;
					
					case "template":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TEMPLATE_TABLE").".name ".$sql_order_method;
					break;
					
					case "location":
						$sql_order_by = "ORDER BY ".constant("LOCATION_TABLE").".name ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id";
			}
			
			$sql = "SELECT ".constant("SAMPLE_TABLE").".id AS id, " .
						"".constant("SAMPLE_TABLE").".name AS name," .
						"".constant("SAMPLE_TABLE").".datetime AS datetime," .
						"".constant("SAMPLE_TEMPLATE_TABLE").".name AS template," .
						"NAMECONCAT(".constant("LOCATION_TYPE_TABLE").".name,".constant("LOCATION_TABLE").".name) AS location," .
						"".constant("SAMPLE_TABLE").".date_of_expiry AS date_of_expiry, " .
						"".constant("SAMPLE_TABLE").".expiry_warning AS expiry_warning, " .
						"".constant("SAMPLE_TABLE").".available AS av " .
						"FROM ".constant("SAMPLE_TABLE")." " .
						"JOIN ".constant("SAMPLE_TEMPLATE_TABLE")." 			ON ".constant("SAMPLE_TABLE").".template_id 				= ".constant("SAMPLE_TEMPLATE_TABLE").".id " .
						"LEFT JOIN ".constant("SAMPLE_HAS_LOCATION_TABLE")." 	ON ".constant("SAMPLE_TABLE").".id 							= ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id " .
						"LEFT JOIN ".constant("LOCATION_TABLE")." 				ON ".constant("SAMPLE_HAS_LOCATION_TABLE").".location_id 	= ".constant("LOCATION_TABLE").".id " .
						"LEFT JOIN ".constant("LOCATION_TYPE_TABLE")." 			ON ".constant("LOCATION_TABLE").".type_id 					= ".constant("LOCATION_TYPE_TABLE").".id " .
						"WHERE " .
							"(".constant("SAMPLE_TABLE").".id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_USER_TABLE")." WHERE ".constant("SAMPLE_HAS_USER_TABLE").".write = 't' AND ".constant("SAMPLE_HAS_USER_TABLE").".user_id = ".$user_id.") " .
							"OR ".constant("SAMPLE_TABLE").".owner_id = ".$user_id.") " .
							"AND " .
								"(".constant("SAMPLE_HAS_LOCATION_TABLE").".primary_key IN " .
									"( " .
									"SELECT primary_key " .
									"FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." " .
									"WHERE sample_id = ".constant("SAMPLE_TABLE").".id AND " .
									"".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime = " .
										"(SELECT MAX(".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime) FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." WHERE ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id = ".constant("SAMPLE_TABLE").".id) " .
									") " .
								"OR ".constant("SAMPLE_TABLE").".id NOT IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_LOCATION_TABLE").") " .
								") " .
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
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $user_id
	 * @return integer
	 */
	public static function count_user_samples($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{	
			$sql = "SELECT COUNT(DISTINCT ".constant("SAMPLE_TABLE").".id) AS result " .
						"FROM ".constant("SAMPLE_TABLE")." " .
						"LEFT JOIN ".constant("SAMPLE_HAS_USER_TABLE")." ON ".constant("SAMPLE_TABLE").".id = ".constant("SAMPLE_HAS_USER_TABLE").".sample_id " .
						"WHERE ".constant("SAMPLE_TABLE").".id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_USER_TABLE")." WHERE ".constant("SAMPLE_HAS_USER_TABLE").".write = 't' AND ".constant("SAMPLE_HAS_USER_TABLE").".user_id = ".$user_id.") " .
							"OR owner_id = ".$user_id."";
			
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
	
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_item_samples($item_sql, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if ($item_sql)
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
				
					case "id":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id ".$sql_order_method;
					break;
						
					case "name":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime ".$sql_order_method;
					break;
					
					case "template":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TEMPLATE_TABLE").".name ".$sql_order_method;
					break;
					
					case "location":
						$sql_order_by = "ORDER BY ".constant("LOCATION_TABLE").".name ".$sql_order_method;
					break;
					
					case "owner":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime";
			}
				
			$sql = "SELECT ".constant("SAMPLE_TABLE").".id AS id, " .
						"".constant("SAMPLE_TABLE").".name AS name," .
						"".constant("SAMPLE_TABLE").".datetime AS datetime," .
						"".constant("SAMPLE_TEMPLATE_TABLE").".name AS template, " .
						"NAMECONCAT(".constant("LOCATION_TYPE_TABLE").".name,".constant("LOCATION_TABLE").".name) AS location," .
						"".constant("SAMPLE_TABLE").".date_of_expiry AS date_of_expiry, " .
						"".constant("SAMPLE_TABLE").".expiry_warning AS expiry_warning, " .
						"".constant("SAMPLE_TABLE").".available AS av, " .
						"".constant("SAMPLE_TABLE").".owner_id AS owner, " .
						"".constant("SAMPLE_IS_ITEM_TABLE").".item_id AS item_id " .
						"FROM ".constant("SAMPLE_TABLE")." " .
						"LEFT JOIN ".constant("SAMPLE_IS_ITEM_TABLE")." 		ON ".constant("SAMPLE_TABLE").".id 							= ".constant("SAMPLE_IS_ITEM_TABLE").".sample_id " .
						"LEFT JOIN ".constant("SAMPLE_TEMPLATE_TABLE")." 		ON ".constant("SAMPLE_TABLE").".template_id 				= ".constant("SAMPLE_TEMPLATE_TABLE").".id " .
						"LEFT JOIN ".constant("SAMPLE_HAS_LOCATION_TABLE")." 	ON ".constant("SAMPLE_TABLE").".id 							= ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id " .
						"LEFT JOIN ".constant("LOCATION_TABLE")." 				ON ".constant("SAMPLE_HAS_LOCATION_TABLE").".location_id 	= ".constant("LOCATION_TABLE").".id " .
						"LEFT JOIN ".constant("LOCATION_TYPE_TABLE")." 			ON ".constant("LOCATION_TABLE").".type_id 					= ".constant("LOCATION_TYPE_TABLE").".id " .
						"LEFT JOIN ".constant("USER_PROFILE_TABLE")." 			ON ".constant("SAMPLE_TABLE").".owner_id					= ".constant("USER_PROFILE_TABLE").".id " .
						"WHERE ".constant("SAMPLE_IS_ITEM_TABLE").".item_id IN (".$item_sql.") " .
						"AND (".constant("SAMPLE_HAS_LOCATION_TABLE").".primary_key IN " .
									"( " .
									"SELECT primary_key " .
									"FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." " .
									"WHERE sample_id = ".constant("SAMPLE_TABLE").".id AND " .
									"".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime = " .
										"(SELECT MAX(".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime) FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." WHERE ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id = ".constant("SAMPLE_TABLE").".id) " .
									") " .
								"OR ".constant("SAMPLE_TABLE").".id NOT IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_LOCATION_TABLE").") " .
								") " .
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
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $item_sql
	 * @return integer
	 */
	public static function count_item_samples($item_sql)
	{
		global $db;
		
		if ($item_sql)
		{	
			$sql = "SELECT COUNT (".constant("SAMPLE_IS_ITEM_TABLE").".sample_id) AS result " .
						"FROM ".constant("SAMPLE_IS_ITEM_TABLE")." " .
						"WHERE ".constant("SAMPLE_IS_ITEM_TABLE").".item_id IN (".$item_sql.") " .
						"".$sql_order_by."";
			
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
	
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_samples_by_item_id($item_id, $order_by, $order_method, $start, $end)
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
				
					case "id":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id ".$sql_order_method;
					break;
						
					case "name":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime ".$sql_order_method;
					break;
					
					case "template":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TEMPLATE_TABLE").".name ".$sql_order_method;
					break;
					
					case "location":
						$sql_order_by = "ORDER BY ".constant("LOCATION_TABLE").".name ".$sql_order_method;
					break;
					
					case "owner":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime";
			}
				
			$sql = "SELECT ".constant("SAMPLE_TABLE").".id AS id, " .
						"".constant("SAMPLE_TABLE").".name AS name," .
						"".constant("SAMPLE_TABLE").".datetime AS datetime," .
						"".constant("SAMPLE_TEMPLATE_TABLE").".name AS template, " .
						"NAMECONCAT(".constant("LOCATION_TYPE_TABLE").".name,".constant("LOCATION_TABLE").".name) AS location," .
						"".constant("SAMPLE_TABLE").".date_of_expiry AS date_of_expiry, " .
						"".constant("SAMPLE_TABLE").".expiry_warning AS expiry_warning, " .
						"".constant("SAMPLE_TABLE").".available AS av, " .
						"".constant("SAMPLE_TABLE").".owner_id AS owner " .
						"FROM ".constant("SAMPLE_TABLE")." " .
						"LEFT JOIN ".constant("SAMPLE_HAS_ITEM_TABLE")." 		ON ".constant("SAMPLE_TABLE").".id 							= ".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id " .
						"LEFT JOIN ".constant("SAMPLE_TEMPLATE_TABLE")." 		ON ".constant("SAMPLE_TABLE").".template_id 				= ".constant("SAMPLE_TEMPLATE_TABLE").".id " .
						"LEFT JOIN ".constant("SAMPLE_HAS_LOCATION_TABLE")." 	ON ".constant("SAMPLE_TABLE").".id 							= ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id " .
						"LEFT JOIN ".constant("LOCATION_TABLE")." 				ON ".constant("SAMPLE_HAS_LOCATION_TABLE").".location_id 	= ".constant("LOCATION_TABLE").".id " .
						"LEFT JOIN ".constant("LOCATION_TYPE_TABLE")." 			ON ".constant("LOCATION_TABLE").".type_id 					= ".constant("LOCATION_TYPE_TABLE").".id " .
						"LEFT JOIN ".constant("USER_PROFILE_TABLE")." 			ON ".constant("SAMPLE_TABLE").".owner_id					= ".constant("USER_PROFILE_TABLE").".id " .
						"WHERE ".constant("SAMPLE_HAS_ITEM_TABLE").".item_id = ".$item_id." " .
						"AND (".constant("SAMPLE_HAS_LOCATION_TABLE").".primary_key IN " .
									"( " .
									"SELECT primary_key " .
									"FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." " .
									"WHERE sample_id = ".constant("SAMPLE_TABLE").".id AND " .
									"".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime = " .
										"(SELECT MAX(".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime) FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." WHERE ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id = ".constant("SAMPLE_TABLE").".id) " .
									") " .
								"OR ".constant("SAMPLE_TABLE").".id NOT IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_LOCATION_TABLE").") " .
								") " .
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
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $item_sql
	 * @return integer
	 */
	public static function count_samples_by_item_id($item_id)
	{
		global $db;
		
		if (is_numeric($item_id))
		{	
			$sql = "SELECT COUNT (".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id) AS result " .
						"FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." " .
						"WHERE ".constant("SAMPLE_HAS_ITEM_TABLE").".item_id = ".$item_id." " .
						"".$sql_order_by."";
			
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
	
			return $data['result'];
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
	public static function list_organisation_unit_samples($organisation_unit_id, $order_by, $order_method, $start, $end)
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
				
					case "id":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id ".$sql_order_method;
					break;
						
					case "name":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime ".$sql_order_method;
					break;
					
					case "template":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TEMPLATE_TABLE").".name ".$sql_order_method;
					break;
					
					case "location":
						$sql_order_by = "ORDER BY ".constant("LOCATION_TABLE").".name ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id";
			}
			
			$sql = "SELECT ".constant("SAMPLE_TABLE").".id AS id, " .
						"".constant("SAMPLE_TABLE").".name AS name," .
						"".constant("SAMPLE_TABLE").".datetime AS datetime," .
						"".constant("SAMPLE_TEMPLATE_TABLE").".name AS template," .
						"NAMECONCAT(".constant("LOCATION_TYPE_TABLE").".name,".constant("LOCATION_TABLE").".name) AS location," .
						"".constant("SAMPLE_TABLE").".date_of_expiry AS date_of_expiry, " .
						"".constant("SAMPLE_TABLE").".expiry_warning AS expiry_warning, " .
						"".constant("SAMPLE_TABLE").".available AS av " .
						"FROM ".constant("SAMPLE_TABLE")." " .
						"JOIN ".constant("SAMPLE_TEMPLATE_TABLE")." 			ON ".constant("SAMPLE_TABLE").".template_id 				= ".constant("SAMPLE_TEMPLATE_TABLE").".id " .
						"LEFT JOIN ".constant("SAMPLE_HAS_LOCATION_TABLE")." 	ON ".constant("SAMPLE_TABLE").".id 							= ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id " .
						"LEFT JOIN ".constant("LOCATION_TABLE")." 				ON ".constant("SAMPLE_HAS_LOCATION_TABLE").".location_id 	= ".constant("LOCATION_TABLE").".id " .
						"LEFT JOIN ".constant("LOCATION_TYPE_TABLE")." 			ON ".constant("LOCATION_TABLE").".type_id 					= ".constant("LOCATION_TYPE_TABLE").".id " .
						"WHERE " .
							"".constant("SAMPLE_TABLE").".id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE").".organisation_unit_id = ".$organisation_unit_id.") " .
							"AND " .
								"(".constant("SAMPLE_HAS_LOCATION_TABLE").".primary_key IN " .
									"( " .
									"SELECT primary_key " .
									"FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." " .
									"WHERE sample_id = ".constant("SAMPLE_TABLE").".id AND " .
									"".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime = " .
										"(SELECT MAX(".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime) FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." WHERE ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id = ".constant("SAMPLE_TABLE").".id) " .
									") " .
								"OR ".constant("SAMPLE_TABLE").".id NOT IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_LOCATION_TABLE").") " .
								") " .
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
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_samples($organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{	
			$sql = "SELECT COUNT(".constant("SAMPLE_TABLE").".id) AS result " .
						"FROM ".constant("SAMPLE_TABLE")." " .
						"LEFT JOIN ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." ON ".constant("SAMPLE_TABLE").".id = ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE").".sample_id " .
						"WHERE organisation_unit_id = ".$organisation_unit_id." ";
			
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
	
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_locations($sample_id, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($sample_id))
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
						$sql_order_by = "ORDER BY ".constant("LOCATION_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime ".$sql_order_method;
					break;
					
					case "user":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime";
			}
			
			$sql = "SELECT ".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime AS datetime," .
						"NAMECONCAT(".constant("LOCATION_TYPE_TABLE").".name,".constant("LOCATION_TABLE").".name) AS name," .
						"".constant("SAMPLE_HAS_LOCATION_TABLE").".user_id AS user " .
						"FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." " .
						"JOIN ".constant("USER_PROFILE_TABLE")." 		ON ".constant("SAMPLE_HAS_LOCATION_TABLE").".user_id 		= ".constant("USER_PROFILE_TABLE").".id " .
						"LEFT JOIN ".constant("LOCATION_TABLE")." 		ON ".constant("SAMPLE_HAS_LOCATION_TABLE").".location_id 	= ".constant("LOCATION_TABLE").".id " .
						"LEFT JOIN ".constant("LOCATION_TYPE_TABLE")." 	ON ".constant("LOCATION_TABLE").".type_id 					= ".constant("LOCATION_TYPE_TABLE").".id " .
						"WHERE " .
							"".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id = ".$sample_id." " .
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
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_locations($sample_id)
	{
		global $db;
		
		if (is_numeric($sample_id))
		{	
			$sql = "SELECT COUNT(primary_key) AS result " .
						"FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." " .
						"WHERE sample_id = ".$sample_id." ";
			
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
	
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_users($sample_id, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($sample_id))
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
					
					case "name":
						$sql_order_by = "ORDER BY ".constant("USER_PROFILE_TABLE").".surname ".$sql_order_method;
					break;
					
					case "read":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_USER_TABLE").".read ".$sql_order_method;
					break;
					
					case "write":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_USER_TABLE").".write ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_USER_TABLE").".primary_key ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_USER_TABLE").".primary_key";
			}
			
			$sql = "SELECT ".constant("SAMPLE_HAS_USER_TABLE").".read AS read," .
						"".constant("SAMPLE_HAS_USER_TABLE").".write AS write," .
						"".constant("USER_TABLE").".id AS user " .
						"FROM ".constant("SAMPLE_HAS_USER_TABLE")." " .
						"JOIN ".constant("USER_PROFILE_TABLE")." 	ON ".constant("SAMPLE_HAS_USER_TABLE").".user_id 	= ".constant("USER_PROFILE_TABLE").".id " .
						"JOIN ".constant("USER_TABLE")." 			ON ".constant("SAMPLE_HAS_USER_TABLE").".user_id 	= ".constant("USER_TABLE").".id " .
						"WHERE " .
							"".constant("SAMPLE_HAS_USER_TABLE").".sample_id = ".$sample_id." " .
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
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_users($sample_id)
	{
		global $db;
		
		if (is_numeric($sample_id))
		{	
			$sql = "SELECT COUNT(primary_key) AS result " .
						"FROM ".constant("SAMPLE_HAS_USER_TABLE")." " .
						"WHERE sample_id = ".$sample_id." ";
			
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
	
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_organisation_units($sample_id, $order_by, $order_method, $start, $end)
	{
		global $db;
		
		if (is_numeric($sample_id))
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
						$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE").".primary_key ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE").".primary_key";
			}
			
			$sql = "SELECT ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE").".organisation_unit_id AS organisation_unit_id " .
						"FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." " .
						"JOIN ".constant("ORGANISATION_UNIT_TABLE")." ON ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE").".organisation_unit_id = ".constant("ORGANISATION_UNIT_TABLE").".id " .
						"WHERE " .
							"".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE").".sample_id = ".$sample_id." " .
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
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_organisation_units($sample_id)
	{
		global $db;
		
		if (is_numeric($sample_id))
		{	
			$sql = "SELECT COUNT(primary_key) AS result " .
						"FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." " .
						"WHERE sample_id = ".$sample_id." ";
			
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
	
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $name
	 * @param array $organisation_unit_array
	 * @param array $template_array
	 * @param bool $in_id
	 * @param bool $in_name
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, $order_by, $order_method, $start, $end)
	{
		global $db;
   		
   		if (($name or $id or (is_array($template_array) and count($template_array) >= 1)) and 
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
				
					case "id":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id ".$sql_order_method;
					break;
						
					case "name":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".name ".$sql_order_method;
					break;
					
					case "datetime":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".datetime ".$sql_order_method;
					break;
					
					case "template":
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TEMPLATE_TABLE").".name ".$sql_order_method;
					break;
					
					case "location":
						$sql_order_by = "ORDER BY ".constant("LOCATION_TABLE").".name ".$sql_order_method;
					break;
					
					default:
						$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY ".constant("SAMPLE_TABLE").".id";
			}
   			
   			$name = strtolower(trim($name));
   			$id = strtolower(trim($id));
   			
   			$base_sql = "SELECT ".constant('SAMPLE_TABLE').".id AS id, ".
   								"".constant('SAMPLE_TABLE').".name AS name, " .
   								"".constant("SAMPLE_TABLE").".datetime AS datetime," .
								"".constant("SAMPLE_TABLE").".owner_id AS owner," .
   								"".constant("SAMPLE_TEMPLATE_TABLE").".name AS template," .
								"NAMECONCAT(".constant("LOCATION_TYPE_TABLE").".name,".constant("LOCATION_TABLE").".name) AS location," .
								"".constant("SAMPLE_TABLE").".date_of_expiry AS date_of_expiry, " .
   								"".constant("SAMPLE_TABLE").".expiry_warning AS expiry_warning, " .
								"".constant("SAMPLE_TABLE").".available AS av " .
   								" FROM ".constant('SAMPLE_TABLE')." " .
   								"JOIN ".constant("SAMPLE_TEMPLATE_TABLE")." 			ON ".constant("SAMPLE_TABLE").".template_id 				= ".constant("SAMPLE_TEMPLATE_TABLE").".id " .
								"LEFT JOIN ".constant("SAMPLE_HAS_LOCATION_TABLE")." 	ON ".constant("SAMPLE_TABLE").".id 							= ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id " .
								"LEFT JOIN ".constant("LOCATION_TABLE")." 				ON ".constant("SAMPLE_HAS_LOCATION_TABLE").".location_id 	= ".constant("LOCATION_TABLE").".id " .
   								"LEFT JOIN ".constant("LOCATION_TYPE_TABLE")." 			ON ".constant("LOCATION_TABLE").".type_id 					= ".constant("LOCATION_TYPE_TABLE").".id " .
								"LEFT JOIN ".constant("USER_PROFILE_TABLE")." 			ON ".constant("SAMPLE_TABLE").".owner_id					= ".constant("USER_PROFILE_TABLE").".id " .
								"WHERE (";
   			
   			if ($id)
   			{
   				$id = str_replace("*","%",$id);
   				$id_string = "";
   				$id_length = strlen($id);
   				for($i=0;$i<=($id_length-1);$i++)
   				{
   					if (is_numeric($id{$i}))
   					{
   						$id_string .= $id{$i};
   					}
   				}
   				
   				$id_string = (int)$id_string;
   				if ($id_string)
   				{
   					$add_sql = " CAST(".constant('SAMPLE_TABLE').".id AS TEXT) LIKE '".$id_string."'";
   				}
   			}
   			else
   			{
   				$add_sql .= "";
   			}
   			
   			if ($name)
   			{
   				$name = str_replace("*","%",$name);
   				if ($add_sql)
   				{
					$add_sql .= " OR LOWER(".constant('SAMPLE_TABLE').".name) LIKE '".$name."'";
				}
				else
				{
					$add_sql = " LOWER(".constant('SAMPLE_TABLE').".name) LIKE '".$name."'";
				}
   			}
   			else
   			{
   				$add_sql .= "";
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
   						$template_sql .= " OR ".constant('SAMPLE_TABLE').".template_id = '".$value."'";
   					}
   					else
   					{
   						$template_sql .= "".constant('SAMPLE_TABLE').".template_id = '".$value."'";
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
					$organisation_unit_sql .= " OR ".constant('SAMPLE_TABLE').".id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE organisation_unit_id = ".$value.")";
				}
				else
				{
					$organisation_unit_sql .= "".constant('SAMPLE_TABLE').".id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE organisation_unit_id = ".$value.")";
				}
			}
			
			$add_sql .= $organisation_unit_sql.")";
			
			
   			$sql = $base_sql."".$add_sql.") AND " .
   								"(".constant("SAMPLE_HAS_LOCATION_TABLE").".primary_key IN " .
									"( " .
									"SELECT primary_key " .
									"FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." " .
									"WHERE sample_id = ".constant("SAMPLE_TABLE").".id AND " .
									"".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime = " .
										"(SELECT MAX(".constant("SAMPLE_HAS_LOCATION_TABLE").".datetime) FROM ".constant("SAMPLE_HAS_LOCATION_TABLE")." WHERE ".constant("SAMPLE_HAS_LOCATION_TABLE").".sample_id = ".constant("SAMPLE_TABLE").".id) " .
									") " .
								"OR ".constant("SAMPLE_TABLE").".id NOT IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_LOCATION_TABLE").") " .
								") " .
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
   		else
   		{
   			return null;
   		}
	}
	
	/**
	 * @param string $name
	 * @param array $organisation_unit_array
	 * @param array $template_array
	 * @param bool $in_id
	 * @param bool $in_name
	 * @return integer
	 */
	public static function count_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name)
	{
		global $db;
   		
   		if (($name or $id or (is_array($template_array) and count($template_array) >= 1)) and 
   			(is_array($organisation_unit_array) and count($organisation_unit_array) >= 1))
   		{	
   			$name = strtolower(trim($name));
   			$id = strtolower(trim($id));
   			
   			$base_sql = "SELECT COUNT(".constant('SAMPLE_TABLE').".id) AS result ".
   								" FROM ".constant('SAMPLE_TABLE')." " .
								"WHERE";
   			
   			if ($id)
   			{
   				$id = str_replace("*","%",$id);
   				$id_string = "";
   				$id_length = strlen($id);
   				for($i=0;$i<=($id_length-1);$i++)
   				{
   					if (is_numeric($id{$i}))
   					{
   						$id_string .= $id{$i};
   					}
   				}
   				
   				$id_string = (int)$id_string;
   				if ($id_string)
   				{
   					$add_sql = " CAST(".constant('SAMPLE_TABLE').".id AS TEXT) LIKE '".$id_string."'";
   				}
   			}
   			else
   			{
   				$add_sql .= "";
   			}
   			
   			if ($name)
   			{
   				$name = str_replace("*","%",$name);
   				if ($add_sql)
   				{
					$add_sql .= " OR LOWER(".constant('SAMPLE_TABLE').".name) LIKE '".$name."'";
				}
				else
				{
					$add_sql = " LOWER(".constant('SAMPLE_TABLE').".name) LIKE '".$name."'";
				}
   			}
   			else
   			{
   				$add_sql .= "";
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
   						$template_sql .= " OR ".constant('SAMPLE_TABLE').".template_id = '".$value."'";
   					}
   					else
   					{
   						$template_sql .= "".constant('SAMPLE_TABLE').".template_id = '".$value."'";
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
					$organisation_unit_sql .= " OR ".constant('SAMPLE_TABLE').".id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE organisation_unit_id = ".$value.")";
				}
				else
				{
					$organisation_unit_sql .= "".constant('SAMPLE_TABLE').".id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE organisation_unit_id = ".$value.")";
				}
			}
			
			$add_sql .= $organisation_unit_sql.")";
			
			
   			$sql = $base_sql."".$add_sql."";
   			   			
   			$res = $db->db_query($sql);
   			$data = $db->fetch($res);
	
			return $data['result'];
   		}
   		else
   		{
   			return null;
   		}
	}
	
	/**
	 * @param string $string
	 * @param array �sample_id_array
	 * @param array $item_select_sql_array
	 * @param string $item_join_sql
	 * @param string $item_where_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
   	 */
   	public static function list_data_search($string, $sample_id_array, $item_select_sql_array, $item_join_sql, $item_where_sql, $order_by, $order_method, $start, $end)
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
   					$name_select_sql 		.= "CONCAT(".$item_select_sql_array[$i]['name'].",";
   					$datetime_select_sql 	.= "CONCAT(CAST(".$item_select_sql_array[$i]['datetime']." AS TEXT),";
   				}
   				
   				$name_select_sql 		.= $item_select_sql_array[($item_select_sql_array_length-1)]['name'];
   				$datetime_select_sql	.= "CAST(".$item_select_sql_array[($item_select_sql_array_length-1)]['datetime']." AS TEXT)";
   				
   				for ($i=0;$i<=($item_select_sql_array_length-2);$i++)
   				{
   					$name_select_sql 		.= ")";
   					$datetime_select_sql 	.= ")";
   				}
   				
   				for ($i=0;$i<=($item_select_sql_array_length-1);$i++)
   				{
   					if ($type_select_sql == "")
   					{
   						$type_select_sql .= $item_select_sql_array[$i]['type_id'];
   					}
   					else
   					{
   						$type_select_sql .= ",".$item_select_sql_array[$i]['type_id'];
   					}
   					
   				}
   			}
   			elseif (count($item_select_sql_array) == 1)
   			{
   				$name_select_sql = $item_select_sql_array[0]['name'];
   				$type_select_sql = $item_select_sql_array[0]['type_id'];
   				$datetime_select_sql = $item_select_sql_array[0]['datetime'];
   			}
   		}

   		if (is_array($sample_id_array) and count($sample_id_array) >= 1)
   		{
   			$sample_where_sql = "";
   			
   			foreach ($sample_id_array as $key => $value)
   			{
   				if ($sample_where_sql == "")
   				{
   					$sample_where_sql .= "".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id = ".$value."";
   				}
   				else
   				{
   					$sample_where_sql .= " OR ".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id = ".$value."";
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
						$sql_order_by = "ORDER BY holder_sample_id ".$sql_order_method.", name";
					break;
					
					default:
						$sql_order_by = "ORDER BY name ".$sql_order_method;
					break;
				
				endswitch;
			}
			else
			{
				$sql_order_by = "ORDER BY sample_table.id, name";
			}
	   		
	   		$sql = "SELECT ".constant("SAMPLE_HAS_ITEM_TABLE").".item_id AS id, " .
	   							"".$name_select_sql." AS name, ".
	   							"".$type_select_sql.", ".
	   							"".$datetime_select_sql." AS datetime, ".
	   							"sample_table.id AS holder_sample_id, " .
	   							"sample_table.name AS holder_sample_name " .
								"FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." " .
								"JOIN ".constant("ITEM_TABLE")." 					ON ".constant("SAMPLE_HAS_ITEM_TABLE").".item_id 	= ".constant("ITEM_TABLE").".id " .
	   							"JOIN ".constant("SAMPLE_TABLE")." AS sample_table 	ON ".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id 	= sample_table.id " .
	   							"".$item_join_sql." " .
	   							"WHERE (".$item_where_sql.") AND (".$sample_where_sql.") AND " . 
	   							"(sample_table.id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_USER_TABLE")." WHERE (".constant("SAMPLE_HAS_USER_TABLE").".write = 't' OR ".constant("SAMPLE_HAS_USER_TABLE").".read = 't') AND ".constant("SAMPLE_HAS_USER_TABLE").".user_id = ".$user->get_user_id().") " .
									"OR sample_table.owner_id = ".$user->get_user_id().") " .
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
   		else
   		{
   			return null;
   		}
   	}
	
   	/**
	 * @param string $string
	 * @param array �sample_id_array
	 * @param array $item_select_sql_array
	 * @param string $item_join_sql
	 * @param string $item_where_sql
	 * @return integer
   	 */
   	public static function count_data_search($string, $sample_id_array, $item_select_sql_array, $item_join_sql, $item_where_sql)
   	{
   		global $db, $user;

   		if (is_array($sample_id_array) and count($sample_id_array) >= 1)
   		{
   			$sample_where_sql = "";
   			
   			foreach ($sample_id_array as $key => $value)
   			{
   				if ($sample_where_sql == "")
   				{
   					$sample_where_sql .= "".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id = ".$value."";
   				}
   				else
   				{
   					$sample_where_sql .= " OR ".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id = ".$value."";
   				}
   			}
   	
	   		$sql = "SELECT COUNT(".constant("SAMPLE_HAS_ITEM_TABLE").".item_id) AS result " .
								"FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." " .
								"JOIN ".constant("ITEM_TABLE")." 	ON ".constant("SAMPLE_HAS_ITEM_TABLE").".item_id 		= ".constant("ITEM_TABLE").".id " .
	   							"JOIN ".constant("SAMPLE_TABLE")." AS sample_table 	ON ".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id 	= sample_table.id " .
								"".$item_join_sql."" .
	   							"WHERE (".$item_where_sql.") AND (".$sample_where_sql.") AND " .
	   							"(sample_table.id IN (SELECT sample_id FROM ".constant("SAMPLE_HAS_USER_TABLE")." WHERE (".constant("SAMPLE_HAS_USER_TABLE").".write = 't' OR ".constant("SAMPLE_HAS_USER_TABLE").".read = 't') AND ".constant("SAMPLE_HAS_USER_TABLE").".user_id = ".$user->get_user_id().") " .
									"OR sample_table.owner_id = ".$user->get_user_id().") " .
	   							"".$sql_order_by."";
	   		
	   		$res = $db->db_query($sql);
	   		$data = $db->fetch($res);
		
			return $data['result'];
   		}
   		else
   		{
   			return null;
   		}
   	}
   	
   	/**
	 * @param integer $sample_id
	 * @return array
	 */
   	public static function get_sample_id_and_gid_by_parent_sample_id($sample_id)
   	{
   		global $db;

   		if (is_numeric($sample_id))
   		{	
   			$return_array = array();
   			
	   		$sql = "SELECT ".constant("SAMPLE_IS_ITEM_TABLE").".sample_id AS id, ".constant("SAMPLE_HAS_ITEM_TABLE").".gid AS pos_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." " .
	   				"JOIN ".constant("SAMPLE_IS_ITEM_TABLE")." ON ".constant("SAMPLE_HAS_ITEM_TABLE").".item_id = ".constant("SAMPLE_IS_ITEM_TABLE").".item_id " .
	   				"WHERE ".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id = ".$sample_id." AND ".constant("SAMPLE_HAS_ITEM_TABLE").".parent = 't'";
	   		
	   		$res = $db->db_query($sql);
   			
	   		while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		
			return $return_array;
   		}
   		else
   		{
   			return null;
   		}
   	}
   	
	/**
   	 * @param integer $parent_item_id
   	 * @param integer $sample_id
   	 * @return bool
   	 */
	public static function delete_data_entity_sub_item_links($parent_item_id, $sample_id = null)
	{
		global $db;
		
		if (is_numeric($parent_item_id))
		{
			if (is_numeric($sample_id))
			{
				$sql = "DELETE FROM ".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE")." " .
						"WHERE " .
							"( ".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE").".data_entity_pid IN (SELECT * FROM search_get_sub_folders((SELECT data_entity_id FROM ".constant("FOLDER_TABLE")." WHERE id = (SELECT folder_id FROM ".constant("SAMPLE_HAS_FOLDER_TABLE")." WHERE sample_id = ".$sample_id."))::INT)) " .
							" OR ".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE").".data_entity_pid = (SELECT data_entity_id FROM ".constant("FOLDER_TABLE")." WHERE id = (SELECT folder_id FROM ".constant("SAMPLE_HAS_FOLDER_TABLE")." WHERE sample_id = ".$sample_id.")) )" .
						" AND ".
						"".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE").".data_entity_cid IN (SELECT data_entity_id FROM ".constant("DATA_ENTITY_IS_ITEM_TABLE")." WHERE item_id IN (SELECT item_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE parent_item_id = ".$parent_item_id." AND sample_id = ".$sample_id.")) " .
						" AND " .
						"".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE").".link_item_id IS NOT NULL";
			}
			else
			{
				$sql = "DELETE FROM ".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE")." " .
						"WHERE " .
							"( ".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE").".data_entity_pid IN (SELECT * FROM search_get_sub_folders((SELECT data_entity_id FROM ".constant("FOLDER_TABLE")." WHERE id = ".constant("SAMPLE_FOLDER_ID").")::INT)) " .
							" OR ".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE").".data_entity_pid = (SELECT data_entity_id FROM ".constant("FOLDER_TABLE")." WHERE id = ".constant("SAMPLE_FOLDER_ID").") )" .
						" AND ".
						"".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE").".data_entity_cid IN (SELECT data_entity_id FROM ".constant("DATA_ENTITY_IS_ITEM_TABLE")." WHERE item_id IN (SELECT item_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE parent_item_id = ".$parent_item_id.")) " .
						" AND " .
						"".constant("DATA_ENTITY_HAS_DATA_ENTITY_TABLE").".link_item_id IS NOT NULL";
			}
			
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
}

?>