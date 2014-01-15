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
 * Project Task Point Interface
 * @package project
 */ 	
interface ProjectTaskPointInterface
{
	/**
	 * @param integer $project_id
	 */
	function __construct($project_id);
	
	function __destruct();
	
	/**
     * Returns the achived points of a given status
     * @param integer $status_id
     * @param string $datetime
     * @return integer
     */
	public function get_status_achieved_points($status_id, $datetime);
	
	/**
     * Returns the currently achieved points
     * @param string $datetime
     * @return integer
     */
	public function get_current_achieved_points($datetime);
	
	/**
     * Returns the maximum of points of a given status
     * @param integer $status_id
     * @return integer
     */
	public function get_status_max_points($status_id);
	
	/**
     * Returns the maximium-points of a given task
     * @param integer $task_id
     * @return integer
     */
	public function get_task_max_points($task_id);
	
	/**
     * Returns the achieved-points of a given task
     * @param integer $task_id
     * @return integer
     */
	public function get_task_achieved_points($task_id);
}
?>
