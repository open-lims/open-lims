<?php
/**
 * @package item
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * 
 */
require_once("interfaces/item.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/item.wrapper.access.php");
}

/**
 * Item Wrapper Class for complex sample-joins
 * @package item
 */
class Item_Wrapper implements Item_WrapperInterface
{
	/**
	 * @see Item_WrapperInterface::list_fulltext_search()
	 * @param string $string
	 * @param array $item_type_array
	 * @param integer $language_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
   	 */
	public static function list_fulltext_search($string, $item_type_array, $language_id, $order_by, $order_method, $start, $end)
	{
		if (is_array($item_type_array) and count($item_type_array) >= 1)
		{
			$string = strtolower(trim($string));
			$string = str_replace("*", "%", $string);
			$string = str_replace(" ", "", $string);
			
			$select_sql_array = array();
			$join_sql = "";
			$where_sql = "";
			
			if (is_numeric($language_id))
			{
				$language = new Language($language_id);
				$tsvector_language = $language->get_tsvector_name();
			}
			else
			{
				$tsvector_language = "english";
			}
			
			foreach($item_type_array as $key => $value)
			{
				$handling_class = Item::get_handling_class_by_type($value);
				if (class_exists($handling_class))
				{
					if (is_array($handling_class::get_sql_fulltext_select_array($value)))
					{
						$temp_array = array();
						foreach ($handling_class::get_sql_fulltext_select_array($value) as $select_key => $select_value)
						{
							$temp_array[$select_key] = str_replace("{STRING}", $string, $select_value);
							$temp_array[$select_key] = str_replace("{LANGUAGE}", $tsvector_language, $temp_array[$select_key]);
						}
						array_push($select_sql_array, $temp_array);
						unset($temp_array);
					}
					
					$join_sql .= $handling_class::get_sql_join($value);
					if (($return_where_sql = $handling_class::get_sql_fulltext_where($value)) != null)
					{
						$return_where_sql = str_replace("{STRING}", $string, $return_where_sql);
						$return_where_sql = str_replace("{LANGUAGE}", "english", $return_where_sql);
						
						if ($where_sql == "")
						{
							$where_sql .= $return_where_sql;
						}
						else
						{
							$where_sql .= " OR ".$return_where_sql;
						}
					}
				}
			}
			return Item_Wrapper_Access::list_fulltext_search($string, $select_sql_array, $join_sql, $where_sql, $order_by, $order_method, $start, $end);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see Item_WrapperInterface::count_fulltext_search()
   	 * @param string $string
	 * @param array $item_type_array
	 * @param integer $language_id
	 * @return integer
   	 */
	public static function count_fulltext_search($string, $item_type_array, $language_id)
	{
		if (is_array($item_type_array) and count($item_type_array) >= 1)
		{
			$string = strtolower(trim($string));
			$string = str_replace("*", "%", $string);
			$string = str_replace(" ", "", $string);
			
			$join_sql = "";
			$where_sql = "";
			
			if (is_numeric($language_id))
			{
				$language = new Language($language_id);
				$tsvector_language = $language->get_tsvector_name();
			}
			else
			{
				$tsvector_language = "english";
			}
			
			foreach($item_type_array as $key => $value)
			{
				$handling_class = Item::get_handling_class_by_type($value);
				if (class_exists($handling_class))
				{					
					$join_sql .= $handling_class::get_sql_join($value);
					if (($return_where_sql = $handling_class::get_sql_fulltext_where($value)) != null)
					{
						$return_where_sql = str_replace("{STRING}", $string, $return_where_sql);
						$return_where_sql = str_replace("{LANGUAGE}", "english", $return_where_sql);
						
						if ($where_sql == "")
						{
							$where_sql .= $return_where_sql;
						}
						else
						{
							$where_sql .= " OR ".$return_where_sql;
						}
					}
				}
			}
			return Item_Wrapper_Access::count_fulltext_search($string, $select_sql_array, $join_sql, $where_sql);
		}
		else
		{
			return null;
		}
	}
}
