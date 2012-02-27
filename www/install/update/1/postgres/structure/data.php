<?php

$statement = array();

$statement[] = "CREATE TABLE core_file_image_cache
(
  id serial NOT NULL,
  file_version_id integer,
  width integer,
  height integer,
  size bigint,
  last_access timestamp with time zone,
  CONSTRAINT core_file_image_cache_pkey PRIMARY KEY (id ),
  CONSTRAINT core_file_image_cache_file_version_id_fkey FOREIGN KEY (file_version_id)
      REFERENCES core_file_versions (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE INDEX core_file_image_cache_height_ix
  ON core_file_image_cache
  USING btree
  (file_version_id , height );";

$statement[] = "CREATE INDEX core_file_image_cache_width_ix
  ON core_file_image_cache
  USING btree
  (file_version_id , width );";

$statement[] = "ALTER TABLE core_value_var_cases DROP CONSTRAINT core_value_var_cases_include_id_fkey;";
$statement[] = "ALTER TABLE core_value_var_cases ADD CONSTRAINT core_value_var_cases_include_id_fkey FOREIGN KEY (include_id) REFERENCES core_base_includes(id) ON DELETE CASCADE DEFERRABLE;";

$statement[] = "ALTER TABLE core_folder_concretion DROP CONSTRAINT core_folder_concretion_include_id_fkey;";
$statement[] = "ALTER TABLE core_folder_concretion ADD CONSTRAINT core_folder_concretion_include_id_fkey FOREIGN KEY (include_id) REFERENCES core_base_includes(id) ON DELETE CASCADE DEFERRABLE;";

?>