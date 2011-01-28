<?php
/**
 * @package data
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
 * Object Access Class
 * @package data
 */
class Object_Access
{
	const OBJECT_TABLE = 'core_objects';
	const OBJECT_PK_SEQUENCE = 'core_objects_id_seq';

	private $object_id;
	
	private $toid;
	private $file_id;
	private $value_id;
	private $hidden;
	private $deleted;

	/**
	 * @param integer $object_id
	 */
	function __construct($object_id)
	{
		global $db;
		
		if ($object_id == null)
		{
			$this->object_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::OBJECT_TABLE." WHERE id='".$object_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->object_id			= $object_id;
				
				$this->toid					= $data[toid];
				$this->file_id				= $data[file_id];
				$this->value_id				= $data[value_id];
									
				if ($data[hidden] == "t")
				{
					$this->hidden			= true;
				}
				else
				{
					$this->hidden			= false;
				}
				
				if ($data[deleted] == "t")
				{
					$this->deleted			= true;
				}
				else
				{
					$this->deleted			= false;
				}
			}
			else
			{
				$this->object_id			= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->object_id)
		{
			unset($this->object_id);
			unset($this->toid);
			unset($this->hidden);
			unset($this->deleted);		
		}	
	}

	/**
	 * @param integer $folder_id
	 * @param integer $file_id
	 * @param integer $value_id
	 * @param bool $hidden
	 * @return integer
	 */
	public function create($folder_id, $file_id, $value_id, $hidden)
	{
		global $db;
		
		if (($file_id xor $value_id) and $folder_id)
		{
			if ($file_id)
			{
				$file_id_insert = $file_id;
			}
			else
			{
				$file_id_insert = "null";
			}
			
			if ($value_id)
			{
				$value_id_insert = $value_id;
			}
			else
			{
				$value_id_insert = "null";
			}
			
			if ($hidden == true)
			{
				$hidden_insert = "t";
			}
			else
			{
				$hidden_insert = "f";
			}
			
			$sql_write = "INSERT INTO ".self::OBJECT_TABLE." (id,toid,file_id,value_id,hidden,deleted) " .
					"VALUES (nextval('".self::OBJECT_PK_SEQUENCE."'::regclass),".$folder_id.",".$file_id_insert.",".$value_id_insert.",'".$hidden_insert."','f')";
					
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".self::OBJECT_TABLE." WHERE id = currval('".self::OBJECT_PK_SEQUENCE."'::regclass)";
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
	
		if ($this->object_id)
		{
			$object_id_tmp = $this->object_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".self::OBJECT_TABLE." WHERE id = ".$object_id_tmp."";
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
	 * @return integer
	 */
	public function get_file_id()
	{
		if ($this->file_id)
		{
			return $this->file_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_value_id()
	{
		if ($this->value_id)
		{
			return $this->value_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_hidden()
	{
		if (isset($this->hidden))
		{
			return $this->hidden;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_deleted()
	{
		if (isset($this->deleted))
		{
			return $this->deleted;
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
			
		if ($this->object_id and is_numeric($toid))
		{
			$sql = "UPDATE ".self::OBJECT_TABLE." SET toid = ".$toid." WHERE id = ".$this->object_id."";
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
	 * @param integer $file_id
	 * @return bool
	 */
	public function set_file_id($file_id)
	{			
		global $db;

		if ($this->object_id and is_numeric($file_id))
		{
			$sql = "UPDATE ".self::OBJECT_TABLE." SET file_id = ".$file_id." WHERE id = ".$this->object_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->file_id = $file_id;
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
	 * @param integer $value_id
	 * @return bool
	 */
	public function set_value_id($value_id)
	{
		global $db;
	
		if ($this->object_id and is_numeric($value_id))
		{
			$sql = "UPDATE ".self::OBJECT_TABLE." SET value_id = ".$value_id." WHERE id = ".$this->object_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->value_id = $value_id;
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
	 * @param bool $hidden
	 * @return bool
	 */
	public function set_hidden($hidden)
	{
		global $db;

		if ($this->object_id and isset($hidden))
		{
			if ($hidden == true)
			{
				$hidden_insert = "t";
			}
			else
			{
				$hidden_insert = "f";
			}
			
			$sql = "UPDATE ".self::OBJECT_TABLE." SET hidden = '".$hidden_insert."' WHERE id = ".$this->object_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->hidden = $hidden;
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
	 * @param bool $deleted
	 * @return bool
	 */
	public function set_deleted($deleted)
	{
		global $db;

		if ($this->object_id and isset($deleted))
		{
			if ($deleted == true)
			{
				$deleted_insert = "t";
			}
			else
			{
				$deleted_insert = "f";
			}
			
			$sql = "UPDATE ".self::OBJECT_TABLE." SET deleted = '".$deleted_insert."' WHERE id = ".$this->object_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->deleted = deleted;
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
			
			$sql = "SELECT id FROM ".self::OBJECT_TABLE." WHERE toid = ".$toid."";
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
	 * @param integer $toid
	 * @return array
	 */
	public static function list_value_id_entries_by_toid($toid)
	{
		global $db;

		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".self::OBJECT_TABLE." WHERE toid = ".$toid." AND file_id IS NULL AND value_id IS NOT NULL";
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
	 * @param integer $toid
	 * @return array
	 */
	public static function list_file_id_entries_by_toid($toid)
	{
		global $db;

		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".self::OBJECT_TABLE." WHERE toid = ".$toid." AND value_id IS NULL AND file_id IS NOT NULL";
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
	 * @param integer $value_id
	 * @return integer
	 */
	public static function get_id_by_value_id($value_id)
	{
		global $db;

		if (is_numeric($value_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".self::OBJECT_TABLE." WHERE value_id = ".$value_id." AND file_id IS NULL AND value_id IS NOT NULL";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				return $data[id];
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
	 * @param integer $file_id
	 * @return integer
	 */
	public static function get_id_by_file_id($file_id)
	{
		global $db;

		if (is_numeric($file_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".self::OBJECT_TABLE." WHERE file_id = ".$file_id." AND file_id IS NOT NULL AND value_id IS NULL";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				return $data[id];
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
	 * @param integer $id
	 * @return integer
	 */
	public static function get_value_id_by_id($id)
	{
		global $db;

		if (is_numeric($id))
		{
			$return_array = array();
			
			$sql = "SELECT value_id FROM ".self::OBJECT_TABLE." WHERE id = ".$id." AND file_id IS NULL AND value_id IS NOT NULL";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[value_id])
			{
				return $data[value_id];
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
	 * @param integer $id
	 * @return integer
	 */
	public static function get_file_id_by_id($id)
	{
		global $db;
	
		if (is_numeric($id))
		{
			$return_array = array();
			
			$sql = "SELECT file_id FROM ".self::OBJECT_TABLE." WHERE id = ".$id." AND file_id IS NOT NULL AND value_id IS NULL";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[file_id])
			{
				return $data[file_id];
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
	
}

?>
