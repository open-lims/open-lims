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
 */

	/**
	 * @ignore
	 */
	define("UNIT_TEST", false);

	require_once("config/version.php");
	require_once("core/include/base/system/system_config.class.php");

 	SystemConfig::load_system_config("config/main.php");
 	SystemConfig::load_module_config();
 	
	require_once("core/db/db.php");
	
	require_once("core/include/base/system/transaction.class.php");
	require_once("core/include/base/system/events/event.class.php");
	require_once("core/include/base/system/system_handler.class.php");
	
	require_once("core/include/base/security/security.class.php");
	require_once("core/include/base/security/session.class.php");

	require_once("core/include/base/system/autoload.function.php");	
	
	if ($_GET[session_id] and $_GET[file_id])
	{
		global $db;
		
		$database = SystemConfig::get_database();
		
		$db = new Database($database['type']);
		$db->db_connect($database[0]['server'],$database[0]['port'],$database['user'],$database['password'],$database['database']);
			
		Security::protect_session();
		
		$transaction = new Transaction();
		
		try
		{
			$system_handler = new SystemHandler(false);
		}
		catch(Exception $e)
		{
			die("Exception");
		}
		
		$session = new Session($_GET[session_id]);
		$user = new User($session->get_user_id());
		
		if ($session->is_valid() == true)
		{
			$image_cache = new ImageCache($_GET[file_id]);
			$file_path = constant("BASE_DIR")."/filesystem/temp/".$image_cache->get_image(700);
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
