<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
	global $db;

	define("UNIT_TEST", false);

	require_once("config/version.php");
	require_once("core/include/base/system/system_config.class.php");

 	SystemConfig::load_system_config("config/main.php");

 	date_default_timezone_set($server['timezone']);
 	
	require_once("core/db/db.php");

	$database = SystemConfig::get_database();
		
	$db = new Database($database['type']);
	$db->db_connect($database[0]['server'],$database[0]['port'],$database['user'],$database['password'],$database['database']);

	require_once("core/include/base/system/transaction.class.php");
	require_once("core/include/base/system/events/event.class.php");
	require_once("core/include/base/system/events/delete_event.class.php");
	require_once("core/include/base/system/system_handler.class.php");
	
	require_once("core/include/base/security/security.class.php");
	require_once("core/include/base/security/session.class.php");

	require_once("core/include/base/system/autoload.function.php");	

	SystemConfig::load_module_config();
	
	if ($_GET['session_id'] and $_GET['file_id'])
	{	
		$transaction = new Transaction();
		
		try
		{
			$system_handler = new SystemHandler();
		}
		catch(BaseIncludeDataCorruptException $e)
		{
			die("The config-ata of a module is corrupt!");
		}
		catch(BaseIncludeProcessFailedException $e)
		{
			die("Include register process failed!");
		}
		catch(BaseIncludeRequirementFailedException $e)
		{
			die("An include-module requirement is not found!");
		}
		catch(BaseIncludeFolderEmptyException $e)
		{
			die("Include folder is empty!");
		}
		catch(BaseModuleProcessFailedException $e)
		{
			die("Module register process failed!");
		}
		catch(BaseModuleDataCorruptException $e)
		{
			die("Module Data Corrupt!");
		}
		catch(BaseEventHandlerCreationFailedException $e)
		{
			die("Event-handler creation failed!");
		}
		
		Security::protect_session();
		
		$session = new Session($_GET['session_id']);
		$user = new User($session->get_user_id());
		
		$session_valid_array = $session->is_valid();
		if ($session_valid_array[0] === true)
		{
			$file = File::get_instance($_GET['file_id']);
			
			if ($_GET['version'])
			{
				$file->open_internal_revision($_GET['version']);
			}
			
			if ($file->is_read_access() == true)
			{
				$folder = Folder::get_instance($file->get_parent_folder_id());
				$folder_path = $folder->get_path();
				
				$extension_array = explode(".",$file->get_name());
				$extension_array_length = substr_count($file->get_name(),".");
				
				if (!$_GET['version'])
				{
					$file_path = constant("BASE_DIR")."/".$folder_path."/".$file->get_data_entity_id()."-".$file->get_internal_revision().".".$extension_array[$extension_array_length];
				}
				else
				{
					$file_path = constant("BASE_DIR")."/".$folder_path."/".$file->get_data_entity_id()."-".$_GET['version'].".".$extension_array[$extension_array_length];
				}
				
				header("Content-Type: application/octet-stream");

				header("Content-Disposition: attachment; filename=\"".$file->get_name()."\"");
				
				readfile($file_path);
			}
			else
			{
				echo "Access Denied: You does not have access on this file!";
			}
		}
		else
		{
			echo "Access Denied: Your session is outdated!";
		}
	}
	else
	{
		echo "Access Denied: A GET Variable is missing!";
	}

?>
