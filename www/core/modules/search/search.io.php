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
 * Search IO Class
 * @package base
 * @todo split class due to dependencies
 */
class SearchIO
{
	private static function main()
	{
		$template = new Template("languages/en-gb/template/search/main.html");
		
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("search");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			$counter = 0;
			$search_array = array();
			
			foreach ($module_dialog_array as $key => $value)
			{
				require_once($value[class_path]);
				
				$paramquery[username] 	= $_GET[username];
				$paramquery[session_id] = $_GET[session_id];
				$paramquery[nav]		= "search";
				$paramquery[run]		= "search";
				$paramquery[dialog]		= $value[internal_name];
				$params 				= http_build_query($paramquery,'','&#38;');
				
				$search_array[$counter][params] = $params;
				$search_array[$counter][title] = $value[display_name];
				$search_array[$counter][icon] = $value['class']::get_icon();
				$search_array[$counter][description] = $value['class']::get_description(null);
				$counter++;
			}
			
			$template->set_var("search_array", $search_array);
		}

		$template->output();		
	}
		
	public static function method_handler()
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
				self::main();
			break;
		endswitch;
	}
	
}
?>
