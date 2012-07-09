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
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'organisation_unit', 'organisation_unit', '0.3.9.9-6');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_groups', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_leaders', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_members', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_owners', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_has_quality_managers', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_unit_types', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','core_organisation_units', NULL);";

$statement[] = "INSERT INTO core_organisation_unit_types (id,name,icon) VALUES (nextval('core_organisation_unit_types_id_seq'::regclass),'root','root.png')";
$statement[] = "INSERT INTO core_organisation_unit_types (id,name,icon) VALUES (nextval('core_organisation_unit_types_id_seq'::regclass),'institute','institutes.png')";
$statement[] = "INSERT INTO core_organisation_unit_types (id,name,icon) VALUES (nextval('core_organisation_unit_types_id_seq'::regclass),'lab-group','ou_groups.png')";

$statement[] = "INSERT INTO core_base_include_functions (id,include,function_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'organisation_unit','get_organisation_unit_childs', NULL);";

$statement[] = "INSERT INTO core_groups (id,name) VALUES (9, 'Group-Leaders')";
$statement[] = "INSERT INTO core_groups (id,name) VALUES (11, 'Quality-Managers')";

?>