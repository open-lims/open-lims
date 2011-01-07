<?php
/**
 * @package base
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
 * System Log Management Interface
 * @package base
 */
interface SystemLogInterface
{
	function __construct($log_id);
	function __destruct();
	
	public function create($user_id, $type_id, $content_int, $content_string, $content_errorno, $file, $line, $link);
	
	public function get_user_id();
	public function get_datetime();
	public function get_ip();
	public function get_content_int();
	public function get_content_string();
	public function get_file();
	public function get_line();
	public function get_link();
	public function get_stack_trace();
	
	public function set_stack_trace($stack_trace);
	
	public static function set_user_id_on_null($user_id);
	public static function list_types();
	public static function get_type_name($id);
	public static function exist_id($id);
	public static function exist_ip($ip);
	public static function count_ip_failed_logins($ip);
	public static function count_ip_successful_logins($ip);
	public static function list_ip_users($ip);
}
?>