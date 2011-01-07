<?php
/**
 * @package project
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
 * Project Task Interface
 * @package project
 */ 	
interface ProjectTaskInterface
{
	function __construct($task_id);
	function __destruct();
	
	public function create_status_process($project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, $end_status_id, $finalise, $auto_connect);
	public function create_process($project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, $name, $auto_connect);
	public function create_milestone($project_id, $user_id, $comment, $date, $time, $name, $auto_connect);
	// private function delete_status_process();
	// private function delete_process_or_milestone();
	public function delete();
	
	public function check_status_process($project_id, $end_status_id, $finalise, $auto_connect);
	
	public function get_type();
	public function get_type_name();
	public function get_project_id();
	public function get_owner_id();
	public function get_comment();
	public function get_start_date();
	public function get_start_time();
	public function get_end_date();
	public function get_end_time();
	public function get_uf_end_time();
	public function get_auto_connect();
	public function get_created_at();
	public function get_name();
	public function get_color();
	public function get_begin_status_id();
	public function get_end_status_id();
	public function get_subtraction_points();
	public function get_finalise();
	public function get_progress();
	public function set_name($name);
	public function set_start($date, $time);
	public function set_end($date, $time, $whole_day);
	public function set_progress($progress);
	
	// private function get_over_time();
	// private function set_over_time($over_time);
	// private function set_finished($finished);
	// private function calc_progress();
	
	public static function list_tasks($project_id);
	public static function list_tasks_by_user_id($user_id);
	public static function check_over_time_tasks($project_id);
	public static function list_upcoming_tasks();
}
?>
