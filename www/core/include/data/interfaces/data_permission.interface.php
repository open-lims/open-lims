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
 * Data Permission Interface
 * @package data
 */
interface DataPermissionInterface
{	
	/**
	 * @param string type file|value|folder
	 * @param integer $id
	 */
	function __construct($type, $id);
	
	function __destruct();
	
	/**
	 * Returns an array with permission-information
	 * @return array
	 */
	public function get_permission_array();
	
	/**
	 * Sets the array
	 * @param array $array
	 * @return bool
	 */
	public function set_permission_array($array);
	
	/**
	 * @return integer
	 */
	public function get_owner_id();
	
	/**
	 * @return integer
	 */
	public function get_owner_group_id();
	
	/**
	 * @return bool
	 */
	public function get_automatic();
	
	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id);
	
	/**
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public function set_owner_group_id($owner_group_id);
}

?>

