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
require_once("interfaces/equipment.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/equipment.access.php");
	require_once("access/equipment_is_item.access.php");
}

/**
 * Equipment Category Management Class
 * @package equipment
 */
class Equipment extends Item implements EquipmentInterface, EventListenerInterface, ItemListenerInterface
{
	private $equipment_id;
	private $equipment;
	
	/**
	 * @param integer $equipment_id
	 */
	function __construct($equipment_id)
	{
		if ($equipment_id)
		{
			$this->equipment_id = $equipment_id;
			$this->equipment = new Equipment_Access($equipment_id);
			
			$equipment_is_item = new EquipmentIsItem_Access($equipment_id);
			$this->item_id = $equipment_is_item->get_item_id();
    		parent::__construct($this->item_id);
		}
		else
		{
			$this->equipment_id = null;
			$this->equipment = new Equipment_Access(null);
			parent::__construct(null);
		}
	}
	
	public function __destruct()
   	{
   		
   	}
	
	/**
	 * Creates a new equipment
	 * @param integer $type_id
	 * @param integer $owner_id
	 * @return integer
	 */
	public function create($type_id, $owner_id)
	{
		global $transaction;
		
		if (is_numeric($type_id) and is_numeric($owner_id))
		{		
			$transaction_id = $transaction->begin();
			
			if (($equipment_id = $this->equipment->create($type_id, $owner_id)) != null)
			{
				
			// Create Item
				if (($this->item_id = parent::create()) == null)
				{
					$folder->delete(true, true);
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				
				$equipment_is_item = new EquipmentIsItem_Access(null);
				if ($equipment_is_item->create($equipment_id, $this->item_id) == false)
				{
					$folder->delete(true, true);
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					$this->__construct($equipment_id);
					return $equipment_id;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Deletes a equipment
	 * @return bool
	 */
	public function delete()
	{
   		global $transaction;
   		
   		if ($this->equipment_id and $this->equipment)
		{
			$transaction_id = $transaction->begin();
			
			$tmp_equipment_id = $this->equipment_id;
			
			// Delete Item
			if ($this->item_id) {
				if (parent::delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}else{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			$equipment_is_item = new EquipmentIsItem_Access($tmp_equipment_id);
			if ($equipment_is_item->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			if ($this->equipment->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			else
			{
				if ($transaction_id != null)
    			{
					$transaction->commit($transaction_id);
				}
				return true;
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
		if ($this->equipment_id and $this->equipment)
		{
			return $this->equipment->get_type_id();
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
		if ($this->equipment_id and $this->equipment)
		{
			return $this->equipment->get_owner_id();
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
		if ($this->equipment_id and $this->equipment)
		{
			return $this->equipment->get_datetime();
		}
		else
		{
			return null;
		}
	}
	
	public final function get_item_name()
	{
		if ($this->equipment_id and $this->equipment)
		{
			$equipment_type = new EquipmentType($this->equipment->get_type_id());
			return $equipment_type->get_name();
		}
		else
		{
			return null;
		}
	}
	
	public final function get_item_parents()
	{
		return null;
	}
	
	
	/**
	 * @return array
	 */
	public static function list_entries_by_user_id($user_id)
	{
		return Equipment_Access::list_entries_by_owner_id($user_id);
	} 
	
	/**
	 * @param integer $type_id
	 * @return array
	 */
	public static function list_entries_by_type_id($type_id)
	{
		return Equipment_Access::list_entries_by_type_id($type_id);
	}
	
	/**
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_entry_by_item_id($item_id)
	{
		return EquipmentIsItem_Access::get_entry_by_item_id($item_id);
	}
	
    /**
     * @params object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeletePrecheckEvent)
    	{
    		$equipment_array =  self::list_entries_by_user_id($event_object->get_user_id()); 
			
			if (is_array($equipment_array))
			{
				if (count($equipment_array) >= 1)
				{
					return false;
				}
			}
    	}
    	
    	if ($event_object instanceof ItemUnlinkEvent)
    	{
    		if (($equipment_id = EquipmentIsItem_Access::get_entry_by_item_id($event_object->get_item_id())) != null)
    		{
    			$equipment = new Equipment($equipment_id);
    			if ($equipment->delete() == false)
    			{
    				return false;
    			}	
    		}
    	}
    	
    	return true;
    }
    
 	/**
     * @param string $type
     * @param integer $item_id
     * @return bool
     */
    public static function is_kind_of($type, $item_id)
    {
    	if (is_numeric($item_id))
    	{
    		if (($equipment_id = EquipmentIsItem_Access::get_entry_by_item_id($item_id)) != null)
    		{
    			return true;
    		}
    		else
    		{
    			return false;
    		}
    	}
    }
    
    /**
     * @todo
     */
    public static function is_type_or_category($category_id, $type_id, $item_id)
    {
    	
    }
    
    public static function get_instance_by_item_id($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$equipment_id = EquipmentIsItem_Access::get_entry_by_item_id($item_id);
    		return new Equipment($equipment_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param string $type
     * @param array $type_array
     * @return string
     */
    public static function get_generic_name($type, $type_array)
    {
    	return "Equipment";
    }
    
    public static function get_generic_symbol($type, $id)
    {
    	return "<img src='images/icons/equipment.png' alt='' style='border: 0;' />";
    }
    
	public static function get_generic_link($type, $id)
	{
		return null;
	}
    
	public static function get_sql_select_array($type)
    {
    	$select_array[name] = "".constant("EQUIPMENT_TYPE_TABLE").".name";
		$select_array[type_id] = "".constant("EQUIPMENT_TYPE_TABLE").".id AS equipment_id";
		$select_array[datetime] = "".constant("EQUIPMENT_TABLE").".datetime";
		return $select_array;
    }
    
	public static function get_sql_join($type)
	{
		return 	"LEFT JOIN ".constant("EQUIPMENT_IS_ITEM_TABLE")." 		ON ".constant("ITEM_TABLE").".id 						= ".constant("EQUIPMENT_IS_ITEM_TABLE").".item_id " .
				"LEFT JOIN ".constant("EQUIPMENT_TABLE")." 				ON ".constant("EQUIPMENT_IS_ITEM_TABLE").".equipment_id = ".constant("EQUIPMENT_TABLE").".id " .
				"LEFT JOIN ".constant("EQUIPMENT_TYPE_TABLE")." 		ON ".constant("EQUIPMENT_TABLE").".type_id 				= ".constant("EQUIPMENT_TYPE_TABLE").".id ";
	}
	
	public static function get_sql_where($type)
	{
		return "(LOWER(TRIM(".constant("EQUIPMENT_TYPE_TABLE").".name)) LIKE '{STRING}')";
	}
	
}
?>