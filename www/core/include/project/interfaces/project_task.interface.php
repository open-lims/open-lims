<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Project Task Interface
 * @package project
 */ 	
interface ProjectTaskInterface
{
	/**
	 * @param integer $task_id
	 */
	function __construct($task_id);
	
	function __destruct();
	
	/**
     * Creates a new status-related task
     * @param integer $project_id
     * @param integer $user_id
     * @param string $comment
     * @param string $start_date
     * @param string $start_time
     * @param string $end_date
     * @param string $end_time
     * @param bool $whole_day
     * @param integer $end_status_id
     * @param bool $finalise
     * @param bool $auto_connect
     * @return integer
     */
	public function create_status_process($project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, $end_status_id, $finalise, $auto_connect);
	
	/**
     * Creates a new process
     * @param integer $project_id
     * @param integer $user_id
     * @param string $comment
     * @param string $start_date
     * @param string $start_time
     * @param string $end_date
     * @param string $end_time
     * @param bool $whole_day
     * @param string $name
     * @param bool $auto_connect
     * @return integer
     */
	public function create_process($project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, $name, $auto_connect);
	
	/**
     * Creates a new milestone
     * @param integer $project_id
     * @param integer $user_id
     * @param string $date
     * @param string $time
     * @param string $name
     * @param bool $auto_connect
     * @return integer
     */
	public function create_milestone($project_id, $user_id, $comment, $date, $time, $name, $auto_connect);

	/**
     * Public Delete Method
     * @return bool
     */
	public function delete();
	
	/**
     * @param integer $project_id
     * @param integer $end_status_id
     * @param bool $finalise
     * @param bool $auto_connect
     * @return bool
     */
	public function check_status_process($project_id, $end_status_id, $finalise, $auto_connect);
	
	/**
     * @return integer
     */
	public function get_type();
	
	/**
     * @return string
     */
	public function get_type_name();
	
	/**
     * @return integer
     */
	public function get_project_id();
	
	/**
     * @return integer
     */
	public function get_owner_id();
	
	/**
     * @return string
     */
	public function get_comment();
	
	/**
     * @return string
     */
	public function get_start_date();
	
	/**
     * @return string
     */
	public function get_start_time();
	
	/**
     * @return string
     */
	public function get_end_date();
	
	/**
     * @return string
     */
	public function get_end_time();
	
	/**
     * @return string
     */
	public function get_uf_end_time();
	
	/**
     * @return bool
     */
	public function get_auto_connect();
	
	/**
     * @return string
     */
	public function get_created_at();
	
	/**
     * @return string
     */
	public function get_name();
	
	/**
     * @return string
     */
	public function get_color();
	
	/**
     * @return integer
     */
	public function get_begin_status_id();
	
	/**
     * @return integer
     */
	public function get_end_status_id();
	
	/**
     * @return integer
     */
	public function get_subtraction_points();
	
	/**
     * @return bool
     */
	public function get_finalise();
	
	/**
 	 * @return float
 	 */
	public function get_progress();
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
     * @param string $date
     * @param string $time
     * @return bool
     */
	public function set_start($date, $time);
	
	/**
     * @param string $date
     * @param string $time
     * @param bool $whole_day
     * @return bool
     */
	public function set_end($date, $time, $whole_day);
	
	/**
     * @param float $progress
     * @return bool
     */
	public function set_progress($progress);

	/**
     * @param integer $project_id
     * @return array
     */
	public static function list_tasks($project_id);
	
	/**
     * Checks all overtime tasks of an project
     * @param integer $project_id
     * @return bool
     */
	public static function check_over_time_tasks($project_id);
	
	/**
     * Lists all upcoming tasks
     * @return array
     */
	public static function list_upcoming_tasks();
}
?>
