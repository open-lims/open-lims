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
 * Item Fulltext Search IO Class
 * @package item
 */
class ItemFulltextSearchIO
{
	/**
	 * @param integer $language_id
	 * @return string
	 */
	public static function get_description($language_id)
	{
		return "Finds Items via Fulltext-Search.";
	}
	
	/**
	 * @return string
	 */
	public static function get_icon()
	{
		return "images/icons_large/fulltext_search_50.png";
	}
	
	public static function search()
	{
		global $session;
		
		if ($_GET['nextpage'])
		{
			if ($_GET['sortvalue'] and $_GET['sortmethod'])
			{
				if ($_GET['nextpage'] == "2" and $_POST['string'])
				{
					$string = $_POST['string'];
					$item_type_array = $session->read_value("SEARCH_FULL_TEXT_ITEM_TYPE");		
				}
				else
				{
					$string = $session->read_value("SEARCH_FULLTEXT_STRING");
					$item_type_array = $session->read_value("SEARCH_FULL_TEXT_ITEM_TYPE");	
				}
			}
			else
			{
				if ($_GET['page'])
				{
					$string = $session->read_value("SEARCH_FULLTEXT_STRING");
					$item_type_array = $session->read_value("SEARCH_FULL_TEXT_ITEM_TYPE");		
				}
				else
				{
					if ($_GET['nextpage'] == "1")
					{
						$string = $_POST['string'];
						$session->delete_value("SEARCH_FULL_TEXT_ITEM_TYPE");
					}
					else
					{
						$string = $session->read_value("SEARCH_FULLTEXT_STRING");
						$item_type_array = $session->read_value("SEARCH_FULL_TEXT_ITEM_TYPE");	
					}
				}
			}
			$no_error = true;
		}
		else
		{
			$no_error = false;
		}
		
		if ($no_error == false)
		{
			$template = new HTMLTemplate("item/search/full_text_search.html");
			
			$paramquery = $_GET;
			unset($paramquery['page']);
			$paramquery['nextpage'] = "1";
			$params = http_build_query($paramquery,'','&#38;');
					
			$template->set_var("params",$params);
			
			$template->set_var("error", "");
			
			
			$result = array();
			$counter = 0;
			$item_type_array = Item::list_types();
			
			if (is_array($item_type_array))
			{
				foreach($item_type_array as $key => $value)
				{
					if (class_exists($value))
					{
						if ($value::get_sql_fulltext_select_array($key) != null)
						{
							$result[$counter]['title'] = $value::get_generic_name($key, null);
							$result[$counter]['name'] = "item-".$key;
							$result[$counter]['value'] = $key;
							$result[$counter]['checked'] = "checked='checked'";
							
							$counter++;
						}
					}
				}
			}
			
			$template->set_var("item_type_array",$result);
			
			
			$template->output();
		}
		else
		{
			if(!$item_type_array)
			{
				$item_type_array = array();
				
				foreach($_POST as $key => $value)
				{
					if (strpos($key, "item-") === 0)
					{
						array_push($item_type_array, $value);
					}
				}
			}
			
			$session->write_value("SEARCH_FULLTEXT_STRING", $string, true);
			$session->write_value("SEARCH_FULL_TEXT_ITEM_TYPE", $item_type_array, true);	

			
			$argument_array = array();
			$argument_array[0][0] = "string";
			$argument_array[0][1] = $string;
			$argument_array[1][0] = "item_type_array";
			$argument_array[1][1] = $item_type_array;
			$argument_array[2][0] = "lanugage_id";
			$argument_array[2][1] = null;
					
			$list = new List_IO("ItemFulltextSearch", "ajax.php?nav=item", "search_fulltext_list_items", "search_fulltext_count_items", $argument_array, "ItemFulltextSearch");

			$list->add_column("", "symbol", false, "16px");
			$list->add_column("Name", "name", true, null);
			$list->add_column("Type", "type", false, null);
			$list->add_column("Datetime", "datetime", true, null);
			$list->add_column("Rank", "rank", true, null);
			
			$template = new HTMLTemplate("item/search/full_text_search_result.html");
		
			$paramquery = $_GET;
			$paramquery['nextpage'] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("string", $string);
				
			$template->set_var("list", $list->get_list());	
	
			$template->output();
		}
	}
}

?>