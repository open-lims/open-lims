<?php
/**
 * @package manufacturer
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
 * Manufacturer IO Class
 * @package manufacturer
 */
class ManufacturerIO
{
	public static function dialog()
	{
		$template = new HTMLTemplate("manufacturer/dialog.html");
		return $template->get_string();
	}
	
	public static function add()
	{
		$template = new HTMLTemplate("manufacturer/add.html");
		return $template->get_string();
	}
	
	public static function list_manufacturers()
	{
		$list = new List_IO("Manufacturer", "/core/modules/manufacturer/ajax/manufacturer.ajax.php", "get_list", "get_list_count", "0", "ManufacturerListPage");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column("Name","name",true,null);
		$list->add_column("User","user",true,null);
		$list->add_column("","delete",false,"16px");
		
		$template = new HTMLTemplate("manufacturer/list.html");
		
		$template->set_var("ADD_DIALOG", self::add());
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
}
?>