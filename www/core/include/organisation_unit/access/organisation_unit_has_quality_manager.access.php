<?php
/**
 * @package organisation_unit
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
 * Organisation Unit User Quality Managership Access Class
 * @package organisation_unit
 */
class OrganisationUnitHasQualityManager_Access
{
	private $organisation_unit_id;
	private $quality_manager_id;
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $quality_manager_id
	 */
	function __construct($organisation_unit_id, $quality_manager_id)
	{
		global $db;
		
		if (!is_numeric($organisation_unit_id) or !is_numeric($quality_manager_id))
		{
			$this->organisation_unit_id = null;
			$this->quality_manager_id = null;
		}
		else
		{	
			$sql = "SELECT * FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id." AND quality_manager_id = ".$quality_manager_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[organisation_unit_id])
			{
				$this->organisation_unit_id	= $data[organisation_unit_id];
				$this->quality_manager_id			= $data[quality_manager_id];
			}
		}
	}
	
	function __destruct()
	{
		if ($this->organisation_unit_id and $this->quality_manager_id)
		{
			unset($this->organisation_unit_id);
			unset($this->quality_manager_id);
		}
	}

	/**
	 * @param integer $organisation_unit_id
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public function create($organisation_unit_id, $quality_manager_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id) and is_numeric($quality_manager_id))
		{
			$sql_write = "INSERT INTO ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." (organisation_unit_id,quality_manager_id) " .
					"VALUES (".$organisation_unit_id.",".$quality_manager_id.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->organisation_unit_id and $this->quality_manager_id)
		{
			$tmp_organisation_unit_id = $this->organisation_unit_id;
			$tmp_quality_manager_id = $this->quality_manager_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." WHERE organisation_unit_id = ".$tmp_organisation_unit_id." AND quality_manager_id = ".$tmp_quality_manager_id."";
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
	public function get_quality_manager_id()
	{
		if ($this->quality_manager_id)
		{
			return $this->quality_manager_id;
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
			
		if ($this->organisation_unit_id and $this->quality_manager_id and is_numeric($organisation_unit_id))
		{
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." SET organisation_unit_id = '".$organisation_unit_id."' WHERE organisation_unit_id = '".$this->organisation_unit_id."' AND quality_manager_id='".$this->quality_manager_id."'";
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
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public function set_quality_manager_id($quality_manager_id)
	{
		global $db;

		if ($this->organisation_unit_id and $this->quality_manager_id and is_numeric($quality_manager_id))
		{
			
			$sql = "UPDATE ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." SET quality_manager_id = '".$quality_manager_id."' WHERE organisation_unit_id = '".$this->organisation_unit_id."' AND quality_manager_id='".$this->quality_manager_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->quality_manager_id = $quality_manager_id;
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
	 * @param integer $quality_manager_id
	 * @return integer
	 */
	public static function count_organisation_units_by_quality_manager_id($quality_manager_id)
	{		
		global $db;
		
		if (is_numeric($quality_manager_id))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(organisation_unit_id) AS result FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." WHERE quality_manager_id = ".$quality_manager_id."";
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
	public static function count_quality_managers_by_organisation_unit_id($organisation_unit_id)
	{		
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(quality_manager_id) AS result FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
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
	 * @param integer $quality_manager_id
	 * @return integer
	 */
	public static function list_organisation_units_by_quality_manager_id($quality_manager_id)
	{
		global $db;
			
		if (is_numeric($quality_manager_id))
		{
				
			$return_array = array();
			
			$sql = "SELECT organisation_unit_id FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." WHERE quality_manager_id = ".$quality_manager_id."";
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
	public static function list_quality_managers_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
			
		if (is_numeric($organisation_unit_id))
		{
			$return_array = array();
			
			$sql = "SELECT quality_manager_id FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[quality_manager_id]);
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
	 * @param integer $quality_manager_id
	 * @return bool
	 */
	public static function delete_by_quality_manager_id($quality_manager_id)
	{
		global $db;
		
		if (is_numeric($quality_manager_id))
		{
			
			$return_array = array();
			
			$sql = "DELETE FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." WHERE quality_manager_id = ".$quality_manager_id."";
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
			
			$sql = "DELETE FROM ".constant("ORGANISATION_UNIT_HAS_QUALITY_MANAGER_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
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