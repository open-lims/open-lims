<?php
/**
 * @package sample
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
 * Sample Wrapper Access Class
 * @package sample
 */
class Sample_Wrapper_Access
{
	const SAMPLE_TABLE = 'core_samples';
	const SAMPLE_TEMPLATE_TABLE = 'core_sample_templates';
	const SAMPLE_TEMPLATE_CAT_TABLE = 'core_sample_template_cats';
	const SAMPLE_HAS_USER_TABLE = 'core_sample_has_users';
	
	const OLDL_TEMPLATE_TABLE = 'core_oldl_templates';
	
	const DATA_ENTITY_TABLE = 'core_data_entities';
	const FILE_TABLE = 'core_files';
	const FILE_VERSION_TABLE = 'core_file_versions';
	
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
			
		$sql = "SELECT ".self::SAMPLE_TEMPLATE_TABLE.".id AS id, " .
					"".self::SAMPLE_TEMPLATE_TABLE.".name AS name," .
					"".self::FILE_VERSION_TABLE.".name AS file, " .
					"".self::SAMPLE_TEMPLATE_CAT_TABLE.".name AS category " .
					"FROM ".self::SAMPLE_TEMPLATE_TABLE." " .
					"LEFT JOIN ".self::SAMPLE_TEMPLATE_CAT_TABLE." 	ON ".self::SAMPLE_TEMPLATE_TABLE.".cat_id 			= ".self::SAMPLE_TEMPLATE_CAT_TABLE.".id " .
					"LEFT JOIN ".self::OLDL_TEMPLATE_TABLE." 		ON ".self::SAMPLE_TEMPLATE_TABLE.".template_id 		= ".self::OLDL_TEMPLATE_TABLE.".id " .
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
	public static function count_list_sample_templates()
	{
		global $db;
						
		$sql = "SELECT COUNT(".self::SAMPLE_TEMPLATE_TABLE.".id) AS result " .
					"FROM ".self::SAMPLE_TEMPLATE_TABLE."";
		
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
			
		$sql = "SELECT ".self::SAMPLE_TEMPLATE_CAT_TABLE.".id AS id, " .
					"".self::SAMPLE_TEMPLATE_CAT_TABLE.".name AS name " .
					"FROM ".self::SAMPLE_TEMPLATE_CAT_TABLE." " .
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
	public static function count_list_sample_template_categories()
	{
		global $db;
						
		$sql = "SELECT COUNT(".self::SAMPLE_TEMPLATE_CAT_TABLE.".id) AS result " .
					"FROM ".self::SAMPLE_TEMPLATE_CAT_TABLE."";
		
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);

		return $data[result];
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
			$sql = "SELECT COUNT(".self::SAMPLE_TABLE.".id) AS result " .
						"FROM ".self::SAMPLE_TABLE." " .
						"LEFT JOIN ".self::SAMPLE_HAS_USER_TABLE." ON ".self::SAMPLE_TABLE.".id = ".self::SAMPLE_HAS_USER_TABLE.".sample_id " .
						"WHERE (".self::SAMPLE_HAS_USER_TABLE.".read = 't' AND user_id = ".$user_id.") " .
							"OR owner_id = ".$user_id."";
			
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