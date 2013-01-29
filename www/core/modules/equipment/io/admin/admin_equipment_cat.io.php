<?php
/**
 * @package equipment
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Equipment Admin IO Class
 * @package equipment
 */
class AdminEquipmentCatIO
{
	private static $home_list_counter = 0;

	private static function home_child_list($id, $layer)
	{
		if (is_numeric($id))
		{
			$content_array = array();
			
			$equipment_cat = new EquipmentCat($id);
			$equipment_cat_child_array = $equipment_cat->get_children();
			
			if(is_array($equipment_cat_child_array) and count($equipment_cat_child_array) >= 1)
			{
				foreach($equipment_cat_child_array as $key => $value)
				{
					$equipment_cat = new EquipmentCat($value);
					
					$content_array[self::$home_list_counter]['padding'] = 0.5 * $layer;				
					$content_array[self::$home_list_counter]['name'] = $equipment_cat->get_name();					
					$content_array[self::$home_list_counter]['id'] = $value;	
					
					$paramquery = $_GET;
					$paramquery['action'] = "delete";
					$paramquery['id'] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter]['delete_params'] = $params;
					
					
					$paramquery = $_GET;
					$paramquery['action'] = "add_child";
					$paramquery['id'] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter]['create_child_params'] = $params;
					
					
					$paramquery = $_GET;
					$paramquery['action'] = "edit";
					$paramquery['id'] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter]['edit_params'] = $params;
					
					$temp_counter = self::$home_list_counter;
					
					self::$home_list_counter++;
					
					$equipment_cat_child_array = self::home_child_list($value, $layer+1);
				
					if (is_array($equipment_cat_child_array))
					{
						$content_array =  $content_array + $equipment_cat_child_array;
					}
					
				}
				return $content_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}

	public static function home()
	{
		$template = new HTMLTemplate("equipment/admin/equipment_cat/list.html");	

		$content_array = array();
		
		$equipment_cat_root_array = EquipmentCat::list_root_entries();
		
		if(is_array($equipment_cat_root_array) and count($equipment_cat_root_array) >= 1)
		{
			foreach($equipment_cat_root_array as $key => $value)
			{
				$equipment_cat = new EquipmentCat($value);
				
				$content_array[self::$home_list_counter]['padding'] = 0;
				$content_array[self::$home_list_counter]['name'] = $equipment_cat->get_name();	
				$content_array[self::$home_list_counter]['id'] = $value;				
				
				$paramquery = $_GET;
				$paramquery['action'] = "delete";
				$paramquery['id'] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter]['delete_params'] = $params;
				
				
				$paramquery = $_GET;
				$paramquery['action'] = "add_child";
				$paramquery['id'] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter]['create_child_params'] = $params;
				
				
				$paramquery = $_GET;
				$paramquery['action'] = "edit";
				$paramquery['id'] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter]['edit_params'] = $params;
				
				$temp_counter = self::$home_list_counter;
				
				self::$home_list_counter++;
				
				$equipment_cat_child_array = self::home_child_list($value, 1);
				
				if (is_array($equipment_cat_child_array))
				{
					$content_array = $content_array + $equipment_cat_child_array;
				}
			}
			$template->set_var("no_entry", false);
		}
		else
		{
			$template->set_var("no_entry", true);
		}
				
		$paramquery = $_GET;
		$paramquery['action'] = "add";
		unset($paramquery['nextpage']);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
		
		$template->set_var("equipment_cat_array", $content_array);
		
		$template->output();
	}

	/**
	 * @throws EquipmentCategoryIDMissingException
	 */
	public static function create()
	{
		if (($_GET['action'] == "add_child" and $_GET['id']) or $_GET['action'] == "add")
		{
			if ($_GET['nextpage'] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST['name'])
				{
					if (EquipmentCat::exist_name($_POST['name']) == true)
					{
						$page_1_passed = false;
						$error = "This name already exists";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must enter a name";
				}
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
	
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("equipment/admin/equipment_cat/add.html");
				
				$paramquery = $_GET;
				$paramquery['nextpage'] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				if ($error)
				{
					$template->set_var("error", $error);
				}
				else
				{
					$template->set_var("error", "");	
				}
													 
				if ($_POST['name'])
				{
					$template->set_var("name", $_POST['name']);
				}
				else
				{
					$template->set_var("name", "");
				}
							
				$template->output();
			}
			else
			{				
				$equipment_cat = new EquipmentCat(null);
					
				if ($_GET['action'] == "add_child" and is_numeric($_GET['id']))
				{
					$toid = $_GET['id'];
				}
				else
				{
					$toid = null;
				}	

				$paramquery = $_GET;
				unset($paramquery['action']);
				unset($paramquery['nextpage']);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($equipment_cat->create($toid, $_POST['name']))
				{
					Common_IO::step_proceed($params, "Add Equipment Category", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Add Equipment Category", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new EquipmentCategoryIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentCategoryIDMissingException
	 */
	public static function delete()
	{
		if ($_GET['id'])
		{
			if ($_GET['sure'] != "true")
			{
				$template = new HTMLTemplate("equipment/admin/equipment_cat/delete.html");
				
				$paramquery = $_GET;
				$paramquery['sure'] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery['sure']);
				unset($paramquery['action']);
				unset($paramquery['id']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery['sure']);
				unset($paramquery['action']);
				unset($paramquery['id']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$equipment_cat = new EquipmentCat($_GET['id']);
				
				if ($equipment_cat->delete())
				{							
					Common_IO::step_proceed($params, "Delete Equipment Category", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Equipment Category", "Operation Failed" ,null);
				}		
			}
		}
		else
		{
			throw new EquipmentCategoryIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentCategoryIDMissingException
	 */
	public static function edit()
	{
		if ($_GET['id'])
		{
			$equipment_cat = new EquipmentCat($_GET['id']);
		
			if ($_GET['nextpage'] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST['name'])
				{
					if (EquipmentCat::exist_name($_POST['name']) == true and $equipment_cat->get_name() != $_POST['name'])
					{
						$page_1_passed = false;
						$error = "This name already exists";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must enter a name";
				}
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
	
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("equipment/admin/equipment_cat/edit.html");
				
				$paramquery = $_GET;
				$paramquery['nextpage'] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				if ($error)
				{
					$template->set_var("error", $error);
				}
				else
				{
					$template->set_var("error", "");	
				}
													 
				if ($_POST['name'])
				{
					$template->set_var("name", $_POST['name']);
				}
				else
				{
					$template->set_var("name", $equipment_cat->get_name());
				}
							
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery['nextpage']);
				unset($paramquery['action']);
				$params = http_build_query($paramquery);
				
				if ($equipment_cat->set_name($_POST['name']))
				{
					Common_IO::step_proceed($params, "Edit Equipment Category", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Edit Equipment Category", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new EquipmentCategoryIDMissingException();
		}
	}
	
	public static function handler()
	{
		switch($_GET['action']):
			case "add":
			case "add_child":
				self::create();
			break;
			
			case "edit":
				self::edit();
			break;
			
			case "delete":
				self::delete();
			break;
						
			default:
				self::home();
			break;
		endswitch;
	}
	
}

?>