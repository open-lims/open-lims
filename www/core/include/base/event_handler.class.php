<?php
/**
 * @package base
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
require_once("interfaces/event_handler.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	define("BASE_EVENT_LISTENER_TABLE"	, "core_base_event_listeners");
	
	require_once("access/base_event_listener.access.php");
}

/**
 * Event Handler Class
 * @package base
 */
class EventHandler implements EventHandlerInterface
{
	private $event_object;
	private $success = false;
	
	function __construct($event_object)
	{
		if (is_object($event_object))
		{
			$event_listener_array = BaseEventListener_Access::list_classes();
			
			if (is_array($event_listener_array) and count($event_listener_array) >= 1)
			{
				foreach($event_listener_array as $key => $class)
				{
					if (($listen_success = $class::listen_events($event_object)) == false)
					{
						break;
					}
				}
				if ($listen_success == true)
				{
					$this->success = true;	
				}
			}
		}
	}
	
	public function get_success()
	{
		return $this->success;
	}
}
?>
