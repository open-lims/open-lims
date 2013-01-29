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

$statement[] = "ALTER TABLE core_project_has_items ADD COLUMN parent_item_id integer";

$statement[] = "ALTER TABLE ONLY core_project_has_items ADD CONSTRAINT core_project_has_items_parent_item_id_fkey FOREIGN KEY (parent_item_id)
      REFERENCES core_items (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE";

?>