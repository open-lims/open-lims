<?php
/**
 * @package base
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
 * Autoload-Function
 * Loads required classes
 * @param string $classname
 */
function __autoload($classname)
{
	if ($GLOBALS[autoload_prefix])
	{
		$path_prefix = $GLOBALS[autoload_prefix];
	}
	else
	{
		$path_prefix = "";
	}
	
	$classes['AuthForgotPasswordSendFailedException']	= $path_prefix."core/include/base/exceptions/auth_forgot_password_send_failed_exception.class.php";
	$classes['AuthUserNotFoundException']				= $path_prefix."core/include/base/exceptions/auth_user_not_found_exception.class.php";
	$classes['IdMissingException']						= $path_prefix."core/include/base/exceptions/id_missing_exception.class.php";
	
	$classes['Auth'] 						= $path_prefix."core/include/base/auth.class.php";
	$classes['Communicator']				= $path_prefix."core/include/base/communicator.class.php";
	$classes['DatetimeHandler']				= $path_prefix."core/include/base/datetime_handler.class.php";
	$classes['ExceptionHandler']			= $path_prefix."core/include/base/exception_handler.class.php";
	$classes['Regional']					= $path_prefix."core/include/base/regional.class.php";
	$classes['System']						= $path_prefix."core/include/base/system.class.php";
	$classes['SystemMessage']				= $path_prefix."core/include/base/system_message.class.php";
	
	$classes['OrganisationUnit']			= $path_prefix."core/include/organisation_unit/organisation_unit.class.php";
	
	$classes['ProjectException']			= $path_prefix."core/include/project/exceptions/project_exception.class.php";
	$classes['ProjectSecurityException']	= $path_prefix."core/include/project/exceptions/project_security_exception.class.php";
	
	$classes['Project'] 					= $path_prefix."core/include/project/project.class.php";
	$classes['ProjectItem'] 				= $path_prefix."core/include/project/project_item.class.php";
	$classes['ProjectLog']					= $path_prefix."core/include/project/project_log.class.php";
	$classes['ProjectPermission'] 			= $path_prefix."core/include/project/project_permission.class.php";
	$classes['ProjectSecurity'] 			= $path_prefix."core/include/project/project_security.class.php";
	$classes['ProjectStatus']				= $path_prefix."core/include/project/project_status.class.php";
	$classes['ProjectStatusRelation']		= $path_prefix."core/include/project/project_status_relation.class.php";
	$classes['ProjectTask']					= $path_prefix."core/include/project/project_task.class.php";
	$classes['ProjectTaskPoint']			= $path_prefix."core/include/project/project_task_point.class.php";
	$classes['ProjectTemplate']				= $path_prefix."core/include/project/project_template.class.php";
	$classes['ProjectTemplateCat']			= $path_prefix."core/include/project/project_template_cat.class.php";
	
	$classes['SampleException']				= $path_prefix."core/include/sample/exceptions/sample_exception.class.php";
	$classes['SampleSecurityException']		= $path_prefix."core/include/sample/exceptions/sample_security_exception.class.php";
	
	$classes['Sample']						= $path_prefix."core/include/sample/sample.class.php";
	$classes['SampleDepository']			= $path_prefix."core/include/sample/sample_depository.class.php";
	$classes['SampleItem']					= $path_prefix."core/include/sample/sample_item.class.php";
	$classes['SampleSecurity']				= $path_prefix."core/include/sample/sample_security.class.php";
	$classes['SampleTemplate']				= $path_prefix."core/include/sample/sample_template.class.php";
	$classes['SampleTemplateCat']			= $path_prefix."core/include/sample/sample_template_cat.class.php";
	
	$classes['Method']						= $path_prefix."core/include/method/method.class.php";
	$classes['MethodCat']					= $path_prefix."core/include/method/method_cat.class.php";
	$classes['MethodType']					= $path_prefix."core/include/method/method_type.class.php";
	
	$classes['User'] 						= $path_prefix."core/include/user/user.class.php";
	$classes['Group'] 						= $path_prefix."core/include/user/group.class.php";
	
	$classes['Item']						= $path_prefix."core/include/item/item.class.php";
	$classes['ItemClass']					= $path_prefix."core/include/item/item_class.class.php";
	$classes['ItemHasProjectStatus']		= $path_prefix."core/include/item/item_has_project_status.class.php";
	$classes['ItemHasProjectLog']			= $path_prefix."core/include/item/item_has_project_log.class.php";
	$classes['ItemHasSampleGid']			= $path_prefix."core/include/item/item_has_sample_gid.class.php";
	$classes['ItemInformation']				= $path_prefix."core/include/item/item_information.class.php";
	
	$classes['Xml']							= $path_prefix."core/include/parser/xml.class.php";
	
	$classes['XmlCache']					= $path_prefix."core/include/template/xml_cache.class.php";
	$classes['Oldl']						= $path_prefix."core/include/template/oldl.class.php";
	$classes['Olvdl']						= $path_prefix."core/include/template/olvdl.class.php";
	
	$classes['DataException']				= $path_prefix."core/include/data/exceptions/data_exception.class.php";
	$classes['DataSecurityException']		= $path_prefix."core/include/data/exceptions/data_security_exception.class.php";
		
	$classes['DataBrowser']					= $path_prefix."core/include/data/data_browser.class.php";
	$classes['DataPath']					= $path_prefix."core/include/data/data_path.class.php";
	$classes['DataPermission']				= $path_prefix."core/include/data/data_permission.class.php";
	$classes['File']						= $path_prefix."core/include/data/file/file.class.php";
	$classes['Folder']						= $path_prefix."core/include/data/folder/folder.class.php";
	$classes['Object']						= $path_prefix."core/include/data/object/object.class.php";
	$classes['ObjectPermission']			= $path_prefix."core/include/data/object/object_permission.class.php";
	$classes['Path']						= $path_prefix."core/include/data/folder/path.class.php";
	$classes['Value']						= $path_prefix."core/include/data/value/value.class.php";
	$classes['ValueVar']					= $path_prefix."core/include/data/value/value_var.class.php";
	$classes['ValueType']					= $path_prefix."core/include/data/value/value_type.class.php";
	$classes['ValueExternalVar']			= $path_prefix."core/include/data/value/value_external_var.class.php";
	$classes['VirtualFolder']				= $path_prefix."core/include/data/folder/virtual_folder.class.php";

	$classes['DataSearch_Wrapper']			= $path_prefix."core/include/wrapper/data.search.wrapper.class.php";
	$classes['SystemLog_Wrapper']			= $path_prefix."core/include/wrapper/system_log.list.wrapper.class.php";
	$classes['Data_Wrapper']				= $path_prefix."core/include/wrapper/data.wrapper.class.php";
	$classes['Project_Wrapper']				= $path_prefix."core/include/wrapper/project.wrapper.class.php";
	$classes['Sample_Wrapper']				= $path_prefix."core/include/wrapper/sample.wrapper.class.php";

	if (isset($classes[$classname])) {
		require_once($classes[$classname]);
	}
	
}

?>