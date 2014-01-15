<?php
/**
 * @package data
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
 * File Management Interface
 * @package data
 */
interface FileInterface
{
	/**
	 * @param integer $file_id
	 */
	function __construct($file_id);
	
	function __destruct();
	
	/**
	 * Opens another version of the file with internal revision id
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function open_internal_revision($internal_revision);
	
	/**
	 * Creates a new file
	 * @param string $name
	 * @param integer $folder_id
	 * @param string $path
	 * @param integer $owner_id
	 * @param bool $premature
	 * @return integer
	 */
	public function create($name, $folder_id, $path, $owner_id);
	
	/**
	 * Deletes a file, including all versions
	 * @return bool
	 */
	public function delete();
	
	/**
	 * Deletes a specific file version
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function delete_version($internal_revision);
	
	/**
	 * Returns true, if a file version exists
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function exist_file_version($internal_revision);
	
	/**
	 * Uploads a stack of files
	 * @param integer $file_amount
	 * @param integer $folder_id
	 * @param array $file_array
	 * @param string $unique_id
	 * @return bool
	 */
	public function upload_file_stack($file_amount, $folder_id, $file_array, $unique_id);
	
	/**
	 * Uploads a file
	 * @param integer $folder_id
	 * @param array $file_array
	 * @return integer
	 */
	public function upload_file($folder_id, $file_array);
	
	/**
	 * Updates a file
	 * @param array $file_array
	 * @param integer $previous_version_id
	 * @param bool $major
	 * @param bool $current
	 * @return integer
	 */
	public function update_file($file_array, $previous_version_id, $major, $current);
	
	/**
	 * Moves file to another location
	 * @param integer $folder_id
	 * @return bool
	 */
	public function move($folder_id);
	
	/**
	 * Copies file to another location
	 * Copies the current version only.
	 * @param integer $folder_id
	 * @return bool
	 */
	public function copy($folder_id, $all_versions = false);
	
	/**
	 * Checks if the current opened file version is the current file version
	 * @return bool
	 */
	public function is_current();
	
	/**
	 * Checks if the file is an image
	 * @return bool
	 */
	public function is_image();
	
	/**
	 * Returns the content of a file
	 * @return string
	 */
	public function get_file_content();
	
	/**
	 * Retruns an array of internal revisions
	 * @return array
	 */
	public function get_file_internal_revisions();
	
	/**
	 * @return array
	 */
	public function get_item_id_array();
		
	/**
	 * @return string
	 */
	public function get_icon();
	
	/**
	 * @return string
	 */
	public function get_mime_type();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @return integer
	 */
	public function get_version();
	
	/**
	 * @return integer
	 */
	public function get_internal_revision();
	
	/**
	 * @return string
	 */
	public function get_version_datetime();
	
	/**
	 * @return integer
	 */
	public function get_size();
	
	/**
	 * @return string
	 */
	public function get_checksum();
	
	/**
	 * @return integer
	 */
	public function get_file_version_id();
	
	
	/**
	 * Returns true, if a file exists
	 * @param integer $file_id
	 * @return bool
	 */
	public static function exist_file($file_id);
	
	/**
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_file_id_by_data_entity_id($data_entity_id);
	
	/**
	 * @param string $name
	 * @return string
	 */
	public static function get_icon_by_name($name);
	
	/**
     * @param integer $file_id
     * @return object
     */
    public static function get_instance($file_id);
}
?>
