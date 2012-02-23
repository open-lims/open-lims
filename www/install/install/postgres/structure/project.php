<?php

$check_statement = "SELECT id FROM core_projects";

$statement = array();

$statement[] = "CREATE TABLE core_project_has_folder
(
  project_id integer NOT NULL,
  folder_id integer NOT NULL,
  CONSTRAINT core_project_has_folder_pkey PRIMARY KEY (project_id , folder_id ),
  CONSTRAINT core_project_has_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_has_folder_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_project_has_items_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_project_has_items_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_has_items_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_has_items_project_status_id_fkey FOREIGN KEY (project_status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_project_has_project_status_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_project_has_project_status_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_has_project_status_status_id_fkey FOREIGN KEY (status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_links
(
  primary_key serial NOT NULL,
  to_project_id integer,
  project_id integer,
  CONSTRAINT core_project_links_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_project_links_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_links_to_project_id_fkey FOREIGN KEY (to_project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_project_log_pkey PRIMARY KEY (id ),
  CONSTRAINT core_project_log_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_log_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_log_has_items
(
  primary_key serial NOT NULL,
  project_log_id integer,
  item_id integer,
  CONSTRAINT core_item_has_project_log_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_item_has_project_log_project_log_id_fkey FOREIGN KEY (project_log_id)
      REFERENCES core_project_log (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_log_has_item_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_project_log_has_project_status_log_id_fkey FOREIGN KEY (log_id)
      REFERENCES core_project_log (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_log_has_project_status_status_id_fkey FOREIGN KEY (status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
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
  CONSTRAINT core_project_permissions_pkey PRIMARY KEY (id ),
  CONSTRAINT core_project_permissions_group_id_fkey FOREIGN KEY (group_id)
      REFERENCES core_groups (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_permissions_organisation_unit_id_fkey FOREIGN KEY (organisation_unit_id)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_permissions_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_permissions_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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

$statement[] = "CREATE INDEX core_project_status_name_ix
  ON core_project_status
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE TABLE core_project_status_has_folder
(
  project_id integer NOT NULL,
  project_status_id integer NOT NULL,
  folder_id integer NOT NULL,
  CONSTRAINT core_project_status_has_folder_pkey PRIMARY KEY (project_id , project_status_id , folder_id ),
  CONSTRAINT core_project_status_has_folder_folder_id_fkey FOREIGN KEY (folder_id)
      REFERENCES core_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_status_has_folder_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_status_has_folder_project_status_id_fkey FOREIGN KEY (project_status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_task_has_previous_tasks
(
  task_id integer NOT NULL,
  previous_task_id integer NOT NULL,
  CONSTRAINT core_project_task_has_previous_tasks_pkey PRIMARY KEY (task_id , previous_task_id ),
  CONSTRAINT core_project_task_has_previous_tasks_previous_task_id_fkey FOREIGN KEY (previous_task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_task_has_previous_tasks_task_id_fkey FOREIGN KEY (task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_task_milestones
(
  task_id integer NOT NULL,
  name text,
  CONSTRAINT core_project_task_milestones_pkey PRIMARY KEY (task_id ),
  CONSTRAINT core_project_task_milestones_task_id_fkey FOREIGN KEY (task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_project_task_processes
(
  task_id integer NOT NULL,
  name text,
  progress double precision,
  CONSTRAINT core_project_task_processes_pkey PRIMARY KEY (task_id ),
  CONSTRAINT core_project_task_processes_task_id_fkey FOREIGN KEY (task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_project_task_status_processes_pkey PRIMARY KEY (task_id ),
  CONSTRAINT core_project_task_status_processes_begin_status_id_fkey FOREIGN KEY (begin_status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_task_status_processes_end_status_id_fkey FOREIGN KEY (end_status_id)
      REFERENCES core_project_status (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_task_status_processes_task_id_fkey FOREIGN KEY (task_id)
      REFERENCES core_project_tasks (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_project_tasks_pkey PRIMARY KEY (id ),
  CONSTRAINT core_project_tasks_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_tasks_project_id_fkey FOREIGN KEY (project_id)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_project_templates_pkey PRIMARY KEY (id ),
  CONSTRAINT core_project_templates_cat_id_fkey FOREIGN KEY (cat_id)
      REFERENCES core_project_template_cats (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_project_templates_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_oldl_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE INDEX core_project_templates_name_ix
  ON core_project_templates
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE TABLE core_project_user_data
(
  user_id integer NOT NULL,
  quota bigint,
  CONSTRAINT core_project_user_data_pkey PRIMARY KEY (user_id ),
  CONSTRAINT core_project_user_data_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  CONSTRAINT core_projects_pkey PRIMARY KEY (id ),
  CONSTRAINT core_projects_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_projects_template_id_fkey FOREIGN KEY (template_id)
      REFERENCES core_project_templates (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_projects_toid_organ_unit_fkey FOREIGN KEY (toid_organ_unit)
      REFERENCES core_organisation_units (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_projects_toid_project_fkey FOREIGN KEY (toid_project)
      REFERENCES core_projects (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE INDEX core_projects_name_ix
  ON core_projects
  USING btree
  (name COLLATE pg_catalog.\"default\" );";

$statement[] = "CREATE TABLE core_virtual_folder_is_project
(
  id integer NOT NULL,
  CONSTRAINT core_virtual_folder_is_project_pkey PRIMARY KEY (id ),
  CONSTRAINT core_virtual_folder_is_project_id_fkey FOREIGN KEY (id)
      REFERENCES core_virtual_folders (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

?>