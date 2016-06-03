<?php
/**
 * @package project
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
require_once("interfaces/project_log_has_item.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_log_has_item.access.php");
}

/**
 * Item Project Log Management Class
 * @package project
 */
class ProjectLogHasItem implements ProjectLogHasItemInterface, EventListenerInterface
{
	private $project_log_id;

	/**
	 * @see ProjectLogHasItemInterface::__construct()
	 * @param integer $item_id
	 */
    function __construct($project_log_id)
    {
    	if ($project_log_id)
    	{
    		$this->project_log_id = $project_log_id;
    	}
    	else
    	{
    		$this->project_log_id = null;
    	}
    }
    
    function __destruct()
    {
    	unset($this->project_log_id);
    }

    /**
     * @see ProjectLogHasItemInterface::link_item()
     * @param integer $item_id
     * @return bool
     * @throws ProjectLogItemLinkException
     */
    public function link_item($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$project_log_has_item = new ProjectLogHasItem_Access(null);
    		if ($project_log_has_item->create($item_id, $this->project_log_id) != null)
			{
				return true;
			}
			else
			{
				throw new ProjectLogItemLinkException();
			}
    	}
    	else
    	{
    		throw new ProjectLogItemLinkException();
    	}
    }
    
    
    /**
     * @see ProjectLogHasItemInterface::get_items_by_log_id()
     * @param integer $log_id
     * @return array
     */   
    public static function get_items_by_log_id($log_id)
    {
    	if (is_numeric($log_id))
    	{
    		$pk_array = ProjectLogHasItem_Access::list_entries_by_log_id($log_id);

    		$return_array = array();
    		
    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			foreach($pk_array as $key => $value)
    			{
    				$item_has_project_log = new ProjectLogHasItem_Access($value);
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
     * @see ProjectLogHasItemInterface::delete_by_log_id()
     * @param integer $log_id
     * @return bool
     */
    public static function delete_by_log_id($log_id)
    {
    	return ProjectLogHasItem_Access::delete_by_log_id($log_id);
    }
 
 	/**
 	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof ItemDeleteEvent)
    	{
    		if (ProjectLogHasItem_Access::delete_by_item_id($event_object->get_item_id()) == false)
			{
				return false;
			}
    	}
    	
    	return true;
    }
    
}
?>