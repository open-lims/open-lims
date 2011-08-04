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
 * 
 */
require_once("ajax.php");

/**
 * Login AJAX IO Class
 * @package base
 */
class LoginAjax extends Ajax
{	
	function __construct()
	{
		parent::__construct();
	}
	
	public function logout()
	{
		global $session;
		
		$auth = new Auth();
		
		if ($auth->logout($session->get_user_id(),$_GET[session_id]) == true)
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
	
				case "login":

				break;
				
				case "logout":
					$this->logout();
				break;
			
			endswitch;
		}
	}
}

$login_ajax = new LoginAjax;
$login_ajax->method_handler();

?>