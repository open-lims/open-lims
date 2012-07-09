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
$statement = array();

$statement[] = "CREATE TABLE core_binaries
(
  id serial NOT NULL,
  path text,
  file text,
  CONSTRAINT core_binaries_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_extensions
(
  id serial NOT NULL,
  name text,
  identifer text,
  folder text,
  class text,
  main_file text,
  version text,
  CONSTRAINT core_extensions_pkey PRIMARY KEY (id ),
  CONSTRAINT core_extensions_identifer_key UNIQUE (identifer )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_job_types
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
);";

$statement[] = "CREATE TABLE core_jobs
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
);";

$statement[] = "CREATE TABLE core_service_has_log_entries
(
  service_id integer NOT NULL,
  log_entry_id integer NOT NULL,
  CONSTRAINT core_service_has_log_entries_pkey PRIMARY KEY (service_id , log_entry_id )
)
WITH (
  OIDS=FALSE
);";

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
);";

$statement[] = "ALTER TABLE ONLY core_job_types ADD CONSTRAINT core_job_types_binary_id_fkey FOREIGN KEY (binary_id)
      REFERENCES core_binaries (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_jobs ADD CONSTRAINT core_jobs_binary_id_fkey FOREIGN KEY (binary_id)
      REFERENCES core_binaries (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_jobs ADD CONSTRAINT core_jobs_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_job_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_jobs ADD CONSTRAINT core_jobs_user_id_fkey FOREIGN KEY (user_id)
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

$statement[] = "ALTER TABLE core_base_module_navigation RENAME display_name TO language_address";
$statement[] = "ALTER TABLE core_base_module_navigation ADD COLUMN alias text";
$statement[] = "ALTER TABLE core_base_module_navigation ADD COLUMN controller_class text";
$statement[] = "ALTER TABLE core_base_module_navigation ADD COLUMN controller_file text";

?>