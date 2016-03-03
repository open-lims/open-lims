<?php
/**
 * @package data
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
 * Parameter Method Admin IO Class
 * @package data
 */
class AdminParameterMethodIO
{	
	public static function home()
	{
		$list = new List_IO("DataAdminParameterMethod", "ajax.php?nav=data", "admin_list_parameter_methods", "admin_count_parameter_methods", "0", "DataAdminParameterMethod");
		
		$list->add_column("", "symbol", false, "20px");
		$list->add_column(Language::get_message("DataGeneralListColumnName", "general"), "name");
		$list->add_column("", "edit", false, "20px");
		$list->add_column("", "delete", false, "20px");
		
		$template = new HTMLTemplate("data/admin/parameter_method/list.html");	
	
		$template->set_var("list", $list->get_list());		
		
		$template->output();			
	}
	
	public static function handler()
	{
		self::home();
	}
}
?>