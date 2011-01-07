<?php
/**
 * @package user
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
 * User Management Interface
 * @package user
 */ 	 
interface UserInterface
{
	function __construct($user_id);
	function __destruct();
	
	public function create($username, $gender, $title, $forename, $surname, $mail, $can_change_password, $must_change_password, $disabled);
	public function delete();
	
	public function check_delete_dependencies();
	public function check_password($password);
	public function check_mail($mail);
	
	public function is_admin();
	
	public function get_user_id();
	public function get_username();
	public function get_full_name($short_version);
	public function get_password();
	public function get_project_quota();
	public function get_user_quota();
	public function get_user_filesize();
	public function get_last_password_change();
	public function get_boolean_user_entry($entry);
	public function get_profile($entry);
	public function get_language_id();
	public function get_timezone_id();
	
	public function set_username($username);
	public function set_password($password);
	public function set_password_on_login($password);
	public function set_last_password_change($last_password_change);
	public function set_project_quota($project_quota);
	public function set_user_quota($user_quota);
	public function set_user_filesize($filesize);
	public function increase_filesize($filesize);
	public function set_boolean_user_entry($entry, $value);
	public function set_profile($entry, $value);
	public function set_language_id($language_id);
	public function set_timezone_id($timezone_id);
	
	public static function generate_password();
	public static function get_user_id_by_username($username);
	public static function get_number_of_users();
	public static function get_number_of_inactive_users();
	public static function get_number_of_locked_users();
	public static function get_used_user_space();
	public static function exist_username($username);
	public static function exist_user($user_id);
	public static function list_entries();
	public static function search_users($username);
	public static function count_users();
	public static function count_administrators();
}
?>
