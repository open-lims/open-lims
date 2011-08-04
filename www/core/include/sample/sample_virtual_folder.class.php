<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz
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
require_once("interfaces/sample_virtual_folder.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/virtual_folder_is_sample.access.php");
}

/**
 * Sample Virtual Folder Management Class
 * @package sample
 */
class SampleVirtualFolder extends VirtualFolder implements SampleVirtualFolderInterface, EventListenerInterface
{
	function __construct($virtual_folder_id)
	{
		parent::__construct($virtual_folder_id);
	}
	
	/**
	 * @return bool
	 */
	public function set_sample_vfolder()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			$virtual_folder_is_sample = new VirtualFolderIsSample_Access(null);
			return $virtual_folder_is_sample->create($this->virtual_folder_id);	
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function is_sample_vfolder()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			return VirtualFolderIsSample_Access::is_entry($this->virtual_folder_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserFolderCreateEvent or 
   			$event_object instanceof GroupFolderCreateEvent or 
   			$event_object instanceof OrganisationUnitFolderCreateEvent or
   			$event_object instanceof SampleFolderCreateEvent or 
   			$event_object instanceof ProjectFolderCreateEvent)
    	{
    		$sample_virtual_folder = new SampleVirtualFolder(null);
			if (($virtual_folder_id = $sample_virtual_folder->create($event_object->get_folder_id(), "samples")) == null)
			{
				return false;
			}
			
			$sample_virtual_folder = new SampleVirtualFolder($virtual_folder_id);
			if ($sample_virtual_folder->set_sample_vfolder() == false)
			{
				return false;
			}
    	}
    	
    	if ($event_object instanceof VirtualFolderDeleteEvent)
    	{
    		$sample_virtual_folder = new SampleVirtualFolder($event_object->get_virtual_folder_id());
    		if ($sample_virtual_folder->is_sample_vfolder() == true)
			{
				$virtual_folder_is_sample = new VirtualFolderIsSample_Access($event_object->get_virtual_folder_id());
				if ($virtual_folder_is_sample->delete() == false)
				{
					return false;
				}
			}
    	}
    	
    	if ($event_object instanceof SampleItemLinkEvent or 
    		$event_object instanceof ProjectItemLinkEvent)
    	{
    		if (($sample_id = Sample::get_entry_by_item_id($event_object->get_item_id())) != null)
    		{
    			$sample_folder_id = SampleFolder::get_folder_by_sample_id($sample_id);
    			
	    		$folder = Folder::get_instance($event_object->get_folder_id());
	    		$child_array = $folder->get_childs();
	    		if (is_array($child_array) and count($child_array) >= 1)
	    		{
	    			foreach($child_array as $key => $value)
	    			{
	    				if (($virtual_folder_id = VirtualFolder::get_virtual_folder_id_by_data_entity_id($value)) != null)
	    				{
	    					$sample_virtual_folder = new SampleVirtualFolder($virtual_folder_id);
	    					if ($sample_virtual_folder->is_sample_vfolder() == true)
	    					{
	    						if ($sample_virtual_folder->link_folder($sample_folder_id) == false)
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