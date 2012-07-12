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
		if (is_numeric($extension_id) and is_numeric($project_id))
		{
			$run_array = ProjectHasExtensionRun_Access::list_runs_by_extension_id_and_project_id($extension_id, $project_id);
			
			if (is_array($run_array) and count($run_array) >= 1)
			{
				$extension = new Extension($extension_id);
				$return = 1;
				
				foreach($run_array as $key => $value)
				{
					$status = $extension->get_run_status($value);
					if ($status == 0 or $status == -1)
					{
						$return = 0;
					}
				}
				
				return $return;
			}
			else
			{
				return -1;
			}
		}
		else
		{
			return -1;
		}
	}
	
	/**
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	global $session;
    	
    	if ($event_object instanceof ExtensionCreateRunEvent)
    	{
    		$event_identifier_array = $session->read_value("PROJECT_EXTENSION_EVENT_IDENTIFER_ARRAY");
    		$event_object_identifier = $event_object->get_event_identifier();
    		
    		if ($event_identifier_array[$event_object_identifier])
    		{
    			$run_id = $event_object->get_run_id();
    			$extension_id = $event_object->get_extension_id();
    			
    			$project_has_run_access = new ProjectHasExtensionRun_Access(null);
    			if ($project_has_run_access->create($event_identifier_array[$event_object_identifier], $extension_id, $run_id) == null)
    			{
    				return false;
    			}
    		}
    	}
    	    	
    	return true;
    }
}
?>