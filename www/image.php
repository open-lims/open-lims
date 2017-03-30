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
 */

	/**
	 * @ignore
	 */
	global $db;
	
	define("UNIT_TEST", false);

	require_once("config/version.php");
	require_once("core/include/base/system/system_config.class.php");

 	SystemConfig::load_system_config("config/main.php");
 	
 	date_default_timezone_set(constant("TIMEZONE"));
 	
	require_once("core/db/db.php");
		
	$database = SystemConfig::get_database();
	
	$db = new Database();
	$db->connect($database['type'],$database[0]['server'],$database[0]['port'],$database['user'],$database['password'],$database['database']);
	
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
			$system_handler = new SystemHandler(false);
		}
		catch(Exception $e)
		{
			die("Exception");
		}
		
		Security::protect_session();
		
		$session = new Session($_GET['session_id']);
		$user = new User($session->get_user_id());
		
		$session_valid_array = $session->is_valid();
		if ($session_valid_array[0] === true)
		{
			try
			{
				$image_cache = new ImageCache($_GET['file_id']);
			}
			catch(Exception $e)
			{
				die("Exception");
			}
			
			if ($_GET['max_width'])
			{
				$image_cache->set_max_width($_GET['max_width']);
			}
			
			if ($_GET['max_height'])
			{
				$image_cache->set_max_height($_GET['max_height']);
			}
			
			if ($_GET['width'])
			{
				$file_path = constant("BASE_DIR")."/filesystem/temp/".$image_cache->get_image($_GET['width']);
			}
			elseif($_GET['height'])
			{
				$file_path = constant("BASE_DIR")."/filesystem/temp/".$image_cache->get_image(null, $_GET['height']);
			}
			else
			{
				$file_path = constant("BASE_DIR")."/filesystem/temp/".$image_cache->get_image();
			}
			
			if (!$file_path)
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
		header("Content-Type: image/jpeg");	
	}
	else
	{
		header("Content-Type: image/png");
	}
	
	 
	echo $image;
	
?>
