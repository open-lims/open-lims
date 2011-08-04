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
require_once("interfaces/project_virtual_folder.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/virtual_folder_is_project.access.php");
}

/**
 * Virtual Folder Management Class
 * @package project
 */
class ProjectVirtualFolder extends VirtualFolder implements ProjectVirtualFolderInterface, EventListenerInterface
{
	function __construct($virtual_folder_id)
	{
		parent::__construct($virtual_folder_id);
	}
	
	/**
	 * @return bool
	 */
	public function set_project_vfolder()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{	
			$virtual_folder_is_project = new VirtualFolderIsProject_Access(null);
			return $virtual_folder_is_project->create($this->virtual_folder_id);	
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function is_project_vfolder()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			return VirtualFolderIsProject_Access::is_entry($this->virtual_folder_id);
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
   			$event_object instanceof OrganisationUnitFolderCreateEvent)
    	{
    		$project_virtual_folder = new ProjectVirtualFolder(null);
			if (($virtual_folder_id = $project_virtual_folder->create($event_object->get_folder_id(), "projects")) == null)
			{
				return false;
			}
			$project_virtual_folder = new ProjectVirtualFolder($virtual_folder_id);
			if ($project_virtual_folder->set_project_vfolder() == false)
			{
				return false;
			}
    	} 
    	
    	if ($event_object instanceof VirtualFolderDeleteEvent)
    	{
    		$project_virtual_folder = new ProjectVirtualFolder($event_object->get_virtual_folder_id());
    		if ($project_virtual_folder->is_project_vfolder() == true)
			{
				$virtual_folder_is_project = new VirtualFolderIsProject_Access($event_object->get_virtual_folder_id());
				if ($virtual_folder_is_project->delete() == false)
				{
					return false;
				}
			}
    	}
    	    	
    	return true;
    }
}
?>