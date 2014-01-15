<?php
/**
 * @package base
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
 * 
 */
require_once("interfaces/system.interface.php");

/**
 * System Class
 * Class handles information about system
 * @package base
 */
class System implements SystemInterface
{
    /**
     * @see SystemInterface::get_base_directory()
     * @return string
     */
    public static function get_base_directory()
    {
    	return constant("BASE_DIR");
    }
    
    /**
     * @see SystemInterface::get_system_space()
     * @return integer
     */
    public static function get_system_space()
    {
    	return disk_total_space(constant("BASE_DIR"));
    }
    
    /**
     * @see SystemInterface::get_used_database_space()
     * @return integer
     */
    public static function get_used_database_space()
    {
    	global $db;
    	$res = $db->db_database_size();
    	return $res['size'];
    }
    
    /**
     * @see SystemInterface::get_free_space()
     * @return integer
     */
    public static function get_free_space()
    {
    	return disk_free_space(constant("BASE_DIR"));
    }
}
?>