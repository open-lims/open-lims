<?php
/**
 * @package project
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
 * Project Wrapper Interface
 * @package project
 */ 		 
interface Project_WrapperInterface
{
	/**
	 * Returns a list of all project status
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_status($order_by, $order_method, $start, $end);
	
	/**
	 * Retruns the number of all project status
	 * @return integer
	 */
	public static function count_list_project_status();
	
	/**
	 * Returns a list of all project templates
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_templates($order_by, $order_method, $start, $end);
	
	/**
	 * Returns the number of all project templates
	 * @return integer
	 */
	public static function count_list_project_templates();
	
	/**
	 * Return a list of all project template categories
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_template_categories($order_by, $order_method, $start, $end);
	
	/**
	 * Returns the number of all project template categories
	 * @return integer
	 */
	public static function count_list_project_template_categories();
	
	/**
	 * Returns the number of all users projects
	 * @return integer
	 */
	public static function count_user_projects($user_id);
	
	/**
	 * Returns the number of all users running projects
	 * @return integer
	 */
	public static function count_user_running_projects($user_id);
	
	/**
	 * Returns the number of all users finished projects
	 * @return integer
	 */
	public static function count_user_finished_projects($user_id);
	
	/**
	 * Return a list of all user related projects
	 * @param integer $user_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_user_related_projects($user_id, $admin, $order_by, $order_method, $start, $end);
	
	/**
	 * Returns the number of all users related projects
	 * @param integer $user_id
	 * @return integer
	 */
	public static function count_list_user_related_projects($user_id, $admin);
	
	/**
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_organisation_unit_related_projects($organisation_unit_id, $admin);
	
	/**
	 * @param integer $organisation_unit_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_organisation_unit_related_projects($organisation_unit_id, $admin, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $item_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_projects_by_item_id($item_id, $admin, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $item_id
	 * @return integer
	 */
	public static function count_projects_by_item_id($item_id, $admin);
	
	/**
	 * @param integer $project_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_permissions($project_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $project_id
	 */
	public static function count_project_permissions($project_id);
	
	/**
	 * @param integer $project_id
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_project_tasks($project_id, $order_by, $order_method, $start, $end);
	
	/**
	 * @param integer $project_id
	 * @return integer
	 */
	public static function count_project_tasks($project_id);
	
	/**
	 * @param string $name
	 * @param array $template_id
	 * @param array $organisation_unit_array
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_search_projects_without_subprojects($name, $template_array, $organisation_unit_array, $order_by, $order_method, $start, $end);
	
	/**
	 * @param string $name
	 * @param array $template_id
	 * @param array $organisation_unit_array
	 * @return integer
	 */
	public static function count_search_projects_without_subprojects($name, $template_array, $organisation_unit_array);
	
	/**
	 * @param string $name
	 * @param array $template_id
	 * @param array $organisation_unit_array
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_search_projects_with_subprojects($name, $template_array, $organisation_unit_array, $order_by, $order_method, $start, $end);
	
	/**
	 * @param string $name
	 * @param array $template_id
	 * @param array $organisation_unit_array
	 * @return integer
	 */
	public static function count_search_projects_with_subprojects($name, $template_array, $organisation_unit_array);
	
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
	public static function list_data_search($string, $project_id_array, $item_type_array, $order_by, $order_method, $start, $end);
	
	/**
   	 * @param string $string
   	 * @param array $project_id_array
   	 * @param array $item_type_array
   	 * @return integer
   	 */
	public static function count_data_search($string, $project_id_array, $item_type_array);
	
	/**
   	 * @param integer $parent_item_id
   	 * @param integer $project_id
   	 * @return bool
   	 */
	public static function delete_data_entity_sub_item_links($parent_item_id, $project_id = null);
}
?>