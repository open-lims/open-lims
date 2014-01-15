<?php
/**
 * @package install
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
 * @package install
 */
class LoginAjax
{
	public static function login($username, $password)
	{
		if ($username and $password)
		{
			$auth = new Auth();
			
			if ($auth->login($username, $password) == true)
			{
				$session_id = $auth->get_session_id();
				$session = new Session($session_id);
				
		 		return "index.php?session_id=".$session_id;	
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
}
?>