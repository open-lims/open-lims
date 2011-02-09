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
 * File Management Interface
 * @package data
 */
interface FileInterface
{
	function __construct($file_id);
	function __destruct();
	
	// private function compare_with_current_version($checksum);
	public function open_internal_revision($internal_revision);
	// private function open_file_version_id($file_version_id)
	
	public function create($name, $folder_id, $path, $owner_id);
	public function delete();
	
	public function delete_version($internal_revision);
	
	public function upload_file_stack($file_amount, $folder_id, $file_array, $unique_id);
	public function upload_file($folder_id, $file_array);
	public function update_file($file_array, $previous_version_id, $major, $current);
	
	public function move($folder_id);
	public function copy($folder_id);
	
	public function is_current();
	public function is_image();
	
	public function get_file_content();
	
	public function get_file_internal_revisions();
	
	public function get_item_id_array();
	
	public function get_icon();
	public function get_mime_type();
	
	public function get_name();
	public function get_version();
	public function get_internal_revision();
	public function get_version_datetime();
	public function get_size();
	public function get_checksum();

	public static function exist_file($file_id);
	public static function get_icon_by_name($name);
}
?>
