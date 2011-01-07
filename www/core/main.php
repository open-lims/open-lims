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
 * Main Class
 * @package base
 */
class Main
{
	
	/**
	 * Checks basic requirements, includes basic files, creates global classes and starts Database-Connection
	 */
	function __construct()
	{
		if (version_compare(PHP_VERSION, '5.2.0', 'le'))
		{
    		die('PHP 5.2.0 is minimum required');
		}
		
		if (!extension_loaded("imagick"))
		{
			die('Extension "Imagick" is missing!');
		}
		
		if (!extension_loaded("mbstring"))
		{
			die('Extension "mbstring" is missing!');
		}
		
		if (!extension_loaded("gd"))
		{
			die('Extension "GD" is missing!');
		}

		global $db, $misc, $runtime_data, $transaction;
		
		require_once("core/db/db.php");
		
		$db = new Database("postgresql");
		@$GLOBALS[con_run] = $db->db_connect($GLOBALS[server],$GLOBALS[port],$GLOBALS[dbuser],$GLOBALS[password],$GLOBALS[database]);
				
		require_once("include/base/error_handler.php");
		
		set_error_handler('error_handler');
		
		require_once("include/base/autoload.function.php");
		
		if ($GLOBALS[con_run] == true)
		{
			require_once("include/base/transaction.class.php");
			
			$transaction = new Transaction();
			
			require_once("include/base/security.class.php");
			require_once("include/base/system_log.class.php");
			require_once("include/base/misc.class.php");
			require_once("include/base/session.class.php");
			require_once("include/base/runtime_data.class.php");

			require_once("include/base/interfaces/event_listener.interface.php");
			
			Security::protect_session();
	
			$misc = new Misc();
			
			$runtime_data = new RuntimeData();
		}

	}
	
	/**
	 * Closes Database Connection
	 */
	function __destruct()
	{
		@$db->db_close();
	}
		
	/**
	 * Initalisation of Controller
	 */
	public function init()
	{
		global $session, $user, $common;
		
		if ($GLOBALS[con_run] == true)
		{
			if ($_GET[session_id])
			{
				$session = new Session($_GET[session_id]);
				$user = new User($session->get_user_id());
			}
			else
			{
				$session = new Session(null);
				$user = null;
			}
		}
		
		require_once("modules/content_handler.io.php");
		require_once("modules/base/common.io.php");
		require_once("modules/base/error.io.php");
		require_once("modules/base/table.io.php");
		require_once("modules/base/list.io.php");
		
		$common = new Common_IO;

		ContentHandler_IO::main();
	}	
	
}

?>
