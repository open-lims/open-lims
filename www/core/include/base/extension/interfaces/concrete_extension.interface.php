<?php
/**
 * @package extension
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
 * Concrete Extension Interface
 * Defined the interfaces of a Open-LIMS extension
 * The base class implements this interface and redeirects is to its logic classes
 * @package extension
 */
interface ConcreteExtensionInterface
{
	/**
	 * Returns the path to the icon of the extension
	 * @return string
	 */
	public static function get_icon();
	
	/**
	 * Return the description of the extension
	 * @return string
	 */
	public static function get_description();
	
	/**
	 * Set the data (item-array) of the extensions
	 * This is a content controller function
	 * @param array $data
	 */
	public static function push_data($data);
	
	/**
	 * Returns the status of a current run
	 * @param integer $run_id
	 * @return integer
	 */
	public static function get_data_status($run_id);
	
	/**
	 * Sets the target folder for the generated files or values
	 * @param integer $target_folder_id
	 */
	public static function set_target_folder_id($target_folder_id);
	
	/**
	 * Sets an unique identifier which will be retured by the create-event to identify the extension run
	 * @param string $event_identifier
	 */
	public static function set_event_identifier($event_identifier);
	
	/**
	 * Sets called events which will be listend by the extension system to deal with it
	 * e.g. deleted files
	 * @param object $event
	 */
	public static function listen_events($event);
	
	/**
	 * main content controller
	 */
	public static function main();
	
	/**
	 * AJAX controller
	 */
	public static function ajax();
}
?>