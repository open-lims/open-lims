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
class AdminBaseNavigationIO
{
	public static function home()
	{
		$list = new List_IO("BaseAdminNavigationHome" ,"ajax.php?nav=base.admin", "admin_list_navigation", "admin_count_navigation", "0", "BaseAdminNavigationHome");
		
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"), "name", false, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnModule", "general"), "module", false, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnColour", "general"), "colour", false, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnUW", "general"), "uw", false, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnDW", "general"), "dw", false, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnShowHide", "general"), "hide", false, "80px");

		$template = new HTMLTemplate("base/admin/base_navigation/list.html");

		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	/**
	 * @throws BaseModuleNavigationIDMissingException
	 */
	public static function hide()
	{
		if ($_GET['id'])
		{
			$module_navigation = new ModuleNavigation($_GET['id']);
				
			$paramquery = $_GET;
			unset($paramquery['action']);
			unset($paramquery['id']);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($module_navigation->hide())
			{
				Common_IO::step_proceed($params, "Hide/Show", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Hide/Show", "Operation Failed" ,null);	
			}
		}
		else
		{
			throw new BaseModuleNavigationIDMissingException();
		}
	}
	
	/**
	 * @throws BaseModuleNavigationIDMissingException
	 */
	public static function upwards()
	{
		if ($_GET['id'])
		{
			$module_navigation = new ModuleNavigation($_GET['id']);
				
			$paramquery = $_GET;
			unset($paramquery['action']);
			unset($paramquery['id']);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($module_navigation->upwards())
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			throw new BaseModuleNavigationIDMissingException();
		}
	}
	
	/**
	 * @throws BaseModuleNavigationIDMissingException
	 */
	public static function downwards()
	{
		if ($_GET['id'])
		{
			$module_navigation = new ModuleNavigation($_GET['id']);
				
			$paramquery = $_GET;
			unset($paramquery['action']);
			unset($paramquery['id']);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($module_navigation->downwards())
			{
				Common_IO::step_proceed($params, "Downwards", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Downwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			throw new BaseModuleNavigationIDMissingException();
		}
	}
	
	public static function handler()
	{
		switch($_GET['action']):
			case "upwards":
				self::upwards();
			break;	
		
			case "downwards":
				self::downwards();
			break;
			
			case "hide":
				self::hide();
			break;
		
			default:
				self::home();
			break;
		endswitch;
	}
}