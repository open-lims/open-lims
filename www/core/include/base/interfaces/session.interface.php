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
 * Session Management Interface
 * @package base
 */
interface SessionInterface
{
	function __construct($session_id);
	function __destruct();
	
	public function create($user_id);
	public function destroy();
	public function is_valid();
	public function get_user_id();
	
	public function read_value($address);
	public function write_value($address, $value, $force_overwrite);
	public function is_value($address);
	public function delete_value($address);
	
	public static function check_all();
	public static function delete_user_sessions($user_id);
}
?>