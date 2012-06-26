<?php
/**
 * @package search
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
 * Search Request Class
 * @package search
 */
class SearchRequest
{
	public static function ajax_handler()
	{
		
	}
	
	public static function io_handler()
	{	
		switch($_GET[run]):
			
			// Search
			/**
			 * @todo errors, exceptions
			 */
			case("search"):
				if ($_GET[dialog])
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("search", $_GET[dialog]);
					
					if (file_exists($module_dialog[class_path]))
					{
						require_once($module_dialog[class_path]);
						
						if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
						{
							$module_dialog['class']::$module_dialog[method]();
						}
						else
						{
							// Error
						}
					}
					else
					{
						// Error
					}
				}
				else
				{
					// error
				}
			break;
			
			// Common Dialogs
			/**
			 * @todo errors, exceptions
			 */
			case("common_dialog"):
				if ($_GET[dialog])
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("common_dialog", $_GET[dialog]);
					
					if (file_exists($module_dialog[class_path]))
					{
						require_once($module_dialog[class_path]);
						
						if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
						{
							$module_dialog['class']::$module_dialog[method]();
						}
						else
						{
							// Error
						}
					}
					else
					{
						// Error
					}
				}
				else
				{
					// error
				}
			break;
						
			default:
				require_once("io/search.io.php");
				SearchIO::main();
			break;
		endswitch;
	}
}
?>