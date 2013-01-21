<?php
/**
 * @package template
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
 * OLVDL Interface
 * @package template
 */ 	 
interface OlvdlInterface
{
	/**
     * @param integer $olvdl_id
     */
	function __construct($olvdl_id);
	
	function __destruct();
	
	/**
     * Creates a new OLVDL-Template in DB
     * @param integer $data_entity_id
     * @return integer
     */
	public function create($data_entity_id);
	
	/**
     * Deletes an OLVDL-Template from DB
     * @return bool
     */
	public function delete();
	
	/**
     * @return array
     */ 
	public function get_xml_array();
}
?>
