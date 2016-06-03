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
 * Base Include Access Class
 * @package base
 */
class BaseInclude_Access
{
	const BASE_INCLUDE_PK_SEQUENCE = 'core_base_includes_id_seq';
	
	private $id;
	private $name;
	private $folder;
	private $db_version;
	
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
			$sql = "SELECT * FROM ".constant("BASE_INCLUDE_TABLE")." WHERE id=:id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id 			= $id;
				$this->name			= $data['name'];
				$this->folder		= $data['folder'];
				$this->db_version	= $data['db_version'];
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
			unset($this->db_version);
		}
	}
	
	/**
	 * @param string $name
	 * @param string $folder
	 * @return integer
	 */
	public function create($name, $folder)
	{
		global $db;

		if ($name and $folder)
		{
	 		$sql_write = "INSERT INTO ".constant("BASE_INCLUDE_TABLE")." (id, name, folder, db_version) " .
								"VALUES (nextval('".self::BASE_INCLUDE_PK_SEQUENCE."'::regclass), :name, :folder, NULL)";		
				
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":name", $name, PDO::PARAM_STR);
			$db->bind_value($res_write, ":folder", $folder, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_INCLUDE_TABLE")." WHERE id = currval('".self::BASE_INCLUDE_PK_SEQUENCE."'::regclass)";
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

			$sql = "DELETE FROM ".constant("BASE_INCLUDE_TABLE")." WHERE id = :id";
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
	public function get_db_version()
	{
		if ($this->db_version)
		{
			return $this->db_version;
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
			$sql = "UPDATE ".constant("BASE_INCLUDE_TABLE")." SET name = :name WHERE id = :id";
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
	 * @param string $folder;
	 * @return bool
	 */
	public function set_folder($folder)
	{
		global $db;

		if ($this->id and $folder)
		{
			$sql = "UPDATE ".constant("BASE_INCLUDE_TABLE")." SET folder = :folder WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":folder", $folder, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
	 * @param string $db_version;
	 * @return bool
	 */
	public function set_db_version($db_version)
	{
		global $db;

		if ($this->id and $db_version)
		{
			$sql = "UPDATE ".constant("BASE_INCLUDE_TABLE")." SET db_version = :db_version WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":db_version", $db_version, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->db_version = $db_version;
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
	public static function list_folder_entries()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT id, folder FROM ".constant("BASE_INCLUDE_TABLE")."";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		while ($data = $db->fetch($res))
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
		
		$sql = "SELECT id,name,folder FROM ".constant("BASE_INCLUDE_TABLE")." ORDER BY name";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		while ($data = $db->fetch($res))
		{
			$result_array[$data['id']]['name']		= $data['name'];
			$result_array[$data['id']]['folder']	= $data['folder'];
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
			
			$sql = "SELECT id FROM ".constant("BASE_INCLUDE_TABLE")." WHERE TRIM(LOWER(name)) = :name";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":name", $name, PDO::PARAM_STR);
			$db->execute($res);

			$data = $db->fetch($res);
			
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
	
	public static function check_installation()
	{
		global $db;
		
		$sql = "SELECT table_name FROM information_schema.tables";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		while($data = $db->fetch($res))
		{
			if ($data['table_name'] == "core_base_includes")
			{
				return true;
			}
		}
		
		return false;
	}
}

?>