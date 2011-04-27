<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
	public function is_flag_change_permission();
	public function is_flag_add_folder();
	public function is_flag_cmd_folder();
	public function is_flag_rename_folder();
	
	// public function create($name, $toid, $root, $path, $owner_id, $owner_group_id);
	
	public function exist_folder();
	public function exist_subfolder_name($name);
	
	public function delete($recursive, $content);
	public function mark_as_deleted();
	public function mark_as_undeleted();
	
	public function move_folder($destination_id, $force_exist_check);
	public function copy_folder($destination_id, $force_exist_check);
	
	public function get_object_path();
	public function get_object_id_path();
	public function get_name();
	public function get_path();
	
	public function set_name($name);
	public function set_flag($flag);
	
	public function get_subfolder_array();
	public function is_folder_image_content();
	
	public static function get_folder_by_path($path);
	public static function list_folder();
}

?>
