<?php
/**
 * @package location
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
require_once("interfaces/location.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/location_type.access.php");
	require_once("access/location.access.php");
}

/**
 * Location Management Class
 * @package location
 */
class Location implements LocationInterface
{
	private $location_id;
	private $location;
    
	/**
	 * @param integer $location_id
	 */
	function __construct($location_id)
	{
		if ($location_id) {
			$this->location_id = $location_id;
			$this->location = new Location_Access($location_id);
		}else{
			$this->location_id = null;
			$this->location = new Location_Access(null);
		}
	}
	
	/**
	 * @param bool $show_additional_name
	 * @return string
	 */
	function get_name($show_additional_name)
	{
		if ($this->location_id and $this->location)
		{
			$show_prefix = $this->location->get_prefix();
			$name = $this->location->get_name();
			
			if ($show_additional_name == true)
			{
				$additional_name = $this->location->get_additional_name();
			}
			else
			{
				$additional_name = null;
			}
			
			if ($show_prefix == true)
			{
				$location_type_access = new LocationType_Access($this->location->get_type_id());
				
				if ($additional_name)
				{
					return $location_type_access->get_name()." ".$name." (".$additional_name.")";
				}
				else
				{
					return $location_type_access->get_name()." ".$name;
				}
			}
			else
			{
				if ($additional_name)
				{
					return $name." (".$additional_name.")";
				}
				else
				{
					return $name;
				}
			}
		}
		else
		{
			return null;
		}
	}
	
	
	public static function list_entries()
	{
		return Location_Access::list_entries();
	}
}
?>