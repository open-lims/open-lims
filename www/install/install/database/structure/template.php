<?php

$statement = array();

$statement[] = "CREATE TABLE core_oldl_templates
(
  id serial NOT NULL,
  data_entity_id integer,
  CONSTRAINT core_oldl_templates_pkey PRIMARY KEY (id ),
  CONSTRAINT core_oldl_templates_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_olvdl_templates
(
  id serial NOT NULL,
  data_entity_id integer,
  CONSTRAINT core_olvdl_templates_pkey PRIMARY KEY (id ),
  CONSTRAINT core_olvdl_templates_data_entity_id_fkey FOREIGN KEY (data_entity_id)
      REFERENCES core_data_entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_xml_cache
(
  id serial NOT NULL,
  data_entity_id integer,
  path text,
  checksum character(32),
  CONSTRAINT core_xml_cache_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_xml_cache_elements
(
  primary_key serial NOT NULL,
  toid integer,
  field_0 text,
  field_1 text,
  field_2 text,
  field_3 text,
  CONSTRAINT core_xml_cache_elements_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

?>