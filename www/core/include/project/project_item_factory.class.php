<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
	private static $project_instance_array;
	
	/**
	 * @see ProjectItemFactoryInterface::create()
	 * @param integer $project_id
	 * @param integer $item_id
	 * @param integer $gid
	 * @param string $keywords
	 * @param string $description
	 * @return bool
	 */
	public static function create($project_id, $item_id, $gid, $keywords = null, $description = null, $parent_item_id = null, $status_id = null)
	{
		global $transaction;
		
		if ($transaction->is_in_transction() == true and is_numeric($project_id) and is_numeric($item_id))
		{						
			if (self::$project_instance_array[$project_id])
    		{
    			$project = self::$project_instance_array[$project_id];
    		}
    		else
    		{
    			$project = new Project($project_id);
    			self::$project_instance_array[$project_id] = $project;
    		}
			
			$project_item = new ProjectItem($project_id);
			
			$project_item->set_gid($gid);
			
			if (!$status_id)
			{
				$project_item->set_status_id($project->get_current_status_id());
			}
			else
			{
				$project_item->set_status_id($status_id);
			}

			$project_item->set_parent_item_id($parent_item_id);
			
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
    	
   		if ($event_object instanceof ItemAddHolderEvent)
    	{
    		$type = $event_object->get_type();
    		
    		if ($type == "project")
    		{
    			$project_id = $event_object->get_id();
    			$item_id = $event_object->get_item_id();
    			$gid = $event_object->get_gid();
    			
    			$transaction_id = $transaction->begin();
    			
    			if (self::create($project_id, $item_id, $gid, null, null) == false)
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
    	
    	if ($event_object instanceof ItemAddEvent)
    	{
    		$get_array = $event_object->get_get_array();
    		$post_array = $event_object->get_post_array();
    		$item_holder = $event_object->get_item_holder();
    		$item_holder_name = $event_object->get_item_holder_name();
    		
    		if ($get_array['nav'] == "project" and is_numeric($get_array['project_id']) and !$get_array['parent'])
    		{
    			$transaction_id = $transaction->begin();
    			
    			if ($item_holder == true and $item_holder_name)
    			{
    				$item_holder_class = Item::get_holder_handling_class_by_name($item_holder_name);
    				$item_holder_instance = $item_holder_class::get_instance_by_item_id($event_object->get_item_id());
    				
    				if (self::$project_instance_array[$get_array['project_id']])
    				{
    					$project = self::$project_instance_array[$get_array['project_id']];
    				}
    				else
    				{
    					$project = new Project($get_array['project_id']);
    					self::$project_instance_array[$get_array['project_id']] = $project;
    				}
    				
    				$required_sub_item_array = $project->list_required_sub_items($get_array['key']);
    				    				
    				if (is_array($required_sub_item_array) and count($required_sub_item_array) >= 1)
    				{
	    				if ($required_sub_item_array[0] == "all")
	    				{
	    					$sub_item_array = $item_holder_instance->get_item_holder_items(null);
	    					if (is_array($sub_item_array) and count($sub_item_array))
	    					{
		    					foreach($sub_item_array as $sub_item_key => $sub_item_value)
		    					{
		    						if (self::create($get_array['project_id'], $sub_item_value, null, null, null, $event_object->get_item_id()) == false)
					    			{
					    				if ($transaction_id != null)
						    			{
											$transaction->rollback($transaction_id);
										}
					    				return false;
					    			}
					    			
		    						if (DataEntity::is_kind_of("file", $sub_item_value) or DataEntity::is_kind_of("value", $sub_item_value))
									{
										$data_entity_id = DataEntity::get_entry_by_item_id($sub_item_value);
						    			$folder_id = $project->get_item_holder_value("folder_id");
						    			$parent_data_entity_id = Folder::get_data_entity_id_by_folder_id($folder_id);
						
						    			$child_data_entity = new DataEntity($data_entity_id);
						    			
						    			if ($child_data_entity->set_as_child_of($parent_data_entity_id, true, $parent_item_id) == false)
						    			{
						    				return false;
						    			}
									}
		    					}
	    					}
	    				}
	    				else
	    				{
	    					foreach($required_sub_item_array as $key => $value)
	    					{
	    						$sub_item_array = $item_holder_instance->get_item_holder_items($value['position_id']);	    						
	    						if (is_array($sub_item_array) and count($sub_item_array))
	    						{
	    							foreach($sub_item_array as $sub_item_key => $sub_item_value)
	    							{
	    								if (self::create($get_array['project_id'], $sub_item_value, null, null, null, $event_object->get_item_id(), $value['status_id']) == false)
						    			{
						    				if ($transaction_id != null)
							    			{
												$transaction->rollback($transaction_id);
											}
						    				return false;
						    			}
						    			
		    							if (DataEntity::is_kind_of("file", $sub_item_value) or DataEntity::is_kind_of("value", $sub_item_value))
										{
											$data_entity_id = DataEntity::get_entry_by_item_id($sub_item_value);
							    			$folder_id = $project->get_item_holder_value("folder_id");
							    			$parent_data_entity_id = Folder::get_data_entity_id_by_folder_id($folder_id);
							
							    			$child_data_entity = new DataEntity($data_entity_id);
							    			
							    			if ($child_data_entity->set_as_child_of($parent_data_entity_id, true, $parent_item_id) == false)
							    			{
							    				return false;
							    			}
										}
	    							}
	    						}
	    					}
	    				}
    				}
    			}
    			
    			if (self::create($get_array['project_id'], $event_object->get_item_id(), $get_array['key'], $post_array['keywords'], $post_array['description']) == false)
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
    	
   		if ($event_object instanceof ItemHolderAddEvent)
    	{
			$id_array = $event_object->get_id_array();
			$item_id = $event_object->get_item_id();
			$parent_item_id = $event_object->get_parent_item_id();
			$pos_id = $event_object->get_pos_id();
						
			if(is_array($id_array) and count($id_array) >= 1)
			{
				if (is_array($id_array['project']) and count($id_array['project']) >= 1)
				{
					foreach($id_array['project'] as $key => $value)
					{
						if (self::$project_instance_array[$value['id']])
	    				{
	    					$project = self::$project_instance_array[$value['id']];
	    				}
	    				else
	    				{
	    					$project = new Project($value['id']);
	    					self::$project_instance_array[$value['id']] = $project;
	    				}
						
						if (($item_status_id = $project->is_sub_item_required($value['pos_id'], $value['status_id'], $pos_id)) == true)
						{
							if (self::create($value['id'], $item_id, null, null, null, $parent_item_id, $item_status_id) == false)
							{
								return false;
							}
							
							if (DataEntity::is_kind_of("file", $item_id) or DataEntity::is_kind_of("value", $item_id))
							{
								$data_entity_id = DataEntity::get_entry_by_item_id($item_id);
				    			$folder_id = $project->get_item_holder_value("folder_id");
				    			$parent_data_entity_id = Folder::get_data_entity_id_by_folder_id($folder_id);
				
				    			$child_data_entity = new DataEntity($data_entity_id);
				    			
				    			if ($child_data_entity->set_as_child_of($parent_data_entity_id, true, $parent_item_id) == false)
				    			{
				    				return false;
				    			}
							}
						}
					}
				}
			}
    	}
    	    	
    	return true;
    }
    
}
?>