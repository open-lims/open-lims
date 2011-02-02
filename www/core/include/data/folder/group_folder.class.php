<?php
/**
 * @package data
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
require_once("interfaces/group_folder.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/folder_is_group_folder.access.php");
}

/**
 * Group Folder Class
 * @package data
 */
class GroupFolder implements GroupFolderInterface, EventListenerInterface
{
	function __construct()
	{
		
	}
	
	/**
	 * @todo: remove v-folder
	 */
	public function create($group_id)
	{
		if (is_numeric($group_id))
		{
			$group = new Group($group_id);
			
			// Folder
			$group_folder_id = $GLOBALS[group_folder_id];
			$folder = new Folder($group_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($group_id);
			
			$folder = new Folder(null);
			if (($folder_id = $folder->create($group->get_name(), $group_folder_id, false, $path->get_path_string(), 1, $group_id)) != null)
			{
				$folder_is_group_folder_access = new FolderIsGroupFolder_Access(null);
				if ($folder_is_group_folder_access->create($group_id, $folder_id) == null)
				{
					return false;
				}
				if ($folder->set_flag(4) == false)
				{
					$folder->delete(true, true);
					return false;
				}
										
				// Sample - Virtual Folder
				$virtual_folder = new VirtualFolder(null);
				if ($virtual_folder->create($folder_id, "samples") == null)
				{
					$folder->delete(true, true);
					return false;
				}
				if ($virtual_folder->set_sample_vfolder() == false)
				{
					$folder->delete(true, true);
					return false;
				}
				
				
				// Project - Virtual Folder
				
				$virtual_folder = new VirtualFolder(null);
				if ($virtual_folder->create($folder_id, "projects") == null)
				{
					$folder->delete(true, true);
					return false;
				}
				if ($virtual_folder->set_project_vfolder() == false)
				{
					$folder->delete(true, true);
					return false;
				}
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
	
	public static function listen_events($event_object)
	{
		if ($event_object instanceof GroupCreateEvent)
    	{
    		$group_folder = new GroupFolder();
    		if ($group_folder->create($event_object->get_group_id()) == false)
    		{
				return false;
    		}
    	}
    	
		return true;
	}
}