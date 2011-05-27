<?php
/**
 * @package data
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
 * Data Entity Interface
 * @package data
 */
interface DataEntityInterface
{	
	/**
	 * @return bool
	 */
	public function is_read_access();
	
	/**
	 * @return bool
	 */
	public function is_write_access();
	
	/**
	 * @return bool
	 */
	public function is_delete_access();
	
	/**
	 * @return bool
	 */
	public function is_control_access();
	
	/**
	 * @return bool
	 */
	public function can_set_automatic();
	
	/**
	 * @return bool
	 */
	public function can_set_data_entity();
	
	/**
	 * @return bool
	 */
	public function can_set_control();
	
	/**
	 * @return bool
	 */
	public function can_set_remain();
	
	/**
	 * @return integer
	 */
	public function get_parent_folder();
	
	/**
	 * @return integer
	 */
	public function get_parent_folder_id();
	
	/**
	 * @return array
	 */
	public function get_parent_virtual_folders();
	
	/**
	 * @return array
	 */
	public function get_parent_virtual_folder_ids();
	
	/**
	 * @return array
	 */
	public function get_childs();
	
	/**
	 * @return integer
	 */
	public function get_data_entity_id();
	
	/**
	 * @return string
	 */
	public function get_datetime();
	
	/**
	 * @return integer
	 */
	public function get_owner_id();
	
	/**
	 * @return integer
	 */
	public function get_owner_group_id();
	
	/**
	 * @return integer
	 */
	public function get_permission();
	
	/**
	 * @return string
	 */
	public function get_permission_string();
	
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
	
	/**
	 * @param integer $permission
	 * @return bool
	 */
	public function set_permission($permission);
	
	/**
	 * @param bool $automatic
	 * @return bool
	 */
	public function set_automatic($automatic);
	
	/**
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public function set_as_child_of($data_entity_id);
	
	/**
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public function unset_child_of($data_entity_id);
	
	/**
	 * @return bool
	 */
	public function unset_childs();
}

?>
