<?php
/**
 * @package manufacturer
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2011 by Roman Konertz
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
require_once("interfaces/manufacturer.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/manufacturer.access.php");
}

/**
 * Manufacturer Management Class
 * @package manufacturer
 */
class Manufacturer implements ManufacturerInterface, EventListenerInterface
{
	private $manufacturer_id;
	private $manufacturer;
	
	function __construct($manufacturer_id)
	{
		if (is_numeric($manufacturer_id))
		{
			$this->manufacturer_id = $manufacturer_id;
			$this->manufacturer = new Manufacturer_Access($manufacturer_id);
		}
		else
		{
			$this->manufacturer_id = null;
			$this->manufacturer = new Manufacturer_Access(null);
		}
	}
	
	function __destruct()
	{
		unset($this->manufacturer_id);
		unset($this->manufacturer);
	}
	
	public function create($name)
	{
		global $user;
		
		if ($this->manufacturer)
		{
			return $this->manufacturer->create($name, $user->get_user_id());
		}
		else
		{
			return false;
		}
	}
	
	public function delete()
	{
		if ($this->manufacturer_id and $this->manufacturer)
		{
			return $this->manufacturer->delete();
		}
		else
		{
			return false;
		}
	}
	
	public function get_name()
	{
		if ($this->manufacturer)
		{
			return $this->manufacturer->get_name();
		}
		else
		{
			return null;
		}
	}
	
	
	public static function exist_name($name)
	{
		return Manufacturer_Access::exist_name($name);
	}
	
	public static function count_entries($string)
	{
		return Manufacturer_Access::count_entries($string);
	}
	
	public static function list_manufacturers($number_of_entries, $start_entry, $start_string)
	{
		return Manufacturer_Access::list_manufacturers($number_of_entries, $start_entry, $start_string);
	}
	
	/**
	 * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeleteEvent)
    	{
			if (Manufacturer_Access::set_user_id_by_user_id($event_object->get_user_id(), 1) == false)
			{
				return false;
			}
    	}

    	return true;
    }
    
}