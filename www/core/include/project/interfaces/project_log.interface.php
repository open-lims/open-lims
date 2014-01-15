<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Project Log Interface
 * @package project
 */ 	
interface ProjectLogInterface
{
	/**
	 * @param integer $log_id
	 */
	function __construct($log_id);
	
	function __destruct();
	
	/**
	 * Creates a new log-entry
	 * @param integer $project_id
	 * @param string $content
	 * @param bool $cancel
	 * @param bool $important
	 * @param string $action_checksum
	 * @return integer
	 */
	public function create($project_id, $content, $cancel = false, $important = false);
	
	/**
	 * Deletes a log-entry
	 * @return bool
	 */
	public function delete();
	
	/**
	 * Links a project-status to the current log-entry
	 * @param integer $status_id
	 * @return bool
	 */
	public function link_status($status_id);
	
	/**
	 * Returns a given project-status linked to the current log-entry
	 * @return integer
	 */
	public function get_status_id();
	
	/**
	 * @return string
	 */
	public function get_datetime();
	
	/**
	 * @return string
	 */
	public function get_content();
	
	/**
	 * @return bool
	 */
	public function get_cancel();
	
	/**
	 * @return bool
	 */
	public function get_important();
	
	/**
	 * @return integer
	 */
	public function get_owner_id();
	
	/**
	 * @param string $content
	 * @return bool
	 */
	public function set_content($content);
	
	/**
	 * @param bool $important
	 * @return bool
	 */
	public function set_important($important);
	
	/**
	 * @return array
	 */
	public function list_items();
	
	/**
	 * @param integer $project_id
	 * @return array
	 */
	public static function list_entries_by_project_id($project_id);
}
?>
