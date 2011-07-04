<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Quiring
 * @copyright (c) 2008-2010 by Roman Quiring
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
 * Project AJAX IO Class
 * @package project
 */
class ProjectAjax extends Ajax
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	private function get_name()
	{
		echo "Project";
	}
	
	/**
	 * Should return HTML of Menu
	 */
	private function get_html()
	{
		$template = new Template("../../../../languages/en-gb/template/projects/navigation/left.html");
		
		$template->output();
	}
	
	
	public function get_array()
	{
		$return_array = array();
		$return_array[0][0] = 0; //layer
		$return_array[0][1] = 1; //id
		$return_array[0][2] = "kaesebrot";
		$return_array[0][3] = "project.png";
		$return_array[0][4] = true; // Permission
		$return_array[0][5] = true; //clickable
		$return_array[0][6] = ""; //link
		$return_array[0][7] = true; //open
		
		$return_array[1][0] = 1; //layer
		$return_array[1][1] = 2; //id
		$return_array[1][2] = "wurstbrot";
		$return_array[1][3] = "project.png";
		$return_array[1][4] = true; // Permission
		$return_array[1][5] = true;
		$return_array[1][6] = ""; //link
		$return_array[1][7] = true; //open
		
		$return_array[2][0] = 2; //layer
		$return_array[2][1] = 3; //id
		$return_array[2][2] = "marmeladenbrot";
		$return_array[2][3] = "project.png";
		$return_array[2][4] = true; // Permission
		$return_array[2][5] = true;
		$return_array[2][6] = ""; //link
		$return_array[2][7] = false; //open
		
		$return_array[3][0] = 2; //layer
		$return_array[3][1] = 4; //id
		$return_array[3][2] = "abrot";
		$return_array[3][3] = "project.png";
		$return_array[3][4] = true; // Permission
		$return_array[3][5] = true;
		$return_array[3][6] = ""; //link
		$return_array[3][7] = false; //open
		
		$return_array[4][0] = 0; //layer
		$return_array[4][1] = 5; //id
		$return_array[4][2] = "bbrot";
		$return_array[4][3] = "project.png";
		$return_array[4][4] = true; // Permission
		$return_array[4][5] = true;
		$return_array[4][6] = ""; //link
		$return_array[4][7] = true; //open
		
		$return_array[5][0] = 1; //layer
		$return_array[5][1] = 6; //id
		$return_array[5][2] = "cbrot";
		$return_array[5][3] = "project.png";
		$return_array[5][4] = true; // Permission
		$return_array[5][5] = true;
		$return_array[5][6] = ""; //link
		$return_array[5][7] = false; //open
		
		$return_array[6][0] = 0; //layer
		$return_array[6][1] = 7; //id
		$return_array[6][2] = "dbrot";
		$return_array[6][3] = "project.png";
		$return_array[6][4] = true; // Permission
		$return_array[6][5] = true;
		$return_array[6][6] = ""; //link
		$return_array[6][7] = false; //open
		
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

$organisation_unit_ajax = new ProjectAjax;
$organisation_unit_ajax->method_handler();

?>