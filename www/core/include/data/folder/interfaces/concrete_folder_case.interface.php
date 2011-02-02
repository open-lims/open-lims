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
 * Folder Management Interface
 * @package data
 */
interface ConcreteFolderCaseInterface
{
	function __construct($folder_id);
	function __destruct();

	public function is_read_access();
	public function is_write_access();
	public function is_delete_access();
	public function is_control_access();
	
	public function is_flag_change_permission();
	public function is_flag_add_folder();
	public function is_flag_cmd_folder();
	public function is_flag_rename_folder();
	
	public function delete($recursive, $content);
	
	public static function is_case($folder_id);
}

?>
