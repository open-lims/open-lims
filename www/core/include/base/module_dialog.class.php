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
require_once("interfaces/module_dialog.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base_module_dialog.access.php");
}

/**
 * Module Dialog Class
 * @package base
 */
class ModuleDialog implements ModuleDialogInterface
{	
	public static function get_by_type_and_internal_name($dialog_type, $internal_name)
	{
		return BaseModuleDialog_Access::get_by_type_and_internal_name($dialog_type, $internal_name);
	}
	
	public static function list_dialogs_by_type($dialog_type)
	{
		return BaseModuleDialog_Access::list_dialogs_by_type($dialog_type);
	}
}

?>