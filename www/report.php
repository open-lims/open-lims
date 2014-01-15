<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
	
	try
	{
		// External Libraries
		if (file_exists("libraries/tcpdf/config/lang/eng.php"))
		{
			require_once("libraries/tcpdf/config/lang/eng.php");
		}
		else
		{
			throw new BaseReportTCPDFLanguageFileMissingException();
		}
		
		if (file_exists("libraries/tcpdf/tcpdf.php"))
		{
			require_once("libraries/tcpdf/tcpdf.php");
		}
		else
		{
			throw new BaseReportTCPDFFileMissingException();
		}
		
		if ($_GET['session_id'])
		{	
			$transaction = new Transaction();
			$system_handler = new SystemHandler(false);

			Security::protect_session();
			
			$session = new Session($_GET['session_id']);
			$user = new User($session->get_user_id());
			$regional = new Regional();
			
			$session_valid_array = $session->is_valid();
			if ($session_valid_array[0] === true)
			{
				if ($_GET['dialog'])
				{
					require_once("core/modules/base/report/report_table.io.php");	
					
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("report", $_GET['dialog']);
							
					if (file_exists($module_dialog['class_path']))
					{
						require_once($module_dialog['class_path']);
						
						if (class_exists($module_dialog['class']))
						{
							if (method_exists($module_dialog['class'], $module_dialog['method']))
							{
								$pdf = $module_dialog['class']::$module_dialog['method']();
								if (is_object($pdf))
								{
									$pdf->Output();
								}
								else
								{
									throw new BaseReportException();
								}
							}
							else
							{
								throw new BaseModuleDialogMethodNotFoundException();
							}
						}
						else
						{
							throw new BaseModuleDialogClassNotFoundException();
						}
					}
					else
					{
						throw new BaseModuleDialogFileNotFoundException();
					}
				}
				else
				{
					throw new BaseModuleDialogMissingException();
				}
			}
			else
			{
				throw new BaseUserAccessDeniedException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	catch(BaseException $e)
	{
		echo "Error; Exception: ".$e->get_message();
	}
?>
