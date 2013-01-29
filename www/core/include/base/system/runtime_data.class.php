<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
require_once("interfaces/runtime_data.interface.php");

/**
 * Runtime Data Class
 * Saves calculated data via runtime and avoids recalculation
 * @package base
 */
class RuntimeData implements RuntimeDataInterface
{
	private $object_identification_array;
	private $runtime_data;

	/**
	 * Finds the key of an object in array
	 * @param object $object
	 * @return integer
	 */
	private function ident_object($object)
	{
		if (is_object($object))
		{
			if (is_array($this->object_identification_array) and count($this->object_identification_array) >= 1)
			{
				$max_key = 0;
				
				foreach($this->object_identification_array as $key => $value)
				{
					if ($value == $object)
					{
						return $key;
					}
					
					if ($key > $max_key)
					{
						$max_key = $key;
					}
				}
				
				$new_key = $max_key + 1;
				$this->object_identification_array[$new_key] = $object;
				return $new_key;	
			}
			else
			{
				$this->object_identification_array[0] = $object;
				return 0;
			}			
		}
		else
		{
			return -1;
		}
	}

	/**
	 * @see RuntimeDataInterface::write_object_data()
	 * @param object $object
	 * @param string $address
	 * @param mixed $data
	 * @return bool
	 */
   	public function write_object_data($object, $address, $data)
   	{
   		if (is_object($object) and $address and $data)
   		{
   			$key = $this->ident_object($object);
   			$this->runtime_data[$key][$address] = $data;
   			return true;
   		}
   		else
   		{
   			return false;
   		}
   	}
   	
   	/**
   	 * @see RuntimeDataInterface::read_object_data()
   	 * @param object $object
   	 * @param string $address
   	 * @return mixed
   	 */
   	public function read_object_data($object, $address)
   	{
   		if (is_object($object) and $address)
   		{
   			$key = $this->ident_object($object);
   			if ($this->runtime_data[$key][$address])
   			{
   				return $this->runtime_data[$key][$address];
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
   	 * @see RuntimeDataInterface::is_object_data()
   	 * @param object $object
   	 * @param string $address
   	 * @return bool
   	 */
   	public function is_object_data($object, $address)
   	{
   		if (is_object($object) and $address)
   		{
   			$key = $this->ident_object($object);
   			if ($this->runtime_data[$key][$address])
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
   	
   	/**
   	 * @see RuntimeDataInterface::clear_object_data()
   	 * @param object $object
   	 * @param string $address
   	 * @return bool
   	 */
   	public function clear_object_data($object, $address)
   	{
   		if (is_object($object) and $address)
   		{
   			$key = $this->ident_object($object);
   			if ($this->runtime_data[$key][$address])
   			{
   				unset($this->runtime_data[$key][$address]);
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
   	
   	/**
   	 * @see RuntimeDataInterface::clear_object()
   	 * @param object $object
   	 * @return bool
   	 */
   	public function clear_object($object)
   	{
   		if (is_object($object))
   		{
   			$key = $this->ident_object($object);
   			if (is_array($this->runtime_data[$key]))
   			{
   				foreach($this->runtime_data[$key] as $key => $value)
   				{
   					unset($this->runtime_data[$key][$key]);
   				}
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