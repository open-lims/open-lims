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
 * Date/Time Interface
 * @package base
 */
interface DatetimeHandlerInterface
{
	function __construct($input_string);
	function __destruct();
	
	public function less_then_current();
	public function distance($date_object);
	public function day_distance($date_object);
	
	public function add_day($day);
	public function add_month($month);
	public function add_year($year);
	public function add_hour($hour);
	public function add_minute($minute);
	public function add_second($second);
	
	public function sub_day($day);
	public function sub_month($sub_month);
	public function sub_year($sub_year);
	public function sub_hour($hour);
	public function sub_minute($minute);
	public function sub_second($second);
	
	public function get_mktime();
	public function get_timestamp();
	public function get_date();
	public function get_time();
	public function get_formatted_string($format);
	public function get_timezone();
	
	public function set_user_timezone($user_timezone);
}
?>