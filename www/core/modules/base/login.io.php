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
 * Login IO Class
 * @package base
 */
class Login_IO
{
	/**
	 * Login
	 * @param integer $error_no
	 */
	public static function login($error_no)
	{
		if ($error_no != null)
		{
			switch($error_no):
				case 1:
					$error = "Your username or your password are wrong.";
				break;
				
				case 2:
					$error = "You must enter an username.";
				break;
				
				case 3:
					$error = "Your must enter a password.";
				break;
				
				case 4:
					$error = "This user is locked by administrator.";
				break;
			endswitch;
		}
		else
		{
			$error = "";
		}

		$template = new Template("template/base/login/login.html");

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

		if ($_POST[username])
		{
			$template->set_var("username", $_POST[username]);
		}
		else
		{
			$template->set_var("username", "");
		}
		
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
	 * Logout
	 */
	public static function logout()
	{
		global $session;
		
		$auth = new Auth();
		
		if ($auth->logout($session->get_user_id(),$_GET[session_id]) == true)
		{
 			$template = new Template("template/base/login/logout_proceed.html");
			$template->set_var("footer",constant("LOGIN_FOOTER"));
 			$template->output();		
		}
	}
	
	/**
	 * Forgot Password
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
		
		$template = new Template("template/base/login/forgot_password.html");

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
	 */
	public static function forgot_password_proceed($success)
	{
		$template = new Template("template/base/login/forgot_password_proceed.html");
		
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
		$template = new Template("template/base/login/info.html");
		$template->set_var("version",constant("PRODUCT_VERSION"));
		$template->set_var("product_name",constant("PRODUCT"));
		$template->set_var("footer",constant("LOGIN_FOOTER"));
		$template->output();
	}
	
	public static function login_help()
	{
		$template = new Template("template/base/login/help.html");
		$template->set_var("footer",constant("LOGIN_FOOTER"));
		$template->output();
	}
	
	public static function output()
	{
		$auth = new Auth();
		
		$template = new Template("template/login_header.html");
		$template->output();
		
		switch ($_GET[run]):
		
			case ("login"):				
				if ($_POST[username] and $_POST[password])
				{
					if ($auth->login($_POST[username], $_POST[password]) == true)
					{
						$session_id = $auth->get_session_id();
						$session = new Session($session_id);
						$user = new User($session->get_user_id());
											
						if ($user->get_boolean_user_entry("user_locked") == false)
						{
				 			$template = new Template("template/base/login/proceed.html");
							$template->set_var("username",$_POST[username]);
							$template->set_var("session_id",$session_id);
							$template->set_var("footer",constant("LOGIN_FOOTER"));
				 			$template->output();		
						}
						else
						{
							self::login(4);
						}
					}
					else
					{
						self::login(1);
					}
				}
				else
				{
					if ($_POST[submit] and !$_POST[username])
					{
						self::login(2);
					}
					elseif($_POST[submit] and $_POST[username] and !$_POST[password])
					{
						self::login(3);
					}
					else
					{
						self::login(null);
					}
				}
			break;
			
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
 			
 			case("logout"):		
				self::logout();
 			break;
				
			case ("login_help"):
				self::login_help();
			break;
			
			case ("login_info"):
 				self::login_info();
			break;
			
			default:
				self::login(null);					
			break;
			
		endswitch;
	}
	
}

?>
