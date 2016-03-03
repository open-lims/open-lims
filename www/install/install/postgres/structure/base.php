<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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

$statement[] = "CREATE TABLE core_base_batch_runs
(
  id serial NOT NULL,
  binary_id integer,
  status integer,
  create_datetime timestamp with time zone,
  start_datetime timestamp with time zone,
  end_datetime timestamp with time zone,
  last_lifesign timestamp with time zone,
  user_id integer,
  type_id integer,
  CONSTRAINT core_jobs_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 1

$statement[] = "CREATE TABLE core_base_batch_types
(
  id serial NOT NULL,
  name text,
  internal_name text,
  binary_id integer,
  CONSTRAINT core_job_types_pkey PRIMARY KEY (id ),
  CONSTRAINT core_job_types_internal_name_key UNIQUE (internal_name )
)
WITH (
  OIDS=FALSE
);"; // 2

$statement[] = "CREATE TABLE core_base_event_listeners
(
  id serial NOT NULL,
  include_id integer,
  class_name text,
  CONSTRAINT core_base_event_listeners_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 3

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
);"; // 4

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
);"; // 5

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
);"; // 6

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
);"; // 7

$statement[] = "CREATE TABLE core_base_measuring_unit_categories
(
  id serial NOT NULL,
  name text,
  created_by_user boolean,
  CONSTRAINT core_measuring_unit_categories_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 8

$statement[] = "CREATE TABLE core_base_measuring_unit_ratios
(
  id serial NOT NULL,
  numerator_unit_id integer,
  numerator_unit_exponent integer,
  denominator_unit_id integer,
  denominator_unit_exponent integer,
  CONSTRAINT core_base_measuring_unit_ratios_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 9

$statement[] = "CREATE TABLE core_base_measuring_units
(
  id serial NOT NULL,
  base_id integer,
  category_id integer,
  name text,
  unit_symbol text,
  min_value double precision,
  max_value double precision,
  min_prefix_exponent integer,
  max_prefix_exponent integer,
  prefix_calculation_exponent integer,
  calculation text,
  type text,
  created_by_user boolean,
  CONSTRAINT core_measuring_units_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 10

$statement[] = "CREATE TABLE core_base_module_dialogs
(
  id serial NOT NULL,
  module_id integer,
  dialog_type text,
  class_path text,
  class text,
  method text,
  internal_name text,
  language_address text,
  weight integer,
  disabled boolean,
  CONSTRAINT core_base_module_dialogs_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 11

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
);"; // 12

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
);"; // 13

$statement[] = "CREATE TABLE core_base_module_navigation
(
  id serial NOT NULL,
  language_address text,
  \"position\" integer,
  colour text,
  module_id integer,
  hidden boolean,
  alias text,
  controller_class text,
  controller_file text,
  CONSTRAINT core_base_module_navigation_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_module_navigation_position_key UNIQUE (\"position\" )
)
WITH (
  OIDS=FALSE
);"; // 14

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
);"; // 15

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
);"; // 16

$statement[] = "CREATE TABLE core_binaries
(
  id serial NOT NULL,
  path text,
  file text,
  CONSTRAINT core_binaries_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 17

$statement[] = "CREATE TABLE core_countries
(
  id serial NOT NULL,
  english_name text,
  local_name text,
  iso_3166 text,
  CONSTRAINT core_countries_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 18

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
);"; // 19

$statement[] = "CREATE TABLE core_extensions
(
  id serial NOT NULL,
  name text,
  identifier text,
  folder text,
  class text,
  main_file text,
  version text,
  CONSTRAINT core_extensions_pkey PRIMARY KEY (id ),
  CONSTRAINT core_extensions_identifer_key UNIQUE (identifier )
)
WITH (
  OIDS=FALSE
);"; // 20

$statement[] = "CREATE TABLE core_group_has_users
(
  primary_key serial NOT NULL,
  group_id integer,
  user_id integer,
  CONSTRAINT core_user_has_groups_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);"; // 21

$statement[] = "CREATE TABLE core_groups
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_groups_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 22

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
);"; // 23

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
);"; // 24

$statement[] = "CREATE TABLE core_service_has_log_entries
(
  service_id integer NOT NULL,
  log_entry_id integer NOT NULL,
  CONSTRAINT core_service_has_log_entries_pkey PRIMARY KEY (service_id , log_entry_id )
)
WITH (
  OIDS=FALSE
);"; // 25

$statement[] = "CREATE TABLE core_services
(
  id serial NOT NULL,
  name text,
  binary_id integer,
  status integer,
  last_lifesign timestamp with time zone,
  CONSTRAINT core_services_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 26

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
);"; // 27

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
);"; // 28

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
);"; // 29

$statement[] = "CREATE TABLE core_system_log_types
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_system_log_types_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 30

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
);"; // 31

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
);"; // 32

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
);"; // 33

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
  lync text,
  jabber text,
  CONSTRAINT core_user_profiles_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 34

$statement[] = "CREATE TABLE core_user_regional_settings
(
  id integer NOT NULL,
  language_id integer,
  timezone_id integer,
  time_display_format boolean,
  time_enter_format boolean,
  date_display_format text,
  date_enter_format text,
  country_id integer,
  system_of_units text,
  system_of_paper_format text,
  currency_id integer,
  currency_significant_digits integer,
  decimal_separator text,
  thousand_separator text,
  name_display_format text,
  CONSTRAINT core_user_regional_settings_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);"; // 35

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
);"; // 36


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

$statement[] = "ALTER TABLE ONLY core_base_batch_runs ADD CONSTRAINT core_jobs_binary_id_fkey FOREIGN KEY (binary_id)
      REFERENCES core_binaries (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_batch_runs ADD CONSTRAINT core_jobs_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_base_batch_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_batch_runs ADD  CONSTRAINT core_jobs_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_batch_types ADD CONSTRAINT core_job_types_binary_id_fkey FOREIGN KEY (binary_id)
      REFERENCES core_binaries (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_event_listeners ADD CONSTRAINT core_base_event_listeners_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_include_files ADD CONSTRAINT core_base_include_files_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_measuring_unit_ratios ADD CONSTRAINT core_base_measuring_unit_ratios_denominator_unit_id_fkey FOREIGN KEY (denominator_unit_id)
      REFERENCES core_base_measuring_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_measuring_unit_ratios ADD CONSTRAINT core_base_measuring_unit_ratios_numerator_unit_id_fkey FOREIGN KEY (numerator_unit_id)
      REFERENCES core_base_measuring_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_measuring_units ADD CONSTRAINT core_base_measuring_units_base_id_fkey FOREIGN KEY (base_id)
      REFERENCES core_base_measuring_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_base_measuring_units ADD CONSTRAINT core_base_measuring_units_category_id_fkey FOREIGN KEY (category_id)
      REFERENCES core_base_measuring_unit_categories (id) MATCH SIMPLE
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

$statement[] = "ALTER TABLE ONLY core_service_has_log_entries ADD CONSTRAINT core_service_has_log_entries_log_entry_id_fkey FOREIGN KEY (log_entry_id)
      REFERENCES core_system_log (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_service_has_log_entries ADD CONSTRAINT core_service_has_log_entries_service_id_fkey FOREIGN KEY (service_id)
      REFERENCES core_services (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_services ADD CONSTRAINT core_services_binary_id_fkey FOREIGN KEY (binary_id)
      REFERENCES core_binaries (id) MATCH SIMPLE
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

$statement[] = "ALTER TABLE ONLY core_system_messages ADD CONSTRAINT core_system_messages_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_admin_settings ADD CONSTRAINT core_user_admin_settings_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_profiles ADD  CONSTRAINT core_user_profiles_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_regional_settings ADD CONSTRAINT core_user_regional_settings_currency_id_fkey FOREIGN KEY (currency_id)
      REFERENCES core_currencies (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_regional_settings ADD CONSTRAINT core_user_regional_settings_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_regional_settings ADD CONSTRAINT core_user_regional_settings_language_id_fkey FOREIGN KEY (language_id)
      REFERENCES core_languages (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_regional_settings ADD CONSTRAINT core_user_regional_settings_timezone_id_fkey FOREIGN KEY (timezone_id)
      REFERENCES core_timezones (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_regional_settings ADD CONSTRAINT core_user_regional_settings_country_id_fkey FOREIGN KEY (country_id)
      REFERENCES core_countries (id) MATCH SIMPLE
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
  
$statement[] = "CREATE OR REPLACE FUNCTION nameconcat(text, text)
  RETURNS text AS
\$BODY\$SELECT
CASE WHEN \$1 IS NULL THEN \$2
WHEN \$2 IS NULL THEN \$1
ELSE \$1 || ' ' || \$2 END\$BODY\$
  LANGUAGE sql VOLATILE
  COST 100;";
?>