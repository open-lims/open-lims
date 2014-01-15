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
 * Project Template Interface
 * @package project
 */ 
interface ProjectTemplateInterface
{
	/**
	 * @param integer $project_tempalte_id
	 */
	function __construct($project_template_id);
	
	function __destruct();
	
	/**
	 * Creates a new project-template
	 * @param integer $data_entity_id
	 * @param integer $category_id
	 * @param bool $parent_template
	 * @return bool
	 */
	public function create($object_id, $category_id, $parent_template);
	
	/**
	 * Deletes a project-tempalte
	 * @return bool
	 */
	public function delete();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @return bool
	 */
	public function get_parent_template();
	
	/**
	 * Checks if a required-section exists in template
	 * @return bool
	 */
	public function is_required_requirements();
	
	/**
	 * Returns the requirements of the required-section
	 * @return array
	 */
	public function get_required_requirements();
	
	/**
	 * Returns the requirements of a given status
	 * @param integer $status_id
	 * @return array
	 */
	public function get_status_requirements($status_id);
	
	/**
	 * Returns all status of a project
	 * @return array
	 */
	public function get_all_status();
	
	/**
	 * Returns the next status of an given status
	 * @param status_id
	 * @return integer
	 */
	public function get_next_status($status_id);
	
	/**
	 * Returns the attributes of a given gid-entry
	 * @param integer $gid
	 * @param integer $status_id
	 * @return array
	 */
	public function get_gid_attributes($gid, $status_id);
	
	/**
	 * Returns the attributes of a giben status
	 * @param integer $status_id
	 * @return array
	 */
	public function get_status_attributes($status_id);
	
	/**
	 * Returns an item-element selected by status and GID
	 * @param integer $gid
	 * @param integer $status_id
	 * @return array
	 */
	public function get_status_item($gid, $status_id);
	
	/**
	 * Returns information about an class (elemtens inside class-element) from project-template
	 * @param integer $status_id
	 * @param string $class_name
	 * @return array
	 */
	public function get_status_class($status_id, $class_name);
	
	/**
	 * Checks a given id
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id);
	
	/**
	 * @return array
	 */
	public static function list_entries();
	
	/**
	 * @param integer $cat_id
	 * @return array
	 */
	public static function list_entries_by_cat_id($cat_id);
}
?>
