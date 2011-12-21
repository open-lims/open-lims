<?php
/**
 * @package equipment
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
 * Equipment IO Class
 * @package equipment
 */
class EquipmentIO
{
	public static function add_equipment_item($type_array, $category_array, $organisation_unit_id, $folder_id)
	{
		global $user, $project_security;
		
		if ($_GET[nextpage] == "2")
		{
			if (!is_numeric($_POST[type_id]))
			{
				$page_2_passed = false;
			}
			else
			{
				$page_2_passed = true;
			}
		}
		else
		{
			$page_2_passed = false;
		}
		
		if ($page_2_passed == false)
		{
			$equipment_array = EquipmentType::list_entries();
		
			$template = new HTMLTemplate("equipment/add.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = 2;
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params",$params);
			
			$result = array();
			$hit_array = array();
			$counter = 0;
			
			if (is_array($type_array) and count($type_array) >= 1)
			{
				if (is_array($equipment_array) and count($equipment_array) >= 1)
				{
					foreach($equipment_array as $key => $value)
					{
						if (in_array($value, $type_array))
						{
							$equipment_type = new EquipmentType($value);
						
							$result[$counter][value] = $value;
							$result[$counter][content] = $equipment_type->get_name()." (".$equipment_type->get_cat_name().")";
							
							$counter++;
							array_push($hit_array, $value);
						}
					}
				}
				
				if (is_array($category_array) and count($category_array) >= 1)
				{
					foreach ($category_array as $key => $value)
					{
						$equipment_cat_array = EquipmentType::list_entries_by_cat_id($value);
						
						if (is_array($equipment_cat_array) and count($equipment_cat_array) >= 1)
						{
							foreach ($equipment_cat_array as $key => $value)
							{
								if (!in_array($value, $hit_array))
								{
									$equipment_type = new EquipmentType($value);
							
									$result[$counter][value] = $value;
									$result[$counter][content] = $equipment_type->get_name()." (".$equipment_type->get_cat_name().")";
									
									$counter++;
									array_push($hit_array, $value);
								}
							} 
						}
					}
				}
			}
			else
			{
				if (is_array($category_array) and count($category_array) >= 1)
				{
					foreach ($category_array as $key => $value)
					{
						$equipment_cat_array = EquipmentType::list_entries_by_cat_id($value);
						
						if (is_array($equipment_cat_array) and count($equipment_cat_array) >= 1)
						{
							foreach ($equipment_cat_array as $key => $value)
							{
								if (!in_array($value, $hit_array))
								{
									$equipment_type = new EquipmentType($value);
							
									$result[$counter][value] = $value;
									$result[$counter][content] = $equipment_type->get_name()." (".$equipment_type->get_cat_name().")";
									
									$counter++;
									array_push($hit_array, $value);
								}
							} 
						}
					}
				}
				
				if (is_array($equipment_array) and count($equipment_array) >= 1)
				{
					foreach($equipment_array as $key => $value)
					{
						$equipment_type = new EquipmentType($value);
						
						$result[$counter][value] = $value;
						$result[$counter][content] = $equipment_type->get_name()." (".$equipment_type->get_cat_name().")";
						
						$counter++;
					}
				}
			}

			if ($counter == 0)
			{
				$result[0][value] = "0";
				$result[0][content] = "NO EQUIPMENT FOUND!";	
			}
			
			$template->set_var("select",$result);
			
			if ($_POST[keywords])
			{
				$template->set_var("keywords", $_POST[keywords]);
			}
			else
			{
				$template->set_var("keywords", "");
			}
			
			if ($_POST[description])
			{
				$template->set_var("description", $_POST[description]);
			}
			else
			{
				$template->set_var("description", "");	
			}
			
			$template->output();
		}
		else
		{
			$equipment = new Equipment($_POST[equipment_id]);

			$equipment_add_successful = $equipment->create($_POST[type_id], $user->get_user_id());

			if ($equipment_add_successful)
			{
				return $equipment->get_item_id();
			}
			else
			{
				return false;
			}
		}
		return null;
	}
		
	/**
	 * @param string $sql
	 */
	public static function list_equipment_item_handler($item_holder_type, $item_holder_id, $in_page = true)
	{
		switch ($_GET[action]):
		
			case "detail":
				self::detail();
			break;
			
			default:
				self::list_equipment_items($item_holder_type, $item_holder_id, $in_page, false);
			break;
		
		endswitch;
	}

	/**
	 * @todo error on missing $sql
	 * @param string $sql
	 */
	public static function list_equipment_items($item_holder_type, $item_holder_id, $as_page = true, $in_assistant = false, $form_field_name = null)
	{	
		if ($GLOBALS['autoload_prefix'])
		{
			$path_prefix = $GLOBALS['autoload_prefix'];
		}
		else
		{
			$path_prefix = "";
		}
		
		$handling_class = Item::get_holder_handling_class_by_name($item_holder_type);
		if ($handling_class)
		{
			$sql = $handling_class::get_item_list_sql($item_holder_id);
		}
		
		$argument_array = array();
		$argument_array[0][0] = "item_holder_type";
		$argument_array[0][1] = $item_holder_type;
		$argument_array[1][0] = "item_holder_id";
		$argument_array[1][1] = $item_holder_id;
		$argument_array[2][0] = "as_page";
		$argument_array[2][1] = $as_page;
		$argument_array[3][0] = "in_assistant";
		$argument_array[3][1] = $in_assistant;

		if ($in_assistant == false)
		{
			$list = new List_IO("EquipmentItem", "ajax.php?nav=equipment", "list_equipment_items", "count_equipment_items", $argument_array, "EquipmentAjax", 20, true, true);
			
			$template = new HTMLTemplate("equipment/list.html");
			
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Equipment Name","name",true,null);
			$list->add_column("Category","category",true,null);
			$list->add_column("Date/Time","datetime",true,null);
		}
		else
		{
			$list = new List_IO("EquipmentItem", "ajax.php?nav=equipment", "list_equipment_items", "count_equipment_items", $argument_array, "EquipmentAjax", 20, false, false);
			
			$template = new HTMLTemplate("equipment/list_without_border.html");
			
			$list->add_column("","checkbox",false,"16px", $form_field_name);
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Equipment Name","name",false,null);
			$list->add_column("Category","category",false,null);
			$list->add_column("Date/Time","datetime",false,null);
		}
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	/**
	 * @param string $sql
	 */
	public static function list_organisation_unit_related_equipment_handler()
	{
		switch ($_GET[action]):
		
			case "detail":
				self::type_detail($_GET[id], null);
			break;
			
			default:
				self::list_organisation_unit_related_equipment();
			break;
		
		endswitch;
	}
	
	public static function list_organisation_unit_related_equipment()
	{
		if ($_GET[ou_id])
		{
			$list = new ListStat_IO(Equipment_Wrapper::count_organisation_unit_equipments($_GET[ou_id]), 20);

			$list->add_column("","symbol",false,16);
			$list->add_column("Equipment Name","name",true,null);
			$list->add_column("Category","category",true,null);
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Equipment_Wrapper::list_organisation_unit_equipments($_GET[ou_id], $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = Equipment_Wrapper::list_organisation_unit_equipments($_GET[ou_id], null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Equipment_Wrapper::list_organisation_unit_equipments($_GET[ou_id], $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = Equipment_Wrapper::list_organisation_unit_equipments($_GET[ou_id], null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$paramquery = $_GET;
					$paramquery[action] = "detail";
					$paramquery[id] = $result_array[$key][id];
					$params = http_build_query($paramquery,'','&#38;');
					
					$result_array[$key][symbol][link]		= $params;
					$result_array[$key][symbol][content] 	= "<img src='images/icons/equipment.png' alt='N' border='0' />";
				
					if ($result_array[$key][organisation_unit_id] != $_GET[ou_id])
					{
						$equipment_name = $result_array[$key][name];
						unset($result_array[$key][name]);
						$result_array[$key][name][link] 		= $params;
						$result_array[$key][name][content]		= $equipment_name." (CH)";
					}
					else
					{
						$equipment_name = $result_array[$key][name];
						unset($result_array[$key][name]);
						$result_array[$key][name][link] 		= $params;
						$result_array[$key][name][content]		= $equipment_name;
					}
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			$template = new HTMLTemplate("equipment/list_organisation_unit.html");

			$template->set_var("table", $list->get_list($result_array, $_GET[page]));
			
			$template->output();
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentIDMissingException
	 */
	public static function detail()
	{
		if ($_GET[id])
		{
			$equipment = new Equipment($_GET[id]);
			self::type_detail($equipment->get_type_id(), $equipment->get_owner_id());
		}
		else
		{
			throw new EquipmentIDMissingException();
		}
	}
	
	/**
	 * @throws EquipmentTypeIDMissingException
	 */
	public static function type_detail($type_id, $owner_id)
	{
		if (is_numeric($type_id))
		{
			$equipment_type = new EquipmentType($type_id);
			$equipment_owner = new User($owner_id);
						
			$template = new HTMLTemplate("equipment/detail.html");

			$template->set_var("name", $equipment_type->get_name());
			$template->set_var("category", $equipment_type->get_cat_name());
			
			if ($equipment_type->get_location_id() == null)
			{
				$template->set_var("location", "<span class='italic'>none</span>");
			}
			else
			{
				$location = new Location($equipment_type->get_location_id());
				$template->set_var("location", $location->get_name(true));
			}
			
			$template->set_var("owner", $equipment_owner->get_full_name(false));
			
			if ($equipment_type->get_description())
			{
				$template->set_var("description", $equipment_type->get_description());
			}
			else
			{
				$template->set_var("description", "<span class='italic'>none</span>");
			}
			
			$user_array = $equipment_type->list_users();
			$user_content_array = array();
			
			$counter = 0;
			
			if (is_array($user_array) and count($user_array) >= 1)
			{
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					$user_content_array[$counter][username] = $user->get_username();
					$user_content_array[$counter][fullname] = $user->get_full_name(false);
					$counter++;
				}
				$template->set_var("no_user", false);
			}
			else
			{
				$template->set_var("no_user", true);
			}
			
			$template->set_var("user", $user_content_array);
			
			
			$ou_array = $equipment_type->list_organisation_units();
			$ou_content_array = array();
			
			$counter = 0;
			
			if (is_array($ou_array) and count($ou_array) >= 1)
			{
				foreach($ou_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
					
					$ou_content_array[$counter][name] = $organisation_unit->get_name();
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
		else
		{
			throw new EquipmentTypeIDMissingException();
		}
	}
}
?>

