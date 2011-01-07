<?php
/**
 * @package project
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
 * Project Template Interface
 * @package project
 */ 
interface ProjectTemplateInterface
{
	function __construct($project_template_id);
	function __destruct();
	
	public function create($object_id, $category_id, $parent_template);
	public function delete();
	
	public function get_name();
	public function get_parent_template();
	public function is_required_requirements();
	public function get_required_requirements();
	public function get_status_requirements($status_id);
	public function get_all_status();
	public function get_next_status($status_id);
	public function get_gid_attributes($gid, $status_id);
	public function get_status_attributes($status_id);
	public function get_status_item($gid, $status_id);
	public function get_status_class($status_id, $class_name);
	
	public static function exist_id($id);
	public static function list_entries();
	public static function list_entries_by_cat_id($cat_id);
}
?>
