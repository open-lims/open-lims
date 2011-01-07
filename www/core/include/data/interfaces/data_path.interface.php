<?
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
 * Data Path Interface
 * @package data
 */
interface DataPathInterface
{
	function __construct($folder_id, $virtual_folder_id);	
	function __destruct();
	
	// private function clear_stack();
	// private function check_stack();
	// private function init_stack($folder_id);
	// private function push_folder_id($folder_id);
	// private function push_virtual_folder_id($virtual_folder_id);
	
	public function init_project_folder($project_id);
	public function init_sample_folder($sample_id);
	
	public function get_path();
	public function get_stack_path();
	public function get_previous_entry_virtual();
	public function get_previous_entry_id();
	public function get_last_entry_type();
	public function get_last_entry_id();
	public function delete_stack();
}
?>
