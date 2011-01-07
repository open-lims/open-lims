<?php
/**
 * @package data
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
 * Folder Management Interface
 * @package data
 */
interface FolderInterface
{
	function __construct($folder_id);
	function __destruct();

	public function is_read_access();
	public function is_write_access();
	public function is_delete_access();
	public function is_control_access();
	
	public function is_flag_change_permission();
	public function is_flag_add_folder();
	public function is_flag_cmd_folder();
	public function is_flag_rename_folder();
	
	public function create($name, $toid, $root, $path, $owner_id, $owner_group_id);
	public function create_group_folder($group_id);
	public function create_home_folder($user_id);
	public function create_organisation_unit_folder($organisation_unit_id);
	public function create_project_folder($project_id);
	public function create_project_status_folder($status_id, $project_id);
	public function create_sample_folder($sample_id);
	
	public function exist_folder();
	public function exist_subfolder_name($name);
	public function exist_file($name);
	public function exist_value($value_type_id);
	
	public function delete($recursive, $content);
	public function mark_as_deleted();
	public function mark_as_undeleted();
	
	public function unset_group_folder();
	public function unset_home_folder();
	public function unset_organisation_unit_folder();
	public function unset_project_folder();
	public function unset_project_status_folder();
	public function unset_sample_folder();
	
	public function is_in_project();
	public function is_in_sample();
	
	public function is_group_folder();
	public function is_home_folder();
	public function is_organisation_unit_folder();
	public function is_project_folder();
	public function is_project_status_folder();
	public function is_sample_folder();
	
	public function is_child_of_project_folder();
	public function is_child_of_sample_folder();
	
	public function move_folder($destination_id);
	public function copy_folder($destination_id);
	
	public function get_object_path();
	public function get_object_id_path();
	public function get_toid();
	public function get_is_root();
	public function get_name();
	public function get_path();
	public function get_datetime();
	public function get_owner_id();
	public function get_owner_group_id();
	public function get_permission();
	public function get_permission_string();
	public function get_automatic();
	
	public function set_name($name);
	public function set_owner_id($owner_id);
	public function set_owner_group_id($owner_group_id);
	public function set_permission($permission);
	public function set_automatic($automatic);
	public function set_flag($flag);
	
	public function get_subfolder_array();
	public function is_folder_image_content();
	
	public static function get_project_supplementary_folder($project_id);
	public static function get_group_folder_by_group_id($group_id);
	public static function get_home_folder_by_user_id($user_id);
	public static function get_organisation_unit_folder_by_organisation_unit_id($organisation_unit_id);
	public static function get_project_folder_by_project_id($project_id);
	public static function get_project_status_folder_by_status_id($project_id, $status_id);
	public static function get_sample_folder_by_sample_id($sample_id);
	public static function get_folder_by_path($path);
	public static function list_folder();
	public static function set_owner_id_on_null($owner_id);
	public static function set_owner_group_id_on_null($owner_group_id);
}

?>
