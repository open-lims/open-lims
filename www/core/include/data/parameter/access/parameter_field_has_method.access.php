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
 * Parameter Field Has Method Access Class
 * @package data
 */
class ParameterFieldFieldHasMethod_Access
{
	private $parameter_field_id;
	private $parameter_method_id;

	/**
	 * @param integer $parameter_field_id
	 * @param integer $parameter_method_id
	 */
	function __construct($parameter_field_id, $parameter_method_id)
	{
		global $db;
		
		if ($parameter_field_id == null or $parameter_method_id == null)
		{
			$this->parameter_field_id = null;
			$this->parameter_method_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_FIELD_HAS_METHOD_TABLE")." WHERE parameter_field_id='".$parameter_field_id."' AND parameter_method_id='".$parameter_method_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['parameter_field_id'] and $data['parameter_method_id'])
			{
				$this->parameter_field_id	= $parameter_field_id;
				$this->parameter_method_id 	= $parameter_method_id;
			}
			else
			{
				$this->parameter_field_id	= null;
				$this->parameter_method_id 	= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_field_id and $this->parameter_method_id)
		{
			unset($this->parameter_field_id);
			unset($this->parameter_method_id);
		}
	}

	/**
	 * @param integer $parameter_field_id
	 * @param integer $parameter_method_id
	 * @return boolean
	 */
	public function create($parameter_field_id, $parameter_method_id)
	{
		global $db;
		
		if (is_numeric($parameter_field_id) and is_numeric($parameter_method_id))
		{	
			$sql_write = "INSERT INTO ".constant("PARAMETER_FIELD_HAS_METHOD_TABLE")." (parameter_field_id,parameter_method_id) " .
					"VALUES ('".$parameter_field_id."','".$parameter_method_id."')";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->row_count($res_write) == 1)
			{
				self::__construct($parameter_field_id, $parameter_method_id);
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

		if ($this->parameter_field_id and $this->parameter_method_id)
		{
			$parameter_field_id_tmp = $this->parameter_field_id;
			$parameter_method_id_tmp = $this->parameter_method_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_HAS_METHOD_TABLE")." WHERE parameter_field_id = ".$parameter_field_id_tmp." AND parameter_method_id=".$parameter_method_id_tmp."";
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
	 * @param integer $parameter_field_id
	 * @return array
	 */
	public static function list_methods_by_field_id($parameter_field_id)
	{
		global $db;
		
		if (is_numeric($parameter_field_id))
		{
			$return_array = array();
			
			$sql = "SELECT parameter_method_id FROM ".constant("PARAMETER_FIELD_HAS_METHOD_TABLE")." WHERE parameter_field_id = ".$parameter_field_id."";
			$res = $db->db_query($sql);
			
			while($data = $db->fetch($res))
			{
				array_push($return_array, $data['parameter_method_id']);
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
	 * @param integer $parameter_method_id
	 * @return bool
	 */
	public static function delete_by_method_id($parameter_method_id)
	{
		global $db;

		if (is_numeric($parameter_method_id))
		{
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_HAS_METHOD_TABLE")." WHERE parameter_method_id=".$parameter_method_id."";
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
	 * @param integer $parameter_field_id
	 * @return bool
	 */
	public static function delete_by_field_id($parameter_field_id)
	{
		global $db;

		if (is_numeric($parameter_field_id))
		{
			$sql = "DELETE FROM ".constant("PARAMETER_FIELD_HAS_METHOD_TABLE")." WHERE parameter_field_id = ".$parameter_field_id."";
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