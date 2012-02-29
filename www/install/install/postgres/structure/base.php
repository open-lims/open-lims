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
$check_statement = "SELECT id FROM core_base_includes";

$statement = array();


// TABLES

$statement[] = "CREATE TABLE core_base_event_listeners
(
  id serial NOT NULL,
  include_id integer,
  class_name text,
  CONSTRAINT core_base_event_listeners_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_include_files
(
  id serial NOT NULL,
  include_id integer,
  name text,
  checksum character(32),
  CONSTRAINT core_base_include_files_pkey PRIMARY KEY (id )
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

$statement[] = "CREATE TABLE core_base_includes
(
  id serial NOT NULL,
  name text,
  folder text,
  db_version text,
  CONSTRAINT core_base_includes_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_module_dialogs
(
  id serial NOT NULL,
  module_id integer,
  dialog_type text,
  class_path text,
  class text,
  method text,
  internal_name text,
  display_name text,
  weight integer,
  disabled boolean,
  CONSTRAINT core_base_module_dialogs_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_module_files
(
  id serial NOT NULL,
  module_id integer,
  name text,
  checksum character(32),
  CONSTRAINT core_base_module_files_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_module_links
(
  id serial NOT NULL,
  module_id integer,
  link_type text,
  link_array text,
  link_file text,
  weight integer,
  disabled boolean,
  CONSTRAINT core_base_module_links_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_module_navigation
(
  id serial NOT NULL,
  display_name text,
  \"position\" integer,
  colour text,
  module_id integer,
  hidden boolean,
  CONSTRAINT core_base_module_navigation_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_module_navigation_position_key UNIQUE (\"position\" )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_modules
(
  id serial NOT NULL,
  name text,
  folder text,
  class text,
  disabled boolean,
  CONSTRAINT core_base_modules_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_base_registry
(
  id serial NOT NULL,
  name text,
  include_id integer,
  value text,
  CONSTRAINT core_base_registry_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_registry_name_key UNIQUE (name )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_currencies
(
  id serial NOT NULL,
  name text,
  symbol text,
  iso_4217 text,
  CONSTRAINT core_currencies_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_group_has_users
(
  primary_key serial NOT NULL,
  group_id integer,
  user_id integer,
  CONSTRAINT core_user_has_groups_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_groups
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_groups_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_languages
(
  id serial NOT NULL,
  english_name text,
  language_name text,
  tsvector_name text,
  iso_639 character(2),
  iso_3166 character(2),
  CONSTRAINT core_languages_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_measuring_units
(
  id serial NOT NULL,
  toid integer,
  name text,
  type integer,
  base boolean,
  unit_symbol text,
  calculation text,
  CONSTRAINT core_measuring_units_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_paper_sizes
(
  id serial NOT NULL,
  name text,
  width double precision,
  height double precision,
  margin_left double precision,
  margin_right double precision,
  margin_top double precision,
  margin_bottom double precision,
  base boolean,
  standard boolean,
  CONSTRAINT core_paper_sizes_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_session_values
(
  id serial NOT NULL,
  session_id character(32),
  address text,
  value text,
  CONSTRAINT core_session_values_pkey PRIMARY KEY (id ),
  CONSTRAINT core_session_values_session_id_key UNIQUE (session_id , address )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_sessions
(
  session_id character(32) NOT NULL,
  ip inet,
  user_id integer,
  datetime timestamp with time zone,
  CONSTRAINT core_sessions_pkey PRIMARY KEY (session_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_system_log
(
  id serial NOT NULL,
  type_id integer,
  user_id integer,
  datetime timestamp with time zone,
  ip inet,
  content_int integer,
  content_string text,
  content_errorno text,
  file text,
  line integer,
  link text,
  stack_trace text,
  CONSTRAINT core_system_log_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_system_log_types
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_system_log_types_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_system_messages
(
  id serial NOT NULL,
  user_id integer,
  datetime timestamp with time zone,
  content text,
  CONSTRAINT core_system_messages_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_timezones
(
  id serial NOT NULL,
  title text,
  php_title text,
  deviation double precision,
  CONSTRAINT core_timezones_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_user_admin_settings
(
  id integer NOT NULL,
  can_change_password boolean,
  must_change_password boolean,
  user_locked boolean,
  user_inactive boolean,
  secure_password boolean,
  last_password_change timestamp with time zone,
  block_write boolean,
  create_folder boolean,
  CONSTRAINT core_user_admin_settings_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_user_profile_settings
(
  id integer NOT NULL,
  language_id integer,
  timezone_id integer,
  CONSTRAINT core_user_profile_settings_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_user_profiles
(
  id integer NOT NULL,
  gender character(1),
  title text,
  forename text,
  surname text,
  mail text,
  institution text,
  department text,
  street text,
  zip text,
  city text,
  country text,
  phone text,
  icq integer,
  msn text,
  yahoo text,
  aim text,
  skype text,
  CONSTRAINT core_user_profiles_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_users
(
  id serial NOT NULL,
  username text,
  password character(32),
  CONSTRAINT core_users_pkey PRIMARY KEY (id ),
  CONSTRAINT core_users_username_key UNIQUE (username )
)
WITH (
  OIDS=FALSE
);";


// INDIZES

$statement[] = "CREATE INDEX core_base_include_files_name_ix
  ON core_base_include_files
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX core_base_module_files_name_ix
  ON core_base_module_files
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX core_groups_name_ix
  ON core_groups
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX core_sessions_address_ix
  ON core_session_values
  USING btree
  (address COLLATE pg_catalog.\"default\" );";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_base_event_listeners ADD CONSTRAINT core_base_event_listeners_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_include_files ADD CONSTRAINT core_base_include_files_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_module_dialogs ADD CONSTRAINT core_base_module_dialogs_module_id_fkey FOREIGN KEY (module_id)
      REFERENCES core_base_modules (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_module_files ADD CONSTRAINT core_base_module_files_module_id_fkey FOREIGN KEY (module_id)
      REFERENCES core_base_modules (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_module_links ADD CONSTRAINT core_base_module_links_module_id_fkey FOREIGN KEY (module_id)
      REFERENCES core_base_modules (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_module_navigation ADD CONSTRAINT core_base_module_navigation_module_id_fkey FOREIGN KEY (module_id)
      REFERENCES core_base_modules (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_registry ADD CONSTRAINT core_base_registry_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_group_has_users ADD CONSTRAINT core_group_has_users_group_id_fkey FOREIGN KEY (group_id)
      REFERENCES core_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_group_has_users ADD CONSTRAINT core_group_has_users_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_measuring_units ADD CONSTRAINT core_measuring_units_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_measuring_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_session_values ADD CONSTRAINT core_session_values_session_id_fkey FOREIGN KEY (session_id)
      REFERENCES core_sessions (session_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_sessions ADD CONSTRAINT core_sessions_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_system_log ADD CONSTRAINT core_system_log_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_system_log_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_system_log ADD CONSTRAINT core_system_log_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_system_messages ADD  CONSTRAINT core_system_messages_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_admin_settings ADD CONSTRAINT core_user_admin_settings_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_profile_settings ADD CONSTRAINT core_user_profile_settings_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_profile_settings ADD CONSTRAINT core_user_profile_settings_language_id_fkey FOREIGN KEY (language_id)
      REFERENCES core_languages (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_profile_settings ADD CONSTRAINT core_user_profile_settings_timezone_id_fkey FOREIGN KEY (timezone_id)
      REFERENCES core_timezones (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_profiles ADD CONSTRAINT core_user_profiles_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";


// FUNCTIONS

$statement[] = "CREATE OR REPLACE FUNCTION concat(text, text)
  RETURNS text AS
\$BODY\$SELECT
CASE WHEN \$1 IS NULL THEN \$2
WHEN \$2 IS NULL THEN \$1
ELSE \$1 || \$2
END\$BODY\$
  LANGUAGE sql VOLATILE
  COST 100;";

$statement[] = "CREATE OR REPLACE FUNCTION higher(double precision, double precision)
  RETURNS double precision AS
\$BODY\$SELECT
CASE WHEN \$1 < \$2 THEN \$2
WHEN \$2 < \$1 THEN \$1
ELSE \$1
END\$BODY\$
  LANGUAGE sql VOLATILE
  COST 100;";
  
$statement[] = "CREATE OR REPLACE FUNCTION nameconcat(text, text)
  RETURNS text AS
\$BODY\$SELECT
CASE WHEN \$1 IS NULL THEN \$2
WHEN \$2 IS NULL THEN \$1
ELSE \$1 || ' ' || \$2 END\$BODY\$
  LANGUAGE sql VOLATILE
  COST 100;";
?>