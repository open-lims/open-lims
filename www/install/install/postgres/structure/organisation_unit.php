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
$check_statement = "SELECT id FROM core_organisation_units";

$statement = array();

$statement[] = "CREATE TABLE core_organisation_unit_types
(
  id serial NOT NULL,
  name text,
  icon text,
  CONSTRAINT core_organ_unit_types_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_units
(
  id serial NOT NULL,
  toid integer,
  is_root boolean,
  name text,
  type_id integer,
  stores_data boolean,
  \"position\" integer,
  hidden boolean,
  CONSTRAINT core_organ_units_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_quality_managers
(
  organisation_unit_id integer NOT NULL,
  quality_manager_id integer NOT NULL,
  CONSTRAINT core_organisation_unit_has_quality_managers_pkey PRIMARY KEY (organisation_unit_id , quality_manager_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_owners
(
  organisation_unit_id integer NOT NULL,
  owner_id integer NOT NULL,
  master_owner boolean,
  CONSTRAINT core_organisation_unit_has_owners_pkey PRIMARY KEY (organisation_unit_id , owner_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_members
(
  organisation_unit_id integer NOT NULL,
  member_id integer NOT NULL,
  CONSTRAINT core_organisation_unit_has_members_pkey PRIMARY KEY (organisation_unit_id , member_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_leaders
(
  organisation_unit_id integer NOT NULL,
  leader_id integer NOT NULL,
  CONSTRAINT core_organisation_unit_has_leaders_pkey PRIMARY KEY (organisation_unit_id , leader_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_groups
(
  organisation_unit_id integer NOT NULL,
  group_id integer NOT NULL,
  CONSTRAINT core_organisation_unit_has_groups_pkey PRIMARY KEY (organisation_unit_id , group_id )
)
WITH (
  OIDS=FALSE
);";


// INDIZES

$statement[] = "CREATE INDEX core_organisation_units_name_ix
  ON core_organisation_units
  USING btree
  (name COLLATE pg_catalog.\"default\" );";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_organisation_units ADD CONSTRAINT core_organisation_units_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";
      
$statement[] = "ALTER TABLE ONLY core_organisation_units ADD CONSTRAINT core_organisation_units_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_organisation_unit_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_quality_managers ADD CONSTRAINT core_organisation_unit_has_quality_ma_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_quality_managers ADD CONSTRAINT core_organisation_unit_has_quality_mana_quality_manager_id_fkey FOREIGN KEY (quality_manager_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_owners ADD CONSTRAINT core_organisation_unit_has_owners_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_owners ADD CONSTRAINT core_organisation_unit_has_owners_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_members ADD CONSTRAINT core_organisation_unit_has_members_member_id_fkey FOREIGN KEY (member_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_members ADD CONSTRAINT core_organisation_unit_has_members_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_leaders ADD CONSTRAINT core_organisation_unit_has_leaders_leader_id_fkey FOREIGN KEY (leader_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_leaders ADD CONSTRAINT core_organisation_unit_has_leaders_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_groups ADD CONSTRAINT core_organisation_unit_has_groups_group_id_fkey FOREIGN KEY (group_id)
      REFERENCES core_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_organisation_unit_has_groups ADD CONSTRAINT core_organisation_unit_has_groups_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";


// FUNCTIONS

$statement[] = "CREATE OR REPLACE FUNCTION get_organisation_unit_childs(integer)
  RETURNS SETOF integer AS
\$BODY\$DECLARE
organisation_unit_record RECORD;
rec_record RECORD;
BEGIN

	IF \$1 IS NOT NULL THEN

		RETURN NEXT \$1;
		
		FOR organisation_unit_record IN SELECT id FROM core_organisation_units WHERE toid=\$1 AND id != toid
		LOOP

			IF organisation_unit_record.id IS NOT NULL THEN

				FOR rec_record IN SELECT * FROM get_organisation_unit_childs(organisation_unit_record.id) AS id
				LOOP

					RETURN NEXT rec_record.id;

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
  ROWS 1000;
";

?>