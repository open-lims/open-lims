<?php
/**
 * @package item
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
 * Item Information Management Interface
 * @package item
 */ 	 
interface ItemInformationInterface
{
	function __construct($item_information_id);
	function __destruct();
	
	public function create($description, $keywords);
	public function delete();
	
	public function link_class($class_id);
	public function unlink_class($class_id);
	public function link_item($item_id);
	public function unlink_item($item_id);
	
	public static function list_class_information($class_id);
	public static function list_item_information($item_id);
}

?>
