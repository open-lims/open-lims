<?php
/**
 * @package organisation_unit
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Organisation Unit User Ownership Access Class
 * @package organisation_unit
 */
class OrganisationUnitHasOwner_Access
{
	private $organisation_unit_id;
	private $owner_id;
	private $master_owner;
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $owner_id
	 */
	function __construct($organisation_unit_id, $owner_id)
	{
		global $db;
		
		if (!is_numeric($organisation_unit_id) or !is_numeric($owner_id))
		{
			$this->organisation_unit_id = null;
			$this->owner_id = null;
		}
		else
		{	
			$sql = "SELECT * FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id." AND owner_id = ".$owner_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[organisation_unit_id])
			{
				$this->organisation_unit_id	= $data[organisation_unit_id];
				$this->owner_id				= $data[owner_id];
				
				if ($data[master_owner] == 't')
				{
					$this->master_owner = true;
				}
				else
				{
					$this->master_owner = false;
				}
			}
		}
	}
	
	function __destruct()
	{
		if ($this->organisation_unit_id and $this->owner_id)
		{
			unset($this->organisation_unit_id);
			unset($this->owner_id);
			unset($this->master_owner);
		}
	}

	/**
	 * @param integer $organisation_unit_id
	 * @param integer $owner_id
	 * @return bool
	 */
	public function create($organisation_unit_id, $owner_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id) and is_numeric($owner_id))
		{
			$sql_write = "INSERT INTO ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." (organisation_unit_id,owner_id) " .
					"VALUES (".$organisation_unit_id.",".$owner_id.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$this->__construct($organisation_unit_id, $owner_id);
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->organisation_unit_id and $this->owner_id)
		{
			$tmp_organisation_unit_id = $this->organisation_unit_id;
			$tmp_owner_id = $this->owner_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE organisation_unit_id = ".$tmp_organisation_unit_id." AND owner_id = ".$tmp_owner_id."";
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
	public function get_organisation_unit_id()
	{
		if ($this->organisation_unit_id)
		{
			return $this->organisation_unit_id;
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
	 * @return bool
	 */
	public function get_master_owner()
	{
		if (isset($this->master_owner))
		{
			return $this->master_owner;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public function set_organisation_unit_id($organisation_unit_id)
	{
		global $db;
			
		if ($this->organisation_unit_id and $this->owner_id and is_numeric($organisation_unit_id))
		{
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." SET organisation_unit_id = '".$organisation_unit_id."' WHERE organisation_unit_id = '".$this->organisation_unit_id."' AND owner_id='".$this->owner_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->organisation_unit_id = $organisation_unit_id;
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

		if ($this->organisation_unit_id and $this->owner_id and is_numeric($owner_id))
		{
			
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." SET owner_id = '".$owner_id."' WHERE organisation_unit_id = '".$this->organisation_unit_id."' AND owner_id='".$this->owner_id."'";
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
	 * @param bool $master_owner
	 * @return bool
	 */
	public function set_master_owner($master_owner)
	{
		global $db;

		if ($this->organisation_unit_id and $this->owner_id and isset($master_owner))
		{
			if ($master_owner == true)
			{
				$master_owner_insert = "t";
			}
			else
			{
				$master_owner_insert = "f";
			}
			
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." SET master_owner = '".$master_owner_insert."' WHERE organisation_unit_id = '".$this->organisation_unit_id."' AND owner_id='".$this->owner_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->master_owner = $master_owner;
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
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function get_master_owner_id_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
			
		if (is_numeric($organisation_unit_id))
		{
				
			$return_array = array();
			
			$sql = "SELECT owner_id FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id." AND master_owner = 't'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
						
			if ($data[owner_id])
			{
				return $data[owner_id];
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
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function get_first_owner_id_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
			
		if (is_numeric($organisation_unit_id))
		{
				
			$return_array = array();
			
			$sql = "SELECT owner_id FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
						
			if ($data[owner_id])
			{
				return $data[owner_id];
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
	 * @return integer
	 */
	public static function count_organisation_units_by_owner_id($owner_id)
	{		
		global $db;
		
		if (is_numeric($owner_id))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(organisation_unit_id) AS result FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE owner_id = ".$owner_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[result])
			{
				return $data[result];
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
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_owners_by_organisation_unit_id($organisation_unit_id)
	{		
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(owner_id) AS result FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[result])
			{
				return $data[result];
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
	 * @return integer
	 */
	public static function list_organisation_units_by_owner_id($owner_id)
	{
		global $db;
			
		if (is_numeric($owner_id))
		{
				
			$return_array = array();
			
			$sql = "SELECT organisation_unit_id FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE owner_id = ".$owner_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[organisation_unit_id]);
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
	 * @param integer $organisation_unit_id
	 * @return array
	 */
	public static function list_owners_by_organisation_unit_id($organisation_unit_id, $limit)
	{
		global $db;
			
		if (is_numeric($organisation_unit_id))
		{
			if ($limit > 0)
			{
				$limit_sql = "LIMIT ".$limit;
			}
			else
			{
				$limit_sql = "";
			}
			
			$return_array = array();
			
			$sql = "SELECT owner_id FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."".$limit_sql;
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[owner_id]);
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
	public static function delete_by_owner_id($owner_id)
	{
		global $db;
		
		if (is_numeric($owner_id))
		{
			
			$return_array = array();
			
			$sql = "DELETE FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE owner_id = ".$owner_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			return true;	
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public static function delete_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			$return_array = array();
			
			$sql = "DELETE FROM ".constant("ORGANISATION_UNIT_HAS_OWNER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			return true;	
		}
		else
		{
			return false;
		}
	}


}
?>