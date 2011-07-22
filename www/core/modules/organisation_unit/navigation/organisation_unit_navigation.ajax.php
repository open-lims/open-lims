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
	
	private function get_html()
	{
		$template = new Template("../../../../template/organisation_unit/navigation/left.html");
		
		$template->output();
	}
	
	public function get_array()
	{
		global $session;

		if ($session->is_value("LEFT_NAVIGATION_OU_ARRAY"))
		{
			echo json_encode($session->read_value("LEFT_NAVIGATION_OU_ARRAY"));
		}
		else
		{
			$return_array = array();
									
			$organisation_unit_array = OrganisationUnit::list_organisation_unit_roots();
			
			if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
			{
				$counter = 0;
				$module_link_array = ModuleLink::list_links_by_type("ou_navigation");
				
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);

					$return_array[$counter][0] = 0;
					$return_array[$counter][1] = $value;
					$return_array[$counter][2] = $organisation_unit->get_name();
					$return_array[$counter][3] = $organisation_unit->get_icon();
					$return_array[$counter][4] = true; // Permission
					
					if ($organisation_unit->get_stores_data() == true)
					{	
						if (is_array($module_link_array) and count($module_link_array) >= 1)
						{
							$paramquery['username'] = $_GET['username'];
							$paramquery['session_id'] = $_GET['session_id'];
							
							if (is_array($module_link_array[0]['array']) and count($module_link_array[0]['array']) >= 1)
							{
								foreach ($module_link_array[0]['array'] as $array_key => $array_value)
								{
									if ($array_value == "%OU_ID%")
									{
										$paramquery['ou_id'] = $value;
									}
									else
									{
										$paramquery[$array_key] = $array_value;
									}
								}
							}
							
							$params = http_build_query($paramquery, '', '&#38;');
							
							$return_array[$counter][5] = true;
							$return_array[$counter][6] = $params; //link
						}
						else
						{
							$return_array[$counter][5] = true;
							$return_array[$counter][6] = ""; //link
						}
					}
					else
					{
						$return_array[$counter][5] = false;
						$return_array[$counter][6] = "";
					}
					
					$return_array[$counter][7] = false; //open
					
					$counter++;
				}
			}
			
			echo json_encode($return_array);
		}
	}
	
	public function set_array($array)
	{
		global $session;
		
		$var = json_decode($array);
		if (is_array($var))
		{
			if($session->write_value("LEFT_NAVIGATION_OU_ARRAY", $var, true) == true)
			{
				echo "läuft";
			}
		}

	}
	
	public function get_children($id)
	{
		if (is_numeric($id) and $id != 0)
		{
			$return_array = array();

			$organisation_unit = new OrganisationUnit($id);
			
			$organisation_unit_array = $organisation_unit->get_organisation_unit_childs();

			if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
			{
				$counter = 0;
				
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
						
					$return_array[$counter][0] = -1;
					$return_array[$counter][1] = $value;
					$return_array[$counter][2] = $organisation_unit->get_name();
					$return_array[$counter][3] = $organisation_unit->get_icon();
					$return_array[$counter][4] = true; // Permission
					
					if ($organisation_unit->get_stores_data() == true)
					{
						$module_link_array = ModuleLink::list_links_by_type("ou_navigation");
	
						if (is_array($module_link_array) and count($module_link_array) >= 1)
						{
							$paramquery['username'] = $_GET['username'];
							$paramquery['session_id'] = $_GET['session_id'];
							
							if (is_array($module_link_array[0]['array']) and count($module_link_array[0]['array']) >= 1)
							{
								foreach ($module_link_array[0]['array'] as $array_key => $array_value)
								{
									if ($array_value == "%OU_ID%")
									{
										$paramquery['ou_id'] = $value;
									}
									else
									{
										$paramquery[$array_key] = $array_value;
									}
								}
							}
							
							$params = http_build_query($paramquery, '', '&#38;');
							
							$return_array[$counter][5] = true;
							$return_array[$counter][6] = $params; //link
						}
						else
						{
							$return_array[$counter][5] = true;
							$return_array[$counter][6] = ""; //link
						}
					}
					else
					{
						$return_array[$counter][5] = false;
						$return_array[$counter][6] = "";
					}
					
					$return_array[$counter][7] = false; //open
					
					$counter++;
				}
			}
			
			echo json_encode($return_array);
		}
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
				
				case "get_children":
					$this->get_children($_GET['id']);
				break;	
			endswitch;
		}
	}
}

$organisation_unit_ajax = new OrganisationUnitAjax;
$organisation_unit_ajax->method_handler();

?>