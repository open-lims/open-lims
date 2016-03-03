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
 * Folder Management Interface
 * @package data
 */
interface FolderInterface
{
	/**
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_change_permission($inherit = false);
	
	/**
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_add_folder($inherit = false);
	
	/**
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_command_folder($inherit = false);
	
	/**
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_rename_folder($inherit = false);
	
	/**
	 * @return bool
	 */
	public function exist_folder();
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function exist_subfolder_name($name);
	
	/**
	 * Deletes a folder including sub-folders and content
	 * @param bool $recursive
	 * @param bool $content
	 * @return bool
	 */
	public function delete($recursive, $content);
	
	/**
	 * @return bool
	 */
	public function mark_as_deleted();
	
	/**
	 * @return bool
	 */
	public function mark_as_undeleted();
	
	/**
	 * Moves a folder to another location
	 * @param integer $destination_id
	 * @param bool $force_exist_check
	 * @return bool
	 */
	public function move_folder($destination_id, $force_exist_check);
	
	/**
	 * Copies a folder to another location
	 * @param integer $destination_id
	 * @return bool
	 */
	public function copy_folder($destination_id, $force_exist_check);
	
	/**
	 * @return string
	 */
	public function get_object_path();
	
	/**
	 * @return integer
	 */
	public function get_object_id_path();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @return string
	 */
	public function get_path();
	
	/**
	 * @param integer $user_id
	 * @param integer $filesize
	 * @return bool
	 */
	public function get_quota_access($user_id, $filesize);
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
	 * @param integer $flag
	 * @return bool
	 */
	public function set_flag($flag);
	
	/**
	 * @param integer $user_id
	 * @param integer $filesize
	 * @return bool
	 */
	public function increase_filesize($user_id, $filesize);
	
	/**
	 * @param integer $user_id
	 * @param integer $filesize
	 * @return bool
	 */
	public function decrease_filesize($user_id, $filesize);
	
	/**
	 * Returns an array with all subfolders
	 * @return array
	 */
	public function get_subfolder_array();
	
	/**
	 * Checks if images are in the current folder
	 * @return bool
	 */
	public function is_folder_image_content();
	
	/**
	 * @param string $path
	 * @return integer
	 */
	public static function get_folder_by_path($path);
	
	/**
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_folder_id_by_data_entity_id($data_entity_id);
	
	/**
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_data_entity_id_by_folder_id($folder_id);
	
	/**
	 * Returns folder name by id without create an instance (due to performance reasons)
	 * @param integer $folder_id
	 * @return string
	 */
	public static function get_name_by_id($folder_id);
	
	/**
	 * @param string $type
	 * @param string $handling_class
	 * @param integer $include_id
	 * @return bool
	 */
	public static function register_type($type, $handling_class, $include_id);
	
	/**
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_type_by_include_id($include_id);
	
	/**
	 * @return array
	 */
	public static function list_folder();
	
	/**
     * Returns a new instance of Folder or of one of its child
     * @param integer $folder_id
     * @return object
     */
    public static function get_instance($folder_id);
}

?>
