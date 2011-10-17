<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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

if ($GLOBALS['autoload_prefix'])
{
	$path_prefix = $GLOBALS['autoload_prefix'];
}
else
{
	$path_prefix = "";
}

require_once($path_prefix."../../../config/version.php");
require_once($path_prefix."../../../config/main.php");
require_once($path_prefix."../../db/db.php");

require_once($path_prefix."../../include/base/security/security.class.php");

global $db, $transaction;

$db = new Database(constant("DB_TYPE"));
$db->db_connect(constant("DB_SERVER"),constant("DB_PORT"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_DATABASE"));


$GLOBALS['autoload_prefix'] = $path_prefix."../../../";

require_once($path_prefix."../../include/base/system/template.class.php");
require_once($path_prefix."../../include/base/system/events/event.class.php");
require_once($path_prefix."../../include/base/system/system_handler.class.php");
require_once($path_prefix."../../include/base/system/autoload.function.php");
require_once($path_prefix."../../include/base/system/transaction.class.php");
				
$transaction = new Transaction();

require_once($path_prefix."../../include/base/user/group.class.php");	
require_once($path_prefix."../../include/base/user/user.class.php");

require_once($path_prefix."../../include/base/misc.class.php");

require_once($path_prefix."../../include/base/security/session.class.php");


require_once($path_prefix."../../modules/base/common.io.php");
require_once($path_prefix."../../modules/base/list.io.php");
require_once($path_prefix."../../modules/base/list_request.io.php");

/**
 * @deprecated remove later
 */
require_once($path_prefix."../../modules/base/list_stat.io.php");

SystemHandler::init_db_constants();

/**
 * AJAX Class
 * @package base
 */
class Ajax
{
	function __construct()
	{
		global $session, $user;

		Security::filter_var();
		
		if ($_GET[session_id])
		{
			$session = new Session($_GET[session_id]);
			$user = new User($session->get_user_id());
		}
		else
		{
			$session = new Session(null);
			$user = new User(1);
		}
	}
}

?>
