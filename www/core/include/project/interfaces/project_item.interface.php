<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Project Item Interface
 * @package project
 */ 		 
interface ProjectItemInterface
{
	function __construct($project_id);
	function __destruct();
	
	public function link_item();
	public function unlink_item();
	public function unlink_item_full();
	
	public function set_active($active);
	public function set_required($required);
	public function is_active();
	public function is_required();
	
	public function get_project_items();
	public function set_item_id($item_id);
	public function set_gid($gid);
	public function set_status_id($status_id);
	public function set_item_status();
	public function unset_item_status();
	
	//  private function exist_class($class_name);
	public function set_class($class_name);
	public function unset_class();
	
	public function set_information($description, $keywords);
	
	// private function is_item_information();
	// private function is_class_information();
	
	public function is_description();
	public function is_keywords();
	public function is_classified();
	public function create_log_entry();
	
	public static function list_projects_by_item_id($item_id);
}
?>
