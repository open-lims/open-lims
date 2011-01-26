<?php
/**
 * @package item
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
require_once("interfaces/item.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("events/item_delete_event.class.php");
	
	require_once("access/item.access.php");	
	require_once("access/item_is_object.access.php");
	require_once("access/item_is_method.access.php");
	require_once("access/item_is_sample.access.php");
}

/**
 * Item Management Class
 * @package item
 */
class Item implements ItemInterface
{
	private $item_id;
	
	private $item;
	
	private $object_id;
	private $method_id;
	private $sample_id;
	
	/**
	 * @param integer $item_id
	 */
	function __construct($item_id)
	{
		if ($item_id == null)
		{
			$this->item_id = null;
			$this->item = new Item_Access(null);
		}
		else
		{
			$this->item_id = $item_id;
			$this->item = new Item_Access($item_id);
			
			$is_object_pk = ItemIsObject_Access::get_entry_by_item_id($item_id);
			$is_object = new ItemIsObject_Access($is_object_pk);
			$this->object_id = $is_object->get_object_id();
			
			$is_method_pk = ItemIsMethod_Access::get_entry_by_item_id($item_id);
			$is_method = new ItemIsMethod_Access($is_method_pk);
			$this->method_id = $is_method->get_method_id();
			
			$is_sample_pk = ItemIsSample_Access::get_entry_by_item_id($item_id);
			$is_sample = new ItemIsSample_Access($is_sample_pk);
			$this->sample_id = $is_sample->get_sample_id();
		}
	}
	
	function __destruct()
	{
		if ($this->item_id)
		{
			unset($this->item_id);
			unset($this->item);
			unset($this->object_id);
			unset($this->method_id);
			unset($this->sample_id);
		}
		else
		{
			unset($this->item);
		}
	}

	/**
	 * Creates a new item
	 * @return integer
	 */
	public function create()
	{
		$this->item_id = $this->item->create();
		$this->__construct($this->item_id);
		return $this->item_id;
	}
	
	/**
	 * Deletes an item
	 * @return bool
	 */
	public function delete()
	{
		if ($this->item_id and $this->item)
		{
			// Item Information
			$item_information_array = ItemInformation::list_item_information($this->item_id);
			
			if (is_array($item_information_array) and count($item_information_array) >= 1)
			{
				foreach($item_information_array as $key => $value)
				{
					$item_information = new ItemInformation($value);
					if ($item_information->unlink_item($this->item_id) == false)
					{
						return false;
					}
				}
			}
			
			// Itme Classes
			$item_class_array = ItemClass::list_classes_by_item_id($this->item_id);

			if (is_array($item_class_array) and count($item_class_array) >= 1)
			{
				foreach($item_class_array as $key => $value)
				{
					$item_class = new ItemClass($value);
					if ($item_class->unlink_item($this->item_id) == false)
					{
						return false;
					}
				}
			}			
			
			// Project Log
			// Event
			$item_delete_event = new ItemDeleteEvent($this->item_id);
			$event_handler = new EventHandler($item_delete_event);
			
			if ($event_handler->get_success() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			
			// Project Item Link
			/**
			 * @todo extrat from method due to loose dependency
			 */
			$project_item = new ProjectItem(null);
			$project_item->set_item_id($this->item_id);
			$project_item->unlink_item_full();
			
			
			// Sample Item Link
			/**
			 * @todo extrat from method due to loose dependency
			 */
			$sample_item = new SampleItem(null);
			$sample_item->set_item_id($this->item_id);
			$sample_item->unlink_item_full();

				
			// Item Löschen
			$this->unlink_object();
			$this->unlink_method();
			$this->unlink_sample();
			
			
			$success = $this->item->delete();
			$this->__destruct();
			
			return $success;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Links an object to the current item
	 * @param integer $object_id
	 * @return bool
	 */
	public function link_object($object_id)
	{
		if ($this->object_id == null)
		{
			$is_object = new ItemIsObject_Access(null);
			$is_object_pk = $is_object->create($object_id, $this->item_id);
			
			if ($is_object_pk != null)
			{
				$this->object_id = $object_id;
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
	 * Links a method to the current item
	 * @param integer $method_id
	 * @return bool
	 */
	public function link_method($method_id)
	{
		if ($this->method_id == null)
		{
			$is_method = new ItemIsMethod_Access(null);
			$is_method_pk = $is_method->create($method_id, $this->item_id);
			
			if ($is_method_pk != null)
			{
				$this->method_id = $method_id;
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
	 * Links a sample to the current item
	 * @param integer $sample_id
	 * @return bool
	 */
	public function link_sample($sample_id)
	{
		if ($this->sample_id == null)
		{
			$is_sample = new ItemIsSample_Access(null);
			$is_sample_pk = $is_sample->create($sample_id, $this->item_id);
			
			if ($is_sample_pk != null)
			{
				$this->sample_id = $sample_id;
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
	public function unlink_object()
	{
		if ($this->item_id)
		{
			$pk = ItemIsObject_Access::get_entry_by_item_id($this->item_id);
			$is_object = new ItemIsObject_Access($pk);
			return $is_object->delete();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function unlink_method()
	{
		if ($this->item_id)
		{
			$pk = ItemIsMethod_Access::get_entry_by_item_id($this->item_id);
			$is_method = new ItemIsMethod_Access($pk);
			return $is_method->delete();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function unlink_sample()
	{
		if ($this->item_id)
		{
			$pk = ItemIsSample_Access::get_entry_by_item_id($this->item_id);
			$is_sample = new ItemIsSample_Access($pk);
			return $is_sample->delete();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks if the current item is classified
	 * @return bool
	 */
	public function is_classified()
	{
		if ($this->item_id)
		{
			$item_class_array = ItemClass::list_classes_by_item_id($this->item_id);
			
			if (is_array($item_class_array) and count($item_class_array) >= 1)
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
	 * Returns the class-ids of the current item
	 * @return integer
	 */
	public function get_class_ids()
	{
		if ($this->item_id)
		{
			$item_class_array = ItemClass::list_classes_by_item_id($this->item_id);
			
			if (is_array($item_class_array) and count($item_class_array) >= 1)
			{
				return $item_class_array;
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
	 * @todo implementation
	 */
	public function get_information()
	{
		
	}
	
	/**
	 * @return string
	 */
	public function get_datetime()
	{
		if ($this->item and $this->item_id)
		{
			return $this->item->get_datetime();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
    public function get_object_id()
    {
    	if ($this->object_id)
    	{
    		return $this->object_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return integer
     */
	public function get_method_id()
	{
		if ($this->method_id)
		{
    		return $this->method_id;
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * @return integer
	 */
	public function get_sample_id()
	{
		if ($this->sample_id)
		{
    		return $this->sample_id;
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * @param integer $object_id
	 * @return integer
	 */
	public static function get_id_by_object_id($object_id)
	{
		if (is_numeric($object_id))
		{
			$is_object = new ItemIsObject_Access(null);
			$is_object_pk = ItemIsObject_Access::get_entry_by_object_id($object_id);
			$is_object = new ItemIsObject_Access($is_object_pk);
			return $is_object->get_item_id();
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @param integer $method_id
	 * @return integer
	 */
	public static function get_id_by_method_id($method_id)
	{
		if (is_numeric($method_id))
		{
			$is_method_pk = ItemIsMethod_Access::get_entry_by_method_id($method_id);
			$is_method = new ItemIsMethod_Access($is_method_pk);
			return $is_method->get_item_id();
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function get_id_by_sample_id($sample_id)
	{
		if (is_numeric($sample_id))
		{
			$is_sample = new ItemIsSample_Access(null);
			$is_sample_pk = ItemIsSample_Access::get_entry_by_sample_id($sample_id);
			$is_sample = new ItemIsSample_Access($is_sample_pk);
			return $is_sample->get_item_id();
		}
		else
		{
			return null;
		}
	}

}
?>