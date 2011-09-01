<?php
/**
 * @package project
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
require_once("interfaces/project_item_factory.interface.php");

/**
 * Project Item Factory Class
 * @package project
 */
class ProjectItemFactory implements ProjectItemFactoryInterface, EventListenerInterface
{
	/**
	 * @see ProjectItemFactoryInterface::create()
	 * @param integer $project_id
	 * @param integer $item_id
	 * @param integer $gid
	 * @param string $keywords
	 * @param string $description
	 * @return bool
	 */
	public static function create($project_id, $item_id, $gid, $keywords, $description)
	{
		global $transaction;
		
		if ($transaction->is_in_transction() == true)
		{
			$project = new Project($project_id);
			$project_item = new ProjectItem($project_id);
			
			$project_item->set_gid($gid) == false;
			$project_item->set_status_id($project->get_current_status_id());
			
			if ($project_item->set_item_id($item_id) == false)
			{
				return false;	
			}
			
			if ($project_item->link_item() == false)
			{
				return false;	
			}
			
			$project_item->set_item_status();
		
			if (($class_name = $project_item->is_classified()) == true)
			{
				if ($project_item->set_class($class_name) == false)
				{
					return false;
				}
			}
			
			$description_required = $project_item->is_description();
			$keywords_required = $project_item->is_keywords();
			
			if ($description_required == true xor $keywords_required == true)
			{
				if ($description_required == false and $keywords_required == true)
				{
					$project_item->set_information(null, $keywords);
				}
				else
				{
					$project_item->set_information($description, null);
				}
			}
			else
			{
				if ($description_required == true and $keywords_required == true)
				{
					$project_item->set_information($description, $keywords);
				}
			}
			
			if ($project_item->create_log_entry() == false)
			{
				return false;
			}
			else
			{
				ProjectTask::check_over_time_tasks($project_id);
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	global $transaction;
    	
    	if ($event_object instanceof ItemAddEvent)
    	{
    		$get_array = $event_object->get_get_array();
    		$post_array = $event_object->get_post_array();
    		if ($get_array[nav] == "project" and is_numeric($get_array[project_id]))
    		{
    			$transaction_id = $transaction->begin();
    			
    			if (self::create($get_array[project_id], $event_object->get_item_id(), $get_array[key], $post_array[keywords], $post_array[description]) == false)
    			{
    				if ($transaction_id != null)
	    			{
						$transaction->rollback($transaction_id);
					}
    				return false;
    			}
    			else
    			{
    				if ($transaction_id != null)
	    			{
						$transaction->commit($transaction_id);
					}
    			}
    		}
    	}
    	    	
    	return true;
    }
    
}
?>