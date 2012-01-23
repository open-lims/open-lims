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
$LANG_EXCEPTION['ProjectException'] 												= "A Project related error occurs!";

$LANG_EXCEPTION['ProjectIDMissingException'] 										= "The Project-ID is missing!";
$LANG_EXCEPTION['ProjectNotFoundException'] 										= "The requested Project was not found!";
$LANG_EXCEPTION['ProjectSetNextStatusException'] 									= "Could not set next status!";
$LANG_EXCEPTION['ProjectUserSetQuotaException'] 									= "Could not set user quota!";

$LANG_EXCEPTION['ProjectCreateException'] 											= "Could not create Project!";
$LANG_EXCEPTION['ProjectCreateProjectExistsException'] 								= "Project already exists!";
$LANG_EXCEPTION['ProjectCreateStatusException'] 									= "Could not create initial Project status!";
$LANG_EXCEPTION['ProjectCreateFolderException'] 									= "Could not create Project main folder!";
$LANG_EXCEPTION['ProjectCreateStatusFolderException'] 								= "Could not create status folder!";
$LANG_EXCEPTION['ProjectCreateStatusSubFolderException'] 							= "Could not create status sub folder!";
$LANG_EXCEPTION['ProjectCreateSupplementaryFolderException'] 						= "Could not create supplementary folder!";
$LANG_EXCEPTION['ProjectCreateDescriptionException'] 								= "Could not create Project description!";
$LANG_EXCEPTION['ProjectCreateMasterDataException'] 								= "Could not create Project master-data!";
$LANG_EXCEPTION['ProjectCreatePermissionUserException'] 							= "Could not create user/owner permission!";
$LANG_EXCEPTION['ProjectCreatePermissionLeaderException'] 							= "Could not create Organisation Unit leader permission!";
$LANG_EXCEPTION['ProjectCreatePermissionGroupException'] 							= "Could not create group permission!";
$LANG_EXCEPTION['ProjectCreatePermissionOrganisationUnitException'] 				= "Could not create Organisation Unit permission!";
$LANG_EXCEPTION['ProjectCreatePermissionQualityManagerException'] 					= "Could not create quality-manager permission!";

$LANG_EXCEPTION['ProjectDeleteException'] 											= "Could not delete Project!";
$LANG_EXCEPTION['ProjectDeleteContainsSubProjectsException'] 						= "Could not delete Project - Project contains Sub-Project!";
$LANG_EXCEPTION['ProjectDeleteFolderException'] 									= "Could not delete Project folder!";
$LANG_EXCEPTION['ProjectDeleteItemException'] 										= "Could not delete Project items!";
$LANG_EXCEPTION['ProjectDeleteLinkException'] 										= "Could not delete Project links!";
$LANG_EXCEPTION['ProjectDeleteLogException'] 										= "Could not delete Project log!";
$LANG_EXCEPTION['ProjectDeletePermissionException'] 								= "Could not delete Project permissions!";
$LANG_EXCEPTION['ProjectDeleteStatusException'] 									= "Could not delete Project status!";
$LANG_EXCEPTION['ProjectDeleteTaskException'] 										= "Could not delete Project tasks!";

$LANG_EXCEPTION['ProjectMoveException'] 											= "Could not move the Project!";
$LANG_EXCEPTION['ProjectMoveProjectExistsException'] 								= "Could not move the Project - The project already exists!";
$LANG_EXCEPTION['ProjectMovePermissionException'] 									= "An error occurs during permission changes!";
$LANG_EXCEPTION['ProjectMoveFolderException'] 										= "Could not move the folder!";

$LANG_EXCEPTION['ProjectSecurityException'] 										= "A security error occurs!";
$LANG_EXCEPTION['ProjectAccessDeniedException'] 									= "Project access denied!";
$LANG_EXCEPTION['ProjectChangeException'] 											= "Could not change permission!";

$LANG_EXCEPTION['ProjectItemException'] 											= "A Project Item error occurs!";
$LANG_EXCEPTION['ProjectItemLinkException'] 										= "Could not link an Item!";
$LANG_EXCEPTION['ProjectItemUnlinkException'] 										= "Could not unlink an Item!";
$LANG_EXCEPTION['ProjectItemNotFoundException'] 									= "Project Item not found!";

$LANG_EXCEPTION['ProjectLogException'] 												= "A Project Log error occurs!";
$LANG_EXCEPTION['ProjectLogCreateException'] 										= "Could not create Project Log!";
$LANG_EXCEPTION['ProjectLogDeleteException'] 										= "Could not delete Project Log!";
$LANG_EXCEPTION['ProjectLogNotFoundException'] 										= "Project Log not found!";
$LANG_EXCEPTION['ProjectLogIDMissingException'] 									= "Project Log ID is missing!";
$LANG_EXCEPTION['ProjectLogItemLinkException'] 										= "Could not link an Item to the Project Log!";

$LANG_EXCEPTION['ProjectPermissionException'] 										= "A Project Permission error occurs!";
$LANG_EXCEPTION['ProjectPermissionUserException'] 									= "A Project Permission User error occurs!";
$LANG_EXCEPTION['ProjectPermissionUserCreateException'] 							= "Could not create the User permission!";
$LANG_EXCEPTION['ProjectPermissionUserCreateVirtualFolderException'] 				= "Could not create the virtual folder!";
$LANG_EXCEPTION['ProjectPermissionUserDeleteException'] 							= "Could not delete the User permission!";
$LANG_EXCEPTION['ProjectPermissionUserDeleteVirtualFolderException'] 				= "Could not delete the virtual folder!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitException'] 						= "A Project Permission Organisation Unit error occurs!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitCreateException'] 				= "Could not create the Organisation Unit permission!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitCreateVirtualFolderException'] 	= "Could not create the virtual folder!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitDeleteException'] 				= "Could not delete the Organisation Unit permission!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitDeleteVirtualFolderException'] 	= "Could not delete the virtual folder!";
$LANG_EXCEPTION['ProjectPermissionGroupException'] 									= "A Project Permission Group error occurs!";
$LANG_EXCEPTION['ProjectPermissionGroupCreateException'] 							= "Could not create the Group permission!";
$LANG_EXCEPTION['ProjectPermissionGroupCreateVirtualFolderException'] 				= "Could not create the virtual folder!";
$LANG_EXCEPTION['ProjectPermissionGroupDeleteException'] 							= "Could not delete the Group permission!";
$LANG_EXCEPTION['ProjectPermissionGroupDeleteVirtualFolderException'] 				= "Could not delete the virtual folder!";
$LANG_EXCEPTION['ProjectPermissionNotFoundException'] 								= "Project Permission not found!";
$LANG_EXCEPTION['ProjectPermissionIDMissingException'] 								= "The Project Permission ID is missing!";

$LANG_EXCEPTION['ProjectSecurityException'] 										= "Project Security Error!";
$LANG_EXCEPTION['ProjectSecurityChangeException'] 									= "Project Security change faield!";
$LANG_EXCEPTION['ProjectSecurityAccessDeniedException'] 							= "Project Access Denied!";

$LANG_EXCEPTION['ProjectStatusException'] 											= "A Project status error occurs!";
$LANG_EXCEPTION['ProjectStatusCreateException'] 									= "Could not create the Project status!";
$LANG_EXCEPTION['ProjectStatusDeleteException'] 									= "Could not delete the Project status!";
$LANG_EXCEPTION['ProjectStatusNotFoundException'] 									= "Project status not found!";
$LANG_EXCEPTION['ProjectStatusIDMissingException'] 									= "The Project status ID is missing!";

$LANG_EXCEPTION['ProjectTaskException'] 											= "A Project task error occurs!";
$LANG_EXCEPTION['ProjectTaskCreateException'] 										= "Could not create the Project task!";
$LANG_EXCEPTION['ProjectTaskCreateAttachException'] 								= "Could not attach the Project task!";
$LANG_EXCEPTION['ProjectTaskDeleteException'] 										= "Could not delete the Project task!";
$LANG_EXCEPTION['ProjectTaskNotFoundException'] 									= "Project task not found!";
$LANG_EXCEPTION['ProjectTaskIDMissingException'] 									= "The Project task ID is missing!";

$LANG_EXCEPTION['ProjectTemplateException'] 										= "A Project template error occurs!";
$LANG_EXCEPTION['ProjectTemplateCreateException'] 									= "Could not create the template!";
$LANG_EXCEPTION['ProjectTemplateCreateOLDLNotFoundException'] 						= "OLDL file not found!";
$LANG_EXCEPTION['ProjectTemplateCreateOLDLCreateException'] 						= "Could not create OLDL entry!";
$LANG_EXCEPTION['ProjectTemplateDeleteException'] 									= "Could not delete the template!";
$LANG_EXCEPTION['ProjectTemplateDeleteInUseException'] 								= "The template is in use!";
$LANG_EXCEPTION['ProjectTemplateDeleteOLDLDeleteException'] 						= "Could not delete the OLDL entry!";
$LANG_EXCEPTION['ProjectTemplateNotFoundException'] 								= "Project template not found!";
$LANG_EXCEPTION['ProjectTemplateIDMissingException'] 								= "The Project tempalte ID is missing!";
$LANG_EXCEPTION['ProjectTemplateCategoryCreateException'] 							= "Could not create the template category!";
$LANG_EXCEPTION['ProjectTemplateCategoryDeleteException'] 							= "Could not delete the template category!";
$LANG_EXCEPTION['ProjectTemplateCategoryNotFoundException'] 						= "Project Tempalte Category not found!";
$LANG_EXCEPTION['ProjectTemplateCategoryIDMissingException'] 						= "The Project tempalte category ID is missing!";

?>