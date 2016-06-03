<?php
/**
 * @package base
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
 * Common IO Class
 * @package base
 */
class Error_IO
{
	private $exception;
	
	/**
	 * @param object $exception
	 * @param integer $module
	 * @param integer $layer
	 * @param integer $error_type
	 */
	function __construct($exception)
	{
		$this->exception = $exception;
	}
	
	public function get_error_message()
	{
		if ($this->exception instanceof BasePHPErrorException)
		{

			$language_error_message = $this->exception->getMessage();
		}
		else
		{
			$language_error_message = Language::get_message(get_class($this->exception), "exception");
		}
		
		$exception_error_message = $this->exception->getMessage();
		
		if ($language_error_message == null)
		{
			if ($exception_error_message == null)
			{
				return "A non-specfic error occurs!";
			}
			else
			{
				return $exception_error_message;
			}
		}
		else
		{
			return $language_error_message;
		}
	}
	
	public function display_error()
	{
		if (method_exists($this->exception, "is_security"))
		{
			if ($this->exception->is_security() == true)
			{
				$template = new HTMLTemplate("base/error/security_in_box.html");
			}
			else
			{
				$template = new HTMLTemplate("base/error/error_in_box.html");
			}
		}
		else
		{
			$template = new HTMLTemplate("base/error/error_in_box.html");
		}	

		$template->set_var("error_msg", $this->get_error_message());

		$template->output();
	}
	
	/**
	 * @param string $message
	 */
	public static function fatal_error($message)
	{
		$template = new HTMLTemplate("login_header.html");
		$template->output();
		
		$template = new HTMLTemplate("base/error/fatal.html");
		$template->set_var("message", $message);
		$template->output();
	}
	
	/**
	 * @param string $message
	 */
	public static function security_out_of_box_error($message)
	{
		$template = new HTMLTemplate("login_header.html");
		$template->output();
		
		$template = new HTMLTemplate("base/error/security_out_of_box.html");
		$template->set_var("message", $message);
		$template->output();
	}
}

?>
