<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
$check_statement = "SELECT id FROM core_manufacturers";

$statement = array();

$statement[] = "CREATE TABLE core_manufacturers
(
  id serial NOT NULL,
  name text,
  user_id integer,
  datetime timestamp with time zone,
  CONSTRAINT core_manufacturers_pkey PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);";


// INDIZES

$statement[] = "CREATE INDEX core_manufacturers_name_ix
  ON core_manufacturers
  USING btree
  (name COLLATE pg_catalog.\"default\" );";


// FOREIGN KEYS

$statement[] = "ALTER TABLE ONLY core_manufacturers ADD CONSTRAINT core_manufacturers_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES core_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";
?>