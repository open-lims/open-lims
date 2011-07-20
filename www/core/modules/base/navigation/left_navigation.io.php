<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Quiring
 * @copyright (c) 2008-2011 by Roman Konertz, Roman Quiring
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
 * Left Navigation IO Class
 * @package base
 */
class LeftNavigationIO
{
	public static function create_left_navigation()
	{
		global $session;
		
		if ($session->is_value("LEFT_NAVIGATION_ACTIVE"))
		{
			$active_id = $session->read_value("LEFT_NAVIGATION_ACTIVE");
		}
		else
		{
			$active_id = "LeftNavigationSymbol0";
		}
		
		
		$counter = 0;
		$js_array = array();
		$symbols_array = array();
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("base_left_navigation");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			foreach ($module_dialog_array as $key => $value)
			{
				if (file_exists($value['class_path']))
				{
					require_once($value['class_path']);
					
					if (class_exists($value['class']))
					{
						$js_array[$counter][ajax_url] = $value['class']::get_ajax_url();
						$js_array[$counter][id] = "LeftNavigationSymbol".$key;
						
						if ($js_array[$counter][id] == $active_id)
						{
							$active_js_array[ajax_url] = $js_array[$counter][ajax_url];
							$active_js_array[id] = $js_array[$counter][id];
						}
						
						$symbols_array[$counter][icon] = $value['class']::get_icon();
						$symbols_array[$counter][active] = $value['class']::get_active();
						$symbols_array[$counter][id] = "LeftNavigationSymbol".$key;
						$counter++;
					}
				}
			}
		}

		$template = new Template("template/base/navigation/left/main.html");
		
		$template->set_var("symbols", $symbols_array);
		$template->set_var("js", $js_array);
		
		$template->set_var("active_js_ajax_url", $active_js_array[ajax_url]);
		$template->set_var("active_js_id", $active_js_array[id]);
		
		$template->output();
	}
}
?>