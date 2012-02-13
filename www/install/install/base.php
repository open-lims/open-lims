<?php

$statement = array();

// STRUCTURE

$statement[] = "CREATE TABLE core_base_event_listeners
(
  id serial NOT NULL,
  include_id integer,
  class_name text,
  CONSTRAINT core_base_event_listeners_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_event_listeners_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_base_include_files_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_include_files_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";


$statement[] = "CREATE INDEX core_base_include_files_name_ix
  ON core_base_include_files
  USING btree
  (name COLLATE pg_catalog.\"default\" );";


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
  CONSTRAINT core_base_module_dialogs_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_module_dialogs_module_id_fkey FOREIGN KEY (module_id)
      REFERENCES core_base_modules (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_base_module_files_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_module_files_module_id_fkey FOREIGN KEY (module_id)
      REFERENCES core_base_modules (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";


$statement[] = "CREATE INDEX core_base_module_files_name_ix
  ON core_base_module_files
  USING btree
  (name COLLATE pg_catalog.\"default\" );";


$statement[] = "CREATE TABLE core_base_module_links
(
  id serial NOT NULL,
  module_id integer,
  link_type text,
  link_array text,
  link_file text,
  weight integer,
  disabled boolean,
  CONSTRAINT core_base_module_links_pkey PRIMARY KEY (id ),
  CONSTRAINT core_base_module_links_module_id_fkey FOREIGN KEY (module_id)
      REFERENCES core_base_modules (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_base_module_navigation_module_id_fkey FOREIGN KEY (module_id)
      REFERENCES core_base_modules (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
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
  CONSTRAINT core_base_registry_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
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
  CONSTRAINT core_user_has_groups_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_group_has_users_group_id_fkey FOREIGN KEY (group_id)
      REFERENCES core_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_group_has_users_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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


$statement[] = "CREATE INDEX core_groups_name_ix
  ON core_groups
  USING btree
  (name COLLATE pg_catalog.\"default\" );";


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
  CONSTRAINT core_measuring_units_pkey PRIMARY KEY (id ),
  CONSTRAINT core_measuring_units_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_measuring_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_session_values_session_id_fkey FOREIGN KEY (session_id)
      REFERENCES core_sessions (session_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_session_values_session_id_key UNIQUE (session_id , address )
)
WITH (
  OIDS=FALSE
);";


$statement[] = "CREATE INDEX core_sessions_address_ix
  ON core_session_values
  USING btree
  (address COLLATE pg_catalog.\"default\" );";


$statement[] = "CREATE TABLE core_sessions
(
  session_id character(32) NOT NULL,
  ip inet,
  user_id integer,
  datetime timestamp with time zone,
  CONSTRAINT core_sessions_pkey PRIMARY KEY (session_id ),
  CONSTRAINT core_sessions_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_system_log_pkey PRIMARY KEY (id ),
  CONSTRAINT core_system_log_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_system_log_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_system_log_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";


$statement[] = "CREATE TABLE core_system_log_types
(
  id integer NOT NULL DEFAULT nextval('core_system_log_type_id_seq'::regclass),
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
  CONSTRAINT core_system_messages_pkey PRIMARY KEY (id ),
  CONSTRAINT core_system_messages_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_user_admin_settings_pkey PRIMARY KEY (id ),
  CONSTRAINT core_user_admin_settings_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";


$statement[] = "CREATE TABLE core_user_profile_settings
(
  id integer NOT NULL,
  language_id integer,
  timezone_id integer,
  CONSTRAINT core_user_profile_settings_pkey PRIMARY KEY (id ),
  CONSTRAINT core_user_profile_settings_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_user_profile_settings_language_id_fkey FOREIGN KEY (language_id)
      REFERENCES core_languages (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_user_profile_settings_timezone_id_fkey FOREIGN KEY (timezone_id)
      REFERENCES core_timezones (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";


$statement[] = "

CREATE TABLE core_user_profiles
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
  CONSTRAINT core_user_profiles_pkey PRIMARY KEY (id ),
  CONSTRAINT core_user_profiles_id_fkey FOREIGN KEY (id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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


// DATA

// Include
$statement[] = "INSERT INTO core_base_includes VALUES (nextval('core_base_includes_id_seq'::regclass), 'base', 'base', '0.3.9.9-5');";

// Registry
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_timezone_id', (SELECT id FROM core_base_includes WHERE name='base'), '26');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_os', (SELECT id FROM core_base_includes WHERE name='base'), 'win');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_product_user', (SELECT id FROM core_base_includes WHERE name='base'), 'University of Cologne');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_product_function', (SELECT id FROM core_base_includes WHERE name='base'), 'development server');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_html_title', (SELECT id FROM core_base_includes WHERE name='base'), 'Open-LIMS (development server)');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_update_check', (SELECT id FROM core_base_includes WHERE name='base'), 'false');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_update_check_url', (SELECT id FROM core_base_includes WHERE name='base'), 'http://update.open-lims.org/check.php');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_session_timeout', (SELECT id FROM core_base_includes WHERE name='base'), '36000');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_max_ip_failed_logins', (SELECT id FROM core_base_includes WHERE name='base'), '10');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_max_ip_lead_time', (SELECT id FROM core_base_includes WHERE name='base'), '36000');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_cron_last_run_datetime', (SELECT id FROM core_base_includes WHERE name='base'), '2011-01-01 12:00:00');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_cron_last_run_id', (SELECT id FROM core_base_includes WHERE name='base'), '1');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_cron_last_run_daily_id', (SELECT id FROM core_base_includes WHERE name='base'), '1');";
$statement[] = "INSERT INTO core_base_registry (id, name, include_id, value) VALUES (nextval('core_base_registry_id_seq'::regclass), 'base_cron_last_run_weekly_id', (SELECT id FROM core_base_includes WHERE name='base'), '1');";

// Languages
$statement[] = "INSERT INTO core_languages (id,english_name,language_name,tsvector_name,iso_639,iso_3166) VALUES (nextval('core_languages_id_seq'::regclass),'English','English','english','en','GB')";
$statement[] = "SELECT pg_catalog.setval('core_languages_id_seq', 2, true);";

// Users
$statement[] = "INSERT INTO core_users VALUES (1, 'system', '096013f88fcf51a89f6d0c4e5285428e');";
$statement[] = "INSERT INTO core_user_profiles VALUES (1, NULL, '', 'main', 'administrator', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
$statement[] = "INSERT INTO core_user_profile_settings VALUES (1, 1, 26);";
$statement[] = "INSERT INTO core_user_admin_settings VALUES (1, 't', 'f', 'f', 'f', 'f', '2008-01-01 12:00:00+01', 'f', 'f');";
$statement[] = "SELECT pg_catalog.setval('core_users_id_seq', 100, true);";

// Groups
$statement[] = "INSERT INTO core_groups (id,name) VALUES (1, 'Administrators')";
$statement[] = "INSERT INTO core_groups (id,name) VALUES (2, 'Member-Administrators')";
$statement[] = "INSERT INTO core_groups (id,name) VALUES (11, 'Users')";
$statement[] = "SELECT pg_catalog.setval('core_groups_id_seq', 100, true);";
$statement[] = "INSERT INTO core_group_has_users (primary_key,group_id,user_id) VALUES (nextval('core_group_has_users_primary_key_seq'::regclass), 1, 1)";
$statement[] = "INSERT INTO core_group_has_users (primary_key,group_id,user_id) VALUES (nextval('core_group_has_users_primary_key_seq'::regclass), 11, 1)";

// Currencies
$statement[] = "INSERT INTO core_currencies (id,name,symbol,iso_4217) VALUES (nextval('core_currencies_id_seq'::regclass), 'Euro', '€', 'EUR')";
$statement[] = "INSERT INTO core_currencies (id,name,symbol,iso_4217) VALUES (nextval('core_currencies_id_seq'::regclass), 'US-Dollar', '$', 'USD')";

// Measuring Units
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (1,NULL,'Meter',1,'t','m',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (2,NULL,'Kilogramm',2,'t','kg',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (3,NULL,'Ampere',3,'t','A',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (4,NULL,'Kelvin',4,'t','K',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (5,NULL,'Mol',5,'t','mol',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (6,NULL,'Candela',6,'t','cd',NULL)";
$statement[] = "INSERT INTO core_measuring_units (id,toid,name,type,base,unit_symbol,calculation) VALUES (7,NULL,'Second',7,'t','s',NULL)";
$statement[] = "SELECT pg_catalog.setval('core_measuring_units_id_seq', 7, true);";

// Paper Sizes
$statement[] = "INSERT INTO core_paper_sizes VALUES (2, 'A1', 594, 841, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (3, 'A2', 420, 594, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (4, 'A3', 297, 420, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (5, 'A4', 210, 297, 10, 10, 10, 10, 't', 't');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (6, 'A5', 148, 210, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (7, 'A6', 105, 148, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (8, 'Invoice', 140, 216, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (9, 'Executive', 184, 267, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (10, 'Legal', 216, 356, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (11, 'Letter', 216, 279, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (12, 'Ledger', 279, 432, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (13, 'Broadsheet', 432, 559, 10, 10, 10, 10, 't', 'f');";
$statement[] = "INSERT INTO core_paper_sizes VALUES (1, 'A0', 841, 1189, 10, 10, 10, 10, 't', 'f');";
$statement[] = "SELECT pg_catalog.setval('core_paper_sizes_id_seq', 19, true);";

// Sysmte Log
$statement[] = "INSERT INTO core_system_log_types VALUES (1, 'Security Notices');";
$statement[] = "INSERT INTO core_system_log_types VALUES (2, 'Open-LIMS Errors');";
$statement[] = "INSERT INTO core_system_log_types VALUES (3, 'PHP Errors');";
$statement[] = "INSERT INTO core_system_log_types VALUES (4, 'Deleted Objects');";
$statement[] = "SELECT pg_catalog.setval('core_system_log_types_id_seq', 5, true);";

// Timezones
$statement[] = "INSERT INTO core_timezones VALUES (1, 'Midway Islands, Samoa', 'Pacific/Samoa', -11);";
$statement[] = "INSERT INTO core_timezones VALUES (2, 'Hawaii, Polynesia', 'US/Hawaii', -10);";
$statement[] = "INSERT INTO core_timezones VALUES (3, 'Alaska', 'US/Alaska', -9);";
$statement[] = "INSERT INTO core_timezones VALUES (4, 'Tijuana, Los Angeles, Seattle, Vancouver', 'America/Los_Angeles', -8);";
$statement[] = "INSERT INTO core_timezones VALUES (5, 'Arizona', 'US/Arizona', -7);";
$statement[] = "INSERT INTO core_timezones VALUES (6, 'Chihuahua, La Paz, Mazatlan', 'America/Chihuahua', -7);";
$statement[] = "INSERT INTO core_timezones VALUES (7, 'Arizona, Denver, Salt Lake City, Calgary', 'America/Denver', -7);";
$statement[] = "INSERT INTO core_timezones VALUES (8, 'Chicago, Dallas, Kansas City, Winnipeg', 'America/Chicago', -6);";
$statement[] = "INSERT INTO core_timezones VALUES (9, 'Guadalajara, Mexico City, Monterrey', 'America/Monterrey', -6);";
$statement[] = "INSERT INTO core_timezones VALUES (10, 'Saskatchewan', 'Canada/Saskatchewan', -6);";
$statement[] = "INSERT INTO core_timezones VALUES (11, 'Central America', 'US/Central', -6);";
$statement[] = "INSERT INTO core_timezones VALUES (12, 'Bogota, Lima, Quito', 'America/Bogota', -5);";
$statement[] = "INSERT INTO core_timezones VALUES (13, 'East-Indiana', 'US/East-Indiana', -5);";
$statement[] = "INSERT INTO core_timezones VALUES (14, 'New York, Miami, Atlanta, Detroit, Toronto', 'America/New_York', -5);";
$statement[] = "INSERT INTO core_timezones VALUES (15, 'Atlantic (Canada)', 'Canada/Atlantic', -4);";
$statement[] = "INSERT INTO core_timezones VALUES (16, 'Carcas, La Paz', 'America/La_Paz', -4);";
$statement[] = "INSERT INTO core_timezones VALUES (17, 'Santiago', 'America/Santiago', -4);";
$statement[] = "INSERT INTO core_timezones VALUES (18, 'Newfoundland', 'Canada/Newfoundland', -3);";
$statement[] = "INSERT INTO core_timezones VALUES (19, 'Sao Paulo', 'Brazil/East', -3);";
$statement[] = "INSERT INTO core_timezones VALUES (20, 'Buenes Aires, Georgtown', 'America/Argentina/Buenos_Aires', -3);";
$statement[] = "INSERT INTO core_timezones VALUES (21, 'Greenland, Uruguay, Surinam', 'GMT+3', -3);";
$statement[] = "INSERT INTO core_timezones VALUES (22, 'Cape Verde, Greenland, South Georgia', 'Atlantic/Cape_Verde', -2);";
$statement[] = "INSERT INTO core_timezones VALUES (23, 'Azores', 'Atlantic/Azores', -1);";
$statement[] = "INSERT INTO core_timezones VALUES (24, 'Casablanca, Monrovia', 'Africa/Casablanca', 0);";
$statement[] = "INSERT INTO core_timezones VALUES (25, 'Dublin, Edinburgh, Lisbon, London', 'Europe/London', 0);";
$statement[] = "INSERT INTO core_timezones VALUES (26, 'Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna', 'Europe/Berlin', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (27, 'Belgrade, Bratislava, Budapest, Ljubljana, Prague', 'Europe/Belgrade', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (28, 'Brussels, Copenhagen, Paris, Madrid', 'Europe/Paris', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (29, 'Sarajevo, Skopje, Warsaw, Zagreb', 'Europe/Sarajevo', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (30, 'West-Central Africa', 'Africa/Lagos', 1);";
$statement[] = "INSERT INTO core_timezones VALUES (31, 'Athens, Beirut, Istanbul, Minsk', 'Europe/Athens', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (32, 'Bucharest', 'Europe/Bucharest', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (33, 'Harare, Pratoria', 'Africa/Harare', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (34, 'Helsinki, Kiev, Riga, Sofia, Tallinn, Vilnius', 'Europe/Helsinki', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (35, 'Jerusalem', 'Asia/Jerusalem', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (36, 'Cairo', 'Africa/Cairo', 2);";
$statement[] = "INSERT INTO core_timezones VALUES (37, 'Baghdad', 'Asia/Baghdad', 3);";
$statement[] = "INSERT INTO core_timezones VALUES (38, 'Kuwait, Riyadh', 'Asia/Kuwait', 3);";
$statement[] = "INSERT INTO core_timezones VALUES (39, 'Moscow, Saint Petersburg', 'Europe/Moscow', 3);";
$statement[] = "INSERT INTO core_timezones VALUES (40, 'Nairobi,Teheran', 'Africa/Nairobi', 3);";
$statement[] = "INSERT INTO core_timezones VALUES (41, 'Abu Dhabi, Muscat', 'Asia/Muscat', 4);";
$statement[] = "INSERT INTO core_timezones VALUES (42, 'Baku, Tbilisi, Erivan', 'Asia/Baku', 4);";
$statement[] = "INSERT INTO core_timezones VALUES (43, 'Kabul', 'Asia/Kabul', 4);";
$statement[] = "INSERT INTO core_timezones VALUES (44, 'Islamabad, Karachi, Taschkent', 'Asia/Karachi', 5);";
$statement[] = "INSERT INTO core_timezones VALUES (45, 'Yekaterinburg, New Delhi', 'Asia/Yekaterinburg', 5);";
$statement[] = "INSERT INTO core_timezones VALUES (46, 'Almaty, Novosibirsk, Kathmandu', 'Asia/Novosibirsk', 6);";
$statement[] = "INSERT INTO core_timezones VALUES (47, 'Astana, Dhaka', 'Asia/Dhaka', 6);";
$statement[] = "INSERT INTO core_timezones VALUES (48, 'Sri Jayawardenepura, Rangoon', 'Asia/Rangoon', 6);";
$statement[] = "INSERT INTO core_timezones VALUES (49, 'Bangkok, Hanoi, Jakarta', 'Asia/Jakarta', 7);";
$statement[] = "INSERT INTO core_timezones VALUES (50, 'Krasnoyarsk', 'Asia/Krasnoyarsk', 7);";
$statement[] = "INSERT INTO core_timezones VALUES (51, 'Irkutsk, Ulan Bator', 'Asia/Irkutsk', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (52, 'Kuala Lumpour, Singapore', 'Asia/Singapore', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (53, 'Beijing, Chongqing, Hong kong, Urumchi', 'Asia/Hong_Kong', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (54, 'Perth', 'Australia/Perth', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (55, 'Taipei', 'Asia/Taipei', 8);";
$statement[] = "INSERT INTO core_timezones VALUES (56, 'Yakutsk', 'Asia/Yakutsk', 9);";
$statement[] = "INSERT INTO core_timezones VALUES (57, 'Osaka, Sapporo, Tokyo', 'Asia/Tokyo', 9);";
$statement[] = "INSERT INTO core_timezones VALUES (58, 'Seoul, Darwin, Adelaide', 'Asia/Seoul', 9);";
$statement[] = "INSERT INTO core_timezones VALUES (59, 'Brisbane', 'Australia/Brisbane', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (60, 'Canberra, Melbourne, Sydney', 'Australia/Sydney', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (61, 'Guam, Port Moresby', 'Pacific/Guam', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (62, 'Hobart', 'Australia/Hobart', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (63, 'Vladivostok', 'Asia/Vladivostok', 10);";
$statement[] = "INSERT INTO core_timezones VALUES (64, 'Salomon Islands, New Caledonia, Magadan', 'Asia/Magadan', 11);";
$statement[] = "INSERT INTO core_timezones VALUES (65, 'Auckland, Wellington', 'Pacific/Auckland', 12);";
$statement[] = "INSERT INTO core_timezones VALUES (66, 'Fiji, Kamchatka, Marshall-Islands', 'Pacific/Fiji', 12);";
$statement[] = "INSERT INTO core_timezones VALUES (67, 'Caracas', 'America/Caracas', -4.5);";
$statement[] = "SELECT pg_catalog.setval('core_timezones_id_seq', 67, true);";

?>
