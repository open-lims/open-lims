<?php 
/**
 * @package base
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
	define("BASE_BINARY_TABLE", 		"core_binaries");
	define("BASE_SERVICE_TABLE", 		"core_services");
	
	define("BASE_BATCH_RUN_TABLE", 		"core_base_batch_runs");
	define("BASE_BATCH_TYPE_TABLE", 	"core_base_batch_types");
	
	define("COUNTRY_TABLE", 			"core_countries");
	define("CURRENCY_TABLE", 			"core_currencies");
	define("LANGUAGE_TABLE", 			"core_languages");
	define("MEASURING_UNIT_CATEGORY_TABLE", "core_base_measuring_unit_categories");
	define("MEASURING_UNIT_TABLE", 		"core_base_measuring_units");
	define("MEASURING_UNIT_RATIO_TABLE", 		"core_base_measuring_unit_ratios");
	define("PAPER_SIZE_TABLE", 			"core_paper_sizes");
	define("SESSION_VALUE_TABLE", 		"core_session_values");
	define("SESSION_TABLE", 			"core_sessions");
	define("SYSTEM_LOG_TYPE_TABLE", 	"core_system_log_types");
	define("SYSTEM_LOG_TABLE", 			"core_system_log");
	define("SYSTEM_MESSAGE_TABLE", 		"core_system_messages");
	define("TIMEZONE_TABLE", 			"core_timezones");
	
	define("GROUP_HAS_USER_TABLE", 		"core_group_has_users");
	define("GROUP_TABLE", 				"core_groups");
	define("USER_ADMIN_SETTING_TABLE", 	"core_user_admin_settings");
	define("USER_REGIONAL_SETTING_TABLE","core_user_regional_settings");
	define("USER_PROFILE_TABLE", 		"core_user_profiles");
	define("USER_TABLE", 				"core_users");
	
	define("EXTENSION_TABLE", 			"core_extensions");
?>