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
 * 
 */
// require_once("interfaces/exception_handler.interface.php");

/**
 * Exception Handler Class
 * Handles Exceptions with SystemLog
 * @package base
 */
class ExceptionHandler  // implements ExceptionHandlerInterface
{
	private $module;
	private $layer;
	private $error_type;
	
	private $exception_message;
	private $exception_code;
	private $exception_file;
	private $exception_line;
	private $exception_trace_array;
	
	private $error_code;
	private $error_message;

	/**
	 * @see ExceptionHandlerInterface::__construct()
	 * @param object $exception
	 */
	function __construct($exception)
	{
		global $db, $transaction, $session;
		
		if (is_object($exception))
		{
			$transaction->force_rollback(false);
			
			if ($exception instanceof BaseException)
			{
				$error_id = uniqid();
				$error_data = date("Ymd-His");
				
				if ($exception->get_write_hdd_sql())
				{
	    			$db->query_log_end();
	    			if (is_writable(constant("LOG_DIR")))
	    			{
	    				$sql_log_dir = constant("LOG_DIR")."/sql";
	    				if (is_writable($sql_log_dir))
	    				{
		    				$filename = "sql-".$error_data."-".get_class($exception)."-".$error_id.".txt";
		    				$handle = fopen($sql_log_dir."/".$filename, "w");
		    				fwrite($handle, $db->get_query_log());
		    				fwrite($handle, "\n\n");
		    				fwrite($handle, "-----------\n");
		    				fwrite($handle, "LAST ERROR:\n");
		    				fwrite($handle, serialize($db->get_last_error()));
		    				fclose($handle);
	    				}
	    			}
				}
				
				if ($exception->get_write_hdd_session())
				{
					$session_value_array = Session::list_all_session_values($session->get_session_id());
					
					if (is_array($session_value_array) and count($session_value_array))
					{
		    			if (is_writable(constant("LOG_DIR")))
		    			{
		    				$session_log_dir = constant("LOG_DIR")."/session";
		    				if (is_writable($session_log_dir))
		    				{
		    					$filename = "session-".$error_data."-".get_class($exception)."-".$error_id.".txt";
		    					$handle = fopen($session_log_dir."/".$filename, "w");
		    					
		    					foreach($session_value_array as $key => $value)
		    					{
		    						fwrite($handle, $value['address']." => ".$value['value']."\n");
		    					}
		    					
		    					fclose($handle);
		    				}
		    			}
					}
				}
				
				if ($exception->get_write_log())
				{
					if (method_exists($session, get_user_id))
					{
						if ($session->get_user_id())
						{
							$this->write_log($session->get_user_id(), $exception);
						}
						else
						{
							$this->write_log(null, $exception);
						}
					}
				}
			}
			elseif ($exception instanceof Exception)
			{
				if ($session->get_user_id())
				{
					$this->write_log($session->get_user_id(), $exception);
				}
				else
				{
					$this->write_log(null, $exception);
				}
			}
		}
	}

	private function write_log($user_id, $exception)
	{
		$file = $exception->getFile();
		$file = str_replace('\\','\\\\',$file);
		
		$trace_as_string = $exception->getTraceAsString();
		$trace_as_string = str_replace('\\','\\\\',$trace_as_string);
		$trace_as_string = str_replace('\'','\"',$trace_as_string);
		
		$system_log = new SystemLog(null);
   		$system_log->create($user_id,2,1,$exception->getMessage(),$exception->getCode(),$file,$exception->getLine(),null);
   		$system_log->set_stack_trace($trace_as_string);
	}
}
?>