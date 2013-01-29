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
 * Virtual Folder Access Class
 * @package data
 */
class VirtualFolder_Access
{	
	const VIRTUAL_FOLDER_PK_SEQUENCE = 'core_virtual_folders_id_seq';
	
	private $virtual_folder_id;
	
	private $data_entity_id;
	private $name;
	
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
			$sql = "SELECT * FROM ".constant("VIRTUAL_FOLDER_TABLE")." WHERE id='".$virtual_folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->virtual_folder_id	= $virtual_folder_id;
				
				$this->data_entity_id		= $data['data_entity_id'];
				$this->name					= $data['name'];		
			}
			else
			{
				$this->virtual_folder_id			= null;
			}
		}
		$this->read_access = true;
		$this->write_access = false;
		$this->delete_access = false;
		$this->control_access = false;
	} 

	function __destruct()
	{
		if ($this->virtual_folder_id)
		{
			unset($this->virtual_folder_id);
			unset($this->data_entity_id);
			unset($this->name);
		}
	}

	/**
	 * @param integer $data_entity_id
	 * @param string $name
	 * @return integer
	 */
	public function create($data_entity_id, $name)
	{
		global $db, $session;

		if (is_numeric($data_entity_id) and $name)
		{	
			$sql_write = "INSERT INTO ".constant("VIRTUAL_FOLDER_TABLE")." (id, data_entity_id, name) " .
								"VALUES (nextval('".self::VIRTUAL_FOLDER_PK_SEQUENCE."'::regclass), ".$data_entity_id.",'".$name."')";		

			$res_write = $db->db_query($sql_write);

			if ($db->db_affected_rows($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT id FROM ".constant("VIRTUAL_FOLDER_TABLE")." WHERE id = currval('".self::VIRTUAL_FOLDER_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->virtual_folder_id)
		{	
			$virtual_folder_id_tmp = $this->virtual_folder_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("VIRTUAL_FOLDER_TABLE")." WHERE id = ".$virtual_folder_id_tmp."";
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
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public function set_data_entity_id($data_entity_id)
	{
		global $db;
			
		if ($this->folder_id and is_numeric($data_entity_id))
		{
			$sql = "UPDATE ".constant("VIRTUAL_FOLDER_TABLE")." SET data_entity_id = ".$data_entity_id." WHERE id = ".$this->virtual_folder_id."";
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

		if ($this->virtual_folder_id and $name)
		{
			$sql = "UPDATE ".constant("VIRTUAL_FOLDER_TABLE")." SET name = '".$name."' WHERE id = ".$this->virtual_folder_id."";
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
	 * @param integer $data_entity_id
	 * @return array
	 */
	public static function get_entry_by_data_entity_id($data_entity_id)
	{
		global $db;

		if (is_numeric($data_entity_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("VIRTUAL_FOLDER_TABLE")." WHERE data_entity_id = ".$data_entity_id."";
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
	 * @param integer $virtual_folder_id
	 * @return bool
	 */
	public static function exist_virtual_folder_by_virtual_folder_id($virtual_folder_id)
	{
		global $db;
			
		if (is_numeric($virtual_folder_id))
		{
			$sql = "SELECT id FROM ".constant("VIRTUAL_FOLDER_TABLE")." WHERE id = ".$virtual_folder_id."";
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
