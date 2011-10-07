<?php 
/**
 * @package sample
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
 * 
 */
	$dialog[0][type]			= "item_list";
	$dialog[0][class_path]		= "core/modules/sample/sample.io.php";
	$dialog[0]['class']			= "SampleIO";
	$dialog[0][method]			= "list_sample_items";
	$dialog[0][internal_name]	= "sample";
	$dialog[0][display_name]	= "Samples";
	$dialog[0][weight]			= 100;
	
	$dialog[1][type]			= "item_add";
	$dialog[1][class_path]		= "core/modules/sample/sample.io.php";
	$dialog[1]['class']			= "SampleIO";
	$dialog[1][method]			= "add_sample_item";
	$dialog[1][internal_name]	= "sample";
	$dialog[1][display_name]	= "Sample";
	
	$dialog[2][type]			= "item_add";
	$dialog[2][class_path]		= "core/modules/sample/sample.io.php";
	$dialog[2]['class']			= "SampleIO";
	$dialog[2][method]			= "add_sample_item";
	$dialog[2][internal_name]	= "parentsample";
	$dialog[2][display_name]	= "Parent Sample";
		
	$dialog[3][type]			= "module_admin";
	$dialog[3][class_path]		= "core/modules/sample/admin/admin_sample_template_cat.io.php";
	$dialog[3]['class']			= "AdminSampleTemplateCatIO";
	$dialog[3][method]			= "handler";
	$dialog[3][internal_name]	= "sample_template_cat";
	$dialog[3][display_name]	= "Sample Template Cat.";
	$dialog[3][weight]			= 2100;
	
	$dialog[4][type]			= "module_admin";
	$dialog[4][class_path]		= "core/modules/sample/admin/admin_sample_template.io.php";
	$dialog[4]['class']			= "AdminSampleTemplateIO";
	$dialog[4][method]			= "handler";
	$dialog[4][internal_name]	= "sample_template";
	$dialog[4][display_name]	= "Sample Templates";
	$dialog[4][weight]			= 2200;
	
	$dialog[5][type]			= "search";
	$dialog[5][class_path]		= "core/modules/sample/sample_search.io.php";
	$dialog[5]['class']			= "SampleSearchIO";
	$dialog[5][method]			= "search";
	$dialog[5][internal_name]	= "sample_search";
	$dialog[5][display_name]	= "Sample Search";
	$dialog[5][weight]			= 200;
	
	$dialog[6][type]			= "search";
	$dialog[6][class_path]		= "core/modules/sample/sample_data_search.io.php";
	$dialog[6]['class']			= "SampleDataSearchIO";
	$dialog[6][method]			= "search";
	$dialog[6][internal_name]	= "sample_data_search";
	$dialog[6][display_name]	= "Sample Data Search";
	$dialog[6][weight]			= 400;
	
	$dialog[8][type]			= "parent_item_list";
	$dialog[8][class_path]		= "core/modules/sample/sample.io.php";
	$dialog[8]['class']			= "SampleIO";
	$dialog[8][method]			= "list_samples_by_item_id";
	$dialog[8][internal_name]	= "sample";
	$dialog[8][display_name]	= "Par. Samples";
	$dialog[8][weight]			= 200;
	
	$dialog[9][type]			= "report";
	$dialog[9][class_path]		= "core/modules/sample/sample_report.io.php";
	$dialog[9]['class']			= "SampleReportIO";
	$dialog[9][method]			= "get_full_report";
	$dialog[9][internal_name]	= "sample_full_report";
	$dialog[9][display_name]	= "Sample Full Report";
	
	$dialog[10][type]			= "report";
	$dialog[10][class_path]		= "core/modules/sample/sample_report.io.php";
	$dialog[10]['class']		= "SampleReportIO";
	$dialog[10][method]			= "get_barcode_report";
	$dialog[10][internal_name]	= "sample_barcode_report";
	$dialog[10][display_name]	= "Sample Barcode Report";
	
	$dialog[11][type]			= "item_report";
	$dialog[11][class_path]		= "core/modules/sample/sample_report.io.php";
	$dialog[11]['class']		= "SampleReportIO";
	$dialog[11][method]			= "get_sample_item_report";
	$dialog[11][internal_name]	= "sample_item_report";
	$dialog[11][display_name]	= "Sample Item Report";
	$dialog[11][weight]			= 1000;
	
	$dialog[12][type]			= "item_assistant_list";
	$dialog[12][class_path]		= "core/modules/sample/sample.io.php";
	$dialog[12]['class']		= "SampleIO";
	$dialog[12][method]			= "list_sample_items";
	$dialog[12][internal_name]	= "sample";
	$dialog[12][display_name]	= "Sample";
	$dialog[12][weight]			= 300;
	
	$dialog[13][type]			= "item_parent_assistant_list";
	$dialog[13][class_path]		= "core/modules/sample/sample.io.php";
	$dialog[13]['class']		= "SampleIO";
	$dialog[13][method]			= "list_samples_by_item_id";
	$dialog[13][internal_name]	= "sample";
	$dialog[13][display_name]	= "Sample";
	$dialog[13][weight]			= 200;
?>