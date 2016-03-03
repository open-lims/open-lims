<?php
/**
 * @package location
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
 * Location Access Class
 * @package location
 */
class Location_Access
{
	const LOCATION_PK_SEQUENCE = 'core_locations_id_seq';

	private $location_id;
	private $toid;
	private $type_id;
	private $name;
	private $additional_name;
	private $prefix;
	
	/**
	 * @param integer $location_id
	 */
	function __construct($location_id)
	{
		global $db;

		if ($location_id == null)
		{
			$this->location_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("LOCATION_TABLE")." WHERE id='".$location_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->location_id 		= $location_id;
				$this->toid				= $data['toid'];
				$this->type_id			= $data['type_id'];
				$this->name				= $data['name'];
				$this->additional_name	= $data['additional_name'];

				if ($data['prefix'] == 't')
				{
					$this->prefix = true;
				}
				else
				{
					$this->prefix = false;
				}
			}
			else
			{
				$this->location_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->location_id)
		{
			unset($this->location_id);
			unset($this->toid);
			unset($this->type_id);
			unset($this->name);
			unset($this->additional_name);
			unset($this->prefix);
		}
	}
	
	/**
	 * @param integer $toid
	 * @param string $name
	 * @return integer
	 */
	public function create($toid, $type_id, $name, $additional_name, $prefix)
	{
		global $db;
		
		if ($type_id and $name)
		{
			if ($additional_name)
			{
				$additional_name_insert = "'".$additional_name."'";
			}
			else
			{
				$additional_name_insert = "NULL";
			}
			
			if ($prefix == true)
			{
				$prefix_insert = "t";
			}
			else
			{
				$prefix_insert = "f";
			}
			
			if (!is_numeric($toid) or $toid == 0)
			{
				$sql_write = "INSERT INTO ".constant("LOCATION_TABLE")." (id,toid,type_id,name,additional_name,prefix) " .
							"VALUES (nextval('".self::LOCATION_PK_SEQUENCE."'::regclass),NULL,".$type_id.",'".$name."',".$additional_name_insert.",'".$prefix_insert."')";
			}
			else
			{
				$sql_write = "INSERT INTO ".constant("LOCATION_TABLE")." (id,toid,type_id,name,additional_name,prefix) " .
							"VALUES (nextval('".self::LOCATION_PK_SEQUENCE."'::regclass),".$toid.",".$type_id.",'".$name."',".$additional_name_insert.",'".$prefix_insert."')";
			}


			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("LOCATION_TABLE")." WHERE id = currval('".self::LOCATION_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				self::__construct($data_read['id']);
			
				return $data_read['id'];
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
		
		if ($this->location_id)
		{
			$tmp_location_id = $this->location_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("LOCATION_TABLE")." WHERE id = ".$tmp_location_id."";
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
	public function get_type_id()
	{
		if ($this->type_id)
		{
			return $this->type_id;
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
	 * @return string
	 */
	public function get_additional_name()
	{
		if ($this->additional_name)
		{
			return $this->additional_name;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_prefix()
	{
		if (isset($this->prefix))
		{
			return $this->prefix;
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
			
		if ($this->location_id and is_numeric($toid))
		{
			$sql = "UPDATE ".constant("LOCATION_TABLE")." SET toid = ".$toid." WHERE id = '".$this->location_id."'";
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
	 * @param integer $type_id
	 * @return bool
	 */
	public function set_type_id($type_id)
	{
		global $db;
			
		if ($this->location_id and is_numeric($type_id))
		{
			$sql = "UPDATE ".constant("LOCATION_TABLE")." SET type_id = ".$type_id." WHERE id = '".$this->location_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->type_id = $type_id;
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
			
		if ($this->location_id and $name)
		{
			$sql = "UPDATE ".constant("LOCATION_TABLE")." SET name = '".$name."' WHERE id = '".$this->location_id."'";
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
	 * @param string $additional_name
	 * @return bool
	 */
	public function set_additional_name($additional_name)
	{
		global $db;
			
		if ($this->location_id)
		{
			$sql = "UPDATE ".constant("LOCATION_TABLE")." SET additional_name = '".$additional_name."' WHERE id = '".$this->location_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->additional_name = $additional_name;
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
	 * @param bool $prefix
	 * @return bool
	 */
	public function set_prefix($prefix)
	{
		global $db;
			
		if ($this->location_id and isset($prefix))
		{
			if ($prefix == true)
			{
				$prefix_insert = "t";
			}
			else
			{
				$prefix_insert = "f";
			}
			
			$sql = "UPDATE ".constant("LOCATION_TABLE")." SET prefix = '".$prefix_insert."' WHERE id = '".$this->location_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->prefix = $prefix;
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
			$sql = "SELECT id FROM ".constant("LOCATION_TABLE")." WHERE id = '".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
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
	 * @return array
	 */
	public static function list_root_entries()
	{
		global $db;
				
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("LOCATION_TABLE")." WHERE toid IS NULL OR id = toid ORDER BY id";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data['id']);
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
	 * @param integer $id
	 * @return array
	 */
	public static function list_entries_by_id($id)
	{
		global $db;

		if (is_numeric($id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("LOCATION_TABLE")." WHERE id != toid AND toid = '".$id."' ORDER BY id";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['id']);
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
	public static function list_entries()
	{
		global $db;
				
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("LOCATION_TABLE")." ORDER BY id";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data['id']);
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
