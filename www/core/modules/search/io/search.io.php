<?php
/**
 * @package search
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
	public static function main()
	{
		$template = new Template("template/search/main.html");
		
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
}
?>
