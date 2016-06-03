<?php 
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
	$dialog[0]['type']				= "standard_navigation";
	$dialog[0]['class_path']		= "core/modules/base/io/navigation/left_navigation.io.php";
	$dialog[0]['class']				= "LeftNavigationIO";
	$dialog[0]['method']			= "create_left_navigation";
	$dialog[0]['internal_name']		= "navigation";
	
	$dialog[1]['type']				= "search";
	$dialog[1]['class_path']		= "core/modules/base/io/user_search.io.php";
	$dialog[1]['class']				= "UserSearchIO";
	$dialog[1]['method']			= "search";
	$dialog[1]['internal_name']		= "user_search";
	$dialog[1]['language_address']	= "BaseDialogUserSearch";
	$dialog[1]['weight']			= 500;
	
	$dialog[2]['type']				= "common_dialog";
	$dialog[2]['class_path']		= "core/modules/base/io/user.io.php";
	$dialog[2]['class']				= "UserIO";
	$dialog[2]['method']			= "user_details";
	$dialog[2]['internal_name']		= "user_detail";
	
	$dialog[3]['type']				= "common_dialog";
	$dialog[3]['class_path']		= "core/modules/base/io/user.io.php";
	$dialog[3]['class']				= "UserIO";
	$dialog[3]['method']			= "group_details";
	$dialog[3]['internal_name']		= "group_detail";
	
	$dialog[4]['type']				= "organisation_admin";
	$dialog[4]['class_path']		= "core/modules/base/io/admin/admin_user.io.php";
	$dialog[4]['class']				= "AdminUserIO";
	$dialog[4]['method']			= "handler";
	$dialog[4]['internal_name']		= "users";
	$dialog[4]['language_address']	= "BaseDialogAdminMenuUser";
	$dialog[4]['weight']			= 100;
	
	$dialog[5]['type']				= "organisation_admin";
	$dialog[5]['class_path']		= "core/modules/base/io/admin/admin_group.io.php";
	$dialog[5]['class']				= "AdminGroupIO";
	$dialog[5]['method']			= "handler";
	$dialog[5]['internal_name']		= "groups";
	$dialog[5]['language_address']	= "BaseDialogAdminMenuGroup";
	$dialog[5]['weight']			= 200;
	
	$dialog[6]['type']				= "admin_home_box";
	$dialog[6]['class_path']		= "core/modules/base/io/admin/admin_user.io.php";
	$dialog[6]['class']				= "AdminUserIO";
	$dialog[6]['method']			= "home_dialog";
	$dialog[6]['internal_name']		= "users";
	$dialog[6]['weight']			= "200";
	
	$dialog[7]['type']				= "admin_home_box";
	$dialog[7]['class_path']		= "core/modules/base/io/admin/admin_group.io.php";
	$dialog[7]['class']				= "AdminGroupIO";
	$dialog[7]['method']			= "home_dialog";
	$dialog[7]['internal_name']		= "groups";
	$dialog[7]['weight']			= 300;
	
	$dialog[8]['type']				= "base_user_lists";
	$dialog[8]['class_path']		= "core/modules/base/io/batch.io.php";
	$dialog[8]['class']				= "BatchIO";
	$dialog[8]['method']			= "list_batches";
	$dialog[8]['internal_name']		= "list_batches";
	$dialog[8]['language_address']	= "BaseDialogBatchTopMenuUserList";
?>