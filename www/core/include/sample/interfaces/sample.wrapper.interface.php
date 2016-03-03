<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Sample Wrapper Interface
 * @todo fulfill
 * @package sample
 */ 		 
interface Sample_WrapperInterface
{
	/**
	 * Returns a list of all sample templates
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_templates($order_by, $order_method, $start, $end);
	
	/**
	 * Returns the number of all sample templates
	 * @return integer
	 */
	public static function count_list_sample_templates();
	
	/**
	 * Returns a list of all sample template categories
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_template_categories($order_by, $order_method, $start, $end);
	
	/**
	 * Returns the number of all sample template categories
	 * @return integer
	 */
	public static function count_list_sample_template_categories();
	
	/**
	 * @param integer $user_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_user_samples($user_id, $order_by, $order_method, $start, $end);
	
	/**
	 * Returns the number of all users related samples
	 * @param integer $user_id
	 * @return integer
	 */
	public static function count_user_samples($user_id);
	
	/**
	 * Returns a set of sample in relations of a SQL-String
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_item_samples($item_sql, $order_by, $order_method, $start, $end);
	
	/**
	 * Returns the number of samples in relations of a SQL-String
	 * @param string $item_id
	 * @return integer
	 */
	public static function count_item_samples($item_sql);
	
	/** 
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_samples_by_item_id($item_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param string $item_id
	 * @return integer
	 */
	public static function count_samples_by_item_id($item_id);
	
	/** 
	 * @param integer $organisation_unit_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_organisation_unit_samples($organisation_unit_id, $order_by, $order_method, $start, $end);
	
	/** 
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_samples($organisation_unit_id);
	
	/** 
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_locations($sample_id, $order_by, $order_method, $start, $end);
	
	/** 
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_locations($sample_id);
	
	/** 
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_users($sample_id, $order_by, $order_method, $start, $end);
	
	/** 
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_users($sample_id);
	
	/** 
	 * @param integer $sample_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_sample_organisation_units($sample_id, $order_by, $order_method, $start, $end);
	
	/** 
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function count_sample_organisation_units($sample_id);
	
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
	public static function list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, $order_by, $order_method, $start, $end);
	
	/** 
	 * @param string $name
	 * @param array $organisation_unit_array
	 * @param array $template_array
	 * @param bool $in_id
	 * @param bool $in_name
	 * @return integer
	 */
	public static function count_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name);
	
	/**
	 * @param string $string
	 * @param array $sample_id_array
	 * @param array $item_type_array
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
   	 */
	public static function list_data_search($string, $sample_id_array, $item_type_array, $order_by, $order_method, $start, $end);
	
	/**
   	 * @param string $string
   	 * @param array $sample_id_array
   	 * @param array $item_type_array
   	 * @return integer
   	 */
	public static function count_data_search($string, $sample_id_array, $item_type_array);
	
	/**
	 * @param integer $sample_id
	 * @return array
	 */
	public static function get_sample_id_and_gid_by_parent_sample_id($sample_id);
	
	/**
   	 * @param integer $parent_item_id
   	 * @param integer $sample_id
   	 * @return bool
   	 */
	public static function delete_data_entity_sub_item_links($parent_item_id, $sample_id = null);
}
?>