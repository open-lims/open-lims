<?php
/**
 * @package base
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
 * Base Module Navigation Access Class
 * @package base
 */
class BaseModuleNavigation_Access
{
	const BASE_MODULE_NAVIGATION_PK_SEQUENCE = 'core_base_module_navigation_id_seq';
	
	private $id;
	private $language_address;
	private $position;
	private $colour;
	private $module_id;
	private $hidden;
	
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
			$sql = "SELECT * FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE id= :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id 				= $id;
				$this->language_address	= $data['language_address'];
				$this->position			= $data['position'];
				$this->colour			= $data['colour'];
				$this->module_id		= $data['module_id'];
				
				if ($data['hidden'] == 't')
				{
					$this->hidden	= true;
				}
				else
				{
					$this->hidden	= false;
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
			unset($this->language_address);
			unset($this->position);
			unset($this->colour);
			unset($this->module_id);
		}
	}
	
	/**
	 * @param string $language_address
	 * @param string $colour
	 * @param integer $position
	 * @param integer $module_id
	 * @return integer
	 */
	public function create($language_address, $colour, $position, $module_id, $controller_class, $controller_file, $alias)
	{
		global $db;

		if ($language_address and $colour and $controller_class and $controller_file and is_numeric($position) and is_numeric($module_id))
		{
			$sql_write = "INSERT INTO ".constant("BASE_MODULE_NAVIGATION_TABLE")." (id, language_address, position, colour, module_id, hidden, alias, controller_class, controller_file) " .
					"VALUES (nextval('".self::BASE_MODULE_NAVIGATION_PK_SEQUENCE."'::regclass), :language_address, :position, :colour, :module_id, 'f', :alias, :controller_class, :controller_file)";

			$res_write = $db->prepare($sql_write);
			
			if ($alias)
			{
				$db->bind_value($res_write, ":alias", $alias, PDO::PARAM_STR);
			}
			else
			{
				$db->bind_value($res_write, ":alias", null, PDO::PARAM_NULL);
			}
			
			$db->bind_value($res_write, ":language_address", $language_address, PDO::PARAM_STR);
			$db->bind_value($res_write, ":position", $position, PDO::PARAM_INT);
			$db->bind_value($res_write, ":colour", $colour, PDO::PARAM_STR);
			$db->bind_value($res_write, ":module_id", $module_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":controller_class", $controller_class, PDO::PARAM_STR);
			$db->bind_value($res_write, ":controller_file", $controller_file, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE id = currval('".self::BASE_MODULE_NAVIGATION_PK_SEQUENCE."'::regclass)";
				$res_read = $db->prepare($sql);
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

			$sql = "DELETE FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE id = :id";
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
	public function get_position()
	{
		if ($this->position)
		{
			return $this->position;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_colour()
	{
		if ($this->colour)
		{
			return $this->colour;
		}
		else
		{
			return null;
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
	 * @return boolean
	 */
	public function get_hidden()
	{
		if (isset($this->hidden))
		{
			return $this->hidden;
		}
		else
		{
			return false;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_alias()
	{
		if ($this->alias)
		{
			return $this->alias;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_controller_class()
	{
		if ($this->controller_class)
		{
			return $this->controller_class;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_controller_file()
	{
		if ($this->controller_file)
		{
			return $this->controller_file;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @param string $language_address
	 * @return bool
	 */
	public function set_language_address($language_address)
	{
		global $db;

		if ($this->id and $language_address)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET language_address = :language_address WHERE id = :id";
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
	 * @param integer $position
	 * @return bool
	 */
	public function set_position($position)
	{
		global $db;

		if ($this->id and is_numeric($position) or $position == null)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET position = :position WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if ($position == null)
			{
				$db->bind_value($res, ":position", null, PDO::PARAM_NULL);
			}
			else
			{
				$db->bind_value($res, ":position", $position, PDO::PARAM_INT);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->position = $position;
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
	 * @param string $colour
	 * @return bool
	 */
	public function set_colour($colour)
	{
		global $db;

		if ($this->id and $colour)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET colour = :colour WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":colour", $colour, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->colour = $colour;
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
	public function set_module_id($module_id)
	{
		global $db;

		if ($this->id and is_numeric($module_id))
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET module_id = :module_id WHERE id = :id";
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
	 * @param bool $hidden
	 * @return bool
	 */
	public function set_hidden($hidden)
	{
		global $db;

		if ($this->id and isset($hidden))
		{			
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET hidden = :hidden WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":hidden", $hidden, PDO::PARAM_BOOL);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->hidden = $hidden;
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
	 * @param string $alias
	 * @return bool
	 */
	public function set_alias($alias)
	{
		global $db;

		if ($this->id and $alias)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET alias = :alias WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":alias", $alias, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->alias = $alias;
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
	 * @param string $controller_class
	 * @return bool
	 */
	public function set_controller_class($controller_class)
	{
		global $db;

		if ($this->id and $controller_class)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET controller_class = :controller_class WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":controller_class", $controller_class, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->controller_class = $controller_class;
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
	 * @param string $controller_file
	 * @return bool
	 */
	public function set_controller_file($controller_file)
	{
		global $db;

		if ($this->id and $controller_file)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET controller_file = :controller_file WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":controller_file", $controller_file, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->controller_file= $controller_file;
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
	public function get_next_position()
	{
		global $db;
		
		if ($this->id and $this->position)
		{			
			$next_position = $this->position + 1;
			
			$sql = "SELECT id FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." " .
				"WHERE position = :position";
			
			$res = $db->prepare($sql);
			$db->bind_value($res, ":position", $next_position, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				return $data['id'];
			}
			else
			{
				return $this->id;
			}
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 * @return integer
	 */
	public static function get_highest_position()
	{
		global $db;
		
		$sql = "SELECT MAX(position) AS position FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")."";
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);
		
		if ($data['position'])
		{
			return $data['position'];
		}
		else
		{
			return 1;
		}
	}
	
	
	public static function list_entries_by_module_id($module_id)
	{	
		global $db;
		
		if (is_numeric($module_id))
		{
			$result_array = array();
			
			$sql = "SELECT id,language_address,controller_class,controller_file,alias FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE module_id = :module_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":module_id", $module_id, PDO::PARAM_INT);
			$db->execute($res);
			while ($data = $db->fetch($res))
			{
				$result_array[$data['language_address']]['id']					= $data['id'];
				$result_array[$data['language_address']]['colour']				= $data['colour'];
				$result_array[$data['language_address']]['controller_class']	= $data['controller_class'];
				$result_array[$data['language_address']]['controller_file']		= $data['controller_file'];
				$result_array[$data['language_address']]['alias']				= $data['alias'];
			}
			
			return $result_array;
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
		
		$sql = "SELECT id,language_address,alias,colour,module_id FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE hidden = 'f' ORDER BY position";
		$res = $db->prepare($sql);
		$db->execute($res);
		while ($data = $db->fetch($res))
		{
			$result_array[$data['id']]['language_address']	= $data['language_address'];
			$result_array[$data['id']]['alias']				= $data['alias'];
			$result_array[$data['id']]['colour']			= $data['colour'];
			$result_array[$data['id']]['module_id']			= $data['module_id'];
		}
		
		return $result_array;
	}
	
	public static function get_module_controller($module_id, $alias)
	{
		global $db;
		
		if (is_numeric($module_id))
		{
			if ($alias)
			{
				$alias_sql = "alias = :alias";
			}
			else
			{
				$alias_sql = "alias IS NULL";
			}
			
			$sql = "SELECT controller_file, controller_class FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE module_id = :module_id AND ".$alias_sql."";
			$res = $db->prepare($sql);
			
			if ($alias)
			{
				$db->bind_value($res, ":alias", $alias, PDO::PARAM_STR);
			}
		
			$db->bind_value($res, ":module_id", $module_id, PDO::PARAM_INT);
			
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['controller_file'] and $data['controller_class'])
			{
				return array("controller_file" => $data['controller_file'], "controller_class" => $data['controller_class']);
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
	public static function get_id_by_module_id($module_id)
	{
		global $db;
		
		if (is_numeric($module_id))
		{
			$sql = "SELECT id FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE module_id = :module_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":module_id", $module_id, PDO::PARAM_INT);
			$db->execute($res);
		
			$data = $db->fetch($res);
			
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
	 * @param integer $module_id
	 * @return integer
	 */
	public static function get_id_by_position($position)
	{
		global $db;
		
		if (is_numeric($position))
		{
			$sql = "SELECT id FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE position = :position";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":position", $position, PDO::PARAM_STR);
			$db->execute($res);
			
			$data = $db->fetch($res);
			
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
	 * @return array
	 */
	public static function list_ids()
	{
		global $db;
		
		$result_array = array();
		
		$sql = "SELECT id FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." ORDER BY position";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		while ($data = $db->fetch($res))
		{
			array_push($result_array, $data['id']);
		}
		
		return $result_array;
	}
		
	public static function count_entries()
	{
		global $db;
		
		$sql = "SELECT COUNT(id) AS result FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")."";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		$data = $db->fetch($res);
		
		return $data['result'];
	}
	
	/**
	 * @return bool
	 */
	public static function check_position()
	{
		global $db;
		
		$result_array = array();
		
		$sql = "SELECT MAX(position) AS maximum FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")."";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		$data = $db->fetch($res);
		
		if ($data['maximum'] == self::count_entries())
		{
			return true;
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
	public static function delete_by_module_id($module_id)
	{
		global $db;

		if (is_numeric($module_id))
		{
			$sql = "DELETE FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE module_id = :module_id";
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