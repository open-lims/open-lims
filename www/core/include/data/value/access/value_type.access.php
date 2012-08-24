<?php
/**
 * @package data
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
 * Value Type Access Class
 * @package data
 */
class ValueType_Access
{
	private $value_type_id;
	
	private $name;
	private $template_id;
	
	/**
	 * @param integer $value_type_id
	 */
	function __construct($value_type_id)
	{
		global $db;
		
		if ($value_type_id == null)
		{
			$this->value_type_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("VALUE_TYPE_TABLE")." WHERE id='".$value_type_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->value_type_id	= $value_type_id;
				$this->name				= $data[name];
				$this->template_id		= $data[template_id];
			}
			else
			{
				$this->value_type_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->value_type_id)
		{
			unset($this->value_type_id);
			unset($this->name);
			unset($this->template_id);
		}
	}
	
	/**
	 * @param string $name
	 * @param integer $template_id
	 * @return bool
	 */
	public function create($id, $name, $template_id)
	{
		global $db;
		
		if (is_numeric($id) and $name and is_numeric($template_id))
		{
			$sql_write = "INSERT INTO ".constant("VALUE_TYPE_TABLE")." (id,name,template_id) " .
					"VALUES (".$id.",'".$name."',".$template_id.")";			
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
			{				
				return true;
			}
			else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function delete()
	{
		global $db;

		if ($this->value_type_id)
		{
			$value_type_id_tmp = $this->value_type_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("VALUE_TYPE_TABLE")." WHERE id = ".$value_type_id_tmp."";
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
	 * @return integer
	 */
	public function get_template_id()
	{
		if ($this->template_id)
		{
			return $this->template_id;
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

		if ($this->value_type_id and $name)
		{
			$sql = "UPDATE ".constant("VALUE_TYPE_TABLE")." SET name = '".$name."' WHERE id = ".$this->value_type_id."";
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
	 * @param integer $template_id
	 * @return bool
	 */
	public function set_template_id($template_id)
	{
		global $db;

		if ($this->value_type_id and is_numeric($template_id))
		{
			$sql = "UPDATE ".constant("VALUE_TYPE_TABLE")." SET template_id = ".$template_id." WHERE id = ".$this->value_type_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->template_id = $template_id;
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
	public static function exist_id($id)
	{
		global $db;
	
		if (is_numeric($id))
		{	
			$sql = "SELECT id FROM ".constant("VALUE_TYPE_TABLE")." WHERE id = '".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
		
			if ($data[id])
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	/**
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("VALUE_TYPE_TABLE")."";
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
	
}

?>
