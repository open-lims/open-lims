<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Data Entity Access Class
 * @package data
 */
class DataEntity_Access
{
	const DATA_ENTITY_PK_SEQUENCE = 'core_data_entities_id_seq';

	private $id;
	private $datetime;
	private $owner_id;
	private $owner_group_id;
	private $permission;
	private $automatic;

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
			$sql = "SELECT * FROM ".constant("DATA_ENTITY_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->id				= $id;
				$this->datetime			= $data[datetime];
				$this->owner_id			= $data[owner_id];
				$this->owner_group_id	= $data[owner_group_id];
				$this->permission		= $data[permission];
				
				if ($data[automatic] == "t")
				{
					$this->automatic = true;
				}
				else
				{
					$this->automatic = false;
				}
			}
			else
			{
				$this->id = null;
			}
		}
	}

	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->datetime);
			unset($this->owner_id);
			unset($this->owner_group_id);
			unset($this->permission);
			unset($this->automatic);
		}	
	}

	/**
	 * @param integer $owner_id
	 * @param integer $owner_group_id
	 * @return integer
	 */
	public function create($owner_id, $owner_group_id)
	{
		global $db;

		$datetime = date("Y-m-d H:i:s");
		
		if ($owner_id)
		{
			$owner_id_insert = $owner_id;
		}
		else
		{
			$owner_id_insert = "null";
		}
		
		if ($owner_group_id)
		{
			$owner_group_id_insert = $owner_group_id;
		}
		else
		{
			$owner_group_id_insert = "null";
		}
		
		$sql_write = "INSERT INTO ".constant("DATA_ENTITY_TABLE")." (id,datetime,owner_id,owner_group_id,permission,automatic) " .
				"VALUES (nextval('".self::DATA_ENTITY_PK_SEQUENCE."'::regclass),'".$datetime."',".$owner_id_insert.",".$owner_group_id_insert.",NULL,'t')";
				
		$res_write = $db->db_query($sql_write);	
		
		if ($db->db_affected_rows($res_write) == 1)
		{
			$sql_read = "SELECT id FROM ".constant("DATA_ENTITY_TABLE")." WHERE id = currval('".self::DATA_ENTITY_PK_SEQUENCE."'::regclass)";
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
	
		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("DATA_ENTITY_TABLE")." WHERE id = ".$id_tmp."";
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
	public function get_datetime()
	{
		if (isset($this->datetime))
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
		if (isset($this->owner_id))
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
		if (isset($this->owner_group_id))
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
		if (isset($this->permission))
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;
			
		if ($this->id and $datetime)
		{
			$sql = "UPDATE ".constant("DATA_ENTITY_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->id."";
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
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id)
	{
		global $db;
			
		if ($this->id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("DATA_ENTITY_TABLE")." SET owner_id = ".$owner_id." WHERE id = ".$this->id."";
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
			
		if ($this->id and is_numeric($owner_group_id))
		{
			$sql = "UPDATE ".constant("DATA_ENTITY_TABLE")." SET owner_group_id = ".$owner_group_id." WHERE id = ".$this->id."";
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
			
		if ($this->id and $permission)
		{
			$sql = "UPDATE ".constant("DATA_ENTITY_TABLE")." SET permission = ".$permission." WHERE id = ".$this->id."";
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

		if ($this->id and isset($automatic))
		{
			if ($automatic == true)
			{
				$automatic_insert = "t";
			}
			else
			{
				$automatic_insert = "f";
			}
			
			$sql = "UPDATE ".constant("DATA_ENTITY_TABLE")." SET automatic = '".$automatic_insert."' WHERE id = ".$this->id."";
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

	
	public static function exist_id($id)
	{
		global $db;
			
		if (is_numeric($id))
		{
			$sql = "SELECT id FROM ".constant("DATA_ENTITY_TABLE")." WHERE id='".$id."'";
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
	 * @param integer $owner_id
	 * @return bool
	 */
	public static function set_owner_id_on_null($owner_id)
	{
		global $db;
			
		if (is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("DATA_ENTITY_TABLE")." SET owner_id = NULL WHERE owner_id = ".$owner_id."";
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
			$sql = "UPDATE ".constant("DATA_ENTITY_TABLE")." SET owner_group_id = NULL WHERE owner_group_id = ".$owner_group_id."";
			$res = $db->db_query($sql);
			
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>
