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
require_once("interfaces/item_has_project_log.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/item_has_project_log.access.php");
}

/**
 * Item Project Log Management Class
 * @package item
 */
class ItemHasProjectLog implements ItemHasProjectLogInterface
{
	private $primary_key;
	private $item_id;

	private $item_has_project_log;

	/**
	 * @param integer $item_id
	 */
    function __construct($item_id)
    {
    	if ($item_id)
    	{
    		$this->item_id = $item_id;
    		$this->primary_key = ItemHasProjectLog_Access::get_entry_by_item_id($item_id);
    		$this->item_has_project_log = new ItemHasProjectLog_Access($this->primary_key);
    	}
    	else
    	{
    		$this->item_id = null;
    		$this->primary_key = null;
    		$this->item_has_project_log = new ItemHasProjectLog_Access(null);
    	}
    }
    
    function __destruct()
    {
    	unset($this->primary_key);
    	unset($this->item_id);
    	unset($this->item_has_project_log);
    }
    
    /**
     * Checks if a project-log entry exsits to the current item
     * @return bool
     */
    public function exist_log_entry()
    {
    	if ($this->item_id and $this->primary_key)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * Deletes the connection of the current item
     * @return bool
     */
    public function delete()
    {
    	if ($this->item_id and $this->item_has_project_log)
    	{
    		return $this->item_has_project_log->delete();
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * Links a log entry to the current item
     * @param integer $project_log_id
     * @return bool
     */
    public function link_log($project_log_id)
    {
    	if (is_numeric($project_log_id) and $this->item_id)
    	{
    		$pk = $this->item_has_project_log->create($this->item_id, $project_log_id);
    		if ($pk)
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
     * @todo implementation
     * @todo difference to delete()?
     * @param integer $project_log_id
     * @return bool
     */
    public function unlink_log($project_log_id)
    {
    	
    }
    
    
    /**
     * @param integer $log_id
     * @return array
     */   
    public static function get_items_by_log_id($log_id)
    {
    	if (is_numeric($log_id))
    	{
    		$pk_array = ItemHasProjectLog_Access::list_entries_by_log_id($log_id);

    		$return_array = array();
    		
    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			foreach($pk_array as $key => $value)
    			{
    				$item_has_project_log = new ItemHasProjectLog_Access($value);
    				array_push($return_array, $item_has_project_log->get_item_id());
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
    
    /**
     * @return bool
     */
    public static function delete_by_log_id($log_id)
    {
    	return ItemHasProjectLog_Access::delete_by_log_id($log_id);
    }
 
}
?>