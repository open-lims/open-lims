<?php
/**
 * @package data
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
require_once("interfaces/data_permission.interface.php");

/**
 * Data Paermission Class
 * @package data
 */
class DataPermission implements DataPermissionInterface
{	
	private $type;
	
	private $id;
	private $object;
	
	private $owner_id;
	private $owner_group_id;
	
	private $permission;
	private $automatic;
	
	/**
	 * @see DataPermissionInterface::__construct()
	 * @param string type
	 * @param integer $id
	 */
	function __construct($type, $id)
	{
		if(is_numeric($id) and $type)
		{
			$this->type = $type;
			
			switch($type):
			
				case("file"):
					$this->id = $id;
					$this->object = File::get_instance($id);
				break;
				
				case("value"):
					$this->id = $id;
					$this->object = Value::get_instance($id);
				break;
				
				case("parameter"):
					$this->id = $id;
					$this->object = Parameter::get_instance($id);
				break;
				
				case("folder"):
					$this->id = $id;
					$this->object = Folder::get_instance($id);
				break;
			
			endswitch;	
			
			$this->automatic = $this->object->get_automatic();
			$this->permission = $this->object->get_permission();
			$this->owner_id = $this->object->get_owner_id();
			$this->owner_group_id = $this->object->get_owner_group_id();
		}
		else
		{
			$this->id = null;
			$this->object = null;
		}
	}
	
	function __destruct()
	{
		unset($this->type);
		unset($this->id);
		unset($this->object);
		unset($this->owner_id);
		unset($this->owner_group_id);
		unset($this->permission);
		unset($this->automatic);
	}
	
	/**
	 * @see DataPermissionInterface::get_permission_array()
	 * @return array
	 */
	public function get_permission_array()
	{
		$permission = $this->permission;
		
		$value = 32768;
		$line_counter = 4;
		$row_counter = 4;
			
		$permission_array = array();
		
		while ($permission != 0)
		{
			$quotient = $permission / $value;
			settype($quotient,integer);
											
			if ($quotient > 0)
			{
				$permission_array[$row_counter][$line_counter] = true;
			}
			else
			{
				$permission_array[$row_counter][$line_counter] = false;
			}

			if ($line_counter <= 1)
			{
				$line_counter = 4;
				$row_counter--;
			}
			else
			{
				$line_counter--;
			}
	
			$permission = $permission % $value;
			$value = $value/2;	
		}
		return $permission_array;
	}
	
	/**
	 * @see DataPermissionInterface::set_permission_array()
	 * @param array $array
	 * @return bool
	 */
	public function set_permission_array($array)
	{		
		$object_permission = new DataEntityPermission($this->permission, $this->automatic, $this->owner_id, $this->owner_group_id);
		
		$value = 1;
		$max = 32768;
		
		$new_permission = 0;
			
		for ($i=1;$i<=4;$i++)
		{
			for ($j=1;$j<=4;$j++)
			{
				$var = $i."-".$j;
					
				if ($array[$var] == "1")
				{		 				
					if ($object_permission->is_access($j) or 
						$object_permission->is_access(4))
					{
						$new_permission = $value + $new_permission;
					}
					else
					{
						if ($session->userid == $this->owner_id and ($j == 1 or $j == 2))
						{
							$new_permission = $value + $new_permission;
						}
					}
				}
				$value = $value*2;
			}
		}
		
		$return_value = false;
					
		if ($array['automatic'] == "1")
		{
 			$return_value = $this->object->set_automatic(true);
 		}
 		else
 		{
 			$return_value = $this->object->set_automatic(false);
 		}
 		 			
 		if ($return_value == true)
 		{
 			$return_value = $this->object->set_permission($new_permission);
 		}
		
		return $return_value;
	}
	
	/**
	 * @see DataPermissionInterface::get_owner_id()
	 * @return integer
	 */
	public function get_owner_id()
	{
		return $this->owner_id;	
	}
	
	/**
	 * @see DataPermissionInterface::get_owner_group_id()
	 * @return integer
	 */
	public function get_owner_group_id()
	{
		return $this->owner_group_id;	
	}
	
	/**
	 * @see DataPermissionInterface::get_automatic()
	 * @return bool
	 */
	public function get_automatic()
	{
		return $this->automatic;
	}
	
	/**
	 * @see DataPermissionInterface::set_owner_id()
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id)
	{
		if (is_numeric($owner_id) and $this->object)
		{
			return $this->object->set_owner_id($owner_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see DataPermissionInterface::set_owner_group_id()
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public function set_owner_group_id($owner_group_id)
	{
		if (is_numeric($owner_group_id) and $this->object)
		{			
			return $this->object->set_owner_group_id($owner_group_id);
		}
		else
		{
			return false;
		}
	}

}

?>
