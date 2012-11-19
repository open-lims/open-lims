<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Data Entity Interface
 * @package data
 */
interface DataEntityPermissionInterface
{	
	/**
	 * @param integer $permission
	 * @param bool $automatic
	 * @param integer $owner_id
	 * @param integer $owner_group_id
	 */
	function __construct($permission, $automatic, $owner_id, $owner_group_id);
	
	function __destruct();
	
	public function set_read_permission();
	
	public function set_write_permission();
		
	/**
	 * @param integer $intention
	 * 	1 = read
	 * 	2 = write
	 * 	3 = delete
	 * 	4 = control
	 * @return bool
	 */	
	public function is_access($intention);
	
	/**
	 * Returns the permission string; like: rwdc----r---r---
	 * @return string
	 */	
	public function get_permission_string();
}

?>
