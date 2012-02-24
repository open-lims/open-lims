<?php

$check_statement = "SELECT id FROM core_data_entities";

$statement = array();

$statement[] = "CREATE TABLE core_data_entities
(
  id serial NOT NULL,
  datetime timestamp with time zone,
  owner_id integer,
  owner_group_id integer,
  permission integer,
  automatic boolean,
  CONSTRAINT core_data_entities_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_entity_has_data_entities
(
  data_entity_pid integer NOT NULL,
  data_entity_cid integer NOT NULL,
  CONSTRAINT core_data_entity_has_data_entities_pkey PRIMARY KEY (data_entity_pid , data_entity_cid )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_entity_is_item
(
  data_entity_id integer NOT NULL,
  item_id integer NOT NULL,
  CONSTRAINT core_data_entity_is_item_pkey PRIMARY KEY (data_entity_id , item_id ),
  CONSTRAINT core_data_entity_is_item_data_entity_id_key UNIQUE (data_entity_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_user_data
(
  user_id integer NOT NULL,
  quota bigint,
  filesize bigint,
  CONSTRAINT core_data_user_data_pkey PRIMARY KEY (user_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_file_image_cache
(
  id serial NOT NULL,
  file_version_id integer,
  width integer,
  height integer,
  size bigint,
  last_access timestamp with time zone,
  CONSTRAINT core_file_image_cache_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_file_version_blobs
(
  file_version_id integer NOT NULL,
  blob bytea,
  CONSTRAINT core_file_version_blobs_pkey PRIMARY KEY (file_version_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_file_versions
(
  id serial NOT NULL,
  toid integer,
  name text,
  version integer,
  size bigint,
  checksum character(32),
  datetime timestamp with time zone,
  comment text,
  previous_version_id integer,
  internal_revision integer,
  current boolean,
  file_extension text,
  owner_id integer,
  CONSTRAINT core_file_versions_pkey PRIMARY KEY (id ),
  CONSTRAINT core_file_versions_toid_key UNIQUE (toid , internal_revision )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_files
(
  id serial NOT NULL,
  data_entity_id integer,
  flag integer,
  CONSTRAINT core_files_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_folder_concretion
(
  id serial NOT NULL,
  type text,
  handling_class text,
  include_id integer,
  CONSTRAINT core_folder_concretion_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_folder_is_group_folder
(
  group_id integer NOT NULL,
  folder_id integer NOT NULL,
  CONSTRAINT core_folder_is_group_folder_pkey PRIMARY KEY (group_id , folder_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_folder_is_organisation_unit_folder
(
  organisation_unit_id integer NOT NULL,
  folder_id integer NOT NULL,
  CONSTRAINT core_folder_is_organisation_unit_folder_pkey PRIMARY KEY (organisation_unit_id , folder_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_folder_is_system_folder
(
  folder_id integer NOT NULL,
  CONSTRAINT core_folder_is_system_folder_pkey PRIMARY KEY (folder_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_folder_is_user_folder
(
  user_id integer NOT NULL,
  folder_id integer NOT NULL,
  CONSTRAINT core_folder_is_user_folder_pkey PRIMARY KEY (user_id , folder_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_folders
(
  id serial NOT NULL,
  data_entity_id integer,
  name text,
  path text,
  deleted boolean,
  blob boolean,
  flag integer,
  CONSTRAINT folders_pkey PRIMARY KEY (id ),
  CONSTRAINT core_folders_path_key UNIQUE (path )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_value_types
(
  id serial NOT NULL,
  name text,
  template_id integer,
  CONSTRAINT core_value_types_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_value_var_cases
(
  id serial NOT NULL,
  name text,
  handling_class text,
  ignore_this boolean,
  include_id integer,
  CONSTRAINT core_value_var_cases_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_value_versions
(
  id serial NOT NULL,
  toid integer,
  version integer,
  value text,
  text_search_vector tsvector,
  checksum character(32),
  datetime timestamp with time zone,
  language_id integer,
  previous_version_id integer,
  internal_revision integer,
  current boolean,
  owner_id integer,
  name text,
  CONSTRAINT core_value_versions_pkey PRIMARY KEY (id ),
  CONSTRAINT core_value_versions_toid_key UNIQUE (toid , internal_revision )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_values
(
  id serial NOT NULL,
  data_entity_id integer,
  type_id integer,
  CONSTRAINT core_values_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_virtual_folders
(
  id serial NOT NULL,
  data_entity_id integer,
  name text,
  CONSTRAINT core_virtual_folders_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";


// INDIZES

$statement[] = "CREATE INDEX core_file_image_cache_height_ix
  ON core_file_image_cache
  USING btree
  (file_version_id , height );";

$statement[] = "CREATE INDEX core_file_image_cache_width_ix
  ON core_file_image_cache
  USING btree
  (file_version_id , width );";

$statement[] = "CREATE INDEX core_file_versions_file_extension_ix
  ON core_file_versions
  USING btree
  (file_extension COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX core_file_versions_name_ix
  ON core_file_versions
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX core_folders_name_ix
  ON core_folders
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX core_value_types_name_ix
  ON core_value_types
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX fulltext_search
  ON core_value_versions
  USING gist
  (text_search_vector );";

$statement[] = "CREATE INDEX core_virtual_folders_name_ix
  ON core_virtual_folders
  USING btree
  (name COLLATE pg_catalog. \"default\" );";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_data_entities ADD CONSTRAINT core_data_entities_owner_group_id_fkey FOREIGN KEY (owner_group_id)
      REFERENCES core_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_entities ADD CONSTRAINT core_data_entities_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_entity_has_data_entities ADD CONSTRAINT core_data_entity_has_data_entities_data_entity_cid_fkey FOREIGN KEY (data_entity_cid)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_entity_has_data_entities ADD CONSTRAINT core_data_entity_has_data_entities_data_entity_pid_fkey FOREIGN KEY (data_entity_pid)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_entity_is_item ADD CONSTRAINT core_data_entity_is_item_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_entity_is_item ADD CONSTRAINT core_data_entity_is_item_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_user_data ADD CONSTRAINT core_data_user_data_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_file_image_cache ADD CONSTRAINT core_file_image_cache_file_version_id_fkey FOREIGN KEY (file_version_id)
      REFERENCES core_file_versions (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_file_version_blobs ADD CONSTRAINT core_file_version_blobs_file_version_id_fkey FOREIGN KEY (file_version_id)
      REFERENCES core_file_versions (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_file_versions ADD CONSTRAINT core_file_versions_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_file_versions ADD CONSTRAINT core_file_versions_previous_version_id_fkey FOREIGN KEY (previous_version_id)
      REFERENCES core_file_versions (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_file_versions ADD  CONSTRAINT core_file_versions_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_files (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_files ADD CONSTRAINT core_files_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folder_concretion ADD CONSTRAINT core_folder_concretion_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folder_is_group_folder ADD CONSTRAINT core_folder_is_group_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folder_is_group_folder ADD CONSTRAINT core_folder_is_group_folder_group_id_fkey FOREIGN KEY (group_id)
      REFERENCES core_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folder_is_organisation_unit_folder ADD CONSTRAINT core_folder_is_organisation_unit_fol_organisation_unit_id_fkey1 FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folder_is_organisation_unit_folder ADD CONSTRAINT core_folder_is_organisation_unit_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folder_is_system_folder ADD CONSTRAINT core_folder_is_system_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folder_is_user_folder ADD CONSTRAINT core_folder_is_home_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folder_is_user_folder ADD CONSTRAINT core_folder_is_home_folder_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_folders ADD CONSTRAINT core_folders_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_value_types ADD CONSTRAINT core_value_types_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_olvdl_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_value_var_cases ADD CONSTRAINT core_value_var_cases_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_value_versions ADD CONSTRAINT core_value_versions_language_id_fkey FOREIGN KEY (language_id)
      REFERENCES core_languages (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_value_versions ADD CONSTRAINT core_value_versions_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_value_versions ADD  CONSTRAINT core_value_versions_previous_version_id_fkey FOREIGN KEY (previous_version_id)
      REFERENCES core_value_versions (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_value_versions ADD CONSTRAINT core_value_versions_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_values (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_values ADD CONSTRAINT core_values_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_values ADD CONSTRAINT core_values_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_value_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_virtual_folders ADD CONSTRAINT core_virtual_folders_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";


// FOREIGN KEYS (EXTENDS TEMPLATE)

$statement[] = "ALTER TABLE ONLY core_oldl_templates ADD CONSTRAINT core_oldl_templates_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_olvdl_templates ADD CONSTRAINT core_olvdl_templates_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";
?>