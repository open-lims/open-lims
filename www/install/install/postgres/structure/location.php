<?php

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