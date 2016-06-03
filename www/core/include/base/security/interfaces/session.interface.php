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
 * Session Management Interface
 * @package base
 */
interface SessionInterface
{
	/**
	 * @param string $session_id
	 */
	function __construct($session_id);
	
	function __destruct();
	
	/**
     * Creates a new session
     * @param integer $user_id
     * @return string
     */
	public function create($user_id);
	
	/**
     * Destroys current session
     * @return bool
     */
	public function destroy();
	
	/**
     * Checks if the current session is valid
     * @return array
     */
	public function is_valid();
	
	/**
     * Checks if the current session is already alife
     * @return bool
     */
	public function is_dead();
	
	/**
     * @return integer
     */
	public function get_user_id();
	
	/**
     * @return integer
     */
	public function get_session_id();
	
	/**
     * Reads a value from current session
     * @param string $address
     * @return mixed
     */
	public function read_value($address);
	
	/**
     * Writes a value into current session
     * @param string $address
     * @param mixed $value
     * @param bool $force_overwrite (if false, existing values will not be overwritten)
     * @return bool
     */
	public function write_value($address, $value, $force_overwrite);
	
	/**
     * Checks if a value exists in current session
     * @param string $address
     * @return bool
     */
	public function is_value($address);
	
	/**
     * Deletes a value from current session
     * @param string $address
     * @return bool
     */
	public function delete_value($address);
	
	
	/**
     * Checks all existing sessions; destroys them, if invalid
     */
	public static function check_all();
	
	/**
     * @param string $session_id
     * @return array
     * Returns an array with all session data
     */
    public static function list_all_session_values($session_id);
	
	/**
     * Deletes all sessions of an user
     * @param integer $user_id
     * @return bool
     */
	public static function delete_user_sessions($user_id);
}
?>