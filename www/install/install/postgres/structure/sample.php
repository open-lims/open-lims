<?php

$check_statement = "SELECT id FROM core_samples";

$statement = array();

$statement[] = "CREATE TABLE core_sample_has_folder
(
  sample_id integer NOT NULL,
  folder_id integer NOT NULL,
  CONSTRAINT core_sample_has_folder_pkey PRIMARY KEY (sample_id , folder_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_sample_has_items
(
  primary_key serial NOT NULL,
  sample_id integer,
  item_id integer,
  gid integer,
  parent boolean,
  CONSTRAINT core_sample_has_items_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_sample_has_locations
(
  primary_key serial NOT NULL,
  sample_id integer,
  location_id integer,
  datetime timestamp with time zone,
  user_id integer,
  CONSTRAINT core_sample_has_locations_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_sample_has_organisation_units
(
  primary_key serial NOT NULL,
  sample_id integer,
  organisation_unit_id integer,
  CONSTRAINT core_sample_has_organisation_units_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_sample_has_users
(
  primary_key serial NOT NULL,
  sample_id integer,
  user_id integer,
  read boolean,
  write boolean,
  CONSTRAINT core_sample_has_users_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_sample_is_item
(
  sample_id integer NOT NULL,
  item_id integer NOT NULL,
  CONSTRAINT core_sample_is_item_pkey PRIMARY KEY (sample_id , item_id ),
  CONSTRAINT core_sample_is_item_sample_id_key UNIQUE (sample_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_sample_template_cats
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_sample_template_cats_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_sample_templates
(
  id integer NOT NULL,
  name text,
  cat_id integer,
  template_id integer,
  CONSTRAINT core_sample_templates_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_samples
(
  id serial NOT NULL,
  name text,
  datetime timestamp with time zone,
  owner_id integer,
  template_id integer,
  available boolean,
  deleted boolean,
  comment text,
  comment_text_search_vector tsvector,
  language_id integer,
  date_of_expiry date,
  expiry_warning bigint,
  manufacturer_id integer,
  CONSTRAINT core_samples_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_virtual_folder_is_sample
(
  id integer NOT NULL,
  CONSTRAINT core_virtual_folder_is_sample_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";


// INDIZES

$statement[] = "CREATE INDEX core_sample_templates_name_ix
  ON core_sample_templates
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX comment_fulltext_search
  ON core_samples
  USING gist
  (comment_text_search_vector );";

$statement[] = "CREATE INDEX core_samples_name_ix
  ON core_samples
  USING btree
  (name COLLATE pg_catalog.\"default\" );";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_sample_has_folder ADD CONSTRAINT core_sample_has_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_folder ADD CONSTRAINT core_sample_has_folder_sample_id_fkey FOREIGN KEY (sample_id)
      REFERENCES core_samples (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_items ADD CONSTRAINT core_sample_has_items_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_items ADD CONSTRAINT core_sample_has_items_sample_id_fkey FOREIGN KEY (sample_id)
      REFERENCES core_samples (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_locations ADD CONSTRAINT core_sample_has_locations_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES core_locations (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_locations ADD CONSTRAINT core_sample_has_locations_sample_id_fkey FOREIGN KEY (sample_id)
      REFERENCES core_samples (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_locations ADD CONSTRAINT core_sample_has_locations_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_organisation_units ADD CONSTRAINT core_sample_has_organisation_units_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_organisation_units ADD CONSTRAINT core_sample_has_organisation_units_sample_id_fkey FOREIGN KEY (sample_id)
      REFERENCES core_samples (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_users ADD CONSTRAINT core_sample_has_users_sample_id_fkey FOREIGN KEY (sample_id)
      REFERENCES core_samples (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_has_users ADD CONSTRAINT core_sample_has_users_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_is_item ADD CONSTRAINT core_sample_is_item_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_is_item ADD CONSTRAINT core_sample_is_item_sample_id_fkey FOREIGN KEY (sample_id)
      REFERENCES core_samples (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE"; 

$statement[] = "ALTER TABLE ONLY core_sample_templates ADD CONSTRAINT core_sample_templates_cat_id_fkey FOREIGN KEY (cat_id)
      REFERENCES core_sample_template_cats (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sample_templates ADD CONSTRAINT core_sample_templates_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_oldl_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_samples ADD CONSTRAINT core_samples_language_id_fkey FOREIGN KEY (language_id)
      REFERENCES core_languages (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_samples ADD CONSTRAINT core_samples_manufacturer_id_fkey FOREIGN KEY (manufacturer_id)
      REFERENCES core_manufacturers (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_samples ADD CONSTRAINT core_samples_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_samples ADD CONSTRAINT core_samples_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_sample_templates (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_virtual_folder_is_sample ADD CONSTRAINT core_virtual_folder_is_sample_id_fkey FOREIGN KEY (id)
      REFERENCES core_virtual_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";
?>