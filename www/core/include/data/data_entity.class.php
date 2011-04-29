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
	private $data_entity;
	
	protected $data_entity_permission;
	protected $data_entity_id;
	
	protected $read_access;
	protected $write_access;
	protected $delete_access;
	protected $control_access;
	
	protected $inherit_permission;
	
	/**
	 * @param integer $entity_id
	 */
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
		
		$this->data_entity_permission = new DataEntityPermission($this->data_entity->get_permission(), $this->data_entity->get_automatic(), $this->data_entity->get_owner_id(), $this->data_entity->get_owner_group_id());
		
		if ($this->data_entity_permission->is_access(1))
		{
			$this->read_access = true;
		}
		else
		{
			$this->read_access = false;
		}
		
		if ($this->data_entity_permission->is_access(2))
		{
			$this->write_access = true;
		}
		else
		{
			$this->write_access = false;
		}
		
		if ($this->data_entity_permission->is_access(3))
		{
			$this->delete_access = true;
		}
		else
		{
			$this->delete_access = false;
		}
		
		if ($this->data_entity_permission->is_access(4))
		{
			$this->control_access = true;
		}
		else
		{
			$this->control_access = false;
		}
		
		if ($this->data_entity->get_automatic() == true and 
			$this->data_entity->read_access == false and
			$this->data_entity->write_access == false and
			$this->data_entity->delete_access == false and 
			$this->data_entity->control_access == false)
		{
			if ($parent_folder_id = $this->get_parent_folder_id())
			{
				$folder = Folder::get_instance($parent_folder_id);
				if ($folder->get_inherit_permission() == true)
				{
					$this->inherit_permission = true;
					if ($folder->is_read_access() == true)
					{
						$this->read_access = true;
					}
					if ($folder->is_write_access() == true)
					{
						$this->write_access = true;
					}
					if ($folder->is_delete_access() == true)
					{
						$this->delete_access = true;
					}
					if ($folder->is_control_access() == true)
					{
						$this->control_access = true;
					}
				}
				elseif (is_subclass_of($folder, "Folder") == true)
				{
					$this->inherit_permission = true;
					if ($folder->is_read_access() == true)
					{
						$this->read_access = true;
					}
					if ($folder->is_write_access() == true)
					{
						$this->write_access = true;
					}
					if ($folder->is_delete_access() == true)
					{
						$this->delete_access = true;
					}
					if ($folder->is_control_access() == true)
					{
						$this->control_access = true;
					}
				}
			}
			else
			{
				$this->inherit_permission = false;
			}
		} 
	}

	/**
	 * Empty Destructor
	 */
	function __destruct()
	{
		
	}
	
	/**
	 * @return bool
	 */
	protected final function get_inherit_permission()
	{
		return $this->inherit_permission;
	}
	
	/**
	 * @return bool
	 */
	public function is_read_access()
	{
		return $this->read_access;
	}
	
	/**
	 * @return bool
	 */
	public function is_write_access()
	{
		return $this->write_access;
	}
	
	/**
	 * @return bool
	 */
	public function is_delete_access()
	{
		return $this->delete_access;
	}
	
	/**
	 * @return bool
	 */
	public function is_control_access()
	{
		return $this->control_access;
	}
	
	/**
	 * @return bool
	 */
	public function can_set_automatic()
	{
		return true;
	}
	
	/**
	 * @return bool
	 */
	public function can_set_data_entity()
	{
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function can_set_control()
	{
		global $user;
		
		if ($user->is_admin())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function can_set_remain()
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
	
	/**
	 * Returns parent folder as data entity id
	 * @return integer
	 */
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
	
	/**
	 * Returns parent folder as folder id
	 * @return integer
	 */
	public final function get_parent_folder_id()
	{
		if ($this->data_entity_id)
		{
			$parent_array = DataEntityHasDataEntity_Access::list_data_entity_pid_by_data_entity_cid($this->data_entity_id);
			if (count($parent_array) >= 1)
			{
				foreach($parent_array as $key => $value)
				{
					if (($folder_id = Folder::get_folder_id_by_data_entity_id($value)) != null)
					{
						return $folder_id;
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
	
	/**
	 * Returns a set of parent virutal folders with data entity ids
	 * @return array
	 */
	public final function get_parent_virtual_folders()
	{
		if ($this->data_entity_id)
		{
			$parent_array = DataEntityHasDataEntity_Access::list_data_entity_pid_by_data_entity_cid($this->data_entity_id);
			$result_array = array();
			
			if (count($parent_array) >= 1)
			{
				foreach($parent_array as $key => $value)
				{
					if (VirtualFolder::get_virtual_folder_id_by_data_entity_id($value) != null)
					{
						array_push($result_array, $value);
					}
				}
				return $result_array;
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
	 * Returns a set of parent virtual folders with virtual folder ids
	 * @return array
	 */
	public final function get_parent_virtual_folder_ids()
	{
		if ($this->data_entity_id)
		{
			$parent_array = DataEntityHasDataEntity_Access::list_data_entity_pid_by_data_entity_cid($this->data_entity_id);
			$result_array = array();
			
			if (count($parent_array) >= 1)
			{
				foreach($parent_array as $key => $value)
				{
					if (($virtual_folder_id = VirtualFolder::get_virtual_folder_id_by_data_entity_id($value)) != null)
					{
						array_push($result_array, $virtual_folder_id);
					}
				}
				return $result_array;
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
	 * @return array
	 */
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
	
	/**
	 * @return integer
	 */
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
		if ($this->data_entity)
		{
			return $this->data_entity->get_datetime();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public final function get_owner_id()
	{
		if ($this->data_entity)
		{
			return $this->data_entity->get_owner_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public final function get_owner_group_id()
	{
		if ($this->data_entity)
		{
			return $this->data_entity->get_owner_group_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public final function get_permission()
	{
		if ($this->data_entity)
		{
			return $this->data_entity->get_permission();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public final function get_permission_string()
	{
		if (is_object($this->data_entity_permission))
		{
			return $this->data_entity_permission->get_permission_string();
		}
		else
		{
			return "unknown";
		}
	}
	
	/**
	 * @return bool
	 */
	public final function get_automatic()
	{
		if ($this->data_entity)
		{
			return $this->data_entity->get_automatic();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public final function set_owner_id($owner_id)
	{
		if ($this->data_entity and is_numeric($owner_id))
		{
			return $this->data_entity->set_owner_id($owner_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $owner_group_id
	 * @return bool
	 */
	public final function set_owner_group_id($owner_group_id)
	{
		if ($this->data_entity and is_numeric($owner_group_id))
		{
			return $this->data_entity->set_owner_group_id($owner_group_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $permission
	 * @return bool
	 */
	public final function set_permission($permission)
	{
		if ($this->data_entity and is_numeric($permission))
		{
			return $this->data_entity->set_permission($permission);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param bool $automatic
	 * @return bool
	 */
	public final function set_automatic($automatic)
	{
		if ($this->data_entity and isset($automatic))
		{
			return $this->data_entity->set_automatic($automatic);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Sets the current data entity as a child of $data_entity_id
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
	 * Unsets the current data entity from $data_entity_id
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
	
	/**
	 * Unsets all childs of the current data entity
	 * @return bool
	 */
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
    	
    	if ($event_object instanceof UserDeleteEvent)
    	{
    		if (DataEntity_Access::set_owner_id_on_null($event_object->get_user_id()) == false)
    		{
    			return true;
    		}
    	}
    	
    	if ($event_object instanceof GroupDeleteEvent)
    	{
    		if (DataEntity_Access::set_owner_group_id_on_null($event_object->get_group_id()) == false)
    		{
    			return true;
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
    
    /**
     * @param string $type
     * @param array $type_array
     * @return string
     */
    public static function get_generic_name($type, $type_array)
    {
    	if ($type == "file")
    	{
    		return "File";
    	}
    	else
    	{
    		if (is_array($type_array) and count($type_array) == 1)
    		{
				$value_type = new ValueType($type_array[0]);
				if (($value_name = $value_type->get_name()) != null)
				{
					return "".$value_name;
				}
				else
				{
					return "Value";
				}
    		}
    		else
    		{
    			return "Value";
    		}
    	}
    }
    
    public static function get_generic_symbol($type, $id)
    {
   		if ($type == "file")
    	{
    		$file = new File($id);
    		return "<img src='".$file->get_icon()."' alt='' style='border: 0;' />";
    	}
    	else
    	{
    		return "<img src='images/icons/value.png' alt='' style='border: 0;' />";
    	}
    }
    
	public static function get_generic_link($type, $id)
	{
		if ($type == "file")
		{
			$paramquery[username] = $_GET[username];
			$paramquery[session_id] = $_GET[session_id];
			$paramquery[nav] = "data";
			$paramquery[action] = "file_detail";
			$paramquery[file_id] = $id;
			return http_build_query($paramquery, '', '&#38;');
		}
		else
		{
			$paramquery[username] = $_GET[username];
			$paramquery[session_id] = $_GET[session_id];
			$paramquery[nav] = "data";
			$paramquery[action] = "value_detail";
			$paramquery[value_id] = $id;
			return http_build_query($paramquery, '', '&#38;');
		}
	}
    
    public static function get_sql_select_array($type)
    {
    	if ($type == "file")
		{
			$select_array[name] = "".constant("FILE_VERSION_TABLE").".name";
			$select_array[type_id] = "".constant("FILE_TABLE").".id AS file_id";
			$select_array[datetime] = "".constant("FILE_VERSION_TABLE").".datetime";
			return $select_array;
		}
		else
		{
			$select_array[name] = "".constant("VALUE_TYPE_TABLE").".name";
			$select_array[type_id] = "".constant("VALUE_TABLE").".id AS value_id";
			$select_array[datetime] = "".constant("VALUE_VERSION_TABLE").".datetime";
			return $select_array;
		}
    }
    
	public static function get_sql_join($type)
	{
		if ($type == "file")
		{
			return 	"LEFT JOIN ".constant("DATA_ENTITY_IS_ITEM_TABLE")." AS deiita_a 	ON ".constant("ITEM_TABLE").".id 	= deiita_a.item_id " .
					"LEFT JOIN ".constant("FILE_TABLE")." 					ON deiita_a.data_entity_id 						= ".constant("FILE_TABLE").".data_entity_id " .
					"LEFT JOIN ".constant("FILE_VERSION_TABLE")." 			ON ".constant("FILE_TABLE").".id 				= ".constant("FILE_VERSION_TABLE").".toid ";
		}
		else
		{
			return 	"LEFT JOIN ".constant("DATA_ENTITY_IS_ITEM_TABLE")." AS deiita_b  	ON ".constant("ITEM_TABLE").".id 	= deiita_b.item_id " .
					"LEFT JOIN ".constant("VALUE_TABLE")." 					ON deiita_b.data_entity_id 						= ".constant("VALUE_TABLE").".data_entity_id " .
					"LEFT JOIN ".constant("VALUE_TYPE_TABLE")." 			ON ".constant("VALUE_TABLE").".type_id 			= ".constant("VALUE_TYPE_TABLE").".id " .		
					"LEFT JOIN ".constant("VALUE_VERSION_TABLE")." 			ON ".constant("VALUE_TABLE").".id 				= ".constant("VALUE_VERSION_TABLE").".toid ";
		}
	}
	
	public static function get_sql_where($type)
	{
		if ($type == "file")
		{
			return "(LOWER(TRIM(".constant("FILE_VERSION_TABLE").".name)) LIKE '{STRING}' AND ".constant("FILE_VERSION_TABLE").".current = 't')";
		}
		else
		{
			return "(LOWER(TRIM(".constant("VALUE_TYPE_TABLE").".name)) LIKE '{STRING}' AND ".constant("VALUE_VERSION_TABLE").".current = 't')";
		}
	}
}
?>