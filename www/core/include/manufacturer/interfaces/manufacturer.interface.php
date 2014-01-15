<?php
/**
 * @package manufacturer
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
 * Manufacturer Management Interface
 * @package manufacturer
 */ 		 
interface ManufacturerInterface
{
	/**
	 * @param integer $manufacturer_id
	 */
	function __construct($manufacturer_id);
	
	function __destruct();
	
	/**
	 * @param string $name
	 * @return $name
	 */
	public function create($name);
	
	/**
	 * @return bool
	 */
	public function delete();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name);
	
	/**
	 * @param string $string
	 * @return integer
	 */
	public static function count_entries($string);
	
	/**
	 * @param integer $number_of_entries
	 * @param integer $start_entry
	 * @param string $start_string
	 * @return array
	 */
	public static function list_manufacturers($number_of_entries, $start_entry, $start_string);
}

?>
