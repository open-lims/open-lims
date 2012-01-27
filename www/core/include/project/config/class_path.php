<?php 
/**
 * @package project
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
	$classes['ProjectException']												= $path_prefix."core/include/project/exceptions/project.exception.class.php";
	
	$classes['ProjectNotFoundException']										= $path_prefix."core/include/project/exceptions/project_not_found.exception.class.php";
	$classes['ProjectSetNextStatusException']									= $path_prefix."core/include/project/exceptions/project_set_next_status.exception.class.php";
	$classes['ProjectUserSetQuotaException']									= $path_prefix."core/include/project/exceptions/project_user_set_quota.exception.class.php";
	$classes['ProjectIDMissingException']										= $path_prefix."core/include/project/exceptions/project_id_missing.exception.class.php";
	
	$classes['ProjectCreateException']											= $path_prefix."core/include/project/exceptions/project_create.exception.class.php";
	$classes['ProjectCreateProjectExistsException']								= $path_prefix."core/include/project/exceptions/project_create_project_exists.exception.class.php";
	$classes['ProjectCreateStatusException']									= $path_prefix."core/include/project/exceptions/project_create_status.exception.class.php";
	$classes['ProjectCreateFolderException']									= $path_prefix."core/include/project/exceptions/project_create_folder.exception.class.php";
	$classes['ProjectCreateStatusFolderException']								= $path_prefix."core/include/project/exceptions/project_create_status_folder.exception.class.php";
	$classes['ProjectCreateStatusSubFolderException']							= $path_prefix."core/include/project/exceptions/project_create_status_sub_folder.exception.class.php";
	$classes['ProjectCreateSupplementaryFolderException']						= $path_prefix."core/include/project/exceptions/project_create_supplementary_folder.exception.class.php";
	$classes['ProjectCreateDescriptionException']								= $path_prefix."core/include/project/exceptions/project_create_description.exception.class.php";
	$classes['ProjectCreateMasterDataException']								= $path_prefix."core/include/project/exceptions/project_create_master_data.exception.class.php";
	$classes['ProjectCreatePermissionUserException']							= $path_prefix."core/include/project/exceptions/project_create_permission_user.exception.class.php";
	$classes['ProjectCreatePermissionLeaderException']							= $path_prefix."core/include/project/exceptions/project_create_permission_leader.exception.class.php";
	$classes['ProjectCreatePermissionGroupException']							= $path_prefix."core/include/project/exceptions/project_create_permission_group.exception.class.php";
	$classes['ProjectCreatePermissionOrganisationUnitException']				= $path_prefix."core/include/project/exceptions/project_create_permission_organisation_unit.exception.class.php";
	$classes['ProjectCreatePermissionQualityManagerException']					= $path_prefix."core/include/project/exceptions/project_create_permission_quality_manager.exception.class.php";
	
	$classes['ProjectDeleteException']											= $path_prefix."core/include/project/exceptions/project_delete.exception.class.php";
	$classes['ProjectDeleteContainsSubProjectsException']						= $path_prefix."core/include/project/exceptions/project_delete_contains_sub_projects.exception.class.php";
	$classes['ProjectDeleteFolderException']									= $path_prefix."core/include/project/exceptions/project_delete_folder.exception.class.php";
	$classes['ProjectDeleteItemException']										= $path_prefix."core/include/project/exceptions/project_delete_item.exception.class.php";
	$classes['ProjectDeleteLinkException']										= $path_prefix."core/include/project/exceptions/project_delete_link.exception.class.php";
	$classes['ProjectDeleteLogException']										= $path_prefix."core/include/project/exceptions/project_delete_log.exception.class.php";
	$classes['ProjectDeletePermissionException']								= $path_prefix."core/include/project/exceptions/project_delete_permission.exception.class.php";
	$classes['ProjectDeleteStatusException']									= $path_prefix."core/include/project/exceptions/project_delete_status.exception.class.php";
	$classes['ProjectDeleteTaskException']										= $path_prefix."core/include/project/exceptions/project_delete_task.exception.class.php";
	
	$classes['ProjectMoveException']											= $path_prefix."core/include/project/exceptions/project_move.exception.class.php";
	$classes['ProjectMoveFolderException']										= $path_prefix."core/include/project/exceptions/project_move_folder.exception.class.php";
	$classes['ProjectMovePermissionException']									= $path_prefix."core/include/project/exceptions/project_move_permission.exception.class.php";
	$classes['ProjectMoveProjectExistsException']								= $path_prefix."core/include/project/exceptions/project_move_project_exists.exception.class.php";
	
	$classes['ProjectSecurityException']										= $path_prefix."core/include/project/exceptions/project_security.exception.class.php";
	$classes['ProjectSecurityAccessDeniedException']							= $path_prefix."core/include/project/exceptions/project_security_access_denied.exception.class.php";
	$classes['ProjectSecurityChangeException']									= $path_prefix."core/include/project/exceptions/project_security_change.exception.class.php";
	
	$classes['ProjectItemException']											= $path_prefix."core/include/project/exceptions/project_item.exception.class.php";
	$classes['ProjectItemLinkException']										= $path_prefix."core/include/project/exceptions/project_item_link.exception.class.php";
	$classes['ProjectItemUnlinkException']										= $path_prefix."core/include/project/exceptions/project_item_unlink.exception.class.php";
	$classes['ProjectItemNotFoundException']									= $path_prefix."core/include/project/exceptions/project_item_not_found.exception.class.php";
	
	$classes['ProjectLogException']												= $path_prefix."core/include/project/exceptions/project_log.exception.class.php";
	$classes['ProjectLogCreateException']										= $path_prefix."core/include/project/exceptions/project_log_create.exception.class.php";
	$classes['ProjectLogDeleteException']										= $path_prefix."core/include/project/exceptions/project_log_delete.exception.class.php";
	$classes['ProjectLogNotFoundException']										= $path_prefix."core/include/project/exceptions/project_log_not_found.exception.class.php";
	$classes['ProjectLogIDMissingException']									= $path_prefix."core/include/project/exceptions/project_log_id_missing.exception.class.php";
	$classes['ProjectLogItemLinkException']										= $path_prefix."core/include/project/exceptions/project_log_item_link.exception.class.php";
	
	$classes['ProjectPermissionException']										= $path_prefix."core/include/project/exceptions/project_permission.exception.class.php";
	$classes['ProjectPermissionDeleteException']								= $path_prefix."core/include/project/exceptions/project_permission_delete.exception.class.php";
	$classes['ProjectPermissionUserException']									= $path_prefix."core/include/project/exceptions/project_permission_user.exception.class.php";
	$classes['ProjectPermissionUserCreateException']							= $path_prefix."core/include/project/exceptions/project_permission_user_create.exception.class.php";
	$classes['ProjectPermissionUserCreateVirtualFolderException']				= $path_prefix."core/include/project/exceptions/project_permission_user_create_virtual_folder.exception.class.php";
	$classes['ProjectPermissionUserDeleteException']							= $path_prefix."core/include/project/exceptions/project_permission_user_delete.exception.class.php";
	$classes['ProjectPermissionUserDeleteVirtualFolderException']				= $path_prefix."core/include/project/exceptions/project_permission_user_delete_virtual_folder.exception.class.php";
	$classes['ProjectPermissionGroupException']									= $path_prefix."core/include/project/exceptions/project_permission_group.exception.class.php";
	$classes['ProjectPermissionGroupCreateException']							= $path_prefix."core/include/project/exceptions/project_permission_group_create.exception.class.php";
	$classes['ProjectPermissionGroupCreateVirtualFolderException']				= $path_prefix."core/include/project/exceptions/project_permission_group_create_virtual_folder.exception.class.php";
	$classes['ProjectPermissionGroupDeleteException']							= $path_prefix."core/include/project/exceptions/project_permission_group_delete.exception.class.php";
	$classes['ProjectPermissionGroupDeleteVirtualFolderException']				= $path_prefix."core/include/project/exceptions/project_permission_group_delete_virtual_folder.exception.class.php";
	$classes['ProjectPermissionOrganisationUnitException']						= $path_prefix."core/include/project/exceptions/project_permission_organisation_unit.exception.class.php";
	$classes['ProjectPermissionOrganisationUnitCreateException']				= $path_prefix."core/include/project/exceptions/project_permission_organisation_unit_create.exception.class.php";
	$classes['ProjectPermissionOrganisationUnitCreateVirtualFolderException']	= $path_prefix."core/include/project/exceptions/project_permission_organisation_unit_create_virtual_folder.exception.class.php";
	$classes['ProjectPermissionOrganisationUnitDeleteException']				= $path_prefix."core/include/project/exceptions/project_permission_organisation_unit_delete.exception.class.php";
	$classes['ProjectPermissionOrganisationUnitDeleteVirtualFolderException']	= $path_prefix."core/include/project/exceptions/project_permission_organisation_unit_delete_virtual_folder.exception.class.php";
	$classes['ProjectPermissionNotFoundException']								= $path_prefix."core/include/project/exceptions/project_permission_not_found.exception.class.php";
	$classes['ProjectPermissionIDMissingException']								= $path_prefix."core/include/project/exceptions/project_permission_id_missing.exception.class.php";
	
	$classes['ProjectStatusException']											= $path_prefix."core/include/project/exceptions/project_status.exception.class.php";
	$classes['ProjectStatusCreateException']									= $path_prefix."core/include/project/exceptions/project_status_create.exception.class.php";
	$classes['ProjectStatusDeleteException']									= $path_prefix."core/include/project/exceptions/project_status_delete.exception.class.php";
	$classes['ProjectStatusNotFoundException']									= $path_prefix."core/include/project/exceptions/project_status_not_found.exception.class.php";
	$classes['ProjectStatusIDMissingException']									= $path_prefix."core/include/project/exceptions/project_status_id_missing.exception.class.php";
	
	$classes['ProjectTaskException']											= $path_prefix."core/include/project/exceptions/project_task.exception.class.php";
	$classes['ProjectTaskCreateException']										= $path_prefix."core/include/project/exceptions/project_task_create.exception.class.php";
	$classes['ProjectTaskCreateAttachException']								= $path_prefix."core/include/project/exceptions/project_task_create_attach.exception.class.php";
	$classes['ProjectTaskDeleteException']										= $path_prefix."core/include/project/exceptions/project_task_delete.exception.class.php";
	$classes['ProjectTaskNotFoundException']									= $path_prefix."core/include/project/exceptions/project_task_not_found.exception.class.php";
	$classes['ProjectTaskIDMissingException']									= $path_prefix."core/include/project/exceptions/project_task_id_missing.exception.class.php";
	
	$classes['ProjectTemplateException']										= $path_prefix."core/include/project/exceptions/project_template.exception.class.php";
	$classes['ProjectTemplateCreateException']									= $path_prefix."core/include/project/exceptions/project_template_create.exception.class.php";
	$classes['ProjectTemplateCreateOLDLNotFoundException']						= $path_prefix."core/include/project/exceptions/project_template_create_oldl_not_found.exception.class.php";
	$classes['ProjectTemplateCreateOLDLCreateException']						= $path_prefix."core/include/project/exceptions/project_template_create_oldl_create.exception.class.php";
	$classes['ProjectTemplateDeleteException']									= $path_prefix."core/include/project/exceptions/project_template_delete.exception.class.php";
	$classes['ProjectTemplateDeleteInUseException']								= $path_prefix."core/include/project/exceptions/project_template_delete_in_use.exception.class.php";
	$classes['ProjectTemplateDeleteOLDLDeleteException']						= $path_prefix."core/include/project/exceptions/project_template_delete_oldl_delete.exception.class.php";
	$classes['ProjectTemplateNotFoundException']								= $path_prefix."core/include/project/exceptions/project_template_not_found.exception.class.php";
	$classes['ProjectTemplateIDMissingException']								= $path_prefix."core/include/project/exceptions/project_template_id_missing.exception.class.php";
	$classes['ProjectTemplateCategoryCreateException']							= $path_prefix."core/include/project/exceptions/project_template_category_create.exception.class.php";
	$classes['ProjectTemplateCategoryDeleteException']							= $path_prefix."core/include/project/exceptions/project_template_category_delete.exception.class.php";	
	$classes['ProjectTemplateCategoryNotFoundException']						= $path_prefix."core/include/project/exceptions/project_template_category_not_found.exception.class.php";	
	$classes['ProjectTemplateCategoryIDMissingException']						= $path_prefix."core/include/project/exceptions/project_template_category_id_missing.exception.class.php";	
	
	
	$classes['Project'] 							= $path_prefix."core/include/project/project.class.php";
	$classes['ProjectFolder'] 						= $path_prefix."core/include/project/project_folder.class.php";
	$classes['ProjectVirtualFolder'] 				= $path_prefix."core/include/project/project_virtual_folder.class.php";
	$classes['ProjectItem'] 						= $path_prefix."core/include/project/project_item.class.php";
	$classes['ProjectItemFactory'] 					= $path_prefix."core/include/project/project_item_factory.class.php";
	$classes['ProjectLog']							= $path_prefix."core/include/project/project_log.class.php";
	$classes['ProjectLogHasItem']					= $path_prefix."core/include/project/project_log_has_item.class.php";
	$classes['ProjectPermission'] 					= $path_prefix."core/include/project/project_permission.class.php";
	$classes['ProjectPermissionUser']				= $path_prefix."core/include/project/project_permission_user.class.php";
	$classes['ProjectPermissionGroup']				= $path_prefix."core/include/project/project_permission_group.class.php";
	$classes['ProjectPermissionOrganisationUnit']	= $path_prefix."core/include/project/project_permission_organisation_unit.class.php";
	$classes['ProjectSecurity'] 					= $path_prefix."core/include/project/project_security.class.php";
	$classes['ProjectStatus']						= $path_prefix."core/include/project/project_status.class.php";
	$classes['ProjectStatusFolder']					= $path_prefix."core/include/project/project_status_folder.class.php";
	$classes['ProjectStatusRelation']				= $path_prefix."core/include/project/project_status_relation.class.php";
	$classes['ProjectTask']							= $path_prefix."core/include/project/project_task.class.php";
	$classes['ProjectTaskPoint']					= $path_prefix."core/include/project/project_task_point.class.php";
	$classes['ProjectTemplate']						= $path_prefix."core/include/project/project_template.class.php";
	$classes['ProjectTemplateCat']					= $path_prefix."core/include/project/project_template_cat.class.php";
	$classes['ProjectUserData']						= $path_prefix."core/include/project/project_user_data.class.php";
	$classes['ProjectValueVar']						= $path_prefix."core/include/project/project_value_var.class.php";
	
	$classes['Project_Wrapper']						= $path_prefix."core/include/project/project.wrapper.class.php";
?>