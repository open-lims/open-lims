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
 * Sample Security Management Interface
 * @package sample
 */ 		 
interface SampleSecurityInterface
{
	function __construct($sample_id);
	function __destruct();
	
	public function is_access($intention, $ignore_admin_status);
	
	public function get_access_string();
	public function get_access_by_user_id($user_id);
	public function get_entry_by_user_id($user_id);
	public function get_entry_by_organisation_unit_id($organisation_unit_id);
	
	public function create_user($user_id, $read, $write);
	public function create_organisation_unit($organisation_unit_id);
	public function create_project($project_id);
	public function delete_user($entry_id);
	public function delete_organisation_unit($entry_id);
	public function list_users();
	public function list_organisation_units();
	public function list_user_entries();
	public function list_organisation_unit_entries();
	public function is_user($user_id);
	public function is_organisation_unit($organisation_unit_id);
	
	public static function delete_user_complete($user_id);
	public static function delete_organisation_complete($organisation_unit_id);
}

?>
