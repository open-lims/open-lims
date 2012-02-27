<?php

$statement = array();

$statement[] = "UPDATE core_base_includes SET db_version = '0.3.9.9-5' WHERE name='location'";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'location','core_location_types', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'location','core_locations', NULL);";

?>