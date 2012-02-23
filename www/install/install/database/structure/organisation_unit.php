<?php

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
  CONSTRAINT core_organ_units_pkey PRIMARY KEY (id ),
  CONSTRAINT core_organisation_units_toid_fkey FOREIGN KEY (toid)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_organisation_units_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES core_organisation_unit_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE INDEX core_organisation_units_name_ix
  ON core_organisation_units
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE TABLE core_organisation_unit_has_quality_managers
(
  organisation_unit_id integer NOT NULL,
  quality_manager_id integer NOT NULL,
  CONSTRAINT core_organisation_unit_has_quality_managers_pkey PRIMARY KEY (organisation_unit_id , quality_manager_id ),
  CONSTRAINT core_organisation_unit_has_quality_ma_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_organisation_unit_has_quality_mana_quality_manager_id_fkey FOREIGN KEY (quality_manager_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_owners
(
  organisation_unit_id integer NOT NULL,
  owner_id integer NOT NULL,
  master_owner boolean,
  CONSTRAINT core_organisation_unit_has_owners_pkey PRIMARY KEY (organisation_unit_id , owner_id ),
  CONSTRAINT core_organisation_unit_has_owners_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_organisation_unit_has_owners_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_members
(
  organisation_unit_id integer NOT NULL,
  member_id integer NOT NULL,
  CONSTRAINT core_organisation_unit_has_members_pkey PRIMARY KEY (organisation_unit_id , member_id ),
  CONSTRAINT core_organisation_unit_has_members_member_id_fkey FOREIGN KEY (member_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_organisation_unit_has_members_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_leaders
(
  organisation_unit_id integer NOT NULL,
  leader_id integer NOT NULL,
  CONSTRAINT core_organisation_unit_has_leaders_pkey PRIMARY KEY (organisation_unit_id , leader_id ),
  CONSTRAINT core_organisation_unit_has_leaders_leader_id_fkey FOREIGN KEY (leader_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_organisation_unit_has_leaders_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_organisation_unit_has_groups
(
  organisation_unit_id integer NOT NULL,
  group_id integer NOT NULL,
  CONSTRAINT core_organisation_unit_has_groups_pkey PRIMARY KEY (organisation_unit_id , group_id ),
  CONSTRAINT core_organisation_unit_has_groups_group_id_fkey FOREIGN KEY (group_id)
      REFERENCES core_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_organisation_unit_has_groups_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

?>