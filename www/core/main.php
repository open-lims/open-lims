<?php
/**
 * @package base
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
 * Main Class
 * @package base
 */
class Main
{
	private $type;
	
	/**
	 * Checks basic requirements, includes basic files, creates global classes and starts Database-Connection
	 */
	function __construct($type = "io")
	{
		$this->type = $type;
		
		if (version_compare(PHP_VERSION, '5.3.0', 'le'))
		{
    		$GLOBALS['fatal_error'] = "PHP 5.3.0 is minimum required!";
		}
		
		if (!extension_loaded("imagick"))
		{
			$GLOBALS['fatal_error'] = "Extension \"Imagick\" is missing!";
		}
		
		if (!extension_loaded("mbstring"))
		{
			$GLOBALS['fatal_error'] = "Extension \"mbstring\" is missing!";
		}
		
		if (!extension_loaded("gd"))
		{
			$GLOBALS['fatal_error'] = "Extension \"GD\" is missing!";
		}
		
		if ($GLOBALS['fatal_error'] == null)
		{
			global $db, $runtime_data, $transaction;
			
			require_once("core/db/db.php");
			
			$db = new Database(constant("DB_TYPE"));
			@$connection_result = $db->db_connect(constant("DB_SERVER"),constant("DB_PORT"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_DATABASE"));
					
			require_once("include/base/system/error_handler.php");
			
			set_error_handler('error_handler');
			
			require_once("include/base/system/events/event.class.php");
			require_once("include/base/system/system_handler.class.php");
			require_once("include/base/system/system_config.class.php");
			
			SystemConfig::load_module_config();
			
			require_once("include/base/system/autoload.function.php");
			
			if ($connection_result == true)
			{
				require_once("include/base/system/transaction.class.php");
				
				$transaction = new Transaction();
				
				require_once("include/base/security/security.class.php");
				require_once("include/base/security/session.class.php");
				
				require_once("include/base/system/runtime_data.class.php");
				
				require_once("include/base/system_fe/system_log.class.php");

				Security::protect_session();
				
				$runtime_data = new RuntimeData();
				
				try
				{
					if ($type == "io")
					{
						$system_handler = new SystemHandler(true);
					}
					else
					{
						$system_handler = new SystemHandler(false);
					}
				}
				catch(IncludeDataCorruptException $e)
				{
					$GLOBALS['fatal_error'] = "The config-data of a module is corrupt!";
				}
				catch(IncludeProcessFailedException $e)
				{
					$GLOBALS['fatal_error'] = "Include register process failed!";
				}
				catch(IncludeRequirementFailedException $e)
				{
					$GLOBALS['fatal_error'] = "An include-module requirement is not found!";
				}
				catch(IncludeFolderEmptyException $e)
				{
					$GLOBALS['fatal_error'] = "Include folder is empty!";
				}
				catch(ModuleProcessFailedException $e)
				{
					$GLOBALS['fatal_error'] = "Module register process failed!";
				}
				catch(ModuleDataCorruptException $e)
				{
					$GLOBALS['fatal_error'] = "Module Data Corrupt!";
				}
				catch(EventHandlerCreationFailedException $e)
				{
					$GLOBALS['fatal_error'] = "Event-handler creation failed!";
				}
			}
			else
			{
				$GLOBALS['fatal_error'] = "Database-Connection failed!";
			}
		}
	}
	
	/**
	 * Closes Database Connection
	 */
	function __destruct()
	{
		global $db;
		
		if (@is_object($db))
		{
			@$db->db_close();
		}
	}
		
	/**
	 * Initalisation of IO Controller
	 */
	public function init()
	{
		global $session, $user;
		
		if ($GLOBALS['fatal_error'] == null)
		{
			if ($_GET[session_id])
			{
				try 
				{
					$session = new Session($_GET[session_id]);
					$user = new User($session->get_user_id());
				}
				catch (UserException $e)
				{
					$GLOBALS['fatal_error'] = "User initialisation failed!";
				}
			}
			else
			{
				$session = new Session(null);
				$user = null;
			}
		}
		
		require_once("modules/content_handler.php");
		require_once("modules/base/common/io/common.io.php");
		require_once("modules/base/common/io/error.io.php");
		require_once("modules/base/common/io/list.io.php");
		
		if ($this->type == "io")
		{
			require_once("modules/base/common/io/tab.io.php");
		
			/**
			 * @deprecated remove later
			 */
			require_once("modules/base/common/io/list_stat.io.php");
			
			ContentHandler_IO::io();
		}
		elseif ($this->type == "ajax")
		{
			require_once("modules/base/common/io/list_request.io.php");
			ContentHandler_IO::ajax();
		}
	}
}

?>
