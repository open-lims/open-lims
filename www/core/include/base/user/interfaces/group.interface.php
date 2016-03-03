<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Group Management Interface
 * @package base
 */ 
interface GroupInterface
{
	/**
	 * @param integer $group_id Group-ID
	 */
	function __construct($group_id);
	
	function __destruct();

	/**
	 * Creates a new user including all needed dependencies
	 * @param string $name
	 * @return integer
	 * @throws GroupAlreadyExistException
	 * @throws GroupCreationFailedException
	 */
	public function create($name);
	
	/**
	 * Deletes a group
	 * @return bool
	 */
	public function delete();
	
	/**
	 * Checks if a user in group
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function is_user_in_group($user_id);
	
	/**
	 * Links a new user to the group
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function create_user_in_group($user_id);
	
	/**
	 * Deletes an user from the group
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function delete_user_from_group($user_id);
	
	/**
	 * @return integer Number of Members
	 */
	public function get_number_of_user_members();
	
	/**
	 * @return string Group Name
	 */
	public function get_name();
	
	/**
	 * @param string $name Group Name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
	 * @param integer $user_id User-ID
	 * @return integer Number of memberships of an user
	 */
	public static function get_number_of_groups_by_user_id($user_id);
	
	/**
	 * @return integer Number of all Groups
	 */
	public static function get_number_of_groups();
	
	/**
	 * Checks if a group exists by name
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name);
	
	/** 
	 * Checks if a group exists by id
	 * @param integer $group_id
	 * @return bool
	 */
	public static function exist_group($group_id);
	
	/**
	 * @return array Array of all Groups
	 */
	public static function list_groups();
	
	/**
	 * @param integer $user_id User-ID
	 * @return array Array of all related groups
	 */
	public static function list_user_releated_groups($user_id);
	
	/**
	 * @param integer $group_id Group-ID
	 * @return array Array of all related users
	 */
	public static function list_group_releated_users($group_id);
	
	/**
	 * Searchs groups via groupname
	 * @param string $groupname
	 * @return array Array of Group-IDs
	 */
	public static function search_groups($groupname);
	
	/**
	 * @return integer
	 */
	public static function count_groups();
}
?>
