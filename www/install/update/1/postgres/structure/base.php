<?php

$statement = array();

$statement[] = "CREATE TABLE core_base_registry
(
  id serial NOT NULL,
  name text,
  include_id integer,
  value text,
  CONSTRAINT core_base_registry_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_registry_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_base_registry_name_key UNIQUE (name )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_include_functions
(
  id serial NOT NULL,
  include text,
  function_name text,
  db_version text,
  CONSTRAINT core_base_include_functions_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_include_tables
(
  id serial NOT NULL,
  include text,
  table_name text,
  db_version text,
  CONSTRAINT core_base_include_tables_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "ALTER TABLE core_base_includes ADD COLUMN db_version text;";


?>