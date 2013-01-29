<?php
/**
 * @package data
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
 * Value Var Case Interface
 * @package data
 */
interface ValueVarCaseInterface
{	
	/**
	 * Returns the content based on string_array
	 * @param array $string_array array of instructions
	 * @param array $stack stack of instructions
	 * @param mixed $result current result set
	 * @param mixed $temp current temp set
	 * @return mixed
	 */
	public function get_content($string_array, $stack, $result, $temp);
	
	/**
	 * Returns the current stack of instructions
	 * @return array
	 */
	public function get_stack();
	
	/**
	 * Returns the current array of instructions (Should be empty after running)
	 * @return array
	 */
	public function get_string_array();
	
	
	/**
	 * Checks if the current value is a case of current class (used for "this" instructions only)
	 * @param integer $folder_id
	 * @return bool
	 */
	public static function is_case($folder_id);
	
	/**
	 * Returns the instance (Singleton)
	 * @return object
	 */
	public static function get_instance();
}
?>
