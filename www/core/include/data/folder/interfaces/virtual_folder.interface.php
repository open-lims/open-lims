<?php
/**
 * @package data
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
 * Virtual Folder Management Interface
 * @package data
 */
interface VirtualFolderInterface
{
	function __construct($virtual_folder_id);
	function __destruct();
	
	public function create($folder_id, $name);
	public function delete();
	
	public function link_folder($folder_id);
	public function unlink_folder($folder_id);

	public function is_project_vfolder();
	public function is_sample_vfolder();
	
	public function get_subfolder_array();
	public function get_name();
	public function get_datetime();
	
	public function set_project_vfolder();
	public function set_sample_vfolder();

	public static function exist_vfolder($virtual_folder_id);
	public static function list_entries_by_folder_id($folder_id);
}

?>
