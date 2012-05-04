<?php 
/**
 * @package data
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
	$classes['ConcreteFolderCaseInterface']	= $path_prefix."core/include/data/folder/interfaces/concrete_folder_case.interface.php";		
	$classes['ValueVarCaseInterface']		= $path_prefix."core/include/data/value/interfaces/value_var_case.interface.php";	

	
	$classes['DataException']						= $path_prefix."core/include/data/exceptions/data.exception.class.php";
	$classes['DataSecurityException']				= $path_prefix."core/include/data/exceptions/data_security.exception.class.php";
	$classes['DataSecurityAccessDeniedException']	= $path_prefix."core/include/data/exceptions/data_security_access_denied.exception.class.php";
	
	$classes['DataEntityException']					= $path_prefix."core/include/data/exceptions/data_entity.exception.class.php";
	$classes['DataEntityNotFoundException']			= $path_prefix."core/include/data/exceptions/data_entity_not_found.exception.class.php";
	$classes['DataEntityIDMissingException']		= $path_prefix."core/include/data/exceptions/data_entity_id_missing.exception.class.php";
	
	$classes['FolderException']						= $path_prefix."core/include/data/folder/exceptions/folder.exception.class.php";
	$classes['FolderNotFoundException']				= $path_prefix."core/include/data/folder/exceptions/folder_not_found.exception.class.php";
	$classes['FolderIDMissingException']			= $path_prefix."core/include/data/folder/exceptions/folder_id_missing.exception.class.php";
	$classes['FolderIsEmptyException']				= $path_prefix."core/include/data/folder/exceptions/folder_is_empty.exception.class.php";
	
	$classes['FileException']						= $path_prefix."core/include/data/file/exceptions/file.exception.class.php";
	$classes['FileNotFoundException']				= $path_prefix."core/include/data/file/exceptions/file_not_found.exception.class.php";
	$classes['FileIDMissingException']				= $path_prefix."core/include/data/file/exceptions/file_id_missing.exception.class.php";
	$classes['FileVersionNotFoundException']		= $path_prefix."core/include/data/file/exceptions/file_version_not_found.exception.class.php";
	$classes['FileVersionIDMissingException']		= $path_prefix."core/include/data/file/exceptions/file_veriosn_id_missing.exception.class.php";
	
	$classes['ValueException']						= $path_prefix."core/include/data/value/exceptions/value.exception.class.php";
	$classes['ValueNotFoundException']				= $path_prefix."core/include/data/value/exceptions/value_not_found.exception.class.php";
	$classes['ValueIDMissingException']				= $path_prefix."core/include/data/value/exceptions/value_id_missing.exception.class.php";
	$classes['ValueVersionNotFoundException']		= $path_prefix."core/include/data/value/exceptions/value_version_not_found.exception.class.php";
	$classes['ValueVersionIDMissingException']		= $path_prefix."core/include/data/value/exceptions/value_version_id_missing.exception.class.php";
	$classes['ValueTypeException']					= $path_prefix."core/include/data/value/exceptions/value_type.exception.class.php";
	$classes['ValueTypeNotFoundException']			= $path_prefix."core/include/data/value/exceptions/value_type_not_found.exception.class.php";
	$classes['ValueTypeIDMissingException']			= $path_prefix."core/include/data/value/exceptions/value_type_id_missing.exception.class.php";

	$classes['VirtualFolderException']				= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder.exception.class.php";
	$classes['VirtualFolderNotFoundException']		= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder_not_found.exception.class.php";
	$classes['VirtualFolderIDMissingException']		= $path_prefix."core/include/data/virtual_folder/exceptions/virtual_folder_id_missing.exception.class.php";
	
	
	$classes['DataEntityLinkEvent']			= $path_prefix."core/include/data/events/data_entity_link_event.class.php";	
	
	
	$classes['DataEntity']					= $path_prefix."core/include/data/data_entity.class.php";
	$classes['DataEntityPermission']		= $path_prefix."core/include/data/data_entity_permission.class.php";
	$classes['DataBrowser']					= $path_prefix."core/include/data/data_browser.class.php";
	$classes['DataPath']					= $path_prefix."core/include/data/data_path.class.php";
	$classes['DataPermission']				= $path_prefix."core/include/data/data_permission.class.php";
	$classes['DataUserData']				= $path_prefix."core/include/data/data_user_data.class.php";
	
	$classes['File']						= $path_prefix."core/include/data/file/file.class.php";
	$classes['ImageCache']					= $path_prefix."core/include/data/file/image_cache.class.php";
	
	$classes['Folder']						= $path_prefix."core/include/data/folder/folder.class.php";
	
	$classes['SystemFolder']				= $path_prefix."core/include/data/folder/system_folder.class.php";
	$classes['UserFolder']					= $path_prefix."core/include/data/folder/user_folder.class.php";
	$classes['GroupFolder']					= $path_prefix."core/include/data/folder/group_folder.class.php";
	$classes['OrganisationUnitFolder']		= $path_prefix."core/include/data/folder/organisation_unit_folder.class.php";

	$classes['Path']						= $path_prefix."core/include/data/folder/path.class.php";
	$classes['Value']						= $path_prefix."core/include/data/value/value.class.php";
	$classes['ValueType']					= $path_prefix."core/include/data/value/value_type.class.php";
	$classes['ValueExternalVar']			= $path_prefix."core/include/data/value/value_external_var.class.php";
	
	$classes['ValueVar']					= $path_prefix."core/include/data/value/value_var.class.php";
	$classes['ItemValueVar']				= $path_prefix."core/include/data/value/item_value_var.class.php";
	
	$classes['VirtualFolder']				= $path_prefix."core/include/data/virtual_folder/virtual_folder.class.php";
	
	$classes['Data_Wrapper']				= $path_prefix."core/include/data/data.wrapper.class.php";
?>