<?php
/**
 * @package item
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
 * 
 */
require_once("interfaces/item_class.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/item_class.access.php");
	require_once("access/item_has_item_class.access.php");
}

/**
 * Item Class Management Class
 * @package item
 */
class ItemClass implements ItemClassInterface
{
	private $class_id;
	
	private $item_class;
	private $item_has_item_class;

	/**
	 * @see ItemClassInterface::__construct()
	 * @param integer $class_id
	 */
    function __construct($class_id)
    {
    	if ($class_id != null)
    	{
    		$this->class_id = $class_id;
    		$this->item_class = new ItemClass_Access($class_id);
    		$this->item_has_item_class = new ItemHasItemClass_Access(null);
    	}
    	else
    	{
    		$this->class_id = null;
    		$this->item_class = new ItemClass_Access(null);
    		$this->item_has_item_class = new ItemHasItemClass_Access(null);
    	}
    }
    
    function __destruct()
    {
    	unset($this->class_id);
    	unset($this->item_class);
    	unset($this->item_has_item_class);
    }
    
    /**
     * @see ItemClassInterface::create()
     * @param string $name
     * @param integer $owner_id
     * @return integer
     */
    public function create($name, $owner_id)
    {
    	$class_id = $this->item_class->create($name, $owner_id);
    	if (is_numeric($class_id))
    	{
    		$this->__construct($class_id);
    		return $class_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ItemClassInterface::delete()
     * @return bool
     */
	public function delete()
	{
		global $transaction;
		
    	if ($this->class_id and $this->item_class and $this->item_has_item_class)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$item_link_array = $this->list_items();
    		
    		if (is_array($item_link_array) and count($item_link_array) >= 1)
    		{
    			foreach($item_link_array as $key => $value)
    			{
    				$item_has_item_class_access = new ItemHasItemClass_Access($value);
    				if ($item_has_item_class_access->delete() == false)
    				{
    					if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
    				}
    			}
    		}
    		
			$item_information_array = ItemInformation::list_class_information($this->class_id);
			
			if (is_array($item_information_array) and count($item_information_array) >= 1)
			{
				foreach($item_information_array as $key => $value)
				{
					$item_information = new ItemInformation($value);
					if ($item_information->unlink_class($this->class_id) == false)
					{
    					if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
    				}
				}
			}
    		
    		if ($this->item_class->delete() == true)
    		{
    			if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				$this->__destruct();
    		
    			return true;
    		}
    		else
    		{
    			if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ItemClassInterface::link_item()
     * @param integer $item_id
     * @return bool
     */
    public function link_item($item_id)
    {
    	if ($this->class_id and $this->item_has_item_class and is_numeric($item_id))
    	{
    		$primary_key = $this->item_has_item_class->create($item_id, $this->class_id);
    		if (is_numeric($primary_key))
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
     * @see ItemClassInterface::unlink_item()
     * @param integer $item_id
     * @return bool
     */   
    public function unlink_item($item_id)
    {
    	if ($this->class_id and $this->item_has_item_class and is_numeric($item_id))
    	{
    		$primary_key = ItemHasItemClass_Access::get_entry_by_item_id_and_item_class_id($item_id, $this->class_id);

    		$item_has_item_class_access = new ItemHasItemClass_Access($primary_key);
    		$success = $item_has_item_class_access->delete();
    		
    		if ($success == true)
    		{
    			$item_array = ItemHasItemClass_Access::list_entries_by_item_class_id($this->class_id);
    			if (!is_array($item_array) or count($item_array) <= 0)
    			{
    				return $this->delete();
    			}
    			else
    			{
    				return true;
    			}
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
     * @see ItemClassInterface::list_items()
     * @return array
     */
    public function list_items()
    {
    	if ($this->class_id and $this->item_has_item_class)
    	{
    		return ItemHasItemClass_Access::list_entries_by_item_class_id($this->class_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ItemClassInterface::get_name()
     * @return string
     */
    public function get_name()
    {
    	return $this->item_class->get_name();
    }
    
    /**
     * @see ItemClassInterface::get_owner_id()
     * @return integer
     */
    public function get_owner_id()
    {
    	return $this->item_class->get_owner_id();
    }
    
    /**
     * @see ItemClassInterface::get_datetime()
     * @return string
     */
    public function get_datetime()
    {
    	return $this->item_class->get_datetime();
    }
    
    /**
     * @see ItemClassInterface::get_colour()
     * @return string
     */
    public function get_colour()
    {
    	return $this->item_class->get_colour();
    }
    
    /**
     * @see ItemClassInterface::set_name()
     * @param string $name
     * @return bool
     */
    public function set_name($name)
    {
    	return $this->item_class->set_name($name);
    }
    
    /**
     * @see ItemClassInterface::set_owner_id()
     * @param integer $owner_id
     * @return bool
     */
    public function set_owner_id($owner_id)
    {
    	return $this->item_class->set_owner_id($owner_id);
    }
    
    /**
     * @see ItemClassInterface::set_colour()
     * @param string $colour
     * @return bool
     */
    public function set_colour($colour)
    {
    	return $this->item_class->set_colour($colour);
    }
    
    
    /**
     * @see ItemClassInterface::list_classes_by_item_id()
     * @param integer $item_id
     * @return array
     */
    public static function list_classes_by_item_id($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$pk_array = ItemHasItemClass_Access::list_entries_by_item_id($item_id);
    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			$return_array = array();
    			foreach($pk_array as $key => $value)
    			{
    				$item_has_item_class = new ItemHasItemClass_Access($value);
    				array_push($return_array, $item_has_item_class->get_item_class_id());
    			}
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
    
}
?>