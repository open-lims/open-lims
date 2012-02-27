<?php

$statement = array();

// Register Module
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'organisation_unit', 'organisation_unit', '0.3.9.9-5');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_groups', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_leaders', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_members', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_owners', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_quality_managers', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_types', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_units', NULL);";

$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','get_organisation_unit_childs', NULL);";

$statement[] = "INSERT INTO core_groups (id,name) VALUES (9, 'Group-Leaders')";
$statement[] = "INSERT INTO core_groups (id,name) VALUES (11, 'Quality-Managers')";

?>