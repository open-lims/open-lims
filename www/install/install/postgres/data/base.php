<?php
/**
 * @package install
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
$statement = array();

// Register Module
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'base', 'base', '0.3.9.9-5');";

// Register Table
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_event_listeners', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_include_files', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_include_functions', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_include_tables', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_includes', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_module_dialogs', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_module_files', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_module_links', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_module_navigation', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_modules', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_currencies', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_group_has_users', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_groups', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_languages', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_measuring_units', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_paper_sizes', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_session_values', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_sessions', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_system_log', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_system_log_types', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_system_messages', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_timezones', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_user_admin_settings', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_user_profile_settings', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_user_profiles', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_users', NULL);";

// Register Functions
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','concat', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','higher', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','nameconcat', NULL);";

// Registry
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_timezone_id', (SELECT id FROM core_base_includes WHERE name='base'), '26');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_os', (SELECT id FROM core_base_includes WHERE name='base'), 'win');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_product_user', (SELECT id FROM core_base_includes WHERE name='base'), 'University of Cologne');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_product_function', (SELECT id FROM core_base_includes WHERE name='base'), 'development server');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_html_title', (SELECT id FROM core_base_includes WHERE name='base'), 'Open-LIMS (development server)');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_update_check', (SELECT id FROM core_base_includes WHERE name='base'), 'false');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_update_check_url', (SELECT id FROM core_base_includes WHERE name='base'), 'http://update.open-lims.org/check.php');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_session_timeout', (SELECT id FROM core_base_includes WHERE name='base'), '36000');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_max_ip_failed_logins', (SELECT id FROM core_base_includes WHERE name='base'), '10');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_max_ip_lead_time', (SELECT id FROM core_base_includes WHERE name='base'), '36000');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_cron_last_run_datetime', (SELECT id FROM core_base_includes WHERE name='base'), '2011-01-01 12:00:00');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_cron_last_run_id', (SELECT id FROM core_base_includes WHERE name='base'), '1');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_cron_last_run_daily_id', (SELECT id FROM core_base_includes WHERE name='base'), '1');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_cron_last_run_weekly_id', (SELECT id FROM core_base_includes WHERE name='base'), '1');";

// Languages
$statement[] = "INSERT INTO core_languages (id,english_name,language_name,tsvector_name,iso_639,iso_3166) VALUES (nextval('core_languages_id_seq'::regclass),'English','English','english','en','GB')";
$statement[] = "SELECT pg_catalog.setval('core_languages_id_seq', 2, true);";

// Users
$statement[] = "INSERT INTO core_users VALUES (1, 'system', '096013f88fcf51a89f6d0c4e5285428e');";
$statement[] = "INSERT INTO core_user_profiles VALUES (1, NULL, '', 'main', 'administrator', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
$statement[] = "INSERT INTO core_user_profile_settings VALUES (1, 1, 26);";
$statement[] = "INSERT INTO core_user_admin_settings VALUES (1, 't', 'f', 'f', 'f', 'f', '2008-01-01 12:00:00+01', 'f', 'f');";
$statement[] = "SELECT pg_catalog.setval('core_users_id_seq', 100, true);";

// Groups
$statement[] = "INSERT INTO core_groups (id,name) VALUES (1, 'Administrators')";
$statement[] = "INSERT INTO core_groups (id,name) VALUES (2, 'Member-Administrators')";
$statement[] = "INSERT INTO core_groups (id,name) VALUES (10, 'Users')";
$statement[] = "SELECT pg_catalog.setval('core_groups_id_seq', 100, true);";
$statement[] = "INSERT INTO core_group_has_users (primary_key,group_id,user_id) VALUES (nextval('core_group_has_users_primary_key_seq'::regclass), 1, 1)";
$statement[] = "INSERT INTO core_group_has_users (primary_key,group_id,user_id) VALUES (nextval('core_group_has_users_primary_key_seq'::regclass), 10, 1)";

// Currencies
$statement[] = "INSERT INTO core_currencies (id,name,symbol,iso_4217) VALUES (nextval('core_currencies_id_seq'::regclass), 'Euro', '', 'EUR')";
$statement[] = "INSERT INTO core_currencies (id,name,symbol,iso_4217) VALUES (nextval('core_currencies_id_seq'::regclass), 'US-Dollar', '', 'USD')";

// Measuring Units
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (1,NULL,'Meter',1,'t','m',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (2,NULL,'Kilogramm',2,'t','kg',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (3,NULL,'Ampere',3,'t','A',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (4,NULL,'Kelvin',4,'t','K',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (5,NULL,'Mol',5,'t','mol',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (6,NULL,'Candela',6,'t','cd',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (7,NULL,'Second',7,'t','s',NULL)";
$statement[] = "SELECT pg_catalog.setval('core_measuring_units_id_seq', 7, true);";

// Paper Sizes
$statement[] = "INSERT INTO core_paper_sizes VALUES (2, 'A1', 594, 841, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (3, 'A2', 420, 594, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (4, 'A3', 297, 420, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (5, 'A4', 210, 297, 10, 10, 10, 10, 't', 't');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (6, 'A5', 148, 210, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (7, 'A6', 105, 148, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (8, 'Invoice', 140, 216, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (9, 'Executive', 184, 267, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (10, 'Legal', 216, 356, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (11, 'Letter', 216, 279, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (12, 'Ledger', 279, 432, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (13, 'Broadsheet', 432, 559, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (1, 'A0', 841, 1189, 10, 10, 10, 10, 't', 'f');";
$statement[] = "SELECT pg_catalog.setval('core_paper_sizes_id_seq', 19, true);";

// Sysmte Log
$statement[] = "INSERT INTO core_system_log_types VALUES (1, 'Security Notices');";
$statement[] = "INSERT INTO core_system_log_types VALUES (2, 'Open-LIMS Errors');";
$statement[] = "INSERT INTO core_system_log_types VALUES (3, 'PHP Errors');";
$statement[] = "INSERT INTO core_system_log_types VALUES (4, 'Deleted Objects');";
$statement[] = "SELECT pg_catalog.setval('core_system_log_types_id_seq', 5, true);";

// Timezones
$statement[] = "INSERT INTO core_timezones VALUES (1, 'Midway Islands, Samoa', 'Pacific/Samoa', -11);";
$statement[] = "INSERT INTO core_timezones VALUES (2, 'Hawaii, Polynesia', 'US/Hawaii', -10);";
$statement[] = "INSERT INTO core_timezones VALUES (3, 'Alaska', 'US/Alaska', -9);";
$statement[] = "INSERT INTO core_timezones VALUES (4, 'Tijuana, Los Angeles, Seattle, Vancouver', 'America/Los_Angeles', -8);";
$statement[] = "INSERT INTO core_timezones VALUES (5, 'Arizona', 'US/Arizona', -7);";
$statement[] = "INSERT INTO core_timezones VALUES (6, 'Chihuahua, La Paz, Mazatlan', 'America/Chihuahua', -7);";
$statement[] = "INSERT INTO core_timezones VALUES (7, 'Arizona, Denver, Salt Lake City, Calgary', 'America/Denver', -7);";
$statement[] = "INSERT INTO core_timezones VALUES (8, 'Chicago, Dallas, Kansas City, Winnipeg', 'America/Chicago', -6);";
$statement[] = "INSERT INTO core_timezones VALUES (9, 'Guadalajara, Mexico City, Monterrey', 'America/Monterrey', -6);";
$statement[] = "INSERT INTO core_timezones VALUES (10, 'Saskatchewan', 'Canada/Saskatchewan', -6);";
$statement[] = "INSERT INTO core_timezones VALUES (11, 'Central America', 'US/Central', -6);";
$statement[] = "INSERT INTO core_timezones VALUES (12, 'Bogota, Lima, Quito', 'America/Bogota', -5);";
$statement[] = "INSERT INTO core_timezones VALUES (13, 'East-Indiana', 'US/East-Indiana', -5);";
$statement[] = "INSERT INTO core_timezones VALUES (14, 'New York, Miami, Atlanta, Detroit, Toronto', 'America/New_York', -5);";
$statement[] = "INSERT INTO core_timezones VALUES (15, 'Atlantic (Canada)', 'Canada/Atlantic', -4);";
$statement[] = "INSERT INTO core_timezones VALUES (16, 'Carcas, La Paz', 'America/La_Paz', -4);";
$statement[] = "INSERT INTO core_timezones VALUES (17, 'Santiago', 'America/Santiago', -4);";
$statement[] = "INSERT INTO core_timezones VALUES (18, 'Newfoundland', 'Canada/Newfoundland', -3);";
$statement[] = "INSERT INTO core_timezones VALUES (19, 'Sao Paulo', 'Brazil/East', -3);";
$statement[] = "INSERT INTO core_timezones VALUES (20, 'Buenes Aires, Georgtown', 'America/Argentina/Buenos_Aires', -3);";
$statement[] = "INSERT INTO core_timezones VALUES (21, 'Greenland, Uruguay, Surinam', 'GMT+3', -3);";
$statement[] = "INSERT INTO core_timezones VALUES (22, 'Cape Verde, Greenland, South Georgia', 'Atlantic/Cape_Verde', -2);";
$statement[] = "INSERT INTO core_timezones VALUES (23, 'Azores', 'Atlantic/Azores', -1);";
$statement[] = "INSERT INTO core_timezones VALUES (24, 'Casablanca, Monrovia', 'Africa/Casablanca', 0);";
$statement[] = "INSERT INTO core_timezones VALUES (25, 'Dublin, Edinburgh, Lisbon, London', 'Europe/London', 0);";
$statement[] = "INSERT INTO core_timezones VALUES (26, 'Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna', 'Europe/Berlin', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (27, 'Belgrade, Bratislava, Budapest, Ljubljana, Prague', 'Europe/Belgrade', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (28, 'Brussels, Copenhagen, Paris, Madrid', 'Europe/Paris', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (29, 'Sarajevo, Skopje, Warsaw, Zagreb', 'Europe/Sarajevo', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (30, 'West-Central Africa', 'Africa/Lagos', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (31, 'Athens, Beirut, Istanbul, Minsk', 'Europe/Athens', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (32, 'Bucharest', 'Europe/Bucharest', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (33, 'Harare, Pratoria', 'Africa/Harare', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (34, 'Helsinki, Kiev, Riga, Sofia, Tallinn, Vilnius', 'Europe/Helsinki', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (35, 'Jerusalem', 'Asia/Jerusalem', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (36, 'Cairo', 'Africa/Cairo', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (37, 'Baghdad', 'Asia/Baghdad', 3);";
$statement[] = "INSERT INTO core_timezones VALUES (38, 'Kuwait, Riyadh', 'Asia/Kuwait', 3);";
$statement[] = "INSERT INTO core_timezones VALUES (39, 'Moscow, Saint Petersburg', 'Europe/Moscow', 3);";
$statement[] = "INSERT INTO core_timezones VALUES (40, 'Nairobi,Teheran', 'Africa/Nairobi', 3);";
$statement[] = "INSERT INTO core_timezones VALUES (41, 'Abu Dhabi, Muscat', 'Asia/Muscat', 4);";
$statement[] = "INSERT INTO core_timezones VALUES (42, 'Baku, Tbilisi, Erivan', 'Asia/Baku', 4);";
$statement[] = "INSERT INTO core_timezones VALUES (43, 'Kabul', 'Asia/Kabul', 4);";
$statement[] = "INSERT INTO core_timezones VALUES (44, 'Islamabad, Karachi, Taschkent', 'Asia/Karachi', 5);";
$statement[] = "INSERT INTO core_timezones VALUES (45, 'Yekaterinburg, New Delhi', 'Asia/Yekaterinburg', 5);";
$statement[] = "INSERT INTO core_timezones VALUES (46, 'Almaty, Novosibirsk, Kathmandu', 'Asia/Novosibirsk', 6);";
$statement[] = "INSERT INTO core_timezones VALUES (47, 'Astana, Dhaka', 'Asia/Dhaka', 6);";
$statement[] = "INSERT INTO core_timezones VALUES (48, 'Sri Jayawardenepura, Rangoon', 'Asia/Rangoon', 6);";
$statement[] = "INSERT INTO core_timezones VALUES (49, 'Bangkok, Hanoi, Jakarta', 'Asia/Jakarta', 7);";
$statement[] = "INSERT INTO core_timezones VALUES (50, 'Krasnoyarsk', 'Asia/Krasnoyarsk', 7);";
$statement[] = "INSERT INTO core_timezones VALUES (51, 'Irkutsk, Ulan Bator', 'Asia/Irkutsk', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (52, 'Kuala Lumpour, Singapore', 'Asia/Singapore', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (53, 'Beijing, Chongqing, Hong kong, Urumchi', 'Asia/Hong_Kong', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (54, 'Perth', 'Australia/Perth', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (55, 'Taipei', 'Asia/Taipei', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (56, 'Yakutsk', 'Asia/Yakutsk', 9);";
$statement[] = "INSERT INTO core_timezones VALUES (57, 'Osaka, Sapporo, Tokyo', 'Asia/Tokyo', 9);";
$statement[] = "INSERT INTO core_timezones VALUES (58, 'Seoul, Darwin, Adelaide', 'Asia/Seoul', 9);";
$statement[] = "INSERT INTO core_timezones VALUES (59, 'Brisbane', 'Australia/Brisbane', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (60, 'Canberra, Melbourne, Sydney', 'Australia/Sydney', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (61, 'Guam, Port Moresby', 'Pacific/Guam', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (62, 'Hobart', 'Australia/Hobart', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (63, 'Vladivostok', 'Asia/Vladivostok', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (64, 'Salomon Islands, New Caledonia, Magadan', 'Asia/Magadan', 11);";
$statement[] = "INSERT INTO core_timezones VALUES (65, 'Auckland, Wellington', 'Pacific/Auckland', 12);";
$statement[] = "INSERT INTO core_timezones VALUES (66, 'Fiji, Kamchatka, Marshall-Islands', 'Pacific/Fiji', 12);";
$statement[] = "INSERT INTO core_timezones VALUES (67, 'Caracas', 'America/Caracas', -4.5);";
$statement[] = "SELECT pg_catalog.setval('core_timezones_id_seq', 67, true);";

?>