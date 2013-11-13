<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Base Include Admin IO Class
 * @package base
 */
class AdminBaseIncludeIO
{
	public static function home()
	{		
		$list = new List_IO("BaseAdminIncludeHome" ,"ajax.php?nav=base.admin", "admin_list_includes", "admin_count_includes", "0", "BaseAdminIncludeAjax");

		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"),"name",true,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnFolder", "general"),"folder",true,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnEventListerns", "general"),"eventlisteners",true,null);
		
		$template = new HTMLTemplate("base/admin/base_include/list.html");	
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function handler()
	{
		switch($_GET['action']):		
			default:
				self::home();
			break;
		endswitch;
	}
}