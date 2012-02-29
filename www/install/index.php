<?php
/**
 * @package base
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
  * 
  */
set_time_limit(0);
$GLOBALS['autoload_prefix'] = "../";

header("Content-Type: text/html; charset=utf-8");

define("UNIT_TEST", false);
define("DEBUG", false);

require_once("hosts.config.php");
require_once("../config/version.php");
require_once("../core/include/base/system/system_config.class.php");

SystemConfig::load_system_config("../config/main.php");
 
if (version_compare(PHP_VERSION, '5.3.0', 'le'))
{
    die("PHP 5.3.0 is minimum required!");
}

if (!extension_loaded("imagick"))
{
	die("Extension \"Imagick\" is missing!");
}

if (!extension_loaded("mbstring"))
{
	die("Extension \"mbstring\" is missing!");
}

if (!extension_loaded("gd"))
{
	die("Extension \"GD\" is missing!");
}

global $db, $runtime_data, $transaction;

require_once("../core/db/db.php");

$database = SystemConfig::get_database();

$db = new Database($database['type']);
@$connection_result = $db->db_connect($database[0]['server'],$database[0]['port'],$database['user'],$database['password'],$database['database']);
if ($connection_result === false)
{
	die ("Database-Connection failed. Insert config-values first or check config/main.php");
}
else
{
	$ip = $_SERVER["REMOTE_ADDR"];
	
	if (is_array($hosts) and in_array($ip, $hosts) == true)
	{
		require_once("../core/include/base/system/events/event.class.php");
		require_once("../core/include/base/system/system_handler.class.php");
		require_once("../core/include/base/system/transaction.class.php");
		require_once("../core/include/base/security/security.class.php");
		
		require_once("../core/include/base/system/template.class.php");
 		require_once("../core/include/base/system/html_template.class.php");
				
		$transaction = new Transaction();
		
		Security::protect_session(true);
		
		$template = new HTMLTemplate("header.html", "install/template");
		$template->output();
		
		try
		{
			$sql = "SELECT id FROM core_base_includes";
			$res = @$db->db_query($sql);
			
			require_once("classes/install.io.php");
			InstallIO::install();
		}
		catch(DatabaseQueryFailedException $e)
		{
			require_once("classes/install.io.php");
			InstallIO::install();
		}
	
		
		$template = new HTMLTemplate("footer.html", "install/template");
		$template->output();
	}
	else
	{
		die ("Access not allowed from your IP-Adress, check hosts.config.php");
	}
}

?>