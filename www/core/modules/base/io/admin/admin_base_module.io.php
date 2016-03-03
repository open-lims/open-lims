<?php
/**
 * @package base
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
 * Base Module Admin IO Class
 * @package base
 */
class AdminBaseModuleIO
{
	public static function home()
	{
		$list = new List_IO("BaseAdminModuleHome" ,"ajax.php?nav=base.admin", "admin_list_modules", "admin_count_modules", "0", "BaseAdminModuleHome");
		
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"), "name", true, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnDialogs", "general"), "dialogs", true, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnLinks", "general"), "links", true, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnDisable", "general"), "disable", false, "80px");

		$template = new HTMLTemplate("base/admin/base_module/list.html");

		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function disable()
	{
		if ($_GET['id'])
		{				
			$paramquery = $_GET;
			unset($paramquery['action']);
			unset($paramquery['id']);
			$params = http_build_query($paramquery,'','&#38;');
			
			if (SystemHandler::disable_module($_GET['id']))
			{
				Common_IO::step_proceed($params, "Disable", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Disable", "Operation Failed" ,null);	
			}
		}
		else
		{
			
		}
	}
	
	public static function handler()
	{
		switch($_GET['action']):
			case "disable":
				self::disable();
			break;	
		
			default:
				self::home();
			break;
		endswitch;
	}
}