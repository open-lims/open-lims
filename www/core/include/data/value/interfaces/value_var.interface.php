<?php
/**
 * @package data
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
 * @todo
 * Value Var Interface
 * @package data
 */
interface ValueVarInterface
{
	/**
     * @param integer $folder_id
     */
	function __construct($folder_id);
	
	function __destruct();
	
	/**
     * @param string $string
     * @return mixed
     */
    public function get_content($string);
    
     /**
     * @param string $name
     * @param stirng $handling_class
     * @param bool $ignore_this
     * @param integer $include_id
     * @return bool
     */
	public static function register_type($name, $handling_class, $ignore_this, $include_id);
	
	/**
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_by_include_id($include_id);
}
?>
