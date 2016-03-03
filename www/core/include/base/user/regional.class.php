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
 * 
 */
require_once("interfaces/regional.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/user_regional_setting.access.php");
}

/**
 * Regional Class
 * Handles Languages and Timezones
 * @package base
 * @todo put methods into their classes and create new methods for information about the current loged on user
 */
class Regional implements RegionalInterface
{
	private $user_regional_setting;
	
	/**
	 * @see RegionalInterface::__construct()
	 * @param integer $user_id
	 */
	public function __construct($user_id = null)
	{
		global $user;
		
		if (is_numeric($user_id))
		{
			$this->user_regional_setting = new UserRegionalSetting_Access($user_id);
		}
		else
		{
			if (is_object($user))
			{
				$user_id = $user->get_user_id();
				$this->user_regional_setting = new UserRegionalSetting_Access($user_id);
			}
		}
	}
	
	/**
	 * @see RegionalInterface::get_language_id()
	 * @return integer
	 */
	public function get_language_id()
	{
		if ($this->user_regional_setting)
		{
			$language_id = $this->user_regional_setting->get_language_id();
			if (is_numeric($language_id))
			{
				return $language_id;
			}
			else
			{
				return 1;
			}
		}
		else
		{
			return 1;
		}
	}
	
    /**
     * @see RegionalInterface::get_timezone_id()
	 * @return integer
	 */
	public function get_timezone_id()
	{	
		if ($this->user_regional_setting)
		{
			$timezone_id = $this->user_regional_setting->get_timezone_id();
			if (is_numeric($timezone_id))
			{
				return $timezone_id;
			}
			else
			{
				return Registry::get_value("base_timezone_id");
			}
		}
		else
		{
			return Registry::get_value("base_timezone_id");
		}
	}
	
	/**
	 * @see RegionalInterface::get_time_display_format()
	 * @return array
	 */
	public function get_time_display_format()
	{
		if ($this->user_regional_setting)
		{
			$time_display_format = $this->user_regional_setting->get_time_display_format();
			if (isset($time_display_format))
			{
				if ($time_display_format == "t")
				{
					return array(true, "h:i:s a");
				}
				else
				{
					return array(false, "H:i:s");
				}
			}
			else
			{
				return array(false, "H:i:s");
			}
		}
		else
		{
			return array(false, "H:i:s");
		}
	}
	
	/**
	 * @see RegionalInterface::get_time_enter_format()
	 * @return bool
	 */
	public function get_time_enter_format()
	{
		if ($this->user_regional_setting)
		{
			$time_enter_format = $this->user_regional_setting->get_time_enter_format();
			if (isset($time_enter_format))
			{
				return $time_enter_format;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::get_date_display_format()
	 * @return string
	 */
	public function get_date_display_format()
	{
		if ($this->user_regional_setting)
		{
			$date_display_format = $this->user_regional_setting->get_date_display_format();
			if ($date_display_format)
			{
				return $date_display_format;
			}
			else
			{
				return "jS M Y";
			}
		}
		else
		{
			return "jS M Y";
		}
	}
	
	/**
	 * @see RegionalInterface::get_date_enter_format()
	 * @return string
	 */
	public function get_date_enter_format()
	{
		if ($this->user_regional_setting)
		{
			$date_enter_format = $this->user_regional_setting->get_date_enter_format();
			if ($date_enter_format)
			{
				return $date_enter_format;
			}
			else
			{
				return "dd.mm.yyyy";
			}
		}
		else
		{
			return "dd.mm.yyyy";
		}
	}
	
	/**
	 * @see RegionalInterface::get_jquery_date_enter_format()
	 * @return string
	 */
	public function get_jquery_date_enter_format()
	{
		if ($this->user_regional_setting)
		{
			$date_enter_format = $this->user_regional_setting->get_date_enter_format();
			if ($date_enter_format)
			{
				switch($date_enter_format):
				
					case "yyyy.mm.dd":
					return "yy.mm.dd";
					break;
					
					case "dd-mm-yyyy":
						return "dd-mm-yy";
					break;
						
					case "mm-dd-yyyy":
						return "mm-dd-yy";
					break;
						
					case "yyyy-mm-dd":
						return "yy-mm-dd";
					break;
						
					case "dd/mm/yyyy":
						return "dd/mm/yy";
					break;
						
					case "mm/dd/yyyy":
						return "mm/dd/yy";
					break;
						
					default: // dd.mm.yyyy
						return "dd.mm.yy";
					break;
						
				endswitch;
			}
			else
			{
				return "dd.mm.yy";
			}
		}
		else
		{
			return "dd.mm.yy";
		}
	}
	
	/**
	 * @see RegionalInterface::get_jquery_date_enter_format()
	 * @return string
	 */
	public function get_php_date_enter_format()
	{
		if ($this->user_regional_setting)
		{
			$date_enter_format = $this->user_regional_setting->get_date_enter_format();
			if ($date_enter_format)
			{
				switch($date_enter_format):
	
				case "yyyy.mm.dd":
					return "Y.m.d";
				break;
					
				case "dd-mm-yyyy":
					return "d-m-Y";
					break;
	
				case "mm-dd-yyyy":
					return "m-d-Y";
					break;
	
				case "yyyy-mm-dd":
					return "Y-m-d";
					break;
	
				case "dd/mm/yyyy":
					return "d/m/Y";
					break;
	
				case "mm/dd/yyyy":
					return "m/d/Y";
					break;
	
				default: // dd.mm.yyyy
					return "d.m.Y";
					break;
	
					endswitch;
			}
			else
			{
				return "d.m.Y";
			}
		}
		else
		{
			return "d.m.Y";
		}
	}
	
	/**
	 * @see RegionalInterface::get_country_id()
	 * @return integer
	 */
	public function get_country_id()
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->get_country_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see RegionalInterface::get_system_of_units()
	 * @return string
	 */
	public function get_system_of_units()
	{
		if ($this->user_regional_setting)
		{
			$system_of_units = $this->user_regional_setting->get_system_of_units();
			if ($system_of_units)
			{
				return $system_of_units;
			}
			else
			{
				return "metric";
			}
		}
		else
		{
			return "metric";
		}
	}
	
	/**
	 * @see RegionalInterface::get_system_of_paper_format()
	 * @return string
	 */
	public function get_system_of_paper_format()
	{
		if ($this->user_regional_setting)
		{
			$system_of_paper_format = $this->user_regional_setting->get_system_of_paper_format();
			if ($system_of_paper_format)
			{
				return $system_of_paper_format;
			}
			else
			{
				return "din";
			}
		}
		else
		{
			return "din";
		}
	}
	
	/**
	 * @see RegionalInterface::get_currency_id()
	 * @return integer
	 */
	public function get_currency_id()
	{
		if ($this->user_regional_setting)
		{
			$currency_id = $this->user_regional_setting->get_currency_id();
			if ($currency_id)
			{
				return $currency_id;
			}
			else
			{
				return 1;
			}
		}
		else
		{
			return 1;
		}
	}
	
	/**
	 * @see RegionalInterface::get_currency_significant_digits()
	 * @return integer
	 */
	public function get_currency_significant_digits()
	{
		if ($this->user_regional_setting)
		{
			$currency_significan_digits = $this->user_regional_setting->get_currency_significant_digits();
			if (is_numeric($currency_significan_digits) and $currency_significan_digits >= 0)
			{
				return $currency_significan_digits;
			}
			else
			{
				return 2;
			}
		}
		else
		{
			return 2;
		}
	}
	
	/**
	 * @see RegionalInterface::get_decimal_separator()
	 * @return string
	 */
	public function get_decimal_separator()
	{
		if ($this->user_regional_setting)
		{
			$decimal_separator = $this->user_regional_setting->get_decimal_separator();
			if ($decimal_separator)
			{
				switch($decimal_separator):
					case "dot":
						return ".";
					break;
					
					default:
						return ",";
					break;
				endswitch;
			}
			else
			{
				return ",";
			}
		}
		else
		{
			return ",";
		}
	}
	
	/**
	 * @see RegionalInterface::get_thousand_separator()
	 * @return string
	 */
	public function get_thousand_separator()
	{
		if ($this->user_regional_setting)
		{
			$thousand_separator = $this->user_regional_setting->get_thousand_separator();
			if ($thousand_separator)
			{
				switch($thousand_separator):
				
					case "space":
						return "&nbsp;";
					break;
					
					case "comma":
						return ",";
					break;
					
					case "dot":
						return ".";
					break;
					
					case "apostrophe":
						return "'";
					break;
					
					default:
						return "";
					break;
				
				endswitch;
			}
			else
			{
				return "";
			}
		}
		else
		{
			return "";
		}
	}
	
	/**
	 * @see RegionalInterface::get_name_display_format()
	 * @return string
	 */
	public function get_name_display_format()
	{
		if ($this->user_regional_setting)
		{
			$name_display_format = $this->user_regional_setting->get_name_display_format();
			if ($name_display_format)
			{
				return $name_display_format;
			}
			else
			{
				return "T F S";
			}
		}
		else
		{
			return "T F S";
		}
	}
	
	/**
	 * @see RegionalInterface::format_number()
	 * @param float $number
	 * @param integer $decimal
	 * @param boolean $cut_decimal
	 * @return string
	 */
	public function format_number($number, $decimal = 10, $cut_decimal = true)
	{ 
		 $number = number_format($number, $decimal, $this->get_decimal_separator(), $this->get_thousand_separator());
		 if ($cut_decimal == true)
		 {
		 	$number = rtrim($number, "0");
		 	$number = rtrim($number, ",");
		 }
		 return $number;
	}
	
	/**
	 * @see RegionalInterface::set_language_id()
	 * @param integer $language_id
	 * @return boolean
	 */
	public function set_language_id($language_id)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_language_id($language_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_timezone_id()
	 * @param integer $timezone_id
	 * @return boolean
	 */
	public function set_timezone_id($timezone_id)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_timezone_id($timezone_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_time_display_format()
	 * @param boolean $time_display_format
	 * @return boolean
	 */
	public function set_time_display_format($time_display_format)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_time_display_format($time_display_format);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_time_enter_format()
	 * @param boolean $time_enter_format
	 * @return boolean
	 */
	public function set_time_enter_format($time_enter_format)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_time_enter_format($time_enter_format);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_date_display_format()
	 * @param string $date_display_format
	 * @return boolean
	 */
	public function set_date_display_format($date_display_format)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_date_display_format($date_display_format);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_date_enter_format()
	 * @param string $date_enter_format
	 * @return boolean
	 */
	public function set_date_enter_format($date_enter_format)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_date_enter_format($date_enter_format);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_country_id()
	 * @param integer $country_id
	 * @return boolean
	 */
	public function set_country_id($country_id)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_country_id($country_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_system_of_units()
	 * @param string $system_of_units
	 * @return boolean
	 */
	public function set_system_of_units($system_of_units)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_system_of_units($system_of_units);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_system_of_paper_format()
	 * @param string $system_of_paper_format
	 * @return boolean
	 */
	public function set_system_of_paper_format($system_of_paper_format)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_system_of_paper_format($system_of_paper_format);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @see RegionalInterface::set_currency_id()
	 * @param integer $currency_id
	 * @return boolean
	 */
	public function set_currency_id($currency_id)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_currency_id($currency_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see RegionalInterface::set_currency_significant_digits()
	 * @param integer $currency_significant_digits
	 * @return boolean
	 */
	public function set_currency_significant_digits($currency_significant_digits)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_currency_significant_digits($currency_significant_digits);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @see RegionalInterface::set_decimal_separator()
	 * @param string $decimal_separator
	 * @return boolean
	 */
	public function set_decimal_separator($decimal_separator)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_decimal_separator($decimal_separator);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @see RegionalInterface::set_thousand_separator()
	 * @param string $thousand_separator
	 * @return boolean
	 */
	public function set_thousand_separator($thousand_separator)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_thousand_separator($thousand_separator);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @see RegionalInterface::set_name_display_format()
	 * @param string $name_display_format
	 * @return boolean
	 */
	public function set_name_display_format($name_display_format)
	{
		if ($this->user_regional_setting)
		{
			return $this->user_regional_setting->set_name_display_format($name_display_format);
		}
		else
		{
			return false;
		}
	}
	
}
?>