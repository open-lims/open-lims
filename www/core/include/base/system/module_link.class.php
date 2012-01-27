<?php
/**
 * @package base
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
require_once("interfaces/module_link.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base_module_link.access.php");
}

/**
 * Module Link Class
 * @package base
 */
class ModuleLink implements ModuleLinkInterface, EventListenerInterface
{		
	/**
	 * @see ModuleLinkInterface::list_links_by_type()
	 * @param string $link_type
	 * @return array
	 */
	public static function list_links_by_type($link_type)
	{
		return BaseModuleLink_Access::list_links_by_type($link_type);
	}
	
	/**
	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof ModuleDisableEvent)
    	{
    		$id_array = BaseModuleLink_Access::list_id_by_module_id($event_object->get_module_id());
    		if (is_array($id_array) and count($id_array) >= 1)
    		{
    			foreach($id_array as $key => $value)
    			{
	    			$module_link = new BaseModuleLink_Access($value);
	    			if ($module_link->get_disabled() == false)
	    			{
		    			if ($module_link->set_disabled(true) == false)
		    			{
		    				return false;
		    			}
	    			}
    			}
    		}
    	}
    	
    	if ($event_object instanceof ModuleEnableEvent)
    	{
    		$id_array = BaseModuleLink_Access::list_id_by_module_id($event_object->get_module_id());
    		if (is_array($id_array) and count($id_array) >= 1)
    		{
    			foreach($id_array as $key => $value)
    			{
	    			$module_link = new BaseModuleLink_Access($value);
	    			if ($module_link->get_disabled() == true)
	    			{
		    			if ($module_link->set_disabled(false) == false)
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