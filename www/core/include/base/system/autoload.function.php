<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
	if (isset($GLOBALS['autoload_prefix']))
	{
		$path_prefix = $GLOBALS['autoload_prefix'];
	}
	else
	{
		$path_prefix = "";
	}
	
	// Batch
	$classes['BaseBatchException']										= $path_prefix."core/include/base/batch/exceptions/base_batch.exception.class.php";
	$classes['BaseBatchNotFoundException']								= $path_prefix."core/include/base/batch/exceptions/base_batch_not_found.exception.class.php";
	$classes['BaseBatchMissingException']								= $path_prefix."core/include/base/batch/exceptions/base_batch_missing.exception.class.php";
	$classes['BaseBatchInvalidArgumentException']						= $path_prefix."core/include/base/batch/exceptions/base_batch_invalid_argument.exception.class.php";

	$classes['Batch']													= $path_prefix."core/include/base/batch/batch.class.php";
	$classes['Batch_Wrapper']											= $path_prefix."core/include/base/batch/batch.wrapper.class.php";
	
	// Environment
	$classes['BaseEnvironmentException']								= $path_prefix."core/include/base/environment/exceptions/base_environment.exception.class.php";
	$classes['BaseEnvironmentCurrencyException']						= $path_prefix."core/include/base/environment/exceptions/base_environment_currency.exception.class.php";
	$classes['BaseEnvironmentCurrencyIDMissingException']				= $path_prefix."core/include/base/environment/exceptions/base_environment_currency_id_missing.exception.class.php";
	$classes['BaseEnvironmentCurrencyNotFoundException']				= $path_prefix."core/include/base/environment/exceptions/base_environment_currency_not_found.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitException']					= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitIDMissingException']			= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit_id_missing.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitNotFoundException']			= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit_not_found.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitCategoryException']			= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit_category.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitCategoryIDMissingException']	= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit_category_id_missing.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitCategoryNotFoundException']	= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit_category_not_found.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitRatioException']				= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit_ratio.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitRatioIDMissingException']		= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit_ratio_id_missing.exception.class.php";
	$classes['BaseEnvironmentMeasuringUnitRatioNotFoundException']		= $path_prefix."core/include/base/environment/exceptions/base_environment_measuring_unit_ratio_not_found.exception.class.php";
	$classes['BaseEnvironmentPaperSizeException']						= $path_prefix."core/include/base/environment/exceptions/base_environment_paper_size.exception.class.php";
	$classes['BaseEnvironmentPaperSizeIDMissingException']				= $path_prefix."core/include/base/environment/exceptions/base_environment_paper_size_id_missing.exception.class.php";
	$classes['BaseEnvironmentPaperSizeNotFoundException']				= $path_prefix."core/include/base/environment/exceptions/base_environment_paper_size_not_found.exception.class.php";
	
	$classes['Country']													= $path_prefix."core/include/base/environment/country.class.php";
	$classes['Currency']												= $path_prefix."core/include/base/environment/currency.class.php";
	$classes['DatetimeHandler']											= $path_prefix."core/include/base/environment/datetime_handler.class.php";
	$classes['Language']												= $path_prefix."core/include/base/environment/language.class.php";
	$classes['MeasuringUnit']											= $path_prefix."core/include/base/environment/measuring_unit.class.php";
	$classes['MeasuringUnitCategory']									= $path_prefix."core/include/base/environment/measuring_unit_category.class.php";
	$classes['MeasuringUnitRatio']										= $path_prefix."core/include/base/environment/measuring_unit_ratio.class.php";
	$classes['PaperSize']												= $path_prefix."core/include/base/environment/paper_size.class.php";
	$classes['Timezone']												= $path_prefix."core/include/base/environment/timezone.class.php";
	
	$classes['Environment_Wrapper']										= $path_prefix."core/include/base/environment/environment.wrapper.class.php";
	
	
	// Security
	$classes['AuthForgotPasswordSendFailedException']					= $path_prefix."core/include/base/security/exceptions/auth_forgot_password_send_failed_exception.class.php";
	$classes['AuthUserNotFoundException']								= $path_prefix."core/include/base/security/exceptions/auth_user_not_found_exception.class.php";
	
	$classes['BaseUserAccessDeniedException']							= $path_prefix."core/include/base/security/exceptions/base_user_access_denied.exception.class.php";
	
	$classes['Auth'] 													= $path_prefix."core/include/base/security/auth.class.php";
	
	
	// System
	$classes['EventListenerInterface']									= $path_prefix."core/include/base/system/interfaces/event_listener.interface.php"; 
	
	$classes['BaseException']											= $path_prefix."core/include/base/system/exceptions/base.exception.class.php";
	$classes['BasePHPErrorException']									= $path_prefix."core/include/base/system/exceptions/base_php_error.exception.class.php";
	$classes['BaseAjaxException']										= $path_prefix."core/include/base/system/exceptions/base_ajax.exception.class.php";
	$classes['BaseAjaxArgumentMissingException']						= $path_prefix."core/include/base/system/exceptions/base_ajax_argument_missing.exception.class.php";
	$classes['BaseAjaxDependentArgumentMissingException']				= $path_prefix."core/include/base/system/exceptions/base_ajax_dependent_argument_missing.exception.class.php";
	$classes['BaseAssistantException']									= $path_prefix."core/include/base/system/exceptions/base_assistant.exception.class.php";
	$classes['BaseAssistantRequestedPageNotExistsException']			= $path_prefix."core/include/base/system/exceptions/base_assistant_requested_page_not_exsits.exception.class.php";
	$classes['BaseReportException']										= $path_prefix."core/include/base/system/exceptions/base_report.exception.class.php";
	$classes['BaseReportTCPDFClassMissingException']					= $path_prefix."core/include/base/system/exceptions/base_report_tcpdf_class_missing.exception.class.php";
	$classes['BaseReportTCPDFFileMissingException']						= $path_prefix."core/include/base/system/exceptions/base_report_tcpdf_file_missing.exception.class.php";
	$classes['BaseReportTCPDFLanguageFileMissingException']				= $path_prefix."core/include/base/system/exceptions/base_report_tcpdf_language_file_missing.exception.class.php";
	
	$classes['BaseModuleNavigationException']							= $path_prefix."core/include/base/system/exceptions/base_module_navigation.exception.class.php";
	$classes['BaseModuleNavigationIDMissingException']					= $path_prefix."core/include/base/system/exceptions/base_module_navigation_id_missing.exception.class.php";
	$classes['BaseModuleNavigationNotFoundException']					= $path_prefix."core/include/base/system/exceptions/base_module_navigation_not_found.exception.class.php";
	
	$classes['BaseServiceException']									= $path_prefix."core/include/base/system/exceptions/base_service.exception.class.php";
	$classes['BaseServiceIDMissingException']							= $path_prefix."core/include/base/system/exceptions/base_service_id_missing.exception.class.php";
	$classes['BaseServiceNotFoundException']							= $path_prefix."core/include/base/system/exceptions/base_service_not_found.exception.class.php";
	
	$classes['BaseRegistryException']									= $path_prefix."core/include/base/system/exceptions/base_registry.exception.class.php";
	$classes['BaseRegistryIDMissingException']							= $path_prefix."core/include/base/system/exceptions/base_registry_id_missing.exception.class.php";
	$classes['BaseRegistryNotFoundException']							= $path_prefix."core/include/base/system/exceptions/base_registry_not_found.exception.class.php";
	
	$classes['Convert']													= $path_prefix."core/include/base/system/convert.class.php";
	$classes['Cron']													= $path_prefix."core/include/base/system/cron.class.php";
	$classes['EventHandler']											= $path_prefix."core/include/base/system/event_handler.class.php";
	$classes['ExceptionHandler']										= $path_prefix."core/include/base/system/exception_handler.class.php";
	$classes['System']													= $path_prefix."core/include/base/system/system.class.php";
	$classes['Mail']													= $path_prefix."core/include/base/system/mail.class.php";	
	$classes['ModuleDialog']											= $path_prefix."core/include/base/system/module_dialog.class.php";
	$classes['ModuleLink']												= $path_prefix."core/include/base/system/module_link.class.php";
	$classes['ModuleNavigation']										= $path_prefix."core/include/base/system/module_navigation.class.php";
	$classes['Registry']												= $path_prefix."core/include/base/system/registry.class.php";
	$classes['Retrace']													= $path_prefix."core/include/base/system/retrace.class.php";
	$classes['Service']													= $path_prefix."core/include/base/system/service.class.php";
	
	$classes['System_Wrapper']											= $path_prefix."core/include/base/system/system.wrapper.class.php";
	
	
	// System Frontend
	$classes['SystemLogException']										= $path_prefix."core/include/base/system_fe/exceptions/system_log.exception.class.php";
	$classes['SystemLogNotFoundException']								= $path_prefix."core/include/base/system_fe/exceptions/system_log_not_found.exception.class.php";
	$classes['SystemLogIDMissingException']								= $path_prefix."core/include/base/system_fe/exceptions/system_log_id_missing.exception.class.php";
	$classes['SystemMessageException']									= $path_prefix."core/include/base/system_fe/exceptions/system_message.exception.class.php";
	$classes['SystemMessageNotFoundException']							= $path_prefix."core/include/base/system_fe/exceptions/system_message_not_found.exception.class.php";
	$classes['SystemMessageIDMissingException']							= $path_prefix."core/include/base/system_fe/exceptions/system_message_id_missing.exception.class.php";
	
	$classes['SystemLog']												= $path_prefix."core/include/base/system_fe/system_log.class.php";
	$classes['SystemMessage']											= $path_prefix."core/include/base/system_fe/system_message.class.php";
	
	$classes['SystemFE_Wrapper']										= $path_prefix."core/include/base/system_fe/system_fe.wrapper.class.php";
	
	
	// User
	$classes['UserException']											= $path_prefix."core/include/base/user/exceptions/user.exception.class.php";
	$classes['UserNotFoundException']									= $path_prefix."core/include/base/user/exceptions/user_not_found.exception.class.php";
	$classes['UserIDMissingException']									= $path_prefix."core/include/base/user/exceptions/user_id_missing.exception.class.php";
	$classes['UserDeleteException']										= $path_prefix."core/include/base/user/exceptions/user_delete.exception.class.php";
	
	$classes['GroupException']											= $path_prefix."core/include/base/user/exceptions/group.exception.php";
	$classes['GroupNotFoundException']									= $path_prefix."core/include/base/user/exceptions/group_not_found.exception.php";
	$classes['GroupIDMissingException']									= $path_prefix."core/include/base/user/exceptions/group_id_missing.exception.php";
	
	$classes['User'] 													= $path_prefix."core/include/base/user/user.class.php";
	$classes['Group'] 													= $path_prefix."core/include/base/user/group.class.php";
	$classes['Regional'] 												= $path_prefix."core/include/base/user/regional.class.php";
	
	$classes['User_Wrapper'] 											= $path_prefix."core/include/base/user/user.wrapper.class.php";
	
	
	// Extension
	$classes['BaseExtensionException']									= $path_prefix."core/include/base/extension/exceptions/base_extension.exception.class.php";
	$classes['BaseExtensionNotFoundException']							= $path_prefix."core/include/base/extension/exceptions/base_extension_not_found.exception.class.php";
	$classes['BaseExtensionFileNotFoundException']						= $path_prefix."core/include/base/extension/exceptions/base_extension_file_not_found.exception.class.php";
	$classes['BaseExtensionClassNotFoundException']						= $path_prefix."core/include/base/extension/exceptions/base_extension_class_not_found.exception.class.php";
	$classes['BaseExtensionMissingException']							= $path_prefix."core/include/base/extension/exceptions/base_extension_missing.exception.class.php";
	
	$classes['ExtensionCreateRunEvent']									= $path_prefix."core/include/base/extension/events/extension_create_run_event.class.php";

	$classes['ConcreteExtensionInterface']								= $path_prefix."core/include/base/extension/interfaces/concrete_extension.interface.php";

	$classes['Extension']												= $path_prefix."core/include/base/extension/extension.class.php";
	$classes['ExtensionHandler']										= $path_prefix."core/include/base/extension/extension_handler.class.php";
		
	$system_handler_classes = SystemHandler::get_classes();

	if (is_array($system_handler_classes) and count($system_handler_classes) >= 1)
	{
		$classes = array_merge($classes, $system_handler_classes);
	}
	
	if (isset($classes[$classname]))
	{
		if (file_exists($classes[$classname]))
		{
			require_once($classes[$classname]);
		}
	}
	
}

?>