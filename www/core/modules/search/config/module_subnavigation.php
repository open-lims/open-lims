<?php
/**
 * @package project
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
$sub_menu = array();

$project_search_paramquery[username] = $_GET[username];
$project_search_paramquery[session_id] = $_GET[session_id];
$project_search_paramquery[nav] = "search";
$project_search_paramquery[run] = "project";
$project_search_paramquery[change_tab] = "true";
$project_search_params = http_build_query($project_search_paramquery,'','&#38;');

$sub_menu[0][params] = $project_search_params;
$sub_menu[0][title] = "Project Search";

$sample_search_paramquery[username] = $_GET[username];
$sample_search_paramquery[session_id] = $_GET[session_id];
$sample_search_paramquery[nav] = "search";
$sample_search_paramquery[run] = "sample";
$sample_search_paramquery[change_tab] = "true";
$sample_search_params = http_build_query($sample_search_paramquery,'','&#38;');

$sub_menu[1][params] = $sample_search_params;
$sub_menu[1][title] = "Sample Search";

$ffv_search_paramquery[username] = $_GET[username];
$ffv_search_paramquery[session_id] = $_GET[session_id];
$ffv_search_paramquery[nav] = "search";
$ffv_search_paramquery[run] = "ffv";
$ffv_search_paramquery[change_tab] = "true";
$ffv_search_params = http_build_query($ffv_search_paramquery,'','&#38;');

$sub_menu[2][params] = $ffv_search_params;
$sub_menu[2][title] = "File/Folder/Value Search";

$data_search_paramquery[username] = $_GET[username];
$data_search_paramquery[session_id] = $_GET[session_id];
$data_search_paramquery[nav] = "search";
$data_search_paramquery[run] = "data";
$data_search_paramquery[change_tab] = "true";
$data_search_params = http_build_query($data_search_paramquery,'','&#38;');

$sub_menu[3][params] = $data_search_params;
$sub_menu[3][title] = "Data Search";

$user_search_paramquery[username] = $_GET[username];
$user_search_paramquery[session_id] = $_GET[session_id];
$user_search_paramquery[nav] = "search";
$user_search_paramquery[run] = "user";
$user_search_paramquery[change_tab] = "true";
$user_search_params = http_build_query($user_search_paramquery,'','&#38;');

$sub_menu[4][params] = $user_search_params;
$sub_menu[4][title] = "User Search";

$full_text_search_paramquery[username] = $_GET[username];
$full_text_search_paramquery[session_id] = $_GET[session_id];
$full_text_search_paramquery[nav] = "search";
$full_text_search_paramquery[run] = "full_text";
$full_text_search_paramquery[change_tab] = "true";
$full_text_search_params = http_build_query($full_text_search_paramquery,'','&#38;');

$sub_menu[5][params] = $full_text_search_params;
$sub_menu[5][title] = "Full Text Search";
			
$template->set_var("sub_menu", $sub_menu);
$template->set_var("search_bar", false);

unset($sub_menu);
?>