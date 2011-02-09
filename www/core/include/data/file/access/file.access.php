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
 * File Access Class
 * @package data
 */
class File_Access
{
	const FILE_TABLE = 'core_files';
	const FILE_PK_SEQUENCE = 'core_files_id_seq';

	private $file_id;
	private $data_entity_id;
	private $flag;

	/**
	 * @param integer $file_id
	 */
	function __construct($file_id)
	{
		global $db;
		
		if ($file_id == null)
		{
			$this->file_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::FILE_TABLE." WHERE id='".$file_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->file_id			= $file_id;
				$this->data_entity_id	= $data[data_entity_id];
				$this->flag				= $data[flag];
			}
			else
			{
				$this->file_id			= null;
			}				
		}
	}

	function __destruct()
	{
		if ($this->file_id)
		{
			unset($this->file_id);
			unset($this->data_entity_id);
			unset($this->flag);
		}
	}

	/**
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public function create($data_entity_id)
	{
		global $db;
		
		if (is_numeric($data_entity_id))
		{
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".self::FILE_TABLE." (id,data_entity_id,flag) " .
					"VALUES (nextval('".self::FILE_PK_SEQUENCE."'::regclass),".$data_entity_id.",0)";
					
			$db->db_query($sql_write);	
			
			$sql_read = "SELECT id FROM ".self::FILE_TABLE." WHERE id = currval('".self::FILE_PK_SEQUENCE."'::regclass)";
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
	
	/**
	 * @return bool
	 */
	public function delete()
	{
		global $db;
			
		if ($this->file_id)
		{
			$file_id_tmp = $this->file_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".self::FILE_TABLE." WHERE id = ".$file_id_tmp."";
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

		if ($this->file_id and is_numeric($data_entity_id))
		{
			$sql = "UPDATE ".self::FILE_TABLE." SET data_entity_id = ".$data_entity_id." WHERE id = ".$this->file_id."";
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
	 * @param integer $flag
	 * @return bool
	 */
	public function set_flag($flag)
	{
		global $db;

		if ($this->file_id and is_numeric($flag))
		{
			$sql = "UPDATE ".self::FILE_TABLE." SET flag = ".$flag." WHERE id = ".$this->file_id."";
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
	 * @param string $data_entity_id
	 * @return integer
	 */
	public static function get_entry_by_data_entity_id($data_entity_id)
	{
		global $db;

		if (is_numeric($data_entity_id))
		{
			$sql = "SELECT id FROM ".self::FILE_TABLE." WHERE data_entity_id = '".$data_entity_id."'";

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
	 * @param integer $file_id
	 * @return bool
	 */
	public static function exist_file_by_file_id($file_id)
	{
		global $db;
			
		if (is_numeric($file_id))
		{
			$sql = "SELECT id FROM ".self::FILE_TABLE." WHERE id = ".$file_id."";
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