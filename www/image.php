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
 */

	/**
	 * @ignore
	 */
	define("UNIT_TEST", false);

	require_once("config/version.php");
	require_once("config/main.php");
	require_once("core/db/db.php");
	
	require_once("core/include/base/system/transaction.class.php");
	require_once("core/include/base/system/events/event.class.php");
	require_once("core/include/base/system/system_handler.class.php");
	
	require_once("core/include/base/security/session.class.php");

	require_once("core/include/base/system/autoload.function.php");	
		
	if ($_GET[session_id] and $_GET[file_id])
	{
		$db = new Database(constant("DB_TYPE"));
		$db->db_connect(constant("DB_SERVER"),constant("DB_PORT"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_DATABASE"));
		
		$transaction = new Transaction();
		
		try
		{
			$system_handler = new SystemHandler();
		}
		catch(IncludeDataCorruptException $e)
		{
			die("The config-ata of a module is corrupt!");
		}
		catch(IncludeProcessFailedException $e)
		{
			die("Include register process failed!");
		}
		catch(IncludeRequirementFailedException $e)
		{
			die("An include-module requirement is not found!");
		}
		catch(IncludeFolderEmptyException $e)
		{
			die("Include folder is empty!");
		}
		catch(ModuleProcessFailedException $e)
		{
			die("Module register process failed!");
		}
		catch(ModuleDataCorruptException $e)
		{
			die("Module Data Corrupt!");
		}
		catch(EventHandlerCreationFailedException $e)
		{
			die("Event-handler creation failed!");
		}
		
		$session = new Session($_GET[session_id]);
		$user = new User($session->get_user_id());
		
		if ($session->is_valid() == true)
		{
			$file = File::get_instance($_GET[file_id]);
			
			if ($_GET[version])
			{
				$file->open_internal_revision($_GET[version]);
			}
			
			if ($file->is_read_access() == true)
			{
				$folder = Folder::get_instance($file->get_parent_folder_id());
				$folder_path = $folder->get_path();
				
				$extension_array = explode(".",$file->get_name());
				$extension_array_length = substr_count($file->get_name(),".");
				

				$file_path = constant("BASE_DIR")."/".$folder_path."/".$file->get_data_entity_id()."-".$file->get_internal_revision().".".$extension_array[$extension_array_length];
				if (!file_exists($file_path))
				{
					$file_path = constant("WWW_DIR")."/images/access.jpg";
				}
			}
			else
			{
				$file_path = constant("WWW_DIR")."/images/access.jpg";
			}
		}
		else
		{
			$file_path = constant("WWW_DIR")."/images/access.jpg";
		}
	}
	else
	{
		$file_path = constant("WWW_DIR")."/images/access.jpg";
	}

	$image = new Imagick($file_path);
	
	if ($image->getImageFormat() != "PNG")
	{
		$image->setImageFormat("jpg");	
	}

	if (!$_GET[full] and !$_GET[thumb])
	{
		$width = $image->getImageWidth();
		if ($width > 730)
		{
			$image->thumbnailImage(730,0);
		}
		
		$height = $image->getImageHeight();
		if ($height > 580)
		{
			$image->thumbnailImage(0,580);
		}
	}
	
	if ($_GET[multithumb])
	{
		$width = $image->getImageWidth();
		if ($width > 170)
		{
			$image->thumbnailImage(170,0);
		}
		
		$height = $image->getImageHeight();
		if ($height > 170)
		{
			$image->thumbnailImage(0,170);
		}
	}
	
	if ($_GET[thumb])
	{
		$width = $image->getImageWidth();
		if ($width > 400)
		{
			$image->thumbnailImage(400,0);
		}
		
		$height = $image->getImageHeight();
		if ($height > 250)
		{
			$image->thumbnailImage(0,250);
		}
	}

	header("Content-Type: image/jpeg");
	 
	echo $image;
	
?>
