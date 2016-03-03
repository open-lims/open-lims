<?php
/**
 * @package search
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * @package search
 */
class SearchIO
{
	public static function header_search($string, $current_module)
	{
		if ($string and $current_module)
		{
			$current_module_array = explode(".", $current_module);
			
			$module_dialog_array = ModuleDialog::list_dialogs_by_type_and_module("standard_search", $current_module_array[0]);
			
			if (is_array($module_dialog_array) and count($module_dialog_array) === 1)
			{				
				self::search($module_dialog_array[0]['internal_name']);
			}
			else
			{
				$template = new HTMLTemplate("base/search/header_search.html");
				
				$module_dialog_array = ModuleDialog::list_dialogs_by_type("search");
				
				if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
				{
					$counter = 0;
					$search_array = array();
					
					foreach ($module_dialog_array as $key => $value)
					{
						require_once($value['class_path']);
						
						$paramquery['username'] 	= $_GET['username'];
						$paramquery['session_id'] 	= $_GET['session_id'];
						$paramquery['nav']			= "base.search";
						$paramquery['run']			= "search";
						$paramquery['dialog']		= $value['internal_name'];
						$paramquery['nextpage']		= "1";
						$params 					= http_build_query($paramquery,'','&#38;');
						
						$search_array[$counter]['params'] 	= $params;
						$search_array[$counter]['title'] 	= Language::get_message($value['language_address'], "dialog");
						$counter++;
					}
					
					$template->set_var("search_array", $search_array);
					$template->set_var("string", $string);
				}
				
				$template->output();
			}
		}
		else
		{
			self::main();
		}
	}
	
	/**
	 * @param string $dialog
	 * @throws BaseModuleDialogMethodNotFoundException
	 * @throws BaseModuleDialogClassNotFoundException
	 * @throws BaseModuleDialogFileNotFoundException
	 * @throws BaseModuleDialogMissingException
	 */
	public static function search($dialog)
	{
		if ($dialog)
		{
			$module_dialog = ModuleDialog::get_by_type_and_internal_name("search", $dialog);
			
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
	
	public static function main()
	{
		$template = new HTMLTemplate("base/search/main.html");
		
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("search");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			$counter = 0;
			$search_array = array();
			
			foreach ($module_dialog_array as $key => $value)
			{
				require_once($value['class_path']);
				
				$paramquery['username'] 	= $_GET['username'];
				$paramquery['session_id'] 	= $_GET['session_id'];
				$paramquery['nav']			= "base.search";
				$paramquery['run']			= "search";
				$paramquery['dialog']		= $value['internal_name'];
				$params 					= http_build_query($paramquery,'','&#38;');
				
				$search_array[$counter]['params'] 	= $params;
				$search_array[$counter]['title'] 		= Language::get_message($value['language_address'], "dialog");
				$search_array[$counter]['icon'] 		= $value['class']::get_icon();
				$search_array[$counter]['description'] 	= $value['class']::get_description(null);
				$counter++;
			}
			
			$template->set_var("search_array", $search_array);
		}

		$template->output();
	}
}
?>
