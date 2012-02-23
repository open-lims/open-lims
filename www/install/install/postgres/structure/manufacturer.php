<?php

$check_statement = "SELECT id FROM core_manufacturers";

$statement = array();

$statement[] = "CREATE TABLE core_manufacturers
(
  id serial NOT NULL,
  name text,
  user_id integer,
  datetime timestamp with time zone,
  CONSTRAINT core_manufacturers_pkey PRIMARY KEY (id ),
  CONSTRAINT core_manufacturers_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE INDEX core_manufacturers_name_ix
  ON core_manufacturers
  USING btree
  (name COLLATE pg_catalog."default" );";

?>