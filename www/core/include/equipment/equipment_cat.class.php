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
require_once("interfaces/equipment_cat.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/equipment_category_not_found_exception.class.php");
	
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
	 * @param integer $equipment_cat_id
	 */
	function __construct($equipment_cat_id)
	{
    	if ($equipment_cat_id)
    	{
			$this->equipment_cat_id = $equipment_cat_id;
			$this->equipment_cat = new EquipmentCat_Access($equipment_cat_id);
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
	 * Creates a equipment-category
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
	 * Deletes a equipment-category
	 * @return bool
	 */
	public function delete()
	{
		if ($this->equipment_cat_id and $this->equipment_cat)
    	{
    		if ($this->get_childs() != null)
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
    		echo "e";
    		return false;
    	}
	}
	
	/**
	 * @return integer
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
	 * Returns the child of the current equipment-category
	 * @return array
	 */
   	public function get_childs()
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
     * @param integer $id
     * @return bool
     */
    public static function exist_id($id)
    {
    	return EquipmentCat_Access::exist_id($id);
    }
    
    /**
     * @param string $name
     * @return bool
     */
    public static function exist_name($name)
    {
    	return EquipmentCat_Access::exist_name($name);
    }
    
    /**
     * @return array
     */
    public static function list_root_entries()
    {
    	return EquipmentCat_Access::list_root_entries();
    }
    
    /**
     * @return array
     */
    public static function list_entries()
    {
		return EquipmentCat_Access::list_entries();
	}
    
}
?>