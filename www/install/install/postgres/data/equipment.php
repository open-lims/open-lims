<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'equipment', 'equipment', '0.4.0.0');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'equipment','core_equipment', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'equipment','core_equipment_cats', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'equipment','core_equipment_has_organisation_units', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'equipment','core_equipment_has_responsible_persons', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'equipment','core_equipment_is_item', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'equipment','core_equipment_types', NULL);";

$statement[] = "INSERT INTO core_equipment_cats (id,toid,name) VALUES (1,1,'other');";
$statement[] = "INSERT INTO core_equipment_cats (id,toid,name) VALUES (1000,1,'Hardware');";
$statement[] = "INSERT INTO core_equipment_cats (id,toid,name) VALUES (1001,1,'Software');";
$statement[] = "SELECT pg_catalog.setval('core_equipment_cats_id_seq', 100000, true);";

$statement[] = "INSERT INTO core_equipment_types (id,toid,name,cat_id,location_id,description,manufacturer) VALUES (1,1,'manual',1,NULL,NULL,NULL);";
$statement[] = "INSERT INTO core_equipment_types (id,toid,name,cat_id,location_id,description,manufacturer) VALUES (2,2,'external',1,NULL,NULL,NULL);";
$statement[] = "INSERT INTO core_equipment_types (id,toid,name,cat_id,location_id,description,manufacturer) VALUES (3,3,'other',1,NULL,NULL,NULL);";
$statement[] = "SELECT pg_catalog.setval('core_equipment_types_id_seq', 999999000, true);";

?>