<?php
/**
 * @package base
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
	private $language_address;
	private $weight;
	private $disabled;
	
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
			$sql = "SELECT * FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE id=:id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id 				= $id;
				$this->module_id		= $data['module_id'];
				$this->dialog_type		= $data['dialog_type'];
				$this->class_path		= $data['class_path'];
				$this->class			= $data['class'];
				$this->method			= $data['method'];
				$this->internal_name	= $data['internal_name'];
				$this->language_address	= $data['language_address'];
				$this->weight			= $data['weight'];
				$this->disabled			= $data['disabled'];
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
			unset($this->language_address);
			unset($this->weight);
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
	public function create($module_id, $dialog_type, $class_path, $class, $method, $internal_name, $language_address, $weight)
	{
		global $db;

		if (is_numeric($module_id) and $dialog_type and $class and $method and $internal_name)
		{
			$sql_write = "INSERT INTO ".constant("BASE_MODULE_DIALOG_TABLE")." (id, module_id, dialog_type, class_path, class, method, internal_name, language_address, weight, disabled) " .
								"VALUES (nextval('".self::BASE_MODULE_DIALOG_PK_SEQUENCE."'::regclass), :module_id, :dialog_type, :class_path, :class, :method, :internal_name, :language_address, :weight,'f')";		
				
			$res_write = $db->prepare($sql_write);
			
			if (is_numeric($weight))
			{
				$db->bind_value($res_write, ":weight", $weight, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res_write, ":weight", null, PDO::PARAM_NULL);
			}
			
			$db->bind_value($res_write, ":module_id", $module_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":dialog_type", $dialog_type, PDO::PARAM_STR);
			$db->bind_value($res_write, ":class_path", $class_path, PDO::PARAM_STR);
			$db->bind_value($res_write, ":class", $class, PDO::PARAM_STR);
			$db->bind_value($res_write, ":method", $method, PDO::PARAM_STR);
			$db->bind_value($res_write, ":internal_name", $internal_name, PDO::PARAM_STR);
			$db->bind_value($res_write, ":language_address", $language_address, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE id = currval('".self::BASE_MODULE_DIALOG_PK_SEQUENCE."'::regclass)";
				$res_read = $db->prepare($sql_read);
				$db->execute($res_read);
				$data_read = $db->fetch($res_read);
							
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

		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id_tmp, PDO::PARAM_INT);
			$db->execute($res);
			
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
	public function get_language_address()
	{
		if ($this->language_address)
		{
			return $this->language_address;
		}
		else
		{
			return null;
		}	
	}
		
	/**
	 * @return integer
	 */
	public function get_weight()
	{
		if ($this->weight)
		{
			return $this->weight;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return bool
	 */
	public function get_disabled()
	{
		if (isset($this->disabled))
		{
			return $this->disabled;
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
	public function set_module_id($module_id)
	{
		global $db;

		if ($this->id and is_numeric($module_id))
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET module_id = :module_id WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":module_id", $module_id, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
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
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET dialog_type = :dialog_type WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":dialog_type", $dialog_type, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET class_path = :class_path WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":class_path", $class_path, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET class = :class WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":class", $class, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET method = :method WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":method", $method, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET internal_name = :internal_name WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":internal_name", $internal_name, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
	 * @param string $language_address
	 * @return bool
	 */
	public function set_language_address($language_address)
	{
		global $db;

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET language_address = :language_address WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":language_address", $language_address, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->language_address = $language_address;
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
	 * @param integer $weight
	 * @return bool
	 */
	public function set_weight($weight)
	{
		global $db;

		if ($this->id and is_numeric($weight))
		{
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET weight = :weight WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":weight", $weight, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->weight = $weight;
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
	 * @param bool $disabled
	 * @return bool
	 */
	public function set_disabled($disabled)
	{
		global $db;

		if ($this->id and isset($disabled))
		{			
			$sql = "UPDATE ".constant("BASE_MODULE_DIALOG_TABLE")." SET disabled = :disabled WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":disabled", $disabled, PDO::PARAM_BOOL);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->disabled = $disabled;
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
			
			$sql = "SELECT * FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE TRIM(dialog_type) = TRIM(:dialog_type) AND internal_name = TRIM(:internal_name) AND disabled='f'";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":dialog_type", $dialog_type, PDO::PARAM_STR);
			$db->bind_value($res, ":internal_name", $internal_name, PDO::PARAM_STR);
			$db->execute($res);
			$data = $db->fetch($res);

			$result_array['class_path'] = $data['class_path'];
			$result_array['class'] 		= $data['class'];
			$result_array['method'] 	= $data['method'];

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
			
			$sql = "SELECT * FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE TRIM(dialog_type) = TRIM(:dialog_type) AND disabled='f' ORDER BY weight";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":dialog_type", $dialog_type, PDO::PARAM_STR);
			$db->execute($res);
			
			while ($data = $db->fetch($res))
			{
				$result_array[$counter]['class_path'] 		= $data['class_path'];
				$result_array[$counter]['class'] 			= $data['class'];
				$result_array[$counter]['method'] 			= $data['method'];
				$result_array[$counter]['internal_name'] 	= $data['internal_name'];
				$result_array[$counter]['language_address'] = $data['language_address'];
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
	 * @param string $dialog_type
	 * @return array
	 */
	public static function list_dialogs_by_type_and_module_id($dialog_type, $module_id)
	{
		global $db;

		if ($dialog_type and is_numeric($module_id))
		{
			$result_array = array();
			$counter = 0;
			
			$sql = "SELECT * FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE TRIM(dialog_type) = TRIM(:dialog_type) AND module_id = :module_id AND disabled='f' ORDER BY weight";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":dialog_type", $dialog_type, PDO::PARAM_STR);
			$db->bind_value($res, ":module_id", $module_id, PDO::PARAM_INT);
			$db->execute($res);
			
			while ($data = $db->fetch($res))
			{
				$result_array[$counter]['class_path'] 		= $data['class_path'];
				$result_array[$counter]['class'] 			= $data['class'];
				$result_array[$counter]['method'] 			= $data['method'];
				$result_array[$counter]['internal_name'] 	= $data['internal_name'];
				$result_array[$counter]['language_address'] = $data['language_address'];
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
	 * @return integer
	 */
	public static function list_id_by_module_id($module_id)
	{
		global $db;
		
		if (is_numeric($module_id))
		{
			$return_array = array();
					
			$sql = "SELECT id FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE module_id = :module_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":module_id", $module_id, PDO::PARAM_INT);
			$db->execute($res);
			
			while ($data = $db->fetch($res))
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
	 * @param integer $module_id
	 * @return bool
	 */
	public static function delete_by_module_id($module_id)
	{
		global $db;

		if (is_numeric($module_id))
		{
			$sql = "DELETE FROM ".constant("BASE_MODULE_DIALOG_TABLE")." WHERE module_id = :module_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":module_id", $module_id, PDO::PARAM_INT);
			$db->execute($res);
			
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