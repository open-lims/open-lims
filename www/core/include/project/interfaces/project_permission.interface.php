<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
	/**
     * Returns the permission array of a permission
     * @return array
     */
	public function get_permission_array();
	
	/**
     * @return integer
     */
	public function get_user_id();
	
	/**
     * @return integer
     */
	public function get_organisation_unit_id();
	
	/**
     * @return integer
     */
	public function get_group_id();
	
	/**
     * @return integer
     */
	public function get_owner_id();
	
	/**
     * @return integer
     */
	public function get_intention();
	
	/**
     * @param integer $user_id
     * @return bool
     */
	public function set_user_id($user_id);
	
	/**
     * @param integer $organisation_unit_id
     * @return bool
     */
	public function set_organisation_unit_id($organisation_unit_id);
	
	/**
     * @param integer $group_id
     * @return bool
     */
	public function set_group_id($group_id);
	
	/**
     * @param integer $permission
     * @return bool
     */
	public function set_permission($permission);
	
	/**
     * @param integer $project_id
     * @return array
     */
	public static function list_entries_by_project_id($project_id);
	
	/**
	 * @param integer $project_id
	 * @param integer $intention
	 * @return array
	 */
	public static function list_entries_by_project_id_and_intention($project_id, $intention);
	
	/**
	 * @param integer $project_id
	 * @param integer $intention
	 * @return array
	 */
	public static function delete_entries_by_project_id_and_intention($project_id, $intention);
	
	/**
     * Deletes a group from permission table completly.
     * Warning: This method is for group-deletion only!
     * 			Outside group-deletion is causes logical inconsistency!
     * @param integer $group_id
     * @return bool
     */
	public static function delete_by_group_id($group_id);
	
	/**
	 * Changes all owner_ids of another giver owner_id
	 * @param integer $old_owner_id
	 * @param integer $new_owner_id
	 * @return bool
	 */
	public static function reset_owner_id($old_owner_id, $new_owner_id);	
}
?>
