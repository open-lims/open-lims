<?php
/**
 * @package data
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
require_once("interfaces/object.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/object_is_item.access.php");
	require_once("access/object.access.php");
}

/**
 * Object Management Class
 * @package data
 */
class Object extends Item implements ObjectInterface, EventListenerInterface, ItemListenerInterface
{
   	protected $object_id;
   	protected $project_id; // problematic dependency
   	protected $sample_id; // problematic dependency
    
   	private $object;

	/**
	 * @param integer $object_id
	 */
   	function __construct($object_id)
   	{
   	   	if ($object_id == null)
   	   	{
   	   		$this->object_id = null;
   	   		$this->object = new Object_Access(null);
   	   		
   	   		// problematic dependency
   	   		if (is_numeric($_GET[project_id]))
   	   		{
				$this->project_id = $_GET[project_id];
			}
			
			// problematic dependency
			if (is_numeric($_GET[sample_id]))
			{
				$this->sample_id = $_GET[sample_id];
			}
			
			parent::__construct(null);	
   	   	}
   	   	else
   	   	{
   	   		$this->object_id = $object_id;
   	   		$this->object = new Object_Access($object_id);
   	   		
   	   		$object_is_item = new ObjectIsItem_Access($object_id);
   	   		$this->item_id = $object_is_item->get_item_id();
    		parent::__construct($this->item_id);
   	   		
     		
   	   		$project_item_array = ProjectItem::list_projects_by_item_id($this->item_id);
   	   		
   	   		$folder = Folder::get_instance($this->get_toid());
   	   		
   	   		// problematic dependency
			if (($project_id = $folder->is_child_of_project_folder()) != null)
			{
				$this->project_id = $project_id;
			}
			else
			{
				$this->project_id = null;
			}
			
			// problematic dependency
			if (($sample_id = $folder->is_child_of_sample_folder()) != null)
			{
				$this->sample_id = $sample_id;
			}
			else
			{
				$this->sample_id = null;
			}
   	   	}
   	}
   
   	public function __destruct()
   	{
   		
   	}
   	
    /**
     * Creates a new object
     * @param integer $folder_id
     * @param integer $file_id
     * @param integer $value_id
     * @param bool $hidden
     * @return integer
     */
   	public function create($folder_id, $file_id, $value_id, $hidden)
   	{
   		global $transaction;
   		
   		if ($this->object and is_numeric($folder_id) and ($file_id xor $value_id))
   		{
   			$transaction_id = $transaction->begin();
   			
	   		$this->object_id = $this->object->create($folder_id, $file_id, $value_id, $hidden);
	   		
	   		if ($this->object_id)
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
				
				$object_is_item = new ObjectIsItem_Access(null);
				if ($object_is_item->create($this->object_id, $this->item_id) == false)
				{
					$folder->delete(true, true);
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
		   		
		   		if ($transaction_id != null)
		   		{
					$transaction->commit($transaction_id);
				}
		   		
		   		return $this->object_id;
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
   	 * Deletes an object
   	 * @return bool
   	 */
   	public function delete()
   	{
   		global $transaction;
   		
   		if ($this->object_id and $this->object)
   		{
			$transaction_id = $transaction->begin();

			if ($this->item_id != null)
			{	
				if (parent::delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}

				$object_is_item = new ObjectIsItem_Access($this->object_id);
				if ($object_is_item->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
	   			if ($this->object->delete() == true)
	   			{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
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
   		else
   		{
   			return false;
   		}
   	}

	/**
	 * @return integer
	 */
  	public function get_file_id()
  	{
  		return $this->object->get_file_id();
  	}
  	
  	/**
  	 * @return integer
  	 */
  	public function get_value_id()
  	{
  		return $this->object->get_value_id();
  	}
  	
  	/**
  	 * @return integer
  	 */
  	public function get_toid()
  	{
  		if ($this->object_id != null and $this->object)
  		{
  			return $this->object->get_toid();
  		}
  		else
  		{
  			return null;
  		}
  	}
  	
  	
  	/**
  	 * Returns all related files of a folder
  	 * @param integer $folder_id
  	 * @return array
  	 */
   	public static function get_file_array($folder_id)
   	{
		if (is_numeric($folder_id))
		{
			$return_array = array();
	
			$object_id_array = Object_Access::list_file_id_entries_by_toid($folder_id);
			
			foreach ($object_id_array as $key => $value)
			{
				array_push($return_array, Object_Access::get_file_id_by_id($value));
			}
			
			if (is_array($return_array) and count($return_array) > 0)
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
     * Retruns all related values of a folder
     * @param integer $folder_id
     * @return array
     */  
  	public static function get_value_array($folder_id)
  	{
   		if (is_numeric($folder_id))
   		{
   			$return_array = array();
   	
   			$object_id_array = Object_Access::list_value_id_entries_by_toid($folder_id);
   			
   			foreach ($object_id_array as $key => $value)
   			{
   				array_push($return_array, Object_Access::get_value_id_by_id($value));
   			}
   			
   			if (is_array($return_array) and count($return_array) > 0)
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
  	 * Returns all related values of a folder
  	 * @param integer $folder_id
  	 * @return array
  	 */
  	public static function get_object_array($folder_id)
  	{
   		if (is_numeric($folder_id))
   		{
   			$return_array = array();
   	
   			$object_id_array = Object_Access::list_entries_by_toid($folder_id);
   			
   			foreach ($object_id_array as $key => $value)
   			{
   				array_push($return_array, $value);
   			}
   			
   			if (is_array($return_array) and count($return_array) > 0)
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
  	 * @param integer $value_id
  	 * @return integer
  	 */
  	protected static function get_id_by_value_id($value_id)
  	{
  		return Object_Access::get_id_by_value_id($value_id);
  	}
  	
  	/**
  	 * @param integer $file_id
  	 * @return integer
  	 */
  	protected static function get_id_by_file_id($file_id)
  	{
  		return Object_Access::get_id_by_file_id($file_id);
  	}
  	
    
	/**
	 * Returns the object-id of a given item-id
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_entry_by_item_id($item_id)
	{
		return ObjectIsItem_Access::get_entry_by_item_id($item_id);
	}
  	
    /**
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
   		if ($event_object instanceof ItemUnlinkEvent)
    	{
    		if (($object_id = ObjectIsItem_Access::get_entry_by_item_id($event_object->get_item_id())) != null)
    		{
    			if ($file_id = Object_Access::get_file_id_by_id($object_id))
    			{
    				$file = new File($file_id);
    				if ($file->delete() == false)
    				{
    					return false;
    				}
    			}
    			if ($value_id = Object_Access::get_value_id_by_id($object_id))
    			{
    				$value = new Value($value_id);
    				if ($value->delete() == false)
    				{
    					return false;
    				}
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
    	if ($type and is_numeric($item_id))
    	{
    		if (($object_id = ObjectIsItem_Access::get_entry_by_item_id($item_id)) != null)
    		{
    			if (Object_Access::get_file_id_by_id($object_id) and $type == "file")
    			{
    				return true;
    			}
    			if (Object_Access::get_value_id_by_id($object_id) and $type == "value")
    			{
    				return true;
    			}
    			return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
}
?>