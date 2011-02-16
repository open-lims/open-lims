<?php
/**
 * @package organisation_unit
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
 * Organisation Unit Interface
 * @package organisation_unit
 */ 
interface OrganisationUnitInterface
{
	function __construct($organisation_unit_id);
	function __destruct();

	public function create($toid, $name, $type_id, $stores_data);
	public function delete();
	
	public function check_delete_dependencies();
	public function is_permission($user_id);
	public function is_upper_position();
	public function is_lower_position();
	public function position_upwards();
	public function position_downwards();
	
	public function is_user_in_organisation_unit($user_id);
	public function create_user_in_organisation_unit($user_id);
	public function delete_user_from_organisation_unit($user_id);
	
	public function is_group_in_organisation_unit($group_id);
	public function create_group_in_organisation_unit($group_id);
	public function delete_group_from_organisation_unit($group_id);
	
	public function get_type_name();
	public function get_name();
	public function get_icon();
	public function get_owner_id();
	public function get_leader_id();
	public function get_stores_data();
	public function get_hidden();
	public function get_user_status($user_id);
	public function get_organisation_unit_childs();
	public function get_number_of_members();
	public function get_number_of_groups();
	
	public function list_members();
	public function list_groups();
	
	public function set_name($name);
	public function set_owner_id($owner_id);
	public function set_leader_id($leader_id);
	public function set_hidden($hidden);
	
	// private function delete_members();
	// private function delete_groups();
	
	public static function get_number_of_organisation_units_by_user_id($user_id);
	public static function get_number_of_organisation_units_by_group_id($group_id);
	public static function get_name_by_type_id($type_id);
	public static function exist_name($name);
	public static function exist_organisation_unit($id);
	public static function list_organisation_unit_roots();
	public static function list_entries();
	public static function list_entries_by_user_id($user_id);
	public static function list_entries_by_group_id($group_id);
	public static function list_types();
	public static function count_organisation_units();
	// private static function is_leader($user_id);
	
	// private static function make_organisation_unit_tree_path_rec($id);
	// private static function make_full_organisation_unit_tree_rec($layer, $array);
	public function get_organisation_unit_tree();
}
?>
