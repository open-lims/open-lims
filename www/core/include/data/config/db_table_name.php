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
	define("DATA_ENTITY_HAS_DATA_ENTITY_TABLE", 		"core_data_entity_has_data_entities");
	define("DATA_ENTITY_IS_ITEM_TABLE", 				"core_data_entity_is_item");
	define("DATA_ENTITY_TABLE", 						"core_data_entities");
	define("DATA_USER_DATA_TABLE", 						"core_data_user_data");
	
	define("FILE_IMAGE_CACHE_TABLE", 					"core_file_image_cache");
	define("FILE_VERSION_TABLE", 						"core_file_versions");
	define("FILE_TABLE", 								"core_files");
	
	define("FOLDER_CONCRETION_TABLE", 					"core_folder_concretion");
	define("FOLDER_IS_GROUP_FOLDER_TABLE", 				"core_folder_is_group_folder");
	define("FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE", 	"core_folder_is_organisation_unit_folder");
	define("FOLDER_IS_SYSTEM_FOLDER_TABLE", 			"core_folder_is_system_folder");
	define("FOLDER_IS_USER_FOLDER_TABLE", 				"core_folder_is_user_folder");
	define("FOLDER_TABLE", 								"core_folders");

	define("VALUE_TYPE_TABLE", 							"core_value_types");
	define("VALUE_VAR_CASE_TABLE", 						"core_value_var_cases");
	define("VALUE_VERSION_TABLE", 						"core_value_versions");
	define("VALUE_TABLE", 								"core_values");
	
	define("PARAMETER_FIELD_HAS_METHOD_TABLE",			"core_data_parameter_field_has_methods");
	define("PARAMETER_FIELD_LIMIT_TABLE",				"core_data_parameter_field_limits");
	define("PARAMETER_FIELD_VALUE_TABLE",				"core_data_parameter_field_values");
	define("PARAMETER_FIELD_TABLE",						"core_data_parameter_fields");
	define("PARAMETER_HAS_NON_TEMPLATE_TABLE",			"core_data_parameter_has_non_template");
	define("PARAMETER_HAS_TEMPLATE_TABLE",				"core_data_parameter_has_template");
	define("PARAMETER_LIMIT_TABLE",						"core_data_parameter_limits");
	define("PARAMETER_METHOD_TABLE",					"core_data_parameter_methods");
	define("PARAMETER_NON_TEMPLATE_HAS_FIELD_TABLE",	"core_data_parameter_non_template_has_fields");
	define("PARAMETER_NON_TEMPLATE_TABLE",				"core_data_parameter_non_templates");
	define("PARAMETER_TEMPLATE_HAS_FIELD_TABLE",		"core_data_parameter_template_has_fields");
	define("PARAMETER_TEMPLATE_TABLE",					"core_data_parameter_templates");
	define("PARAMETER_VERSION_TABLE",					"core_data_parameter_versions");
	define("PARAMETER_TABLE",							"core_data_parameters");
	
	define("VIRTUAL_FOLDER_TABLE", 						"core_virtual_folders");
?>