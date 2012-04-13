<?php
/**
 * @package extension
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
require_once("interfaces/extension_handler.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/extension.access.php");
}

/**
 * Extension Handler Class
 * @package extension
 */
class ExtensionHandler implements ExtensionHandlerInterface, EventListenerInterface
{
	/**
	 * @see ExtensionHandlerInterface::__construct()
	 * @param boolean $scan
	 */
	function __construct($scan = true)
	{
		if ($scan == true)
		{
			$this->scan_extensions();
		}
	}
	
	private function scan_extensions()
	{
		$registered_extension_array = Extension_Access::list_folder_entries();
		$found_extension_array = array();
		
		$extension_folder_array = scandir(constant("EXTENSION_DIR"));
		
		if (is_array($extension_folder_array) and count($extension_folder_array) >= 1)
		{			
			foreach($extension_folder_array as $key => $value)
			{
				$sub_folder = constant("EXTENSION_DIR")."/".$value;
				if (is_dir($sub_folder) and $key > 1)
				{
					$config_folder = $sub_folder."/config";
					if (is_dir($config_folder))
					{
						$config_file = $config_folder."/extension_info.php";
						
						if (is_file($config_file))
						{
							include($config_file);
							
							if (is_array($registered_extension_array) and ($register_key = array_search($value, $registered_extension_array)) !== false)
							{
								$found_extension_array[$register_key] = $value;
							}
							else
							{
								$extension = new Extension_Access(null);
								$extension->create($name, $identifer, $value, $main_class, $main_file);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * @see ExtensionHandlerInterface::list_extensions()
	 * @return array
	 */
	public static function list_extensions()
	{
		return Extension_Access::list_entries();
	}
	
	/**
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof DeleteEvent)
    	{
    		$extension_array = Extension_Access::list_entries();
    		
    		if (is_array($extension_array) and count($extension_array) >= 1)
    		{
    			foreach ($extension_array as $key => $value)
    			{
    				$main_file = constant("EXTENSION_DIR")."/".$value['folder']."/".$value['main_file'];
					$main_class = $value['class'];
					if (class_exists($main_class))
					{
						if ($main_class::listen_events($event_object) == false)
						{
							return false;
						}
					}
    			}
    		}
    	}
    	
    	return true;
    }
}
?>