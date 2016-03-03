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
$check_statement = "SELECT id FROM core_items";

$statement = array();

$statement[] = "CREATE TABLE core_item_class_has_item_information
(
  primary_key serial NOT NULL,
  item_class_id integer,
  item_information_id integer,
  CONSTRAINT core_item_class_has_item_information_pkey PRIMARY KEY (primary_key )
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
  CONSTRAINT core_item_concretion_pkey PRIMARY KEY (id )
  )
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_item_has_item_classes
(
  primary_key serial NOT NULL,
  item_id integer,
  item_class_id integer,
  CONSTRAINT core_item_has_item_classes_pkey PRIMARY KEY (primary_key )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_item_has_item_information
(
  primary_key serial NOT NULL,
  item_id integer,
  item_information_id integer,
  CONSTRAINT core_item_has_item_information_pkey PRIMARY KEY (primary_key )
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
  CONSTRAINT core_item_holders_pkey PRIMARY KEY (id )
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
  CONSTRAINT core_item_information_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_items
(
  id serial NOT NULL,
  datetime timestamp with time zone,
  CONSTRAINT core_items_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";


// INDIZES

$statement[] = "CREATE INDEX description_fulltext_search
  ON core_item_information
  USING gist
  (description_text_search_vector );";

$statement[] = "CREATE INDEX keywords_fulltext_search
  ON core_item_information
  USING gist
  (keywords_text_search_vector );";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_item_class_has_item_information ADD CONSTRAINT core_item_class_has_item_information_item_class_id_fkey FOREIGN KEY (item_class_id)
      REFERENCES core_item_classes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_item_class_has_item_information ADD CONSTRAINT core_item_class_has_item_information_item_information_id_fkey FOREIGN KEY (item_information_id)
      REFERENCES core_item_information (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_item_concretion ADD CONSTRAINT core_item_concretion_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_item_has_item_classes ADD CONSTRAINT core_item_has_item_classes_item_class_id_fkey FOREIGN KEY (item_class_id)
      REFERENCES core_item_classes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_item_has_item_classes ADD CONSTRAINT core_item_has_item_classes_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_item_has_item_information ADD CONSTRAINT core_item_has_item_information_item_id_fkey FOREIGN KEY (item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_item_has_item_information ADD CONSTRAINT core_item_has_item_information_item_information_id_fkey FOREIGN KEY (item_information_id)
      REFERENCES core_item_information (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_item_holders ADD CONSTRAINT core_item_holders_include_id_fkey FOREIGN KEY (include_id)
      REFERENCES core_base_includes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE";

$statement[] = "ALTER TABLE ONLY core_item_information ADD CONSTRAINT core_item_information_language_id_fkey FOREIGN KEY (language_id)
      REFERENCES core_languages (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

?>