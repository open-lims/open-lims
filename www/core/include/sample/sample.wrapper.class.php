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
	 * Returns a list of all sample templates
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
	 * Returns the number of all sample templates
	 * @return integer
	 */
	public static function count_list_sample_templates()
	{
		return Sample_Wrapper_Access::count_list_sample_templates();
	}
	
	/**
	 * Returns a list of all sample template categories
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
	 * Returns the number of all sample template categories
	 * @return integer
	 */
	public static function count_list_sample_template_categories()
	{
		return Sample_Wrapper_Access::count_list_sample_template_categories();
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
		return Sample_Wrapper_Access::list_user_samples($user_id, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * Returns the number of all users related samples
	 * @param integer $user_id
	 * @return integer
	 */
	public static function count_user_samples($user_id)
	{
		return Sample_Wrapper_Access::count_user_samples($user_id);
	}
	
	/**
	 * Returns a set of sample in relations of a SQL-String
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
	 * Returns the number of samples in relations of a SQL-String
	 * @param string $item_id
	 * @return integer
	 */
	public static function count_item_samples($item_sql)
	{
		return Sample_Wrapper_Access::count_item_samples($item_sql);
	}
	
 	/** Returns a set of sample in relations of a SQL-String
	 * @param string $item_sql
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_item_parentsamples($item_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_item_parentsamples($item_id, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * Returns the number of samples in relations of a SQL-String
	 * @param string $item_id
	 * @return integer
	 */
	public static function count_item_parentsamples($item_id)
	{
		return Sample_Wrapper_Access::count_item_parentsamples($item_id);
	}

	public static function list_organisation_unit_samples($organisation_unit_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_organisation_unit_samples($organisation_unit_id, $order_by, $order_method, $start, $end);
	}
	
	public static function count_organisation_unit_samples($organisation_unit_id)
	{
		return Sample_Wrapper_Access::count_organisation_unit_samples($organisation_unit_id);
	}
	
	public static function list_sample_depositories($sample_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_depositories($sample_id, $order_by, $order_method, $start, $end);
	}
	
	public static function count_sample_depositories($sample_id)
	{
		return Sample_Wrapper_Access::count_sample_depositories($sample_id);
	}
	
	public static function list_sample_users($sample_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_users($sample_id, $order_by, $order_method, $start, $end);
	}
	
	public static function count_sample_users($sample_id)
	{
		return Sample_Wrapper_Access::count_sample_users($sample_id);
	}
	
	public static function list_sample_organisation_units($sample_id, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_organisation_units($sample_id, $order_by, $order_method, $start, $end);
	}
	
	public static function count_sample_organisation_units($sample_id)
	{
		return Sample_Wrapper_Access::count_sample_organisation_units($sample_id);
	}

	public static function list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, $order_by, $order_method, $start, $end)
	{
		return Sample_Wrapper_Access::list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, $order_by, $order_method, $start, $end);
	}
	
	public static function count_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name)
	{
		return Sample_Wrapper_Access::count_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name);
	}

}
?>