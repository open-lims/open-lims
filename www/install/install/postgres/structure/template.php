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
$check_statement = "SELECT id FROM core_oldl_templates";

$statement = array();

$statement[] = "CREATE TABLE core_oldl_templates
(
  id serial NOT NULL,
  data_entity_id integer,
  CONSTRAINT core_oldl_templates_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";

$statement[] = "CREATE TABLE core_olvdl_templates
(
  id serial NOT NULL,
  data_entity_id integer,
  CONSTRAINT core_olvdl_templates_pkey PRIMARY KEY (id )
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