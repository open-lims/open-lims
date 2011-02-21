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
 * 
 */
 
 /**
  * This file uploads data
  */
  
	// Disable PHP Timeout
	set_time_limit(0);
	
	/**
	 * @ignore
	 */
	define("UNIT_TEST", false);

	require_once("../../../config/main.php");
	require_once("../../db/db.php");
	
	require_once("../../include/base/transaction.class.php");

	require_once("../../include/base/events/event.class.php");
	require_once("../../include/base/system_handler.class.php");
		
	require_once("../../include/base/session.class.php");

	$GLOBALS[autoload_prefix] = "../../../";

	require_once("../../include/base/autoload.function.php");


	if ($_GET[username] and $_GET[session_id] and $_FILES)
	{
		global $db, $user, $session, $transaction;
	
		$db = new Database("postgresql");
		$db->db_connect($GLOBALS[server],$GLOBALS[port],$GLOBALS[dbuser],$GLOBALS[password],$GLOBALS[database]);
		
		SystemHandler::init_db_constants();
		
		$session = new Session($_GET[session_id]);
		$user = new User($session->get_user_id());
		$transaction = new Transaction();
		
		if ($session->is_valid() == true)
		{ 
			if ($_POST[file_amount] > 25 or $_POST[file_amount] < 1 or !$_POST[file_amount])
			{				
				$file_amount = 1;		
			}
			else
			{	
				$file_amount = $_POST[file_amount];		
			}
			$file = new File(null);
			$file->upload_file_stack($file_amount, $_GET[folder_id], $_FILES, $_GET[unique_id]);
		}
	}

?>
