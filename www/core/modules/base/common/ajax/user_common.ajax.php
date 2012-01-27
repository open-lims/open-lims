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
 * 
 */
$GLOBALS['autoload_prefix'] = "../../";
require_once("../../../base/ajax.php");

/**
 * User AJAX IO Class
 * @package base
 */
class UserAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @param string $string
	 */
	private function get_users_in_option($string)
	{
		$string = $string."*";
		
		$user_array = User_Wrapper::list_search_users($string ,null, null, null, null);

		if (is_array($user_array) and $user_array >= 1)
		{
			$return = "";
			
			foreach($user_array as $key => $value)
			{
				$return .= "<option id='User".$value['id']."'>".$value['fullname']." (".$value['username'].")</option>";
			}
			
			echo $return;
		}
		else
		{
			echo "<option></option>";
		}
	}
	
	/**
	 * @param string $string
	 */
	private function get_groups_in_option($string)
	{
		$string = $string."*";
		
		$user_array = User_Wrapper::list_search_groups($string ,null, null, null, null);

		if (is_array($user_array) and $user_array >= 1)
		{
			$return = "";
			
			foreach($user_array as $key => $value)
			{
				$return .= "<option id='Group".$value['id']."'>".$value['name']."</option>";
			}
			
			echo $return;
		}
		else
		{
			echo "<option></option>";
		}
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):	
				case "get_users_in_option":
					$this->get_users_in_option($_GET['string']);
				break;
				
				case "get_groups_in_option":
					$this->get_groups_in_option($_GET['string']);
				break;
			endswitch;
		}
	}
}

$user_ajax = new UserAjax;
$user_ajax->method_handler();

?>