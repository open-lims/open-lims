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
 * Value Management Interface
 * @package data
 */
interface ValueInterface
{
	function __construct($value_id);
	function __destruct();
	
	public function open_internal_revision($internal_revision);
	// private function open_value_version_id($value_version_id);
	
	public function create($folder_id, $owner_id, $type_id, $value);
	public function delete();
	public function delete_version($internal_revision);
	
	public function exist_value_version($internal_revision);
	public function update($value_array, $previous_version_id, $major, $current, $full_text_index);
	
	public function move($folder_id);
	public function copy($folder_id);
	public function is_current();
	
	public function get_value_internal_revisions();
	public function get_type_name();
	public function get_type_id();
	public function get_version();
	public function get_internal_revision();
	public function get_value();
	public function get_checksum();
	
	// private function array_contains_each_statements($xml_array);
	// private function resolve_each_statements($xml_array);
	public function get_html_form($error_array, $type_id);
	
	public function set_content_array($content_array);
	public function set_autofield_array_string($autofield_array_string);
	
	public static function list_entries_by_type_id($type_id);
	public static function exist_value($value_id);
}

?>
