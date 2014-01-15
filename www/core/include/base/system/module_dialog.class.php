<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
require_once("interfaces/module_dialog.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base_module.access.php");
	require_once("access/base_module_dialog.access.php");
}

/**
 * Module Dialog Class
 * @package base
 */
class ModuleDialog implements ModuleDialogInterface, EventListenerInterface
{	
	/**
	 * @see ModuleDialogInterface::get_by_type_and_internal_name()
	 * @param string $dialog_type
	 * @param string $internal_array
	 * @return array
	 */
	public static function get_by_type_and_internal_name($dialog_type, $internal_name)
	{
		return BaseModuleDialog_Access::get_by_type_and_internal_name($dialog_type, $internal_name);
	}
	
	/**
	 * @see ModuleDialogInterface::list_dialogs_by_type()
	 * @param string $dialog_type
	 * @return array
	 */
	public static function list_dialogs_by_type($dialog_type)
	{
		return BaseModuleDialog_Access::list_dialogs_by_type($dialog_type);
	}
	
	/**
	 * @see ModuleDialogInterface::list_dialogs_by_type_and_module()
	 * @param string $module
	 * @param string $dialog_type
	 * @return array
	 */
	public static function list_dialogs_by_type_and_module($dialog_type, $module)
	{
		$module_id = BaseModule_Access::get_module_id_by_module_name($module);
		return BaseModuleDialog_Access::list_dialogs_by_type_and_module_id($dialog_type, $module_id);
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
    		$id_array = BaseModuleDialog_Access::list_id_by_module_id($event_object->get_module_id());
    		if (is_array($id_array) and count($id_array) >= 1)
    		{
    			foreach($id_array as $key => $value)
    			{
	    			$module_dialog = new BaseModuleDialog_Access($value);
	    			if ($module_dialog->get_disabled() == false)
	    			{
		    			if ($module_dialog->set_disabled(true) == false)
		    			{
		    				return false;
		    			}
	    			}
    			}
    		}
    	}
    	
    	if ($event_object instanceof ModuleEnableEvent)
    	{
    		$id_array = BaseModuleDialog_Access::list_id_by_module_id($event_object->get_module_id());
    		if (is_array($id_array) and count($id_array) >= 1)
    		{
    			foreach($id_array as $key => $value)
    			{
	    			$module_dialog = new BaseModuleDialog_Access($value);
	    			if ($module_dialog->get_disabled() == true)
	    			{
		    			if ($module_dialog->set_disabled(false) == false)
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