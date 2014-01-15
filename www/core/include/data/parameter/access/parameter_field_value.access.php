<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Parameter Field Value Access Class
 * @package data
 */
class ParameterFieldValue_Access
{
	const PARAMETER_FIELD_VALUE_PK_SEQUENCE = 'core_data_parameter_field_values_id_seq';

	private $parameter_field_value_id;
	
	private $parameter_version_id;
	private $parameter_field_id;
	private $parameter_method_id;
	private $value;
	private $source;
	private $locked;

	/**
	 * @param integer $parameter_field_value_id
	 */
	function __construct($parameter_field_value_id)
	{
		global $db;
		
		if ($parameter_field_value_id == null)
		{
			$this->parameter_field_value_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_FIELD_VALUE_TABLE")." WHERE id='".$parameter_field_value_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->parameter_field_value_id	= $parameter_field_value_id;
				$this->parameter_version_id		= $data['parameter_version_id'];
				$this->parameter_field_id		= $data['parameter_field_id'];
				$this->parameter_method_id		= $data['parameter_method_id'];
				$this->value					= $data['value'];
				$this->source					= $data['source'];
				$this->locked					= $data['locked'];
			}
			else
			{
				$this->parameter_field_value_id			= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_field_value_id)
		{
			unset($this->parameter_field_value_id);
			unset($this->parameter_version_id);
			unset($this->parameter_field_id);
			unset($this->parameter_method_id);
			unset($this->value);
			unset($this->source);
			unset($this->locked);
		}
	}

	/**
	 * @param integer $parameter_version_id
	 * @param integer $parameter_field_id
	 * @param integer $parameter_method_id
	 * @param float $value
	 * @param string $source
	 * @param boolean $locked
	 * @return integer
	 */
	public function create($parameter_version_id, $parameter_field_id, $parameter_method_id, $value, $source = null, $locked = false)
	{
		global $db;
		
		if (is_numeric($parameter_version_id) and is_numeric($parameter_field_id) and is_numeric($value))
		{	
			if (is_numeric($parameter_method_id) and $parameter_method_id > 0)
			{
				$parameter_method_id_insert = $parameter_method_id;
			}
			else
			{
				$parameter_method_id_insert = "NULL";
			}
			
			if ($source)
			{
				$source_insert = "'".$source."'";
			}
			else
			{
				$source_insert = "NULL";
			}
			
			if ($locked === true)
			{
				$locked_insert = "t";
			}
			else
			{
				$locked_insert = "f";
			}
			
			$sql_write = "INSERT INTO ".constant("PARAMETER_FIELD_VALUE_TABLE")." (id,parameter_version_id,parameter_field_id,parameter_method_id,value,source,locked) " .
					"VALUES (nextval('".self::PARAMETER_FIELD_VALUE_PK_SEQUENCE."'::regclass),".$parameter_version_id.",".$parameter_field_id.",".$parameter_method_id_insert.",'".$value."',".$source_insert.",'".$locked_insert."')";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PARAMETER_FIELD_VALUE_TABLE")." WHERE id = currval('".self::PARAMETER_FIELD_VALUE_PK_SEQUENCE."'::regclass)";
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

		if ($this->parameter_field_value_id)
		{
			$parameter_field_value_id_tmp = $this->parameter_field_value_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_VALUE_TABLE")." WHERE id = ".$parameter_field_value_id_tmp."";
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
	public function get_parameter_version_id()
	{
		if ($this->parameter_version_id)
		{
			return $this->parameter_version_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_parameter_field_id()
	{
		if ($this->parameter_field_id)
		{
			return $this->parameter_field_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_parameter_method_id()
	{
		if ($this->parameter_method_id)
		{
			return $this->parameter_method_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return float
	 */
	public function get_value()
	{
		if (is_numeric($this->value))
		{
			return $this->value;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return text
	 */
	public function get_source()
	{
		if ($this->source)
		{
			return $this->source;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return boolean
	 */
	public function get_locked()
	{
		if (isset($this->locked))
		{
			return $this->locked;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $parameter_version_id
	 * @return bool
	 */
	public function set_parameter_version_id($parameter_version_id)
	{	
		global $db;

		if ($this->parameter_field_value_id and is_numeric($parameter_version_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_VALUE_TABLE")." SET parameter_version_id = '".$parameter_version_id."' WHERE id = ".$this->parameter_field_value_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->parameter_version_id = $parameter_version_id;
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
	 * @param integer $parameter_field_id
	 * @return bool
	 */
	public function set_parameter_field_id($parameter_field_id)
	{	
		global $db;

		if ($this->parameter_field_value_id and is_numeric($parameter_field_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_VALUE_TABLE")." SET parameter_field_id = '".$parameter_field_id."' WHERE id = ".$this->parameter_field_value_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->parameter_field_id = $parameter_field_id;
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
	 * @param integer $parameter_method_id
	 * @return bool
	 */
	public function set_parameter_method_id($parameter_method_id)
	{	
		global $db;

		if ($this->parameter_field_value_id and is_numeric($parameter_method_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_VALUE_TABLE")." SET parameter_method_id = '".$parameter_method_id."' WHERE id = ".$this->parameter_field_value_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->parameter_method_id = $parameter_method_id;
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
	 * @param integer $value
	 * @return bool
	 */
	public function set_value($value)
	{	
		global $db;

		if ($this->parameter_field_value_id and is_numeric($value))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_VALUE_TABLE")." SET value = '".$value."' WHERE id = ".$this->parameter_field_value_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->value = $value;
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
	 * @param string $source
	 * @return bool
	 */
	public function set_source($source)
	{	
		global $db;

		if ($this->parameter_field_value_id and $source)
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_VALUE_TABLE")." SET source = '".$source."' WHERE id = ".$this->parameter_field_value_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->source = $source;
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
	 * @param boolean $locked
	 * @return bool
	 */
	public function set_locked($locked)
	{	
		global $db;

		if ($this->parameter_field_value_id and isset($locked))
		{
			if ($locked === true)
			{
				$locked_insert = "t";
			}
			else
			{
				$locked_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PARAMETER_FIELD_VALUE_TABLE")." SET locked = '".$locked_insert."' WHERE id = ".$this->parameter_field_value_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->locked = $locked;
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
	 * @param integer $method_id
	 * @return bool
	 */
	public static function is_method_linked($method_id)
	{
		global $db;
		
		if(is_numeric($method_id))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_FIELD_VALUE_TABLE")." WHERE parameter_method_id='".$method_id."'";
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
	 * @param integer $parameter_version_id
	 * @return array
	 */
	public static function list_values($parameter_version_id)
	{
		global $db;
		
		if(is_numeric($parameter_version_id))
		{
			$return_array = array();
			
			$sql = "SELECT parameter_field_id,value FROM ".constant("PARAMETER_FIELD_VALUE_TABLE")." WHERE parameter_version_id='".$parameter_version_id."'";
			$res = $db->db_query($sql);
			
			while($data = $db->db_fetch_assoc($res))
			{
				$return_array[$data['parameter_field_id']] = $data['value'];
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
	 * @param integer $parameter_version_id
	 * @return array
	 */
	public static function list_methods($parameter_version_id)
	{
		global $db;
		
		if(is_numeric($parameter_version_id))
		{
			$return_array = array();
			
			$sql = "SELECT parameter_field_id,parameter_method_id FROM ".constant("PARAMETER_FIELD_VALUE_TABLE")." WHERE parameter_version_id='".$parameter_version_id."'";
			$res = $db->db_query($sql);
			
			while($data = $db->db_fetch_assoc($res))
			{
				$return_array[$data['parameter_field_id']] = $data['parameter_method_id'];
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

	public static function delete_by_parameter_version_id($parameter_version_id)
	{
		global $db;

		if ($parameter_version_id)
		{
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_VALUE_TABLE")." WHERE parameter_version_id = ".$parameter_version_id."";
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
	
	/**
	 * @param integer $parameter_version_id
	 * @param integer $parameter_field_id
	 * @return integer
	 */
	public static function get_id_by_version_id_and_field_id($parameter_version_id, $parameter_field_id)
	{
		global $db;
		
		if(is_numeric($parameter_version_id) and is_numeric($parameter_field_id))
		{			
			$sql = "SELECT id FROM ".constant("PARAMETER_FIELD_VALUE_TABLE")." WHERE parameter_version_id='".$parameter_version_id."' AND parameter_field_id='".$parameter_field_id."'";
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
}
?>