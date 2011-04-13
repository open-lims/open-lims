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
	$dialog[0][type]			= "project_list";
	$dialog[0][class_path]		= "core/modules/project/project.io.php";
	$dialog[0]['class']			= "ProjectIO";
	$dialog[0][method]			= "list_projects_by_item_id";
	$dialog[0][internal_name]	= "project";
	$dialog[0][display_name]	= "Projects";
	
	$dialog[1][type]			= "search";
	$dialog[1][class_path]		= "core/modules/project/project_search.io.php";
	$dialog[1]['class']			= "ProjectSearchIO";
	$dialog[1][method]			= "search";
	$dialog[1][internal_name]	= "project_search";
	$dialog[1][display_name]	= "Project Search";
	$dialog[1][weight]			= 100;
	
	$dialog[2][type]			= "search";
	$dialog[2][class_path]		= "core/modules/project/project_data_search.io.php";
	$dialog[2]['class']			= "ProjectDataSearchIO";
	$dialog[2][method]			= "search";
	$dialog[2][internal_name]	= "project_data_search";
	$dialog[2][display_name]	= "Project Data Search";
	$dialog[2][weight]			= 300;
?>