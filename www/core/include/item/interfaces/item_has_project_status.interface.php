<?php
/**
 * @package item
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
 * Item Project Status Management Interface
 * @package item
 */ 	 
interface ItemHasProjectStatusInterface
{
	function __construct($item_id, $status_id);
	function __destruct();
	
	public function create($item_id, $status_id);
	public function delete();
	public function is_object();
	public function is_method();
	public function is_sample();
	
	public function get_gid();
	public function set_gid($gid);
	
	public static function get_status_id_by_item_id($item_id);
}

?>
