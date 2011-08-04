<html>
<head>
<script type="text/javascript" src="../../../js/phpjs/phpjs.js"></script>
<script type="text/javascript" src="../../../js/yajsl_php.js"></script>
<script type="text/javascript" src="../../../js/yajsl.js"></script>
<script type="text/javascript" src="../../../js/ol_core.js"></script>
</head>
<body>
<script language='javascript'>
	if (parent.uploader.getUploadReload() == true)
	{
		local_uploader = new Uploader();
		function atload()
		{
			location.reload(true);
		}
		window.onload=atload;
	}
</script>
<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz
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

	require_once("../../../config/main.php");
	require_once("../../db/db.php");
	
	require_once("../../include/base/transaction.class.php");
	
	require_once("../../include/base/events/event.class.php");
	require_once("../../include/base/system_handler.class.php");

	require_once("../../include/base/session.class.php");
	
	$GLOBALS['autoload_prefix'] = "../../../";

	require_once("../../include/base/autoload.function.php");

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
			if ($session->is_value("FILE_UPLOAD_FINISHED_".$_GET[unique_id]) == false)
			{
				// $session->write_value("FILE_UPLOAD_FINISHED_".$_GET[unique_id], false, true);
			}
			
			$upload_error_array = array();
			
			$number_of_complete_uploads = 0;
			$number_of_total_uploads = count($file_upload_status);
			
			$upload_complete = true;
			$upload_error = false;
			
			foreach ($file_upload_status as $key => $value)
			{	
				if ($value == 0)
				{
					$upload_complete = false;
				}
				else
				{
					$number_of_complete_uploads++;
				}
				
				if ($value > 1)
				{
					$upload_error = true;
					$upload_error_array[$key] = $value;
				}
				
			}
			
			if ($upload_complete == false)
			{
				echo "<script language='javascript'>" .
						"parent.uploader.setNumberOfUploads(".$number_of_total_uploads.",".$number_of_complete_uploads.");" .
						"</script>";
			}
			else
			{
				if ($upload_error == true)
				{
					echo "<script language='javascript'>";
					echo "parent.uploader.stop(".$number_of_total_uploads.");";
					
					if ($_GET[run] == "update" or $_GET[run] == "update_minor")
					{
						$type = "update";
					}
					else
					{
						$type = "upload";
					}
					
					if (is_array($upload_error_array))
					{
						foreach ($upload_error_array as $key => $value)
						{
							echo "parent.uploader.enableField(".$key.");";
							echo "parent.uploader.error(".$key.", ".$value.", '".$type."');";
						}
					}
					echo "</script>";
				}
				else
				{
					echo "<script language='javascript'>" .
						"parent.uploader.setNumberOfUploads(".$number_of_total_uploads.",".$number_of_complete_uploads.");" .						
						"</script>";	
					
					if ($session->read_value("FILE_UPLOAD_FINISHED_".$_GET[unique_id]) == true)
					{
						echo "<script language='javascript'>" .
							"parent.uploader.proceed();" .
							"</script>";		
					}	
				}
			}
		}
		else
		{
			echo "No Array";
		}
	}
	$db->db_close();

?>
</body>
</html>
