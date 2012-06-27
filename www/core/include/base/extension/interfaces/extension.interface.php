<?php
/**
 * @package extension
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Concrete Extension Interface
 * Defined the interfaces of a Open-LIMS extension
 * The base class implements this interface and redeirects is to its logic classes
 * @package extension
 */
interface ExtensionInterface
{
	/**
	 * @param integer $extension_id
	 */
	function __construct($extension_id);
	
	/**
	 * @return string
	 */
	public function get_folder();
	
	/**
	 * @return string
	 */
	public function get_class();
	
	/**
	 * @return string
	 */
	public function get_main_file();
	
	/**
	 * @return string
	 */
	public function get_name();
	
	/**
	 * @param integer $run_id
	 * @return integer
	 */
	public function get_run_status($run_id);
	
	
	/**
	 * @param string $identifer
	 * @return integer
	 */
	public static function get_id_by_identifer($identifer);
}
?>