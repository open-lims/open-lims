<?php
/**
 * @package data
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
 * Parameter Field Limit Access Class
 * @package data
 */
class ParameterFieldLimit_Access
{
	private $parameter_limit_id;
	private $parameter_field_id;
	private $upper_specification_limit;
	private $lower_specification_limit;

	/**
	 * @param integer $parameter_limit_id
	 * @param integer $parameter_field_id
	 */
	function __construct($parameter_limit_id, $parameter_field_id)
	{
		global $db;
		
		if ($parameter_limit_id == null or $parameter_field_id == null)
		{
			$this->parameter_limit_id = null;
			$this->parameter_field_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE parameter_limit_id='".$parameter_limit_id."' AND parameter_field_id='".$parameter_field_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['parameter_limit_id'] and $data['parameter_field_id'])
			{
				$this->parameter_limit_id		= $parameter_limit_id;
				$this->parameter_field_id		= $parameter_field_id;
				$this->upper_specification_limit	= $data['upper_specification_limit'];
				$this->lower_specification_limit	= $data['lower_specification_limit'];
			}
			else
			{
				$this->parameter_limit_id = null;
				$this->parameter_field_id = null;
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
	 * @param integer $parameter_limit_id
	 * @param integer $parameter_field_id
	 * @param float $upper_specification_limit
	 * @param float $lower_specification_limit
	 * @return bool
	 */
	public function create($parameter_limit_id, $parameter_field_id, $upper_specification_limit, $lower_specification_limit)
	{
		global $db;
		
		if (is_numeric($parameter_limit_id) and is_numeric($parameter_field_id) and (is_numeric($upper_specification_limit) or is_numeric($lower_specification_limit)))
		{	
			if (is_numeric($upper_specification_limit))
			{
				$upper_specification_limit_insert = $upper_specification_limit;
			}
			else
			{
				$upper_specification_limit_insert = "NULL";
			}
			
			if (is_numeric($lower_specification_limit))
			{
				$lower_specification_limit_insert = $lower_specification_limit;
			}
			else
			{
				$lower_specification_limit_insert = "NULL";
			}
			
			$sql_write = "INSERT INTO ".constant("PARAMETER_FIELD_LIMIT_TABLE")." (parameter_limit_id,parameter_field_id,upper_specification_limit,lower_specification_limit) " .
					"VALUES (".$parameter_limit_id.",".$parameter_field_id.",".$upper_specification_limit_insert.",".$lower_specification_limit_insert.")";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->row_count($res_write) == 1)
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;

		if ($this->parameter_limit_id and $this->parameter_field_id)
		{
			$parameter_limit_id_tmp = $this->parameter_limit_id;
			$parameter_field_id_tmp = $this->parameter_field_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE parameter_limit_id = ".$parameter_limit_id_tmp." AND parameter_field_id = ".$parameter_field_id_tmp."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res) == 1)
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
	 * @return float
	 */
	public function get_upper_specification_limit()
	{
		if (isset($this->upper_specification_limit))
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
		if (isset($this->lower_specification_limit))
		{
			return $this->lower_specification_limit;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param integer $upper_specification_limit
	 * @return bool
	 */
	public function set_upper_specification_limit($upper_specification_limit)
	{	
		global $db;

		if ($this->parameter_limit_id and $this->parameter_field_id and is_numeric($upper_specification_limit))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_LIMIT_TABLE")." SET upper_specification_limit = '".$upper_specification_limit."' WHERE parameter_limit_id = ".$this->parameter_limit_id." AND parameter_field_id = ".$this->parameter_field_id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
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

		if ($this->parameter_limit_id and $this->parameter_field_id and is_numeric($lower_specification_limit))
		{
			$sql = "UPDATE ".constant("PARAMETER_FIELD_LIMIT_TABLE")." SET lower_specification_limit = '".$lower_specification_limit."' WHERE parameter_limit_id = ".$this->parameter_limit_id." AND parameter_field_id = ".$this->parameter_field_id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
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
	
	
	/**
	 * @param integer $parameter_field_id
	 * @return bool
	 */
	public static function list_field_limits_by_parameter_field_id($parameter_field_id)
	{
		global $db;
		
		if (is_numeric($parameter_field_id))
		{
			$return_array = array();
			
			$sql = "SELECT parameter_limit_id,upper_specification_limit,lower_specification_limit FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE parameter_field_id = ".$parameter_field_id." ORDER BY parameter_limit_id";
			$res = $db->db_query($sql);
			
			while ($data = $db->fetch($res))
			{
				$return_array[$data['parameter_limit_id']]['usl'] = $data['upper_specification_limit'];
				$return_array[$data['parameter_limit_id']]['lsl'] = $data['lower_specification_limit'];
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
	 * @param array $parameter_field_array
	 * @return bool
	 */
	public static function list_parameter_limits_by_parameter_field_array($parameter_field_array)
	{
		global $db;
		
		if (is_array($parameter_field_array) and count($parameter_field_array) >= 1)
		{
			$return_array = array();
			
			$sql = "SELECT DISTINCT parameter_limit_id FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE parameter_field_id IN (".implode(",",$parameter_field_array).")";
			$res = $db->db_query($sql);
			
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data['parameter_limit_id']);
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
	
	public static function list_limits_by_limit_id($parameter_limit_id)
	{
		global $db;
		
		if (is_numeric($parameter_limit_id))
		{
			$return_array = array();
			
			$sql = "SELECT lower_specification_limit, upper_specification_limit, parameter_field_id FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE parameter_limit_id = ".$parameter_limit_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->fetch($res))
			{
				$return_array[$data['parameter_field_id']] = array("lsl" => $data['lower_specification_limit'], "usl" => $data['upper_specification_limit']);
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
	 * @param integer $parameter_limit_id
	 * @param integer $parameter_field_id
	 * @return bool
	 */
	public static function exist_field_limit($parameter_limit_id, $parameter_field_id)
	{
		global $db;
		
		if (is_numeric($parameter_limit_id) and is_numeric($parameter_field_id))
		{
			$sql = "SELECT parameter_limit_id FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE parameter_limit_id = ".$parameter_limit_id." AND parameter_field_id = ".$parameter_field_id."";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['parameter_limit_id'])
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
	 * @param integer $parameter_field_id
	 * @return bool
	 */
	public static function delete_limits_by_parameter_field_id($parameter_field_id)
	{
		global $db;
		
		if (is_numeric($parameter_field_id))
		{
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE parameter_field_id = ".$parameter_field_id."";
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
	 * @param integer $parameter_limit_id
	 * @return bool
	 */
	public static function delete_limits_by_parameter_limit_id($parameter_limit_id)
	{
		global $db;
		
		if (is_numeric($parameter_limit_id))
		{
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_LIMIT_TABLE")." WHERE parameter_limit_id = ".$parameter_limit_id."";
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