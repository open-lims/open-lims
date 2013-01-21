<?php
/**
 * @package sample
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
require_once("interfaces/sample.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/sample.wrapper.access.php");
}

/**
 * Sample Wrapper Class for complex sample-joins
 * @package sample
 */
class Sample_Wrapper implements Sample_WrapperInterface
{
	/**
	 * @see Sample_WrapperInterface::list_sample_templates()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_templates($order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_templates($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Sample_WrapperInterface::count_list_sample_templates()
	 * @return integer
	 */
	public static function count_list_sample_templates()
	{
		return Sample_Wrapper_Access::count_list_sample_templates();
	}
	
	/**
	 * @see Sample_WrapperInterface::list_sample_template_categories()
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_template_categories($order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_template_categories($order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Sample_WrapperInterface::count_list_sample_template_categories()
	 * @return integer
	 */
	public static function count_list_sample_template_categories()
	{
		return Sample_Wrapper_Access::count_list_sample_template_categories();
	}
	
	/**
	 * @see Sample_WrapperInterface::list_user_samples()
	 * @param integer $user_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_user_samples($user_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_user_samples($user_id, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Sample_WrapperInterface::count_user_samples()
	 * @param integer $user_id
	 * @return integer
	 */
	public static function count_user_samples($user_id)
	{
		return Sample_Wrapper_Access::count_user_samples($user_id);
	}
	
	/**
	 * @see Sample_WrapperInterface::list_item_samples()
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_item_samples($item_sql, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_item_samples($item_sql, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Sample_WrapperInterface::count_item_samples()
	 * @param string $item_id
	 * @return integer
	 */
	public static function count_item_samples($item_sql)
	{
		return Sample_Wrapper_Access::count_item_samples($item_sql);
	}
	
 	/** 
 	 * @see Sample_WrapperInterface::list_samples_by_item_id()
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_samples_by_item_id($item_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_samples_by_item_id($item_id, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * @see Sample_WrapperInterface::count_samples_by_item_id()
	 * @param string $item_id
	 * @return integer
	 */
	public static function count_samples_by_item_id($item_id)
	{
		return Sample_Wrapper_Access::count_samples_by_item_id($item_id);
	}

	/** 
	 * @see Sample_WrapperInterface::list_organisation_unit_samples()
	 * @param integer $organisation_unit_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_organisation_unit_samples($organisation_unit_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_organisation_unit_samples($organisation_unit_id, $order_by, $order_method, $start, $end);
	}
	
	/** 
	 * @see Sample_WrapperInterface::count_organisation_unit_samples()
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_samples($organisation_unit_id)
	{
		return Sample_Wrapper_Access::count_organisation_unit_samples($organisation_unit_id);
	}
	
	/** 
	 * @see Sample_WrapperInterface::list_sample_locations()
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_locations($sample_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_locations($sample_id, $order_by, $order_method, $start, $end);
	}
	
	/** 
	 * @see Sample_WrapperInterface::count_sample_locations()
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_locations($sample_id)
	{
		return Sample_Wrapper_Access::count_sample_locations($sample_id);
	}
	
	/** 
	 * @see Sample_WrapperInterface::list_sample_users()
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_users($sample_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_users($sample_id, $order_by, $order_method, $start, $end);
	}
	
	/** 
	 * @see Sample_WrapperInterface::count_sample_users()
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_users($sample_id)
	{
		return Sample_Wrapper_Access::count_sample_users($sample_id);
	}
	
	/** 
	 * @see Sample_WrapperInterface::list_sample_organisation_units()
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_organisation_units($sample_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_organisation_units($sample_id, $order_by, $order_method, $start, $end);
	}
	
	/** 
	 * @see Sample_WrapperInterface::count_sample_organisation_units()
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_organisation_units($sample_id)
	{
		return Sample_Wrapper_Access::count_sample_organisation_units($sample_id);
	}

	/** 
	 * @see Sample_WrapperInterface::list_sample_search()
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
		return Sample_Wrapper_Access::list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, $order_by, $order_method, $start, $end);
	}
	
	/** 
	 * @see Sample_WrapperInterface::count_sample_search()
	 * @param string $name
	 * @param array $organisation_unit_array
	 * @param array $template_array
	 * @param bool $in_id
	 * @param bool $in_name
	 * @return integer
	 */
	public static function count_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name)
	{
		return Sample_Wrapper_Access::count_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name);
	}
	 
	/**
	 * @see Sample_WrapperInterface::list_data_search()
	 * @param string $string
	 * @param array $sample_id_array
	 * @param array $item_type_array
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
   	 */
	public static function list_data_search($string, $sample_id_array, $item_type_array, $order_by, $order_method, $start, $end)
	{
		if (is_array($item_type_array) and count($item_type_array) >= 1)
		{
			$string = strtolower(trim($string));
			$string = str_replace("*", "%", $string);
			
			$select_sql_array = array();
			$join_sql = "";
			$where_sql = "";
			
			foreach($item_type_array as $key => $value)
			{
				$handling_class = Item::get_handling_class_by_type($value);
				if (class_exists($handling_class))
				{
					if (is_array($handling_class::get_sql_select_array($value)))
					{
						array_push($select_sql_array, $handling_class::get_sql_select_array($value));
					}
					
					$join_sql .= $handling_class::get_sql_join($value);
					if (($return_where_sql = $handling_class::get_sql_where($value)) != null)
					{
						$return_where_sql = str_replace("{STRING}", $string, $return_where_sql);
						
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
			return Sample_Wrapper_Access::list_data_search($string, $sample_id_array, $select_sql_array, $join_sql, $where_sql, $order_by, $order_method, $start, $end);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see Sample_WrapperInterface::count_data_search()
   	 * @param string $string
   	 * @param array $sample_id_array
   	 * @param array $item_type_array
   	 * @return integer
   	 */
	public static function count_data_search($string, $sample_id_array, $item_type_array)
	{
		if (is_array($item_type_array) and count($item_type_array) >= 1)
		{
			$string = strtolower(trim($string));
			$string = str_replace("*", "%", $string);
			
			$join_sql = "";
			$where_sql = "";
			
			foreach($item_type_array as $key => $value)
			{
				$handling_class = Item::get_handling_class_by_type($value);
				if (class_exists($handling_class))
				{					
					$join_sql .= $handling_class::get_sql_join($value);
					if (($return_where_sql = $handling_class::get_sql_where($value)) != null)
					{
						$return_where_sql = str_replace("{STRING}", $string, $return_where_sql);
						
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
			return Sample_Wrapper_Access::count_data_search($string, $sample_id_array, $select_sql_array, $join_sql, $where_sql);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see Sample_WrapperInterface::get_sample_id_and_gid_by_parent_sample_id()
	 * @param integer $sample_id
	 * @return array
	 */
	public static function get_sample_id_and_gid_by_parent_sample_id($sample_id)
	{
		return Sample_Wrapper_Access::get_sample_id_and_gid_by_parent_sample_id($sample_id);
	}
	
	/**
	 * @see Sample_WrapperInterface::delete_data_entity_sub_item_links
   	 * @param integer $parent_item_id
   	 * @param integer $sample_id
   	 * @return bool
   	 */
	public static function delete_data_entity_sub_item_links($parent_item_id, $sample_id = null)
	{
		return Sample_Wrapper_Access::delete_data_entity_sub_item_links($parent_item_id, $sample_id);
	}
}
?>