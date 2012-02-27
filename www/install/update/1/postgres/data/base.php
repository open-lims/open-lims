<?php

$statement = array();

$statement[] = "UPDATE core_base_includes SET db_version = '0.3.9.9-5' WHERE name='base'";

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

$statement[] = "INSERT INTO core_system_log_types (id,name) VALUES (4,'Deleted Objects');";

?>