<?php
/**
 * @package item
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
 * Item Request Class
 * @package item
 */
class ItemRequest
{
	public static function ajax_handler()
	{
		switch($_GET[run]):
			
			// Search
			
			case "search_fulltext_list_items":
				require_once("ajax/item_fulltext_search.ajax.php");
				echo ItemFulltextSearchAjax::list_items($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "search_fulltext_count_items":
				require_once("ajax/item_fulltext_search.ajax.php");
				echo ItemFulltextSearchAjax::count_items($_POST[argument_array]);
			break;

		endswitch;
	}
	
	public static function io_handler()
	{
		
	}
}
?>