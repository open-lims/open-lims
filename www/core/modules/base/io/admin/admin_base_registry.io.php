<?php
/**
 * @package base
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
 * Base Registry Admin IO Class
 * @package base
 */
class AdminBaseRegistryIO
{
	public static function home()
	{
		$list = new List_IO("BaseAdminRegistryHome" ,"ajax.php?nav=base", "admin_list_registry", "admin_count_registry", "0", "BaseAdminRegistryHome");
		
		$list->add_column("", "icon", false, "16px");
		$list->add_column("Name", "name", true, null);
		$list->add_column("Value", "value", true, null);
		$list->add_column("Include", "include", true, null);
		$list->add_column("", "edit", false, "16px", "BaseAdminRegistryEditButton");

		$template = new HTMLTemplate("base/admin/base_registry/list.html");

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
?>