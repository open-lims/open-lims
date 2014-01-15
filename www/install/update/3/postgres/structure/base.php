<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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


// TABLES

$statement[] = "DROP TABLE IF EXISTS core_measuring_units";

$statement[] = "CREATE TABLE core_base_measuring_unit_categories
(
  id serial NOT NULL,
  name text,
  created_by_user boolean,
  CONSTRAINT core_measuring_unit_categories_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

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
);";

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
);";


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
);";


// FOREIGN KEYS

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


// CHANGES

$statement[] = "ALTER TABLE core_base_module_dialogs RENAME display_name TO language_address";

$statement[] = "ALTER TABLE core_jobs RENAME TO core_base_batch_runs";
$statement[] = "ALTER SEQUENCE core_jobs_id_seq RENAME TO core_base_batch_runs_id_seq";
$statement[] = "ALTER TABLE core_base_batch_runs ALTER id SET DEFAULT nextval('core_base_batch_runs_id_seq'::regclass)";

$statement[] = "ALTER TABLE core_job_types RENAME TO core_base_batch_types";
$statement[] = "ALTER SEQUENCE core_job_types_id_seq RENAME TO core_base_batch_types_id_seq";
$statement[] = "ALTER TABLE core_base_batch_types ALTER id SET DEFAULT nextval('core_base_batch_types_id_seq'::regclass)";

$statement[] = "ALTER TABLE core_user_profiles ADD COLUMN lync text";
$statement[] = "ALTER TABLE core_user_profiles ADD COLUMN jabber text";

$statement[] = "ALTER TABLE core_user_profile_settings RENAME TO core_user_regional_settings";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN time_display_format boolean";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN time_enter_format boolean";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN date_display_format text";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN date_enter_format text";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN country_id integer";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN system_of_units text";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN system_of_paper_format text";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN currency_id integer";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN currency_significant_digits integer";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN decimal_separator text";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN thousand_separator text";
$statement[] = "ALTER TABLE core_user_regional_settings ADD COLUMN name_display_format text";

$statement[] = "ALTER TABLE ONLY core_user_regional_settings ADD CONSTRAINT core_user_regional_settings_country_id_fkey FOREIGN KEY (country_id) 
	  REFERENCES core_countries(id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_user_regional_settings ADD CONSTRAINT core_user_regional_settings_currency_id_fkey FOREIGN KEY (currency_id) 
	  REFERENCES core_currencies(id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "DROP FUNCTION IF EXISTS higher(double precision, double precision)";

?>