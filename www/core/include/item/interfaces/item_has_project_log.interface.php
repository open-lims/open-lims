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
 * Item Project Log Management Interface
 * @package item
 */ 	 
interface ItemHasProjectLogInterface
{
	function __construct($item_id);
	function __destruct();
	
	public function exist_log_entry();
	public function delete();
	public function link_log($project_log_id);
	public function unlink_log($project_log_id);
	
	public static function get_items_by_log_id($log_id);
	public static function delete_by_log_id($log_id);
}

?>
