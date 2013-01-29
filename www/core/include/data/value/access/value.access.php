<?php
/**
 * @package data
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
 * Value Access Class
 * @package data
 */
class Value_Access
{
	const VALUE_PK_SEQUENCE = 'core_values_id_seq';

	private $value_id;
	private $data_entity_id;
	private $type_id;

	/**
	 * @param integer $value_id
	 */
	function __construct($value_id)
	{
		global $db;
		
		if ($value_id == null)
		{
			$this->value_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("VALUE_TABLE")." WHERE id='".$value_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->value_id			= $value_id;
				$this->data_entity_id	= $data['data_entity_id'];
				$this->type_id			= $data['type_id'];
			}
			else
			{
				$this->value_id			= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->value_id)
		{
			unset($this->value_id);
			unset($this->data_entity_id);
			unset($this->type_id);
		}
	}

	/**
	 * @param integer $data_entity_id
	 * @param integer $type_id
	 * @return integer
	 */
	public function create($data_entity_id, $type_id)
	{
		global $db;
		
		if (is_numeric($data_entity_id))
		{	
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("VALUE_TABLE")." (id,data_entity_id,type_id) " .
					"VALUES (nextval('".self::VALUE_PK_SEQUENCE."'::regclass),".$data_entity_id.",".$type_id.")";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("VALUE_TABLE")." WHERE id = currval('".self::VALUE_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
									
				$this->__construct($data_read['id']);
				
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

		if ($this->value_id)
		{
			$value_id_tmp = $this->value_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("VALUE_TABLE")." WHERE id = ".$value_id_tmp."";
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
	public function get_data_entity_id()
	{
		if ($this->data_entity_id)
		{
			return $this->data_entity_id;
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
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public function set_data_entity_id($data_entity_id)
	{	
		global $db;

		if ($this->value_id and is_numeric($data_entity_id))
		{
			$sql = "UPDATE ".constant("VALUE_TABLE")." SET data_entity_id = ".$data_entity_id." WHERE id = ".$this->value_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->data_entity_id = $data_entity_id;
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
	
		if ($this->value_id and is_numeric($type_id))
		{
			$sql = "UPDATE ".constant("VALUE_TABLE")." SET type_id = ".$type_id." WHERE id = ".$this->value_id."";
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
	 * @param string $data_entity_id
	 * @return integer
	 */
	public static function get_entry_by_data_entity_id($data_entity_id)
	{
		global $db;

		if (is_numeric($data_entity_id))
		{
			$sql = "SELECT id FROM ".constant("VALUE_TABLE")." WHERE data_entity_id = '".$data_entity_id."'";

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
	 * @param integer $type_id
	 * @return array
	 */
	public static function list_entries_by_type_id($type_id)
	{
		global $db;
		
		if (is_numeric($type_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("VALUE_TABLE")." WHERE type_id = ".$type_id."";
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
	 * @param integer $value_id
	 * @param integer $type_id
	 * @return array
	 */
	public static function is_entry_type_of($value_id, $type_id)
	{
		global $db;
		
		if (is_numeric($value_id) and is_numeric($type_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("VALUE_TABLE")." WHERE id= ".$value_id." AND type_id = ".$type_id."";
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
	 * @param integer $value_id
	 * @return bool
	 */
	public static function exist_value_by_value_id($value_id)
	{
		global $db;
			
		if (is_numeric($value_id))
		{
			$sql = "SELECT id FROM ".constant("VALUE_TABLE")." WHERE id = ".$value_id."";
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
}

?>
