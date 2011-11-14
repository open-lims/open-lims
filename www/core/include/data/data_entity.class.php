<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
	private static $data_entity_object_array;
	
	private $data_entity;
	
	protected $data_entity_permission;
	protected $data_entity_id;
	
	protected $read_access;
	protected $write_access;
	protected $delete_access;
	protected $control_access;
	
	protected $set_data_entity = false;
	
	protected $inherit_permission;
	
	protected $parent_folder_id;
	protected $parent_folder_object;
	
	/**
	 * @see DataEntityInterface::__construct()
	 * @param integer $entity_id
	 * @throws DataEntityNotFoundException
	 */
	function __construct($entity_id)
	{
		if (is_numeric($entity_id) and $entity_id > 0)
		{
			if (DataEntity_Access::exist_id($entity_id) == true)
			{
				$this->data_entity_id = $entity_id;
				$this->data_entity = new DataEntity_Access($entity_id);
				
				$data_entity_is_item = new DataEntityIsItem_Access($entity_id);
	   	   		$this->item_id = $data_entity_is_item->get_item_id();
	    		parent::__construct($this->item_id);
			}
			else
			{
				throw new DataEntityNotFoundException();
			}
		}
		else
		{
			$this->data_entity_id = null;
			$this->data_entity = new DataEntity_Access(null);
    		parent::__construct(null);
		}
		
		$this->data_entity_permission = new DataEntityPermission($this->data_entity->get_permission(), $this->data_entity->get_automatic(), $this->data_entity->get_owner_id(), $this->data_entity->get_owner_group_id());
		
		if (!self::$data_entity_object_array[$entity_id])
		{
			self::$data_entity_object_array[$entity_id] = $this;
		}
		
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

		$this->parent_folder_id = $this->calc_parent_folder_id();
		
		// Can create folder als methode => flag nur noch für corrupt (über parent folder object)

		if (is_a($this, "SystemFolder") == false and is_numeric($this->parent_folder_id))
		{	
			$this->parent_folder_object = Folder::get_instance($this->parent_folder_id);
			
			if ($this->parent_folder_object->get_inherit_permission() == true and is_a($this->parent_folder_object, "SystemFolder") == false)
			{				
				$this->inherit_permission = true;
				
				if ($this->parent_folder_object->is_read_access(true) == true)
				{
					$this->read_access = true;
				}
				else
				{
					$this->read_access = false;
				}
				
				if ($this->parent_folder_object->is_write_access(true) == true)
				{
					$this->write_access = true;
				}
				else
				{
					$this->write_access = false;
				}
				
				if ($this->parent_folder_object->is_delete_access(true) == true)
				{
					$this->delete_access = true;
				}
				else
				{
					$this->delete_access = false;
				}
				
				if ($this->parent_folder_object->is_control_access(true) == true)
				{
					$this->control_access = true;
				}
				else
				{
					$this->control_access = false;
				}
				
				if ($this->parent_folder_object->can_set_data_entity() == true)
				{
					$this->set_data_entity = true;
				}
			}
			else
			{
				$this->inherit_permission = false;
			}
		}
		else
		{
			$this->inherit_permission = false;
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
	protected function get_inherit_permission()
	{
		return $this->inherit_permission;
	}
	
	/**
	 * @see DataEntityInterface::is_read_access()
	 * @return bool
	 */
	public function is_read_access($inherit = false)
	{
		return $this->read_access;
	}
	
	/**
	 * @see DataEntityInterface::is_write_access()
	 * @return bool
	 */
	public function is_write_access($inherit = false)
	{
		return $this->write_access;
	}
	
	/**
	 * @see DataEntityInterface::is_delete_access()
	 * @return bool
	 */
	public function is_delete_access($inherit = false)
	{
		return $this->delete_access;
	}
	
	/**
	 * @see DataEntityInterface::is_control_access()
	 * @return bool
	 */
	public function is_control_access($inherit = false)
	{
		return $this->control_access;
	}
	
	/**
	 * @see DataEntityInterface::can_set_automatic()
	 * @return bool
	 */
	public function can_set_automatic()
	{
		return true;
	}
	
	/**
	 * @see DataEntityInterface::can_set_data_entity()
	 * @return bool
	 */
	public function can_set_data_entity()
	{
		return $this->set_data_entity;
	}
	
	/**
	 * @see DataEntityInterface::can_set_control()
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
	 * @see DataEntityInterface::can_set_remain()
	 * @return bool
	 */
	public function can_set_remain()
	{
		return true;
	}
	
	/**
	 * Create a new DataEntity
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
	 * Deletes a DataEntity
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
	 * @see DataEntityInterface::get_parent_folder()
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
	
	private function calc_parent_folder_id()
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
	 * @see DataEntityInterface::get_parent_folder_id()
	 * @return integer
	 */
	public final function get_parent_folder_id()
	{
		if (!$this->parent_folder_id)
		{
			$this->parent_folder_id = $this->calc_parent_folder_id();
		}
		return $this->parent_folder_id;
	}
	
	/**
	 * @see DataEntityInterface::get_parent_virtual_folders()
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
	 * @see DataEntityInterface::get_parent_virtual_folder_ids()
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
	 * @see DataEntityInterface::get_children()
	 * @return array
	 */
	public final function get_children()
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
	 * @see DataEntityInterface::get_data_entity_id()
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
	 * @see DataEntityInterface::get_datetime()
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
	 * @see DataEntityInterface::get_owner_id()
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
	 * @see DataEntityInterface::get_owner_group_id()
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
	 * @see DataEntityInterface::get_permission()
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
	 * @see DataEntityInterface::get_permission_string()
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
	 * @see DataEntityInterface::get_automatic()
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
	 * @see DataEntityInterface::set_owner_id()
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
	 * @see DataEntityInterface::set_owner_group_id()
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
	 * @see DataEntityInterface::set_permission()
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
	 * @see DataEntityInterface::set_automatic()
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
	 * @see DataEntityInterface::set_as_child_of()
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
	 * @see DataEntityInterface::unset_child_of()
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
	 * @see DataEntityInterface::unset_children()
	 * @return bool
	 */
	public final function unset_children()
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
	 * @see ItemListenerInterface::get_item_name()
	 * @return string
	 */
	public final function get_item_name()
	{
		if ($this->data_entity_id)
		{
			if (($file_id = File::get_file_id_by_data_entity_id($this->data_entity_id)) != null)
			{
				$file = File::get_instance($file_id);
	    		return $file->get_name();
			}
					
	    	if (($value_id = Value::get_value_id_by_data_entity_id($this->data_entity_id)) != null)
			{
				$value = Value::get_instance($value_id);
	    		return $value->get_name();
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ItemListenerInterface::get_item_parents()
	 * @return string
	 */
	public final function get_item_parents()
	{
		return null;
	}
	
	
	/**
	 * @see ItemListenerInterface::clone_item()
	 * @param integer $item_id
	 * @return integer
	 */
	public static function clone_item($item_id)
	{
		return null;
	}
	
	/**
	 * @see ItemListenerInterface::get_entry_by_item_id()
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_entry_by_item_id($item_id)
	{
		return DataEntityIsItem_Access::get_entry_by_item_id($item_id);
	}
  	
    /**
     * @see ItemListenerInterface::is_kind_of()
     * @param string $type
     * @param integer $item_id
     * @return bool
     */
    public static function is_kind_of($type, $item_id)
    {
    	if (is_numeric($item_id))
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
    			
    			if ($type == null)
    			{
    				return true;
    			}
    			else
    			{
    				return false;
    			}
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ItemListenerInterface::is_type_or_category()
     * @param integer $category_id
     * @param integer $type_id
     * @param integer $item_id
     * @return bool
     */
    public static function is_type_or_category($category_id, $type_id, $item_id)
    {
    	return false;
    }
    
    /**
     * @see ItemListenerInterface::get_instance_by_item_id()
     * @param integer $item_id
     * @return object
     */
	public static function get_instance_by_item_id($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$data_entity_id = DataEntityIsItem_Access::get_entry_by_item_id($item_id);
    		return new DataEntity($data_entity_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ItemListenerInterface::get_generic_name()
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
    
    /**
     * @see ItemListenerInterface::get_generic_symbol()
     * @param string $type
     * @param integer $id
     * @return string
     */
    public static function get_generic_symbol($type, $id)
    {
   		if ($type == "file")
    	{
    		$file = File::get_instance($id);
    		return "<img src='".$file->get_icon()."' alt='' style='border: 0;' />";
    	}
    	else
    	{
    		return "<img src='images/icons/value.png' alt='' style='border: 0;' />";
    	}
    }
    
    /**
     * @see ItemListenerInterface::get_generic_link()
     * @param string $type
     * @param integer $id
     * @return string
     */
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
    
	/**
     * @see ItemListenerInterface::get_sql_select_array()
     * @param string $type
     * @return array
     */
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
    
    /**
     * @see ItemListenerInterface::get_sql_join()
     * @param string $type
     * @return string
     */
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
	
	/**
     * @see ItemListenerInterface::get_sql_where()
     * @param string $type
     * @return string
     */
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
	
	/**
     * @see ItemListenerInterface::get_sql_fulltext_select_array()
     * @param string $type
     * @return array
     */
	public static function get_sql_fulltext_select_array($type)
	{
		if ($type == "file")
		{
			return null;
		}
		else
		{
			$select_array[name] = "".constant("VALUE_TYPE_TABLE").".name";
			$select_array[type_id] = "".constant("VALUE_TABLE").".id AS value_id";
			$select_array[datetime] = "".constant("VALUE_VERSION_TABLE").".datetime";
			$select_array[rank] = "ts_rank_cd(".constant("VALUE_VERSION_TABLE").".text_search_vector, to_tsquery('{LANGUAGE}', '{STRING}'), 32 /* rank/(rank+1) */)";
			return $select_array;
		}
	}
	
	/**
     * @see ItemListenerInterface::get_sql_fulltext_join()
     * @param string $type
     * @return string
     */
	public static function get_sql_fulltext_join($type)
	{
		if ($type == "file")
		{
			return 	null;
		}
		else
		{
			return 	"LEFT JOIN ".constant("DATA_ENTITY_IS_ITEM_TABLE")." AS deiita_b  	ON ".constant("ITEM_TABLE").".id 	= deiita_b.item_id " .
					"LEFT JOIN ".constant("VALUE_TABLE")." 					ON deiita_b.data_entity_id 						= ".constant("VALUE_TABLE").".data_entity_id " .	
					"LEFT JOIN ".constant("VALUE_VERSION_TABLE")." 			ON ".constant("VALUE_TABLE").".id 				= ".constant("VALUE_VERSION_TABLE").".toid ";
		}
	}
	
	/**
     * @see ItemListenerInterface::get_sql_fulltext_where()
     * @param string $type
     * @return string
     */
	public static function get_sql_fulltext_where($type)
	{
		if ($type == "file")
		{
			return null;
		}
		else
		{
			return "(".constant("VALUE_VERSION_TABLE").".text_search_vector @@ to_tsquery('{LANGUAGE}', '{STRING}') AND ".constant("VALUE_VERSION_TABLE").".current = 't')";
		}
	}

	/**
	 * @see EventListenerInterface::listen_events()
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
					$file = File::get_instance($file_id);
    				if ($file->delete() == false)
    				{
    					return false;
    				}
				}
				
    			if (($value_id = Value::get_value_id_by_data_entity_id($data_entity_id)) != null)
				{
					$value = Value::get_instance($value_id);
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
     * @see DataEntityInterface::get_instance()
     * @param integer $entity_id
     * @return object
     */
    public static function get_instance($entity_id)
    {
    	if (is_numeric($entity_id) and $entity_id > 0)
    	{
			if (self::$data_entity_object_array[$entity_id])
			{
				return self::$data_entity_object_array[$entity_id];
			}
			else
			{
				$data_entity = new DataEntity($entity_id);
				self::$data_entity_object_array[$entity_id] = $data_entity;
				return $data_entity;
			}
    	}
    	else
    	{
    		return new DataEntity(null);
    	}
    }
}
?>