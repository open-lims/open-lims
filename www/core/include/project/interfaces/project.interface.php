<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Project Interface
 * @package project
 */ 
interface ProjectInterface
{
	/**
	 * @param integer $project_id
	 */
	function __construct($project_id);
	
	function __destruct();
	
	/**
	 * Sets the template data for project creation
	 * @param string $type
	 * @param integer $type_id
	 * @param array $array
	 * @return bool
	 */
	public function set_template_data($type, $type_id, $array);
	
	/**
	 * Creates a new Project
	 * @param integer $organisation_unit_id
	 * @param integer $parent_project_id
	 * @param string $name
	 * @param integer $owner_id
	 * @param integer $template_id
	 * @param string $description
	 * @return integer
	 */
	public function create($organisation_unit_id, $parent_project_id, $name, $owner_id, $template_id, $description);
	
	/**
	 * Deletes a project
	 * @return bool
	 */
	public function delete();
	
	/**
     * @return bool
     */
	public function exist_subproject();
	
	/**
	 * Checks, if the next status is available
	 * @return integer
	 */
	public function is_next_status_available();
	
	/**
     * Checks the fulfillment of the current project status
     * @return bool
     */
	public function is_current_status_fulfilled();
	
	/**
     * Returns an array with all project status
     * @return array
     */
	public function get_all_status_array();
	
	/**
     * Returns the id of the current status
     * @return integer
     */
	public function get_current_status_id();
	
	/**
     * Returns the name of the current status
     * @return string
     */
	public function get_current_status_name();
	
	/**
     * Returns an array with all requirements of the current status
     * @return array
     */
	public function get_current_status_requirements();
	
	/**
     * Retruns an array with all fulfilled item-requirements of the current status
     * @return array
     */
	public function get_fulfilled_status_requirements();
	
	/**
	 * @return array
	 */
	public function get_fulfilled_status_datetimes();
	
	/**
	 * Retruns an array with all fulfilled extension-requirements of the current status
	 * @return array
	 */
	public function get_fulfilled_status_extension();
	
	/**
     * Returns the ID of the next status of the current project
     * @return integer
     */
	public function get_next_status_id();
	
	/**
     * Returns the name of the next status of the current project
     * @return string
     */
	public function get_next_status_name();
	
	 /**
     * Proceed the current project to the next status
     * @return bool
     */
	public function set_next_status();
	
	/**
     * Returns the requirements of a given status
     * @param integer $status_id
     * @return array
     */
	public function get_status_requirements($status_id);
	
	/**
     * Returns subfolder of a given gid
     * @param integer $folder_id Folder-ID
     * @param integer $gid 
     * @param integer $status_id
     * @return string Sub-Folder-Path
     */
	public function get_sub_folder($gid, $status_id);
	
	/**
     * Moves the project to an organisation unit
     * @param integer $organisation_unit_id
     * @return bool
     */
	public function move_to_organisation_unit($organisation_unit_id);
	
	/**
     * Moves the project to another project
     * @param integer $organisation_unit_id
     * @return bool
     */
	public function move_to_project($project_id);
	
	/**
     * @return bool
     */
	public function mark_as_deleted();
	
	/**
     * @return bool
     */
	public function mark_as_undeleted();
	
	/**
     * @param string $comment
     * @return bool
     */
	public function mark_as_canceled($comment);
	
	/**
     * @return bool
     */
	public function mark_as_reactivated();
	
	/**
     * Returns a list of related projects
     * @return array
     */
	public function list_project_related_projects();
	
	/**
     * Returns the project-tree (as an array) of the current project
     * @return array
     */
	public function get_project_tree();
	
	/**
     * Returns the id of the master-project
     * @return integer
     */
	public function get_master_project_id();
	
	/**
     * @return string
     */
	public function get_name();
	
	/**
     * @return string
     */
	public function get_datetime();
	
	/**
     * @return integer
     */
	public function get_owner_id();
	
	/**
     * @return integer
     */
	public function get_organisation_unit_id();
	
	/**
     * @return integer
     */
	public function get_project_toid();
	
	/**
	 * @return string
	 */
	public function get_description();
	
	/**
     * @return integer
     */
	public function get_template_id();
	
	/**
	 * @return string
	 */
	public function get_template_name();
	
	/**
     * @return integer
     */
	public function get_quota();
	
	/**
     * @return integer
     */
	public function get_filesize();
	
	/**
     * @return bool
     */
	public function get_deleted();
	
	/**
	 * @param integer $quota
	 * @return bool
	 */
	public function set_quota($quota);
	
	/**
	 * @param integer $filesize
	 * @return bool
	 */
	public function set_filesize($filesize);
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id);
	
	/**
     * Returns a list of all user related projects
     * @param integer $user_id
     * @param bool $analysis_only
     * @return array
     */
	public static function list_user_related_projects($user_id, $analysis_only);
	
	/**
	 * Returns a list of all organisation-unit related projects
	 * @param integer $organisation_unit_id
	 * @param bool $include_deleted
	 * @return array
	 */
	public static function list_organisation_unit_related_projects($organisation_unit_id, $include_deleted);
	
	/**
     * @param integer $template_id
     * @return array
     */
	public static function list_entries_by_template_id($template_id);
	
	/**
     * @param integer $toid_organ_unit
     * @param integer $toid_project
     * @param string $name
     * @return bool
     */
	public static function exist_project_name($toid_organ_unit, $toid_project, $name);
	
	/**
     * @param integer $project_id
     * @return bool
     */
	public static function exist_project($project_id);
	
	/**
     * @return integer
     */
	public static function get_used_project_space();
}
?>
