<?php
/**
 * @package location
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
require_once("interfaces/location.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("events/location_delete_event.class.php");
	
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
	 * @see LocationInterface::__construct()
	 * @param integer $location_id
	 * @throws LocationNotFoundException
	 */
	function __construct($location_id)
	{
		if (is_numeric($location_id))
		{
			if (Location_Access::exist_id($location_id) == true)
			{
				$this->location_id = $location_id;
				$this->location = new Location_Access($location_id);
			}
			else
			{
				throw new LocationNotFoundException();
			}
		}
		else
		{
			$this->location_id = null;
			$this->location = new Location_Access(null);
		}
	}
	
	/**
	 * @see LocationInterface::create()
	 * @param integer $toid
	 * @param integer $type_id
	 * @param string $name
	 * @param string $additional_name
	 * @param bool $show_prefix
	 * @return integer
	 */
	public function create($toid, $type_id, $name, $additional_name, $show_prefix)
	{
		if ($this->location)
		{
			return $this->location->create($toid, $type_id, $name, $additional_name, $show_prefix);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see LocationInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;	

		if ($this->location and $this->location_id)
		{
			$transaction_id = $transaction->begin();
			
			// Event
			$location_delete_event = new LocationDeleteEvent($this->location_id);
			$event_handler = new EventHandler($location_delete_event);
			
			if ($event_handler->get_success() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			else
			{
				if ($this->location->delete() == true)
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}	
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see LocationInterface::get_name()
	 * @param bool $show_additional_name
	 * @return string
	 */
	public function get_name($show_additional_name)
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
	
	/**
	 * @see LocationInterface::get_type_id()
	 * @return integer
	 */
	public function get_type_id()
	{
		if ($this->location_id and $this->location)
		{
			return $this->location->get_type_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see LocationInterface::get_db_name()
	 * @return string
	 */
	public function get_db_name()
	{
		if ($this->location_id and $this->location)
		{
			return $this->location->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see LocationInterface::get_additional_name()
	 * @return string
	 */
	public function get_additional_name()
	{
		if ($this->location_id and $this->location)
		{
			return $this->location->get_additional_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see LocationInterface::get_prefix()
	 * @return bool
	 */
	public function get_prefix()
	{
		if ($this->location_id and $this->location)
		{
			return $this->location->get_prefix();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see LocationInterface::get_children()
	 * @return array
	 */
	public function get_children()
	{
		if ($this->location_id)
		{
			return Location_Access::list_entries_by_id($this->location_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see LocationInterface::set_type_id()
	 * @param integer $type_id
	 * @return bool
	 */
	public function set_type_id($type_id)
	{
		if ($this->location_id and $this->location)
		{
			return $this->location->set_type_id($type_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see LocationInterface::set_db_name()
	 * @param string $name
	 * @return bool
	 */
	public function set_db_name($name)
	{
		if ($this->location_id and $this->location)
		{
			return $this->location->set_name($name);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see LocationInterface::set_additional_name()
	 * @param string $additional_name
	 * @return bool
	 */
	public function set_additional_name($additional_name)
	{
		if ($this->location_id and $this->location)
		{
			return $this->location->set_additional_name($additional_name);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see LocationInterface::set_prefix()
	 * @param bool $prefix
	 * @return bool
	 */
	public function set_prefix($prefix)
	{
		if ($this->location_id and $this->location)
		{
			return $this->location->set_prefix($prefix);
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * @see LocationInterface::exist_id()
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		return Location_Access::exist_id($id);
	}
	
	/**
	 * @see LocationInterface::list_root_entries()
	 * @return array
	 */
	public static function list_root_entries()
	{
		return Location_Access::list_root_entries();
	}
	
	/**
	 * @see LocationInterface::list_entries()
	 * @return array
	 */
	public static function list_entries()
	{
		return Location_Access::list_entries();
	}

	/**
	 * @see LocationInterface::list_types()
	 * @return array
	 */
	public static function list_types()
	{
		return LocationType_Access::list_entries();
	}
}
?>