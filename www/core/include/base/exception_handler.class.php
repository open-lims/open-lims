<?php
/**
 * @package base
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
 */


/**
 * 
 */
require_once("interfaces/exception_handler.interface.php");

/**
 * Exception Handler Class
 * Handles Exceptions with SystemLog
 * @package base
 */
class ExceptionHandler implements ExceptionHandlerInterface
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
	 * Gets Exception and information and stores it in SystemLog
	 * @param object $exception
	 * @param integer $module
	 * @param integer $layer
	 * @param integer $error_type
	 */
	function __construct($exception, $module, $layer, $error_type)
	{
		global $user; // And Language
		
		if (is_object($exception) and is_numeric($module) and is_numeric($layer) and is_numeric($error_type))
		{
			$this->exception_message 		= $exception->getMessage();
			$this->exception_code 			= $exception->getCode();
			$this->exception_file			= $exception->getFile();
			$this->exception_line			= $exception->getLine();
			$this->exception_trace_array	= $exception->getTrace();
			
			$this->module					= $module;
			$this->layer					= $layer;
			$this->error_type				= $error_type;
			
			if ($this->exception_code and $this->module and $this->layer and $this->error_type)
			{
				$this->error_code = "OL-".$this->module."-".$this->layer."-".$this->error_type."-".$this->exception_code;
			}
			else
			{
				$this->error_code =  null;
			}
			
			$error = array();
    	
    		include("languages/en-gb/var/errors.inc.php");
			
			if ($error[$this->module][$this->layer][$this->error_type][$this->exception_code])
			{
	    		$this->error_message = $error[$this->module][$this->layer][$this->error_type][$this->exception_code];
	    	}
	    	else
	    	{
	    		$this->error_message = "Undefined Error";
	    	}

			if (class_exists('SystemLog'))
			{
				if ($user)
				{
					$user_id = $user->get_user_id();
				}
				else
				{
					$user_id = 1;
				}
			
				$trace_array_string = serialize($this->exception_trace_array);
			
				$trace_array_string = str_replace("\\","/",$trace_array_string);
				$this->exception_file = str_replace("\\","/",$this->exception_file);
			
				$system_log = new SystemLog(null);
		   		$system_log->create($user_id,2,1,$this->error_message,$this->error_code,$this->exception_file,$this->exception_line,null);
		 		$system_log->set_stack_trace($trace_array_string);
			}
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_error_no()
	{
		if ($this->error_code)
		{
			return $this->error_code;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return string
	 */
    public function get_error_message()
    {
    	if ($this->error_message)
    	{
			return $this->error_message;
		}
		else
		{
			return null;
		}
    }
    
}
?>