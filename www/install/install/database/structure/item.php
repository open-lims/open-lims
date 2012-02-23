<?php

$statement = array();

$statement[] = "CREATE TABLE core_item_class_has_item_information
(
  primary_key serial NOT NULL,
  item_class_id integer,
  item_information_id integer,
  CONSTRAINT core_item_class_has_item_information_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_item_class_has_item_information_item_class_id_fkey FOREIGN KEY (item_class_id)
      REFERENCES core_item_classes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_item_class_has_item_information_item_information_id_fkey FOREIGN KEY (item_information_id)
      REFERENCES core_item_information (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_item_classes
(
  id serial NOT NULL,
  name text,
  datetime timestamp with time zone,
  owner_id integer,
  colour character(6),
  CONSTRAINT core_item_classes_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_item_concretion
(
  id serial NOT NULL,
  type text,
  handling_class text,
  include_id integer,
  CONSTRAINT core_item_concretion_pkey PRIMARY KEY (id ),
  CONSTRAINT core_item_concretion_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_item_has_item_classes
(
  primary_key serial NOT NULL,
  item_id integer,
  item_class_id integer,
  CONSTRAINT core_item_has_item_classes_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_item_has_item_classes_item_class_id_fkey FOREIGN KEY (item_class_id)
      REFERENCES core_item_classes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_item_has_item_classes_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_item_has_item_information
(
  primary_key serial NOT NULL,
  item_id integer,
  item_information_id integer,
  CONSTRAINT core_item_has_item_information_pkey PRIMARY KEY (primary_key ),
  CONSTRAINT core_item_has_item_information_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT core_item_has_item_information_item_information_id_fkey FOREIGN KEY (item_information_id)
      REFERENCES core_item_information (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_item_holders
(
  id serial NOT NULL,
  name text,
  handling_class text,
  include_id integer,
  CONSTRAINT core_item_holders_pkey PRIMARY KEY (id ),
  CONSTRAINT core_item_holders_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_item_information
(
  id serial NOT NULL,
  description text,
  keywords text,
  last_update timestamp with time zone,
  description_text_search_vector tsvector,
  keywords_text_search_vector tsvector,
  language_id integer,
  CONSTRAINT core_item_information_pkey PRIMARY KEY (id ),
  CONSTRAINT core_item_information_language_id_fkey FOREIGN KEY (language_id)
      REFERENCES core_languages (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE INDEX description_fulltext_search
  ON core_item_information
  USING gist
  (description_text_search_vector );";

$statement[] = "CREATE INDEX keywords_fulltext_search
  ON core_item_information
  USING gist
  (keywords_text_search_vector );";

$statement[] = "CREATE TABLE core_items
(
  id serial NOT NULL,
  datetime timestamp with time zone,
  CONSTRAINT core_items_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

?>