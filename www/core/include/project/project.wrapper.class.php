<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
require_once("interfaces/project.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project.wrapper.access.php");
}

/**
 * Project Wrapper Class for complex project-joins
 * @package project
 */
class Project_Wrapper implements Project_WrapperInterface
{
	/**
	 * Returns a list of all project status
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
    public static function list_project_status($order_by, $order_method, $start, $end)
    {
		return Project_Wrapper_Access::list_project_status($order_by, $order_method, $start, $end);
	}
	
	/**
	 * Retruns the number of all project status
	 * @return integer
	 */
	public static function count_list_project_status()
	{
		return Project_Wrapper_Access::count_list_project_status();
	}
	
	/**
	 * Returns a list of all project templates
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_templates($order_by, $order_method, $start, $end)
	{
		return Project_Wrapper_Access::list_project_templates($order_by, $order_method, $start, $end);
	}
	
	/**
	 * Returns the number of all project templates
	 * @return integer
	 */
	public static function count_list_project_templates()
	{
		return Project_Wrapper_Access::count_list_project_templates();
	}
	
	/**
	 * Return a list of all project template categories
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_template_categories($order_by, $order_method, $start, $end)
	{
		return Project_Wrapper_Access::list_project_template_categories($order_by, $order_method, $start, $end);
	}
	
	/**
	 * Returns the number of all project template categories
	 * @return integer
	 */
	public static function count_list_project_template_categories()
	{
		return Project_Wrapper_Access::count_list_project_template_categories();
	}
	
	/**
	 * Returns the number of all users projects
	 * @return integer
	 */
	public static function count_user_projects($user_id)
	{
		return Project_Wrapper_Access::count_user_projects($user_id);
	}
	
	/**
	 * Returns the number of all users running projects
	 * @return integer
	 */
	public static function count_user_running_projects($user_id)
	{
		return Project_Wrapper_Access::count_user_running_projects($user_id);
	}
	
	/**
	 * Returns the number of all users finished projects
	 * @return integer
	 */
	public static function count_user_finished_projects($user_id)
	{
		return Project_Wrapper_Access::count_user_finished_projects($user_id);
	}
	
	/**
	 * Return a list of all user related projects
	 * @param integer $user_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_user_related_projects($user_id, $order_by, $order_method, $start, $end)
	{
		return Project_Wrapper_Access::list_user_related_projects($user_id, $order_by, $order_method, $start, $end);
	}
	
	/**
	 * Returns the number of all users related projects
	 * @return integer
	 */
	public static function count_list_user_related_projects($user_id)
	{
		return Project_Wrapper_Access::count_list_user_related_projects($user_id);
	}
	
	public static function list_projects_by_item_id($item_id, $order_by, $order_method, $start, $end)
	{
		return Project_Wrapper_Access::list_projects_by_item_id($item_id, $order_by, $order_method, $start, $end);
	}
	
	public static function count_projects_by_item_id($item_id)
	{
		return Project_Wrapper_Access::count_projects_by_item_id($item_id);
	}
	
	public static function list_search_projects_without_subprojects($name, $template_array, $organisation_unit_array, $order_by, $order_method, $start, $end)
	{
		return Project_Wrapper_Access::list_search_projects_without_subprojects($name, $template_array, $organisation_unit_array, $order_by, $order_method, $start, $end);
	}
	
	public static function count_search_projects_without_subprojects($name, $template_array, $organisation_unit_array)
	{
		return Project_Wrapper_Access::count_search_projects_without_subprojects($name, $template_array, $organisation_unit_array);
	}
	
	public static function list_search_projects_with_subprojects($name, $template_array, $organisation_unit_array, $order_by, $order_method, $start, $end)
	{
		return Project_Wrapper_Access::list_search_projects_with_subprojects($name, $template_array, $organisation_unit_array, $order_by, $order_method, $start, $end);
	}
	
	public static function count_search_projects_with_subprojects($name, $template_array, $organisation_unit_array)
	{
		return Project_Wrapper_Access::count_search_projects_with_subprojects($name, $template_array, $organisation_unit_array);
	}
	
	/**
   	 * @param string $string
   	 * @param array $project_id_array
   	 * @param array $item_type_array
   	 * @param string $order_by
   	 * @param string $order_method
   	 * @param integer $start
   	 * @param integer $end
   	 * @return array
   	 */
	public static function list_data_search($string, $project_id_array, $item_type_array, $order_by, $order_method, $start, $end)
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
			return Project_Wrapper_Access::list_data_search($string, $project_id_array, $select_sql_array, $join_sql, $where_sql, $order_by, $order_method, $start, $end);
		}
		else
		{
			return null;
		}
	}
	
	/**
   	 * @param string $string
   	 * @param array $project_id_array
   	 * @param array $item_type_array
   	 * @return integer
   	 */
	public static function count_data_search($string, $project_id_array, $item_type_array)
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
			return Project_Wrapper_Access::count_data_search($string, $project_id_array, $select_sql_array, $join_sql, $where_sql);
		}
		else
		{
			return null;
		}
	}
	
}
?>