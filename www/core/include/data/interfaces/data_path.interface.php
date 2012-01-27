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
 * Data Path Interface
 * @package data
 */
interface DataPathInterface
{
	/**
	 * @param integer $folder_id
	 * @param integer $virtual_folder_id
	 */
	function __construct($folder_id, $virtual_folder_id);	
	
	function __destruct();
	
	/**
   	 * @return string
   	 */
	public function get_path();
	
	/**
   	 * Returns the path
   	 * @return string
   	 */
	public function get_stack_path();
	
	/**
   	 * Removes last two entries of the stack an returns the virtual-flag from the first of them
   	 * @return bool
   	 */
	public function get_previous_entry_virtual();
	
	/**
   	 * Removes last two entries of the stack an returns the first of them
   	 * @return integer
   	 */
	public function get_previous_entry_id();
	
	/**
	 * Returns the type of the last entry.
	 * @return bool
	 */
	public function get_last_entry_type();
	
	/**
	 * Returns the id of the last entry.
	 * @return bool
	 */
	public function get_last_entry_id();
	
	/**
	 * Deletes the stack
	 */
	public function delete_stack();
	
	/**
     * @return integer
     */
    public function get_folder_id();
    
    /**
     * @return integer
     */
    public function get_virtual_folder_id();
}
?>
