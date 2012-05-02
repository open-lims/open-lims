<?php
/**
 * @package sample
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
require_once("interfaces/sample_item_factory.interface.php");

/**
 * Sample Item Factory Class
 * @package sample
 */
class SampleItemFactory implements SampleItemFactoryInterface, EventListenerInterface
{
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
	public static function create($sample_id, $item_id, $gid, $keywords = null, $description = null, $parent_item_id = null, $parent = false)
	{
		global $transaction;
		
		if ($transaction->is_in_transction() == true)
		{
			$sample = new Sample($sample_id);
			$sample_item = new SampleItem($sample_id);
			
			$sample_item->set_gid($gid);
			$sample_item->set_parent($parent); // For parent sample only
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
    		if ($get_array[nav] == "sample" and is_numeric($get_array[sample_id]) and !$get_array['parent'] and !$get_array['parent_key'])
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
    			
    			if (self::create($get_array[sample_id], $event_object->get_item_id(), $get_array[key], $post_array[keywords], $post_array[description], null, $parent) == false)
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
    		elseif($get_array['nav'] == "sample" and is_numeric($get_array['sample_id']) and $get_array['parent'] and is_numeric($get_array['parent_id']))
    		{
    			$transaction_id = $transaction->begin();
    			
    			$handling_class = Item::get_holder_handling_class_by_name($get_array['parent']);
    			
    			if (class_exists($handling_class))
    			{
    				$parent_item = new $handling_class($get_array['parent_id']);

	    			if ($get_array['parent_sample'] == "1")
	    			{
	    				$parent = true;
	    			}
	    			else
	    			{
	    				$parent = false;
	    			}
	    			
	    			if (self::create($get_array[sample_id], $event_object->get_item_id(), null, null, null, $parent_item->get_item_id(), $parent) == false)
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
    			else
    			{
    				return false;
    			}
    		}
    		elseif($get_array['parent'] == "sample" and is_numeric($get_array['key']) and is_numeric($get_array['parent_id']))
    		{
    			$transaction_id = $transaction->begin();
    			
    			if (self::create($get_array['parent_id'], $event_object->get_item_id(), $get_array['key']) == false)
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