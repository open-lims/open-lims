<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
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
$GLOBALS['autoload_prefix'] = "../";
require_once("../../base/ajax.php");

/**
 * Left Nav AJAX IO Class
 * @package base
 */
class LeftNavigationAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
		
	public function set_active($id)
	{
		global $session;	
	
		if ($id)
		{
			$session->write_value("LEFT_NAVIGATION_ACTIVE", $id, true);
		}
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):	
				case "set_active":
					$this->set_active($_GET[id]);
				break;
			endswitch;
		}
	}
}

$left_navigation_ajax = new LeftNavigationAjax;
$left_navigation_ajax->method_handler();

?>