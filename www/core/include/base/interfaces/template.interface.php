<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * Template Engine Interface
 * @package base
 */
interface TemplateInterface
{
	function __construct($file);
	function __destruct();
	
	public function set_var($name, $value);
	public function output();
	public function get_string();
	
	// private function call_control_structures();
	// private function call_foreach();
	
	// private function replace_foreach($string);
	// private function replace_control_structure($string);
	// private function replace_containers();
	
	// private function fill_string();
	
	// private function get_var_cardinality($var);
	// private function get_var_value($var);
}
?>