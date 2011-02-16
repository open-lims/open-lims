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
require_once("../base/ajax_init.php");

/**
 * Organiser AJAX IO Class
 * @package organisation_unit
 */
class OrganisationUnitAJAX extends AJAXInit
{
	function __construct()
	{
		parent::__construct();
	}
		
	private function list_menu_childs_by_id()
	{
		if ($_GET[session_id])
		{
			$session = new Session($_GET[session_id]);

			if ($session->is_valid())
			{
				if ($_GET[ou_id] == 0)
				{
					if ($session->is_value("CURRENT_NAVIGATION_OU"))
					{
						return serialize($session->read_value("CURRENT_NAVIGATION_OU"));
					}
					else
					{
						$return_array = array();
												
						$organisation_unit_array = OrganisationUnit::list_organisation_unit_roots();
						
						if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
						{
							$counter = 0;
							
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
									$return_array[$counter][5] = true;
								}
								else
								{
									$return_array[$counter][5] = false;
								}
								
								$counter++;
							}
						}
						return serialize($return_array);
					}
				}
				else
				{
					if ($_GET[ou_id])
					{
						$return_array = array();

						$organisation_unit = new OrganisationUnit($_GET[ou_id]);
						
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
									$return_array[$counter][5] = true;
								}
								else
								{
									$return_array[$counter][5] = false;
								}
								$counter++;
							}
						}
						return serialize($return_array);	
					}
				}
			}
		}
	}
	
	private function rewrite_menu_childs_array()
	{
		if ($_GET[session_id])
		{
			$session = new Session($_GET[session_id]);
		
			if ($_POST[serialized_folder_array])
			{
				$serialized_folder_array = $_POST[serialized_folder_array];
				
				$serialized_folder_array = stripslashes($serialized_folder_array);
				$folder_array = unserialize($serialized_folder_array);

				$session->write_value("CURRENT_NAVIGATION_OU", $folder_array, true);
			}
		}
	}

	public function method_handler()
	{
		header("Content-Type: text/html; charset=utf-8");
		
		switch($_GET[run]):
			
			case "list_menu_ou_childs":
				echo $this->list_menu_childs_by_id();
			break;
			
			case "rewrite_menu_childs_array":
				echo $this->rewrite_menu_childs_array();
			break;
			
		endswitch;
	}

}

$organisation_unit_ajax = new OrganisationUnitAJAX;
$organisation_unit_ajax->method_handler();

?>