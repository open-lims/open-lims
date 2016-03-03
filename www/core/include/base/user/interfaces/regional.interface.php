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
 * Regional Interface
 * @package base
 */
interface RegionalInterface
{
	/**
	 * @param integer $user_id
	 */
	public function __construct($user_id = null);
	
	/**
	 * @return integer
	 */
	public function get_language_id();
	
	/**
	 * @return integer
	 */
	public function get_timezone_id();
	
	/**
	 * @return array
	 */
	public function get_time_display_format();
	
	/**
	 * @return bool
	 * true = 24 hr
	 * false = 12 hr
	 */
	public function get_time_enter_format();
	
	/**
	 * @return string
	 * d.m.Y
	 * Y.m.d
	 * d-m-Y
	 * m-d-Y
	 * Y-m-d
	 * d/m/Y
	 * m/d/Y
	 * jS M Y
	 * d. M Y
	 */
	public function get_date_display_format();
	
	/**
	 * @return string
	 * dd.mm.YYYY
	 * YYYY.mm.dd
	 * dd-mm-YYYY
	 * mm-dd-YYYY
	 * YYYY-mm-dd
	 * dd/mm/YYYY
	 * mm/dd/YYYY
	 */
	public function get_date_enter_format();
	
	/**
	 * @return string
	 * dd.mm.YYYY
	 * YYYY.mm.dd
	 * dd-mm-YYYY
	 * mm-dd-YYYY
	 * YYYY-mm-dd
	 * dd/mm/YYYY
	 * mm/dd/YYYY
	 */
	public function get_jquery_date_enter_format();
	
	/**
	 * @return string
	 */
	public function get_php_date_enter_format();
	
	/**
	 * @return integer
	 */
	public function get_country_id();
	
	/**
	 * @return string
	 * metric
	 * angloamerican
	 */
	public function get_system_of_units();
	
	/**
	 * @return string
	 * din
	 * angloamerican
	 */
	public function get_system_of_paper_format();
	
	/**
	 * @return integer
	 */
	public function get_currency_id();
	
	/**
	 * @return integer
	 */
	public function get_currency_significant_digits();
	
	/**
	 * @return string
	 */
	public function get_decimal_separator();
	
	/**
	 * @return string
	 */
	public function get_thousand_separator();
	
	/**
	 * @return string
	 */
	public function get_name_display_format();
	
	/**
	 * Returns a formatted string with decimal- and thousand-separator
	 * @param float $number
	 * @param integer $decimal
	 * @param boolean $cut_decimal
	 * @return string
	 */
	public function format_number($number, $decimal = 10, $cut_decimal = true);
	
	/**
	 * @param integer $language_id
	 * @return boolean
	 */
	public function set_language_id($language_id);
	
	/**
	 * @param integer $timezone_id
	 * @return boolean
	 */
	public function set_timezone_id($timezone_id);
	
	/**
	 * @param boolean $time_display_format
	 * @return boolean
	 */
	public function set_time_display_format($time_display_format);
	
	/**
	 * @param boolean $time_enter_format
	 * @return boolean
	 */
	public function set_time_enter_format($time_enter_format);
	
	/**
	 * @param string $date_display_format
	 * @return boolean
	 */
	public function set_date_display_format($date_display_format);
	
	/**
	 * @param string $date_enter_format
	 * @return boolean
	 */
	public function set_date_enter_format($date_enter_format);
	
	/**
	 * @param integer $country_id
	 * @return boolean
	 */
	public function set_country_id($country_id);
	
	/**
	 * @param string $system_of_units
	 * @return boolean
	 */
	public function set_system_of_units($system_of_units);
	
	/**
	 * @param string $system_of_paper_format
	 * @return boolean
	 */
	public function set_system_of_paper_format($system_of_paper_format);
	
	/**
	 * @param integer $currency_id
	 * @return boolean
	 */
	public function set_currency_id($currency_id);
	
	/**
	 * @param integer $currency_significant_digits
	 * @return boolean
	 */
	public function set_currency_significant_digits($currency_significant_digits);
	
		/**
	 * @param string $decimal_separator
	 * @return boolean
	 */
	public function set_decimal_separator($decimal_separator);
	
	/**
	 * @param string $thousand_separator
	 * @return boolean
	 */
	public function set_thousand_separator($thousand_separator);
	
	/**
	 * @param string $name_display_format
	 * @return boolean
	 */
	public function set_name_display_format($name_display_format);
}
?>