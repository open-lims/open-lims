<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'data', 'data', '0.4.0.0');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_entities', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_entity_has_data_entities', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_entity_is_item', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_field_has_methods', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_field_limits', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_field_values', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_fields', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_has_non_template', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_has_template', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_limits', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_methods', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_non_template_has_fields', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_non_templates', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_template_has_fields', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_templates', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_versions', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameters', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_user_data', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_file_image_cache', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_file_version_blobs', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_file_versions', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_files', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_folder_concretion', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_folder_is_group_folder', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_folder_is_organisation_unit_folder', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_folder_is_system_folder', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_folder_is_user_folder', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_folders', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_value_types', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_value_var_cases', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_value_versions', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_values', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_virtual_folders', NULL);";

$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','get_all_file_versions', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','get_all_value_versions', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','search_get_sub_folders', NULL);";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (1, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (3, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (6, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (7, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (8, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (9, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (51, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (52, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (101, '2011-01-01 08:00:00+01', 1, 1, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (102, '2011-01-01 08:00:00+01', 1, 2, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (109, '2011-01-01 08:00:00+01', 1, 9, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (110, '2011-01-01 08:00:00+01', 1, 10, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (111, '2011-01-01 08:00:00+01', 1, 11, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (10000, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (10001, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (10002, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "SELECT pg_catalog.setval('core_data_entities_id_seq', 10003, true);";

$statement[] = "INSERT INTO core_items (id,datetime) VALUES (1, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (3, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (6, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (7, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (8, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (9, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (51, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (52, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (101, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (102, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (109, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (110, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (111, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (10000, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (10001, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (10002, '2011-01-01 08:00:00+01')";
$statement[] = "SELECT pg_catalog.setval('core_items_id_seq', 10003, true);";

$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (1,1)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (3,3)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (6,6)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (7,7)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (8,8)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (9,9)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (51,51)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (52,52)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (101,101)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (102,102)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (109,109)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (110,110)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (111,111)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (10000,10000)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (10001,10001)";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (10002,10002)";

$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (1,1,'','filesystem','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (3,3,'organisation_units','filesystem/organisation_units','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (6,6,'temp','filesystem/temp','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (7,7,'templates','filesystem/templates','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (8,8,'users','filesystem/users','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (9,9,'groups','filesystem/groups','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (51,51,'OLDL','filesystem/templates/OLDL','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (52,52,'OLVDL','filesystem/templates/OLVDL','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (101,101,'Administrators','filesystem/groups/1','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (102,102,'Member-Administrators','filesystem/groups/2','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (109,109,'Group-Leaders','filesystem/groups/9','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (110,110,'Users','filesystem/groups/10','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (111,111,'Quality-Managers','filesystem/groups/11','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (10000,10000,'system','filesystem/users/1','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (10001,10001,'_private','filesystem/users/1/_private','f','f',NULL)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (10002,10002,'_public','filesystem/users/1/_public','f','f',NULL)";
$statement[] = "SELECT pg_catalog.setval('core_folders_id_seq', 10003, true);";

$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (1,3)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (1,6)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (1,7)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (1,8)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (1,9)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (7,51)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (7,52)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (9,101)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (9,102)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (9,109)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (9,110)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (9,111)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (8,10000)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (10000,10001)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (10000,10002)";

$statement[] = "INSERT INTO core_folder_is_system_folder (folder_id) VALUES (1)";
$statement[] = "INSERT INTO core_folder_is_system_folder (folder_id) VALUES (3)";
$statement[] = "INSERT INTO core_folder_is_system_folder (folder_id) VALUES (6)";
$statement[] = "INSERT INTO core_folder_is_system_folder (folder_id) VALUES (7)";
$statement[] = "INSERT INTO core_folder_is_system_folder (folder_id) VALUES (8)";
$statement[] = "INSERT INTO core_folder_is_system_folder (folder_id) VALUES (9)";

$statement[] = "INSERT INTO core_folder_is_user_folder (user_id, folder_id) VALUES (1,10000)";

$statement[] = "INSERT INTO core_data_user_data (user_id,quota,filesize) VALUES (1,NULL,0)";

$statement[] = "INSERT INTO core_folder_is_group_folder (group_id, folder_id) VALUES (1,101)";
$statement[] = "INSERT INTO core_folder_is_group_folder (group_id, folder_id) VALUES (2,102)";
$statement[] = "INSERT INTO core_folder_is_group_folder (group_id, folder_id) VALUES (9,109)";
$statement[] = "INSERT INTO core_folder_is_group_folder (group_id, folder_id) VALUES (10,110)";
$statement[] = "INSERT INTO core_folder_is_group_folder (group_id, folder_id) VALUES (11,111)";


// Parameter Example Data

$statement[] = "INSERT INTO core_data_parameter_templates (id, internal_name, name, created_by, datetime) VALUES (1, 'BC', 'Blood Count', 1, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_parameter_templates (id, internal_name, name, created_by, datetime) VALUES (2, 'DBC', 'Differential Blood Count', 1, '2011-01-01 08:00:00+01')";
$statement[] = "SELECT pg_catalog.setval('core_data_parameter_templates_id_seq', 3, true);";

$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (1, 'WBC', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (2, 'RBC', 0, NULL, NULL, NULL, 2)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (3, 'HGB', 0, NULL, NULL, NULL, 3)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (4, 'HCT', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (5, 'MCV', 0, NULL, 24, -15, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (6, 'MCH', 0, NULL, 7, -12, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (7, 'MCHC', 0, NULL, NULL, NULL, 3)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (8, 'RDW', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (9, 'Plt', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (10, 'MPV', 0, NULL, 24, -15, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (11, 'NE%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (12, 'LY%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (13, 'MO%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (14, 'EO%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (15, 'BA%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (16, 'NE#', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (17, 'LY#', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (18, 'MO#', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (19, 'EO#', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (20, 'BA#', 0, NULL, NULL, NULL, 1)";
$statement[] = "SELECT pg_catalog.setval('core_data_parameter_fields_id_seq', 21, true);";

$statement[] = "INSERT INTO core_data_parameter_limits (id, name) VALUES (1, 'First Limit')";
$statement[] = "INSERT INTO core_data_parameter_limits (id, name) VALUES (2, 'First Limit')";
$statement[] = "SELECT pg_catalog.setval('core_data_parameter_limits_id_seq', 3, true);";

$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 1, 11.1, 3.9)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 2, 5.7, 4.2)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 3, 16.9, 13.2)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 4, 49, 38.5)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 5, 97, 80)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 6, 33.5, 27.5)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 7, 36, 32)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 8, 15, 11)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 9, 390, 140)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 10, 11.5, 7.5)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 11, 80, 38)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 12, 49, 15)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 13, 13, 0)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 14, 8, 0)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 15, 2, 0)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 16, 8, 1.6)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 17, 3.5, 1)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 18, 0.9, 0.04)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 19, 0.6, 0.03)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 20, 0.125, 0)";

$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 1, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 2, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 3, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 4, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 5, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 6, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 7, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 8, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 9, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 10, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 11, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 12, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 13, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 14, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 15, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 16, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 17, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 18, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 19, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 20, NULL)";

$statement[] = "INSERT INTO core_data_parameter_methods (id, name) VALUES (1, 'Counting')";
$statement[] = "SELECT pg_catalog.setval('core_data_parameter_methods_id_seq', 3, true);";
?>