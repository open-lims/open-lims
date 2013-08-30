<?php
/**
 * @package equipment
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
 * 
 */
require_once("interfaces/equipment_cat.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/equipment_cat.access.php");
}

/**
 * Equipment Category Management Class
 * @package equipment
 */
class EquipmentCat implements EquipmentCatInterface
{
	private $equipment_cat_id;
	private $equipment_cat;

	/**
	 * @see EquipmentCatInterface::__construct()
	 * @param integer $equipment_cat_id
	 * @throws EquipmentCategoryNotFoundException
	 */
	function __construct($equipment_cat_id)
	{
		if (is_numeric($equipment_cat_id))
		{
			if (EquipmentCat_Access::exist_id($equipment_cat_id) == true)
			{
				$this->equipment_cat_id = $equipment_cat_id;
				$this->equipment_cat = new EquipmentCat_Access($equipment_cat_id);
			}
			else
			{
				throw new EquipmentCategoryNotFoundException();
			}
		}
		else
		{
			$this->equipment_cat_id = null;
			$this->equipment_cat = new EquipmentCat_Access(null);
		}
    }
    
    function __destruct()
    {
    	unset($this->equipment_cat_id);
    	unset($this->equipment_cat);
    }

	/**
	 * @see EquipmentCatInterface::create()
	 * @param integer $toid
	 * @param string $name
	 * @return integer
	 */
	public function create($toid, $name)
	{
		if ($this->equipment_cat)
    	{
    		return $this->equipment_cat->create($toid, $name);
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * @see EquipmentCatInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		if ($this->equipment_cat_id and $this->equipment_cat)
    	{
    		if ($this->get_children() != null)
			{
				return false;
			}
			else
			{
	    		$equipment_type_array = EquipmentType::list_entries_by_cat_id($this->equipment_cat_id);
	    		if (!is_array($equipment_type_array))
	    		{
	    			return $this->equipment_cat->delete();
	    		}
	    		else
	    		{
	    			if (count($equipment_type_array) == 0)
	    			{
	    				return $this->equipment_cat->delete();
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
	 * @see EquipmentCatInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->equipment_cat_id and $this->equipment_cat)
    	{
    		return $this->equipment_cat->get_name();
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * @see EquipmentCatInterface::set_name()
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		if ($this->equipment_cat_id and $this->equipment_cat and $name)
    	{
    		return $this->equipment_cat->set_name($name);
    	}
    	else
    	{
    		return null;
    	}
	}

	/**
	 * @see EquipmentCatInterface::get_children()
	 * @return array
	 */
   	public function get_children()
    {
    	if ($this->equipment_cat_id)
    	{
    		return EquipmentCat_Access::list_entries_by_toid($this->equipment_cat_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    
    /**
     * @see EquipmentCatInterface::exist_id()
     * @param integer $id
     * @return bool
     */
    public static function exist_id($id)
    {
    	return EquipmentCat_Access::exist_id($id);
    }
    
    /**
     * @see EquipmentCatInterface::exist_name()
     * @param string $name
     * @return bool
     */
    public static function exist_name($name)
    {
    	return EquipmentCat_Access::exist_name($name);
    }
    
    /**
     * @see EquipmentCatInterface::list_root_entries()
     * @return array
     */
    public static function list_root_entries()
    {
    	return EquipmentCat_Access::list_root_entries();
    }
    
    /**
     * @see EquipmentCatInterface::list_entries()
     * @return array
     */
    public static function list_entries()
    {
		return EquipmentCat_Access::list_entries();
	}
    
}
?>