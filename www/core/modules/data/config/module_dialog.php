<?php 
/**
 * @package data
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
	$dialog[0][class_path]		= "core/modules/data/data.io.php";
	$dialog[0]['class']			= "DataIO";
	$dialog[0][method]			= "method_handler";
	$dialog[0][internal_name]	= "data";
	$dialog[0][display_name]	= "Data";
	$dialog[0][weight]			= 300;
	
	$dialog[1][type]			= "item_add";
	$dialog[1][class_path]		= "core/modules/data/value.io.php";
	$dialog[1]['class']			= "ValueIO";
	$dialog[1][method]			= "add_value_item";
	$dialog[1][internal_name]	= "value";
	$dialog[1][display_name]	= "Value";
	
	$dialog[2][type]			= "item_add";
	$dialog[2][class_path]		= "core/modules/data/file.io.php";
	$dialog[2]['class']			= "FileIO";
	$dialog[2][method]			= "upload_as_item";
	$dialog[2][internal_name]	= "file";
	$dialog[2][display_name]	= "File";
?>