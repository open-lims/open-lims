<?php 
/**
 * @package project
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
	$classes['ProjectException']									= $path_prefix."core/include/project/exceptions/project_exception.class.php";
	
	$classes['ProjectCreateException']								= $path_prefix."core/include/project/exceptions/project_create_exception.class.php";
	$classes['ProjectCreateProjectExistsException']					= $path_prefix."core/include/project/exceptions/project_create_project_exists_exception.class.php";
	$classes['ProjectCreateStatusException']						= $path_prefix."core/include/project/exceptions/project_create_status_exception.class.php";
	$classes['ProjectCreateFolderException']						= $path_prefix."core/include/project/exceptions/project_create_folder_exception.class.php";
	$classes['ProjectCreateStatusFolderException']					= $path_prefix."core/include/project/exceptions/project_create_status_folder_exception.class.php";
	$classes['ProjectCreateStatusSubFolderException']				= $path_prefix."core/include/project/exceptions/project_create_status_sub_folder_exception.class.php";
	$classes['ProjectCreateSupplementaryFolderException']			= $path_prefix."core/include/project/exceptions/project_create_supplementary_folder_exception.class.php";
	$classes['ProjectCreateDescriptionException']					= $path_prefix."core/include/project/exceptions/project_create_description_exception.class.php";
	$classes['ProjectCreateMasterDataException']					= $path_prefix."core/include/project/exceptions/project_create_master_data_exception.class.php";
	$classes['ProjectCreatePermissionUserException']				= $path_prefix."core/include/project/exceptions/project_create_permission_user_exception.class.php";
	$classes['ProjectCreatePermissionLeaderException']				= $path_prefix."core/include/project/exceptions/project_create_permission_leader_exception.class.php";
	$classes['ProjectCreatePermissionGroupException']				= $path_prefix."core/include/project/exceptions/project_create_permission_group_exception.class.php";
	$classes['ProjectCreatePermissionOrganisationUnitException']	= $path_prefix."core/include/project/exceptions/project_create_permission_organisation_unit_exception.class.php";
	$classes['ProjectCreatePermissionQualityManagerException']		= $path_prefix."core/include/project/exceptions/project_create_permission_quality_manager_exception.class.php";
	
	$classes['ProjectSecurityException']	= $path_prefix."core/include/project/exceptions/project_security_exception.class.php";
		
	$classes['Project'] 					= $path_prefix."core/include/project/project.class.php";
	$classes['ProjectFolder'] 				= $path_prefix."core/include/project/project_folder.class.php";
	$classes['ProjectVirtualFolder'] 		= $path_prefix."core/include/project/project_virtual_folder.class.php";
	$classes['ProjectItem'] 				= $path_prefix."core/include/project/project_item.class.php";
	$classes['ProjectItemFactory'] 			= $path_prefix."core/include/project/project_item_factory.class.php";
	$classes['ProjectLog']					= $path_prefix."core/include/project/project_log.class.php";
	$classes['ProjectLogHasItem']			= $path_prefix."core/include/project/project_log_has_item.class.php";
	$classes['ProjectPermission'] 			= $path_prefix."core/include/project/project_permission.class.php";
	$classes['ProjectSecurity'] 			= $path_prefix."core/include/project/project_security.class.php";
	$classes['ProjectStatus']				= $path_prefix."core/include/project/project_status.class.php";
	$classes['ProjectStatusFolder']			= $path_prefix."core/include/project/project_status_folder.class.php";
	$classes['ProjectStatusRelation']		= $path_prefix."core/include/project/project_status_relation.class.php";
	$classes['ProjectTask']					= $path_prefix."core/include/project/project_task.class.php";
	$classes['ProjectTaskPoint']			= $path_prefix."core/include/project/project_task_point.class.php";
	$classes['ProjectTemplate']				= $path_prefix."core/include/project/project_template.class.php";
	$classes['ProjectTemplateCat']			= $path_prefix."core/include/project/project_template_cat.class.php";
	$classes['ProjectUserData']				= $path_prefix."core/include/project/project_user_data.class.php";
	$classes['ProjectValueVar']				= $path_prefix."core/include/project/project_value_var.class.php";
	
	$classes['Project_Wrapper']				= $path_prefix."core/include/project/project.wrapper.class.php";
?>