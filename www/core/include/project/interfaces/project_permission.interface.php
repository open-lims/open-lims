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
 * Project Permission Interface
 * @package project
 */ 	  	 
interface ProjectPermissionInterface
{
	function __construct($permission_id);
	function __destruct();
	
	public function create($user_id, $organisation_unit_id, $group_id, $project_id, $permission, $owner_id, $intention);
	public function delete();
	
	public function get_permission_array();
	public function get_user_id();
	public function get_organisation_unit_id();
	public function get_group_id();
	public function get_owner_id();
	public function get_intention();
	
	public function set_user_id($user_id);
	public function set_organisation_unit_id($organisation_unit_id);
	public function set_group_id($group_id);
	public function set_permission($permission);
	
	public static function list_entries_by_project_id($project_id);
	public static function list_entries_by_project_id_and_intention($project_id, $intention);
	public static function list_entries_by_project_id_and_intention_and_group_id($project_id, $intention, $group_id);
	public static function list_system_setted_projects_by_organisation_id($organisation_unit_id);
	public static function list_system_setted_entries_by_leader_id($leader_id);
	
	public static function delete_by_user_id($user_id);
	public static function delete_by_group_id($group_id);
	public static function delete_by_organisation_unit_id($organisation_unit_id);
	
	public static function reset_owner_id($old_owner_id, $new_owner_id);	
}
?>
