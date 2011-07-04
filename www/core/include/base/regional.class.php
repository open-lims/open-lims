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
 * 
 */
require_once("interfaces/regional.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/language.access.php");
	require_once("access/timezone.access.php");
}

/**
 * Regional Class
 * Handles Languages and Timezones
 * @package base
 */
class Regional implements RegionalInterface
{

	/**
	 * @param integer $language_id
	 * @return string
	 */
    public static function get_language_name($language_id)
    {
    	if (is_numeric($language_id))
    	{
    		$language = new Language_Access($language_id);
    		return $language->get_english_name()."/".$language->get_language_name()." (".$language->get_iso_639()."-".$language->get_iso_3166().")";
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param integer $timezone_id
     * @return string
     */
    public static function get_timezone_name($timezone_id)
    {
    	if (is_numeric($timezone_id))
    	{
    		$timezone = new Timezone_Access($timezone_id);
    		
    		$deviaton = $timezone->get_deviation();
    		
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
    		
    		return "(".$gmt.") ".$timezone->get_title();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param integer $timezone_id
     * @return integer
     */
    public static function get_timezone_deviation($timezone_id)
    {
    	if (is_numeric($timezone_id))
    	{
    		$timezone = new Timezone_Access($timezone_id);
    		return $timezone->get_deviation();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return array
     */
    public static function list_languages()
    {
    	return Language_Access::list_entries();
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