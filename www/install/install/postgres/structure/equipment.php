<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
$check_statement = "SELECT id FROM core_equipment";

$statement = array();

$statement[] = "CREATE TABLE core_equipment
(
  id serial NOT NULL,
  type_id integer,
  owner_id integer,
  datetime timestamp with time zone,
  CONSTRAINT core_equipment_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_equipment_cats
(
  id serial NOT NULL,
  toid integer,
  name text,
  CONSTRAINT core_equipment_cats_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_equipment_has_organisation_units
(
  equipment_id integer NOT NULL,
  organisation_unit_id integer NOT NULL,
  CONSTRAINT core_equipment_has_organisation_units_pkey PRIMARY KEY (equipment_id , organisation_unit_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_equipment_has_responsible_persons
(
  equipment_id integer NOT NULL,
  user_id integer NOT NULL,
  CONSTRAINT core_equipment_has_responsible_persons_pkey PRIMARY KEY (equipment_id , user_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_equipment_is_item
(
  equipment_id integer NOT NULL,
  item_id integer NOT NULL,
  CONSTRAINT core_equipment_is_item_pkey PRIMARY KEY (equipment_id , item_id ),
  CONSTRAINT core_equipment_is_item_equipment_id_key UNIQUE (equipment_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_equipment_types
(
  id serial NOT NULL,
  toid integer,
  name text,
  cat_id integer,
  location_id integer, -- not used yet
  description text,
  manufacturer text,
  CONSTRAINT core_equipment_types_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";


// INDIZES

$statement[] = "CREATE INDEX core_method_types_name_ix
  ON core_equipment_types
  USING btree
  (name COLLATE pg_catalog.\"default\" );";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_equipment ADD CONSTRAINT core_equipment_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment ADD CONSTRAINT core_equipment_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_equipment_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_cats ADD CONSTRAINT core_equipment_cats_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_equipment_cats (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_has_organisation_units ADD CONSTRAINT core_equipment_has_organisation_units_equipment_id_fkey FOREIGN KEY (equipment_id)
      REFERENCES core_equipment_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_has_organisation_units ADD CONSTRAINT core_equipment_has_organisation_units_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_has_responsible_persons ADD CONSTRAINT core_equipment_has_responsible_persons_equipment_id_fkey FOREIGN KEY (equipment_id)
      REFERENCES core_equipment_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_has_responsible_persons ADD CONSTRAINT core_equipment_has_responsible_persons_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_is_item ADD CONSTRAINT core_equipment_is_item_equipment_id_fkey FOREIGN KEY (equipment_id)
      REFERENCES core_equipment (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_is_item ADD CONSTRAINT core_equipment_is_item_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_types ADD CONSTRAINT core_equipment_types_cat_id_fkey FOREIGN KEY (cat_id)
      REFERENCES core_equipment_cats (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_types ADD CONSTRAINT core_equipment_types_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES core_locations (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_equipment_types ADD CONSTRAINT core_equipment_types_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_equipment_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";
?>