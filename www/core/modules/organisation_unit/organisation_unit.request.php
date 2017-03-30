<?php
/**
 * @package organisation_unit
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Organisation Unit Request Class
 * @package organisation_unit
 */
class OrganisationUnitRequest
{	
	/**
	 * @param string $alias
	 */
	public static function ajax_handler($alias)
	{
		switch(System::get_get("run")):
	
			case "list_members":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::list_members(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_members":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::count_members(System::get_post("argument_array"));
			break;
			
			case "list_owners":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::list_owners(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_owners":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::count_owners(System::get_post("argument_array"));
			break;
			
			case "list_leaders":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::list_leaders(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_leaders":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::count_leaders(System::get_post("argument_array"));
			break;
			
			case "list_quality_managers":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::list_quality_managers(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_quality_managers":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::count_quality_managers(System::get_post("argument_array"));
			break;
			
			case "list_groups":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::list_groups(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_groups":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::count_groups(System::get_post("argument_array"));
			break;
			
			case "list_organisation_units_by_user_id":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::list_organisation_units_by_user_id(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_organisation_units_by_user_id":
				require_once("ajax/organisation_unit.ajax.php");
				echo OrganisationUnitAjax::count_organisation_units_by_user_id(System::get_post("argument_array"));
			break;
			
			// Admin
			
			case "admin_list_members":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::list_members(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "admin_count_members":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::count_members(System::get_post("argument_array"));
			break;
			
			case "admin_delete_member":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::delete_member(System::get_post("organisation_unit_id"), System::get_post("user_id"));
			break;
			
			case "admin_add_member":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::add_member(System::get_post("organisation_unit_id"), System::get_post("user_id"));
			break;
			
			case "admin_list_owners":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::list_owners(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "admin_count_owners":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::count_owners(System::get_post("argument_array"));
			break;
			
			case "admin_delete_owner":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::delete_owner(System::get_post("organisation_unit_id"), System::get_post("user_id"));
			break;
			
			case "admin_add_owner":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::add_owner(System::get_post("organisation_unit_id"), System::get_post("user_id"));
			break;
			
			case "admin_list_leaders":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::list_leaders(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "admin_count_leaders":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::count_leaders(System::get_post("argument_array"));
			break;
			
			case "admin_delete_leader":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::delete_leader(System::get_post("organisation_unit_id"), System::get_post("user_id"));
			break;
			
			case "admin_add_leader":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::add_leader(System::get_post("organisation_unit_id"), System::get_post("user_id"));
			break;
			
			case "admin_list_quality_managers":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::list_quality_managers(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "admin_count_quality_managers":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::count_quality_managers(System::get_post("argument_array"));
			break;
			
			case "admin_delete_quality_manager":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::delete_quality_manager(System::get_post("organisation_unit_id"), System::get_post("user_id"));
			break;
			
			case "admin_add_quality_manager":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::add_quality_manager(System::get_post("organisation_unit_id"), System::get_post("user_id"));
			break;
			
			case "admin_list_groups":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::list_groups(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod"));
			break;
			
			case "admin_count_groups":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::count_groups(System::get_post("argument_array"));
			break;
			
			case "admin_delete_group":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::delete_group(System::get_post("organisation_unit_id"), System::get_post("group_id"));
			break;
			
			case "admin_add_group":
				require_once("ajax/admin/admin_organisation_unit.ajax.php");
				echo AdminOrganisationUnitAjax::add_group(System::get_post("organisation_unit_id"), System::get_post("group_id"));;
			break;
			
			
			// Navigation
			
			case "navigation":
				require_once("ajax/navigation/organisation_unit_navigation.ajax.php");
				switch(System::get_get("action")):

					case "get_name":
						echo OrganisationUnitNavigationAjax::get_name();
					break;
					
					case "get_html":
						echo OrganisationUnitNavigationAjax::get_html();
					break;
					
					case "get_array":
						echo OrganisationUnitNavigationAjax::get_array();
					break;
					
					case "set_array":
						echo OrganisationUnitNavigationAjax::set_array(System::get_post("array"));
					break;
				
					case "get_children":
						echo OrganisationUnitNavigationAjax::get_children(System::get_post("id"));
					break;
					
				endswitch;
			break;
			
		endswitch;
	}
	
	/**
	 * @param string $alias
	 */
	public static function io_handler($alias)
	{
		
	}
}
?>