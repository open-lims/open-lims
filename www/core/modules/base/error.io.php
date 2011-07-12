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
 * Common IO Class
 * @package base
 */
class Error_IO
{
	private $exception_handler;
	private $error_type;
	
	function __construct($exception, $module, $layer, $error_type)
	{
		$this->exception_handler = new ExceptionHandler($exception, $module, $layer, $error_type);
		$this->error_type = $error_type;
	}
	
	public function display_error()
	{
		if ($this->exception_handler)
		{
			if ($this->error_type == 2)
			{
				$template = new Template("template/base/error/security_in_box.html");
			}
			else
			{
				$template = new Template("template/base/error/error_in_box.html");
			}	
				
			$template->set_var("error_code", $this->exception_handler->get_error_no());	
			$template->set_var("error_msg", $this->exception_handler->get_error_message());
	
			$template->output();
		}
	}
	
	public static function fatal_error($message)
	{
		$template = new Template("template/login_header.html");
		$template->output();
		
		$template = new Template("template/base/error/fatal.html");
		$template->set_var("message", $message);
		$template->output();
	}
	
	public static function security_out_of_box_error($message)
	{
		$template = new Template("template/login_header.html");
		$template->output();
		
		$template = new Template("template/base/error/security_out_of_box.html");
		$template->set_var("message", $message);
		$template->output();
	}
}

?>
