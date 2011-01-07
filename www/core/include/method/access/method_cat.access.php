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
 * Method Category Access Class
 * @package method
 */
class MethodCat_Access
{
	const METHOD_CAT_TABLE = 'core_method_cats';
	const METHOD_CAT_PK_SEQUENCE = 'core_method_cats_id_seq';

	private $method_cat_id;
	private $toid;
	private $name;
	
	/**
	 * @param integer $method_cat_id
	 */
	function __construct($method_cat_id)
	{
		global $db;

		if ($method_cat_id == null)
		{
			$this->method_cat_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::METHOD_CAT_TABLE." WHERE id='".$method_cat_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->method_cat_id 	= $method_cat_id;
				$this->toid				= $data[toid];
				$this->name				= $data[name];
			}
			else
			{
				$this->method_cat_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->method_cat_id) {
			unset($this->method_cat_id);
			unset($this->toid);
			unset($this->name);
		}
	}
	
	/**
	 * @param integer $toid
	 * @param string $name
	 * @return integer
	 */
	public function create($toid, $name)
	{
		global $db;
		
		if ($name)
		{
			if (is_numeric($toid))
			{
				$sql_write = "INSERT INTO ".self::METHOD_CAT_TABLE." (id,toid,name) " .
					"VALUES (nextval('".self::METHOD_CAT_PK_SEQUENCE."'::regclass),'".$toid."','".$name."')";
			}
			else
			{
				$sql_write = "INSERT INTO ".self::METHOD_CAT_TABLE." (id,toid,name) " .
					"VALUES (nextval('".self::METHOD_CAT_PK_SEQUENCE."'::regclass),currval('".self::METHOD_CAT_PK_SEQUENCE."'::regclass),'".$name."')";
			}
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".self::METHOD_CAT_TABLE." WHERE id = currval('".self::METHOD_CAT_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->method_cat_id)
		{
			$tmp_method_cat_id = $this->method_cat_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".self::METHOD_CAT_TABLE." WHERE id = ".$tmp_method_cat_id."";
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;
			
		if ($this->method_cat_id and $name)
		{
			$sql = "UPDATE ".self::METHOD_CAT_TABLE." SET name = '".$name."' WHERE id = '".$this->method_cat_id."'";
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
	 * @param integer $toid
	 * @return bool
	 */
	public function set_toid($toid)
	{
		global $db;
			
		if ($this->method_cat_id and is_numeric($toid))
		{
			$sql = "UPDATE ".self::METHOD_CAT_TABLE." SET toid = '".$toid."' WHERE id = '".$this->method_cat_id."'";
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
		
		if (is_numeric($id))
		{	
			$return_array = array();
			
			$sql = "SELECT id FROM ".self::METHOD_CAT_TABLE." WHERE id=".$id."";
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
			
			$sql = "SELECT id FROM ".self::METHOD_CAT_TABLE." WHERE TRIM(LOWER(name))='".$name."'";
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
			
			$sql = "SELECT id FROM ".self::METHOD_CAT_TABLE." WHERE toid = ".$toid." AND toid != id ORDER BY name";
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
		
		$sql = "SELECT id FROM ".self::METHOD_CAT_TABLE." WHERE toid = id ORDER BY name";
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
		
		$sql = "SELECT id FROM ".self::METHOD_CAT_TABLE." ORDER BY name";
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
