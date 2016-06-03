<?php
/**
 * @package sample
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
 * Sample Security Management Interface
 * @package sample
 */ 		 
interface SampleSecurityInterface
{
	/**
	 * @param integer $sample_id
	 */
	function __construct($sample_id);
	
	function __destruct();
	
	/**
   	 * Return true, if the user has access
   	 * @param integer $intention 1 = Read, 2 = Write
   	 * @param bool $ignore_admin_status
   	 * @return bool
   	 */
	public function is_access($intention, $ignore_admin_status);
	
	/**
     * Returns a string with access information (e.g. "Read, Write");
     * @return string
     */
	public function get_access_string();
	
	/**
 	 * Returns an array with access-information of an users (ignores transitive permissions via projects or OUs)
 	 * @param integer $user_id
 	 * @return array
 	 */
	public function get_access_by_user_id($user_id);
	
	/**
     * @param integer $user_id
     * @return integer
     */
	public function get_entry_by_user_id($user_id);
	
	/**
     * @param integer $organisation_unit_id
     * @return integer
     */
	public function get_entry_by_organisation_unit_id($organisation_unit_id);
	
	/**
     * Creates a new user-permission, including V-Folder
     * @param integer $user_id
     * @param bool $read
     * @param bool $write
     * @return integer
     */
	public function create_user($user_id, $read, $write);
	
	/**
     * Creates a new OU-permission, including V-Folder
     * @param integer $organisation_unit_id
     * @return integer
     */
	public function create_organisation_unit($organisation_unit_id);
	
	/**
     * Deletes an user-permission
     * @param integer $entry_id
     * @return bool
     */
	public function delete_user($entry_id);
	
	/**
     * Deletes an OU-permission
     * @param integer $entry_id
     * @return bool
     */
	public function delete_organisation_unit($entry_id);
	
	/**
     * Returns a list of user-IDs
     * @return array
     */
	public function list_users();
	
	/**
   	 * Returns a list of OU-IDs
   	 * @return array
   	 */
	public function list_organisation_units();
	
	/**
     * Return a list of user-PKs
     * @return array
     */
	public function list_user_entries();
	
	/**
     * Returns a list of OU-PKs
     * @return array
     */
	public function list_organisation_unit_entries();
	
	/**
     * Checks if a user has sample-permission-entry
     * @param integer $user_id
     * @return bool
     */
	public function is_user($user_id);
	
	/**
     * Checks if a OU has sample-permission-entry
     * @param integer $organisation_unit_id
     * @return bool
     */
	public function is_organisation_unit($organisation_unit_id);
	
	/**
     * Deletes an organisation unit from n:m table completly.
     * Warning: This method is for organisation-unit-deletion only!
     * 			Outside organisation-unit-deletion is causes logical inconsistency!
     * @param integer $organisation_unit_id
     * @return bool
     */
	public static function delete_organisation_complete($organisation_unit_id);
}

?>
