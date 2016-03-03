<?php
/**
 * @package data
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
 * 
 */
 
 /**
  * This file checks running uploads
  */

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
	require_once("../../include/base/system/events/delete_event.class.php");
	require_once("../../include/base/system/system_handler.class.php");
	
	require_once("../../include/base/security/security.class.php");
	require_once("../../include/base/security/session.class.php");
	
	$GLOBALS['autoload_prefix'] = "../../../";

	new SystemHandler(false);
	
	require_once("../../include/base/system/autoload.function.php");
	
	SystemConfig::load_module_config();
	
	Security::protect_session();
	
	$user = new User(1);

	if ($_GET['session_id'] and $_GET['unique_id'])
	{
		$session = new Session($_GET['session_id']);
		$file_upload_status = $session->read_value("FILE_UPLOAD_".$_GET['unique_id']);
		
	
		if (is_array($file_upload_status) and count($file_upload_status) > 0)
		{
			if ($session->is_value("FILE_UPLOAD_FINISHED_".$_GET['unique_id']) == true)
			{
				echo "ALL_OK";
				echo json_encode($file_upload_status);
			}
			else
			{
				echo json_encode($file_upload_status);
			}
		}
		else
		{
			echo "No Array";
		}
	}
	$db->db_close();

?>
