<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
require_once("interfaces/sample_item_factory.interface.php");

/**
 * Sample Item Factory Class
 * @package sample
 */
class SampleItemFactory implements SampleItemFactoryInterface, EventListenerInterface
{
	private static $sample_instance_array;
	
	/**
	 * @see SampleItemFactoryInterface::create()
	 * @param integer $sample_id
	 * @param integer $item_id
	 * @param integer $gid
	 * @param string $keywords
	 * @param string $description
	 * @param bool $parent
	 * @return bool
	 */
	public static function create($sample_id, $item_id, $gid, $keywords = null, $description = null, $parent_item_id = null, $parent_sample = false, $parent_is_parent_sample = false)
	{
		global $transaction;
		
		if ($transaction->is_in_transction() == true)
		{
			$sample = new Sample($sample_id);
			$sample_item = new SampleItem($sample_id);
			
			$sample_item->set_gid($gid);
			$sample_item->set_parent($parent_sample); // For parent sample only
			$sample_item->set_parent_item_id($parent_item_id);
			
			if ($sample_item->set_item_id($item_id) == false)
			{
				return false;	
			}
			
			if ($sample_item->link_item() == false)
			{
				return false;	
			}
		
			if (($class_name = $sample_item->is_classified()) == true)
			{
				if ($sample_item->set_class($class_name) == false)
				{
					return false;
				}
			}
			
			$description_required = $sample_item->is_description();
			$keywords_required = $sample_item->is_keywords();
			
			if ($description_required == true xor $keywords_required == true)
			{
				if ($description_required == false and $keywords_required == true)
				{
					$sample_item->set_information(null, $keywords);
				}
				else
				{
					$sample_item->set_information($description, null);
				}
			}
			else
			{
				if ($description_required == true and $keywords_required == true)
				{
					$sample_item->set_information($description, $keywords);
				}
			}
			
			$item_holder_type_array = Item::list_holders();
			$item_holder_id_array = array();
			
			if (is_array($item_holder_type_array) and count($item_holder_type_array) >= 1)
			{
				foreach ($item_holder_type_array as $key => $value)
				{
					$item_holder_id_array[$key] = $value::list_item_holders_by_item_id($sample->get_item_id());;
					
					if ($key == "sample" and $parent_is_parent_sample == true)
					{
						$item_holder_id_array[$key] = array_merge($item_holder_id_array[$key], Sample_Wrapper::get_sample_id_and_gid_by_parent_sample_id($sample_id));
					}
				}
			}
						
			$item_holder_add_event = new ItemHolderAddEvent($item_holder_id_array, $sample->get_item_id(), $item_id, $gid);
			$event_handler = new EventHandler($item_holder_add_event);
			
			if ($event_handler->get_success() == false)
			{
				return false;
			}

			return true;
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
    		
    		if ($type == "sample")
    		{
    			$sample_id = $event_object->get_id();
    			$item_id = $event_object->get_item_id();
    			$gid = $event_object->get_gid();
    			
    			$transaction_id = $transaction->begin();
    			
    			if (self::create($sample_id, $item_id, $gid, null, null) == false)
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
    		
    		if ($get_array['nav'] == "sample" and is_numeric($get_array['sample_id']) and !$get_array['parent'] and !$get_array['parent_key'])
    		{
    			$transaction_id = $transaction->begin();
    			
    			if ($get_array['parent_sample'] == "1")
    			{
    				$parent = true;
    			}
    			else
    			{
    				$parent = false;
    			}
    			
    			if ($item_holder == true and $item_holder_name)
    			{
    				$item_holder_class = Item::get_holder_handling_class_by_name($item_holder_name);
    				$item_holder_instance = $item_holder_class::get_instance_by_item_id($event_object->get_item_id());
    				
    				if (self::$sample_instance_array[$get_array['sample_id']])
    				{
    					$sample = self::$sample_instance_array[$get_array['sample_id']];
    				}
    				else
    				{
    					$sample = new Sample($get_array['sample_id']);
    					self::$sample_instance_array[$get_array['sample_id']] = $sample;
    				}
    				
    				$required_sub_item_array = $sample->list_required_sub_items($get_array['key']);
    				    				
    				if (is_array($required_sub_item_array) and count($required_sub_item_array) >= 1)
    				{
	    				if ($required_sub_item_array[0] == "all")
	    				{
	    					$sub_item_array = $item_holder_instance->get_item_holder_items(null);
	    					if (is_array($sub_item_array) and count($sub_item_array))
	    					{
		    					foreach($sub_item_array as $sub_item_key => $sub_item_value)
		    					{
		    						if (self::create($get_array['sample_id'], $sub_item_value, null, null, null, $event_object->get_item_id()) == false)
					    			{
					    				if ($transaction_id != null)
						    			{
											$transaction->rollback($transaction_id);
										}
					    				return false;
					    			}
					    			
		    						if (DataEntity::is_kind_of("file", $sub_item_value) or DataEntity::is_kind_of("value", $sub_item_value) or DataEntity::is_kind_of("parameter", $sub_item_value))
									{
										$data_entity_id = DataEntity::get_entry_by_item_id($sub_item_value);
						    			$folder_id = $sample->get_item_holder_value("folder_id");
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
	    						$sub_item_array = $item_holder_instance->get_item_holder_items($value);	    						
	    						if (is_array($sub_item_array) and count($sub_item_array))
	    						{
	    							foreach($sub_item_array as $sub_item_key => $sub_item_value)
	    							{
	    								if (self::create($get_array['sample_id'], $sub_item_value, null, null, null, $event_object->get_item_id()) == false)
						    			{
						    				if ($transaction_id != null)
							    			{
												$transaction->rollback($transaction_id);
											}
						    				return false;
						    			}
						    			
		    							if (DataEntity::is_kind_of("file", $sub_item_value) or DataEntity::is_kind_of("value", $sub_item_value) or DataEntity::is_kind_of("parameter", $sub_item_value))
										{
											$data_entity_id = DataEntity::get_entry_by_item_id($sub_item_value);
							    			$folder_id = $sample->get_item_holder_value("folder_id");
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
    			
    			if (self::create($get_array['sample_id'], $event_object->get_item_id(), $get_array['key'], $post_array['keywords'], $post_array['description'], null, $parent) == false)
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
    		
    		if(($get_array['parent'] == "sample" or $get_array['parent'] == "parentsample") and is_numeric($get_array['key']) and is_numeric($get_array['parent_key']))
    		{
    			$transaction_id = $transaction->begin();
    			
    			if (is_numeric($get_array['parent_id']))
    			{
    				if ($get_array['parent'] == "parentsample")
    				{
    					$parent_sample = true;
    				}
    				else
    				{
    					$parent_sample = false;
    				}
    				
	    			if (self::create($get_array['parent_id'], $event_object->get_item_id(), $get_array['key'], null, null, null, false, $parent_sample) == false)
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
    			elseif($get_array['nav'])
    			{
    				$handling_class = Item::get_holder_handling_class_by_name($get_array['nav']);
    			
	    			if (class_exists($handling_class))
	    			{
	    				$item_holder = new $handling_class($get_array[$get_array['nav'].'_id']);
	    				$parent_id_array = $item_holder->get_item_add_information($get_array['parent_key']);
	    				
	    				if (is_array($parent_id_array['fulfilled']) and count($parent_id_array['fulfilled']) >= 1)
	    				{
	    					foreach ($parent_id_array['fulfilled'] as $key => $value)
	    					{
	    						if (self::create($value['id'], $event_object->get_item_id(), $get_array['key']) == false)
				    			{
				    				if ($transaction_id != null)
					    			{
										$transaction->rollback($transaction_id);
									}
									return false;
				    			}
	    					} 
	    					
		    				if ($transaction_id != null)
			    			{
								$transaction->commit($transaction_id);
							}
	    				}
	    			}
	    			else
	    			{
	    				return false;
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
				if (is_array($id_array['sample']) and count($id_array['sample']) >= 1)
				{
					foreach($id_array['sample'] as $key => $value)
					{				
						$sample = new Sample($value['id']);
						if ($sample->is_sub_item_required($value['pos_id'], $pos_id) == true)
						{
							if (self::create($value['id'], $item_id, null, null, null, $parent_item_id, false) == false)
							{
								return false;
							}
							
							if (DataEntity::is_kind_of("file", $item_id) or DataEntity::is_kind_of("value", $item_id) or DataEntity::is_kind_of("parameter", $sub_item_value))
							{
								$data_entity_id = DataEntity::get_entry_by_item_id($item_id);
								$folder_id = $sample->get_item_holder_value("folder_id");
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