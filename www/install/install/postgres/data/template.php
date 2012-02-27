<?php

$statement = array();

// Register Module
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'template', 'template', '0.3.9.9-5');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'template','core_oldl_templates', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'template','core_olvdl_templates', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'template','core_xml_cache', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'template','core_xml_cache_elements', NULL);";

?>