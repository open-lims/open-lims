<?php
/**
 * @package base
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
 * System Log Management Interface
 * @package base
 */
interface SystemLogInterface
{
	/**
	 * @param integer $log_id
	 */
	function __construct($log_id);
	
	function __destruct();
	
	/**
	 * Creates a new entry
	 * @param integer $user_id
	 * @param integer $type_id
	 * @param integer $content_int
	 * @param string $content_string
	 * @param string $content_errorno
	 * @param string $file
	 * @param integer $line
	 * @param string $link
	 * @return integer
	 */
	public function create($user_id, $type_id, $content_int, $content_string, $content_errorno, $file, $line, $link);
	
	/**
	 * @return integer
	 */
	public function get_user_id();
	
	/**
	 * @return string
	 */
	public function get_datetime();
	
	/**
	 * @return string
	 */
	public function get_ip();
	
	/**
	 * @return integer
	 */
	public function get_content_int();
	
	/**
	 * @return string
	 */
	public function get_content_string();
	
	/**
	 * @return string
	 */
	public function get_file();
	
	/**
	 * @return integer
	 */
	public function get_line();
	
	/**
	 * @return string
	 */
	public function get_link();
	
	/**
	 * @return string
	 */
	public function get_stack_trace();
	
	/**
	 * @param string $stack_trace
	 * @return bool
	 */
	public function set_stack_trace($stack_trace);
	
	/**
	 * Sets all entries of an user on null (during user-delete)
	 * @param integer $user_id
	 * @return bool
	 */
	public static function set_user_id_on_null($user_id);
	
	/**
	 * @return array
	 */
	public static function list_types();
	
	/**
	 * @param integer $id
	 * @return string
	 */
	public static function get_type_name($id);
	
	/**
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id);
	
	/**
	 * @param string $ip
	 * @return bool
	 */
	public static function exist_ip($ip);
	
	/**
	 * @param string $ip
	 * @param string $begin
	 */
	public static function count_ip_failed_logins_with_begin($ip, $lead_time);
	
	/**
	 * Counts all failed logins with $ip
	 * @param string $ip
	 * @return integer
	 */
	public static function count_ip_failed_logins($ip);
	
	/**
	 * Counts all successful logins with $ip
	 * @param string $ip
	 * @return integer
	 */
	public static function count_ip_successful_logins($ip);
	
	/**
	 * Returns an array with all users who have loged in with $ip
	 * @param string $ip
	 * @return array
	 */
	public static function list_ip_users($ip);
}
?>