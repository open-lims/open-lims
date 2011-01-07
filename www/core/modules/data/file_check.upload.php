<html>
<head>
<script type="text/javascript" src="../../../js/yajsl_php.js"></script>
<script type="text/javascript" src="../../../js/yajsl.js"></script>
<script type="text/javascript" src="../../../js/ol_core.js"></script>
</head>
<body>

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
  * This file checks running uploads
  */

	/**
	 * @ignore
	 */
	define("UNIT_TEST", false);

	require_once("../../../config/main.php");
	require_once("../../db/db.php");
	
	require_once("../../include/base/session.class.php");
	
	$GLOBALS[autoload_prefix] = "../../../";

	require_once("../../include/base/autoload.function.php");

	$db = new Database("postgresql");
	$db->db_connect($GLOBALS[server],$GLOBALS[port],$GLOBALS[dbuser],$GLOBALS[password],$GLOBALS[database]);

	$user = new User(1);

	if ($_GET[session_id] and $_GET[unique_id])
	{
		echo "<script language='javascript'>" .
				"uploader = new Uploader();" .
				"if (top.upload_reload == true) {
					uploader.reload();
				}" .
				"</script>";
	
		$session = new Session($_GET[session_id]);
		$file_upload_status = $session->read_value("FILE_UPLOAD_".$_GET[unique_id]);
	
		if (is_array($file_upload_status) and count($file_upload_status) > 0)
		{
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
						"uploader.setNumberOfUploads(".$number_of_total_uploads.",".$number_of_complete_uploads.");" .
						"</script>";		
			}
			else
			{
				if ($upload_error == true)
				{
					echo "<script language='javascript'>";
					echo "uploader.stop(".$number_of_total_uploads.");";
					
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
							echo "uploader.enableField(".$key.");";
							echo "uploader.error(".$key.", ".$value.", '".$type."');";
						}
					}
					echo "</script>";
				}
				else
				{
					if ($_GET[nav] == "file" and $_GET[run] == "add_to_project")
					{
						$proceed_target = "project";
					}
					elseif ($_GET[nav] == "file" and $_GET[run] == "add_to_sample")
					{
						$proceed_target = "sample";
					}
					else
					{
						if ($_GET[run] == "update" or $_GET[run] == "update_minor")
						{
							$proceed_target = "file_detail";
						}
						else
						{
							$proceed_target = "data_browser";
						}
					}

					echo "<script language='javascript'>" .
						"uploader.setNumberOfUploads(".$number_of_total_uploads.",".$number_of_complete_uploads.");" .						
						"uploader.proceed('".$proceed_target."');" .
						"</script>";					
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
