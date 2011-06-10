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
 * System Handler Interface
 * @package base
 */
interface SystemHandlerInterface
{
	function __construct();
	
	public static function init_db_constants();
	
	/**
	 * @return array;
	 */
	public static function get_include_folders();
	
	/**
	 * @return array
	 */
	public static function get_module_folders();
	
	/**
	 * @param integer $module_id
	 * @return string
	 */
	public static function get_module_name_by_module_id($module_id);
	
	/**
	 * @param string $module_name
	 * @return string
	 */
	public static function get_module_folder_by_module_name($module_name);
		
	/**
	 * @return array
	 */
	public static function list_modules();
	
	/**
	 * @return array
	 */
	public static function list_includes();
	
	/**
	 * @param integer $module_id
	 * @return bool
	 */
	public static function disable_module($module_id);
}
?>