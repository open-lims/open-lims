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

$statement[] = "INSERT INTO core_sample_template_cats (id,name) VALUES(nextval('core_sample_template_cats_id_seq'::regclass),'General');";
$statement[] = "INSERT INTO core_sample_template_cats (id,name) VALUES(nextval('core_sample_template_cats_id_seq'::regclass),'MicroArray');";
$statement[] = "INSERT INTO core_sample_template_cats (id,name) VALUES(nextval('core_sample_template_cats_id_seq'::regclass),'Microscopy');";
$statement[] = "INSERT INTO core_sample_template_cats (id,name) VALUES(nextval('core_sample_template_cats_id_seq'::regclass),'RNA');";

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