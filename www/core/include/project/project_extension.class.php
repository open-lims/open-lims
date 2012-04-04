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
// require_once("interfaces/project_extension.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_has_extension_run.access.php");
}

/**
 * Project Extension
 * @package project
 */
class ProjectExtension // implements ProjectExtensionInterface, EventListenerInterface
{
	public static function start_extension($extension_id, $project_id)
	{
		
	}
	
	public static function get_status($extension_id, $project_id)
	{
		
	}
	
	/**
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof ExtensionCreateRunEvent)
    	{
    		$event_identifer_array = $session->read_value("PROJECT_EXTENSION_EVENT_IDENTIFER_ARRAY");
    		$event_object_identifer = $event_object->get_event_identifer();
    		
    		if ($event_identifer_array[$event_object_identifer])
    		{
    			$run_id = $event_object->get_run_id();
    			$extension_id = $event_object->get_extension_id();
    			
    			$project_has_run_access = new ProjectHasExtensionRun_Access(null);
    			if ($project_has_run_access->create($event_identifer_array[$event_object_identifer], $extension_id, $run_id) == null)
    			{
    				return false;
    			}
    		}
    	}
    	    	
    	return true;
    }
}
?>