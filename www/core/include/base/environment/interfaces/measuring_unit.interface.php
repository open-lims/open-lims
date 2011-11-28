<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Measuring Unit Interface
 * @package base
 */
interface MeasuringUnitInterface
{
	/**
	 * @param integer $measuring_unit_id
	 */
	function __construct($measuring_unit_id);
	
	function __destruct();
	
	/**
	 * @param integer $toid
	 * @param string $name
	 * @param integer $type
	 * @param string $unit_symbol
	 * @param stirng $calculation
	 */
	public function create($toid, $name, $type, $unit_symbol, $calulcation);
	
	/**
	 * @return bool
	 */
	public function delete();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @return string
	 */
	public function get_unit_symbol();
	
	
	/**
	 * @return array
	 */
	public static function list_entries();
}
?>