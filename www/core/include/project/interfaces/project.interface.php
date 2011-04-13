<?php
/**
 * @package project
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
 * Project Interface
 * @package project
 */ 
interface ProjectInterface
{
	function __construct($project_id);
	function __destruct();
	
	public function set_template_data($type, $type_id, $array);
	public function create($organisation_unit_id, $parent_project_id, $name, $owner_id, $template_id, $description);
	public function delete();
	
	public function exist_subproject();
	public function is_next_status_available();
	public function is_current_status_fulfilled();
	public function get_all_status_array();
	public function get_current_status_id();
	public function get_current_status_name();
	public function get_current_status_requirements();
	public function get_fulfilled_status_requirements();
	public function get_fulfilled_status_datetimes();
	public function get_next_status_id();
	public function get_next_status_name();
	public function set_next_status($checksum, $comment);
	public function get_status_requirements($status_id);
	
	public function get_sub_folder($gid, $status_id);
	
	public function move_to_organisation_unit($organisation_unit_id);
	public function move_to_project($project_id);
	
	public function mark_as_deleted();
	public function mark_as_undeleted();
	public function mark_as_canceled($comment);
	public function mark_as_reactivated();
	
	public function list_project_related_projects();
	public function get_project_tree();
	public function get_master_project_id();
	
	public function get_name();
	public function get_datetime();
	public function get_owner_id();
	public function get_organisation_unit_id();
	public function get_project_toid();
	public function get_description();
	public function get_template_id();
	public function get_template_name();
	public function get_quota();
	public function get_filesize();
	public function get_deleted();
	public function set_quota($quota);
	public function set_filesize($filesize);
	public function set_name($name);
	public function set_owner_id($owner_id);
	
	public static function list_user_related_projects($user_id, $analysis_only);
	public static function list_organisation_unit_related_projects($organisation_unit_id, $include_deleted);
	public static function list_entries_by_template_id($template_id);
	public static function exist_project_name($toid_organ_unit, $toid_project, $name);
	public static function exist_project($project_id);
	public static function get_used_project_space();
	// private static function make_project_tree_rec($layer, $id);	
}
?>
