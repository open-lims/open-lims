<?php
/**
 * @package manufacturer
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
 * Manufacturer Request Class
 * @package manufacturer
 */
class ManufacturerRequest
{
	/**
	 * @param string $alias
	 */
	public static function ajax_handler($alias)
	{
		switch(System::get_get("run")):
	
			case "exist_name":
				require_once("ajax/manufacturer.ajax.php");
				echo ManufacturerAjax::exist_name(System::get_post("name"));
			break;
			
			case "add_entry":
				require_once("ajax/manufacturer.ajax.php");
				echo ManufacturerAjax::add_entry(System::get_post("name"));
			break;
		
			case "get_number_of_entries":
				require_once("ajax/manufacturer.ajax.php");
				echo ManufacturerAjax::get_number_of_entries(System::get_post("string"));
			break;
			
			case "get_name":
				require_once("ajax/manufacturer.ajax.php");
				echo ManufacturerAjax::get_name(System::get_post("id"));
			break;
			
			case "get_next_entries":
				require_once("ajax/manufacturer.ajax.php");
				echo ManufacturerAjax::get_next_entries(System::get_post("number"), 
														System::get_post("start"), 
														System::get_post("string"));
			break;
			
			case "list_manufacturers":
				require_once("ajax/manufacturer.ajax.php");
				echo ManufacturerAjax::list_manufacturers(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"),  
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_manufacturers":
				require_once("ajax/manufacturer.ajax.php");
				echo ManufacturerAjax::count_manufacturers();
			break;
			
			case "delete":
				require_once("ajax/manufacturer.ajax.php");
				echo ManufacturerAjax::delete(System::get_post("id"));
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