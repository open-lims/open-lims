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
  link boolean,
  link_item_id integer,
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

$statement[] = "CREATE TABLE core_data_parameter_field_has_methods
(
  parameter_field_id integer NOT NULL,
  parameter_method_id integer NOT NULL,
  CONSTRAINT core_data_parameter_field_has_methods_pkey PRIMARY KEY (parameter_field_id , parameter_method_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_field_limits
(
  parameter_limit_id integer NOT NULL,
  parameter_field_id integer NOT NULL,
  upper_specification_limit double precision,
  lower_specification_limit double precision,
  CONSTRAINT core_data_parameter_field_limits_pkey PRIMARY KEY (parameter_limit_id , parameter_field_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_field_values
(
  id serial NOT NULL,
  parameter_version_id integer,
  parameter_field_id integer,
  parameter_method_id integer,
  value double precision,
  source text,
  locked boolean,
  CONSTRAINT core_data_parameter_field_values_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_fields
(
  id serial NOT NULL,
  name text,
  min_value double precision,
  max_value double precision,
  measuring_unit_id integer,
  measuring_unit_exponent integer,
  measuring_unit_ratio_id integer,
  CONSTRAINT core_data_parameter_fields_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_has_non_template
(
  parameter_id integer NOT NULL,
  non_template_id integer NOT NULL,
  CONSTRAINT core_data_parameter_has_non_template_pkey PRIMARY KEY (parameter_id , non_template_id ),
  CONSTRAINT core_data_parameter_has_non_te_parameter_id_non_template_id_key UNIQUE (parameter_id , non_template_id ),
  CONSTRAINT core_data_parameter_has_non_template_parameter_id_key UNIQUE (parameter_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_has_template
(
  parameter_id integer NOT NULL,
  template_id integer NOT NULL,
  CONSTRAINT core_parameter_has_template_pkey PRIMARY KEY (parameter_id , template_id ),
  CONSTRAINT core_data_parameter_has_template_parameter_id_key UNIQUE (parameter_id ),
  CONSTRAINT core_data_parameter_has_template_parameter_id_template_id_key UNIQUE (parameter_id , template_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_limits
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_data_parameter_limits_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_methods
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_data_parameter_methods_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_non_template_has_fields
(
  non_template_id integer NOT NULL,
  parameter_field_id integer NOT NULL,
  CONSTRAINT core_data_parameter_non_template_has_fields_pkey PRIMARY KEY (non_template_id , parameter_field_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_non_templates
(
  id serial NOT NULL,
  datetime timestamp with time zone,
  CONSTRAINT core_data_parameter_non_templates_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_template_has_fields
(
  template_id integer NOT NULL,
  parameter_field_id integer NOT NULL,
  \"position\" integer,
  CONSTRAINT core_data_parameter_template_has_fields_pkey PRIMARY KEY (template_id , parameter_field_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_templates
(
  id serial NOT NULL,
  internal_name text,
  name text,
  created_by integer,
  datetime timestamp with time zone,
  CONSTRAINT core_data_parameter_templates_pkey PRIMARY KEY (id ),
  CONSTRAINT core_data_parameter_templates_internal_name_key UNIQUE (internal_name )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameter_versions
(
  id serial NOT NULL,
  parameter_id integer,
  version integer,
  internal_revision integer,
  previous_version_id integer,
  current boolean,
  owner_id integer,
  datetime timestamp with time zone,
  name text,
  parameter_limit_id integer,
  CONSTRAINT core_data_parameter_versions_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_data_parameters
(
  id serial NOT NULL,
  data_entity_id integer,
  CONSTRAINT core_data_parameters_pkey PRIMARY KEY (id ) 
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

$statement[] = "ALTER TABLE ONLY core_data_entity_has_data_entities ADD CONSTRAINT core_data_entity_has_data_entities_link_item_id_fkey FOREIGN KEY (link_item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_entity_is_item ADD CONSTRAINT core_data_entity_is_item_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_entity_is_item ADD CONSTRAINT core_data_entity_is_item_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_field_has_methods ADD CONSTRAINT core_data_parameter_field_has_methods_parameter_field_id_fkey FOREIGN KEY (parameter_field_id)
      REFERENCES core_data_parameter_fields (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_field_has_methods ADD CONSTRAINT core_data_parameter_field_has_methods_parameter_method_id_fkey FOREIGN KEY (parameter_method_id)
      REFERENCES core_data_parameter_methods (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_field_limits ADD CONSTRAINT core_data_parameter_field_limits_parameter_field_id_fkey FOREIGN KEY (parameter_field_id)
      REFERENCES core_data_parameter_fields (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_field_limits ADD CONSTRAINT core_data_parameter_field_limits_parameter_limit_id_fkey FOREIGN KEY (parameter_limit_id)
      REFERENCES core_data_parameter_limits (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_field_values ADD CONSTRAINT core_data_parameter_field_values_parameter_field_id_fkey FOREIGN KEY (parameter_field_id)
      REFERENCES core_data_parameter_fields (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_field_values ADD CONSTRAINT core_data_parameter_field_values_parameter_method_id_fkey FOREIGN KEY (parameter_method_id)
      REFERENCES core_data_parameter_methods (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_field_values ADD CONSTRAINT core_data_parameter_field_values_parameter_version_id_fkey FOREIGN KEY (parameter_version_id)
      REFERENCES core_data_parameter_versions (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_fields ADD CONSTRAINT core_data_parameter_fields_measuring_unit_id_fkey FOREIGN KEY (measuring_unit_id)
      REFERENCES core_base_measuring_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_fields ADD CONSTRAINT core_data_parameter_fields_measuring_unit_ratio_id_fkey FOREIGN KEY (measuring_unit_ratio_id)
      REFERENCES core_base_measuring_unit_ratios (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_has_non_template ADD CONSTRAINT core_data_parameter_has_non_template_non_template_id_fkey FOREIGN KEY (non_template_id)
      REFERENCES core_data_parameter_non_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_has_non_template ADD CONSTRAINT core_data_parameter_has_non_template_parameter_id_fkey FOREIGN KEY (parameter_id)
      REFERENCES core_data_parameters (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_has_template ADD CONSTRAINT core_data_parameter_has_template_parameter_id_fkey FOREIGN KEY (parameter_id)
      REFERENCES core_data_parameters (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_has_template ADD CONSTRAINT core_data_parameter_has_template_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_data_parameter_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_non_template_has_fields ADD CONSTRAINT core_data_parameter_non_template_has_fi_parameter_field_id_fkey FOREIGN KEY (parameter_field_id)
      REFERENCES core_data_parameter_fields (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_non_template_has_fields ADD CONSTRAINT core_data_parameter_non_template_has_field_non_template_id_fkey FOREIGN KEY (non_template_id)
      REFERENCES core_data_parameter_non_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_template_has_fields ADD CONSTRAINT core_data_parameter_template_has_fields_parameter_field_id_fkey FOREIGN KEY (parameter_field_id)
      REFERENCES core_data_parameter_fields (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_template_has_fields ADD CONSTRAINT core_data_parameter_template_has_fields_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_data_parameter_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_templates ADD CONSTRAINT core_data_parameter_templates_created_by_fkey FOREIGN KEY (created_by)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_versions ADD CONSTRAINT core_data_parameter_versions_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_versions ADD CONSTRAINT core_data_parameter_versions_parameter_id_fkey FOREIGN KEY (parameter_id)
      REFERENCES core_data_parameters (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_versions ADD CONSTRAINT core_data_parameter_versions_previous_version_id_fkey FOREIGN KEY (previous_version_id)
      REFERENCES core_data_parameter_versions (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameter_versions ADD CONSTRAINT core_data_parameter_versions_parameter_limit_id_fkey FOREIGN KEY (parameter_limit_id)
      REFERENCES core_data_parameter_limits (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_data_parameters ADD CONSTRAINT core_data_parameters_data_entitiy_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
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


// FUNCTIONS

$statement[] = "CREATE OR REPLACE FUNCTION get_all_file_versions(integer, integer)
  RETURNS SETOF integer AS
\$BODY\$DECLARE
file_record RECORD;
rec_return RECORD;
BEGIN
	
	IF \$2 IS NULL THEN

		FOR file_record IN SELECT id FROM core_file_versions WHERE previous_version_id=id AND toid=\$1 ORDER BY version
		LOOP

			IF file_record.id IS NOT NULL THEN

				RETURN NEXT file_record.id;

				FOR rec_return IN select * from get_all_file_versions(\$1, file_record.id) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	ELSE

		FOR file_record IN SELECT id FROM core_file_versions WHERE previous_version_id=\$2 AND toid=\$1 AND previous_version_id != id ORDER BY version
		LOOP

			IF file_record.id IS NOT NULL THEN
				
				RETURN NEXT file_record.id;

				FOR rec_return IN select * from get_all_file_versions(\$1, file_record.id) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	END IF;

	RETURN;	

END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;";

$statement[] = "CREATE OR REPLACE FUNCTION get_all_value_versions(integer, integer)
  RETURNS SETOF integer AS
\$BODY\$DECLARE
value_record RECORD;
rec_return RECORD;
BEGIN
	
	IF \$2 IS NULL THEN

		FOR value_record IN SELECT id FROM core_value_versions WHERE previous_version_id=id AND toid=\$1 ORDER BY version
		LOOP

			IF value_record.id IS NOT NULL THEN

				RETURN NEXT value_record.id;

				FOR rec_return IN select * from get_all_value_versions(\$1, value_record.id) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	ELSE

		FOR value_record IN SELECT id FROM core_value_versions WHERE previous_version_id=\$2 AND toid=\$1 AND previous_version_id != id ORDER BY version
		LOOP

			IF value_record.id IS NOT NULL THEN
				
				RETURN NEXT value_record.id;

				FOR rec_return IN select * from get_all_value_versions(\$1, value_record.id) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	END IF;

	RETURN;	

END;

\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;";

$statement[] = "CREATE OR REPLACE FUNCTION search_get_sub_folders(integer)
  RETURNS SETOF integer AS
\$BODY\$DECLARE
folder_record RECORD;
rec_return RECORD;
BEGIN
	

	IF \$1 IS NOT NULL THEN

		FOR folder_record IN SELECT data_entity_cid FROM core_data_entity_has_data_entities WHERE data_entity_pid = \$1 AND (data_entity_cid IN (SELECT data_entity_id FROM core_folders))
		LOOP

			IF folder_record.data_entity_cid IS NOT NULL THEN

				RETURN NEXT folder_record.data_entity_cid;

				FOR rec_return IN select * from search_get_sub_folders(folder_record.data_entity_cid) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	ELSE

		RETURN;

	END IF;

	RETURN;	

END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;";

?>