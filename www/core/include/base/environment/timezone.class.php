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
//require_once("interfaces/timezone.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/timezone.access.php");
}

/**
 * Timezone Class
 * @package base
 */
class Timezone // implements TimezoneInterface
{
	private $timezone_id;
	private $timezone;
	
	function __construct($timezone_id)
	{
		if (is_numeric($timezone_id))
		{
			if (Timezone_Access::exist_id($timezone_id) == true)
			{
				$this->timezone_id = $timezone_id;
    			$this->timezone = new Timezone_Access($timezone_id);
			}
			else
			{
				// throw new ();
			}
    	}
    	else
    	{
    		$this->timezone_id = null;
    		$this->timezone = new Timezone_Access(null);
    	}
	}
	
	public function get_name()
	{
		if ($this->timezone_id and $this->timezone)
		{
			$deviaton = $this->timezone->get_deviation();
	    		
	    	$hour = str_pad(floor(abs($deviaton)), 2, "0", STR_PAD_LEFT);
	    	$minute = str_pad(round((abs($deviaton)-floor(abs($deviaton)))*60), 2, "0", STR_PAD_LEFT);
	    	
	    	if ($deviaton < 0)
	    	{
	    		$prefix = "-";
	    	}
	    	else
	    	{
	    		$prefix = "+";
	    	}
	    	
	    	$gmt = "GMT".$prefix."".$hour.":".$minute."";
	    	
	    	return "(".$gmt.") ".$this->timezone->get_title();
		}
		else
		{
			return null;
		}
	}
	
	public function get_deviation()
	{
		if ($this->timezone_id and $this->timezone)
		{
			$this->timezone->get_deviation();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return array
	 */
	public static function list_timezones()
    {
    	return Timezone_Access::list_entries();
    }
}
?>