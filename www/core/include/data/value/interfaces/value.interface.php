<?php
/**
 * @package data
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
 * Value Management Interface
 * @package data
 */
interface ValueInterface
{
	/**
	 * @param integer $value_id
	 */
	function __construct($value_id);
	
	function __destruct();
	
	/**
	 * Opens another version of the value with internal revision id
	 * @param integer
	 * @return bool
	 */
	public function open_internal_revision($internal_revision);
	
	/**
	 * Creates a new value
	 * @param integer $folder_id
	 * @param integer $owner_id
	 * @param integer $type_id
	 * @param array $value
	 * @param bool $premature
	 * @return integer
	 */
	public function create($folder_id, $owner_id, $type_id, $value);
	
	/**
	 * Deletes a value
	 * @return bool
	 */
	public function delete();
	
	/**
	 * Deletes a specific value version
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function delete_version($internal_revision);
	
	/**
	 * Returns true, if a value version exists
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function exist_value_version($internal_revision);
	
	/**
	 * Updates the current value
	 * @param array $value_array
	 * @param integer $previous_version_id
	 * @param bool $major
	 * @param bool $current
	 * @param string $full_text_index
	 * @return bool
	 */
	public function update($value_array, $previous_version_id, $major, $current, $full_text_index);
	
	/**
	 * Moves value to another location
	 * @param integer $folder_id
	 * @return bool
	 */
	public function move($folder_id);
	
	/**
	 * Copies value to another location
	 * @param integer $folder_id
	 * @return bool
	 */
	public function copy($folder_id);
	
	/**
	 * Checks if the current value is the current version
	 * @return bool
	 */
	public function is_current();
	
	/**
	 * @return integer
	 */
	public function get_id();
	
	/**
	 * @return integer
	 */
	public function get_value_internal_revisions();
	
	/**
	 * @return string
	 */	
	public function get_type_name();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @return integer
	 */
	public function get_type_id();
	
	/**
	 * @return string (X.Y.Z)
	 */
	public function get_version();
	
	/**
	 * @return integer
	 */
	public function get_internal_revision();
	
	/**
	 * @return string
	 */
	public function get_value();
	
	/**
	 * @return string
	 */
	public function get_checksum();
	
	/**
	 * @return integer
	 */
	public function get_version_owner_id();

	/**
	 * @return integer
	 */
	public function get_version_datetime();
	
	/**
     * @param bool $history
     * @return array
     */
    public function get_value_content($history = false);
    
    /**
     * @return array;
     */
    public function get_value_shape();
	
	/**
	 * @param array $content_array
	 */
	public function set_content_array($content_array);
		
	/**
	 * @return array
	 */
	public static function list_entries_by_type_id($type_id);
	
	/**
	 * Returns true, if a value ($value_id) exists
	 * @param integer $value_id
	 * @return bool
	 */
	public static function exist_value($value_id);
	
	/**
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_value_id_by_data_entity_id($data_entity_id);
	
	
	/**
	 * Sets the owner_id on null, where owner_id = $owner_id
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public static function set_owner_group_id_on_null($owner_group_id);
	
	/**
	 * Checks if an value-id is a type of type-id
	 * @param integer $value_id
	 * @param integer $type_id
	 * @return bool
	 */
	public static function is_entry_type_of($value_id, $type_id);
	
	/**
     * @param integer $value_id
     * @return object
     */
    public static function get_instance($value_id);
}

?>
