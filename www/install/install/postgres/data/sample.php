<?php

$statement = array();

// Register Module
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'sample', 'sample', '0.3.9.9-5');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_sample_has_folder', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_sample_has_items', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_sample_has_locations', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_sample_has_organisation_units', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_sample_has_users', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_sample_is_item', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_sample_template_cats', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_sample_templates', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_samples', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','core_virtual_folder_is_sample', NULL);";

$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'sample','get_sample_id_by_folder_id', NULL);";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (5, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (5, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (5,5)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (5,5,'samples','filesystem/samples','f','f',NULL)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (1,5)";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'samples')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (10000,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'samples')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (101,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'samples')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (102,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'samples')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (109,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'samples')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (110,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'samples')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (111,currval('core_data_entities_id_seq'::regclass))";


?>