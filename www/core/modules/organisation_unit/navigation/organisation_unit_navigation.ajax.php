<?php
/**
 * @package organisation_unit
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
 * 
 */
$GLOBALS['autoload_prefix'] = "../";
require_once("../../base/ajax.php");

/**
 * Organiser AJAX IO Class
 * @package organisation_unit
 */
class OrganisationUnitAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	private function get_name()
	{
		echo "OrganisationUnit";
	}
	
	/**
	 * Should return HTML of Menu
	 */
	private function get_html()
	{
		$template = new Template("../../../../languages/en-gb/template/organisation_unit/navigation/left.html");
		
		$template->output();
	}
	
	public function get_array()
	{
		$return_array = array();
		$return_array[0][0] = 0; //layer
		$return_array[0][1] = 1; //id
		$return_array[0][2] = "bananenbrot";
		$return_array[0][3] = "project.png";
		$return_array[0][4] = true; // Permission
		$return_array[0][5] = true; //clickable
		$return_array[0][6] = ""; //link
		$return_array[0][7] = false; //open
		echo json_encode($return_array);
	}
	
	public function set_array($array)
	{
		$var = json_decode($array);
		echo count($var);
	}
	
	public function get_childs($id)
	{
		$return_array = array();
		$return_array[0][0] = -1; //layer
		$return_array[0][1] = mt_rand(100, 100000); //id
		$return_array[0][2] = md5(microtime());
		$return_array[0][3] = "project.png";
		$return_array[0][4] = true; // Permission
		$return_array[0][5] = true;
		$return_array[0][6] = ""; //link
		$return_array[0][7] = false; //open
		
		echo json_encode($return_array);
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):	
				case "get_name":
					$this->get_name();
				break;
				
				case "get_html":
					$this->get_html();
				break;
							
				case "get_array":
					$this->get_array();
				break;
				
				case "set_array":
					$this->set_array($_POST['array']);
				break;
				
				case "get_childs":
					$this->get_childs($_GET['id']);
				break;	
			endswitch;
		}
	}
}

$organisation_unit_ajax = new OrganisationUnitAjax;
$organisation_unit_ajax->method_handler();

?>