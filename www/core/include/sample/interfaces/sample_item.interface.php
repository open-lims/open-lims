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
 * Sample Item Management Interface
 * @package sample
 */ 		 
interface SampleItemInterface
{
	function __construct($sample_id);
	function __destruct();
	
	public function link_item();
	public function unlink_item();
	public function unlink_item_full();
	public function get_sample_items();
	
	public function set_item_id($item_id);
	public function set_gid($gid);
	
	// private function exist_class($class_name);
	public function set_class($class_name);
	public function unset_class();
	public function set_information($description, $keywords);
	// private function is_item_information();
	// private function is_class_information();
	public function is_description();
	public function is_keywords();
	public function is_classified();
}

?>
