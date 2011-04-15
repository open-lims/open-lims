<?php 
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
	
	$dialog[3][type]			= "admin";
	$dialog[3][class_path]		= "core/modules/sample/admin/admin_sample_depository.io.php";
	$dialog[3]['class']			= "AdminSampleDepositoryIO";
	$dialog[3][method]			= "handler";
	$dialog[3][internal_name]	= "sample_depository";
	$dialog[3][display_name]	= "Sample Depositories";
	
	$dialog[4][type]			= "admin";
	$dialog[4][class_path]		= "core/modules/sample/admin/admin_sample_template_cat.io.php";
	$dialog[4]['class']			= "AdminSampleTemplateCatIO";
	$dialog[4][method]			= "handler";
	$dialog[4][internal_name]	= "sample_template_cat";
	$dialog[4][display_name]	= "Sample Template Cat.";
	
	$dialog[5][type]			= "admin";
	$dialog[5][class_path]		= "core/modules/sample/admin/admin_sample_template.io.php";
	$dialog[5]['class']			= "AdminSampleTemplateIO";
	$dialog[5][method]			= "handler";
	$dialog[5][internal_name]	= "sample_template";
	$dialog[5][display_name]	= "Sample Templates";
	
	$dialog[6][type]			= "search";
	$dialog[6][class_path]		= "core/modules/sample/sample_search.io.php";
	$dialog[6]['class']			= "SampleSearchIO";
	$dialog[6][method]			= "search";
	$dialog[6][internal_name]	= "sample_search";
	$dialog[6][display_name]	= "Sample Search";
	$dialog[6][weight]			= 200;
	
	$dialog[7][type]			= "search";
	$dialog[7][class_path]		= "core/modules/sample/sample_data_search.io.php";
	$dialog[7]['class']			= "SampleDataSearchIO";
	$dialog[7][method]			= "search";
	$dialog[7][internal_name]	= "sample_data_search";
	$dialog[7][display_name]	= "Sample Data Search";
	$dialog[7][weight]			= 400;
	
	$dialog[8][type]			= "parent_item_list";
	$dialog[8][class_path]		= "core/modules/sample/sample.io.php";
	$dialog[8]['class']			= "SampleIO";
	$dialog[8][method]			= "list_samples_by_item_id";
	$dialog[8][internal_name]	= "sample";
	$dialog[8][display_name]	= "Par. Samples";
	$dialog[8][weight]			= 200;
?>