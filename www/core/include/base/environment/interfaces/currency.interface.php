<?php
/**
 * @package base
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
 * Currency Interface
 * @package base
 */
interface CurrencyInterface
{
	/**
	 * @param integer $currency_id
	 */
	function __construct($currency_id);
	
	function __destruct();
	
	/**
	 * @param string $name
	 * @param string $symbol
	 * @param stirng $iso_4217
	 * @return integer
	 */
	public function create($name, $symbol, $iso_4217);
	
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
	public function get_symbol();
	
	/**
	 * @return string
	 */
	public function get_iso_4217();
	
	
	/**
	 * @return array
	 */
	public static function list_entries();
}
?>