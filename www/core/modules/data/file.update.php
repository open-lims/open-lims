<?php
/**
 * @package data
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
 * 
 */
 
 /**
  * This file uploads a new version
  */
  
	// Disable PHP Timeout
	set_time_limit(0);

	/**
	 * @ignore
	 */
	define("UNIT_TEST", false);

	require_once("../../../config/version.php");
	require_once("../../include/base/system/system_config.class.php");

 	SystemConfig::load_system_config("../../../config/main.php");
 		
	require_once("../../db/db.php");
	
	$database = SystemConfig::get_database();
	
	$db = new Database($database['type']);
	$db->db_connect($database[0]['server'],$database[0]['port'],$database['user'],$database['password'],$database['database']);
	
	require_once("../../include/base/system/transaction.class.php");
	
	require_once("../../include/base/system/events/event.class.php");
	require_once("../../include/base/system/system_handler.class.php");

	require_once("../../include/base/security/security.class.php");
	require_once("../../include/base/security/session.class.php");
	
	$GLOBALS['autoload_prefix'] = "../../../";

	require_once("../../include/base/system/autoload.function.php");

	SystemHandler::init_db_constants();
	
	SystemConfig::load_module_config();
	
	Security::protect_session();

	if ($_GET[session_id] and $_FILES)
	{
		global $db, $user, $session, $transaction;
			
		$session = new Session($_GET[session_id]);
		$user = new User($session->get_user_id());
		$transaction = new Transaction();
		
		if ($session->is_valid() == true)
		{ 
			$session_file_array = array();
			$session_file_array[1] = 0;
			$session->write_value("FILE_UPLOAD_".$_GET[unique_id], $session_file_array, true);
			
			if ($_POST[current] == 1)
			{
				$current = true;
			}
			else
			{
				$current = false;
			}
			
			if ($_GET[action] == "file_update")
			{
				$major = true;
			}
			else
			{
				$major = false;
			}
			
			if ($_GET[version])
			{
				$previous_version_id = $_GET[version];
			}
			else
			{
				$previous_version_id = null;
			}
			
			if (!empty($_FILES['file-1']['name']))
			{				
				$file = File::get_instance($_GET[file_id]);
				$session_file_array[1] = $file->update_file($_FILES['file-1'], $previous_version_id, $major, $current);
				$session->write_value("FILE_UPLOAD_".$_GET[unique_id], $session_file_array, true);
				$session->write_value("FILE_UPLOAD_FINISHED_".$_GET[unique_id], true, true);
			}
			else
			{
				$session_file_array[1] = 1;
				$session->write_value("FILE_UPLOAD_".$_GET[unique_id], $session_file_array, true);
				$session->write_value("FILE_UPLOAD_FINISHED_".$_GET[unique_id], true, true);
			}
		}
	}

?>
