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
 * Login AJAX IO Class
 * @package base
 */
class LoginAjax
{	
	public static function login($username, $password, $language)
	{
		if ($username and $password)
		{
			$auth = new Auth();
			
			if ($auth->login($username, $password) == true)
			{
				$session_id = $auth->get_session_id();
				$session = new Session($session_id);
				$user = new User($session->get_user_id());
				$regional = new Regional($session->get_user_id());
				
				if (is_numeric($language))
				{
					$session->write_value("LANGUAGE", $language);
				}
				else
				{
					$session->write_value("LANGUAGE", $regional->get_language_id());
				}
								
				if ($user->get_boolean_user_entry("user_locked") == false)
				{
		 			return "index.php?username=".$username."&session_id=".$session_id;	
				}
				else
				{
					return "This user is locked by administrator.";
				}
			}
			else
			{
				return "Your username or your password are wrong.";
			}
		}
		else
		{
			if ($username and !$password)
			{
				return "Your must enter a password.";
			}
			else
			{
				return "You must enter an username.";
			}
		}
	}
	
	public static function logout()
	{
		global $session;
		
		$auth = new Auth();
		
		if ($auth->logout($session->get_user_id(),$_GET[session_id]) == true)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}
?>