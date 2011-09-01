<?php
/**
 * @package data
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
 * 
 */
 
 /**
  * This file uploads project data
  */

	// Disable PHP Timeout
	set_time_limit(0);
	/**
	 * @ignore
	 */
	define("UNIT_TEST", false);

	require_once("../../../config/version.php");
	require_once("../../../config/main.php");
	require_once("../../db/db.php");
	
	require_once("../../include/base/transaction.class.php");
	
	require_once("../../include/base/events/event.class.php");
	require_once("../../include/base/system_handler.class.php");
	
	require_once("../../include/base/session.class.php");

	$GLOBALS['autoload_prefix'] = "../../../";

	require_once("../../include/base/autoload.function.php");

	if ($_GET[username] and $_GET[session_id] and $_FILES)
	{
		global $db, $user, $session, $transaction;
	
		$db = new Database(constant("DB_TYPE"));
		$db->db_connect(constant("DB_SERVER"),constant("DB_PORT"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_DATABASE"));
		
		SystemHandler::init_db_constants();
		
		$session = new Session($_GET[session_id]);
		$user = new User($session->get_user_id());
		$transaction = new Transaction();
		
		if ($session->is_valid() == true)
		{ 
			$folder_id = $_GET[folder_id];
			
			if ($_POST[file_amount] > 25 or $_POST[file_amount] < 1 or !$_POST[file_amount])
			{				
				$file_amount = 1;		
			}
			else
			{	
				$file_amount = $_POST[file_amount];		
			}	
	
			
			$file = new File(null);
			$file_upload_successful = $file->upload_file_stack($file_amount, $folder_id, $_FILES, $_GET[unique_id]);
	
			if ($file_upload_successful == true)
			{
				// Create Item
				$item_id_array = $file->get_item_id_array();
				
				if(is_array($item_id_array) and count($item_id_array) >= 1)
				{
					foreach($item_id_array as $key => $value)
					{
						$item_add_event = new ItemAddEvent($value, $_GET, $_POST);
						$event_handler = new EventHandler($item_add_event);
					}
				}
				$session->write_value("FILE_UPLOAD_FINISHED_".$_GET[unique_id], true, true);
			}
			else
			{
				
			}
		}
	}

?>

