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
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'project', 'project', '0.4.0.0');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_has_extension_runs', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_has_folder', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_has_items', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_has_project_status', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_links', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_log', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_log_has_items', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_log_has_project_status', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_permissions', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_status', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_status_has_folder', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_task_has_previous_tasks', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_task_milestones', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_task_processes', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_task_status_processes', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_tasks', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_template_cats', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_templates', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_project_user_data', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_projects', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','core_virtual_folder_is_project', NULL);";

$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','get_project_id_by_folder_id', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','get_project_supplementary_folder', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','project_permission_group', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','project_permission_organisation_unit', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','project_permission_user', NULL);";
$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'project','search_get_project_subprojects', NULL);";

$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (0,'canceled','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (1,'creating','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (2,'finished','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (100,'in progress','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10001,'RNA','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10002,'MicroArray','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10003,'cDNA','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10004,'hybridisation','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10005,'washing','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10006,'scanning','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10007,'spotfinding','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10008,'manual curation','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (10009,'analysis','t','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (20001,'sample description','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (20002,'specify experiment','f','f',NULL);";
$statement[] = "INSERT INTO core_project_status (id,name,analysis,blocked,comment) VALUES (20003,'import data','f','f',NULL);";
$statement[] = "SELECT pg_catalog.setval('core_project_status_id_seq', 999999000, true);";

$statement[] = "INSERT INTO core_project_template_cats (id,name) VALUES (nextval('core_project_template_cats_id_seq'::regclass), 'General');";
$statement[] = "INSERT INTO core_project_template_cats (id,name) VALUES (nextval('core_project_template_cats_id_seq'::regclass), 'MicroArray');";
$statement[] = "INSERT INTO core_project_template_cats (id,name) VALUES (nextval('core_project_template_cats_id_seq'::regclass), 'Microscopy');";

$statement[] = "INSERT INTO core_value_types (id,name,template_id) VALUES (2,'project description',NULL)";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (4, '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (4, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (4,4)";
$statement[] = "INSERT INTO core_folders (id,data_entity_id,name,path,deleted,blob,flag) VALUES (4,4,'projects','filesystem/projects','f','f',NULL)";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (1,4)";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'projects')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (10000,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'projects')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (101,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'projects')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (102,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'projects')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (109,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'projects')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (110,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_data_entities (id,datetime,owner_id,owner_group_id,permission,automatic) VALUES (nextval('core_data_entities_id_seq'::regclass), '2011-01-01 08:00:00+01', 1, NULL, NULL, 't')";
$statement[] = "INSERT INTO core_items (id,datetime) VALUES (nextval('core_items_id_seq'::regclass), '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_entity_is_item (data_entity_id,item_id) VALUES (currval('core_data_entities_id_seq'::regclass),currval('core_items_id_seq'::regclass))";
$statement[] = "INSERT INTO core_virtual_folders (id,data_entity_id,name) VALUES (nextval('core_virtual_folders_id_seq'::regclass),currval('core_data_entities_id_seq'::regclass), 'projects')";
$statement[] = "INSERT INTO core_data_entity_has_data_entities (data_entity_pid,data_entity_cid) VALUES (111,currval('core_data_entities_id_seq'::regclass))";

$statement[] = "INSERT INTO core_folder_is_system_folder (folder_id) VALUES (4)";

?>