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
require_once("interfaces/organisation_unit_folder.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/folder_is_organisation_unit_folder.access.php");
}

/**
 * Organisation Unit Folder Class
 * @package data
 */
class OrganisationUnitFolder implements OrganisationUnitFolderInterface, EventListenerInterface
{
	function __construct()
	{
		
	}
	
	/**
	 * @todo: remove v-folder
	 */
	public function create($organisation_unit_id)
	{
		if (is_numeric($organisation_unit_id))
		{
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			
			// Folder
			$organisation_unit_folder_id = $GLOBALS[organisation_unit_folder_id];
			$folder = new Folder($organisation_unit_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($organisation_unit_id);
			
			$folder = new Folder(null);
			if (($folder_id = $folder->create($organisation_unit->get_name(), $organisation_unit_folder_id, false, $path->get_path_string(), $organisation_unit->get_owner_id(), null)) != null)
			{
				$folder_is_organisation_unit_folder_access = new FolderIsOrganisationUnitFolder_Access(null);
				if ($folder_is_organisation_unit_folder_access->create($organisation_unit_id, $folder_id) == null)
				{
					return false;
				}
				if ($folder->set_flag(8) == false)
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
				$folder->delete(true, true);
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
		if ($event_object instanceof OrganisationUnitCreateEvent)
    	{
    		if ($event_object->get_contains_projects() == true)
    		{
    			$organisation_unit_folder = new OrganisationUnitFolder();
	    		if ($organisation_unit_folder->create($event_object->get_organisation_unit_id()) == false)
	    		{
					return false;
	    		}
    		}	
    	}
    	
		return true;
	}
}