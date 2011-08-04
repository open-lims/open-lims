<?php
/**
 * @package user
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * @package user
 */ 
interface GroupInterface
{
	function __construct($group_id);
	function __destruct();

	public function is_user_in_group($user_id);
	public function create_user_in_group($user_id);
	public function delete_user_from_group($user_id);
	public function get_number_of_user_members();
	
	public function get_name();
	public function set_name($name);
	
	public static function get_number_of_groups_by_user_id($user_id);
	public static function get_number_of_groups();
	public static function exist_name($name);
	public static function exist_group($group_id);
	public static function list_groups();
	public static function list_user_releated_groups($user_id);
	public static function list_group_releated_users($group_id);
	public static function search_groups($groupname);
	public static function count_groups();
}
?>
