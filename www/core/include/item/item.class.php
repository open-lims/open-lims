<?php
/**
 * @package item
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
	require_once("access/item_concretion.access.php");
	require_once("access/item_holder.access.php");
}

/**
 * Item Management Class
 * @package item
 */
class Item implements ItemInterface, EventListenerInterface
{
	protected $item_id;
	private $item;
	
	/**
	 * @param integer $item_id
	 */
	protected function __construct($item_id)
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
		}
	}
	
	protected function __destruct()
	{
		if ($this->item_id)
		{
			unset($this->item_id);
			unset($this->item);
		}
		else
		{
			unset($this->item);
		}
	}

	/**
	 * Creates a new item
	 * @return integer
	 * @throws ItemCreateFailedException
	 */
	protected function create()
	{
		if (($this->item_id = $this->item->create()) != null)
		{
			return $this->item_id;
		}
		else
		{
			throw new ItemCreateFailedException();
		}
	}
	
	/**
	 * Deletes an item
	 * @return bool
	 * @throws ItemDeleteInformationException
	 * @throws ItemDeleteClassException
	 * @throws ItemDeleteEventFailedException
	 * @throws ItemDeleteFailedException
	 * @throws ItemNoInstanceException
	 */
	protected function delete()
	{
		global $transaction;
		
		if ($this->item_id and $this->item)
		{
			$transaction_id = $transaction->begin();
				
			try
			{
				$item_information_array = ItemInformation::list_item_information($this->item_id);
				
				if (is_array($item_information_array) and count($item_information_array) >= 1)
				{
					foreach($item_information_array as $key => $value)
					{
						$item_information = new ItemInformation($value);
						if ($item_information->unlink_item($this->item_id) == false)
						{
							throw new ItemDeleteInformationException();
						}
					}
				}
				
				// Item Classes
				$item_class_array = ItemClass::list_classes_by_item_id($this->item_id);
	
				if (is_array($item_class_array) and count($item_class_array) >= 1)
				{
					foreach($item_class_array as $key => $value)
					{
						$item_class = new ItemClass($value);
						if ($item_class->unlink_item($this->item_id) == false)
						{
							throw new ItemDeleteClassException();
						}
					}
				}			
	
				// Event
	  			$item_delete_event = new ItemDeleteEvent($this->item_id);
				$event_handler = new EventHandler($item_delete_event);
					
				if ($event_handler->get_success() == false)
				{
					throw new ItemDeleteEventFailedException();
				}
	
				if($this->item->delete() == false)
				{
					throw new ItemDeleteFailedException();
				}
			}
			catch(BaseException $e)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw $e;
			}
			
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			return true;
		}
		else
		{
			throw new ItemNoInstanceException();
		}
	}
	
	/**
	 * @see ItemInterface::get_item_id()
	 * @return integer
	 */
	public final function get_item_id()
	{
		if ($this->item_id)
		{
			return $this->item_id;
		}
		else
		{
			return null;
		}
	}
		
	/**
	 * @see ItemInterface::is_classified()
	 * @return bool
	 */
	public final function is_classified()
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
	 * @see ItemInterface::get_class_ids()
	 * @return integer
	 */
	public final function get_class_ids()
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
	 * @see ItemInterface::get_information()
	 * @todo implementation
	 * @return string
	 */
	public final function get_information()
	{
		
	}
	
	/**
	 * @see ItemInterface::get_datetime()
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
	 * @see ItemInterface::register_type()
	 * @param string $type
	 * @param string $handling_class
	 * @param integer $include_id
	 * @return bool
	 */
	public static function register_type($type, $handling_class, $include_id)
	{
		$item_concretion = new ItemConcretion_Access(null);
		if ($item_concretion->create($type, $handling_class, $include_id) != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ItemInterface::delete_type_by_include_id()
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_type_by_include_id($include_id)
	{
		return ItemConcretion_Access::delete_by_include_id($include_id);
	}
	
	/**
	 * @see ItemInterface::list_types()
	 * @return array
	 */
	public static function list_types()
	{
		return ItemConcretion_Access::list_entries();
	}
	
	/**
	 * @see ItemInterface::list_holders()
	 * @return array
	 */
	public static function list_holders()
	{
		return ItemHolder_Access::list_entries();
	}
	
	/**
	 * @see ItemInterface::get_handling_class_by_type()
	 * @param string $type
	 * @return string
	 */
	public static function get_handling_class_by_type($type)
	{
		return ItemConcretion_Access::get_handling_class_by_type($type);
	}
	
	/**
	 * @see ItemInterface::register_holder()
	 * @param string $name
	 * @param string $handling_class
	 * @param integer $include_id
	 * @return bool
	 */
	public static function register_holder($name, $handling_class, $include_id)
	{
		$item_holder = new ItemHolder_Access(null);
		if ($item_holder->create($name, $handling_class, $include_id) != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ItemInterface::delete_holder_by_include_id()
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_holder_by_include_id($include_id)
	{
		return ItemHolder_Access::delete_by_include_id($include_id);
	}
	
	/**
	 * @see ItemInterface::get_holder_handling_class_by_name()
	 * @param string $name
	 * @return string
	 */
	public static function get_holder_handling_class_by_name($name)
	{
		return ItemHolder_Access::get_handling_class_by_name($name);
	}
	
    /**
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {    	
    	if ($event_object instanceof IncludeDeleteEvent)
    	{
			if (ItemConcretion_Access::delete_by_include_id($event_object->get_include_id()) == false)
			{
				return false;
			}
    		if (ItemHolder_Access::delete_by_include_id($event_object->get_include_id()) == false)
			{
				return false;
			}
    	}
    	
    	return true;
    }
	
}
?>