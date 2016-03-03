<?php
/**
 * @package project
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
 * Project Security Interface
 * @package project
 */ 
interface ProjectSecurityInterface
{
	/**
     * @param integer $project_id
     */ 
	function __construct($project_id);
	
	function __destruct();
	
	/**
     * Checks the permissions of the current user
     * @param integer $itention Intention of the User (Read, Write, etc.)
     * @param integer $ignore_admin_status If it's true, an admin-status of an user will be ignored
     * @return bool
     */
	public function is_access($intention, $ignore_admin_status);
	
	/**
     * Lists involved users of a project
     * @return array
     */
	public function list_involved_users();
	
	/**
     * Changes the owner of a project
     * @param integer $owner_id Project Owner
     * @return bool
     */
	public function change_owner_permission($owner_id);
	
	/**
     * Changes the leader of a project
     * @param integer $leader_id
     * @return bool
     */
	public function change_ou_user_permission($organisation_unit_id);
	
	/**
     * Changes the organisation_unit of a project
     * @param integer $organisation_unit_id
     * @return bool
     */
	public function change_organisation_unit_permission($organisation_unit_id);
	
	/**
     * Sets another user-id (if the current user id is not required)
     * @param integer $user_id
     * @return bool
     */
    public function set_user_id($user_id);
}
?>
