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
$check_statement = "SELECT id FROM core_locations";

$statement = array();

$statement[] = "CREATE TABLE core_location_types
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_location_types_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_locations
(
  id serial NOT NULL,
  toid integer,
  type_id integer,
  name text,
  additional_name text,
  prefix boolean,
  CONSTRAINT core_locations_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_locations ADD CONSTRAINT core_locations_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_locations (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_locations ADD CONSTRAINT core_locations_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_location_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";
?>