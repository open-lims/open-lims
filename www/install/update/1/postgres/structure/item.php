<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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

$statement[] = "ALTER TABLE core_item_concretion DROP CONSTRAINT core_item_concretion_include_id_fkey;";
$statement[] = "ALTER TABLE core_item_concretion ADD CONSTRAINT core_item_concretion_include_id_fkey FOREIGN KEY (include_id) REFERENCES core_base_includes(id) ON DELETE CASCADE DEFERRABLE;";

$statement[] = "ALTER TABLE core_item_holders DROP CONSTRAINT core_item_holders_include_id_fkey;";
$statement[] = "ALTER TABLE core_item_holders ADD CONSTRAINT core_item_holders_include_id_fkey FOREIGN KEY (include_id) REFERENCES core_base_includes(id) ON DELETE CASCADE DEFERRABLE;";

?>