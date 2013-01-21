<?php
/**
 * @package base
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
 * Date/Time Interface
 * @package base
 */
interface DatetimeHandlerInterface
{
	/**
	 * @param string $input_string 
	 * 
	 * Accepted Inputs
	 * **********************
	 * 1276862128 (microtime)
	 * 2010-06-18
	 * 18-06-2010 (18-06-10 is not allowed)
	 * 2010-06-18 13:55:20
	 * 2010-06-18 13:55:20+02
	 * **********************
	 */
	function __construct($input_string);
	
	function __destruct();
	
	 /**
     * Checks if the date is in the past
     * @return bool
     */
	public function less_then_current();
	
	/**
     * Checks the distance between current- and another-object
     * @param object $date_object
     * @return integer distance in seconds
     */
	public function distance($date_object);
	
	/**
     * Checks the distance between current- and another-object in days
     * @param object $date_object
     * @return integer distance in days
     */
	public function day_distance($date_object);
	
	/**
     * Adds X Days to current object
     * @param integer $day
     * @return bool
     */
	public function add_day($day);
	
	/**
     * Adds X Months to current object
     * @param integer $month
     * @return bool
     */
	public function add_month($month);
	
	 /**
     * Adds X Years to current object
     * @param integer $year
     * @return bool
     */
	public function add_year($year);
	
	/**
     * Adds X Hours to current object
     * @param integer $hour
     * @return bool
     */
	public function add_hour($hour);
	
	/**
     * Adds X Minutes to current object
     * @param integer $minute
     * @return bool
     */
	public function add_minute($minute);
	
	/**
     * Adds X Seconds to current object
     * @param integer $second
     * @return bool
     */
	public function add_second($second);
	
	/**
     * Subs X Days from current object
     * @param integer $day
     * @return bool
     */
	public function sub_day($day);
	
	/**
     * Subs X Months from current object
     * @param integer $month
     * @return bool
     */
	public function sub_month($sub_month);
	
	/**
     * Subs X Years from current object
     * @param integer $year
     * @return bool
     */
	public function sub_year($sub_year);
	
	/**
     * Subs X Hours from current object
     * @param integer $hour
     * @return bool
     */
	public function sub_hour($hour);
	
	/**
     * Subs X Minutes from current object
     * @param integer $minute
     * @return bool
     */
	public function sub_minute($minute);
	
	/**
     * Subs X Seconds from current object
     * @param integer $second
     * @return bool
     */
	public function sub_second($second);
	
	/**
     * @return integer
     */
	public function get_mktime();
	
	/**
     * Returns Timestamp of current object
     * @return string
     */
	public function get_timestamp();
	
	/**
     * Returns date of current object
     * @return string
     */
	public function get_date();
	
	/**
     * Returns time of current object
     * @param bool $display_second
     * @return string
     */
	public function get_time($display_second = true);
	
	/**
	 * Returns regional formatted datetime string
	 * @param bool $display_second
	 * @return string
	 */
	public function get_datetime($display_second = true);
	
	/**
     * Returns a formatted date/time of current object
     * @param string $format Use php-function date() chars for format
     * @param bool $display_second
     * @return string 
     */
	public function get_formatted_string($format = null, $display_second = true);
	
	/**
     * @return integer
     */
	public function get_timezone();
	
	/**
     * @param integer $user_timezone
     * @return bool
     */
	public function set_user_timezone($user_timezone);
}
?>