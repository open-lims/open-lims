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
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'location', 'location', '0.3.9.9-6');";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'location','core_location_types', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'location','core_locations', NULL);";

$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Building');";
$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Floor');";
$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Room');";
$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Lab');";
$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Freezer');";
$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Refrigerator');";
$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Deep Freezer');";
$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Ice-chest');";
$statement[] = "INSERT INTO core_location_types (id,name) VALUES (nextval('core_location_types_id_seq'::regclass), 'Cold-room');";

?>