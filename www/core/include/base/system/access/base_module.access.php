<?php
/**
 * @package base
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
 * Base Module Access Class
 * @package base
 */
class BaseModule_Access
{
	const BASE_MODULE_PK_SEQUENCE = 'core_base_modules_id_seq';
	
	private $id;
	private $name;
	private $folder;
	private $class;
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
			$sql = "SELECT * FROM ".constant("BASE_MODULE_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->id 			= $id;
				$this->name			= $data['name'];
				$this->folder		= $data['folder'];
				$this->class		= $data['class'];
				
				if ($data['disabled'] == 't')
				{
					$this->disabled = true;
				}
				else
				{
					$this->disabled = false;
				}
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
			unset($this->name);
			unset($this->folder);
			unset($this->class);
		}
	}
	
	/**
	 * @param string $name
	 * @param string $folder
	 * @param string $class
	 * @return integer
	 */
	public function create($name, $folder, $class)
	{
		global $db;

		if ($name and $folder)
		{
			if (!$class)
			{
				$class_insert = "NULL";
			}
			else
			{
				$class_insert = "'".$class."'";
			}
			
	 		$sql_write = "INSERT INTO ".constant("BASE_MODULE_TABLE")." (id, name, folder, class, disabled) " .
								"VALUES (nextval('".self::BASE_MODULE_PK_SEQUENCE."'::regclass),'".$name."','".$folder."',".$class_insert.",'f')";		
				
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_MODULE_TABLE")." WHERE id = currval('".self::BASE_MODULE_PK_SEQUENCE."'::regclass)";
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

		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("BASE_MODULE_TABLE")." WHERE id = '".$id_tmp."'";
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
	 * @return string
	 */
	public function get_folder()
	{
		if ($this->folder)
		{
			return $this->folder;
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
	public function set_name($name)
	{
		global $db;

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_TABLE")." SET name = '".$name."' WHERE id = ".$this->id."";
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
	 * @param string $folder
	 * @return bool
	 */
	public function set_folder($folder)
	{
		global $db;

		if ($this->id and $folder)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_TABLE")." SET folder = '".$folder."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->folder = $folder;
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
			$sql = "UPDATE ".constant("BASE_MODULE_TABLE")." SET class = '".$class."' WHERE id = ".$this->id."";
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
	 * @param bool $disabled
	 * @return bool
	 */
	public function set_disabled($disabled)
	{
		global $db;

		if ($this->id and isset($disabled))
		{
			if ($disabled == true)
			{
				$disabled_insert = "t";
			}
			else
			{
				$disabled_insert = "f";
			}
			
			$sql = "UPDATE ".constant("BASE_MODULE_TABLE")." SET disabled = '".$disabled_insert."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
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
	 * @param integer $module_id
	 * @return bool
	 */
	public static function get_module_name_by_module_id($module_id)
	{
		global $db;
		
		if (is_numeric($module_id))
		{		
			$sql = "SELECT name FROM ".constant("BASE_MODULE_TABLE")." WHERE id = ".$module_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['name'])
			{
				return $data['name'];
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
	 * @param string $name
	 * @return bool
	 */
	public static function get_module_id_by_module_name($name)
	{
		global $db;
		
		if ($name)
		{		
			$name = trim(strtolower($name));
			
			$sql = "SELECT id FROM ".constant("BASE_MODULE_TABLE")." WHERE TRIM(LOWER(name)) = '".$name."'";
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
	 * @param param $module_name
	 * @return bool
	 */
	public static function get_module_folder_by_module_name($module_name)
	{
		global $db;
		
		if ($module_name)
		{		
			$sql = "SELECT folder FROM ".constant("BASE_MODULE_TABLE")." WHERE TRIM(name) = '".trim($module_name)."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['folder'])
			{
				return $data['folder'];
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
	 * @return array
	 */
	public static function list_folder_entries()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT id, folder FROM ".constant("BASE_MODULE_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			$return_array[$data['id']] = $data['folder'];
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
		
		$result_array = array();
		
		$sql = "SELECT id,name,folder,class FROM ".constant("BASE_MODULE_TABLE")." ORDER BY name";
		$res = $db->db_query($sql);
		while ($data = $db->db_fetch_assoc($res))
		{
			$result_array[$data['id']]['name']		= $data['name'];
			$result_array[$data['id']]['folder']	= $data['folder'];
			$result_array[$data['id']]['class']		= $data['class'];
		}
		
		return $result_array;
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public static function exist_entry($name)
	{
		global $db;
		
		if ($name)
		{		
			$name = trim(strtolower($name));
			
			$sql = "SELECT id FROM ".constant("BASE_MODULE_TABLE")." WHERE TRIM(LOWER(name)) = '".$name."'";
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