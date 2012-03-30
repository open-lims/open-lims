<?php
/**
 * @package sample
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
 * 
 */
$sub_menu = array();

$new_sample_paramquery[username] = $_GET[username];
$new_sample_paramquery[session_id] = $_GET[session_id];
$new_sample_paramquery[nav] = "sample";
$new_sample_paramquery[run] = "new";
$new_sample_params = http_build_query($new_sample_paramquery,'','&#38;');

$sub_menu[0][params] = $new_sample_params;
$sub_menu[0][title] = "New Sample";


$new_sample_paramquery[username] = $_GET[username];
$new_sample_paramquery[session_id] = $_GET[session_id];
$new_sample_paramquery[nav] = "sample";
$new_sample_paramquery[run] = "clone";
$new_sample_params = http_build_query($new_sample_paramquery,'','&#38;');

$sub_menu[1][params] = $new_sample_params;
$sub_menu[1][title] = "Clone Sample";


$my_samples_paramquery[username] = $_GET[username];
$my_samples_paramquery[session_id] = $_GET[session_id];
$my_samples_paramquery[nav] = "sample";
$my_samples_params = http_build_query($my_samples_paramquery,'','&#38;');

$sub_menu[2][params] = $my_samples_params;
$sub_menu[2][title] = "My Samples";


$manufacturer_paramquery[username] = $_GET[username];
$manufacturer_paramquery[session_id] = $_GET[session_id];
$manufacturer_paramquery[nav] = "sample";
$manufacturer_paramquery[run] = "common_dialog";
$manufacturer_paramquery[dialog] = "list_manufacturers";
$manufacturer_params = http_build_query($manufacturer_paramquery,'','&#38;');

$sub_menu[3][params] = $manufacturer_params;
$sub_menu[3][title] = "Manufacturer List";
?>