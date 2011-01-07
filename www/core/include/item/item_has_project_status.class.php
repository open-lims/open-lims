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
require_once("interfaces/item_has_project_status.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/item_has_project_status.access.php");
}

/**
 * Item Project Status Management Class
 * @package item
 */
class ItemHasProjectStatus implements ItemHasProjectStatusInterface
{
	private $primary_key;

	private $item_id;
	private $status_id;
	
	private $item_has_project_status_access;
	
	/**
	 * @param integer $item_id
	 * @param integer $status_id
	 */
    function __construct($item_id, $status_id)
    {
    	if (is_numeric($item_id) and is_numeric($status_id))
    	{
    		$primary_key = ItemHasProjectStatus_Access::get_entry_by_item_id($item_id);
    		$this->item_has_project_status_access = new ItemHasProjectStatus_Access($primary_key);
	    		
    		if ($this->item_has_project_status_access->get_project_status_id() == $status_id)
    		{
	    		if ($primary_key != null)
	    		{
	    			$this->primary_key = $primary_key;
	    			$this->item_id = $item_id;
	    			$this->status_id = $status_id;
	    		}
	    		else
	    		{
	    			$this->primary_key = null;
	    			$this->item_id = null;
	    			$this->status_id = null;
	    		}
    		}
    		else
    		{
    			$this->item_has_project_status_access = new ItemHasProjectStatus_Access(null);
	    		$this->primary_key = null;
	    		$this->item_id = null;
	    		$this->status_id = null;
    		}
    	}
    	else
    	{
    		$this->item_has_project_status_access = new ItemHasProjectStatus_Access(null);
    		$this->primary_key = null;
    		$this->item_id = null;
    		$this->status_id = null;
    	}
    }
    
    function __destruct()
    {
    	unset($this->primary_key);
    	unset($this->item_id);
    	unset($this->sample_id);
    }
    
    /**
     * Create a new item - project-status connection
     * @param integer $item_id
     * @param integer $status_id
     * @return integer
     */
   	public function create($item_id, $status_id)
   	{
    	if (is_numeric($item_id) and is_numeric($status_id))
    	{
    		$primary_key = $this->item_has_project_status_access->create($item_id, $status_id);
    		$this->__construct($item_id, $status_id);
    		return $primary_key;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Delete the connection
     * @return bool
     */
    public function delete()
    {
    	if ($this->item_has_project_status_access and $this->primary_key)
    	{
    		return $this->item_has_project_status_access->delete();
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @return bool
     */
    public function is_object()
    {
    	if ($this->item_id and $this->status_id)
    	{
    		$item = new Item($this->item_id);
    		if ($item->get_object_id() != null)
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
     * @return bool
     */
    public function is_method()
    {
    	if ($this->item_id and $this->status_id)
    	{
    		$item = new Item($this->item_id);
    		if ($item->get_method_id() != null)
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
     * @return bool
     */
    public function is_sample()
    {
    	if ($this->item_id and $this->status_id)
    	{
    		$item = new Item($this->item_id);
    		if ($item->get_sample_id() != null)
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
     * @return integer
     */
    public function get_gid()
    {
    	if ($this->item_has_project_status_access)
    	{
    		return $this->item_has_project_status_access->get_gid();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param integer $gid
     * @return bool
     */
    public function set_gid($gid)
    {
    	if ($this->item_has_project_status_access)
    	{
    		return $this->item_has_project_status_access->set_gid($gid);
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
    public static function get_status_id_by_item_id($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$pk = ItemHasProjectStatus_Access::get_entry_by_item_id($item_id);
    		if (is_numeric($pk))
    		{
    			$item_has_project_status_access = new ItemHasProjectStatus_Access($pk);
    			return $item_has_project_status_access->get_project_status_id();
    		}
    		else
    		{
    			return null;
    		}
    	}
    	else{
    		return null;
    	}
    }
    
}
?>