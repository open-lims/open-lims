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
 * Data Request Class
 * @package data
 */
class DataRequest
{
	/**
	 * @param string $alias
	 */
	public static function ajax_handler($alias)
	{		
		switch(System::get_get("run")):
			
			case "list_data_browser":
				require_once("ajax/data.ajax.php");
				echo DataAjax::list_data_browser(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("'css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_data_browser":
				require_once("ajax/data.ajax.php");
				echo DataAjax::count_data_browser(System::get_post("argument_array"));
			break;
			
			case "list_file_items":
				require_once("ajax/file.ajax.php");
				echo FileAjax::list_file_items(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"),  
						System::get_post("css_row_sort_id"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			
			// Data Browser
			
			case "get_data_browser_path":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_data_browser_path(System::get_post("folder_id"),
															System::get_post("virtual_folder_id"));
			break;
			
			case "get_data_browser_path_cleared":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_data_browser_path_cleared(System::get_post("folder_id"),
																	System::get_post("virtual_folder_id"));
			break;
			
			case "get_context_sensitive_file_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_context_sensitive_file_menu(System::get_post("id"));
			break;
			
			case "get_context_sensitive_folder_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_context_sensitive_folder_menu(System::get_post("id"));
			break;
			
			case "get_context_sensitive_value_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_context_sensitive_value_menu(System::get_post("id"));
			break;
			
			case "get_context_sensitive_parameter_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_context_sensitive_parameter_menu(System::get_post("id"));
			break;
			
			case "get_browser_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_browser_menu(System::get_post("folder_id"));
			break;
			
			case "delete_stack":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::delete_stack();
			break;
			
			
			// File
			
			case "file_list_versions":
				require_once("ajax/file.ajax.php");
				echo FileAjax::list_versions(
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
			
			case "file_count_versions":
				require_once("ajax/file.ajax.php");
				echo FileAjax::count_versions(System::get_post("argument_array"));
			break;
			
			case "file_add":
				require_once("ajax/file.ajax.php");
				echo FileAjax::add_file(System::get_post("folder_id"));
			break;

			case "file_delete":
				require_once("ajax/file.ajax.php");
				echo FileAjax::get_data_browser_link_html_and_button_handler("file_delete");
			break;
			
			case "file_update":
				require_once("ajax/file.ajax.php");
				echo FileAjax::get_data_browser_link_html_and_button_handler("file_update");
			break;
			
			case "file_update_minor":
				require_once("ajax/file.ajax.php");
				echo FileAjax::get_data_browser_link_html_and_button_handler("file_update_minor");
			break;
			
			case "file_permission":
				require_once("ajax/file.ajax.php");
				echo FileAjax::get_data_browser_link_html_and_button_handler("permission");
			break;
			
			
			// Value
			
			case "value_list_versions":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::list_versions(
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
			
			case "value_count_versions":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::count_versions(System::get_post("argument_array"));
			break;
			
			case "value_add":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::get_data_browser_link_html_and_button_handler("value_add");
			break;
			
			case "value_delete":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::get_data_browser_link_html_and_button_handler("value_delete");
			break;
			
			case "value_permission":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::get_data_browser_link_html_and_button_handler("permission");
			break;
			
			case "value_add_as_item":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::add_as_item(
						System::get_post("folder_id"), 
						System::get_post("type_id"), 
						System::get_post("value_array"), 
						System::get_post("get_array")
						);
			break;
			
			case "value_add_as_item_window":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::add_as_item_window(System::get_post("get_array"), 
													System::get_post("type_array"), 
													System::get_post("folder_id"));
			break;
			
			case "value_update":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::update(
						System::get_post("value_id"), 
						System::get_post("version"), 
						System::get_post("value_array"), 
						true
						);
			break;
			
			case "value_update_minor":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::update(
						System::get_post("value_id"), 
						System::get_post("version"), 
						System::get_post("value_array"), 
						false
						);
			break;
			
			
			// Folder
			
			case "folder_add":
				require_once("ajax/folder.ajax.php");
				echo FolderAjax::get_data_browser_link_html_and_button_handler("folder_add");
			break;
			
			case "folder_delete":
				require_once("ajax/folder.ajax.php");
				echo FolderAjax::get_data_browser_link_html_and_button_handler("folder_delete");
			break;
			
			case "folder_rename":
				require_once("ajax/folder.ajax.php");
				echo FolderAjax::get_data_browser_link_html_and_button_handler("folder_rename");
			break;
			
			case "folder_permission":
				require_once("ajax/folder.ajax.php");
				echo FolderAjax::get_data_browser_link_html_and_button_handler("permission");
			break;
			
			
			// Parameter
			
			case "parameter_list_versions":
				require_once("ajax/parameter.ajax.php");
				echo ParameterAjax::list_versions(
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
			
			case "parameter_count_versions":
				require_once("ajax/parameter.ajax.php");
				echo ParameterAjax::count_versions(System::get_post("argument_array"));
			break;
			
			case "parameter_add_as_item":
				require_once("ajax/parameter.ajax.php");
				echo ParameterAjax::add_as_item(
						System::get_post("folder_id"), 
						System::get_post("type_id"), 
						System::get_post("limit_id"), 
						System::get_post("parameter_array"), 
						System::get_post("get_array")
						);
			break;
			
			case "parameter_update":
				require_once("ajax/parameter.ajax.php");
				echo ParameterAjax::update(
						System::get_get("parameter_id"), 
						System::get_post("parameter_array"), 
						System::get_post("limit_id"), 
						System::get_post("major"), 
						System::get_post("current")
						);
			break;
			
			case "parameter_get_limits":
				require_once("ajax/parameter.ajax.php");
				echo ParameterAjax::get_limits(
						System::get_post("parameter_template_id"), 
						System::get_post("parameter_limit_id")
						);
			break;
			
			case "parameter_get_methods":
				require_once("ajax/parameter.ajax.php");
				echo ParameterAjax::get_methods();
			break;
			
			case "parameter_delete":
				require_once("ajax/parameter.ajax.php");
				echo ParameterAjax::get_data_browser_link_html_and_button_handler("parameter_delete");
			break;
			
			case "parameter_permission":
				require_once("ajax/parameter.ajax.php");
				echo ParameterAjax::get_data_browser_link_html_and_button_handler("permission");
			break;
			
			
			// Search 
			
			case "search_data_list_data":
				require_once("ajax/data_search.ajax.php");
				echo DataSearchAjax::list_data(
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
			
			case "search_data_count_data":
				require_once("ajax/data_search.ajax.php");
				echo DataSearchAjax::count_data(System::get_post("argument_array"));
			break;
			
			
			// Image Types
			
			case "get_allowed_image_types":
				require_once("ajax/data.ajax.php");
				echo DataAjax::get_allowed_image_types();
			break;
			
			
			// Admin
			
			case "admin_list_value_templates":
				require_once("ajax/admin/admin_value_template.ajax.php");
				echo AdminValueTemplateAjax::list_templates(
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
			
			case "admin_count_value_templates":
				require_once("ajax/admin/admin_value_template.ajax.php");
				echo AdminValueTemplateAjax::count_templates(System::get_post("argument_array"));
			break;	
			
			
			case "admin_list_parameter_templates":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::list_templates(
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
			
			case "admin_count_parameter_templates":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::count_templates(System::get_post("argument_array"));
			break;	
			
			case "admin_add_parameter_template":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::add_template(System::get_post("name"), 
																System::get_post("internal_name"), 
																System::get_post("json_object_string"), 
																System::get_post("json_limit_string"));
			break;
			
			case "admin_edit_parameter_template":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::edit_template(System::get_get("id"), 
																System::get_post("name"), 
																System::get_post("json_object_string"), 
																System::get_post("json_limit_string"));
			break;
			
			case "admin_delete_parameter_template":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::delete_template(System::get_post("id"));
			break;
			
			case "admin_parameter_template_exist_internal_name":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::exist_internal_name(System::get_post("internal_name"));
			break;

			case "admin_list_parameter_methods":
				require_once("ajax/admin/admin_parameter_method.ajax.php");
				echo AdminParameterMethodAjax::list_methods(
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
			
			case "admin_count_parameter_methods":
				require_once("ajax/admin/admin_parameter_method.ajax.php");
				echo AdminParameterMethodAjax::count_methods(System::get_post("argument_array"));
			break;	
			
			case "admin_add_parameter_method":
				require_once("ajax/admin/admin_parameter_method.ajax.php");
				echo AdminParameterMethodAjax::add_method(System::get_post("name"));
			break;
			
			case "admin_edit_parameter_method":
				require_once("ajax/admin/admin_parameter_method.ajax.php");
				echo AdminParameterMethodAjax::edit_method(System::get_post("id"), 
															System::get_post("name"));
			break;
			
			case "admin_delete_parameter_method":
				require_once("ajax/admin/admin_parameter_method.ajax.php");
				echo AdminParameterMethodAjax::delete_method(System::get_post("id"));
			break;
			
			case "admin_parameter_method_get_name":
				require_once("ajax/admin/admin_parameter_method.ajax.php");
				echo AdminParameterMethodAjax::get_name(System::get_post("id"));
			break;
			
			case "admin_parameter_method_exist_name":
				require_once("ajax/admin/admin_parameter_method.ajax.php");
				echo AdminParameterMethodAjax::exist_name(System::get_post("name"));
			break;
			
			
			// Navigation
			
			case "navigation_data":
				require_once("ajax/navigation/data_navigation.ajax.php");
				
				switch(System::get_get("action")):
				
					case "get_name":
						echo DataNavigationAjax::get_name();
					break;
					
					case "get_html":
						echo DataNavigationAjax::get_html();
					break;
					
					case "get_array":
						echo DataNavigationAjax::get_array();
					break;
					
					case "set_array":
						echo DataNavigationAjax::set_array(System::get_post("array"));
					break;
				
					case "get_children":
						echo DataNavigationAjax::get_children(System::get_post("id"));
					break;
					
				endswitch;
				
			break;
			
			case "navigation_folder":
				require_once("ajax/folder.ajax.php");
				
				switch(System::get_get("action")):
				
					case "get_array":
						echo FolderAjax::get_array();
					break;
					
					case "get_children":
						echo FolderAjax::get_children(System::get_post("id"));
					break;
					
				endswitch;
				
			break;
					
		endswitch;
		
	}
	
	/**
	 * @param string $alias
	 */
	public static function io_handler($alias)
	{	
		switch(System::get_get("action")):
		
			// General
			case("permission"):
				require_once("io/data.io.php");
				DataIO::permission();
			break;
			
			case("chown"):
				require_once("io/data.io.php");
				DataIO::change_owner();
			break;
			
			case("chgroup"):
				require_once("io/data.io.php");
				DataIO::change_group();
			break;

			case("image_browser_detail"):
				require_once("io/data.io.php");
				DataIO::image_browser_detail();
			break;
			
			case("image_browser_multi"):
				require_once("io/data.io.php");
				DataIO::image_browser_multi();
			break;

			
			// Values
			case("value_detail"):
				require_once("io/value.io.php");
				ValueIO::detail();
			break;
			
			case("value_history"):
				require_once("io/value.io.php");
				ValueIO::history();
			break;
				
			case("value_delete_version"):
				require_once("io/value.io.php");
				ValueIO::delete_version();
			break;

			
			// File
			case("file_add"):
				require_once("io/file.io.php");
				FileIO::upload();
			break;
			
			case("file_update"):
			case("file_update_minor"):
				require_once("io/file.io.php");
				FileIO::update();
			break;

			case("file_detail"):
				require_once("io/file.io.php");
				FileIO::detail();
			break;
			
			case("file_history"):
				require_once("io/file.io.php");
				FileIO::history();
			break;
			
			case("file_delete"):
				require_once("io/file.io.php");
				FileIO::delete();
			break;
			
			case("file_delete_version"):
				require_once("io/file.io.php");
				FileIO::delete_version();
			break;
			
			
			// Parameter
			case("parameter_detail"):
				require_once("io/parameter.io.php");
				ParameterIO::detail();
			break;
			
			case("parameter_history"):
				require_once("io/parameter.io.php");
				ParameterIO::history();
			break;
			
			
			// Common Dialogs
			case("common_dialog"):
				require_once("core/modules/base/common.request.php");
				CommonRequest::common_dialog();
			break;
			
			
			// Default
			default:
				require_once("io/data.io.php");
				DataIO::browser();
			break;
				
		endswitch;	

	}
}
?>