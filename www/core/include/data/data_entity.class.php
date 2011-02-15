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
require_once("interfaces/data_entity.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/data_entity.access.php");
	require_once("access/data_entity_is_item.access.php");
	require_once("access/data_entity_has_data_entity.access.php");
}

/**
 * Data Entity Class
 * @package data
 */
class DataEntity extends Item implements DataEntityInterface, EventListenerInterface, ItemListenerInterface
{
	protected $data_entity_id;
	private $data_entity;
	
	function __construct($entity_id)
	{
		if (is_numeric($entity_id) and $entity_id > 0)
		{
			$this->data_entity_id = $entity_id;
			$this->data_entity = new DataEntity_Access($entity_id);
			
			$data_entity_is_item = new DataEntityIsItem_Access($entity_id);
   	   		$this->item_id = $data_entity_is_item->get_item_id();
    		parent::__construct($this->item_id);
		}
		else
		{
			$this->data_entity_id = null;
			$this->data_entity = new DataEntity_Access(null);
    		parent::__construct(null);
		}
		
		
		// Create Item Object with Item ID
		
		// Überarbeiten (aus Folder)
		/*
		// [OLD] => To Data Entity
		$object_permission = new ObjectPermission($this->folder->get_permission(), $this->folder->get_automatic(), $this->folder->get_owner_id(), $this->folder->get_owner_group_id());
		$object_permission->set_folder_flag($this->folder->get_flag());
		
		if (($project_id = $this->is_child_of_project_folder()) != null)
		{
			$object_permission->set_project_id($project_id);
		}
		
		if (($sample_id = $this->is_child_of_sample_folder()) != null)
		{
			$object_permission->set_sample_id($sample_id);
		}
		
		if ($object_permission->is_access(1))
		{
			$this->read_access = true;
		}
		else
		{
			$this->read_access = false;
		}
		
		if ($object_permission->is_access(2))
		{
			$this->write_access = true;
		}
		else
		{
			$this->write_access = false;
		}
		
		if ($object_permission->is_access(3))
		{
			$this->delete_access = true;
		}
		else
		{
			$this->delete_access = false;
		}
		
		if ($object_permission->is_access(4))
		{
			$this->control_access = true;
		}
		else
		{
			$this->control_access = false;
		}
		*/
		// [DLO]	
	}
	
	/**
	 * @todo implementation
	 */
	function __destruct()
	{
		
	}
	
	/**
	 * @todo implementation
	 */
	public function is_read_access()
	{
		return true;
	}
	
	/**
	 * @todo implementation
	 */
	public function is_write_access()
	{
		return true;
	}
	
	/**
	 * @todo implementation
	 */
	public function is_delete_access()
	{
		return true;
	}
	
	/**
	 * @todo implementation
	 */
	public function is_control_access()
	{
		return true;
	}
	
	/**
	 * @todo implementation
	 */
	public function can_set_automatic()
	{
		return true;
	}
	
	/**
	 * @todo implementation
	 */
	public function can_set_data_entity()
	{
		return true;
	}
	
	/**
	 * @todo implementation
	 */
	public function can_set_control()
	{
		return true;
	}
	
	/**
	 * @todo implementation
	 */
	public function cat_set_remain()
	{
		return true;
	}
	
	/**
	 * @param integer $owner_id
	 * @param integer $owner_group_id
	 * @return integer
	 */
	protected function create($owner_id, $owner_group_id)
	{
		global $transaction;
		
		if (is_numeric($owner_id))
		{
			$transaction_id = $transaction->begin();
			
			$data_entity_id = $this->data_entity->create($owner_id, $owner_group_id);
			
			if ($data_entity_id)
	   		{
				if (($item_id = parent::create()) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				
				$data_entity_is_item = new DataEntityIsItem_Access(null);
				if ($data_entity_is_item->create($data_entity_id, $item_id) == false)
				{
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
		   		
				$this->data_entity_id = $data_entity_id;
				$this->data_entity = new DataEntity_Access($data_entity_id);
	   	   		$this->item_id = $item_id;
	    		parent::__construct($item_id);
				
		   		return $data_entity_id;
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
	 * @return bool
	 */
	protected function delete()
	{
		global $transaction;
		
		if ($this->data_entity_id)
		{
			$transaction_id = $transaction->begin();

			if (DataEntityHasDataEntity_Access::delete_by_data_entity_cid($this->data_entity_id) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			$data_entity_is_item = new DataEntityIsItem_Access($this->data_entity_id);
			if ($data_entity_is_item->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			if ($this->data_entity->delete() == true)
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
	
	public final function get_parent_folder()
	{
		if ($this->data_entity_id)
		{
			$parent_array = DataEntityHasDataEntity_Access::list_data_entity_pid_by_data_entity_cid($this->data_entity_id);
			if (count($parent_array) >= 1)
			{
				foreach($parent_array as $key => $value)
				{
					if (Folder::get_folder_id_by_data_entity_id($value) != null)
					{
						return $value;
					}
				}
				return null;
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
	
	public final function get_parent_virtual_folders()
	{
		if ($this->data_entity_id)
		{
			$parent_array = DataEntityHasDataEntity_Access::list_data_entity_pid_by_data_entity_cid($this->data_entity_id);
			if (count($parent_array) >= 1)
			{
				foreach($parent_array as $key => $value)
				{
					if (VirtualFolder::get_virtual_folder_id_by_data_entity_id($value) != null)
					{
						array_push($parent_array, $value);
					}
				}
				return $parent_array;
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
	
	
	public final function get_childs()
	{
		if ($this->data_entity_id)
		{
			return DataEntityHasDataEntity_Access::list_data_entity_cid_by_data_entity_pid($this->data_entity_id);
		}
		else
		{
			return null;
		}
	}
	
	public final function get_data_entity_id()
	{
		if ($this->data_entity_id)
		{
			return $this->data_entity_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public final function get_datetime()
	{
		
	}
	
	/**
	 * @return integer
	 */
	public final function get_owner_id()
	{
		
	}
	
	/**
	 * @return integer
	 */
	public final function get_owner_group_id()
	{
		
	}
	
	/**
	 * @return integer
	 */
	public final function get_permission()
	{
		
	}
	
	/**
	 * @return string
	 */
	public final function get_permission_string()
	{
		return "automatic";	
	}
	
	/**
	 * @return bool
	 */
	public final function get_automatic()
	{
		
	}
	
	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public final function set_owner_id($owner_id)
	{
		
	}
	
	/**
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public final function set_owner_group_id($owner_group_id)
	{
		
	}
	
	/**
	 * @param integer $permission
	 * @return bool
	 */
	public final function set_permission($permission)
	{
		
	}
	
	/**
	 * @param bool $automatic
	 * @return bool
	 */
	public final function set_automatic($automatic)
	{
		
	}
	
	/**
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public final function set_as_child_of($data_entity_id)
	{
		if ($this->data_entity_id and $data_entity_id)
		{
			if (!in_array($this->data_entity_id, DataEntityHasDataEntity_Access::list_data_entity_cid_by_data_entity_pid($data_entity_id)))
			{
				$data_entity_has_data_entity = new DataEntityHasDataEntity_Access(null, null);
				if ($data_entity_has_data_entity->create($data_entity_id, $this->data_entity_id) == true)
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
				return true;
			}
		}
		else
		{
			return false;	
		}
	}
	
	/**
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public final function unset_child_of($data_entity_id)
	{
		if ($this->data_entity_id and $data_entity_id)
		{
			$data_entity_has_data_entity = new DataEntityHasDataEntity_Access($data_entity_id, $this->data_entity_id);
			if ($data_entity_has_data_entity->delete() == true)
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
	
	public final function unset_childs()
	{
		if ($this->data_entity_id)
		{
			$parent_array = DataEntityHasDataEntity_Access::list_data_entity_cid_by_data_entity_pid($this->data_entity_id);
			if (is_array($parent_array) and count($parent_array) >= 1)
			{
				foreach($parent_array as $key => $value)
				{
					$data_entity_has_data_entity = new DataEntityHasDataEntity_Access($this->data_entity_id, $value);
					if ($data_entity_has_data_entity->delete() == false)
					{
						return false;
					}
				}
				return true;
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
	
	
	/**
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_entry_by_item_id($item_id)
	{
		return DataEntityIsItem_Access::get_entry_by_item_id($item_id);
	}
	
	/**
     * @param object $event_object
     * @return bool
     * @todo
     */
    public static function listen_events($event_object)
    {
   		if ($event_object instanceof ItemUnlinkEvent)
    	{
    		if (($data_entity_id = DataEntityIsItem_Access::get_entry_by_item_id($event_object->get_item_id())) != null)
    		{
    			if (($file_id = File::get_file_id_by_data_entity_id($data_entity_id)) != null)
				{
					$file = new File($file_id);
    				if ($file->delete() == false)
    				{
    					return false;
    				}
				}
				
    			if (($value_id = Value::get_value_id_by_data_entity_id($data_entity_id)) != null)
				{
					$value = new Value($value_id);
    				if ($value->delete() == false)
    				{
    					return false;
    				}
				}
    		}
    	}
    	
    	// User Delete Event, set owner id On Null
    	
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
    		if (($data_entity_id = DataEntityIsItem_Access::get_entry_by_item_id($item_id)) != null)
    		{
    			if ($type == "file")
    			{
	    			if (File::get_file_id_by_data_entity_id($data_entity_id) != null)
					{
						return true;
					}
    			}
    			
    			if ($type == "value")
    			{
    				if (Value::get_value_id_by_data_entity_id($data_entity_id) != null)
					{
						return true;
					}
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