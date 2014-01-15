<?php
/**
 * @package equipment
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
 * Equipment Access Class
 * @package equipment
 */
class Equipment_Access
{
	const EQUIPMENT_PK_SEQUENCE = 'core_equipment_id_seq';

	private $equipment_id;

	private $type_id;
	private $onwer_id;
	private $datetime;

	/**
	 * @param integer $equipment_id
	 */
	function __construct($equipment_id)
	{
		global $db;
			
		if ($equipment_id == null)
		{
			$this->equipment_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("EQUIPMENT_TABLE")." WHERE id='".$equipment_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->equipment_id 	= $equipment_id;
				
				$this->type_id		= $data['type_id'];
				$this->owner_id		= $data['owner_id'];
				$this->datetime		= $data['datetime'];
			}
			else
			{
				$this->equipment_id = null;
			}
		}	
	}
	
	function __destruct()
	{
		if ($this->equipment_id)
		{
			unset($this->equipment_id);
			unset($this->type_id);
			unset($this->owner_id);
			unset($this->datetime);
		}
	}
	
	/**
	 * @param integer $type_id
	 * @param integer $owner_id
	 * @return integer
	 */
	public function create($type_id, $owner_id)
	{
		global $db;
		
		if (is_numeric($type_id) and is_numeric($owner_id))
		{
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("EQUIPMENT_TABLE")." (id,type_id,owner_id,datetime) " .
					"VALUES (nextval('".self::EQUIPMENT_PK_SEQUENCE."'::regclass),".$type_id.",".$owner_id.",'".$datetime."')";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("EQUIPMENT_TABLE")." WHERE id = currval('".self::EQUIPMENT_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
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
		
		if ($this->equipment_id)
		{
			$tmp_equipment_id = $this->equipment_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("EQUIPMENT_TABLE")." WHERE id = ".$tmp_equipment_id."";
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
	public function get_type_id()
	{
		if ($this->type_id)
		{
			return $this->type_id;
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
	 * @param integer $type_id
	 * @return bool
	 */
	public function set_type_id($type_id)
	{
		global $db;

		if ($this->equipment_id and is_numeric($type_id))
		{
			$sql = "UPDATE ".constant("EQUIPMENT_TABLE")." SET type_id = '".$type_id."' WHERE id = '".$this->equipment_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->type_id = $type_id;
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
	
		if ($this->equipment_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("EQUIPMENT_TABLE")." SET owner_id = '".$owner_id."' WHERE id = '".$this->equipment_id."'";
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;

		if ($this->equipment_id and $datetime)
		{
			$sql = "UPDATE ".constant("EQUIPMENT_TABLE")." SET datetime = '".$datetime."' WHERE id = '".$this->equipment_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->odatetime = $datetime;
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
		
		if (is_numeric($id))
		{	
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("EQUIPMENT_TABLE")." WHERE id=".$id."";
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
	 * @return array
	 */
	public static function list_entries_by_owner_id($owner_id)
	{
		global $db;
		
		if (is_numeric($owner_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("EQUIPMENT_TABLE")." WHERE owner_id = ".$owner_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['id']);
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
	 * @param integer $type_id
	 * @return array
	 */
	public static function list_entries_by_type_id($type_id)
	{
		global $db;
		
		if (is_numeric($type_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("EQUIPMENT_TABLE")." WHERE type_id = ".$type_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['id']);
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;

		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("EQUIPMENT_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data['id']);
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
	
}

?>
