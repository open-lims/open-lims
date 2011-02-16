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
	$classes['ConcreteFolderCaseInterface']	= $path_prefix."core/include/data/folder/interfaces/concrete_folder_case.interface.php";	

	$classes['DataException']				= $path_prefix."core/include/data/exceptions/data_exception.class.php";
	$classes['DataSecurityException']		= $path_prefix."core/include/data/exceptions/data_security_exception.class.php";

	$classes['DataEntity']					= $path_prefix."core/include/data/data_entity.class.php";
	$classes['DataEntityPermission']		= $path_prefix."core/include/data/data_entity_permission.class.php";
	$classes['DataBrowser']					= $path_prefix."core/include/data/data_browser.class.php";
	$classes['DataPath']					= $path_prefix."core/include/data/data_path.class.php";
	$classes['DataPermission']				= $path_prefix."core/include/data/data_permission.class.php";
	$classes['DataUserData']				= $path_prefix."core/include/data/data_user_data.class.php";
	
	$classes['File']						= $path_prefix."core/include/data/file/file.class.php";
	$classes['Folder']						= $path_prefix."core/include/data/folder/folder.class.php";
	$classes['UserFolder']					= $path_prefix."core/include/data/folder/user_folder.class.php";
	$classes['GroupFolder']					= $path_prefix."core/include/data/folder/group_folder.class.php";
	$classes['OrganisationUnitFolder']		= $path_prefix."core/include/data/folder/organisation_unit_folder.class.php";

	$classes['Path']						= $path_prefix."core/include/data/folder/path.class.php";
	$classes['Value']						= $path_prefix."core/include/data/value/value.class.php";
	$classes['ValueVar']					= $path_prefix."core/include/data/value/value_var.class.php";
	$classes['ValueType']					= $path_prefix."core/include/data/value/value_type.class.php";
	$classes['ValueExternalVar']			= $path_prefix."core/include/data/value/value_external_var.class.php";
	
	$classes['VirtualFolder']				= $path_prefix."core/include/data/virtual_folder/virtual_folder.class.php";
?>