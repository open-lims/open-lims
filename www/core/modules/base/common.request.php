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
 * Request Class for Common Requests from each Module Request Handler (Controller)
 * @package base
 */
class CommonRequest
{
	/**
	 * @throws BaseModuleDialogMethodNotFoundException
	 * @throws BaseModuleDialogClassNotFoundException
	 * @throws BaseModuleDialogFileNotFoundException
	 * @throws BaseModuleDialogMissingException
	 */
	public static function common_dialog()
	{
		if ($_GET['dialog'])
		{
			$module_dialog = ModuleDialog::get_by_type_and_internal_name("common_dialog", $_GET['dialog']);
			
			if (file_exists($module_dialog['class_path']))
			{
				require_once($module_dialog['class_path']);
				
				if (class_exists($module_dialog['class']))
				{
					if (method_exists($module_dialog['class'], $module_dialog['method']))
					{
						$module_dialog['class']::$module_dialog['method']();
					}
					else
					{
						throw new BaseModuleDialogMethodNotFoundException();
					}
				}
				else
				{
					throw new BaseModuleDialogClassNotFoundException();
				}
			}
			else
			{
				throw new BaseModuleDialogFileNotFoundException();
			}
		}
		else
		{
			throw new BaseModuleDialogMissingException();
		}
	}
}
?>