<?php 
/**
 * @package data
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
	$classes['ConcreteFolderCaseInterface']					= $path_prefix."core/include/data/folder/interfaces/concrete_folder_case.interface.php";		
	$classes['ValueVarCaseInterface']						= $path_prefix."core/include/data/value/interfaces/value_var_case.interface.php";	

	
	$classes['DataException']								= $path_prefix."core/include/data/exceptions/data.exception.class.php";
	$classes['DataSecurityException']						= $path_prefix."core/include/data/exceptions/data_security.exception.class.php";
	$classes['DataSecurityAccessDeniedException']			= $path_prefix."core/include/data/exceptions/data_security_access_denied.exception.class.php";
	
	$classes['DataEntityException']							= $path_prefix."core/include/data/exceptions/data_entity.exception.class.php";
	$classes['DataEntityNotFoundException']					= $path_prefix."core/include/data/exceptions/data_entity_not_found.exception.class.php";
	$classes['DataEntityNoInstanceException']				= $path_prefix."core/include/data/exceptions/data_entity_no_instance.exception.class.php";
	$classes['DataEntityIDMissingException']				= $path_prefix."core/include/data/exceptions/data_entity_id_missing.exception.class.php";
	$classes['DataEntityCreateException']					= $path_prefix."core/include/data/exceptions/data_entity_create.exception.class.php";
	$classes['DataEntityCreateIDMissingException']			= $path_prefix."core/include/data/exceptions/data_entity_create_id_missing.exception.class.php";
	$classes['DataEntityCreateEntryFailedException']		= $path_prefix."core/include/data/exceptions/data_entity_create_entry_failed.exception.class.php";
	$classes['DataEntityCreateItemLinkFailedException']		= $path_prefix."core/include/data/exceptions/data_entity_create_item_link_failed.exception.class.php";
	$classes['DataEntityDeleteException']					= $path_prefix."core/include/data/exceptions/data_entity_delete.exception.class.php";
	$classes['DataEntityDeleteFailedException']				= $path_prefix."core/include/data/exceptions/data_entity_delete_failed.exception.class.php";
	$classes['DataEntityDeleteItemLinkException']			= $path_prefix."core/include/data/exceptions/data_entity_delete_item_link.exception.class.php";
	$classes['DataEntityDeleteParentLinkException']			= $path_prefix."core/include/data/exceptions/data_entity_delete_parent_link.exception.class.php";
	$classes['DataEntitySetAsChildException']				= $path_prefix."core/include/data/exceptions/data_entity_set_as_child.exception.class.php";
	
	$classes['FolderException']								= $path_prefix."core/include/data/folder/exceptions/folder.exception.class.php";
	$classes['FolderNotFoundException']						= $path_prefix."core/include/data/folder/exceptions/folder_not_found.exception.class.php";
	$classes['FolderIDMissingException']					= $path_prefix."core/include/data/folder/exceptions/folder_id_missing.exception.class.php";
	$classes['FolderIsEmptyException']						= $path_prefix."core/include/data/folder/exceptions/folder_is_empty.exception.class.php";
	$classes['FolderCreateException']						= $path_prefix."core/include/data/folder/exceptions/folder_create.exception.class.php";
	$classes['FolderCreateFailedException']					= $path_prefix."core/include/data/folder/exceptions/folder_create_failed.exception.class.php";
	$classes['FolderCreateIDMissingException']				= $path_prefix."core/include/data/folder/exceptions/folder_create_id_missing.exception.class.php";
	$classes['FolderCreateFolderAlreadyExsitsException']	= $path_prefix."core/include/data/folder/exceptions/folder_create_folder_already_exists.exception.class.php";
	$classes['FolderCreatePhysicalCreationFailedException']	= $path_prefix."core/include/data/folder/exceptions/folder_create_physical_creation_failed.exception.class.php";
	
	$classes['FileException']								= $path_prefix."core/include/data/file/exceptions/file.exception.class.php";
	$classes['FileNotFoundException']						= $path_prefix."core/include/data/file/exceptions/file_not_found.exception.class.php";
	$classes['FileIDMissingException']						= $path_prefix."core/include/data/file/exceptions/file_id_missing.exception.class.php";
	$classes['FileVersionNotFoundException']				= $path_prefix."core/include/data/file/exceptions/file_version_not_found.exception.class.php";
	$classes['FileVersionIDMissingException']				= $path_prefix."core/include/data/file/exceptions/file_veriosn_id_missing.exception.class.php";
	$classes['FileCreateException']							= $path_prefix."core/include/data/file/exceptions/file_create.exception.class.php";
	$classes['FileCreateIDMissingException']				= $path_prefix."core/include/data/file/exceptions/file_create_id_missing.exception.class.php";
	$classes['FileCreateFailedException']					= $path_prefix."core/include/data/file/exceptions/file_create_failed.exception.class.php";
	$classes['FileCreateVersionCreateFaileException']		= $path_prefix."core/include/data/file/exceptions/file_create_version_create_failed.exception.class.php";
	
	$classes['ValueException']								= $path_prefix."core/include/data/value/exceptions/value.exception.class.php";
	$classes['ValueNotFoundException']						= $path_prefix."core/include/data/value/exceptions/value_not_found.exception.class.php";
	$classes['ValueIDMissingException']						= $path_prefix."core/include/data/value/exceptions/value_id_missing.exception.class.php";
	$classes['ValueVersionNotFoundException']				= $path_prefix."core/include/data/value/exceptions/value_version_not_found.exception.class.php";
	$classes['ValueVersionIDMissingException']				= $path_prefix."core/include/data/value/exceptions/value_version_id_missing.exception.class.php";
	$classes['ValueTypeException']							= $path_prefix."core/include/data/value/exceptions/value_type.exception.class.php";
	$classes['ValueTypeNotFoundException']					= $path_prefix."core/include/data/value/exceptions/value_type_not_found.exception.class.php";
	$classes['ValueTypeIDMissingException']					= $path_prefix."core/include/data/value/exceptions/value_type_id_missing.exception.class.php";
	$classes['ValueCreateException']						= $path_prefix."core/include/data/value/exceptions/value_create.exception.class.php";
	$classes['ValueCreateIDMissingException']				= $path_prefix."core/include/data/value/exceptions/value_create_id_missing.exception.class.php";
	$classes['ValueCreateFailedException']					= $path_prefix."core/include/data/value/exceptions/value_create_failed.exception.class.php";
	$classes['ValueCreateVersionCreateFailedException']		= $path_prefix."core/include/data/value/exceptions/value_create_version_create_failed.exception.class.php";
	
	$classes['VirtualFolderException']						= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder.exception.class.php";
	$classes['VirtualFolderNotFoundException']				= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder_not_found.exception.class.php";
	$classes['VirtualFolderIDMissingException']				= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder_id_missing.exception.class.php";
	$classes['VirtualFolderCreateException']				= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder_create.exception.class.php";
	$classes['VirtualFolderCreateFailedException']			= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder_create_failed.exception.class.php";
	$classes['VirtualFolderCreateFolderNotFoundException']	= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder_create_folder_not_found.exception.class.php";
	$classes['VirtualFolderCreateIDMissingException']		= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder_create_id_missing.exception.class.php";
	
	$classes['ParameterException']							= $path_prefix."core/include/data/parameter/exceptions/parameter.exception.class.php";
	$classes['ParameterNotFoundException']					= $path_prefix."core/include/data/parameter/exceptions/parameter_not_found.exception.class.php";
	$classes['ParameterIDMissingException']					= $path_prefix."core/include/data/parameter/exceptions/parameter_id_missing.exception.class.php";
	$classes['ParameterNoInstanceException']				= $path_prefix."core/include/data/parameter/exceptions/parameter_no_instance.exception.class.php";
	$classes['ParameterUpdateException']					= $path_prefix."core/include/data/parameter/exceptions/parameter_update.exception.class.php";
	$classes['ParameterUpdateNoValuesException']			= $path_prefix."core/include/data/parameter/exceptions/parameter_update_no_values.exception.class.php";
	$classes['ParameterUpdateValueCreateFailedException']	= $path_prefix."core/include/data/parameter/exceptions/parameter_update_value_create_failed.exception.class.php";
	$classes['ParameterUpdateVersionCreateFailedException']	= $path_prefix."core/include/data/parameter/exceptions/parameter_update_version_create_failed.exception.class.php";
	$classes['ParameterCreateException']					= $path_prefix."core/include/data/parameter/exceptions/parameter_create.exception.class.php";
	$classes['ParameterCreateIDMissingException']			= $path_prefix."core/include/data/parameter/exceptions/parameter_create_id_missing.exception.class.php";
	$classes['ParameterCreateFailedException']				= $path_prefix."core/include/data/parameter/exceptions/parameter_create_failed.exception.class.php";
	$classes['ParameterCreateValueCreateFailedException']	= $path_prefix."core/include/data/parameter/exceptions/parameter_create_value_create_failed.exception.class.php";
	$classes['ParameterCreateVersionCreateFailedException']	= $path_prefix."core/include/data/parameter/exceptions/parameter_create_version_create_failed.exception.class.php";
	$classes['ParameterCreateTemplateLinkFailed']			= $path_prefix."core/include/data/parameter/exceptions/parameter_create_template_link_failed.exception.class.php";
	
	$classes['DataEntity']									= $path_prefix."core/include/data/data_entity.class.php";
	$classes['DataEntityPermission']						= $path_prefix."core/include/data/data_entity_permission.class.php";
	$classes['DataBrowser']									= $path_prefix."core/include/data/data_browser.class.php";
	$classes['DataPath']									= $path_prefix."core/include/data/data_path.class.php";
	$classes['DataPermission']								= $path_prefix."core/include/data/data_permission.class.php";
	$classes['DataUserData']								= $path_prefix."core/include/data/data_user_data.class.php";
	
	$classes['File']										= $path_prefix."core/include/data/file/file.class.php";
	$classes['ImageCache']									= $path_prefix."core/include/data/file/image_cache.class.php";
	
	$classes['Folder']										= $path_prefix."core/include/data/folder/folder.class.php";
	$classes['SystemFolder']								= $path_prefix."core/include/data/folder/system_folder.class.php";
	$classes['UserFolder']									= $path_prefix."core/include/data/folder/user_folder.class.php";
	$classes['GroupFolder']									= $path_prefix."core/include/data/folder/group_folder.class.php";
	$classes['OrganisationUnitFolder']						= $path_prefix."core/include/data/folder/organisation_unit_folder.class.php";
	$classes['Path']										= $path_prefix."core/include/data/folder/path.class.php";
	
	$classes['Value']										= $path_prefix."core/include/data/value/value.class.php";
	$classes['ValueType']									= $path_prefix."core/include/data/value/value_type.class.php";
	$classes['ValueVar']									= $path_prefix."core/include/data/value/value_var.class.php";
	$classes['ItemValueVar']								= $path_prefix."core/include/data/value/item_value_var.class.php";
	
	$classes['VirtualFolder']								= $path_prefix."core/include/data/virtual_folder/virtual_folder.class.php";
	
	$classes['Parameter']									= $path_prefix."core/include/data/parameter/parameter.class.php";
	$classes['ParameterTemplateParameter']					= $path_prefix."core/include/data/parameter/parameter_template_parameter.class.php";
	$classes['ParameterNonTemplateParameter']				= $path_prefix."core/include/data/parameter/parameter_non_template_parameter.class.php";
	$classes['ParameterTemplate']							= $path_prefix."core/include/data/parameter/parameter_template.class.php";
	$classes['ParameterMethod']								= $path_prefix."core/include/data/parameter/parameter_method.class.php";
	
	$classes['Data_Wrapper']								= $path_prefix."core/include/data/data.wrapper.class.php";
?>