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
 * @ignore
 */
define("UNIT_TEST", false);

require_once("../../../config/main.php");
require_once("../../db/db.php");

require_once("../../include/user/group.class.php");	
require_once("../../include/user/user.class.php");

require_once("../../include/organisation_unit/organisation_unit.class.php");

require_once("../../include/base/session.class.php");

/**
 * AJAX Init Class
 * @package base
 */
class AJAXInit
{
	function __construct()
	{
		global $db, $user;

		$db = new Database("postgresql");
		$db->db_connect($GLOBALS[server],$GLOBALS[port],$GLOBALS[dbuser],$GLOBALS[password],$GLOBALS[database]);

		if ($_GET[session_id])
		{
			$session = new Session($_GET[session_id]);
			$user = new User($session->get_user_id());
		}
		else
		{
			$user = new User(1);
		}
	}
	
	function __destruct()
	{
		global $db;
		$db->db_close();
	}
}

?>
