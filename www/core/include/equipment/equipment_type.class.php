<?php
/**
 * @package equipment
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
 * 
 */
require_once("interfaces/equipment_type.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/equipment_type_not_found_exception.class.php");
	
	require_once("access/equipment_type.access.php");
	require_once("access/equipment_cat.access.php");
	require_once("access/equipment_has_responsible_person.access.php");
	require_once("access/equipment_has_organisation_unit.access.php");
}

/**
 * Equipment Type Management Class
 * @package equipment
 */
class EquipmentType implements EquipmentTypeInterface, EventListenerInterface
{
	private $equipment_type_id;
	private $equipment_type;

	/**
	 * @param integer $equipment_type_id
	 */
    function __construct($equipment_type_id)
    {
    	if ($equipment_type_id) {
			$this->equipment_type_id = $equipment_type_id;
			$this->equipment_type = new EquipmentType_Access($equipment_type_id);
		}else{
			$this->equipment_type_id = null;
			$this->equipment_type = new EquipmentType_Access(null);
		}
    }
    
    function __destruct()
    {
    	unset($this->equipment_type_id);
    	unset($this->equipment_type);
    }
    
    /**
     * Creates a new equipment-type
     * @param integer $toid
     * @param string $name
     * @param integer $cat_id
     * @param integer $location_id
     * @param string $description
     * @return integer
     */
    public function create($toid, $name, $cat_id, $location_id, $description, $manufacturer)
    {
    	if ($this->equipment_type)
    	{
    		return $this->equipment_type->create($toid, $name, $cat_id, $location_id, $description, $manufacturer);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Deletes a equipment-type
     * @return bool
     */
    public function delete()
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		if ($this->get_childs() != null)
			{
				return false;
			}
			else
			{
	    		$equipment_array = Equipment::list_entries_by_type_id($this->equipment_type_id);
	    		if (!is_array($equipment_array))
	    		{
	    			return $this->equipment_type->delete();
	    		}
	    		else
	    		{
	    			if (count($equipment_array) == 0)
	    			{
	    				return $this->equipment_type->delete();
	    			}
	    			else
	    			{
	    				return false;
	    			}
	    		}
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
    public function get_name()
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		$name = $this->equipment_type->get_name();
    		$manufacturer = $this->equipment_type->get_manufacturer();
    		
    		if ($name and $manufacturer)
    		{
    			return $manufacturer." ".$name;
    		}
    		elseif($name and !$manufacturer)
    		{
    			return $name;
    		}
    		elseif(!$name and $manufacturer)
    		{
    			return $manufacturer;
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
     * @return string
     */
    public function get_internal_name()
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		return $this->equipment_type->get_name();
    	}
    	else
    	{
    		return null;
    	}
    }
    
	/**
     * @return string
     */
    public function get_manufacturer()
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		return $this->equipment_type->get_manufacturer();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return string
     */
    public function get_description()
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		return $this->equipment_type->get_description();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Returns the cateogory-name of the current equipment-type
     * @return string
     */
    public function get_cat_id()
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		return $this->equipment_type->get_cat_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Returns the cateogory-name of the current equipment-type
     * @return string
     */
    public function get_cat_name()
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		$equipment_cat = new EquipmentCat($this->equipment_type->get_cat_id());
    		return $equipment_cat->get_name();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    public function get_location_id()
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		return $this->equipment_type->get_location_id();
    	}
    	else
    	{
    		return null;
    	}
    }
        
    /**
     * Returns the childs of the current equipment-type
     * @return array
     */
    public function get_childs()
    {
    	if ($this->equipment_type_id)
    	{
    		return EquipmentType_Access::list_entries_by_toid($this->equipment_type_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
  	/**
     * @param string $name
     * @return bool
     */
    public function set_name($name)
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		return $this->equipment_type->set_name($name);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param string $name
     * @return bool
     */
    public function set_manufacturer($manufacturer)
    {
    	if ($this->equipment_type_id and $this->equipment_type)
    	{
    		return $this->equipment_type->set_manufacturer($manufacturer);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    
	/**
     * @param string $name
     * @return bool
     */
    public function set_location_id($location_id)
    {
    	if ($this->equipment_type_id and $this->equipment_type and is_numeric($location_id))
    	{
    		return $this->equipment_type->set_location_id($location_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param integer $user_id
     * @return bool
     */
    public function add_responsible_person($user_id)
    {
    	if ($this->equipment_type_id and is_numeric($user_id))
		{
			$equipment_has_responsible_person = new EquipmentHasResponsiblePerson_Access(null, null);
			if ($equipment_has_responsible_person->create($this->equipment_type_id, $user_id) != null)
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
     * @param integer $user_id
     * @return bool
     */
    public function delete_responsible_person($user_id)
    {
   		if ($this->equipment_type_id and is_numeric($user_id))
		{
			$equipment_has_responsible_person = new EquipmentHasResponsiblePerson_Access($this->equipment_type_id, $user_id);
			if ($equipment_has_responsible_person->delete())
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
     * @param integer $user_id
     * @return bool
     */
    public function is_user_responsible($user_id)
    {
   		if ($this->equipment_type_id and is_numeric($user_id))
		{
			$user_array = EquipmentHasResponsiblePerson_Access::get_user_ids_by_equipment_id($this->equipment_type_id);
			if (in_array($user_id, $user_array))
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
     * @return array
     */
    public function list_users()
    {
   		if ($this->equipment_type_id)
		{
			return EquipmentHasResponsiblePerson_Access::get_user_ids_by_equipment_id($this->equipment_type_id);
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
    public function add_organisation_unit($organisation_unit_id)
    {
    	if ($this->equipment_type_id and is_numeric($organisation_unit_id))
		{
			$equipment_has_organisation_unit = new EquipmentHasOrganisationUnit_Access(null, null);
			if ($equipment_has_organisation_unit->create($this->equipment_type_id, $organisation_unit_id) != null)
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
     * @param integer $organisation_unit_id
     * @return bool
     */
    public function delete_organisation_unit($organisation_unit_id)
    {
   		if ($this->equipment_type_id and is_numeric($organisation_unit_id))
		{
			$equipment_has_organisation_unit = new EquipmentHasOrganisationUnit_Access($this->equipment_type_id, $organisation_unit_id);
			if ($equipment_has_organisation_unit->delete())
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
     * @param integer $organisation_unit
     * @return bool
     */
    public function is_organisation_unit($organisation_unit_id)
    {
   		if ($this->equipment_type_id and is_numeric($organisation_unit_id))
		{
			$organisation_unit_array = EquipmentHasOrganisationUnit_Access::get_organisation_unit_ids_by_equipment_id($this->equipment_type_id);
			if (in_array($organisation_unit_id, $organisation_unit_array))
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
     * @return array
     */
    public function list_organisation_units()
    {
   		if ($this->equipment_type_id)
		{
			return EquipmentHasOrganisationUnit_Access::get_organisation_unit_ids_by_equipment_id($this->equipment_type_id);
		}
		else
		{
			return null;
		}
    }
    
    
    /**
     * @param integer $id
     * @return bool
     */
    public static function exist_id($id)
    {
    	return EquipmentType_Access::exist_id($id);
    }
    
    /**
     * @param string $name
     * @return bool
     */
    public static function exist_name($name)
    {
    	return EquipmentType_Access::exist_name($name);
    }
    
    /**
     * @return array
     */
    public static function list_entries_by_cat_id($cat_id)
    {
		return EquipmentType_Access::list_entries_by_cat_id($cat_id);
	}
    
    /**
     * @return array
     */
    public static function list_root_entries()
    {
    	return EquipmentType_Access::list_root_entries();
    }
    
    /**
     * @return array
     */
    public static function list_entries_by_id($id)
    {
		if (is_numeric($id))
		{
			$return_array = array();
			$toid_array = EquipmentType_Access::list_entries_by_toid($id);
			if (is_array($toid_array) and count($toid_array) >= 1)
			{
				foreach($toid_array as $key => $value)
				{
					array_push($return_array, $value);
					$return_array = array_merge($return_array, self::list_entries_by_id($value));
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
     * @return array
     */
    public static function list_entries()
    {
		return EquipmentType_Access::list_entries();
	}
	
    /**
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeleteEvent)
    	{
			if (EquipmentHasResponsiblePerson_Access::delete_by_user_id($event_object->get_user_id()) == false)
			{
				return false;
			}
    	}

   		if ($event_object instanceof OrganisationUnitDeleteEvent)
    	{
    		if (EquipmentHasOrganisationUnit_Access::delete_by_organisation_unit_id($event_object->get_organisation_unit_id()) == false)
			{
				return false;
			}
    	}
    	
    	return true;
    }
}
?>