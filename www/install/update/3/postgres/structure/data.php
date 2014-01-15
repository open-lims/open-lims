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


// FOREIGN KEYS

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


// FUNCTIONS

$statement[] = "CREATE OR REPLACE FUNCTION get_all_parameter_versions(integer, integer)
  RETURNS SETOF integer AS
\$BODY\$DECLARE
parameter_record RECORD;
rec_return RECORD;
BEGIN
	
	IF $2 IS NULL THEN

		FOR parameter_record IN SELECT id FROM core_data_parameter_versions WHERE previous_version_id=id AND parameter_id=$1 ORDER BY version
		LOOP

			IF parameter_record.id IS NOT NULL THEN

				RETURN NEXT parameter_record.id;

				FOR rec_return IN select * from get_all_parameter_versions($1, parameter_record.id) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	ELSE

		FOR parameter_record IN SELECT id FROM core_data_parameter_versions WHERE previous_version_id=$2 AND parameter_id=$1 AND previous_version_id != id ORDER BY version
		LOOP

			IF parameter_record.id IS NOT NULL THEN
				
				RETURN NEXT parameter_record.id;

				FOR rec_return IN select * from get_all_parameter_versions($1, value_record.id) AS subid
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

?>