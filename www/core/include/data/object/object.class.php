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
	require_once("access/object.access.php");
}

/**
 * Object Management Class
 * @package data
 */
class Object implements ObjectInterface
{
   	protected $object_id;
   	protected $project_id; // problematic dependency
   	protected $sample_id; // problematic dependency
    protected $item_id;
    
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
   	   	}
   	   	else
   	   	{
   	   		$this->object_id = $object_id;
   	   		$this->object = new Object_Access($object_id);
   	   		$this->item_id = Item::get_id_by_object_id($object_id);
   	   		
   	   		$project_item_array = ProjectItem::list_projects_by_item_id($this->item_id);
   	   		
   	   		$folder = new Folder($this->get_toid());
   	   		
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
   	
   	function __destruct()
   	{
   		unset($this->object_id);
   		unset($this->item_id);
   		unset($this->object);
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
		   		$item = new Item(null);
				$this->item_id = $item->create();
				$item->link_object($this->object_id);
		   		
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
		   		$item = new Item($this->item_id);
	
		   		if ($item->delete() == true)
		   		{
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
		   			if ($transaction_id != null)
		   			{
						$transaction->rollback($transaction_id);
					}
		   			return false;
		   		}
			}
			else
			{
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
  	public function get_item_id()
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
  	 
}
?>