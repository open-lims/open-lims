<?php
/**
 * @package data
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
		switch($_GET['run']):
			
			case "list_data_browser":
				require_once("ajax/data.ajax.php");
				echo DataAjax::list_data_browser(
						$_POST['column_array'], 
						$_POST['argument_array'], 
						$_POST['get_array'], 
						$_POST['css_page_id'], 
						$_POST['css_row_sort_id'], 
						$_POST['entries_per_page'], 
						$_GET['page'], 
						$_GET['sortvalue'], 
						$_GET['sortmethod']
						);
			break;
			
			case "count_data_browser":
				require_once("ajax/data.ajax.php");
				echo DataAjax::count_data_browser($_POST['argument_array']);
			break;
			
			case "list_file_items":
				require_once("ajax/file.ajax.php");
				echo FileAjax::list_file_items(
						$_POST['column_array'], 
						$_POST['argument_array'], 
						$_POST['css_page_id'],  
						$_POST['css_row_sort_id'], 
						$_GET['page'], 
						$_GET['sortvalue'], 
						$_GET['sortmethod']
						);
			break;
			
			
			// Data Browser
			
			case "get_data_browser_path":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_data_browser_path($_POST['folder_id'],$_POST['virtual_folder_id']);
			break;
			
			case "get_data_browser_path_cleared":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_data_browser_path_cleared($_POST['folder_id'],$_POST['virtual_folder_id']);
			break;
			
			case "get_context_sensitive_file_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_context_sensitive_file_menu($_POST['file_id']);
			break;
			
			case "get_context_sensitive_folder_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_context_sensitive_folder_menu($_POST['file_id']);
			break;
			
			case "get_context_sensitive_value_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_context_sensitive_value_menu($_POST['file_id']);
			break;
			
			case "get_browser_menu":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::get_browser_menu($_POST['folder_id']);
			break;
			
			case "delete_stack":
				require_once("ajax/data_browser.ajax.php");
				echo DataBrowserAjax::delete_stack();
			break;
			
			
			// File
			
			case "file_list_versions":
				require_once("ajax/file.ajax.php");
				echo FileAjax::list_versions(
						$_POST['column_array'], 
						$_POST['argument_array'], 
						$_POST['get_array'], 
						$_POST['css_page_id'], 
						$_POST['css_row_sort_id'], 
						$_POST['entries_per_page'], 
						$_GET['page'], 
						$_GET['sortvalue'], 
						$_GET['sortmethod']
						);
			break;
			
			case "file_count_versions":
				require_once("ajax/file.ajax.php");
				echo FileAjax::count_versions($_POST['argument_array']);
			break;
			
			case "file_add":
				require_once("ajax/file.ajax.php");
				echo FileAjax::add_file($_POST['folder_id']);
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
						$_POST['column_array'], 
						$_POST['argument_array'], 
						$_POST['get_array'], 
						$_POST['css_page_id'], 
						$_POST['css_row_sort_id'], 
						$_POST['entries_per_page'], 
						$_GET['page'], 
						$_GET['sortvalue'], 
						$_GET['sortmethod']
						);
			break;
			
			case "value_count_versions":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::count_versions($_POST['argument_array']);
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
						$_POST['folder_id'], 
						$_POST['type_id'], 
						$_POST['value_array'], 
						$_POST['get_array']
						);
			break;
			
			case "value_add_as_item_window":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::add_as_item_window($_POST['get_array'], $_POST['type_array'], $_POST['folder_id']);
			break;
			
			case "value_update":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::update(
						$_POST['value_id'], 
						$_POST['version'], 
						$_POST['value_array'], 
						true
						);
			break;
			
			case "value_update_minor":
				require_once("ajax/value.ajax.php");
				echo ValueAjax::update(
						$_POST['value_id'], 
						$_POST['version'], 
						$_POST['value_array'], 
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
			
			
			// Search 
			
			case "search_data_list_data":
				require_once("ajax/data_search.ajax.php");
				echo DataSearchAjax::list_data(
						$_POST['column_array'], 
						$_POST['argument_array'], 
						$_POST['css_page_id'],  
						$_POST['css_row_sort_id'], 
						$_POST['entries_per_page'], 
						$_GET['page'], 
						$_GET['sortvalue'], 
						$_GET['sortmethod']
						);
			break;
			
			case "search_data_count_data":
				require_once("ajax/data_search.ajax.php");
				echo DataSearchAjax::count_data($_POST['argument_array']);
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
						$_POST['column_array'], 
						$_POST['argument_array'], 
						$_POST['get_array'], 
						$_POST['css_page_id'], 
						$_POST['css_row_sort_id'], 
						$_POST['entries_per_page'], 
						$_GET['page'], 
						$_GET['sortvalue'], 
						$_GET['sortmethod']
						);
			break;
			
			case "admin_count_value_templates":
				require_once("ajax/admin/admin_value_template.ajax.php");
				echo AdminValueTemplateAjax::count_templates($_POST['argument_array']);
			break;	
			
			
			
			case "admin_list_parameter_templates":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::list_templates(
						$_POST['column_array'], 
						$_POST['argument_array'], 
						$_POST['get_array'], 
						$_POST['css_page_id'], 
						$_POST['css_row_sort_id'], 
						$_POST['entries_per_page'], 
						$_GET['page'], 
						$_GET['sortvalue'], 
						$_GET['sortmethod']
						);
			break;
			
			case "admin_count_parameter_templates":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::count_templates($_POST['argument_array']);
			break;	
			
			case "admin_add_parameter_template":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::add_template($_POST['name'], $_POST['internal_name'], $_POST['json_object_string'], $_POST['json_limit_string']);
			break;
			
			case "admin_edit_parameter_template":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::edit_template($_GET['id'], $_POST['name'], $_POST['json_object_string'], $_POST['json_limit_string']);
			break;
			
			case "admin_delete_parameter_template":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::delete_template($_POST['id']);
			break;
			
			case "admin_parameter_template_exist_internal_name":
				require_once("ajax/admin/admin_parameter_template.ajax.php");
				echo AdminParameterTemplateAjax::exist_internal_name($_POST['internal_name']);
			break;

			
			// Navigation
			
			case "navigation_data":
				require_once("ajax/navigation/data_navigation.ajax.php");
				switch($_GET['action']):
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
						echo DataNavigationAjax::set_array($_POST['array']);
					break;
				
					case "get_children":
						echo DataNavigationAjax::get_children($_POST['id']);
					break;
				endswitch;
			break;
			
			case "navigation_folder":
				require_once("ajax/folder.ajax.php");
				
				switch($_GET['action']):
					case "get_array":
						echo FolderAjax::get_array();
					break;
					
					case "get_children":
						echo FolderAjax::get_children($_POST['id']);
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
		switch($_GET['action']):
		
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
			
			// Common Dialogs
			case("common_dialog"):
				require_once("core/modules/base/common.request.php");
				CommonRequest::common_dialog();
			break;
			
			default:
				require_once("io/data.io.php");
				DataIO::browser();
			break;
			
		endswitch;	
	}
}
?>