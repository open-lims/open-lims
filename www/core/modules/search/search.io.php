<?php
/**
 * @package base
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
 * Search IO Class
 * @package base
 * @todo split class due to dependencies
 */
class SearchIO
{
	private static function main()
	{
		$template = new Template("languages/en-gb/template/search/main.html");
		
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("search");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			$counter = 0;
			$search_array = array();
			
			foreach ($module_dialog_array as $key => $value)
			{
				require_once($value[class_path]);
				
				$paramquery[username] 	= $_GET[username];
				$paramquery[session_id] = $_GET[session_id];
				$paramquery[nav]		= "search";
				$paramquery[run]		= "search";
				$paramquery[dialog]		= $value[internal_name];
				$params 				= http_build_query($paramquery,'','&#38;');
				
				$search_array[$counter][params] = $params;
				$search_array[$counter][title] = $value[display_name];
				$search_array[$counter][icon] = $value['class']::get_icon();
				$search_array[$counter][description] = $value['class']::get_description(null);
				$counter++;
			}
			
			$template->set_var("search_array", $search_array);
		}

		$template->output();		
	}
	
	
	
	private static function full_text_search()
	{
		global $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				if ($_GET[nextpage] == "2" and $_POST[string])
				{
					$string = $_POST[string];
					$values = $session->read_value("SEARCH_FULL_TEXT_VALUES");
					$project_descriptions = $session->read_value("SEARCH_FULL_TEXT_PROJECT_DESCRIPTIONS");
					$sample_descriptions = $session->read_value("SEARCH_FULL_TEXT_SAMPLE_DESCRIPTIONS");
					$items = $session->read_value("SEARCH_FULL_TEXT_ITEMS");				
				}
				else
				{
					$string = $session->read_value("SEARCH_FULLTEXT_STRING");
					$values = $session->read_value("SEARCH_FULL_TEXT_VALUES");
					$project_descriptions = $session->read_value("SEARCH_FULL_TEXT_PROJECT_DESCRIPTIONS");
					$sample_descriptions = $session->read_value("SEARCH_FULL_TEXT_SAMPLE_DESCRIPTIONS");
					$items = $session->read_value("SEARCH_FULL_TEXT_ITEMS");	
				}
			}
			else
			{
				if ($_GET[page])
				{
					$string = $session->read_value("SEARCH_FULLTEXT_STRING");
					$values = $session->read_value("SEARCH_FULL_TEXT_VALUES");
					$project_descriptions = $session->read_value("SEARCH_FULL_TEXT_PROJECT_DESCRIPTIONS");
					$sample_descriptions = $session->read_value("SEARCH_FULL_TEXT_SAMPLE_DESCRIPTIONS");
					$items = $session->read_value("SEARCH_FULL_TEXT_ITEMS");	
				}
				else
				{
					if ($_GET[nextpage] == "1")
					{
						$string = $_POST[string];
						$session->delete_value("SEARCH_FULL_TEXT_VALUES");
						$session->delete_value("SEARCH_FULL_TEXT_PROJECT_DESCRIPTIONS");
						$session->delete_value("SEARCH_FULL_TEXT_SAMPLE_DESCRIPTIONS");
						$session->delete_value("SEARCH_FULL_TEXT_ITEMS");
					}
					else
					{
						$string = $_POST[string];
						$values = $session->read_value("SEARCH_FULL_TEXT_VALUES");
						$project_descriptions = $session->read_value("SEARCH_FULL_TEXT_PROJECT_DESCRIPTIONS");
						$sample_descriptions = $session->read_value("SEARCH_FULL_TEXT_SAMPLE_DESCRIPTIONS");
						$items = $session->read_value("SEARCH_FULL_TEXT_ITEMS");
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
			$template = new Template("languages/en-gb/template/search/full_text_search.html");
			
			$paramquery = $_GET;
			unset($paramquery[page]);
			$paramquery[nextpage] = "1";
			$params = http_build_query($paramquery,'','&#38;');
					
			$template->set_var("params",$params);
			
			$template->set_var("error", "");
			
			$template->output();
		}
		else
		{
			if (!$values)
			{
				$values = $_POST[values];
			}
			
			if (!$project_descriptions)
			{
				$project_descriptions = $_POST[project_descriptions];
			}
			
			if (!$sample_descriptions)
			{
				$sample_descriptions = $_POST[sample_descriptions];
			}
			
			if (!$items)
			{
				$items = $_POST[items];
			}
			
			$session->write_value("SEARCH_FULLTEXT_STRING", $string, true);
			$session->write_value("SEARCH_FULL_TEXT_VALUES", $values, true);
			$session->write_value("SEARCH_FULL_TEXT_PROJECT_DESCRIPTIONS", $project_descriptions, true);
			$session->write_value("SEARCH_FULL_TEXT_SAMPLE_DESCRIPTIONS", $sample_descriptions, true);
			$session->write_value("SEARCH_FULL_TEXT_ITEMS", $items, true);	
			
			if ($values == "1")
			{
				$search_values = true;
			}
			else
			{
				$search_values = false;
			}
			
			if ($project_descriptions == "1")
			{
				$search_project_descriptions = true;
			}
			else
			{
				$search_project_descriptions = false;
			}
			
			if ($sample_descriptions == "1")
			{
				$search_sample_descriptions = true;
			}
			else
			{
				$search_sample_descriptions = false;
			}
			
			if ($items == "1")
			{
				$search_items = true;
			}
			else
			{
				$search_items = false;
			}
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = DataSearch_Wrapper::search_fulltext($search_values, $search_project_descriptions, $search_sample_descriptions, $search_items, null, $string, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = DataSearch_Wrapper::search_fulltext($search_values, $search_project_descriptions, $search_sample_descriptions, $search_items, null, $string, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = DataSearch_Wrapper::search_fulltext($search_values, $search_project_descriptions, $search_sample_descriptions, $search_items, null, $string, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = DataSearch_Wrapper::search_fulltext($search_values, $search_project_descriptions, $search_sample_descriptions, $search_items, null, $string, null, null, 0, 20);
				}	
			}
			
			$list = new List_IO(DataSearch_Wrapper::count_search_fulltext(true, true, true, true, null, $string), 20);
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					if (is_numeric($value[value_id]))
					{
						$item_paramquery = $_GET;
						$item_paramquery[nav] = "value";
						$item_paramquery[run] = "detail";
						$item_paramquery[value_id] = $value[value_id];
						unset($item_paramquery[sortvalue]);
						unset($item_paramquery[sortmethod]);
						unset($item_paramquery[nextpage]);
						$item_params = http_build_query($item_paramquery, '', '&#38;');
						
						$tmp_name = $value[name];
						unset($result_array[$key][name]);
						$result_array[$key][name][content] = $tmp_name;
						$result_array[$key][name][link] = $item_params;
						
						$result_array[$key][symbol][content] = "<img src='images/fileicons/16/unknown.png' alt='' style='border: 0;'>";
						$result_array[$key][symbol][link] = $item_params;
						
						$result_array[$key][type] = "Value";
					}
					
					if (is_numeric($value[sample_id]))
					{
						$item_paramquery = $_GET;
						$item_paramquery[nav] = "sample";
						$item_paramquery[run] = "detail";
						$item_paramquery[sample_id] = $value[sample_id];
						unset($item_paramquery[sortvalue]);
						unset($item_paramquery[sortmethod]);
						unset($item_paramquery[nextpage]);
						$item_params = http_build_query($item_paramquery, '', '&#38;');
						
						$tmp_name = $value[name];
						unset($result_array[$key][name]);
						$result_array[$key][name][content] = $tmp_name;
						$result_array[$key][name][link] = $item_params;
						
						$result_array[$key][symbol][content] = "<img src='images/icons/sample.png' alt='' style='border: 0;'>";
						$result_array[$key][symbol][link] = $item_params;
						
						$result_array[$key][type] = "Sample";
					}
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			$list->add_row("", "symbol", false, "16px");
			$list->add_row("ID", "item_id_name", true, null);
			$list->add_row("Name", "name", true, null);
			$list->add_row("Type", "type", false, null);
			$list->add_row("Rank", "rank", true, null);
			
			// print_r($result_array);
			
			$template = new Template("languages/en-gb/template/search/full_text_search_result.html");
		
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("string", $string);
				
			$template->set_var("table", $list->get_list($result_array, $_GET[page]));		
	
			$template->output();
		}
	}
	
	public static function method_handler()
	{	
		switch($_GET[run]):
			
			// Search
			/**
			 * @todo errors, exceptions
			 */
			case("search"):
				if ($_GET[dialog])
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("search", $_GET[dialog]);
					
					if (file_exists($module_dialog[class_path]))
					{
						require_once($module_dialog[class_path]);
						
						if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
						{
							$module_dialog['class']::$module_dialog[method]();
						}
						else
						{
							// Error
						}
					}
					else
					{
						// Error
					}
				}
				else
				{
					// error
				}
			break;
			
			// Common Dialogs
			/**
			 * @todo errors, exceptions
			 */
			case("common_dialog"):
				if ($_GET[dialog])
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("common_dialog", $_GET[dialog]);
					
					if (file_exists($module_dialog[class_path]))
					{
						require_once($module_dialog[class_path]);
						
						if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
						{
							$module_dialog['class']::$module_dialog[method]();
						}
						else
						{
							// Error
						}
					}
					else
					{
						// Error
					}
				}
				else
				{
					// error
				}
			break;
			
			default:
				self::main();
			break;
		endswitch;
	}
	
}
?>
