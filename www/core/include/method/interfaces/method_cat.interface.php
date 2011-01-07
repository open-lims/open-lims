<?php
/**
 * @package method
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
 * Method Category Management Interface
 * @package method
 */ 		 
interface MethodCatInterface
{
	function __construct($method_cat_id);
	function __destruct();
	
	public function create($toid, $name);
	public function delete();
	public function get_name();
	public function set_name($name);
	public function get_childs();
	
	public static function exist_id($id);
	public static function exist_name($name);
	public static function list_root_entries();
	public static function list_entries();
}

?>
