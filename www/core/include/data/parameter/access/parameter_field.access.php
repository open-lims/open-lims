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
 * Parameter Field Access Class
 * @package data
 */
class ParameterField_Access
{
	const PARAMETER_FIELD_PK_SEQUENCE = 'core_data_parameter_fields_id_seq';

	private $parameter_field_id;
	
	private $name;
	private $min_value;
	private $max_value;
	private $measuring_unit_id;
	private $measuring_unit_exponent;
	private $measuring_unit_ratio_id;

	/**
	 * @param integer $parameter_field_id
	 */
	function __construct($parameter_field_id)
	{
		global $db;
		
		if ($parameter_field_id == null)
		{
			$this->parameter_field_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_FIELD_TABLE")." WHERE id='".$parameter_field_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->parameter_field_id		= $parameter_field_id;
				$this->name						= $data['name'];
				$this->min_value				= $data['min_value'];
				$this->max_value				= $data['max_value'];
				$this->measuring_unit_id		= $data['measuring_unit_id'];
				$this->measuring_unit_exponent	= $data['measuring_unit_exponent'];
				$this->measuring_unit_ratio_id	= $data['measuring_unit_ratio_id'];
			}
			else
			{
				$this->parameter_field_id		= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_field_id)
		{
			unset($this->parameter_field_id);
			unset($this->name);
			unset($this->min_value);
			unset($this->max_value);
			unset($this->measuring_unit_id);
			unset($this->measuring_unit_exponent);
			unset($this->measuring_unit_ratio_id);
		}
	}

	/**
	 * @param string $name
	 * @param float $min_value
	 * @param float $max_value
	 * @param integer $measuring_unit_id
	 * @return integer
	 */
	public function create($name, $min_value, $max_value, $measuring_unit_id, $measuring_unit_exponent, $measuring_unit_ratio_id)
	{
		global $db;
		
		if ($name)
		{	
			if (is_numeric($min_value))
			{
				$min_value_insert = $min_value;
			}
			else
			{
				$min_value_insert = "NULL";
			}
			
			if (is_numeric($max_value))
			{
				$max_value_insert = $max_value;
			}
			else
			{
				$max_value_insert = "NULL";
			}
			
			if (is_numeric($measuring_unit_id))
			{
				$measuring_unit_id_insert = $measuring_unit_id;
			}
			else
			{
				$measuring_unit_id_insert = "NULL";
			}
			
			if (is_numeric($measuring_unit_exponent))
			{
				$measuring_unit_exponent_insert = $measuring_unit_exponent;
			}
			else
			{
				$measuring_unit_exponent_insert = "NULL";
			}
			
			if (is_numeric($measuring_unit_ratio_id))
			{
				$measuring_unit_ratio_id_insert = $measuring_unit_ratio_id;
			}
			else
			{
				$measuring_unit_ratio_id_insert = "NULL";
			}
			
			$sql_write = "INSERT INTO ".constant("PARAMETER_FIELD_TABLE")." (id,name,min_value,max_value,measuring_unit_id,measuring_unit_exponent,measuring_unit_ratio_id) " .
					"VALUES (nextval('".self::PARAMETER_FIELD_PK_SEQUENCE."'::regclass),'".$name."',".$min_value_insert.",".$max_value_insert.",".$measuring_unit_id_insert.",".$measuring_unit_exponent_insert.",".$measuring_unit_ratio_id_insert.")";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PARAMETER_FIELD_TABLE")." WHERE id = currval('".self::PARAMETER_FIELD_PK_SEQUENCE."'::regclass)";
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

		if ($this->parameter_field_id)
		{
			$parameter_field_id_tmp = $this->parameter_field_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_TABLE")." WHERE id = ".$parameter_field_id_tmp."";
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
	 * @return float
	 */
	public function get_min_value()
	{
		if (isset($this->min_value))
		{
			return $this->min_value;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return float
	 */
	public function get_max_value()
	{
		if (isset($this->max_value))
		{
			return $this->max_value;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_measuring_unit_id()
	{
		if ($this->measuring_unit_id)
		{
			return $this->measuring_unit_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_measuring_unit_exponent()
	{
		if ($this->measuring_unit_exponent)
		{
			return $this->measuring_unit_exponent;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_measuring_unit_ratio_id()
	{
		if ($this->measuring_unit_ratio_id)
		{
			return $this->measuring_unit_ratio_id;
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

		if ($this->parameter_field_id and $name)
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_TABLE")." SET name = '".$name."' WHERE id = ".$this->parameter_field_id."";
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
	 * @param float $min_value
	 * @return bool
	 */
	public function set_min_value($min_value)
	{	
		global $db;

		if ($this->parameter_field_id and is_numeric($min_value))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_TABLE")." SET min_value = '".$min_value."' WHERE id = ".$this->parameter_field_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->min_value = $min_value;
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
	 * @param float $max_value
	 * @return bool
	 */
	public function set_max_value($max_value)
	{	
		global $db;

		if ($this->parameter_field_id and is_numeric($max_value))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_TABLE")." SET max_value = '".$max_value."' WHERE id = ".$this->parameter_field_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->max_value = $max_value;
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
	 * @param integer $measuring_unit_id
	 * @return bool
	 */
	public function set_measuring_unit_id($measuring_unit_id)
	{	
		global $db;

		if ($this->parameter_field_id)
		{
			if (is_numeric($measuring_unit_id))
			{
				$measuring_unit_id_insert = $measuring_unit_id;
			}
			else
			{
				$measuring_unit_id_insert = "NULL";
			}
			
			$sql = "UPDATE ".constant("PARAMETER_FIELD_TABLE")." SET measuring_unit_id = ".$measuring_unit_id_insert." WHERE id = ".$this->parameter_field_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->measuring_unit_id = $measuring_unit_id;
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
	 * @param integer $measuring_unit_exponent
	 * @return bool
	 */
	public function set_measuring_unit_exponent($measuring_unit_exponent)
	{	
		global $db;

		if ($this->parameter_field_id)
		{
			if (is_numeric($measuring_unit_exponent))
			{
				$measuring_unit_exponent_insert = $measuring_unit_exponent;
			}
			else
			{
				$measuring_unit_exponent_insert = "NULL";
			}
			
			$sql = "UPDATE ".constant("PARAMETER_FIELD_TABLE")." SET measuring_unit_exponent = ".$measuring_unit_exponent_insert." WHERE id = ".$this->parameter_field_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->measuring_unit_exponent = $measuring_unit_exponent;
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
	 * @param integer $measuring_unit_ratio_id
	 * @return bool
	 */
	public function set_measuring_unit_ratio_id($measuring_unit_ratio_id)
	{	
		global $db;

		if ($this->parameter_field_id)
		{
			if (is_numeric($measuring_unit_ratio_id))
			{
				$measuring_unit_ratio_id_insert = $measuring_unit_ratio_id;
			}
			else
			{
				$measuring_unit_ratio_id_insert = "NULL";
			}
			
			$sql = "UPDATE ".constant("PARAMETER_FIELD_TABLE")." SET measuring_unit_ratio_id = ".$measuring_unit_ratio_id_insert." WHERE id = ".$this->parameter_field_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->measuring_unit_ratio_id = $measuring_unit_ratio_id;
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