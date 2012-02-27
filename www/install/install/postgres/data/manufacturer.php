<?php

$statement = array();

// Register Module
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'manufacturer', 'manufacturer', '0.3.9.9-5');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'manufacturer','core_manufacturers', NULL);";

?>