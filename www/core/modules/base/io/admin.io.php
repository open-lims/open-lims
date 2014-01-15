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
 * Admin IO Class
 * @package base
 */
class AdminIO
{
	public static function home()
	{
		$template = new HTMLTemplate("base/admin/home.html");
		
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("admin_home_box");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			$content = "";
			
			foreach ($module_dialog_array as $key => $value)
			{
				require_once($value['class_path']);
				$content .= $value['class']::$value['method']();
			}
			
			$template->set_var("content", $content);
		}
		else
		{
			$template->set_var("content", "");
		}
		
		$template->output();
	}	
}
	
?>