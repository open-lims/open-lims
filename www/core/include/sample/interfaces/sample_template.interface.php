<?php
/**
 * @package sample
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
 * Sample Template Interface
 * @package sample
 */ 	 
interface SampleTemplateInterface
{
	function __construct($sample_template_id);
	function __destruct();
	
	public function create($object_id, $category_id);
	public function delete();
	
	public function get_name();
	public function get_cat_id();
	
	public function is_required_requirements();
	public function get_required_requirements();
	public function get_requirements();
	public function get_gid_attributes($gid);
	public function get_gid_item($gid);
	public function get_class($class_name);
	
	public static function exist_id($id);
	public static function list_entries_by_cat_id($cat_id);
	public static function list_entries();	
}
?>
