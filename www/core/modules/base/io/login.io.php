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
 * Login IO Class
 * @package base
 */
class Login_IO
{
	/**
	 * Login
	 * @param integer $error_no
	 */
	public static function login()
	{
		$template = new HTMLTemplate("base/login/login.html");

		$template->set_var("footer",constant("LOGIN_FOOTER"));

		$language_array = Regional::list_languages();
				
		$result = array();
		$counter = 0;
		
		if (is_array($language_array))
		{
			foreach($language_array as $key => $value)
			{
				$result[$counter][value] = $value;
				$result[$counter][content] = Regional::get_language_name($value);
				$counter++;		
			}
		}
		
		$template->set_var("option",$result);
			
		$template->output();
	}
		
	/**
	 * Forgot Password
	 * @param integer $error_no
	 */
	public static function forgot_password($error_no)
	{
		if ($error_no != null)
		{
			switch($error_no):
				case 1:
					$error = "<span class='formError'>Your username or your mail are wrong.</span>";
				break;
			endswitch;
		}
		
		$template = new HTMLTemplate("base/login/forgot_password.html");

		$template->set_var("footer",constant("LOGIN_FOOTER"));
		
		if ($error)
		{
			$template->set_var("error",$error);
		}
		else
		{
			$template->set_var("error","");
		}
		$template->output();
	}
	
	/**
	 * Proceed of Forgor Password
	 * @param bool $success
	 */
	public static function forgot_password_proceed($success)
	{
		$template = new HTMLTemplate("base/login/forgot_password_proceed.html");
		
		if ($success == true)
		{
			$template->set_var("message","A new password was sent to your mail-adress.");
		}
		else
		{
			$template->set_var("message","Unable to send you a new password. Contact Administrator.");
		}
		
		$template->set_var("footer",constant("LOGIN_FOOTER"));
		$template->output();	
	}
	
	public static function login_info()
	{
		$template = new HTMLTemplate("base/login/info.html");
		$template->set_var("version",constant("PRODUCT_VERSION"));
		$template->set_var("product_name",constant("PRODUCT"));
		$template->set_var("footer",constant("LOGIN_FOOTER"));
		$template->output();
	}
	
	public static function login_help()
	{
		$template = new HTMLTemplate("base/login/help.html");
		$template->set_var("footer",constant("LOGIN_FOOTER"));
		$template->output();
	}
	
	public static function output()
	{
		$auth = new Auth();
		
		$template = new HTMLTemplate("login_header.html");
		$template->output();
		
		switch ($_GET[run]):
			
			case ("forgot"):
				if ($_POST[username] and $_POST[mail])
				{
					try
					{
						$auth->forgot_password($_POST[username], $_POST[mail]);
						self::forgot_password_proceed(true);
					}
					catch(AuthUserNotFoundException $e)
					{
						self::forgot_password(1);	
					}
					catch(AuthForgotPasswordSendFailedException $e)
					{
						self::forgot_password_proceed(false);
					}
				}
				else
				{
					self::forgot_password(null);
				}
 			break;
				
			case ("login_help"):
				self::login_help();
			break;
			
			case ("login_info"):
 				self::login_info();
			break;
			
			default:
				self::login();					
			break;
			
		endswitch;
	}
	
}

?>
