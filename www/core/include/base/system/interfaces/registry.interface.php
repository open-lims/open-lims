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
 * Registry Interface
 * @package base
 */
interface RegistryInterface
{
	/**
	 * @param integer $id
	 */
	function __construct($id);
	
	function __destruct();
	
	/**
	 * @param string $name
	 * @param integer $include_id
	 * @param string $value
	 * @return integer
	 */
	public function create($name, $include_id, $value);
	
	/**
	 * @return bool
	 */
	public function delete();
	
	
	/**
	 * @param string $name
	 * @return string
	 */
	public static function is_value($name);
	
	/**
	 * @param string $name
	 * @return string
	 */
	public static function get_value($name);
	
	/**
	 * @param string $name
	 * @param string $value
	 * @return bool
	 */
	public static function set_value($name, $value);
}
?>