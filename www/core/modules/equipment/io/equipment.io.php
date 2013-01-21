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
 * Equipment IO Class
 * @package equipment
 */
class EquipmentIO
{		
	/**
	 * @param string $item_holder_type
	 * @param integer $item_holder_id
	 * @param bool $in_page
	 */
	public static function list_equipment_item_handler($item_holder_type, $item_holder_id, $in_page = true)
	{
		switch ($_GET['action']):
		
			case "detail":
				self::detail();
			break;
			
			default:
				self::list_equipment_items($item_holder_type, $item_holder_id, $in_page, false);
			break;
		
		endswitch;
	}

	/**
	 * @param string $item_holder_type
	 * @param integer $item_holder_id
	 * @param bool $as_page
	 * @param bool $in_assistant
	 * @param string $form_field_name
	 * @throws ItemHolderTypeMissingException
	 * @throws ItemHolderIDMissingException
	 */
	public static function list_equipment_items($item_holder_type, $item_holder_id, $as_page = true, $in_assistant = false, $form_field_name = null)
	{
		if (!$item_holder_type)
		{
			throw new ItemHolderTypeMissingException();
		}
		
		if (!is_numeric($item_holder_id))
		{
			throw new ItemHolderIDMissingException();
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
			$list->add_column(Language::get_message("EquipmentGeneralListColumnEquipmentName", "general"),"name",true,null);
			$list->add_column(Language::get_message("EquipmentGeneralListColumnCategory", "general"),"category",true,null);
			$list->add_column(Language::get_message("EquipmentGeneralListColumnDateTime", "general"),"datetime",true,null);
		}
		else
		{
			$list = new List_IO("EquipmentItem", "ajax.php?nav=equipment", "list_equipment_items", "count_equipment_items", $argument_array, "EquipmentAjax", 20, false, false);
			
			$template = new HTMLTemplate("equipment/list_without_border.html");
			
			$list->add_column("","checkbox",false,"16px", $form_field_name);
			$list->add_column("","symbol",false,"16px");
			$list->add_column(Language::get_message("EquipmentGeneralListColumnEquipmentName", "general"),"name",false,null);
			$list->add_column(Language::get_message("EquipmentGeneralListColumnCategory", "general"),"category",false,null);
			$list->add_column(Language::get_message("EquipmentGeneralListColumnDateTime", "general"),"datetime",false,null);
		}
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function list_organisation_unit_related_equipment_handler()
	{
		switch ($_GET['action']):
		
			case "detail":
				self::type_detail($_GET['id'], null);
			break;
			
			default:
				self::list_organisation_unit_related_equipment();
			break;
		
		endswitch;
	}
	
	public static function list_organisation_unit_related_equipment()
	{
		if ($_GET['ou_id'])
		{
			$argument_array = array();
			$argument_array[0][0] = "organisation_unit_id";
			$argument_array[0][1] = $_GET['ou_id'];
					
			$list = new List_IO("EquipmentOrganisationUnit", "ajax.php?nav=equipment", "list_organisation_unit_related_equipment", "count_organisation_unit_related_equipment", $argument_array, "EquipmentOrganisationUnit");
		
			$list->add_column("","symbol",false,16);
			$list->add_column(Language::get_message("EquipmentGeneralListColumnEquipmentName", "general"),"name",true,null);
			$list->add_column(Language::get_message("EquipmentGeneralListColumnCategory", "general"),"category",true,null);
			
			$template = new HTMLTemplate("equipment/list_organisation_unit.html");

			$template->set_var("list", $list->get_list());	
			
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
		if ($_GET['id'])
		{
			$equipment = new Equipment($_GET['id']);
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
					$user_content_array[$counter]['username'] = $user->get_username();
					$user_content_array[$counter]['fullname'] = $user->get_full_name(false);
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
					
					$ou_content_array[$counter]['name'] = $organisation_unit->get_name();
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

