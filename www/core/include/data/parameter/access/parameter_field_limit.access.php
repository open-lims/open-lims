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
 * Parameter FieldLimit Access Class
 * @package data
 */
class ParameterFieldLimit_Access
{
	const PARAMETER_FIELD_LIMIT_PK_SEQUENCE = 'core_data_parameter_field_limits_id_seq';

	private $parameter_field_limit_id;
	
	private $parameter_field_id;
	private $name;
	private $upper_specification_limit;
	private $lower_specification_limit;

	/**
	 * @param integer $parameter_field_limit_id
	 */
	function __construct($parameter_field_limit_id)
	{
		global $db;
		
		if ($parameter_field_limit_id == null)
		{
			$this->parameter_field_limit_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE id='".$parameter_field_limit_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->parameter_field_limit_id	= $parameter_field_limit_id;
				$this->parameter_field_id		= $data['parameter_field_id'];
				$this->name						= $data['name'];
				$this->upper_specification_limit	= $data['upper_specification_limit'];
				$this->lower_specification_limit	= $data['lower_specification_limit'];
			}
			else
			{
				$this->parameter_field_limit_id			= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_field_limit_id)
		{
			unset($this->parameter_field_limit_id);
			unset($this->parameter_field_id);
			unset($this->name);
			unset($this->upper_specification_limit);
			unset($this->lower_specification_limit);
		}
	}

	/**
	 * @param integer $parameter_field_id
	 * @param string $name
	 * @param float $upper_specification_limit
	 * @param float $lower_specification_limit
	 * @return integer
	 */
	public function create($parameter_field_id, $name, $upper_specification_limit, $lower_specification_limit)
	{
		global $db;
		
		if (is_numeric($parameter_field_id) and $name and is_numeric($upper_specification_limit) and is_numeric($lower_specification_limit))
		{	
			$sql_write = "INSERT INTO ".constant("PARAMETER_FIELD_LIMIT_TABLE")." (id,parameter_field_id,name,upper_specification_limit,lower_specification_limit) " .
					"VALUES (nextval('".self::PARAMETER_FIELD_LIMIT_PK_SEQUENCE."'::regclass),".$parameter_field_id.",'".$name."',".$upper_specification_limit.",".$lower_specification_limit.")";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE id = currval('".self::PARAMETER_FIELD_LIMIT_PK_SEQUENCE."'::regclass)";
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

		if ($this->parameter_field_limit_id)
		{
			$parameter_field_limit_id_tmp = $this->parameter_field_limit_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE id = ".$parameter_field_limit_id_tmp."";
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
	public function get_upper_specification_limit()
	{
		if ($this->upper_specification_limit)
		{
			return $this->upper_specification_limit;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return float
	 */
	public function get_lower_specification_limit()
	{
		if ($this->lower_specification_limit)
		{
			return $this->lower_specification_limit;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param integer $parameter_field_id
	 * @return bool
	 */
	public function set_parameter_field_id($parameter_field_id)
	{	
		global $db;

		if ($this->parameter_field_limit_id and is_numeric($parameter_field_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_LIMIT_TABLE")." SET parameter_field_id = '".$parameter_field_id."' WHERE id = ".$this->parameter_field_limit_id."";
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{	
		global $db;

		if ($this->parameter_field_limit_id and $name)
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_LIMIT_TABLE")." SET name = '".$name."' WHERE id = ".$this->parameter_field_limit_id."";
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
	 * @param integer $upper_specification_limit
	 * @return bool
	 */
	public function set_upper_specification_limit($upper_specification_limit)
	{	
		global $db;

		if ($this->parameter_field_limit_id and is_numeric($upper_specification_limit))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_LIMIT_TABLE")." SET upper_specification_limit = '".$upper_specification_limit."' WHERE id = ".$this->parameter_field_limit_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->upper_specification_limit = $upper_specification_limit;
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
	 * @param integer $lower_specification_limit
	 * @return bool
	 */
	public function set_lower_specification_limit($lower_specification_limit)
	{	
		global $db;

		if ($this->parameter_field_limit_id and is_numeric($lower_specification_limit))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_LIMIT_TABLE")." SET lower_specification_limit = '".$lower_specification_limit."' WHERE id = ".$this->parameter_field_limit_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->lower_specification_limit = $lower_specification_limit;
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