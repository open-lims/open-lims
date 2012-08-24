<?php
/**
 * @package base
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
 * User Management Interface
 * @package base
 */ 	 
interface UserInterface
{
	/**
	 * @param interger $user_id User-ID
	 */
	function __construct($user_id);
	
	function __destruct();
	
	/**
	 * Creates a new user including all dependencies
	 * @param string $username
	 * @param string $gener
	 * @param string $title
	 * @param string $forename
	 * @param string $surname
	 * @param string $mail
	 * @param bool $can_change_password
	 * @param bool $must_change_password
	 * @param bool $disabled
	 * @return string Generated User Password
	 * @throws UserCreationFailedException
	 * @throws UserAlreadyExistException
	 */
	public function create($username, $gender, $title, $forename, $surname, $mail, $can_change_password, $must_change_password, $disabled);
	
	/**
	 * Deletes an user
	 * @return bool
	 */
	public function delete();
	
	/**
	 * Checks dependencies before user deletion.
	 * @return bool
	 */
	public function check_delete_dependencies();
	
	/**
	 * Matches a given password with the database-saved password
	 * @param string $password
	 * @return bool
	 */
	public function check_password($password);
	
	/**
	 * Matches a gove mail-address with the database-saved password
	 * @param string $mail
	 * @return bool
	 */
	public function check_mail($mail);
	
	/**
	 * Checks the administration-permission of an user
	 * @return bool
	 */
	public function is_admin();
	
	/**
	 * @return interger User-ID
	 */
	public function get_user_id();
	
	/**
	 * @return string Username
	 */
	public function get_username();
	
	/**
	 * Function will return "J. Smith" instead of "Joe Smith", if it is true
	 * @param bool $short_version
	 * @return string Full User-Name (Title, Forname and Surname)
	 */
	public function get_full_name($short_version);
	
	/**
	 * @return string Hashed Password String
	 */
	public function get_password();
	
	/**
	 * @return string Date of last password change
	 */
	public function get_last_password_change();
	
	/**
	 * @param string $entry Name of required entry
	 * @return bool
	 */
	public function get_boolean_user_entry($entry);
	
	/**
	 * @param string $entry Name of required entry
	 * @return mixed
	 */
	public function get_profile($entry);
	
	/**
	 * @return integer Language-ID
	 */
	public function get_language_id();
	
	/**
	 * @return integer Timezone-ID
	 */
	public function get_timezone_id();
	
	/**
	 * @param string $username New User-Name
	 * @return bool
	 */
	public function set_username($username);
	
	/**
	 * @param string $password New Password
	 * @return bool
	 */
	public function set_password($password);
	
	/**
	 * @param string $password New Password
	 * @return bool
	 */
	public function set_password_on_login($password);
	
	/**
	 * @param string $last_password_change New date of lase password change
	 * @return bool
	 */
	public function set_last_password_change($last_password_change);
	
	/**
	 * @param string $entry Name of the entry
	 * @param bool $value
	 * @return bool
	 */
	public function set_boolean_user_entry($entry, $value);
	
	/**
	 * @param string $entry Name of the entry
	 * @param string $value
	 * @return bool
	 */
	public function set_profile($entry, $value);
	
	/**
	 * @param integer $language_id Language-ID
	 * @return bool
	 */
	public function set_language_id($language_id);
	
	/**
	 * @param integer $timezone_id Timezone-ID
	 * @return bool
	 */
	public function set_timezone_id($timezone_id);
	
	/**
	 * Generates a random password
	 * @return string new password
	 */
	public static function generate_password();
	
	/**
	 * @param string $username
	 * @return integer User-ID
	 */
	public static function get_user_id_by_username($username);
	
	/**
	 * @return integer Number of inactive Users
	 */
	public static function get_number_of_inactive_users();
	
	/**
	 * @return integer Number of locked Users
	 */
	public static function get_number_of_locked_users();
	
	/**
	 * Checks if an user exists
	 * @param string $username
	 * @return bool
	 */
	public static function exist_username($username);
	
	/**
	 * Checks if an user exists
	 * @param integer $user_id
	 * @return bool
	 */
	public static function exist_user($user_id);
	
	/**
	 * @return array Array of all User-IDs
	 */
	public static function list_entries();
	
	/**
   	 * @return integer
   	 */
	public static function count_users();
	
	/**
   	 * @return integer
   	 */
	public static function count_administrators();
}
?>
