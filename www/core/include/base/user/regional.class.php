<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
				// Fallback
			}
		}
		else
		{
			// Fallback
		}
	}
	
    /**
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
	 * @return string
	 */
	public function get_time_display_format()
	{
		if ($this->user_regional_setting)
		{
			$time_display_format = $this->user_regional_setting->get_time_display_format();
			if (isset($time_display_format))
			{
				if ($time_display_format == true)
				{
					return "H:i:s";
				}
				else
				{
					return "h:i:s a";
				}
			}
			else
			{
				return "H:i:s";
			}
		}
		else
		{
			return "H:i:s";
		}
	}
	
	/**
	 * @return bool
	 * true = 24 hr
	 * false = 12 hr
	 */
	public function get_time_enter_format()
	{

	}
	
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
	public function get_date_display_format()
	{
		// 
		
		if ($this->user_regional_setting)
		{
			
		}
		else
		{
			return "jS M Y";
		}
	}
	
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
	public function get_date_enter_format()
	{
		if ($this->user_regional_setting)
		{
			
		}
		else
		{
			return "dd.mm.YYYY";
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_country_id()
	{
		
	}
	
	/**
	 * @return string
	 * metric
	 * angloamerican
	 */
	public function get_system_of_units()
	{
		
	}
	
	/**
	 * @return string
	 * din
	 * angloamerican
	 */
	public function get_system_of_paper_format()
	{

	}
	
	/**
	 * @return integer
	 */
	public function get_currency_id()
	{
		
	}
	
	/**
	 * @return integer
	 */
	public function get_currency_significant_digits()
	{
		
	}
	
	/**
	 * @return string
	 */
	public function get_decimal_separator()
	{
		
	}
	
	/**
	 * @return string
	 */
	public function get_thousand_separator()
	{
		
	}
	
	/**
	 * @return string
	 */
	public function get_name_display_format()
	{
		
	}
	
	public function set_language_id($language_id)
	{
		
	}
	
	public function set_timezone_id($timezone_id)
	{
		
	}
	
	public function set_time_display_format($time_display_format)
	{
		
	}
	
	public function set_time_enter_format($time_enter_format)
	{

	}
	
	public function set_date_display_format($date_display_format_id)
	{

	}
	

	public function set_date_enter_format($date_enter_format_id)
	{
		
	}
	
	public function set_country_id($country_id)
	{
		
	}
	
	public function set_system_of_units($system_of_units_id)
	{
		
	}
	
	public function set_system_of_paper_format($system_of_paper_format_id)
	{

	}

	public function set_currency_id($currency_id)
	{
		
	}
	
	public function set_currency_significant_digits($currency_significant_digits)
	{
		
	}

	public function set_decimal_separator($decimal_separator_id)
	{
		
	}

	public function set_thousand_separator($thousand_separator_id)
	{
		
	}

	public function set_name_display_format($name_display_format_id)
	{
		
	}
	
}
?>