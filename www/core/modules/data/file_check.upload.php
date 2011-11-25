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
  * This file checks running uploads
  */

	/**
	 * @ignore
	 */
	define("UNIT_TEST", false);

	require_once("../../../config/version.php");
	require_once("../../../config/main.php");
	require_once("../../db/db.php");
	
	require_once("../../include/base/system/transaction.class.php");
	
	require_once("../../include/base/system/events/event.class.php");
	require_once("../../include/base/system/system_handler.class.php");

	require_once("../../include/base/security/session.class.php");
	
	$GLOBALS['autoload_prefix'] = "../../../";

	require_once("../../include/base/system/autoload.function.php");

	$db = new Database(constant("DB_TYPE"));
	$db->db_connect(constant("DB_SERVER"),constant("DB_PORT"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_DATABASE"));

	SystemHandler::init_db_constants();
	
	$user = new User(1);

	if ($_GET[session_id] and $_GET[unique_id])
	{
		$session = new Session($_GET[session_id]);
		$file_upload_status = $session->read_value("FILE_UPLOAD_".$_GET[unique_id]);
		
	
		if (is_array($file_upload_status) and count($file_upload_status) > 0)
		{
			if ($session->is_value("FILE_UPLOAD_FINISHED_".$_GET[unique_id]) == true)
			{
				// $session->write_value("FILE_UPLOAD_FINISHED_".$_GET[unique_id], false, true);
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
