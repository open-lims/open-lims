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
 * Equipment Has Organisation Unit Access Class
 * @package equipment
 */
class EquipmentHasOrganisationUnit_Access
{
	private $equipment_id;
	private $organisation_unit_id;
	
	/**
	 * @param integer $equipment_id
	 * @param integer $user_id
	 */
	function __construct($equipment_id, $organisation_unit_id)
	{
		global $db;

		if ($equipment_id == null or $organisation_unit_id == null)
		{
			$this->equipment_id = null;
			$this->user_id 		= null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE")." WHERE equipment_id='".$equipment_id."' AND organisation_unit_id='".$organisation_unit_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['equipment_id'])
			{
				$this->equipment_id 		= $equipment_id;
				$this->organisation_unit_id	= $organisation_unit_id;
			}
			else
			{
				$this->equipment_id = null;
				$this->organisation_unit_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->equipment_id) {
			unset($this->equipment_id);
			unset($this->organisation_unit_id);
		}
	}
	
	/**
	 * @param integer $equipment_id
	 * @param integer $user_id
	 * @return true
	 */
	public function create($equipment_id, $organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($equipment_id) and is_numeric($organisation_unit_id))
		{

			$sql_write = "INSERT INTO ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE")." (equipment_id, organisation_unit_id) " .
					"VALUES (".$equipment_id.",".$organisation_unit_id.")";

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
		
		if ($this->equipment_id and $this->organisation_unit_id)
		{
			$tmp_equipment_id = $this->equipment_id;
			$tmp_organisation_unit_id = $this->organisation_unit_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE")." WHERE equipment_id = ".$tmp_equipment_id." AND organisation_unit_id = ".$tmp_organisation_unit_id."";
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
	 * @param integer $equipment_id
	 * @return array
	 */
	public static function get_organisation_unit_ids_by_equipment_id($equipment_id)
	{
		global $db;
	
		if (is_numeric($equipment_id))
		{	
			$return_array = array();
			
			$sql = "SELECT organisation_unit_id FROM ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE")." WHERE equipment_id = ".$equipment_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['organisation_unit_id']);
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
	 * @param integer $organisaiton_unit_id
	 * @return array
	 */
	public static function delete_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			
			$return_array = array();
			
			$sql = "DELETE FROM ".constant("EQUIPMENT_HAS_ORGANISTAION_UNIT_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
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