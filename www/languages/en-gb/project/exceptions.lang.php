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
$LANG['ProjectException'] 												= "A Project related error occurs!";

$LANG['ProjectIDMissingException'] 										= "The Project-ID is missing!";
$LANG['ProjectNotFoundException'] 										= "The requested Project was not found!";
$LANG['ProjectSetNextStatusException'] 									= "Could not set next status!";
$LANG['ProjectUserSetQuotaException'] 									= "Could not set user quota!";

$LANG['ProjectCreateException'] 										= "Could not create Project!";
$LANG['ProjectCreateProjectExistsException'] 							= "Project already exists!";
$LANG['ProjectCreateStatusException'] 									= "Could not create initial Project status!";
$LANG['ProjectCreateFolderException'] 									= "Could not create Project main folder!";
$LANG['ProjectCreateStatusFolderException'] 							= "Could not create status folder!";
$LANG['ProjectCreateStatusSubFolderException'] 							= "Could not create status sub folder!";
$LANG['ProjectCreateSupplementaryFolderException'] 						= "Could not create supplementary folder!";
$LANG['ProjectCreateDescriptionException'] 								= "Could not create Project description!";
$LANG['ProjectCreateMasterDataException'] 								= "Could not create Project master-data!";
$LANG['ProjectCreatePermissionUserException'] 							= "Could not create user/owner permission!";
$LANG['ProjectCreatePermissionLeaderException'] 						= "Could not create Organisation Unit leader permission!";
$LANG['ProjectCreatePermissionGroupException'] 							= "Could not create group permission!";
$LANG['ProjectCreatePermissionOrganisationUnitException'] 				= "Could not create Organisation Unit permission!";
$LANG['ProjectCreatePermissionQualityManagerException'] 				= "Could not create quality-manager permission!";

$LANG['ProjectDeleteException'] 										= "Could not delete Project!";
$LANG['ProjectDeleteContainsSubProjectsException'] 						= "Could not delete Project - Project contains Sub-Project!";
$LANG['ProjectDeleteFolderException'] 									= "Could not delete Project folder!";
$LANG['ProjectDeleteItemException'] 									= "Could not delete Project items!";
$LANG['ProjectDeleteLinkException'] 									= "Could not delete Project links!";
$LANG['ProjectDeleteLogException'] 										= "Could not delete Project log!";
$LANG['ProjectDeletePermissionException'] 								= "Could not delete Project permissions!";
$LANG['ProjectDeleteStatusException'] 									= "Could not delete Project status!";
$LANG['ProjectDeleteTaskException'] 									= "Could not delete Project tasks!";

$LANG['ProjectMoveException'] 											= "Could not move the Project!";
$LANG['ProjectMoveProjectExistsException'] 								= "Could not move the Project - The project already exists!";
$LANG['ProjectMovePermissionException'] 								= "An error occurs during permission changes!";
$LANG['ProjectMoveFolderException'] 									= "Could not move the folder!";

$LANG['ProjectSecurityException'] 										= "A security error occurs!";
$LANG['ProjectAccessDeniedException'] 									= "Project access denied!";
$LANG['ProjectChangeException'] 										= "Could not change permission!";

$LANG['ProjectItemException'] 											= "A Project Item error occurs!";
$LANG['ProjectItemLinkException'] 										= "Could not link an Item!";
$LANG['ProjectItemUnlinkException'] 									= "Could not unlink an Item!";
$LANG['ProjectItemNotFoundException'] 									= "Project Item not found!";

$LANG['ProjectLogException'] 											= "A Project Log error occurs!";
$LANG['ProjectLogCreateException'] 										= "Could not create Project Log!";
$LANG['ProjectLogDeleteException'] 										= "Could not delete Project Log!";
$LANG['ProjectLogNotFoundException'] 									= "Project Log not found!";
$LANG['ProjectLogIDMissingException'] 									= "Project Log ID is missing!";
$LANG['ProjectLogItemLinkException'] 									= "Could not link an Item to the Project Log!";

$LANG['ProjectPermissionException'] 									= "A Project Permission error occurs!";
$LANG['ProjectPermissionUserException'] 								= "A Project Permission User error occurs!";
$LANG['ProjectPermissionUserCreateException'] 							= "Could not create the User permission!";
$LANG['ProjectPermissionUserCreateVirtualFolderException'] 				= "Could not create the virtual folder!";
$LANG['ProjectPermissionUserDeleteException'] 							= "Could not delete the User permission!";
$LANG['ProjectPermissionUserDeleteVirtualFolderException'] 				= "Could not delete the virtual folder!";
$LANG['ProjectPermissionOrganisationUnitException'] 					= "A Project Permission Organisation Unit error occurs!";
$LANG['ProjectPermissionOrganisationUnitCreateException'] 				= "Could not create the Organisation Unit permission!";
$LANG['ProjectPermissionOrganisationUnitCreateVirtualFolderException'] 	= "Could not create the virtual folder!";
$LANG['ProjectPermissionOrganisationUnitDeleteException'] 				= "Could not delete the Organisation Unit permission!";
$LANG['ProjectPermissionOrganisationUnitDeleteVirtualFolderException'] 	= "Could not delete the virtual folder!";
$LANG['ProjectPermissionGroupException'] 								= "A Project Permission Group error occurs!";
$LANG['ProjectPermissionGroupCreateException'] 							= "Could not create the Group permission!";
$LANG['ProjectPermissionGroupCreateVirtualFolderException'] 			= "Could not create the virtual folder!";
$LANG['ProjectPermissionGroupDeleteException'] 							= "Could not delete the Group permission!";
$LANG['ProjectPermissionGroupDeleteVirtualFolderException'] 			= "Could not delete the virtual folder!";
$LANG['ProjectPermissionNotFoundException'] 							= "Project Permission not found!";
$LANG['ProjectPermissionIDMissingException'] 							= "The Project Permission ID is missing!";

$LANG['ProjectSecurityException'] 										= "Project Security Error!";
$LANG['ProjectSecurityChangeException'] 								= "Project Security change faield!";
$LANG['ProjectSecurityAccessDeniedException'] 							= "Project Access Denied!";

$LANG['ProjectStatusException'] 										= "A Project status error occurs!";
$LANG['ProjectStatusCreateException'] 									= "Could not create the Project status!";
$LANG['ProjectStatusDeleteException'] 									= "Could not delete the Project status!";
$LANG['ProjectStatusNotFoundException'] 								= "Project status not found!";
$LANG['ProjectStatusIDMissingException'] 								= "The Project status ID is missing!";

$LANG['ProjectTaskException'] 											= "A Project task error occurs!";
$LANG['ProjectTaskCreateException'] 										= "Could not create the Project task!";
$LANG['ProjectTaskCreateAttachException'] 								= "Could not attach the Project task!";
$LANG['ProjectTaskDeleteException'] 										= "Could not delete the Project task!";
$LANG['ProjectTaskNotFoundException'] 									= "Project task not found!";
$LANG['ProjectTaskIDMissingException'] 									= "The Project task ID is missing!";

$LANG['ProjectTemplateException'] 										= "A Project template error occurs!";
$LANG['ProjectTemplateCreateException'] 								= "Could not create the template!";
$LANG['ProjectTemplateCreateOLDLNotFoundException'] 					= "OLDL file not found!";
$LANG['ProjectTemplateCreateOLDLCreateException'] 						= "Could not create OLDL entry!";
$LANG['ProjectTemplateDeleteException'] 								= "Could not delete the template!";
$LANG['ProjectTemplateDeleteInUseException'] 							= "The template is in use!";
$LANG['ProjectTemplateDeleteOLDLDeleteException'] 						= "Could not delete the OLDL entry!";
$LANG['ProjectTemplateNotFoundException'] 								= "Project template not found!";
$LANG['ProjectTemplateIDMissingException'] 								= "The Project tempalte ID is missing!";
$LANG['ProjectTemplateCategoryCreateException'] 						= "Could not create the template category!";
$LANG['ProjectTemplateCategoryDeleteException'] 						= "Could not delete the template category!";
$LANG['ProjectTemplateCategoryNotFoundException'] 						= "Project Tempalte Category not found!";
$LANG['ProjectTemplateCategoryIDMissingException'] 						= "The Project tempalte category ID is missing!";

?>