<?php
/**
 * @package base
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
	 * @param integer $category_id
	 * @param string $name
	 * @param string $symbol
	 * @param integer $min_value
	 * @param integer $max_value
	 * @param integer $min_prefix_exponent
	 * @param integer $max_prefix_exponent
	 * @param integer $prefix_calculcation_exponent
	 * @param integer $calculation
	 * @param string $type
	 * @return integer
	 */
	public function create($category_id, $name, $symbol, $min_value, $max_value, $min_prefix_exponent, $max_prefix_exponent, $prefix_calculation_exponent, $calculation, $type);
	
	/**
	 * @return bool
	 */
	public function delete();
	
	/**
	 * @return integer
	 */
	public function get_category_id();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @return string
	 */
	public function get_unit_symbol();
	
	/**
	 * @return integer
	 */
	public function get_min_value();
	
	/**
	 * @return integer
	 */
	public function get_max_value();
	
	/**
	 * @return integer
	 */
	public function get_min_prefix_exponent();
	
	/**
	 * @return integer
	 */
	public function get_max_prefix_exponent();
	
	/**
	 * @return integer
	 */
	public function get_prefix_calculation_exponent();
	
	/**
	 * @return string
	 */
	public function get_calculation();
	
	/**
	 * @return string
	 */
	public function get_type();
	
	/**
	 * @param integer $category_id
	 * @return bool
	 */
	public function set_category_id($category_id);
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name);
	
	/**
	 * @param string $unit_symbol
	 * @return bool
	 */
	public function set_unit_symbol($unit_symbol);
	
	/**
	 * @param float $min_value
	 * @return bool
	 */
	public function set_min_value($min_value);
	
	/**
	 * @param float $max_value
	 * @return bool
	 */
	public function set_max_value($max_value);
	
	/**
	 * @param integer $min_prefix_exponent
	 * @return bool
	 */
	public function set_min_prefix_exponent($min_prefix_exponent);
	
	/**
	 * @param integer $max_prefix_exponent
	 * @return bool
	 */
	public function set_max_prefix_exponent($max_prefix_exponent);
	
	/**
	 * @param integer $prefix_calculation_exponent
	 * @return bool
	 */
	public function set_prefix_calculation_exponent($prefix_calculation_exponent);
	
	/**
	 * @param string $calculation
	 * @return bool
	 */
	public function set_calculation($calculation);
	
	/**
	 * @param string $type
	 * @return bool
	 */
	public function set_type($type);
	
	
	/**
	 * @return array
	 */
	public static function get_categorized_list();
}
?>