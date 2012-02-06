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
 * Base Request Class
 * @package base
 */
class BaseRequest
{	
	public static function ajax_handler()
	{
		switch($_GET[run]):

			case "cron":
				require_once("ajax/cron.ajax.php");
				echo CronAjax::run();
			break;
		
			case "login":
				require_once("ajax/login.ajax.php");
				echo LoginAjax::login($_POST[username], $_POST[password], $_POST[language]);
			break;
			
			case "logout":
				require_once("ajax/login.ajax.php");
				echo LoginAjax::logout();
			break;
			
			case "left_navigation":
				require_once("ajax/navigation/left_navigation.ajax.php");
				
				switch($_GET['action']):
					case "set_active":
						echo LeftNavigationAjax::set_active($_POST['id']);
					break;
				endswitch;
			break;
			
			
			// Lists
			
			case "list_get_page_information":
				require_once("ajax/list.ajax.php");
				echo ListAjax::get_page_information($_POST['number_of_entries'], $_POST['number_of_pages']);
			break;
			
			case "list_get_page_bar":
				require_once("ajax/list.ajax.php");
				echo ListAjax::get_page_bar($_POST['page'], $_POST['number_of_pages'], $_POST['css_page_id']);
			break;
			
			
			// Search
			
			case "search_user_list_users":
				require_once("ajax/user_search.ajax.php");
				echo UserSearchAjax::list_users($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "search_user_count_users":
				require_once("ajax/user_search.ajax.php");
				echo UserSearchAjax::count_users($_POST[argument_array]);
			break;
			
			case "search_user_list_groups":
				require_once("ajax/user_search.ajax.php");
				echo UserSearchAjax::list_groups($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "search_user_count_groups":
				require_once("ajax/user_search.ajax.php");
				echo UserSearchAjax::count_groups($_POST[argument_array]);
			break;
			
			
			// User

			case "get_users_in_option":
				require_once("common/ajax/user_common.ajax.php");
				echo UserAjax::get_users_in_option($_POST['string']);
			break;
			
			case "get_groups_in_option":
				require_once("common/ajax/user_common.ajax.php");
				echo UserAjax::get_groups_in_option($_POST['string']);
			break;
			
			
			// Admin
			
			case "admin_list_includes":
				require_once("ajax/admin/admin_base_include.ajax.php");
				echo AdminBaseIncludeAjax::list_includes($_POST[column_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_count_includes":
				require_once("ajax/admin/admin_base_include.ajax.php");
				echo AdminBaseIncludeAjax::count_includes();
			break;
			
			case "admin_list_modules":
				require_once("ajax/admin/admin_base_module.ajax.php");
				echo AdminBaseModuleAjax::list_modules($_POST[column_array], $_POST[get_array], $_POST[css_page_id], $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_count_modules":
				require_once("ajax/admin/admin_base_module.ajax.php");
				echo AdminBaseModuleAjax::count_modules();
			break;
			
			case "admin_list_navigation":
				require_once("ajax/admin/admin_base_navigation.ajax.php");
				echo AdminBaseNavigationAjax::list_navigation($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_count_navigation":
				require_once("ajax/admin/admin_base_navigation.ajax.php");
				echo AdminBaseNavigationAjax::count_navigation($_POST[argument_array]);
			break;
			
			case "admin_list_users":
				require_once("ajax/admin/admin_user.ajax.php");
				echo AdminUserAjax::list_users($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_count_users":
				require_once("ajax/admin/admin_user.ajax.php");
				echo AdminUserAjax::count_users($_POST[argument_array]);
			break;
			
			case "admin_list_groups":
				require_once("ajax/admin/admin_group.ajax.php");
				echo AdminGroupAjax::list_groups($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_count_groups":
				require_once("ajax/admin/admin_group.ajax.php");
				echo AdminGroupAjax::count_groups($_POST[argument_array]);
			break;
			
			case "admin_list_system_log":
				require_once("ajax/admin/admin_system_log.ajax.php");
				echo AdminSystemLogAjax::list_system_log($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_count_system_log":
				require_once("ajax/admin/admin_system_log.ajax.php");
				echo AdminSystemLogAjax::count_system_log($_POST[argument_array]);
			break;
			
			case "admin_general_list_languages":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::list_languages($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_general_count_languages":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::count_languages($_POST[argument_array]);
			break;
			
			case "admin_general_list_timezones":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::list_timezones($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_general_count_timezones":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::count_timezones($_POST[argument_array]);
			break;
			
			case "admin_general_list_paper_sizes":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::list_paper_sizes($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_general_count_paper_sizes":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::count_paper_sizes($_POST[argument_array]);
			break;
			
			case "admin_general_add_paper_size":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::add_paper_size($_POST[name], $_POST[width], $_POST[height], $_POST[margin_left], $_POST[margin_right], $_POST[margin_top], $_POST[margin_bottom]);
			break;
			
			case "admin_general_get_paper_size":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::get_paper_size($_POST[id]);
			break;
			
			case "admin_general_set_paper_size":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::set_paper_size($_POST[id], $_POST[name], $_POST[width], $_POST[height], $_POST[margin_left], $_POST[margin_right], $_POST[margin_top], $_POST[margin_bottom]);
			break;
			
			case "admin_general_delete_paper_size":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::delete_paper_size($_POST[id]);
			break;
			
			case "admin_general_list_measuring_units":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::list_measuring_units($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_general_count_measuring_units":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::count_measuring_units($_POST[argument_array]);
			break;
			
			case "admin_general_list_currencies":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::list_currencies($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "admin_general_count_currencies":
				require_once("ajax/admin/admin_general.ajax.php");
				echo AdminGeneralAjax::count_currencies($_POST[argument_array]);
			break;
			
		endswitch;
	}
	
	public static function io_handler()
	{
		switch ($_GET[run]):
					
			// BASE
			case "sysmsg":
				require_once("io/base.io.php");
				BaseIO::list_system_messages();
			break;
			
			case "system_info":
				require_once("io/base.io.php");
				BaseIO::system_info();
			break;
			
			case "software_info":
				require_once("io/base.io.php");
				BaseIO::software_info();
			break;
			
			case "license":
				require_once("io/base.io.php");
				BaseIO::license();
			break;
			
			case "base_user_lists";
				if ($_GET[dialog])
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("base_user_lists", $_GET[dialog]);
					
					if (file_exists($module_dialog[class_path]))
					{
						require_once($module_dialog[class_path]);
						
						if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
						{
							$module_dialog['class']::$module_dialog[method]();
						}
						else
						{
							// Error
						}
					}
					else
					{
						// Error
					}
				}
				else
				{
					// error
				}
			break;
			
			
			// USER
			case "user_profile":
				require_once("io/user.io.php");
				UserIO::profile();
			break;
			
			case ("user_details"):
				require_once("io/user.io.php");
				UserIO::details();
			break;
			
			case("user_change_personal"):
				require_once("io/user.io.php");
				UserIO::change_personal();
			break;
			
			case("user_change_my_settings"):
				require_once("io/user.io.php");
				UserIO::change_my_settings();
			break;
			
			case("user_change_password"):
				require_once("io/user.io.php");
				UserIO::change_password();
			break;
			
			case("user_change_language"):
				require_once("io/user.io.php");
				UserIO::change_language();
			break;
			
			case("user_change_timezone"):
				require_once("io/user.io.php");
				UserIO::change_timezone();
			break;		
			
		endswitch;
	}
}
?>