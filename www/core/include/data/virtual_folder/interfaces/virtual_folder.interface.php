<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Virtual Folder Management Interface
 * @package data
 */
interface VirtualFolderInterface
{
	/**
	 * @param integer $virtual_folder_id
	 */
	function __construct($virtual_folder_id);
	
	function __destruct();
	
	/**
	 * Creates a new Virtual-Folder in a given Folder
	 * @param integer $folder_id
	 * @param string $name
	 * @return integer
	 */
	public function create($folder_id, $name);
	
	/**
	 * Deletes a Virtual-Folder
	 * @return bool
	 */
	public function delete($recursive, $content);
	
	/**
	 * Adds a given Folder to the current Virtual-Folder
	 * @param integer $folder_id
	 * @return bool
	 */
	public function link_folder($folder_id);
	
	/**
	 * Removes a given Folder from the current Virtual-Folder
	 * @param integer $folder_id
	 * @return bool
	 */
	public function unlink_folder($folder_id);
	
	/**
	 * @return string
	 */
	public function get_name();

	/**
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_virtual_folder_id_by_data_entity_id($data_entity_id);
	
	/**
	 * @param integer $virtual_folder_id
	 * @return bool
	 */
	public static function exist_vfolder($virtual_folder_id);
	
	/**
	 * @param integer $folder_id
	 * @return array
	 */
	public static function list_entries_by_folder_id($folder_id);
}

?>
