<?php 
/**
 * @package sample
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
	$classes['SampleException']										= $path_prefix."core/include/sample/exceptions/sample.exception.class.php";
	
	$classes['SampleIDMissingException']							= $path_prefix."core/include/sample/exceptions/sample_id_missing.exception.class.php";
	$classes['SampleNotFoundException']								= $path_prefix."core/include/sample/exceptions/sample_not_found.exception.class.php";
	
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