<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz, Roman Quiring
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
require_once("../base/ajax.php");

/**
 * File AJAX IO Class
 * @package organisation_unit
 */
class FileAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function is_uploadprogress_installed()
	{
		if(extension_loaded("up loadprogress"))
		{
			return "1";
		}
		else
		{
			return "0";
		}
	}
	
	public function get_progress($id)
	{
		if(extension_loaded("uploadprogress"))
		{
			return json_encode(uploadprogress_get_info($id));
		}
		else
		{
			return "0";
		}
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):	
				case "is_uploadprogress_installed":
					echo $this->is_uploadprogress_installed();
				break;
				
				case "get_progress":
					echo $this->get_progress($_GET["id"]);
				break;
			endswitch;
		}
	}
}

$file_ajax = new FileAjax;
$file_ajax->method_handler();

?>