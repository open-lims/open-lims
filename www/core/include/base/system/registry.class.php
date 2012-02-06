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
require_once("interfaces/registry.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base_registry.access.php");
	
	define("BASE_REGISTRY_TABLE", "core_base_registry");
}

/**
 * Registry Class
 * @package base
 */
class Registry implements RegistryInterface, EventListenerInterface
{	
	private $registry_id;
	private $registry;
	
	/**
	 * @see RegistryInterface::__construct()
	 * @param integer $id
	 */
	function __construct($id)
	{
		if (is_numeric($id))
		{
			$this->registry_id = $id;
			$this->registry = new BaseRegistry_Access($id);
		}
		else
		{
			$this->registry_id = null;
			$this->registry = new BaseRegistry_Access(null);
		}
	}
	
	/**
	 * @see RegistryInterface::__destruct()
	 */
	function __destruct()
	{
		if ($this->registry_id)
		{
			unset($this->registry_id);
			unset($this->registry);
		}
	}
	
	/**
	 * @see RegistryInterface::create()
	 * @param string $name
	 * @param integer $include_id
	 * @param string $value
	 * @return integer
	 */
	public function create($name, $include_id, $value)
	{
		return $this->registry->create($name, $include_id, $value);
	}
	
	/**
	 * @see RegistryInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		if ($this->registry_id)
		{
			return $this->registry->delete();
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * @see RegistryInterface::is_value()
	 * @param string $name
	 * @return string
	 */
	public static function is_value($name)
	{
		if ($name)
		{
			if (BaseRegistry_Access::get_id_by_name($name))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see RegistryInterface::get_value()
	 * @param string $name
	 * @return string
	 */
	public static function get_value($name)
	{
		if ($name)
		{
			$id = BaseRegistry_Access::get_id_by_name($name);
			
			if ($id)
			{
				$base_registry = new BaseRegistry_Access($id);
				return $base_registry->get_value();
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see RegistryInterface::set_value()
	 * @param string $name
	 * @param string $value
	 * @return bool
	 */
	public static function set_value($name, $value)
	{
		if ($name and $value)
		{
			$id = BaseRegistry_Access::get_id_by_name($name);
			
			if ($id)
			{
				$base_registry = new BaseRegistry_Access($id);
				if ($base_registry->set_value($value))
				{
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
		else
		{
			return false;
		}
	}
	
	/**
	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof IncludeDeleteEvent)
    	{
    		if (BaseRegistry_Access::delete_by_include_id($event_object->get_include_id()) == false)
			{
				return false;
			}
    	}
    	
    	return true;
    }
}
?>