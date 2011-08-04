<?php 
/**
 * @package user
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
	$dialog[0][type]			= "search";
	$dialog[0][class_path]		= "core/modules/user/user_search.io.php";
	$dialog[0]['class']			= "UserSearchIO";
	$dialog[0][method]			= "search";
	$dialog[0][internal_name]	= "user_search";
	$dialog[0][display_name]	= "User Search";
	$dialog[0][weight]			= 500;
	
	$dialog[1][type]			= "common_dialog";
	$dialog[1][class_path]		= "core/modules/user/user.io.php";
	$dialog[1]['class']			= "UserIO";
	$dialog[1][method]			= "user_details";
	$dialog[1][internal_name]	= "user_detail";
	$dialog[1][display_name]	= "User Detail";
	
	$dialog[2][type]			= "common_dialog";
	$dialog[2][class_path]		= "core/modules/user/user.io.php";
	$dialog[2]['class']			= "UserIO";
	$dialog[2][method]			= "group_details";
	$dialog[2][internal_name]	= "group_detail";
	$dialog[2][display_name]	= "Group Detail";
	
	$dialog[3][type]			= "organisation_admin";
	$dialog[3][class_path]		= "core/modules/user/admin/admin_user.io.php";
	$dialog[3]['class']			= "AdminUserIO";
	$dialog[3][method]			= "handler";
	$dialog[3][internal_name]	= "users";
	$dialog[3][display_name]	= "Users";
	$dialog[3][weight]			= 100;
	
	$dialog[4][type]			= "organisation_admin";
	$dialog[4][class_path]		= "core/modules/user/admin/admin_group.io.php";
	$dialog[4]['class']			= "AdminGroupIO";
	$dialog[4][method]			= "handler";
	$dialog[4][internal_name]	= "groups";
	$dialog[4][display_name]	= "Groups";
	$dialog[4][weight]			= 200;
	
	$dialog[5][type]			= "admin_home_box";
	$dialog[5][class_path]		= "core/modules/user/admin/admin_user.io.php";
	$dialog[5]['class']			= "AdminUserIO";
	$dialog[5][method]			= "home_dialog";
	$dialog[5][internal_name]	= "users";
	$dialog[5][display_name]	= "Users";
	$dialog[5][weight]			= "200";
	
	$dialog[6][type]			= "admin_home_box";
	$dialog[6][class_path]		= "core/modules/user/admin/admin_group.io.php";
	$dialog[6]['class']			= "AdminGroupIO";
	$dialog[6][method]			= "home_dialog";
	$dialog[6][internal_name]	= "groups";
	$dialog[6][display_name]	= "Groups";
	$dialog[6][weight]			= 300;
?>