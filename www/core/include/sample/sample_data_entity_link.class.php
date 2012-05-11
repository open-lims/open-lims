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
 * Sample Data Entity Link Class
 * Listens on Data Entity link events while adding a file as a sub-item
 * @package sample
 */
class SampleDataEntityLink implements EventListenerInterface
{
	/**
	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
   		if ($event_object instanceof DataEntityLinkEvent)
    	{
    		$data_entity_id = $event_object->get_data_entity_id();
    		$get_array = $event_object->get_get_array();
    		
    		if($get_array['nav'] == "sample" and is_numeric($get_array['sample_id']) and $get_array['parent'] and is_numeric($get_array['parent_id']))
    		{
    			$sample = new Sample($get_array['sample_id']);
    			$folder_id = $sample->get_item_holder_value("folder_id");
    			$parent_data_entity_id = Folder::get_data_entity_id_by_folder_id($folder_id);

    			$child_data_entity = new DataEntity($data_entity_id);
    			
    			if ($child_data_entity->set_as_child_of($parent_data_entity_id, true) == false)
    			{
    				return false;
    			}
    		}
    	}
    	
    	return true;
    }
}
?>