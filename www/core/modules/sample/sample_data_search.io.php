<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * Sample Data Search IO Class
 * @package sample
 */
class SampleDataSearchIO
{
	public static function get_description($language_id)
	{
		return "Finds Data in Samples uncoupled to Folder-structure.";
	}
	
	public static function get_icon()
	{
		return "images/icons_large/data_search_50.png";
	}
	
	public static function search()
	{
		global $user, $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				if ($_GET[nextpage] == "2" and $_POST[string])
				{
					$string = $_POST[string];
					$item_type_array = $session->read_value("SEARCH_DATA_ITEM_TYPE");
					$sample_id_array = $session->read_value("SEARCH_DATA_SAMPLE_ID");
				}
				else
				{
					$string = $session->read_value("SEARCH_DATA_STRING");
					$item_type_array = $session->read_value("SEARCH_DATA_ITEM_TYPE");
					$sample_id_array = $session->read_value("SEARCH_DATA_SAMPLE_ID");
				}
			}
			else
			{
				if ($_GET[page])
				{
					$string = $session->read_value("SEARCH_DATA_STRING");
					$item_type_array = $session->read_value("SEARCH_DATA_ITEM_TYPE");
					$sample_id_array = $session->read_value("SEARCH_DATA_SAMPLE_ID");
				}
				else
				{
					if ($_GET[nextpage] == "1")
					{
						$string = $_POST[string];
						$session->delete_value("SEARCH_DATA_STRING");
						$session->delete_value("SEARCH_DATA_ITEM_TYPE");
						$session->delete_value("SEARCH_DATA_SAMPLE_ID");
					}
					else
					{
						$string = $_POST[string];
						$item_type_array = $session->read_value("SEARCH_DATA_ITEM_TYPE");
						$sample_id_array = $session->read_value("SEARCH_DATA_SAMPLE_ID");
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
			$template = new Template("template/samples/search/data_search.html");
			
			$template->set_var("error", "");
			
			$paramquery = $_GET;
			unset($paramquery[page]);
			$paramquery[nextpage] = "1";
			$params = http_build_query($paramquery,'','&#38;');
					
			$template->set_var("params",$params);
			
			$result = array();
			$counter = 0;
							
			$sample_array = Sample::list_user_related_samples($user->get_user_id(), false);
			
			if (is_array($sample_array))
			{
				foreach($sample_array as $key => $value)
				{
					$sample = new Sample($value);
				
					$result[$counter][value] = $value;
					$result[$counter][content] = $sample->get_name();		
					$result[$counter][selected] = "";
		
					$counter++;
				}
			}
			
			$template->set_var("sample_array",$result);
			
			
			$result = array();
			$counter = 0;
			$item_type_array = Item::list_types();
			
			if (is_array($item_type_array))
			{
				foreach($item_type_array as $key => $value)
				{
					if (class_exists($value))
					{
						if ($value::get_sql_select_array($key) != null)
						{
							$result[$counter][title] = $value::get_generic_name($key, null);
							$result[$counter][name] = "item-".$key;
							$result[$counter][value] = $key;
							$result[$counter][checked] = "checked='checked'";
							
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
			if(!$sample_id_array)
			{	
				if ($_POST[sample_id] == 0)
				{
					$sample_id_array = array();
					$tmp_id_array = Sample::list_user_related_samples($user->get_user_id(), false);
					if (is_array($tmp_id_array) and count($tmp_id_array) >= 1)
					{
						foreach($tmp_id_array as $key => $value)
						{
							array_push($sample_id_array, $value);
						}
					}
				}
				else
				{
					$sample_id_array = array();
					$sample_id_array[0] = $_POST[sample_id];
					$sample = new Sample($_POST[sample_id]);
					$search_name = $sample->get_name();
				}
			}
			else
			{
				if (count($sample_id_array) == 1)
				{
					$sample = new Sample($sample_id_array[0]);
					$search_name = $sample->get_name();
				}
				else
				{
					$search_name = "All";
				}
			}
			
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
			
			$session->write_value("SEARCH_DATA_STRING", $string, true);
			$session->write_value("SEARCH_DATA_ITEM_TYPE", $item_type_array, true);
			$session->write_value("SEARCH_DATA_SAMPLE_ID", $sample_id_array, true);

			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_data_search($string, $sample_id_array, $item_type_array, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = Sample_Wrapper::list_data_search($string, $sample_id_array, $item_type_array, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_data_search($string, $sample_id_array, $item_type_array, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = Sample_Wrapper::list_data_search($string, $sample_id_array, $item_type_array, null, null, 0, 20);
				}	
			}
											
			$list = new List_IO(Sample_Wrapper::count_data_search($string, $sample_id_array, $item_type_array), 20);
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				$item_type_array = Item::list_types();
				
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
					$sample_paramquery = array();
					$sample_paramquery[username] = $_GET[username];
					$sample_paramquery[session_id] = $_GET[session_id];
					$sample_paramquery[nav] = "sample";
					$sample_paramquery[run] = "detail";
					$sample_paramquery[sample_id] = $value[sample_id];
					$sample_params = http_build_query($sample_paramquery, '', '&#38;');
					
					$tmp_sample_name = $result_array[$key][sample_name];
					unset($result_array[$key][sample_name]);
					$result_array[$key][sample_name][content] = $tmp_sample_name;
					$result_array[$key][sample_name][link] = $sample_params;
					
					if (is_array($item_type_array) and count($item_type_array) >= 1)
					{
						foreach($item_type_array as $item_key => $item_value)
						{
							if($value[$item_key."_id"] != null)
							{
								$result_array[$key][type] = $item_value::get_generic_name($item_key, null);
								
								$tmp_item_name = $result_array[$key][name];
								unset($result_array[$key][name]);
								$result_array[$key][name][content] = $tmp_item_name;
								$result_array[$key][name][link] = $item_value::get_generic_link($item_key, $value[$item_key."_id"]);
								
								$result_array[$key][symbol][content] = $item_value::get_generic_symbol($item_key, $value[$item_key."_id"]);
								$result_array[$key][symbol][link] = $item_value::get_generic_link($item_key, $value[$item_key."_id"]);
							}
						}
					}
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}

			$list->add_row("", "symbol", false, "16px");
			$list->add_row("Name", "name", true, null);
			$list->add_row("Type", "type", false, null);
			$list->add_row("Datetime", "datetime", true, null);
			$list->add_row("Sample", "sample_name", true, null);
			
			$template = new Template("template/samples/search/data_search_result.html");
		
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("string", $string);
				
			$template->set_var("table", $list->get_list($result_array, $_GET[page]));		
	
			$template->output();
		}
	}
}