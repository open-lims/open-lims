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
 * Folder Access Class
 * @package data
 */
class Folder_Access
{
	const FOLDER_PK_SEQUENCE = 'core_folders_id_seq';
	
	private $folder_id;
	private $data_entity_id;
	private $name;
	private $path;
	private $deleted;
	private $blob;
	private $flag;
	
	/**
	 * @param integer $folder_id
	 */
	function __construct($folder_id)
	{
		global $db;
		
		if ($folder_id == null)
		{
			$this->folder_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("FOLDER_TABLE")." WHERE id='".$folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->folder_id 			= $folder_id;
				
				$this->data_entity_id		= $data['data_entity_id'];
				$this->name					= $data['name'];
				$this->path					= $data['path'];
				$this->flag					= $data['flag'];
									
				if ($data['deleted'] == "t")
				{
					$this->deleted			= true;
				}
				else
				{
					$this->deleted			= false;
				}
				
				if ($data['blob'] == "t")
				{
					$this->blob				= true;
				}
				else
				{
					$this->blob				= false;
				}
			}
			else
			{
				$this->folder_id			= null;
			}
		}
	} 

	function __destruct()
	{
		if ($this->folder_id)
		{
			unset($this->folder_id);
			unset($this->data_entity_id);
			unset($this->name);
			unset($this->path);
			unset($this->deleted);
			unset($this->blob);
			unset($this->flag);
		}
	}

	/**
	 * @param integer $data_entity_id
	 * @param string $name
	 * @param string $path
	 * @return integer
	 */
	public function create($data_entity_id, $name, $path)
	{
		global $db, $user;
		
		if (is_numeric($data_entity_id) and $path and $name)
		{
			$datetime = date("Y-m-d H:i:s");

			$sql_write = "INSERT INTO ".constant("FOLDER_TABLE")." (id, data_entity_id, name, path, deleted, blob, flag) " .
								"VALUES (nextval('".self::FOLDER_PK_SEQUENCE."'::regclass), ".$data_entity_id.",'".$name."','".$path."','f','f','0')";		
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT id FROM ".constant("FOLDER_TABLE")." WHERE id = currval('".self::FOLDER_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read['id']);
				
				return $data_read['id'];
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

		if ($this->folder_id and $this->path)
		{	
			$folder_id_tmp = $this->folder_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("FOLDER_TABLE")." WHERE id = ".$folder_id_tmp."";
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
	public function get_id()
	{
		if ($this->folder_id)
		{
			return $this->folder_id;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return integer
	 */
	public function get_data_entity_id()
	{
		if ($this->data_entity_id)
		{
			return $this->data_entity_id;
		}
		else
		{
			return null;
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
			return "/";
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_path()
	{
		if ($this->path)
		{
			return $this->path;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return bool
	 */
	public function get_deleted()
	{
		if (isset($this->deleted))
		{
			return $this->deleted;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return bool
	 */
	public function get_blob()
	{
		if (isset($this->blob))
		{
			return $this->blob;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_flag()
	{
		if ($this->flag)
		{
			return $this->flag;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public function set_data_entity_id($data_entity_id)
	{
		global $db;
			
		if ($this->folder_id and is_numeric($data_entity_id))
		{
			$sql = "UPDATE ".constant("FOLDER_TABLE")." SET data_entity_id = ".$data_entity_id." WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->data_entity_id = $data_entity_id;
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
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;
			
		if ($this->folder_id and $name)
		{
			$sql = "UPDATE ".constant("FOLDER_TABLE")." SET name = '".$name."' WHERE id = ".$this->folder_id."";
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
	 * @param string $path
	 * @return bool
	 */
	public function set_path($path)
	{
		global $db;
			
		if ($this->folder_id and $path)
		{
			$sql = "UPDATE ".constant("FOLDER_TABLE")." SET path = '".$path."' WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->path = $path;
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
	 * @param bool $deleted
	 * @return bool
	 */
	public function set_deleted($deleted)
	{
		global $db;

		if ($this->folder_id and isset($deleted))
		{
			if ($deleted == true)
			{
				$deleted_insert = "t";
			}
			else
			{
				$deleted_insert = "f";
			}
			
			$sql = "UPDATE ".constant("FOLDER_TABLE")." SET deleted = '".$deleted_insert."' WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->deleted = $deleted;
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
	 * @param bool $blob
	 * @return bool
	 */
	public function set_blob($blob)
	{
		global $db;
			
		if ($this->folder_id and isset($blob))
		{
			if ($blob == true)
			{
				$blob_insert = "t";
			}
			else
			{
				$blob_insert = "f";
			}
			
			$sql = "UPDATE ".constant("FOLDER_TABLE")." SET blob = '".$blob_insert."' WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->blob = $blob;
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
	 * @param integer $flag
	 * @return bool
	 */
	public function set_flag($flag)
	{
		global $db;

		if ($this->folder_id and is_numeric($flag))
		{
			$sql = "UPDATE ".constant("FOLDER_TABLE")." SET flag = ".$flag." WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->flag = $flag;
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
 	 * @param integer $folder_id
	 * @return bool
	 */
	public static function exist_id($folder_id)
	{
		global $db;
			
		if (is_numeric($folder_id))
		{
			$sql = "SELECT id FROM ".constant("FOLDER_TABLE")." WHERE id = ".$folder_id."";
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
	
	/**
	 * @param string $path
	 * @return integer
	 */
	public static function get_entry_by_path($path)
	{
		global $db;

		if ($path)
		{
			$sql = "SELECT id FROM ".constant("FOLDER_TABLE")." WHERE TRIM(LOWER(path)) = '".trim(strtolower($path))."'";

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
	 * @param string $data_entity_id
	 * @return integer
	 */
	public static function get_entry_by_data_entity_id($data_entity_id)
	{
		global $db;

		if (is_numeric($data_entity_id))
		{
			$sql = "SELECT id FROM ".constant("FOLDER_TABLE")." WHERE data_entity_id = '".$data_entity_id."'";

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
	 * @param string $data_entity_id
	 * @return integer
	 */
	public static function get_data_entity_id_by_folder_id($folder_id)
	{
		global $db;

		if (is_numeric($folder_id))
		{
			$sql = "SELECT data_entity_id FROM ".constant("FOLDER_TABLE")." WHERE id = '".$folder_id."'";

			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['data_entity_id'])
			{
				return $data['data_entity_id'];
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
	 * @param integer $folder_id
	 * @return string
	 */
	public static function get_name_by_id($folder_id)
	{
		global $db;

		if (is_numeric($folder_id))
		{
			$sql = "SELECT name FROM ".constant("FOLDER_TABLE")." WHERE id = '".$folder_id."'";

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
}
?>