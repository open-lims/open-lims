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
 * Virtual Folder Access Class
 * @package data
 */
class VirtualFolder_Access
{	
	const VIRTUAL_FOLDER_TABLE = 'core_virtual_folders';
	const VIRTUAL_FOLDER_PK_SEQUENCE = 'core_virtual_folders_id_seq';
	
	private $virtual_folder_id;
	
	private $folder_id;
	private $name;
	private $datetime;
	
	/**
	 * @param integer $virutal_folder_id
	 */
	function __construct($virtual_folder_id)
	{
		global $db;
		
		if ($virtual_folder_id == null)
		{
			$this->virtual_folder_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::VIRTUAL_FOLDER_TABLE." WHERE id='".$virtual_folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->virtual_folder_id	= $virtual_folder_id;
				
				$this->folder_id			= $data[folder_id];
				$this->name					= $data[name];
				$this->datetime				= $data[datetime];			
			}
			else
			{
				$this->virtual_folder_id			= null;
			}
		}

	} 

	function __destruct()
	{
		if ($this->virtual_folder_id)
		{
			unset($this->virtual_folder_id);
			unset($this->folder_id);
			unset($this->name);
			unset($this->datetime);
		}
	}

	/**
	 * @param integer $folder_id
	 * @param string $name
	 * @return integer
	 */
	public function create($folder_id, $name)
	{
		global $db, $session;

		if (is_numeric($folder_id) and $name)
		{
			$datetime = date("Y-m-d H:i:s");
					
			$sql_write = "INSERT INTO ".self::VIRTUAL_FOLDER_TABLE." (id, folder_id, name, datetime) " .
								"VALUES (nextval('".self::VIRTUAL_FOLDER_PK_SEQUENCE."'::regclass), ".$folder_id.",'".$name."','".$datetime."')";		
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT id FROM ".self::VIRTUAL_FOLDER_TABLE." WHERE id = currval('".self::VIRTUAL_FOLDER_PK_SEQUENCE."'::regclass)";
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

	/**
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->virtual_folder_id)
		{	
			$virtual_folder_id_tmp = $this->virtual_folder_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".self::VIRTUAL_FOLDER_TABLE." WHERE id = ".$virtual_folder_id_tmp."";
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
	public function get_folder_id()
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
	 * @param integer $folder_id
	 * @return bool
	 */
	public function set_folder_id($folder_id)
	{
		global $db;
			
		if ($this->folder_id and is_numeric($folder_id))
		{
			$sql = "UPDATE ".self::VIRTUAL_FOLDER_TABLE." SET folder_id = ".$folder_id." WHERE id = ".$this->virtual_folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->folder_id = $folder_id;
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

		if ($this->virtual_folder_id and $name)
		{
			$sql = "UPDATE ".self::VIRTUAL_FOLDER_TABLE." SET name = '".$name."' WHERE id = ".$this->virtual_folder_id."";
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;

		if ($this->virtual_folder_id and $datetime)
		{
			$sql = "UPDATE ".self::VIRTUAL_FOLDER_TABLE." SET datetime = '".$datetime."' WHERE id = ".$this->virtual_folder_id."";
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
	 * @param integer $folder_id
	 * @return array
	 */
	public static function list_entries_by_folder_id($folder_id)
	{
		global $db;

		if (is_numeric($folder_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".self::VIRTUAL_FOLDER_TABLE." WHERE folder_id = ".$folder_id."";
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
	 * @param integer $virtual_folder_id
	 * @return bool
	 */
	public static function exist_virtual_folder_by_virtual_folder_id($virtual_folder_id)
	{
		global $db;
			
		if (is_numeric($virtual_folder_id))
		{
			$sql = "SELECT id FROM ".self::VIRTUAL_FOLDER_TABLE." WHERE id = ".$virtual_folder_id."";
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
		else
		{
			return false;
		}
	}
	
}

?>
