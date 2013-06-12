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
 * 
 */
	$dialog[0]['type']				= "item_list";
	$dialog[0]['class_path']		= "core/modules/data/data.request.php";
	$dialog[0]['class']				= "DataRequest";
	$dialog[0]['method']			= "io_handler";
	$dialog[0]['internal_name']		= "data";
	$dialog[0]['language_address']	= "DataDialogItemDataList";
	$dialog[0]['weight']			= 300;
	
	$dialog[1]['type']				= "item_add";
	$dialog[1]['class_path']		= "core/modules/data/io/value.io.php";
	$dialog[1]['class']				= "ValueIO";
	$dialog[1]['method']			= "add_value_item";
	$dialog[1]['internal_name']		= "value";
	
	$dialog[2]['type']				= "item_add";
	$dialog[2]['class_path']		= "core/modules/data/io/file.io.php";
	$dialog[2]['class']				= "FileIO";
	$dialog[2]['method']			= "upload_as_item";
	$dialog[2]['internal_name']		= "file";
	
	$dialog[3]['type']				= "search";
	$dialog[3]['class_path']		= "core/modules/data/io/data_search.io.php";
	$dialog[3]['class']				= "DataSearchIO";
	$dialog[3]['method']			= "search";
	$dialog[3]['internal_name']		= "ffv_search";
	$dialog[3]['language_address']	= "DataDialogFFVSearch";
	$dialog[3]['weight']			= 600;
	
	$dialog[4]['type']				= "module_admin";
	$dialog[4]['class_path']		= "core/modules/data/io/admin/admin_value_template.io.php";
	$dialog[4]['class']				= "AdminValueTemplateIO";
	$dialog[4]['method']			= "handler";
	$dialog[4]['internal_name']		= "value_template";
	$dialog[4]['language_address']	= "DataDialogAdminMenuValueTemplates";
	$dialog[4]['weight']			= 100;
	
	$dialog[5]['type']				= "admin_home_box";
	$dialog[5]['class_path']		= "core/modules/data/io/admin/admin_data.io.php";
	$dialog[5]['class']				= "AdminDataIO";
	$dialog[5]['method']			= "home_dialog";
	$dialog[5]['internal_name']		= "data";
	$dialog[5]['weight']			= "100";
	
	$dialog[6]['type']				= "user_module_detail_setting";
	$dialog[6]['class_path']		= "core/modules/data/io/data.io.php";
	$dialog[6]['class']				= "DataIO";
	$dialog[6]['method']			= "get_user_module_detail_setting";
	$dialog[6]['internal_name']		= "user_quota";
	$dialog[6]['language_address']	= "DataDialogModuleDetailUserQuota";
	$dialog[6]['weight']			= 100;
	
	$dialog[7]['type']				= "module_value_change";
	$dialog[7]['class_path']		= "core/modules/data/io/data.io.php";
	$dialog[7]['class']				= "DataIO";
	$dialog[7]['method']			= "change_quota";
	$dialog[7]['internal_name']		= "user_quota";
	$dialog[7]['language_address']	= "DataDialogModuleValueChangeUserQuota";
	
	$dialog[8]['type']				= "common_dialog";
	$dialog[8]['class_path']		= "core/modules/data/io/file.io.php";
	$dialog[8]['class']				= "FileIO";
	$dialog[8]['method']			= "upload";
	$dialog[8]['internal_name']		= "file_add";
		
	$dialog[9]['type']				= "base_left_navigation";
	$dialog[9]['class_path']		= "core/modules/data/io/navigation/data_navigation.io.php";
	$dialog[9]['class']				= "DataNavigationIO";
	$dialog[9]['method']			= "get_html";
	$dialog[9]['internal_name']		= "data";
	$dialog[9]['language_address']	= "DataDialogLeftNavigation";
	$dialog[9]['weight']			= 400;
	
	$dialog[10]['type']				= "item_report";
	$dialog[10]['class_path']		= "core/modules/data/report/data_report.io.php";
	$dialog[10]['class']			= "DataReportIO";
	$dialog[10]['method']			= "get_data_item_report";
	$dialog[10]['internal_name']	= "data_item_report";
	$dialog[10]['weight']			= 100;
	
	$dialog[11]['type']				= "item_assistant_list";
	$dialog[11]['class_path']		= "core/modules/data/io/file.io.php";
	$dialog[11]['class']			= "FileIO";
	$dialog[11]['method']			= "list_file_items";
	$dialog[11]['internal_name']	= "data";
	$dialog[11]['weight']			= 100;
	
	$dialog[12]['type']				= "home_summary_left";
	$dialog[12]['class_path']		= "core/modules/data/io/data_home.io.php";
	$dialog[12]['class']			= "DataHomeIO";
	$dialog[12]['method']			= "quota";
	$dialog[12]['internal_name']	= "data";
	$dialog[12]['weight']			= 300;
	
	$dialog[13]['type']				= "home_summary_right";
	$dialog[13]['class_path']		= "core/modules/data/io/data_home.io.php";
	$dialog[13]['class']			= "DataHomeIO";
	$dialog[13]['method']			= "used_diskspace";
	$dialog[13]['internal_name']	= "data";
	$dialog[13]['weight']			= 300;
	
	$dialog[14]['type']				= "item_edit";
	$dialog[14]['class_path']		= "core/modules/data/io/value.io.php";
	$dialog[14]['class']			= "ValueIO";
	$dialog[14]['method']			= "edit_value_item";
	$dialog[14]['internal_name']	= "value";
	
	$dialog[15]['type']				= "standard_search";
	$dialog[15]['class_path']		= "core/modules/data/io/data_search.io.php";
	$dialog[15]['class']			= "DataSearchIO";
	$dialog[15]['method']			= "search";
	$dialog[15]['internal_name']	= "ffv_search";
	
	$dialog[16]['type']				= "module_admin";
	$dialog[16]['class_path']		= "core/modules/data/io/admin/admin_parameter_template.io.php";
	$dialog[16]['class']			= "AdminParameterTemplateIO";
	$dialog[16]['method']			= "handler";
	$dialog[16]['internal_name']	= "parameter_template";
	$dialog[16]['language_address']	= "DataDialogAdminMenuParameterTemplates";
	$dialog[16]['weight']			= 200;
	
	$dialog[17]['type']				= "module_admin";
	$dialog[17]['class_path']		= "core/modules/data/io/admin/admin_parameter_method.io.php";
	$dialog[17]['class']			= "AdminParameterMethodIO";
	$dialog[17]['method']			= "handler";
	$dialog[17]['internal_name']	= "parameter_method";
	$dialog[17]['language_address']	= "DataDialogAdminMenuParameterMethods";
	$dialog[17]['weight']			= 200;
	
	$dialog[18]['type']				= "item_add";
	$dialog[18]['class_path']		= "core/modules/data/io/parameter.io.php";
	$dialog[18]['class']			= "ParameterIO";
	$dialog[18]['method']			= "add_parameter_item";
	$dialog[18]['internal_name']	= "parameter";
	
	$dialog[19]['type']				= "item_edit";
	$dialog[19]['class_path']		= "core/modules/data/io/parameter.io.php";
	$dialog[19]['class']			= "ParameterIO";
	$dialog[19]['method']			= "edit_parameter_item";
	$dialog[19]['internal_name']	= "parameter";
?>