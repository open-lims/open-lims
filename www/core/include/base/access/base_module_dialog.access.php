<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Base Module Dialog Access Class
 * @package base
 */
class BaseModuleDialog_Access
{
	const BASE_MODULE_DIALOG_PK_SEQUENCE = 'core_base_module_dialogs_id_seq';
	
	private $id;
	private $module_id;
	private $dialog_type;
	private $class_path;
	private $class;
	private $method;
	private $internal_name;
	private $display_name;
	
	/**
	 * @param integer $id
	 */
	function __construct($id)
	{
		global $db;
		
		if ($id == null)
		{
			$this->id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->id 				= $id;
				$this->module_id		= $data[module_id];
				$this->dialog_type		= $data[dialog_type];
				$this->class_path		= $data[class_path];
				$this->class			= $data['class'];
				$this->method			= $data[method];
				$this->internal_name	= $data[internal_name];
				$this->display_name		= $data[display_name];
			}
			else
			{
				$this->id			= null;
			}				
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->module_id);
			unset($this->dialog_type);
			unset($this->class);
			unset($this->class_path);
			unset($this->method);
			unset($this->internal_name);
			unset($this->display_name);
		}
	}
	
	/**
	 * @param integer $module_id
	 * @param string $dialog_type
	 * @param string $name
	 * @param string $class
	 * @param string $method
	 * @return integer
	 */
	public function create($module_id, $dialog_type, $class_path, $class, $method, $internal_name, $display_name)
	{
		global $db;

		if (is_numeric($module_id) and $dialog_type and $class and $method and $internal_name and $display_name)
		{
	 		$sql_write = "INSERT INTO ".constant("BASE_MODULE_DIALOG_TABLE")." (id, module_id, dialog_type, class_path, class, method, internal_name, display_name) " .
								"VALUES (nextval('".self::BASE_MODULE_DIALOG_PK_SEQUENCE."'::regclass),'".$module_id."','".$dialog_type."','".$class_path."','".$class."','".$method."','".$internal_name."','".$display_name."')";		
				
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE id = currval('".self::BASE_MODULE_DIALOG_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
							
				$this->__construct($data_read[id]);		
								
				return $data_read[id];
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

		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE id = '".$id_tmp."'";
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
	public function get_module_id()
	{
		if ($this->module_id)
		{
			return $this->module_id;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_dialog_type()
	{
		if ($this->dialog_type)
		{
			return $this->dialog_type;
		}
		else
		{
			return null;
		}	
	}

	/**
	 * @return string
	 */
	public function get_class_path()
	{
		if ($this->class_path)
		{
			return $this->class_path;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_class()
	{
		if ($this->class)
		{
			return $this->class;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_method()
	{
		if ($this->method)
		{
			return $this->method;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_interal_name()
	{
		if ($this->internal_name)
		{
			return $this->internal_name;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_display_name()
	{
		if ($this->display_name)
		{
			return $this->display_name;
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
	public function set_module_id($module_id)
	{
		global $db;

		if ($this->id and is_numeric($module_id))
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET module_id = '".$module_id."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->module_id = $module_id;
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
	 * @param string $dialog_type
	 * @return bool
	 */
	public function set_dialog_type($dialog_type)
	{
		global $db;

		if ($this->id and $dialog_type)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET dialog_type = '".$dialog_type."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->dialog_type = $dialog_type;
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
	 * @param string $class_path
	 * @return bool
	 */
	public function set_class_path($class_path)
	{
		global $db;

		if ($this->id and $class_path)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET class_path = '".$class_path."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->class_path = $class_path;
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
	 * @param string $class
	 * @return bool
	 */
	public function set_class($class)
	{
		global $db;

		if ($this->id and $class)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET class = '".$class."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->class = $class;
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
	 * @param string $method
	 * @return bool
	 */
	public function set_method($method)
	{
		global $db;

		if ($this->id and $method)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET method = '".$method."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->method = $method;
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
	 * @param string $internal_name
	 * @return bool
	 */
	public function set_internal_name($internal_name)
	{
		global $db;

		if ($this->id and $internal_name)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET internal_name = '".$internal_name."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->internal_name = $internal_name;
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
	 * @param string $display_name
	 * @return bool
	 */
	public function set_display_name($display_name)
	{
		global $db;

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET display_name = '".$display_name."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->display_name = $display_name;
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
	 * @param string $dialog_type
	 * @param string $internal_name
	 * @return array
	 */
	public static function get_by_type_and_internal_name($dialog_type, $internal_name)
	{
		global $db;

		if ($dialog_type and $internal_name)
		{
			$result_array = array();
			
			$sql = "SELECT * FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE TRIM(dialog_type) = '".trim($dialog_type)."' AND internal_name = '".$internal_name."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);

			$result_array[class_path] 	= $data[class_path];
			$result_array['class'] 		= $data['class'];
			$result_array[method] 		= $data[method];

			if (is_array($result_array))
			{
				return $result_array;
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
	 * @param string $dialog_type
	 * @return array
	 */
	public static function list_dialogs_by_type($dialog_type)
	{
		global $db;

		if ($dialog_type)
		{
			$result_array = array();
			$counter = 0;
			
			$sql = "SELECT * FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE TRIM(dialog_type) = '".trim($dialog_type)."'";
			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				$result_array[$counter]['class'] 		= $data['class'];
				$result_array[$counter][method] 		= $data[method];
				$result_array[$counter][internal_name] 	= $data[internal_name];
				$result_array[$counter][display_name] 	= $data[display_name];
				$counter++;
			}
			
			if (is_array($result_array))
			{
				return $result_array;
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
	 * @param integer $module_id
	 * @return bool
	 */
	public static function delete_by_module_id($module_id)
	{
		global $db;

		if (is_numeric($module_id))
		{
			$sql = "DELETE FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE module_id = '".$module_id."'";
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