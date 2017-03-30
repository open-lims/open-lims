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
 * Base Request Class
 * @package base
 */
class BaseRequest
{	
	/**
	 * @param string $alias
	 */
	public static function ajax_handler($alias)
	{
		switch(System::get_get("run")):

			case "cron":
				require_once("ajax/cron.ajax.php");
				echo CronAjax::run();
			break;
		
			case "login":
				require_once("ajax/login.ajax.php");
				echo LoginAjax::login(System::get_post("username"), 
										System::get_post("password"), 
										System::get_post("language"));
			break;
			
			case "forgot_password":
				require_once("ajax/login.ajax.php");
				echo LoginAjax::forgot_password(System::get_post("username"), System::get_post("mail"));		
			break;
			
			case "logout":
				require_once("ajax/login.ajax.php");
				echo LoginAjax::logout();
			break;
			
			case "left_navigation":
				require_once("ajax/navigation/left_navigation.ajax.php");
				
				switch(System::get_get("action")):
					case "set_active":
						echo LeftNavigationAjax::set_active(System::get_post("id"));
					break;
				endswitch;
			break;
			
			
			// Lists
			
			case "list_get_page_information":
				require_once("ajax/list.ajax.php");
				echo ListAjax::get_page_information(System::get_post("number_of_entries"), System::get_post("number_of_pages"));
			break;
			
			case "list_get_page_bar":
				require_once("ajax/list.ajax.php");
				echo ListAjax::get_page_bar(System::get_post("page"), 
											System::get_post("number_of_pages"), 
											System::get_post("css_page_id"));
			break;
			
			
			// Search
			
			case "search_user_list_users":
				require_once("ajax/user_search.ajax.php");
				echo UserSearchAjax::list_users(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "search_user_count_users":
				require_once("ajax/user_search.ajax.php");
				echo UserSearchAjax::count_users(System::get_post("argument_array"));
			break;
			
			case "search_user_list_groups":
				require_once("ajax/user_search.ajax.php");
				echo UserSearchAjax::list_groups(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "search_user_count_groups":
				require_once("ajax/user_search.ajax.php");
				echo UserSearchAjax::count_groups(System::get_post("argument_array"));
			break;
			
			
			// User

			case "get_users_in_option":
				require_once("common/ajax/user_common.ajax.php");
				echo UserCommonAjax::get_users_in_option(System::get_post("string"));
			break;
			
			case "get_groups_in_option":
				require_once("common/ajax/user_common.ajax.php");
				echo UserCommonAjax::get_groups_in_option(System::get_post("string"));
			break;
			
			case "user_profile_personal_data_change":
				require_once("ajax/user.ajax.php");
				echo UserAjax::profile_personal_data_change(System::get_post("gender"), 
						System::get_post("forename"), 
						System::get_post("surname"), 
						System::get_post("title"), 
						System::get_post("mail"), 
						System::get_post("institution"), 
						System::get_post("department"), 
						System::get_post("street"), 
						System::get_post("zip"), 
						System::get_post("city"), 
						System::get_post("country"), 
						System::get_post("phone"), 
						System::get_post("icq"), 
						System::get_post("msn"), 
						System::get_post("yahoo"), 
						System::get_post("aim"), 
						System::get_post("skype"), 
						System::get_post("lync"), 
						System::get_post("jabber")
						);
			break;
			
			case "user_profile_regional_settings_change":
				require_once("ajax/user.ajax.php");
				echo UserAjax::profile_regional_settings_change(System::get_post("language_id"), 
						System::get_post("country_id"), 
						System::get_post("timezone_id"), 
						System::get_post("time_display"), 
						System::get_post("time_enter"), 
						System::get_post("date_display"), 
						System::get_post("date_enter"), 
						System::get_post("system_of_units"), 
						System::get_post("currency_id"), 
						System::get_post("currency_significant_digits"), 
						System::get_post("decimal_separator"), 
						System::get_post("thousand_separator"), 
						System::get_post("name_display_format"), 
						System::get_post("system_of_paper_format")
						);
			break;
			
			case "user_password_change":
				require_once("ajax/user.ajax.php");
				echo UserAjax::password_change(System::get_post("current_password"), 
						System::get_post("new_password_1"), 
						System::get_post("new_password_2")
						);
			break;
			
			// Batch
			
			case "batch_list_batches":
				require_once("ajax/batch.ajax.php");
				echo BatchAjax::list_batches(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "batch_count_batches":
				require_once("ajax/batch.ajax.php");
				echo BatchAjax::count_batches(System::get_post("argument_array"));
			break;
			
			case "batch_start_test":
				require_once("ajax/batch.ajax.php");
				echo BatchAjax::start_test();
			break;
			
			case "batch_start_test_handler":
				require_once("ajax/batch.ajax.php");
				echo BatchAjax::start_test_handler(System::get_post("number_of_batches"));
			break;
			
		endswitch;
	}
	
	/**
	 * @param string $alias
	 * @throws BaseModuleDialogMethodNotFoundException
	 * @throws BaseModuleDialogClassNotFoundException
	 * @throws BaseModuleDialogFileNotFoundException
	 * @throws BaseModuleDialogMissingException
	 */
	public static function io_handler($alias)
	{
		global $user;
		
		if (System::get_get("run") == "common_dialog" and System::get_get("dialog"))
		{
			require_once("common.request.php");
			CommonRequest::common_dialog();
		}
		else
		{
			switch ($alias):
		
				case "search":
					
					switch(System::get_get("run")):
						
						case("search"):
							require_once("io/search.io.php");
							SearchIO::search(System::get_get("dialog"));
						break;
								
						case ("header_search"):
							require_once("io/search.io.php");
							SearchIO::header_search(System::get_post("string"), System::get_post("current_module"));
						break;
						
						default:
							require_once("io/search.io.php");
							SearchIO::main();
						break;
						
					endswitch;
					
				break;
				
				default:

					switch (System::get_get("run")):
								
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
							if (System::get_get("dialog"))
							{
								$module_dialog = ModuleDialog::get_by_type_and_internal_name("base_user_lists", System::get_get("dialog"));
								
								if (file_exists($module_dialog['class_path']))
								{
									require_once($module_dialog['class_path']);
									
									if (class_exists($module_dialog['class']))
									{
										if(method_exists($module_dialog['class'], $module_dialog['method']))
										{
											$method = $module_dialog['method'];
											$module_dialog['class']::$method();
										}
										else
										{
											throw new BaseModuleDialogMethodNotFoundException();
										}
									}
									else
									{
										throw new BaseModuleDialogClassNotFoundException();
									}
								}
								else
								{
									throw new BaseModuleDialogFileNotFoundException();
								}
							}
							else
							{
								throw new BaseModuleDialogMissingException();
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
									
						default:
							require_once("io/home.io.php");
						break;
						
					endswitch;
						
				break;
				
			endswitch;
		}
	}
}
?>