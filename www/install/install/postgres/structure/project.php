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
$check_statement = "SELECT id FROM core_projects";

$statement = array();

$statement[] = "CREATE TABLE core_project_has_extension_runs
(
  primary_key serial NOT NULL,
  project_id integer,
  extension_id integer,
  run integer,
  CONSTRAINT core_project_has_extension_runs_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_has_folder
(
  project_id integer NOT NULL,
  folder_id integer NOT NULL,
  CONSTRAINT core_project_has_folder_pkey PRIMARY KEY (project_id , folder_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_has_items
(
  primary_key serial NOT NULL,
  project_id integer,
  item_id integer,
  active boolean,
  required boolean,
  gid integer,
  project_status_id integer,
  parent_item_id integer,
  CONSTRAINT core_project_has_items_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_has_project_status
(
  primary_key serial NOT NULL,
  project_id integer,
  status_id integer,
  datetime timestamp with time zone,
  current boolean,
  CONSTRAINT core_project_has_project_status_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_links
(
  primary_key serial NOT NULL,
  to_project_id integer,
  project_id integer,
  CONSTRAINT core_project_links_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_log
(
  id serial NOT NULL,
  project_id integer,
  datetime timestamp with time zone,
  content text,
  cancel boolean,
  important boolean,
  owner_id integer,
  CONSTRAINT core_project_log_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_log_has_items
(
  primary_key serial NOT NULL,
  project_log_id integer,
  item_id integer,
  CONSTRAINT core_item_has_project_log_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_log_has_project_status
(
  primary_key serial NOT NULL,
  log_id integer,
  status_id integer,
  CONSTRAINT core_project_log_has_project_status_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_project_log_has_project_status_log_id_key UNIQUE (log_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_permissions
(
  id serial NOT NULL,
  user_id integer,
  organisation_unit_id integer,
  group_id integer,
  project_id integer,
  permission integer,
  owner_id integer,
  intention integer,
  CONSTRAINT core_project_permissions_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_status
(
  id serial NOT NULL,
  name text,
  analysis boolean,
  blocked boolean,
  comment text,
  CONSTRAINT core_project_status_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_status_has_folder
(
  project_id integer NOT NULL,
  project_status_id integer NOT NULL,
  folder_id integer NOT NULL,
  CONSTRAINT core_project_status_has_folder_pkey PRIMARY KEY (project_id , project_status_id , folder_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_task_has_previous_tasks
(
  task_id integer NOT NULL,
  previous_task_id integer NOT NULL,
  CONSTRAINT core_project_task_has_previous_tasks_pkey PRIMARY KEY (task_id , previous_task_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_task_milestones
(
  task_id integer NOT NULL,
  name text,
  CONSTRAINT core_project_task_milestones_pkey PRIMARY KEY (task_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_task_processes
(
  task_id integer NOT NULL,
  name text,
  progress double precision,
  CONSTRAINT core_project_task_processes_pkey PRIMARY KEY (task_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_task_status_processes
(
  task_id integer NOT NULL,
  begin_status_id integer,
  end_status_id integer,
  finalise boolean,
  subtraction_points integer,
  CONSTRAINT core_project_task_status_processes_pkey PRIMARY KEY (task_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_tasks
(
  id serial NOT NULL,
  type_id integer,
  project_id integer,
  owner_id integer,
  comment text,
  start_date date,
  start_time time with time zone,
  end_date date,
  end_time time with time zone,
  whole_day boolean,
  auto_connect boolean,
  finished boolean,
  created_at timestamp with time zone,
  finished_at timestamp with time zone,
  over_time boolean,
  CONSTRAINT core_project_tasks_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_template_cats
(
  id serial NOT NULL,
  name text,
  CONSTRAINT core_project_template_cats_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_templates
(
  id integer NOT NULL,
  name text,
  cat_id integer,
  parent_template boolean,
  template_id integer,
  CONSTRAINT core_project_templates_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_user_data
(
  user_id integer NOT NULL,
  quota bigint,
  CONSTRAINT core_project_user_data_pkey PRIMARY KEY (user_id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_projects
(
  id serial NOT NULL,
  toid_organ_unit integer,
  toid_project integer,
  datetime timestamp with time zone,
  name text,
  owner_id integer,
  template_id integer,
  quota bigint,
  filesize bigint,
  deleted boolean,
  CONSTRAINT core_projects_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_virtual_folder_is_project
(
  id integer NOT NULL,
  CONSTRAINT core_virtual_folder_is_project_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";


// INDIZES

$statement[] = "CREATE INDEX core_project_status_name_ix
  ON core_project_status
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX core_project_templates_name_ix
  ON core_project_templates
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE INDEX core_projects_name_ix
  ON core_projects
  USING btree
  (name COLLATE pg_catalog.\"default\" );";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_project_has_extension_runs ADD CONSTRAINT core_project_has_extension_runs_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_has_extension_runs ADD CONSTRAINT core_project_has_extension_runs_extension_id_fkey FOREIGN KEY (extension_id)
      REFERENCES core_extensions (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_has_folder ADD CONSTRAINT core_project_has_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_has_folder ADD CONSTRAINT core_project_has_folder_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_has_items ADD CONSTRAINT core_project_has_items_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_has_items ADD CONSTRAINT core_project_has_items_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_has_items ADD CONSTRAINT core_project_has_items_project_status_id_fkey FOREIGN KEY (project_status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_has_items ADD CONSTRAINT core_project_has_items_parent_item_id_fkey FOREIGN KEY (parent_item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_has_project_status ADD CONSTRAINT core_project_has_project_status_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";
      
$statement[] = "ALTER TABLE ONLY core_project_has_project_status ADD CONSTRAINT core_project_has_project_status_status_id_fkey FOREIGN KEY (status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_links ADD CONSTRAINT core_project_links_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_links ADD CONSTRAINT core_project_links_to_project_id_fkey FOREIGN KEY (to_project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_log ADD CONSTRAINT core_project_log_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_log ADD CONSTRAINT core_project_log_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_log_has_items ADD CONSTRAINT core_item_has_project_log_project_log_id_fkey FOREIGN KEY (project_log_id)
      REFERENCES core_project_log (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_log_has_items ADD CONSTRAINT core_project_log_has_item_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_log_has_project_status ADD CONSTRAINT core_project_log_has_project_status_log_id_fkey FOREIGN KEY (log_id)
      REFERENCES core_project_log (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_log_has_project_status ADD CONSTRAINT core_project_log_has_project_status_status_id_fkey FOREIGN KEY (status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_permissions ADD CONSTRAINT core_project_permissions_group_id_fkey FOREIGN KEY (group_id)
      REFERENCES core_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_permissions ADD CONSTRAINT core_project_permissions_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_permissions ADD CONSTRAINT core_project_permissions_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_permissions ADD CONSTRAINT core_project_permissions_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_status_has_folder ADD CONSTRAINT core_project_status_has_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_status_has_folder ADD CONSTRAINT core_project_status_has_folder_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_status_has_folder ADD CONSTRAINT core_project_status_has_folder_project_status_id_fkey FOREIGN KEY (project_status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_task_has_previous_tasks ADD CONSTRAINT core_project_task_has_previous_tasks_previous_task_id_fkey FOREIGN KEY (previous_task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_task_has_previous_tasks ADD CONSTRAINT core_project_task_has_previous_tasks_task_id_fkey FOREIGN KEY (task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_task_milestones ADD CONSTRAINT core_project_task_milestones_task_id_fkey FOREIGN KEY (task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_task_processes ADD CONSTRAINT core_project_task_processes_task_id_fkey FOREIGN KEY (task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_task_status_processes ADD CONSTRAINT core_project_task_status_processes_begin_status_id_fkey FOREIGN KEY (begin_status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_task_status_processes ADD CONSTRAINT core_project_task_status_processes_end_status_id_fkey FOREIGN KEY (end_status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_task_status_processes ADD CONSTRAINT core_project_task_status_processes_task_id_fkey FOREIGN KEY (task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_tasks ADD CONSTRAINT core_project_tasks_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_tasks ADD CONSTRAINT core_project_tasks_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_templates ADD CONSTRAINT core_project_templates_cat_id_fkey FOREIGN KEY (cat_id)
      REFERENCES core_project_template_cats (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_templates ADD CONSTRAINT core_project_templates_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_oldl_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_project_user_data ADD CONSTRAINT core_project_user_data_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_projects ADD CONSTRAINT core_projects_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_projects ADD CONSTRAINT core_projects_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_project_templates (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_projects ADD CONSTRAINT core_projects_toid_organ_unit_fkey FOREIGN KEY (toid_organ_unit)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_projects ADD CONSTRAINT core_projects_toid_project_fkey FOREIGN KEY (toid_project)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_virtual_folder_is_project ADD CONSTRAINT core_virtual_folder_is_project_id_fkey FOREIGN KEY (id)
      REFERENCES core_virtual_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

// FUNCTIONS

$statement[] = "CREATE OR REPLACE FUNCTION get_project_id_by_folder_id(folder_id integer)
  RETURNS integer AS
\$BODY\$DECLARE
project_id INTEGER;
parent_folder_id INTEGER;
BEGIN
	

	IF \$1 IS NOT NULL THEN

		SELECT core_project_has_folder.project_id INTO project_id FROM core_project_has_folder WHERE core_project_has_folder.folder_id = \$1;
		IF project_id IS NOT NULL THEN
			RETURN project_id;
		ELSE
			SELECT core_folders.id INTO parent_folder_id FROM core_folders WHERE core_folders.data_entity_id =
				(SELECT data_entity_pid FROM core_data_entity_has_data_entities WHERE data_entity_cid = (SELECT data_entity_id FROM core_folders WHERE id=folder_id) AND (data_entity_pid IN (SELECT data_entity_id FROM core_folders)));

			RETURN get_project_id_by_folder_id(parent_folder_id);
		END IF;

		

	ELSE

		RETURN NULL;

	END IF;

END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;";

$statement[] = "CREATE OR REPLACE FUNCTION get_project_supplementary_folder(folder_id integer)
  RETURNS integer AS
\$BODY\$DECLARE
supplementary_folder_id INTEGER;
BEGIN
	

	IF \$1 IS NOT NULL THEN

		SELECT core_folders.id INTO supplementary_folder_id FROM core_folders WHERE core_folders.data_entity_id IN
				(SELECT data_entity_cid FROM core_data_entity_has_data_entities WHERE data_entity_pid = (SELECT data_entity_id FROM core_folders WHERE id=folder_id) AND (data_entity_cid IN (SELECT data_entity_id FROM core_folders)))
				AND TRIM(LOWER(name)) = 'supplementary';

		RETURN supplementary_folder_id;

	ELSE

		RETURN NULL;

	END IF;

END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;";

$statement[] = "CREATE OR REPLACE FUNCTION project_permission_group(project_id integer, group_id integer)
  RETURNS boolean AS
\$BODY\$DECLARE
	permission_rec RECORD;
	position1_rec RECORD; 
	position4_rec RECORD;   
	position5_rec RECORD;         
BEGIN
	FOR permission_rec IN SELECT CAST(permission::BIT(7) AS TEXT) AS permission FROM core_project_permissions WHERE core_project_permissions.project_id = \$1 AND core_project_permissions.group_id = \$2
LOOP

	SELECT INTO position1_rec SUBSTRING(permission_rec.permission FROM 1 FOR 1) AS resultchar;
	SELECT INTO position4_rec SUBSTRING(permission_rec.permission FROM 4 FOR 1) AS resultchar;
	SELECT INTO position5_rec SUBSTRING(permission_rec.permission FROM 5 FOR 1) AS resultchar;

IF position1_rec.resultchar = '1' THEN
	RETURN TRUE;
ELSE

	IF position4_rec.resultchar = '1' THEN
		RETURN TRUE;
	ELSE

		IF position5_rec.resultchar = '1' THEN
			RETURN TRUE;
		END IF;

	END IF;

END IF;
END LOOP;
RETURN FALSE;
END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;";

$statement[] = "CREATE OR REPLACE FUNCTION project_permission_organisation_unit(project_id integer, organisation_unit_id integer)
  RETURNS boolean AS
\$BODY\$DECLARE
	permission_rec RECORD;
	position1_rec RECORD; 
	position4_rec RECORD;   
	position5_rec RECORD;         
BEGIN
	FOR permission_rec IN SELECT CAST(permission::BIT(7) AS TEXT) AS permission FROM core_project_permissions WHERE core_project_permissions.project_id = \$1 AND core_project_permissions.organisation_unit_id = \$2
LOOP

	SELECT INTO position1_rec SUBSTRING(permission_rec.permission FROM 1 FOR 1) AS resultchar;
	SELECT INTO position4_rec SUBSTRING(permission_rec.permission FROM 4 FOR 1) AS resultchar;
	SELECT INTO position5_rec SUBSTRING(permission_rec.permission FROM 5 FOR 1) AS resultchar;

IF position1_rec.resultchar = '1' THEN
	RETURN TRUE;
ELSE

	IF position4_rec.resultchar = '1' THEN
		RETURN TRUE;
	ELSE

		IF position5_rec.resultchar = '1' THEN
			RETURN TRUE;
		END IF;

	END IF;

END IF;
END LOOP;
RETURN FALSE;
END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;";

$statement[] = "CREATE OR REPLACE FUNCTION project_permission_user(project_id integer, user_id integer)
  RETURNS boolean AS
\$BODY\$DECLARE
	permission_rec RECORD;
	position1_rec RECORD; 
	position4_rec RECORD;   
	position5_rec RECORD;         
BEGIN
	FOR permission_rec IN SELECT CAST(permission::BIT(7) AS TEXT) AS permission FROM core_project_permissions WHERE core_project_permissions.project_id = \$1 AND core_project_permissions.user_id = \$2
LOOP

	SELECT INTO position1_rec SUBSTRING(permission_rec.permission FROM 1 FOR 1) AS resultchar;
	SELECT INTO position4_rec SUBSTRING(permission_rec.permission FROM 4 FOR 1) AS resultchar;
	SELECT INTO position5_rec SUBSTRING(permission_rec.permission FROM 5 FOR 1) AS resultchar;

IF position1_rec.resultchar = '1' THEN
	RETURN TRUE;
ELSE

	IF position4_rec.resultchar = '1' THEN
		RETURN TRUE;
	ELSE

		IF position5_rec.resultchar = '1' THEN
			RETURN TRUE;
		END IF;

	END IF;

END IF;
END LOOP;
RETURN FALSE;
END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;";

$statement[] = "CREATE OR REPLACE FUNCTION search_get_project_subprojects(integer, integer, integer)
  RETURNS SETOF integer AS
\$BODY\$DECLARE
project_record RECORD;
rec_return RECORD;
BEGIN
	
	IF \$3 IS NULL THEN

	IF \$1 IS NOT NULL THEN

		FOR project_record IN SELECT id FROM core_projects WHERE toid_organ_unit=\$1 AND toid_project IS NULL
		LOOP

			IF project_record.id IS NOT NULL THEN

				RETURN NEXT project_record.id;

				FOR rec_return IN select * from search_get_project_subprojects(NULL, project_record.id, NULL) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	ELSE

		FOR project_record IN SELECT id FROM core_projects WHERE toid_project=\$2 AND toid_organ_unit IS NULL
		LOOP

			IF project_record.id IS NOT NULL THEN

				RETURN NEXT project_record.id;

				FOR rec_return IN select * from search_get_project_subprojects(NULL, project_record.id, NULL) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	END IF;

	ELSE

	IF \$1 IS NOT NULL THEN

		FOR project_record IN SELECT id FROM core_projects WHERE toid_organ_unit=\$1 AND toid_project IS NULL AND template_id = \$3
		LOOP

			IF project_record.id IS NOT NULL THEN

				RETURN NEXT project_record.id;

				FOR rec_return IN select * from search_get_project_subprojects(NULL, project_record.id, NULL) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	ELSE

		FOR project_record IN SELECT id FROM core_projects WHERE toid_project=\$2 AND toid_organ_unit IS NULL AND template_id = \$3
		LOOP

			IF project_record.id IS NOT NULL THEN

				RETURN NEXT project_record.id;

				FOR rec_return IN select * from search_get_project_subprojects(NULL, project_record.id, NULL) AS subid
				LOOP
					RETURN NEXT rec_return.subid;
				END LOOP;

			ELSE
				RETURN;
			END IF;

		END LOOP;

	END IF;

	END IF;

	RETURN;	

END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;";

?>