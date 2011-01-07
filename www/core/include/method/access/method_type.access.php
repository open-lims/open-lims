<?php
/**
 * @package method
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
 * Method Type Access Class
 * @package method
 */
class MethodType_Access
{
	const METHOD_TYPE_TABLE = 'core_method_types';
	const METHOD_TYPE_PK_SEQUENCE = 'core_method_types_id_seq';

	private $method_type_id;

	private $toid;
	private $name;
	private $cat_id;
	private $location_id;
	private $description;
	
	/**
	 * @param integer $method_type_id
	 */
	function __construct($method_type_id)
	{
		global $db;

		if ($method_type_id == null)
		{
			$this->method_type_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::METHOD_TYPE_TABLE." WHERE id='".$method_type_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->method_type_id 	= $method_type_id;
				
				$this->name				= $data[name];
				$this->cat_id			= $data[cat_id];
				$this->description		= $data[description];
			}
			else
			{
				$this->method_type_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->method_type_id)
		{
			unset($this->method_type_id);
			unset($this->toid);
			unset($this->name);
			unset($this->cat_id);
			unset($this->location_id);
			unset($this->description);
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
	public function create($toid, $name, $cat_id, $location_id, $description)
	{
		global $db;
		
		if ($name and is_numeric($cat_id))
		{
			if ($description)
			{
				$description_insert = "'".$description."'";
			}
			else
			{
				$description_insert = "NULL";
			}
			
			if (is_numeric($location_id))
			{
				$location_id_insert = $location_id;
			}
			else
			{
				$location_id_insert = "NULL";
			}
			
			if (is_numeric($toid))
			{
				$sql_write = "INSERT INTO ".self::METHOD_TYPE_TABLE." (id,toid,name,cat_id,location_id,description) " .
						"VALUES (nextval('".self::METHOD_TYPE_PK_SEQUENCE."'::regclass),".$toid.",'".$name."',".$cat_id.",".$location_id_insert.",".$description_insert.")";
			}
			else
			{
				$sql_write = "INSERT INTO ".self::METHOD_TYPE_TABLE." (id,toid,name,cat_id,location_id,description) " .
						"VALUES (nextval('".self::METHOD_TYPE_PK_SEQUENCE."'::regclass),currval('".self::METHOD_TYPE_PK_SEQUENCE."'::regclass),'".$name."',".$cat_id.",".$location_id_insert.",".$description_insert.")";
			}
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".self::METHOD_TYPE_TABLE." WHERE id = currval('".self::METHOD_TYPE_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->method_type_id)
		{
			$tmp_method_type_id = $this->method_type_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".self::METHOD_TYPE_TABLE." WHERE id = ".$tmp_method_type_id."";
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
	 * @param integer $toid
	 * @return bool
	 */
	public function set_toid($toid)
	{
		global $db;

		if ($this->method_type_id and is_numeric($toid))
		{
			$sql = "UPDATE ".self::METHOD_TYPE_TABLE." SET toid = '".$toid."' WHERE id = '".$this->method_type_id."'";
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

		if ($this->method_type_id and $name)
		{
			$sql = "UPDATE ".self::METHOD_TYPE_TABLE." SET name = '".$name."' WHERE id = '".$this->method_type_id."'";
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
			
		if ($this->method_type_id and is_numeric($cat_id))
		{
			$sql = "UPDATE ".self::METHOD_TYPE_TABLE." SET cat_id = '".$cat_id."' WHERE id = '".$this->method_type_id."'";
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
			
		if ($this->method_type_id and is_numeric($location_id))
		{
			$sql = "UPDATE ".self::METHOD_TYPE_TABLE." SET location_id = '".$location_id."' WHERE id = '".$this->method_type_id."'";
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

		if ($this->method_type_id and $description)
		{
			$sql = "UPDATE ".self::METHOD_TYPE_TABLE." SET description = '".$description."' WHERE id = '".$this->method_type_id."'";
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
		
		if (is_numeric($id))
		{	
			$return_array = array();
			
			$sql = "SELECT id FROM ".self::METHOD_TYPE_TABLE." WHERE id=".$id."";
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
			
			$sql = "SELECT id FROM ".self::METHOD_TYPE_TABLE." WHERE TRIM(LOWER(name))='".$name."'";
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
			
			$sql = "SELECT id FROM ".self::METHOD_TYPE_TABLE." WHERE toid = ".$toid." AND toid != id ORDER BY name";
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
			
			$sql = "SELECT id FROM ".self::METHOD_TYPE_TABLE." WHERE cat_id = ".$cat_id." ORDER BY cat_id,name";
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
		
		$sql = "SELECT id FROM ".self::METHOD_TYPE_TABLE." WHERE id = toid ORDER BY cat_id,name";
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
		
		$sql = "SELECT id FROM ".self::METHOD_TYPE_TABLE." ORDER BY name";
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
	
}
?>
