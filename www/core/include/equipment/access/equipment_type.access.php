<?php
/**
 * @package equipment
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
 * Equipment Type Access Class
 * @package equipment
 */
class EquipmentType_Access
{
	const EQUIPMENT_TYPE_PK_SEQUENCE = 'core_equipment_types_id_seq';

	private $equipment_type_id;

	private $toid;
	private $name;
	private $cat_id;
	private $location_id;
	private $description;
	private $manufacturer;
	
	/**
	 * @param integer $equipment_type_id
	 */
	function __construct($equipment_type_id)
	{
		global $db;

		if ($equipment_type_id == null)
		{
			$this->equipment_type_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("EQUIPMENT_TYPE_TABLE")." WHERE id='".$equipment_type_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->equipment_type_id 	= $equipment_type_id;
				
				$this->toid				= $data[toid];
				$this->name				= $data[name];
				$this->cat_id			= $data[cat_id];
				$this->location_id		= $data[location_id];
				$this->description		= $data[description];
				$this->manufacturer		= $data[manufacturer];
			}
			else
			{
				$this->equipment_type_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->equipment_type_id)
		{
			unset($this->equipment_type_id);
			unset($this->toid);
			unset($this->name);
			unset($this->cat_id);
			unset($this->location_id);
			unset($this->description);
			unset($this->manufacturer);
		}
	}
	
	/**
	 * @param integer $toid
	 * @param string $name
	 * @param integer $cat_id
	 * @param integer $location_id
	 * @param string $description
	 * @return integer
	 */
	public function create($toid, $name, $cat_id, $location_id, $description, $manufacturer)
	{
		global $db;
		
		if (($name or $manufacturer) and is_numeric($cat_id))
		{
			
			if ($name)
			{
				$name_insert = "'".$name."'";
			}
			else
			{
				$name_insert = "NULL";
			}
			
			if ($manufacturer)
			{
				$manufacturer_insert = "'".$manufacturer."'";
			}
			else
			{
				$manufacturer_insert = "NULL";
			}
			
			if ($description)
			{
				$description_insert = "'".$description."'";
			}
			else
			{
				$description_insert = "NULL";
			}
			
			if (is_numeric($location_id) and $location_id != 0)
			{
				$location_id_insert = $location_id;
			}
			else
			{
				$location_id_insert = "NULL";
			}
			
			if (is_numeric($toid))
			{
				$sql_write = "INSERT INTO ".constant("EQUIPMENT_TYPE_TABLE")." (id,toid,name,cat_id,location_id,description,manufacturer) " .
						"VALUES (nextval('".self::EQUIPMENT_TYPE_PK_SEQUENCE."'::regclass),".$toid.",".$name_insert.",".$cat_id.",".$location_id_insert.",".$description_insert.",".$manufacturer_insert.")";
			}
			else
			{
				$sql_write = "INSERT INTO ".constant("EQUIPMENT_TYPE_TABLE")." (id,toid,name,cat_id,location_id,description,manufacturer) " .
						"VALUES (nextval('".self::EQUIPMENT_TYPE_PK_SEQUENCE."'::regclass),currval('".self::EQUIPMENT_TYPE_PK_SEQUENCE."'::regclass),".$name_insert.",".$cat_id.",".$location_id_insert.",".$description_insert.",".$manufacturer_insert.")";
			}
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("EQUIPMENT_TYPE_TABLE")." WHERE id = currval('".self::EQUIPMENT_TYPE_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read[id]);
			
				return $data_read[id];
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->equipment_type_id)
		{
			$tmp_equipment_type_id = $this->equipment_type_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("EQUIPMENT_TYPE_TABLE")." WHERE id = ".$tmp_equipment_type_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res) == 1)
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
	 * @return integer
	 */
	public function get_toid()
	{
		if ($this->toid)
		{
			return $this->toid;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_name()
	{
		if ($this->name)
		{
			return $this->name;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_cat_id()
	{
		if ($this->cat_id)
		{
			return $this->cat_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_location_id()
	{
		if ($this->location_id)
		{
			return $this->location_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_description()
	{
		if ($this->description)
		{
			return $this->description;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_manufacturer()
	{
		if ($this->manufacturer)
		{
			return $this->manufacturer;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $toid
	 * @return bool
	 */
	public function set_toid($toid)
	{
		global $db;

		if ($this->equipment_type_id and is_numeric($toid))
		{
			$sql = "UPDATE ".constant("EQUIPMENT_TYPE_TABLE")." SET toid = '".$toid."' WHERE id = '".$this->equipment_type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->toid = $toid;
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;

		if ($this->equipment_type_id and isset($name))
		{
			if (!$name)
			{
				$name_insert = "NULL";
			}
			else
			{
				$name_insert = "'".$name."'";
			}
			
			$sql = "UPDATE ".constant("EQUIPMENT_TYPE_TABLE")." SET name = ".$name_insert." WHERE id = '".$this->equipment_type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->name = $name;
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
	 * @param integer $cat_id
	 * @return bool
	 */
	public function set_cat_id($cat_id)
	{
		global $db;
			
		if ($this->equipment_type_id and is_numeric($cat_id))
		{
			$sql = "UPDATE ".constant("EQUIPMENT_TYPE_TABLE")." SET cat_id = '".$cat_id."' WHERE id = '".$this->equipment_type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->cat_id = $cat_id;
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
	 * @param integer $location_id
	 * @return bool
	 */
	public function set_location_id($location_id)
	{
		global $db;
			
		if ($this->equipment_type_id and is_numeric($location_id))
		{
			if ($location_id == 0)
			{
				$location_id_insert = "NULL";
			}
			else
			{
				$location_id_insert = $location_id;
			}
			
			$sql = "UPDATE ".constant("EQUIPMENT_TYPE_TABLE")." SET location_id = ".$location_id_insert." WHERE id = '".$this->equipment_type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->location_id = $location_id;
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
	 * @param string $description
	 * @return bool
	 */
	public function set_description($description)
	{
		global $db;

		if ($this->equipment_type_id and $description)
		{
			$sql = "UPDATE ".constant("EQUIPMENT_TYPE_TABLE")." SET description = '".$description."' WHERE id = '".$this->equipment_type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->description = $description;
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
	 * @param string $manufacturer
	 * @return bool
	 */
	public function set_manufacturer($manufacturer)
	{
		global $db;

		if ($this->equipment_type_id and isset($manufacturer))
		{
			if (!$manufacturer)
			{
				$manufacturer_insert = "NULL";
			}
			else
			{
				$manufacturer_insert = "'".$manufacturer."'";
			}
			
			$sql = "UPDATE ".constant("EQUIPMENT_TYPE_TABLE")." SET manufacturer = ".$manufacturer_insert." WHERE id = '".$this->equipment_type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->manufacturer = $manufacturer;
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
		
		if (is_numeric($id))
		{	
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("EQUIPMENT_TYPE_TABLE")." WHERE id=".$id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
						
			if ($data[id])
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
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name)
	{
		global $db;
		
		if ($name)
		{	
			
			$name = trim(strtolower($name));
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("EQUIPMENT_TYPE_TABLE")." WHERE TRIM(LOWER(name))='".$name."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
						
			if ($data[id])
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
	 * @param integer $toid
	 * @return array
	 */
	public static function list_entries_by_toid($toid)
	{
		global $db;

		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("EQUIPMENT_TYPE_TABLE")." WHERE toid = ".$toid." AND toid != id ORDER BY name";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
			}
			
			if (is_array($return_array))
			{
				return $return_array;
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
	 * @param integer $cat_id
	 * @return array
	 */
	public static function list_entries_by_cat_id($cat_id)
	{
		global $db;

		if (is_numeric($cat_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("EQUIPMENT_TYPE_TABLE")." WHERE cat_id = ".$cat_id." ORDER BY cat_id,name";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
			}
			
			if (is_array($return_array))
			{
				return $return_array;
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
	 * @return array
	 */
	public static function list_root_entries()
	{
		global $db;

		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("EQUIPMENT_TYPE_TABLE")." WHERE id = toid ORDER BY cat_id,name";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data[id]);
		}
		
		if (is_array($return_array))
		{
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;

		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("EQUIPMENT_TYPE_TABLE")." ORDER BY name";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data[id]);
		}
		
		if (is_array($return_array))
		{
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $location_id
	 * @return bool
	 */
	public static function set_location_id_on_null($location_id)
	{
		global $db;

		if (is_numeric($location_id))
		{
			$sql = "UPDATE ".constant("EQUIPMENT_TYPE_TABLE")." SET location_id = NULL WHERE location_id = ".$location_id."";
			$res = $db->db_query($sql);
			
			if ($res !== false)
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
}
?>
