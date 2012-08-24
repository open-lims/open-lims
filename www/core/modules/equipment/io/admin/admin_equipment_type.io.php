<?php
/**
 * @package equipment
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
 * Equipment Type Admin IO Class
 * @package equipment
 */
class AdminEquipmentTypeIO
{
	private static $home_list_counter = 0;

	private static function home_child_list($id, $layer)
	{
		if (is_numeric($id))
		{
			$content_array = array();
			
			$equipment_type = new EquipmentType($id);
			$equipment_type_child_array = $equipment_type->get_children();
			
			if(is_array($equipment_type_child_array) and count($equipment_type_child_array) >= 1)
			{
				foreach($equipment_type_child_array as $key => $value)
				{
					$equipment_type = new EquipmentType($value);
					
					$content_array[self::$home_list_counter][padding] = 0.5 * $layer;				
					$content_array[self::$home_list_counter][name] = $equipment_type->get_name();					
					$content_array[self::$home_list_counter][category] = $equipment_type->get_cat_name();	
					$content_array[self::$home_list_counter][id] = $value;	
					
					if ($equipment_type->get_location_id() == null)
					{
						$content_array[self::$home_list_counter][location] = "<span class='italic'>none</span>";	
					}
					else
					{
						$location = new Location($equipment_type->get_location_id());
						$content_array[self::$home_list_counter][location] = $location->get_name(false);
					}
					
					$paramquery = $_GET;
					$paramquery[action] = "detail";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][detail_params] = $params;
					
					
					$paramquery = $_GET;
					$paramquery[action] = "delete";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][delete_params] = $params;
					
					
					$paramquery = $_GET;
					$paramquery[action] = "add_child";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$content_array[self::$home_list_counter][create_child_params] = $params;
					

					$temp_counter = self::$home_list_counter;
					
					self::$home_list_counter++;
					
					$equipment_type_child_array = self::home_child_list($value, $layer+1);
				
					if (is_array($equipment_type_child_array))
					{
						$content_array =  $content_array + $equipment_type_child_array;
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
		$template = new HTMLTemplate("equipment/admin/equipment_type/list.html");	

		$content_array = array();
		
		$equipment_type_root_array = EquipmentType::list_root_entries();
		
		if(is_array($equipment_type_root_array) and count($equipment_type_root_array) >= 1)
		{
			foreach($equipment_type_root_array as $key => $value)
			{
				$equipment_type = new EquipmentType($value);
				
				$content_array[self::$home_list_counter][padding] = 0;
				$content_array[self::$home_list_counter][name] = $equipment_type->get_name();				
				$content_array[self::$home_list_counter][category] = $equipment_type->get_cat_name();	
				$content_array[self::$home_list_counter][id] = $value;	
				
				if ($equipment_type->get_location_id() == null)
				{
					$content_array[self::$home_list_counter][location] = "<span class='italic'>none</span>";	
				}
				else
				{
					$location = new Location($equipment_type->get_location_id());
					$content_array[self::$home_list_counter][location] = $location->get_name(false);
				}
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				$paramquery[id] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter][detail_params] = $params;
				
				
				$paramquery = $_GET;
				$paramquery[action] = "delete";
				$paramquery[id] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter][delete_params] = $params;
				
				
				$paramquery = $_GET;
				$paramquery[action] = "add_child";
				$paramquery[id] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$content_array[self::$home_list_counter][create_child_params] = $params;
				
				
				$temp_counter = self::$home_list_counter;
				
				self::$home_list_counter++;
				
				$equipment_type_child_array = self::home_child_list($value, 1);
				
				if (is_array($equipment_type_child_array))
				{
					$content_array = $content_array + $equipment_type_child_array;
				}
			}
			$template->set_var("no_entry", false);
		}
		else
		{
			$template->set_var("no_entry", true);
		}
				
		$paramquery = $_GET;
		$paramquery[action] = "add";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
		
		$template->set_var("equipment_type_array", $content_array);
		
		$template->output();
	}

	/**
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function create()
	{
		if (($_GET[action] == "add_child" and $_GET[id]) or $_GET[action] == "add")
		{
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST[name] or $_POST[manufacturer])
				{
					if ($_POST[name])
					{
						if (EquipmentType::exist_name($_POST[name]) == true)
						{
							$page_1_passed = false;
							$error = "This name already exists";
						}
					}
				}
				else
				{
					$page_1_passed = false;
					$error2 = "You must enter a name or a manufacturer";
				}
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
	
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("equipment/admin/equipment_type/add.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				if ($error1)
				{
					$template->set_var("error1", $error1);
				}
				else
				{
					$template->set_var("error1", "");	
				}
				
				if ($error2)
				{
					$template->set_var("error2", $error2);
				}
				else
				{
					$template->set_var("error2", "");	
				}

				if ($_POST[manufacturer])
				{
					$template->set_var("manufacturer", $_POST[manufacturer]);
				}
				else
				{
					$template->set_var("manufacturer", "");
				}
				
				if ($_POST[name])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					$template->set_var("name", "");
				}
				
				if ($_POST[description])
				{
					$template->set_var("description", $_POST[description]);
				}
				else
				{
					$template->set_var("description", "");
				}
				
				$cat_array = EquipmentCat::list_entries();
						
				$result = array();
				$counter = 0;
				
				foreach($cat_array as $key => $value)
				{
					$equipment_cat = new EquipmentCat($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $equipment_cat->get_name();
					if ($_POST[cat_id] == $value)
					{
						$result[$counter][selected] = "selected='selected'";
					}
					else
					{
						$result[$counter][selected] = "";
					}
					$counter++;
				}
				
				$template->set_var("category",$result);

				$location_array = Location::list_entries();
					
				$result = array();
				$counter = 1;
				
				$result[0][value] = 0;
				$result[0][content] = "none";
				
				foreach($location_array as $key => $value)
				{
					$location = new Location($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $location->get_name(true);
					$counter++;
				}
				
				$template->set_var("location",$result);
				
				$template->output();
			}
			else
			{				
				$equipment_type = new EquipmentType(null);
					
				if ($_GET[action] == "add_child" and is_numeric($_GET[id]))
				{
					$toid = $_GET[id];
				}
				else
				{
					$toid = null;
				}	

				$paramquery = $_GET;
				unset($paramquery[action]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($equipment_type->create($toid, $_POST[name], $_POST[cat_id], $_POST[location_id], $_POST[description], $_POST[manufacturer]))
				{
					Common_IO::step_proceed($params, "Add Equipment Type", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Add Equipment Type", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function delete()
	{
		if ($_GET[id])
		{
			if ($_GET[sure] != "true")
			{
				$template = new HTMLTemplate("equipment/admin/equipment_type/delete.html");
				
				$paramquery = $_GET;
				$paramquery[sure] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery[sure]);
				unset($paramquery[action]);
				unset($paramquery[id]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[sure]);
				unset($paramquery[action]);
				unset($paramquery[id]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$equipment_type = new EquipmentType($_GET[id]);
				
				if ($equipment_type->delete())
				{							
					Common_IO::step_proceed($params, "Delete Type Category", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Type Category", "Operation Failed" ,null);
				}		
			}
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function detail()
	{
		if ($_GET[id])
		{
			$tab_io = new Tab_IO();
	
			$paramquery = $_GET;
			unset($paramquery['tab']);
			$params = http_build_query($paramquery,'','&#38;');
			
			$tab_io->add("detail", "Details", $params, false);
			
			
			$paramquery = $_GET;
			$paramquery['tab'] = "responsible_persons";
			$params = http_build_query($paramquery,'','&#38;');
			
			$tab_io->add("responsible_persons", "Responsible Persons", $params, false);
			
			$paramquery = $_GET;
			$paramquery['tab'] = "organisaiton_units";
			$params = http_build_query($paramquery,'','&#38;');
			
			$tab_io->add("organisaiton_units", "Organisation Units", $params, false);
			
			
			switch($_GET['tab']):
				
				case "responsible_persons":
					$tab_io->activate("responsible_persons");
				break;
			
				case "organisaiton_units":
					$tab_io->activate("organisaiton_units");
				break;
				
				default:
					$tab_io->activate("detail");
				break;
			
			endswitch;
				
			$tab_io->output();
			
			
			switch($_GET['tab']):

				case "responsible_persons":
					self::detail_reponsible_persons();
				break;
			
				case "organisaiton_units":
					self::detail_organisation_units();
				break;
				
				default:
					self::detail_home();
				break;
				
			endswitch;
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	private static function detail_home()
	{
		$equipment_type = new EquipmentType($_GET[id]);	
					
		$template = new HTMLTemplate("equipment/admin/equipment_type/detail.html");
		
		$paramquery = $_GET;
		$paramquery[action] = "rename";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("rename_params", $params);
		
		$template->set_var("name", $equipment_type->get_name());
		$template->set_var("category", $equipment_type->get_cat_name());
		
		$paramquery = $_GET;
		$paramquery[action] = "change_location";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("change_location_params", $params);
		
		if ($equipment_type->get_location_id() == null)
		{
			$template->set_var("location", "<span class='italic'>none</span>");
		}
		else
		{
			$location = new Location($equipment_type->get_location_id());
			$template->set_var("location", $location->get_name(true));
		}
		
		if ($equipment_type->get_description())
		{
			$template->set_var("description", $equipment_type->get_description());
		}
		else
		{
			$template->set_var("description", "<span class='italic'>none</span>");
		}
	
		$template->output();
	}
	
	/**
	 * @todo rebuild with List and JS operations
	 */
	private static function detail_reponsible_persons()
	{
		$equipment_type = new EquipmentType($_GET[id]);	
					
		$template = new HTMLTemplate("equipment/admin/equipment_type/detail_responsible_person.html");
		
		$template->set_var("name", $equipment_type->get_name());
		
		$paramquery = $_GET;
		$paramquery[action] = "add_user";
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_user_params", $params);	
		
		$user_array = $equipment_type->list_users();
		$user_content_array = array();
		
		$counter = 0;
		
		if (is_array($user_array) and count($user_array) >= 1)
		{
			foreach($user_array as $key => $value)
			{
				$user = new User($value);
				
				$paramquery = $_GET;
				$paramquery[action] = "delete_user";
				$paramquery[key] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$user_content_array[$counter][username] = $user->get_username();
				$user_content_array[$counter][fullname] = $user->get_full_name(false);
				$user_content_array[$counter][delete_params] = $params;
				
				$counter++;
			}
			$template->set_var("no_user", false);
		}
		else
		{
			$template->set_var("no_user", true);
		}
		
		$template->set_var("user", $user_content_array);
		
		$template->output();
	}
	
	/**
	 * @todo rebuild with List and JS operations
	 */
	private static function detail_organisation_units()
	{
		$equipment_type = new EquipmentType($_GET[id]);	
					
		$template = new HTMLTemplate("equipment/admin/equipment_type/detail_organisation_unit.html");
		
		$template->set_var("name", $equipment_type->get_name());
		
		$paramquery = $_GET;
		$paramquery[action] = "add_ou";
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_ou_params", $params);	
		
		$ou_array = $equipment_type->list_organisation_units();
		$ou_content_array = array();
		
		$counter = 0;
		
		if (is_array($ou_array) and count($ou_array) >= 1)
		{
			foreach($ou_array as $key => $value)
			{
				$organisation_unit = new OrganisationUnit($value);
				
				$paramquery = $_GET;
				$paramquery[action] = "delete_ou";
				$paramquery[key] = $value;
				$params = http_build_query($paramquery,'','&#38;');
				
				$ou_content_array[$counter][name] = $organisation_unit->get_name();
				$ou_content_array[$counter][delete_params] = $params;
				
				$counter++;
			}
			$template->set_var("no_ou", false);
		}
		else
		{
			$template->set_var("no_ou", true);
		}
		
		$template->set_var("ou", $ou_content_array);
		
		$template->output();
	}
	
	/**
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function rename()
	{
		if ($_GET[id])
		{
			$equipment_type = new EquipmentType($_GET[id]);
		
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST[name] or $_POST[manufacturer])
				{
					if ($_POST[name])
					{
						if (EquipmentType::exist_name($_POST[name]) == true and $equipment_type->get_name() != $_POST[name])
						{
							$page_1_passed = false;
							$error = "This name already exists";
						}
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must enter a name or a manufacturer";
				}
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
	
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("equipment/admin/equipment_type/rename.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
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

				if ($_POST[manufacturer])
				{
					$template->set_var("manufacturer", $_POST[manufacturer]);
				}
				else
				{
					if ($equipment_type->get_manufacturer())
					{
						$template->set_var("manufacturer", $equipment_type->get_manufacturer());
					}
					else
					{
						$template->set_var("manufacturer", "");
					}
				}
				
				if ($_POST[name])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					if ($equipment_type->get_internal_name())
					{
						$template->set_var("name", $equipment_type->get_internal_name());
					}
					else
					{
						$template->set_var("name", "");
					}
				}
							
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[nextpage]);
				$paramquery[action] = "detail";
				$params = http_build_query($paramquery);
				
				if ($equipment_type->set_name($_POST[name]) and $equipment_type->set_manufacturer($_POST[manufacturer]))
				{
					Common_IO::step_proceed($params, "Rename Equipment Type", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Rename Equipment Type", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function add_user()
	{
		if ($_GET[id])
		{			
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[user]))
				{
					$equipment_type = new EquipmentType($_GET[id]);
					if ($equipment_type->is_user_responsible($_POST[user]) == true)
					{
						$page_1_passed = false;
						$error = "The user is already responsible for this equipment.";
					}
					else
					{
						$page_1_passed = true;
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must select an user.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("equipment/admin/equipment_type/add_user.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$user_array = User::list_entries();
					
				$result = array();
				$counter = 0;
				
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $user->get_username()." (".$user->get_full_name(false).")";
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$equipment_type = new EquipmentType($_GET[id]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($equipment_type->add_responsible_person($_POST[user]))
				{
					Common_IO::step_proceed($params, "Equipment Type", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Equipment Type", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	/**
	 * @todo create Exception for missing (user) id (or rebuild)
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function delete_user()
	{
		if ($_GET[id])
		{
			if ($_GET[key])
			{
				if ($_GET[sure] != "true")
				{
					$template = new HTMLTemplate("equipment/admin/equipment_type/delete_user.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery[key]);
					$paramquery[action] = "detail";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[key]);
					unset($paramquery[sure]);
					$paramquery[action] = "detail";
					$params = http_build_query($paramquery);
					
					$equipment_type = new EquipmentType($_GET[id]);	
							
					if ($equipment_type->delete_responsible_person($_GET[key]))
					{							
						Common_IO::step_proceed($params, "Equipment Type", "Operation Successful" ,null);
					}
					else
					{							
						Common_IO::step_proceed($params, "Equipment Type", "Operation Failed" ,null);
					}			
				}
			}
			else
			{
				// error
			}
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function add_organisation_unit()
	{
		if ($_GET[id])
		{			
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[ou]))
				{
					$equipment_type = new EquipmentType($_GET[id]);
					if ($equipment_type->is_organisation_unit($_POST[ou]) == true)
					{
						$page_1_passed = false;
						$error = "The organisation units is already connected with this equipment.";
					}
					else
					{
						$page_1_passed = true;
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "You must select an organisation unit.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("equipment/admin/equipment_type/add_organisation_unit.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("error",$error);
				
				$organisation_unit_array = OrganisationUnit::list_entries();
					
				$result = array();
				$counter = 0;
				
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $organisation_unit->get_name();
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$equipment_type = new EquipmentType($_GET[id]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($equipment_type->add_organisation_unit($_POST[ou]))
				{
					Common_IO::step_proceed($params, "Add Organisation Unit", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Add Organisation Unit", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	/**
	 * @todo create Exception for missing (user) id (or rebuild)
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function delete_organisation_unit()
	{
		if ($_GET[id])
		{
			if ($_GET[key])
			{
				if ($_GET[sure] != "true")
				{
					$template = new HTMLTemplate("equipment/admin/equipment_type/delete_organisation_unit.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery[key]);
					$paramquery[action] = "detail";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[key]);
					unset($paramquery[sure]);
					$paramquery[action] = "detail";
					$params = http_build_query($paramquery);
					
					$equipment_type = new EquipmentType($_GET[id]);
							
					if ($equipment_type->delete_organisation_unit($_GET[key]))
					{							
						Common_IO::step_proceed($params, "Delete Organisation Unit", "Operation Successful" ,null);
					}
					else
					{							
						Common_IO::step_proceed($params, "Delete Organisation Unit", "Operation Failed" ,null);
					}			
				}
			}
			else
			{
				// error
			}
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function change_location()
	{
		if ($_GET[id])
		{
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
			}
			
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("equipment/admin/equipment_type/change_location.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$location_array = Location::list_entries();
					
				$result = array();
				$counter = 1;
				
				$location = new Location($value);
				$result[0][value] = 0;
				$result[0][content] = "none";
				
				foreach($location_array as $key => $value)
				{
					$location = new Location($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $location->get_name(true);
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$template->output();
			}
			else
			{
				$equipment_type = new EquipmentType($_GET[id]);
				
				$paramquery = $_GET;
				$paramquery[action] = "detail";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($equipment_type->set_location_id($_POST[location]))
				{
					Common_IO::step_proceed($params, "Equipment Type", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Equipment Type", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
	
	public static function handler()
	{
		switch($_GET[action]):
			case "add":
			case "add_child":
				self::create();
			break;
			
			case "delete":
				self::delete();
			break;
			
			case "detail":
				self::detail();
			break;
			
			case "rename":
				self::rename();
			break;
			
			case "change_location":
				self::change_location();
			break;
			
			case "add_user":
				self::add_user();
			break;

			case "delete_user":
				self::delete_user();
			break;
			
			case "add_ou":
				self::add_organisation_unit();
			break;

			case "delete_ou":
				self::delete_organisation_unit();
			break;
			
			default:
				self::home();
			break;
		endswitch;
	}
	
}

?>