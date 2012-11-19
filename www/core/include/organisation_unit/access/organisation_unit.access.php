<?php
/**
 * @package organisation_unit
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
 * Organisation Unit Access Class
 * @package organisation_unit
 */
class OrganisationUnit_Access
{
	const ORGANISATION_UNIT_PK_SEQUENCE = 'core_organisation_units_id_seq';

	private $organisation_unit_id;
	
	private $toid;
	private $is_root;
	private $name;
	private $type_id;
	private $stores_data;
	private $position;
	private $hidden;

	/**
	 * @param integer $organisation_unit_id
	 */
	function __construct($organisation_unit_id)
	{
		
		global $db;
		
		if ($organisation_unit_id == null or !is_numeric($organisation_unit_id))
		{
			$this->organisation_unit_id = null;
		}
		else
		{	
			$sql = "SELECT * FROM ".constant("ORGANISATION_UNIT_TABLE")." WHERE id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->organisation_unit_id 	= $data['id'];

				$this->toid						= $data['toid'];
				$this->name						= $data['name'];
				$this->type_id					= $data['type_id'];
				$this->position					= $data['position'];
				
				if ($data['is_root'] == "t")
				{
					$this->is_root = true;
				}
				else
				{
					$this->is_root = false;;
				}
				
				if ($data['stores_data'] == "t")
				{
					$this->stores_data = true;;
				}
				else
				{
					$this->stores_data = false;
				}
				
				if ($data['hidden'] == "t")
				{
					$this->hidden = true;
				}
				else
				{
					$this->hidden = false;
				}
			}
			else
			{
				$this->organisation_unit_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->organisation_unit_id) 
		{
			unset($this->organisation_unit_id);
	
			unset($this->toid);
			unset($this->is_root);
			unset($this->name);
			unset($this->type_id);
			unset($this->stores_data);
			unset($this->position);
			unset($this->hidden);
		}
	}
	
	/**
	 * @param integer $toid
	 * @param string $name
	 * @param integer $type_id
	 * @param bool $stores_data
	 * @param integer $positon
	 * @return integer
	 */
	public function create($toid, $name, $type_id, $stores_data, $position)
	{
		global $db;
		
		if ($name and $type_id and $position)
		{
			if ($stores_data == true)
			{
				$stores_data_insert = "t";
			}
			else
			{
				$stores_data_insert = "f";
			}
			
			if (is_numeric($toid))
			{
				$sql_write = "INSERT INTO ".constant("ORGANISATION_UNIT_TABLE")." (id, toid, is_root, name, type_id, stores_data, position, hidden) " .
								"VALUES (nextval('".self::ORGANISATION_UNIT_PK_SEQUENCE."'::regclass), '".$toid."','f','".$name."','".$type_id."','".$stores_data_insert."',".$position.",'f')";		
			
			}
			else
			{
				$sql_write = "INSERT INTO ".constant("ORGANISATION_UNIT_TABLE")." (id, toid, is_root, name, type_id, stores_data, position, hidden) " .
								"VALUES (nextval('".self::ORGANISATION_UNIT_PK_SEQUENCE."'::regclass), currval('".self::ORGANISATION_UNIT_PK_SEQUENCE."'::regclass),'t','".$name."','".$type_id."','".$stores_data_insert."',".$position.",'f')";		
			
			}
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." WHERE id = currval('".self::ORGANISATION_UNIT_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read['id']);
				
				return $data_read['id'];
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

		if ($this->organisation_unit_id)
		{	
			$organisation_unit_id_tmp = $this->organisation_unit_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("ORGANISATION_UNIT_TABLE")." WHERE id = ".$organisation_unit_id_tmp."";
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
	 * @return bool
	 */
	public function get_is_root()
	{
		if (isset($this->is_root))
		{
			return $this->is_root;
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
	 * @return bool
	 */
	public function get_stores_data()
	{
		if (isset($this->stores_data))
		{
			return $this->stores_data;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return integer
	 */
	public function get_position()
	{
		if ($this->position)
		{
			return $this->position;
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
	 * @param integer $toid
	 * @return bool
	 */
	public function set_toid($toid)
	{
		global $db;

		if ($this->organisation_unit_id and $toid)
		{
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_TABLE")." SET toid = ".$toid." WHERE id = ".$this->organisation_unit_id."";
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
	 * @param bool $is_root
	 * @return bool
	 */
	public function set_is_root($is_root)
	{
		global $db;

		if ($this->organisation_unit_id and isset($is_root))
		{
			if ($is_root == true)
			{
				$is_root_insert = "t";
			}
			else
			{
				$is_root_insert = "f";
			}
			
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_TABLE")." SET is_root = '".$is_root_insert."' WHERE id = ".$this->organisation_unit_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->is_root = $is_root;
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

		if ($this->organisation_unit_id and $name)
		{
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_TABLE")." SET name = '".$name."' WHERE id = ".$this->organisation_unit_id."";
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
	 * @param integer $type_id
	 * @return bool
	 */
	public function set_type_id($type_id)
	{
		global $db;
			
		if ($this->organisation_unit_id and $type_id)
		{
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_TABLE")." SET type_id = ".$type_id." WHERE id = ".$this->organisation_unit_id."";
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
	 * @param bool $stores_data
	 * @return bool
	 */
	public function set_stores_data($stores_data)
	{
		global $db;

		if ($this->organisation_unit_id and isset($stores_data))
		{
			if ($stores_data == true)
			{
				$stores_data_insert = "t";
			}
			else
			{
				$stores_data_insert = "f";
			}
			
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_TABLE")." SET stores_data = '".$stores_data_insert."' WHERE id = ".$this->organisation_unit_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->stores_data = $stores_data;
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
	 * @param integer $position
	 * @return bool
	 */
	public function set_position($position)
	{
		global $db;
			
		if ($this->organisation_unit_id and $position)
		{
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_TABLE")." SET position = ".$position." WHERE id = ".$this->organisation_unit_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->position = $position;
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

		if ($this->organisation_unit_id and isset($hidden))
		{
			if ($hidden == true)
			{
				$hidden_insert = "t";
			}
			else
			{
				$hidden_insert = "f";
			}
			
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_TABLE")." SET hidden = '".$hidden_insert."' WHERE id = ".$this->organisation_unit_id."";
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
	 * @return integer
	 */
	public function get_previous_position()
	{
		global $db;
		
		if ($this->organisation_unit_id)
		{
			if(is_numeric($this->toid) and $this->is_root == false)
			{
				$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
					"WHERE toid=".$this->toid." " .
						"AND is_root = 'f' " .
						"AND position = ".($this->position-1)."";
			}
			else
			{
				$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
					"WHERE is_root = 't' " .
						"AND position = ".($this->position-1)."";
			}
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				return $data['id'];
			}
			else
			{
				return $this->organisation_unit_id;
			}
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_next_position()
	{
		global $db;
		
		if ($this->organisation_unit_id)
		{
			if(is_numeric($this->toid) and $this->is_root == false)
			{
				$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
					"WHERE toid=".$this->toid." " .
						"AND is_root = 'f' " .
						"AND position = ".($this->position+1)."";
			}
			else
			{
				$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
					"WHERE is_root = 't' " .
						"AND position = ".($this->position+1)."";
			}
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				return $data['id'];
			}
			else
			{
				return $this->organisation_unit_id;
			}
		}	
	}
	
	
	/**
	 * @param integer $toid
	 * @return integer
	 */
	public static function get_highest_position_by_toid($toid)
	{
		global $db;
		
		if (is_numeric($toid))
		{
			$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
					"WHERE toid=".$toid." " .
						"AND is_root = 'f' " .
						"AND position = " .
							"(SELECT MAX(position) FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
									"WHERE toid=".$toid." " .
										"AND is_root = 'f')";
		}
		else
		{
			$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
					"WHERE is_root = 't' " .
						"AND position = " .
							"(SELECT MAX(position) FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
									"WHERE is_root = 't')";
		}

		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data['id'])
		{
			return $data['id'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $position
	 * @param integer $toid
	 * @return integer
	 */
	public static function get_id_by_position_and_toid($position, $toid)
	{
		global $db;
		
		if (is_numeric($position))
		{
			
			if (is_numeric($toid))
			{
				$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
						"WHERE toid=".$toid." " .
							"AND is_root = 'f' " .
							"AND position = ".$position."";
			}
			else
			{
				$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." " .
						"WHERE is_root = 't' " .
							"AND position = ".$position."";
			}
	
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				return $data['id'];
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
	 * @param string $name
	 * @return integer
	 */
	public static function get_organisation_unit_id_by_name($name)
	{	
		global $db;
		
		if ($name)
		{						
			$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." WHERE LOWER(name) = '".$name."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				return $data['id'];
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
		
		$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." ORDER BY position";
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
	 * @param integer $toid
	 * @return array
	 */
	public static function list_entries_by_toid($toid)
	{
		global $db;
			
		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." WHERE toid = ".$toid." AND toid != id ORDER BY position";
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
	public static function list_root_entries()
	{
		global $db;
			
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." WHERE is_root = 't' ORDER BY position";
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
	 * @return array
	 */
	public static function list_hidden_root_entries()
	{
		global $db;
					
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." WHERE is_root = 't ORDER BY position'";
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
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public static function exist_organisation_unit($organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			$return_array = array();	
												
			$sql = "SELECT id FROM ".constant("ORGANISATION_UNIT_TABLE")." WHERE id = ".$organisation_unit_id."";
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
	 * @return integer
	 */
	public static function count_organisation_units()
	{
		global $db;
											
		$sql = "SELECT COUNT(id) AS result FROM ".constant("ORGANISATION_UNIT_TABLE")."";
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data['result'])
		{
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
}
?>