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
 * Parameter Method Access Class
 * @package data
 */
class ParameterMethod_Access
{
	const PARAMETER_METHOD_PK_SEQUENCE = 'core_data_parameter_methods_id_seq';

	private $parameter_method_id;
	private $name;

	/**
	 * @param integer $parameter_method_id
	 */
	function __construct($parameter_method_id)
	{
		global $db;
		
		if ($parameter_method_id == null)
		{
			$this->parameter_method_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_METHOD_TABLE")." WHERE id='".$parameter_method_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->parameter_method_id	= $parameter_method_id;
				$this->name					= $data['name'];
			}
			else
			{
				$this->parameter_method_id		= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_method_id)
		{
			unset($this->parameter_method_id);
			unset($this->name);
		}
	}

	/**
	 * @param string $name
	 * @return integer
	 */
	public function create($name)
	{
		global $db;
		
		if ($name)
		{				
			$sql_write = "INSERT INTO ".constant("PARAMETER_METHOD_TABLE")." (id,name) " .
					"VALUES (nextval('".self::PARAMETER_METHOD_PK_SEQUENCE."'::regclass),'".$name."')";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PARAMETER_METHOD_TABLE")." WHERE id = currval('".self::PARAMETER_METHOD_PK_SEQUENCE."'::regclass)";
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

		if ($this->parameter_method_id)
		{
			$parameter_method_id_tmp = $this->parameter_method_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_METHOD_TABLE")." WHERE id = ".$parameter_method_id_tmp."";
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{	
		global $db;

		if ($this->parameter_method_id and $name)
		{
			$sql = "UPDATE ".constant("PARAMETER_METHOD_TABLE")." SET name = '".$name."' WHERE id = ".$this->parameter_method_id."";
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
	 * @return array
	 */
	public static function list_methods()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT id, name FROM ".constant("PARAMETER_METHOD_TABLE")." ORDER BY name";
		$res = $db->db_query($sql);
		
		while($data = $db->db_fetch_assoc($res))
		{
			$return_array[$data['id']] = $data['name'];
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
	 * @param integer $method_id
	 * @return bool
	 */
	public static function exist_id($method_id)
	{
		global $db;
			
		if (is_numeric($method_id))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_METHOD_TABLE")." WHERE id = ".$method_id."";
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