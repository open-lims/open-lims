<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Quiring
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

require_once("../base/ajax.php");

/**
 * Data Browser AJAX IO Class
 * @package data
 */

class DataBrowserAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	private function get_data_browser_path($folder_id, $virtual_folder_id)
	{
		if($folder_id == "null")
			$folder_id = null;
		if($virtual_folder_id == "null")
			$virtual_folder_id = null;
		$data_path = new DataPath($folder_id, $virtual_folder_id);
		$data_path->delete_stack();
		return $data_path->get_stack_path();
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
				
				case "get_data_browser_path":
					echo $this->get_data_browser_path($_GET[folder_id],$_GET[virtual_folder_id]);
				break;
				
				default:
				break;
			
			endswitch;
		}
	}
}

$data_browser_ajax = new DataBrowserAjax;
$data_browser_ajax->method_handler();
?>