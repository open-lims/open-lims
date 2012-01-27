<?php
/**
 * @package sample
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
 * Sample Interface
 * @package sample
 */ 		 
interface SampleInterface
{
	/**
	 * @param integer $sample_id Sample-ID
	 */
	function __construct($sample_id);
	
	function __destruct();
	
	/**
     * Sets tempalte-date before sample creation
     * @param string $type
     * @param integer $type_id
     * @param array $array
     * @return bool
     */
	public function set_template_data($type, $type_id, $array);
	
	/**
     * Creates a new sample
     * @param integer $organisation_unit_id
     * @param integer $template_id
     * @param string $name
     * @param string $supplier
     * @param integer $location_id
     * @param string $desc
     * @return integer Sample-ID
     * @throws SampleCreationFailedException
     */
	public function create($organisation_unit_id, $template_id, $name, $manufacturer_id, $location_id, $desc, $language_id, $date_of_expiry, $expiry_warning);
	
	/**
	 * Deletes a sample
	 * @return bool
	 */
	public function delete();
	
	/**
	 * Returns all requirements
	 * @return array
	 */
	public function get_requirements();
	
	/**
     * Returns fulfilled requirements
     * @return array
     */
	public function get_fulfilled_requirements();
	
	 /**
     * Returns subfolder of a given gid
     * @param integer $folder_id Folder-ID
     * @param integer $gid 
     * @return string Sub-Folder-Path
     */
	public function get_sub_folder($folder_id, $gid);
	
	/**
     * Adds a new location to the current sample
     * @param integer location_id
     * @return bool
     */
	public function add_location($location_id);
	
	/**
     * Returns current location
     * @return integer
     */
	public function get_current_location();	
	
	/**
     * @return string
     */
	public function get_name();
	
	/**
     * @return string
     */
	public function get_datetime();
	
	/**
     * @return integer
     */
	public function get_owner_id();
	
	/**
     * @return string
     */
	public function get_manufacturer_id();
	
	/**
     * @return integer
     */
	public function get_template_id();
	
	/**
	 * @return bool
	 */
	public function get_availability();
	
	/**
	 * @return bool
	 */
	public function get_date_of_expiry();
	
	/**
	 * Returns the name of the current location
	 * @return string
	 */
	public function get_current_location_name();
	
	/**
	 * Returns the name of template
	 * @return string
	 */
	public function get_template_name();
	
	/**
	 * Returns the ID of the current sample as S000000X
	 * @return string
	 */
	public function get_formatted_id();
	
	/**
	 * @return integer
	 */
	public function get_organisation_unit_id();
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id);
	
	/**
	 * @param string $supplier
	 * @return bool
	 */
	public function set_manufacturer_id($manufacturer_id);
	
	/**
	 * @param bool $availability
	 * @return bool
	 */
	public function set_availability($availability);
	
	/**
	 * Returns true if a sample exists
	 * @param integer $sample_id
	 * @return bool
	 */
	public static function exist_sample($sample_id);
	
	/**
   	 * Lists all user-related samples
   	 * @param integer $user_id
   	 * @return array
   	 */
	public static function list_user_related_samples($user_id);
	
	/**
     * Lists all OU related samples
     * @param integer $organisation_unit_id
     * @return array
     */
	public static function list_organisation_unit_related_samples($organisation_unit_id);
	
	/**
   	 * @param integer $template_id
   	 * @return array
   	 */
    public static function list_entries_by_template_id($template_id);
}
?>
