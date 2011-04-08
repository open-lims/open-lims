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
$mini_search_paramquery[username] = $_GET[username];
$mini_search_paramquery[session_id] = $_GET[session_id];
$mini_search_paramquery[nav] = "search";
$mini_search_paramquery[run] = "ffv";
$mini_search_paramquery[change_tab] = "true";
$mini_search_paramquery[nextpage] = "1";
$mini_search_params = http_build_query($mini_search_paramquery,'','&#38;');
			
$template->set_var("sub_menu", false);
$template->set_var("search_bar", true);
$template->set_var("mini_search_params", $mini_search_params);

unset($sub_menu);
?>