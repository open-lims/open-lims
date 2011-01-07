<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
	const FOLDER_TABLE = 'core_folders';
	const FOLDER_PK_SEQUENCE = 'core_folders_id_seq';
	
	private $folder_id;
	
	private $toid;
	private $is_root;
	private $name;
	private $path;
	private $datetime;
	private $owner_id;
	private $owner_group_id;
	private $permission;
	private $automatic;
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
			$sql = "SELECT * FROM ".self::FOLDER_TABLE." WHERE id='".$folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->folder_id 			= $folder_id;
				
				$this->toid					= $data[toid];
				$this->name					= $data[name];
				$this->path					= $data[path];
				$this->datetime				= $data[datetime];
				$this->owner_id				= $data[owner_id];
				$this->owner_group_id		= $data[owner_group_id];
				$this->permission			= $data[permission];
				$this->flag					= $data[flag];
									
				if ($data[is_root] == "t")
				{
					$this->is_root			= true;
				}
				else{
					
					$this->is_root			= false;
				}
				
				if ($data[automatic] == "t")
				{
					$this->automatic		= true;
				}
				else
				{
					$this->automatic		= false;
				}

				if ($data[deleted] == "t")
				{
					$this->deleted			= true;
				}
				else
				{
					$this->deleted			= false;
				}
				
				if ($data[blob] == "t")
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
			unset($this->toid);
			unset($this->is_root);
			unset($this->name);
			unset($this->path);
			unset($this->datetime);
			unset($this->owner_id);
			unset($this->owner_group_id);
			unset($this->permission);
			unset($this->automatic);
			unset($this->deleted);
			unset($this->blob);
			unset($this->flag);
		
		}
	}

	/**
	 * @param string $name
	 * @param integer $toid
	 * @param bool $root
	 * @param string $path
	 * @param integer $owner_id
	 * @param integer $owner_group_id
	 * @return integer
	 */
	public function create($name, $toid, $root, $path, $owner_id, $owner_group_id)
	{
		global $db, $user;
		
		if (is_numeric($toid) and $path and isset($root))
		{
			$datetime = date("Y-m-d H:i:s");
			
			if ($name)
			{
				$name_insert = $name;
			}
			else
			{
				$name_insert = "";
			}
			
			if ($root == true)
			{
				$root_insert = "t";
			}
			else
			{
				$root_insert = "f";
			}
			
			if ($owner_id)
			{
				$owner_id_insert = $owner_id;
			}
			else
			{
				$owner_id_insert = $user->get_user_id();
			}
			
			if ($owner_group_id)
			{
				$owner_group_id_insert = $owner_group_id;
			}
			else
			{
				$owner_group_id_insert = "NULL";
			}
			
			$sql_folder = "SELECT COUNT(id) AS numberoffolders FROM ".self::FOLDER_TABLE." WHERE toid=".trim($toid)." AND TRIM(NAME)='".trim($name)."'";
			$res_folder = $db->db_query($sql_folder);
			$data_folder = $db->db_fetch_assoc($res_folder);
			
			if ($data_folder[numberoffolders] <= 0 or !$name)
			{
				$sql_write = "INSERT INTO ".self::FOLDER_TABLE." (id, toid, is_root, name, path, datetime, owner_id, owner_group_id, permission, automatic, deleted, blob, flag) " .
									"VALUES (nextval('".self::FOLDER_PK_SEQUENCE."'::regclass), '".$toid."','".$root_insert."','".$name_insert."','".$path."','".$datetime."',".$owner_id_insert.",".$owner_group_id_insert.",'0','t','f','f','0')";		
				
				$res_write = $db->db_query($sql_write);
				
				if ($db->db_affected_rows($res_write) != 1)
				{
					return null;
				}
				else
				{
					$sql_read = "SELECT id FROM ".self::FOLDER_TABLE." WHERE id = currval('".self::FOLDER_PK_SEQUENCE."'::regclass)";
					$res_read = $db->db_query($sql_read);
					$data_read = $db->db_fetch_assoc($res_read);
					
					$this->__construct($data_read[id]);
					
					return $data_read[id];
				}
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

		if ($this->folder_id and $this->path)
		{	
			$folder_id_tmp = $this->folder_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".self::FOLDER_TABLE." WHERE id = ".$folder_id_tmp."";
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
	public function get_toid()
	{
		if ($this->toid)
		{
			return $this->toid;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_is_root()
	{
		if (isset($this->is_root))
		{
			return $this->is_root;
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
	 * @return string
	 */	
	public function get_datetime()
	{
		if ($this->datetime)
		{
			return $this->datetime;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_owner_id()
	{
		if ($this->owner_id)
		{
			return $this->owner_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_owner_group_id()
	{
		if ($this->owner_group_id)
		{
			return $this->owner_group_id;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return integer
	 */
	public function get_permission()
	{
		if ($this->permission)
		{
			return $this->permission;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_automatic()
	{
		if (isset($this->automatic))
		{
			return $this->automatic;
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
	 * @param integer $toid
	 * @return bool
	 */
	public function set_toid($toid)
	{
		global $db;
			
		if ($this->folder_id and is_numeric($toid))
		{
			$sql = "UPDATE ".self::FOLDER_TABLE." SET toid = ".$toid." WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->toid = $toid;
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
	 * @param bool $is_root
	 * @return bool
	 */
	public function set_is_root($is_root)
	{
		global $db;
			
		if ($this->folder_id and isset($is_root))
		{
			if ($is_root == true)
			{
				$is_root_insert = "t";
			}
			else
			{
				$is_root_insert = "f";
			}
			
			$sql = "UPDATE ".self::FOLDER_TABLE." SET is_root = '".$is_root_insert."' WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->is_root = $is_root;
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
			$sql = "UPDATE ".self::FOLDER_TABLE." SET name = '".$name."' WHERE id = ".$this->folder_id."";
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
			$sql = "UPDATE ".self::FOLDER_TABLE." SET path = '".$path."' WHERE id = ".$this->folder_id."";
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;

		if ($this->folder_id and $datetime)
		{
			$sql = "UPDATE ".self::FOLDER_TABLE." SET datetime = '".$datetime."' WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->datetime = $datetime;
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
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id)
	{
		global $db;

		if ($this->folder_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".self::FOLDER_TABLE." SET owner_id = ".$owner_id." WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->owner_id = $owner_id;
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
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public function set_owner_group_id($owner_group_id)
	{
		global $db;

		if ($this->folder_id and is_numeric($owner_group_id))
		{
			$sql = "UPDATE ".self::FOLDER_TABLE." SET owner_group_id = ".$owner_group_id." WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->owner_group_id = $owner_group_id;
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
	 * @param integer $permission
	 * @return bool
	 */
	public function set_permission($permission)
	{
		global $db;

		if ($this->folder_id and isset($permission))
		{
			$sql = "UPDATE ".self::FOLDER_TABLE." SET permission = ".$permission." WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->permission = $permission;
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
	 * @param bool $automatic
	 * @return bool
	 */
	public function set_automatic($automatic)
	{
		global $db;

		if ($this->folder_id and isset($automatic))
		{
			if ($automatic == true)
			{
				$automatic_insert = "t";
			}
			else
			{
				$automatic_insert = "f";
			}
			
			$sql = "UPDATE ".self::FOLDER_TABLE." SET automatic = '".$automatic_insert."' WHERE id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->automatic = $automatic;
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
			
			$sql = "UPDATE ".self::FOLDER_TABLE." SET deleted = '".$deleted_insert."' WHERE id = ".$this->folder_id."";
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
			
			$sql = "UPDATE ".self::FOLDER_TABLE." SET blob = '".$blob_insert."' WHERE id = ".$this->folder_id."";
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
			$sql = "UPDATE ".self::FOLDER_TABLE." SET flag = ".$flag." WHERE id = ".$this->folder_id."";
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
	 * @return array
	 */
	public function list_folders()
	{
		if ($this->folder_id)
		{
			return $this->rec_full_folder_tree_array(0, $this->rec_folder_tree_array($this->folder_id));
		}
		else
		{
			return null;
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
			$sql = "SELECT id FROM ".self::FOLDER_TABLE." WHERE TRIM(LOWER(path)) = '".trim(strtolower($path))."'";

			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				return $data[id];
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
	 * @param integer $toid
	 * @return array
	 */
	public static function list_entries_by_toid($toid)
	{
		global $db;

		if ($toid)
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".self::FOLDER_TABLE." WHERE toid = ".$toid." AND id <> toid";
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
		else
		{
			return null;
		}
	}

	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public static function set_owner_id_on_null($owner_id)
	{	
		global $db;
			
		if (is_numeric($owner_id))
		{
			$sql = "UPDATE ".self::FOLDER_TABLE." SET owner_id = NULL WHERE owner_id = ".$owner_id."";
			$res = $db->db_query($sql);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public static function set_owner_group_id_on_null($owner_group_id)
	{
		global $db;
			
		if (is_numeric($owner_group_id))
		{
			$sql = "UPDATE ".self::FOLDER_TABLE." SET owner_group_id = NULL WHERE owner_group_id = ".$owner_group_id."";
			$res = $db->db_query($sql);
			
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * @param integer $id
	 * @return integer
	 */
	private function rec_folder_tree_array($id)
	{
		global $db;

		if ($id)
		{
			$return_array = array();
			
			$sql = "SELECT id, toid, is_root FROM ".self::FOLDER_TABLE." WHERE id = ".$id."";
			$res = $db->db_query($sql);
			while($data = $db->db_fetch_assoc($res))
			{
				array_unshift($return_array,$data[id]);
				
				if ($data[is_root] == "f" and $data[toid] != null)
				{
					$return_array = array_merge($this->get_rec_folder_tree_array($data[toid]),$return_array);
				}	
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $layer
	 * @param array $array
	 * @return array
	 */
	private function rec_full_folder_tree_array($layer, $array)
	{
		global $db;

		if (is_array($array))
		{
			$return_array = array();
			
			$sql = "SELECT id,toid FROM ".self::FOLDER_TABLE." WHERE toid = ".$array[$layer]."";
			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				if ($data[id] != $data[toid])
				{
					$data_array = array();
					$data_array[id] = $data[id];
					$data_array[layer] = $layer;	
						
					array_push($return_array,$data_array);
					
					unset($data_array);
					
					if ($data[id] == $array[$layer+1])
					{
						$return_array = array_merge($return_array,$this->get_rec_full_folder_tree_array($layer+1, $array));
					}
				}
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}

}

?>
