<?php

$statement = array();

$statement[] = "UPDATE core_base_includes SET db_version = '0.3.9.9-5' WHERE name='manufacturer'";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'manufacturer','core_manufacturers', NULL);";

?>