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
require_once("interfaces/user_folder.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/folder_is_home_folder.access.php");
}

/**
 * Path Class
 * @package data
 */
class UserFolder implements UserFolderInterface, EventListenerInterface
{
	function __construct()
	{
		
	}
	
	public function create($user_id)
	{
		if (is_numeric($user_id))
		{
			$user = new User($user_id);
			
			// Folder
			$user_folder_id = $GLOBALS[user_folder_id];
			$folder = new Folder($user_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($user_id);
			
			$folder = new Folder(null);
			if (($folder_id = $folder->create($user->get_username(), $user_folder_id, false, $path->get_path_string(), $user_id, null)) != null)
			{
				$folder_is_home_folder_access = new FolderIsHomeFolder_Access(null);
				if ($folder_is_home_folder_access->create($user_id, $folder_id) == null)
				{
					return false;
				}
				if ($folder->set_flag(2) == false)
				{
					$folder->delete(true, true);
					return false;
				}
				
				// _Public
				$public_path = new Path($path->get_path_string());
				$public_path->add_element("_public");
				
				$public_folder = new Folder(null);
				if (($public_folder->create("_public", $folder_id, false, $public_path->get_path_string(), $user_id, null)) == null)
				{
					$folder->delete();
					return false;
				}
				
				if ($public_folder->set_flag(512) == false)
				{
					$folder->delete();
					return false;
				}
				
				
				// _Private
				
				$private_path = new Path($path->get_path_string());
				$private_path->add_element("_private");
				
				$private_folder = new Folder(null);
				if (($private_folder->create("_private", $folder_id, false, $private_path->get_path_string(), $user_id, null)) == null)
				{
					$folder->delete();
					return false;
				}
				
				if ($private_folder->set_flag(512) == false)
				{
					$folder->delete();
					return false;
				}
				
				// Sample - Virtual Folder
				
				$virtual_folder = new VirtualFolder(null);
				if ($virtual_folder->create($folder_id, "samples") == null)
				{
					$folder->delete();
					return false;
				}
				if ($virtual_folder->set_sample_vfolder() == false)
				{
					$folder->delete();
					return false;
				}
				
				
				// Project - Virtual Folder
				
				$virtual_folder = new VirtualFolder(null);
				if ($virtual_folder->create($folder_id, "projects") == null)
				{
					$folder->delete();
					return false;
				}
				if ($virtual_folder->set_project_vfolder() == false)
				{
					$folder->delete();
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
		if ($event_object instanceof UserCreateEvent)
    	{
    		$user_folder = new UserFolder();
    		if ($user_folder->create($event_object->get_user_id()) == false)
    		{
				return false;
    		}
    	}
    	
		return true;
	}
}