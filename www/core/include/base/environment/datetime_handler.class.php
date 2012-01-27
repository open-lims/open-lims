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
require_once("interfaces/datetime_handler.interface.php");

/**
 * Date/Time Class
 * Handles Date and Time including Timezones
 * @package base
 */
class DatetimeHandler implements DatetimeHandlerInterface
{
	private $mktime;
	
	private $user_timezone;
	private $server_timezone;
	
	private $ignore_timezone;

	/**
	 * @see DatetimeHandlerInterface::__construct()
	 * @param string $input_string 
	 */
    function __construct($input_string)
    {    	
    	$mixed_array = explode(" ", $input_string);
    	
    	$server_timezone = explode(":",date("P"));
    	$server_timezone = (int)$server_timezone[0];
    	$this->server_timezone = $server_timezone;
    	
    	if (count($mixed_array) == 1)
    	{
    		$part_array = explode("-", $mixed_array[0]);
    		
    		if (count($part_array) == 1)
    		{
    			if (is_numeric($part_array[0])) {
    				$part_array[0] 	= $part_array[0] - date("Z");
    				$this->mktime	= $part_array[0];
    				$this->ignore_timezone = false;
    			}
    			else
    			{
    				// Invalid Input
    			}
    		}
    		else
    		{
    			if (count($part_array) == 3)
    			{
    				if (strlen($part_array[0]) == 4)
    				{
    					$mktime = mktime(0, 0, 0, $part_array[1], $part_array[2], $part_array[0]);
    				}
    				else
    				{
    					$mktime = mktime(0, 0, 0, $part_array[1], $part_array[0], $part_array[2]);
    				}
    				
    				$this->mktime 	= $mktime;
    				$this->ignore_timezone = true;
    			}
    			else
    			{
    				// Invalid Input
    			}
    		}
    	}
    	else
    	{
    		if (count($mixed_array) == 2)
    		{
    			$date_array = explode("-", $mixed_array[0]);
    			$timezone_array = explode("+", $mixed_array[1]);
    			$time_array = explode(":", $timezone_array[0]);
    			
    			if (count($timezone_array) == 2)
    			{
    				if (count($date_array) == 3 and count($time_array) == 3)
    				{
    					// Timestamp w. Timezone
    					$mktime = mktime($time_array[0], $time_array[1], $time_array[2], $date_array[1], $date_array[2], $date_array[0]);
    					$mktime = $mktime - date("Z");
	    				
	    				if ($server_timezone != $timezone_array[1])
	    				{
	    					$correction = $timezone_array[1] - $server_timezone;
	    					$mktime = $mktime - ($correction * 3600);
	    				}
	    				
	    				$this->mktime 	= $mktime;
	    				$this->ignore_timezone = false;
    				}
    				else
    				{
    					// Invalid Input
    				}
    			}
    			elseif (count($timezone_array) == 1)
    			{
					if (count($date_array) == 3 and count($time_array) == 3)
					{
    					// Timestamp wo. Timezone (Timezone = GMT = 0)
    					$mktime = mktime($time_array[0], $time_array[1], $time_array[2], $date_array[1], $date_array[2], $date_array[0]);
    					$mktime = $mktime - date("Z");
	    				
	    				$this->mktime 	= $mktime;
	    				$this->ignore_timezone = false;
    				}
    				else
    				{
    					// Invalid Input
    				}
    			}
    			else
    			{
    				// Invalid Input
    			}
    		}
    		else
    		{
    			// Invalid Input
    		}
    	}
    }
    
    function __destruct() {
    	unset($this->mktime);
    	unset($this->user_timezone);
    	unset($this->server_timezone);
    	unset($this->ignore_timezone);
    }
    
    /**
     * @see DatetimeHandlerInterface::less_then_current()
     * @return bool
     */
    public function less_then_current()
    {
    	if ($this->mktime)
    	{
    		$current_mktime = mktime()-date("Z");
    		if ($current_mktime <= $this->mktime)
    		{
    			return false;
    		}
    		else
    		{
    			return true;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::distance()
     * @param object $date_object
     * @return integer distance in seconds
     */
    public function distance($date_object)
    {
    	if ($this->mktime and is_object($date_object))
    	{
    		$object_mktime = $date_object->get_mktime();
    		return ($object_mktime - $this->mktime - date("Z", $this->mktime));
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::day_distance()
     * @param object $date_object
     * @return integer distance in days
     */
    public function day_distance($date_object)
    {
    	if ($this->mktime and is_object($date_object))
    	{
    		$object_mktime = $date_object->get_mktime();
    		$distance = floor(($object_mktime - $this->mktime - date("Z", $this->mktime))/86400);
    		return $distance;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::add_day()
     * @param integer $day
     * @return bool
     */
    public function add_day($day)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		
    		$this->mktime = $this->mktime + (3600 * 24 * $day);
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime - 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime + 3600;
			}
			
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::add_month()
     * @param integer $month
     * @return bool
     */
    public function add_month($month)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		
    		for ($i=1; $i<=$month; $i++)
    		{
    			$current_month_length = date("t", $this->mktime);
    			$this->mktime = $this->mktime + (3600 * 24 * $current_month_length);
    		}
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime - 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime + 3600;
			}
			
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::add_year()
     * @param integer $year
     * @return bool
     */
    public function add_year($year)
    {
    	if ($this->mktime)
    	{
    		for ($i=1; $i<=$year; $i++)
    		{    			
    			if (date("z", $this->mktime) <= 59 and date("L", $this->mktime) == 1)
    			{
	    			$this->mktime = $this->mktime + (3600 * 24 * 366);
    			}
    			else
    			{
    				$this->mktime = $this->mktime + (3600 * 24 * 365);
    				if (date("z", $this->mktime) > 59 and date("L", $this->mktime) == 1)
    				{
    					$this->mktime = $this->mktime + (3600 * 24);
    				}
    			}
    		}
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::add_hour()
     * @param integer $hour
     * @return bool
     */
    public function add_hour($hour)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		
    		$this->mktime = $this->mktime + (3600 * $hour);
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime - 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime + 3600;
			}
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::add_minute()
     * @param integer $minute
     * @return bool
     */
    public function add_minute($minute)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		$this->mktime = $this->mktime + (60 * $minute);
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime - 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime + 3600;
			}
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::add_second()
     * @param integer $second
     * @return bool
     */
    public function add_second($second)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		$this->mktime = $this->mktime + $second;
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime - 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime + 3600;
			}
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::sub_day()
     * @param integer $day
     * @return bool
     */
    public function sub_day($day)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		$this->mktime = $this->mktime - (3600 * 24 * $day);
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime + 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true) {
				$this->mktime = $this->mktime - 3600;
			}
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::sub_month()
     * @param integer $month
     * @return bool
     */
    public function sub_month($sub_month)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		
    		for ($i=1; $i<=$sub_month; $i++)
    		{
	    		$year 	= date("Y", $this->mktime);
	    		$month 	= date("m", $this->mktime);
	    		$day 	= date("d", $this->mktime);
	    		
	    		$hour 	= date("H", $this->mktime);
	    		$minute = date("i", $this->mktime);
	    		$second = date("s", $this->mktime);
	    		
	    		if ($month == 1)
	    		{
	    			$month = 12;
	    			$year = $year - 1;
	    		}
	    		else
	    		{
	    			$month = $month - 1;
	    		}
	    		$this->mktime = mktime($hour, $minute, $second, $month, $day, $year);
    		}
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime + 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime - 3600;
			}
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::sub_year()
     * @param integer $year
     * @return bool
     */
    public function sub_year($sub_year)
    {
    	if ($this->mktime)
    	{
    		for ($i=1; $i<=$sub_year; $i++)
    		{
	    		$year 	= date("Y", $this->mktime);
	    		$month 	= date("m", $this->mktime);
	    		$day 	= date("d", $this->mktime);
	    		
	    		$hour 	= date("H", $this->mktime);
	    		$minute = date("i", $this->mktime);
	    		$second = date("s", $this->mktime);
	 
	    		$year = $year - 1;
	    		
	    		$this->mktime = mktime($hour, $minute, $second, $month, $day, $year);
    		}
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::sub_hour()
     * @param integer $hour
     * @return bool
     */
    public function sub_hour($hour)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		
    		$this->mktime = $this->mktime - (3600 * $hour);
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime + 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime - 3600;
			}
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::sub_minute()
     * @param integer $minute
     * @return bool
     */
    public function sub_minute($minute)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		$this->mktime = $this->mktime - (60 * $minute);
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime + 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime - 3600;
			}
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::sub_second()
     * @param integer $second
     * @return bool
     */
    public function sub_second($second)
    {
    	if ($this->mktime)
    	{
    		if (date("I", $this->mktime) == 1)
    		{
				$summertime = true;
			}
			else
			{
				$summertime = false;
			}
    		$this->mktime = $this->mktime - $second;
    		
    		if (date("I", $this->mktime) == 1 and $summertime == false)
    		{
				$this->mktime = $this->mktime + 3600;
			}
			if (date("I", $this->mktime) == 0 and $summertime == true)
			{
				$this->mktime = $this->mktime - 3600;
			}
    		return true;    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::get_mktime()
     * @return integer
     */
    public function get_mktime()
    {
    	if ($this->mktime)
    	{
    		if ($this->ignore_timezone == false)
    		{
    			return $this->mktime + ($this->server_timezone * 3600);		
    		}
    		else
    		{
    			return $this->mktime;
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::get_timestamp()
     * @return string
     */
    public function get_timestamp()
    {
    	if ($this->mktime)
    	{
    		if ($this->ignore_timezone == false)
    		{
	    		if ($this->user_timezone)
	    		{
	    			$mktime = $this->mktime + ($this->user_timezone * 3600);
	    			$timestamp = date("Y-m-d H:i:s", $mktime);
	    			if ($this->user_timezone >= 0)
	    			{
	    				$timestamp = $timestamp."+".$this->user_timezone;
	    			}
	    			else
	    			{
	    				$timestamp = $timestamp."-".$this->user_timezone;
	    			}
	    		}
	    		else
	    		{
	    			$mktime = $this->mktime + ($this->server_timezone * 3600);
	    			$timestamp = date("Y-m-d H:i:s", $mktime);
	    			if ($this->server_timezone >= 0)
	    			{
	    				$timestamp = $timestamp."+".$this->server_timezone;
	    			}
	    			else
	    			{
	    				$timestamp = $timestamp."-".$this->server_timezone;
	    			}
	    		}
	    		return $timestamp;
    		}
    		else
    		{
    			if ($this->user_timezone)
    			{
    				return date("Y-m-d H:i:s+".$this->user_timezone, $this->mktime);
    			}
    			else
    			{
    				return date("Y-m-d H:i:s+".$this->server_timezone, $this->mktime);
    			}
    		}
    	}
    	else
    	{
    		return null;
    	}    	
    }
    
    /**
     * @see DatetimeHandlerInterface::get_date()
     * @return string
     */
    public function get_date()
    {
    	if ($this->mktime)
    	{
    		if ($this->ignore_timezone == false)
    		{
	    		if ($this->user_timezone)
	    		{
	    			$mktime = $this->mktime + ($this->user_timezone * 3600);
	    			return date("Y-m-d", $mktime);
	    		}
	    		else
	    		{
	    			$mktime = $this->mktime + ($this->server_timezone * 3600);
	    			return date("Y-m-d", $mktime);
	    		}  
    		}
    		else
    		{
    			return  date("Y-m-d", $this->mktime);
    		}  		
    	}
    	else
    	{
    		return null;
    	}    	
    }
    
    /**
     * @see DatetimeHandlerInterface::get_time()
     * @return string
     */
    public function get_time()
    {
    	if ($this->mktime)
    	{
    		if ($this->ignore_timezone == false)
    		{
	    		if ($this->user_timezone)
	    		{
	    			$mktime = $this->mktime + ($this->user_timezone * 3600);
	    			return date("H:i:s", $mktime);
	    		}
	    		else
	    		{
	    			$mktime = $this->mktime + ($this->server_timezone * 3600);
	    			return date("H:i:s", $mktime);
	    		}   
	    	}
	    	else
	    	{
    			return date("H:i:s", $this->mktime);
    		} 	 		
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::get_formatted_string()
     * @param string $format Use php-function date() chars for format
     * @return string 
     */
    public function get_formatted_string($format)
    {
    	if ($this->mktime)
    	{
    		if ($this->ignore_timezone == false)
    		{
	    		if ($this->user_timezone)
	    		{
	    			$mktime = $this->mktime + ($this->user_timezone * 3600);
	    			return date($format, $mktime);
	    		}
	    		else
	    		{
	    			$mktime = $this->mktime + ($this->server_timezone * 3600);
	    			return date($format, $mktime);
	    		}   
    		}
    		else
    		{
    			return date($format, $this->mktime);
    		} 		
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::get_timezone()
     * @return integer
     */
    public function get_timezone()
    {
    	if ($this->mktime)
    	{
    		if ($this->user_timezone)
    		{
    			return $this->user_timezone;
    		}
    		else
    		{
    			return $this->server_timezone;
    		}    		
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see DatetimeHandlerInterface::set_user_timezone()
     * @param integer $user_timezone
     * @return bool
     */
    public function set_user_timezone($user_timezone)
    {
    	if (is_numeric($user_timezone))
    	{
    		if ($user_timezone <= 14 and $user_timezone >= -12)
    		{
    			$this->user_timezone = $user_timezone;
    			return true;
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
    
}
?>