<?php
/**
 * @package organisation_unit
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
 * Organisation Unit Interface
 * @package organisation_unit
 */ 
interface OrganisationUnitInterface
{
	/**
	 * @param integer $organisation_unit_id Organisation-Unit-ID
	 */
	function __construct($organisation_unit_id);
	
	function __destruct();

	/**
	 * Creates a new Organisation-Unit including all dependencies
	 * @param integer $toid Parent-ID
	 * @param string $name
	 * @param integer $type_id
	 * @param bool $stores_data
	 * @return integer
	 * @throws OrganisationUnitAlreadyExistException
	 * @throws OrganisationUnitCreationFailedException
	 */
	public function create($toid, $name, $type_id, $stores_data);
	
	/**
	 * Deletes an Organisation-Unit
	 * @return bool
	 */
	public function delete();
	
	/**
	 * Checks dependencies before Organisation-Unit deletion.
	 * @return bool
	 */
	public function check_delete_dependencies();
	
	/**
	 * Checks permissions on Organisation-Unit
	 * @param integer $user_id
	 * @return bool
	 */
	public function is_permission($user_id);
	
	/**
	 * Checks if the OU is on the upper position
	 * @return bool
	 */
	public function is_upper_position();
	
	/**
	 * Checks if the OU is on the lower position
	 * @return bool
	 */
	public function is_lower_position();
	
	/**
	 * Moves the OU one position upwards
	 * @return bool
	 */
	public function position_upwards();
	
	/**
	 * Moves the OU one position downwards
	 * @return bool
	 */
	public function position_downwards();
	
	/**
	 * Checks if an User is in the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function is_user_in_organisation_unit($user_id);
	
	/**
	 * Creates an User in the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function create_user_in_organisation_unit($user_id);
	
	/**
	 * Deletes an User from the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function delete_user_from_organisation_unit($user_id);
	
	/**
	 * @return integer
	 */
	public function get_number_of_users();
	
	/**
	 * Checks if a Group is in the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function is_group_in_organisation_unit($group_id);
	
	/**
	 * Creates a Group in the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function create_group_in_organisation_unit($group_id);
	
	/**
	 * Deletes a Group from the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function delete_group_from_organisation_unit($group_id);
	
	/**
	 * @return integer
	 */
	public function get_number_of_groups();
	
	/**
	 * Checks if a Leader is in the OU
	 * @param integer $leader_id
	 * @return bool
	 */
	public function is_leader_in_organisation_unit($leader_id);
	
	/**
	 * Creates a Leader in the OU
	 * @param integer $leader_id
	 * @return bool
	 */
	public function create_leader_in_organisation_unit($leader_id);
	
	/**
	 * Deletes a Leader from the OU
	 * @param integer $leader_id
	 * @return bool
	 */
	public function delete_leader_from_organisation_unit($leader_id);
	
	/**
	 * @return integer
	 */
	public function get_number_of_leaders();
	
	/**
	 * Checks if an Owner is in the OU
	 * @param integer $owner_id
	 * @return bool
	 */
	public function is_owner_in_organisation_unit($owner_id);
	
	/**
	 * @param integer $owner_id
	 * @param bool $master_owner
	 * @return bool
	 */
	public function create_owner_in_organisation_unit($owner_id, $master_owner = null);
	
	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public function delete_owner_from_organisation_unit($owner_id);
	
	/**
	 * @return integer
	 */
	public function get_number_of_owners();
	
	/**
	 * Checks if a Quality-Manager is in the OU
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public function is_quality_manager_in_organisation_unit($quality_manager_id);
	
	/**
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public function create_quality_manager_in_organisation_unit($quality_manager_id);
	
	/**
	 * Deletes a Quality-Manager from the OU
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public function delete_quality_manager_from_organisation_unit($quality_manager_id);
	
	/**
	 * @return integer
	 */
	public function get_number_of_quality_managers();
	
	/**
	 * @return string Type-Name
	 */
	public function get_type_name();
	
	/**
	 * @return string OU-Name
	 */
	public function get_name();
	
	/**
	 * @return string Icon
	 */
	public function get_icon();
	
	/**
	 * @return integer
	 */
	public function get_master_owner_id();
	
	/**
	 * @return bool Contains-Projects
	 */
	public function get_stores_data();
	
	/**
	 * @return bool Hidden
	 */
	public function get_hidden();
	
	/**
	 * Return the status of an user
	 * @param integer $user_id
	 * @return string status
	 */
	public function get_user_status($user_id);
	
	/**
	 * @return array Array of children
	 */
	public function get_organisation_unit_children();
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_members($limit = 0);
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_groups($limit = 0);
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_leaders($limit = 0);
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_owners($limit = 0);
	
	/**
	 * @param integer $limit
	 * @return array
	 */
	public function list_quality_managers($limit = 0);
	
	/**
	 * @param string $name New OU-Name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
	 * @param bool $hidden
	 * @return bool
	 */
	public function set_hidden($hidden);
	
	/**
	 * @param integer $user_id User-ID
	 * @return integer
	 */
	public static function get_number_of_organisation_units_by_user_id($user_id);
	
	/**
	 * @param integer $group_id Group-ID
	 * @return integer
	 */
	public static function get_number_of_organisation_units_by_group_id($group_id);
	
	/**
	 * @param interger $type_id
	 * @return string Name of the Type
	 */
	public static function get_name_by_type_id($type_id);
	
	/**
	 * Checks if an OU exists
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name);
	
	/**
	 * Checks if an OU exists
	 * @param integer $id Organisation-Unit-ID
	 * @return bool
	 */
	public static function exist_organisation_unit($id);
	
	/**
	 * @return array List of root OUs
	 */
	public static function list_organisation_unit_roots();
	
	/**
	 * @return array List of OUs
	 */
	public static function list_entries();
	
	/**
	 * @param integer $user_id User-ID
	 * @return array List of user OUs
	 */
	public static function list_entries_by_user_id($user_id);
	
	/**
	 * @param integer $group_id Group-ID
	 * @return array List of group OUs
	 */
	public static function list_entries_by_group_id($group_id);
	
	/**
	 * @return array Array of OU-Types
	 */
	public static function list_types();
	
	/**
   	 * @return integer
   	 */
	public static function count_organisation_units();

	/**
	 * Return the recursively created tree
	 * @return array
	 */
	public function get_organisation_unit_tree();
}
?>
