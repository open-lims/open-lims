<?php 
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
	$classes['SampleException']										= $path_prefix."core/include/sample/exceptions/sample.exception.class.php";
	
	$classes['SampleIDMissingException']							= $path_prefix."core/include/sample/exceptions/sample_id_missing.exception.class.php";
	$classes['SampleNotFoundException']								= $path_prefix."core/include/sample/exceptions/sample_not_found.exception.class.php";
	$classes['SampleNoInstanceException']								= $path_prefix."core/include/sample/exceptions/sample_no_instance.exception.class.php";
	
	$classes['SampleCreateException']								= $path_prefix."core/include/sample/exceptions/sample_create.exception.class.php";
	$classes['SampleCreateFolderException']							= $path_prefix."core/include/sample/exceptions/sample_create_folder.exception.class.php";
	$classes['SampleCreateSubFolderException']						= $path_prefix."core/include/sample/exceptions/sample_create_sub_folder.exception.class.php";
	$classes['SampleCreateAsItemException']							= $path_prefix."core/include/sample/exceptions/sample_create_as_item.exception.class.php";
	$classes['SampleCreateUserException']							= $path_prefix."core/include/sample/exceptions/sample_create_user.exception.class.php";
	$classes['SampleCreateOrganisationUnitException']				= $path_prefix."core/include/sample/exceptions/sample_create_organisation_unit.exception.class.php";
	$classes['SampleCreateLocationException']						= $path_prefix."core/include/sample/exceptions/sample_create_location.exception.class.php";
	$classes['SampleCreateItemSampleException']						= $path_prefix."core/include/sample/exceptions/sample_create_item_sample.exception.class.php";
	$classes['SampleCreateItemValueException']						= $path_prefix."core/include/sample/exceptions/sample_create_item_value.exception.class.php";
	$classes['SampleCreateIDMissingException']						= $path_prefix."core/include/sample/exceptions/sample_create_id_missing.exception.class.php";
	$classes['SampleCreateFailedException']							= $path_prefix."core/include/sample/exceptions/sample_create_failed.exception.class.php";
	
	$classes['SampleCloneException']								= $path_prefix."core/include/sample/exceptions/sample_clone.exception.class.php";
	$classes['SampleCloneCreateException']							= $path_prefix."core/include/sample/exceptions/sample_clone_create.exception.class.php";
	$classes['SampleCloneCreateFailedException']					= $path_prefix."core/include/sample/exceptions/sample_clone_create_failed.exception.class.php";
	$classes['SampleCloneCreateFolderException']					= $path_prefix."core/include/sample/exceptions/sample_clone_create_folder.exception.class.php";
	$classes['SampleCloneCreateSubFolderException']					= $path_prefix."core/include/sample/exceptions/sample_clone_create_sub_folder.exception.class.php";
	$classes['SampleCloneCreateAsItemException']					= $path_prefix."core/include/sample/exceptions/sample_clone_create_as_item.exception.class.php";
	$classes['SampleCloneCreateLocationException']					= $path_prefix."core/include/sample/exceptions/sample_clone_create_location.exception.class.php";
	$classes['SampleCloneUserException']							= $path_prefix."core/include/sample/exceptions/sample_clone_user.exception.class.php";
	$classes['SampleCloneOrganisationUnitException']				= $path_prefix."core/include/sample/exceptions/sample_clone_organisation_unit.exception.class.php";
	$classes['SampleCloneLocationException']						= $path_prefix."core/include/sample/exceptions/sample_clone_location.exception.class.php";
	$classes['SampleCloneValueException']							= $path_prefix."core/include/sample/exceptions/sample_clone_value.exception.class.php";
	$classes['SampleCloneFileException']							= $path_prefix."core/include/sample/exceptions/sample_clone_file.exception.class.php";
	$classes['SampleCloneParentException']							= $path_prefix."core/include/sample/exceptions/sample_clone_parent.exception.class.php";
	$classes['SampleCloneItemException']							= $path_prefix."core/include/sample/exceptions/sample_clone_item.exception.class.php";
	$classes['SampleCloneIDMissingException']						= $path_prefix."core/include/sample/exceptions/sample_clone_id_missing.exception.class.php";
	
	$classes['SampleDeleteException']								= $path_prefix."core/include/sample/exceptions/sample_delete.exception.class.php";
	$classes['SampleDeleteLocationException']						= $path_prefix."core/include/sample/exceptions/sample_delete_location.exception.class.php";
	$classes['SampleDeleteUserException']							= $path_prefix."core/include/sample/exceptions/sample_delete_user.exception.class.php";
	$classes['SampleDeleteOrganisationUnitException']				= $path_prefix."core/include/sample/exceptions/sample_delete_organisation_unit.exception.class.php";
	$classes['SampleDeleteItemException']							= $path_prefix."core/include/sample/exceptions/sample_delete_item.exception.class.php";
	$classes['SampleDeleteFolderException']							= $path_prefix."core/include/sample/exceptions/sample_delete_folder.exception.class.php";
	$classes['SampleDeleteEventFailedException']					= $path_prefix."core/include/sample/exceptions/sample_delete_event_failed.exception.class.php";
	$classes['SampleDeleteFailedException']							= $path_prefix."core/include/sample/exceptions/sample_delete_failed.exception.class.php";
	$classes['SampleDeleteItemLinkException']						= $path_prefix."core/include/sample/exceptions/sample_delete_item_link.exception.class.php";
	
	$classes['SampleSecurityException']								= $path_prefix."core/include/sample/exceptions/sample_security.exception.class.php";
	$classes['SampleSecurityAccessDeniedException']					= $path_prefix."core/include/sample/exceptions/sample_security_access_denied.exception.class.php";
	
	$classes['SamplePermissionException']							= $path_prefix."core/include/sample/exceptions/sample_permission.exception.class.php";
	$classes['SamplePermissionUserException']						= $path_prefix."core/include/sample/exceptions/sample_permission_user.exception.class.php";
	$classes['SamplePermissionUserIDMissingException']				= $path_prefix."core/include/sample/exceptions/sample_permission_user_id_missing.exception.class.php";
	$classes['SamplePermissionOrganisationUnitException']			= $path_prefix."core/include/sample/exceptions/sample_permission_organisation_unit.exception.class.php";
	$classes['SamplePermissionOrganisationUnitIDMissingException']	= $path_prefix."core/include/sample/exceptions/sample_permission_organisation_unit_id_missing.exception.class.php";
	
	$classes['SampleTemplateException']								= $path_prefix."core/include/sample/exceptions/sample_template.exception.class.php";
	$classes['SampleTemplateIDMissingException']					= $path_prefix."core/include/sample/exceptions/sample_template_id_missing.exception.class.php";
	$classes['SampleTemplateNotFoundException']						= $path_prefix."core/include/sample/exceptions/sample_template_not_found.exception.class.php";
	$classes['SampleTemplateCategoryIDMissingException']			= $path_prefix."core/include/sample/exceptions/sample_template_category_id_missing.exception.class.php";
	$classes['SampleTemplateCategoryNotFoundException']				= $path_prefix."core/include/sample/exceptions/sample_template_category_not_found.exception.class.php";
	
	
	$classes['Sample']						= $path_prefix."core/include/sample/sample.class.php";
	$classes['SampleFolder']				= $path_prefix."core/include/sample/sample_folder.class.php";
	$classes['SampleVirtualFolder']			= $path_prefix."core/include/sample/sample_virtual_folder.class.php";
	$classes['SampleItem']					= $path_prefix."core/include/sample/sample_item.class.php";
	$classes['SampleItemFactory']			= $path_prefix."core/include/sample/sample_item_factory.class.php";
	$classes['SampleSecurity']				= $path_prefix."core/include/sample/sample_security.class.php";
	$classes['SampleTemplate']				= $path_prefix."core/include/sample/sample_template.class.php";
	$classes['SampleTemplateCat']			= $path_prefix."core/include/sample/sample_template_cat.class.php";
	$classes['SampleValueVar']				= $path_prefix."core/include/sample/sample_value_var.class.php";
	
	$classes['Sample_Wrapper']				= $path_prefix."core/include/sample/sample.wrapper.class.php";
?>