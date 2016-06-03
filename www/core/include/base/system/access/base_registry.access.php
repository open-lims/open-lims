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
 * Base Registry Access Class
 * @package base
 */
class BaseRegistry_Access
{
	const BASE_REGISTRY_PK_SEQUENCE = 'core_base_registry_id_seq';
	
	private $id;
	private $name;
	private $include_id;
	private $value;
	
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
			$sql = "SELECT * FROM ".constant("BASE_REGISTRY_TABLE")." WHERE id=:id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id 			= $id;
				$this->name			= $data['name'];
				$this->include_id	= $data['include_id'];
				$this->value		= $data['value'];
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
			unset($this->include_id);
			unset($this->value);
		}
	}
	
	/**
	 * @param string $name
	 * @param integer $include_id
	 * @param string $value
	 * @return integer
	 */
	public function create($name, $include_id, $value)
	{
		global $db;

		if ($name and is_numeric($include_id) and $value)
		{
	 		$sql_write = "INSERT INTO ".constant("BASE_REGISTRY_TABLE")." (id, name, include_id, value) " .
								"VALUES (nextval('".self::BASE_REGISTRY_PK_SEQUENCE."'::regclass), :name, :include_id, :value)";		
				
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":name", $name, PDO::PARAM_STR);
			$db->bind_value($res_write, ":include_id", $include_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":value", $value, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_REGISTRY_TABLE")." WHERE id = currval('".self::BASE_REGISTRY_PK_SEQUENCE."'::regclass)";
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

			$sql = "DELETE FROM ".constant("BASE_REGISTRY_TABLE")." WHERE id = :id";
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
	public function get_include_id()
	{
		if ($this->include_id)
		{
			return $this->include_id;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_value()
	{
		if ($this->value)
		{
			return $this->value;
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

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("BASE_REGISTRY_TABLE")." SET name = :name WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":name", $name, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
	 * @param integer $include_id
	 * @return bool
	 */
	public function set_include_id($include_id)
	{
		global $db;

		if ($this->id and is_numeric($include_id))
		{
			$sql = "UPDATE ".constant("BASE_REGISTRY_TABLE")." SET include_id = :include_id WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":include_id", $include_id, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->include_id = $include_id;
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
	 * @param string $value
	 * @return bool
	 */
	public function set_value($value)
	{
		global $db;

		if ($this->id and $value)
		{
			$sql = "UPDATE ".constant("BASE_REGISTRY_TABLE")." SET value = :value WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":value", $value, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->value = $value;
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
	 * @return integer
	 */
	public static function get_id_by_name($name)
	{
		global $db;

		if ($name)
		{
			$sql = "SELECT id FROM ".constant("BASE_REGISTRY_TABLE")." WHERE TRIM(LOWER(name)) = TRIM(LOWER(:name))";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":name", $name, PDO::PARAM_STR);
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
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_by_include_id($include_id)
	{
		global $db;

		if (is_numeric($include_id))
		{
			$sql = "DELETE FROM ".constant("BASE_REGISTRY_TABLE")." WHERE include_id = :include_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":include_id", $include_id, PDO::PARAM_INT);
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