<?php
/**
 * @package project
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
 * Project Data Search IO Class
 * @package project
 */
class ProjectDataSearchIO
{
	public static function get_description($language_id)
	{
		return "Finds Data in Projects uncoupled to Folder-structure.";
	}
	
	public static function get_icon()
	{
		return "images/icons_large/data_search_50.png";
	}
	
	/**
	 * @todo use SQL-Join for permission check
	 * @todo project
	 */
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
					$type = $session->read_value("SEARCH_DATA_TYPE");
					$id_array = $session->read_value("SEARCH_DATA_ID_ARRAY");
				}
				else
				{
					$string = $session->read_value("SEARCH_DATA_STRING");
					$type = $session->read_value("SEARCH_DATA_TYPE");
					$id_array = $session->read_value("SEARCH_DATA_ID_ARRAY");
				}
			}
			else
			{
				if ($_GET[page])
				{
					$string = $session->read_value("SEARCH_DATA_STRING");
					$type = $session->read_value("SEARCH_DATA_TYPE");
					$id_array = $session->read_value("SEARCH_DATA_ID_ARRAY");
				}
				else
				{
					if ($_GET[nextpage] == "1")
					{
						$string = $_POST[string];
						$session->delete_value("SEARCH_DATA_STRING");
						$session->delete_value("SEARCH_DATA_TYPE");
						$session->delete_value("SEARCH_DATA_ID_ARRAY");
					}
					else
					{
						$string = $_POST[string];
						$type = $session->read_value("SEARCH_DATA_TYPE");
						$id_array = $session->read_value("SEARCH_DATA_ID_ARRAY");
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
			$template = new Template("languages/en-gb/template/search/data_search.html");
			
			$template->set_var("error", "");
			
			$paramquery = $_GET;
			unset($paramquery[page]);
			$paramquery[nextpage] = "1";
			$params = http_build_query($paramquery,'','&#38;');
					
			$template->set_var("params",$params);
			
			// Projects
			
			$result = array();
			$counter = 0;
							
			$project = new Project(null);
			$project_array = Project::list_user_related_projects($user->get_user_id(), false);
			
			if (is_array($project_array))
			{
				foreach($project_array as $key => $value)
				{
					$project = new Project($value);
					$organisation_unit = new OrganisationUnit($project->get_organisation_unit_id());
				
					$result[$counter][value] = $value;
					$result[$counter][content] = $project->get_name()." (".$organisation_unit->get_name().")";		
					$result[$counter][selected] = "";
		
					$counter++;
				}
			}
	
			$template->set_array("project",$result);
			
			
			// Samples
			
			$result = array();
			$counter = 0;
							
			$sample_array = Sample::list_user_related_samples($user->get_user_id());
			
			if (is_array($sample_array))
			{
				foreach($sample_array as $key => $value)
				{
					$sample = new Sample($value);
					$organisation_unit = new OrganisationUnit($sample->get_organisation_unit_id());
			
					$result[$counter][value] = $value;
					$result[$counter][content] = $sample->get_name()." (".$organisation_unit->get_name().")";				
					$result[$counter][selected] = "";
		
					$counter++;
				}
			}
	
			$template->set_array("sample",$result);
			
			$template->output();
		}
		else
		{
			if(!$id_array)
			{			
				if (isset($_POST[project_id]))
				{
					if ($_POST[project_id] == 0)
					{
						$id_array = array();
						$tmp_id_array = Project::list_user_related_projects($user->get_user_id(), false);
						foreach($tmp_id_array as $key => $value)
						{
							array_push($id_array, $value);
						}
					}
					else
					{
						$id_array = array();
						$id_array[0] = $_POST[project_id];
						$project = new Project($_POST[project_id]);
						$search_name = $project->get_name();
					}
					$type = "project";
				}
				elseif(isset($_POST[sample_id]))
				{
					if ($_POST[sample_id] == 0) 
					{
						$id_array = Sample::list_user_related_samples($user->get_user_id());
					}
					else
					{
						$id_array = array();
						$id_array[0] = $_POST[sample_id];
						$sample = new Sample($_POST[sample_id]);
						$search_name = $sample->get_name();
					}
					$type = "sample";
				}
			}
			else
			{
				if ($type == "project")
				{
					if (count($id_array) == 1)
					{
						$project = new Project($id_array[0]);
						$search_name = $project->get_name();
					}
					else
					{
						$search_name = "All";
					}
				}
				elseif($type == "sample")
				{
					if (count($id_array) == 1)
					{
						$project = new Sample($id_array[0]);
						$search_name = $sample->get_name();
					}
					else
					{
						$search_name = "All";
					}
				}
			}
			
			$session->write_value("SEARCH_DATA_STRING", $string, true);
			$session->write_value("SEARCH_DATA_TYPE", $type, true);
			$session->write_value("SEARCH_DATA_ID_ARRAY", $id_array, true);
			
			if ($type == "project")
			{
				if ($_GET[page])
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = DataSearch_Wrapper::search_project_data($id_array, $string, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
					}
					else
					{
						$result_array = DataSearch_Wrapper::search_project_data($id_array, $string, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
					}				
				}
				else
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = DataSearch_Wrapper::search_project_data($id_array, $string, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
					}
					else
					{
						$result_array = DataSearch_Wrapper::search_project_data($id_array, $string, null, null, 0, 20);
					}	
				}
												
				$list = new List_IO(DataSearch_Wrapper::count_search_project_data($id_array, $string), 20);
				
				if (is_array($result_array) and count($result_array) >= 1)
				{
					foreach($result_array as $key => $value)
					{
						$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
						$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
						
						$proejct_paramquery = array();
						$project_paramquery[username] = $_GET[username];
						$project_paramquery[session_id] = $_GET[session_id];
						$project_paramquery[nav] = "project";
						$project_paramquery[run] = "detail";
						$project_paramquery[project_id] = $value[project_id];
						$project_params = http_build_query($project_paramquery, '', '&#38;');
						
						$tmp_project_name = $result_array[$key][project_name];
						unset($result_array[$key][project_name]);
						$result_array[$key][project_name][content] = $tmp_project_name;
						$result_array[$key][project_name][link] = $project_params;
						
						if (is_numeric($value[file_id]))
						{
							$item_paramquery = $_GET;
							$item_paramquery[nav] = "file";
							$item_paramquery[run] = "detail";
							$item_paramquery[file_id] = $value[file_id];
							unset($item_paramquery[sortvalue]);
							unset($item_paramquery[sortmethod]);
							unset($item_paramquery[nextpage]);
							$item_params = http_build_query($item_paramquery, '', '&#38;');
							
							$tmp_name = $value[name];
							unset($result_array[$key][name]);
							$result_array[$key][name][content] = $tmp_name;
							$result_array[$key][name][link] = $item_params;
							
							$result_array[$key][symbol][content] = "<img src='".File::get_icon_by_name($value[name])."' alt='' style='border: 0;'>";
							$result_array[$key][symbol][link] = $item_params;
							
							$result_array[$key][type] = "File";
						}
						
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
						
						if (is_numeric($value[method_id]))
						{
							$result_array[$key][symbol] = "<img src='images/icons/method.png' alt='' style='border: 0;'>";
							$result_array[$key][type] = "Method";
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
				$list->add_row("Name", "name", true, null);
				$list->add_row("Type", "type", false, null);
				$list->add_row("Datetime", "datetime", true, null);
				$list->add_row("Project", "project_name", true, null);
				
				// print_r($result_array);
				
				$template = new Template("languages/en-gb/template/search/data_search_result.html");
			
				$paramquery = $_GET;
				$paramquery[nextpage] = "2";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params", $params);
				
				$template->set_var("string", $string);
					
				$template->set_var("table", $list->get_list($result_array, $_GET[page]));		
		
				$template->output();
			}
			elseif($type == "sample")
			{
				if ($_GET[page])
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = DataSearch_Wrapper::search_sample_data($id_array, $string, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
					}
					else
					{
						$result_array = DataSearch_Wrapper::search_sample_data($id_array, $string, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
					}				
				}
				else
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = DataSearch_Wrapper::search_sample_data($id_array, $string, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
					}
					else
					{
						$result_array = DataSearch_Wrapper::search_sample_data($id_array, $string, null, null, 0, 20);
					}	
				}
							
				$list = new List_IO(DataSearch_Wrapper::count_search_sample_data($id_array, $string), 20);
				
				if (is_array($result_array) and count($result_array) >= 1)
				{
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
						
						if (is_numeric($value[file_id]))
						{
							$item_paramquery = $_GET;
							$item_paramquery[nav] = "file";
							$item_paramquery[run] = "detail";
							$item_paramquery[file_id] = $value[file_id];
							unset($item_paramquery[sortvalue]);
							unset($item_paramquery[sortmethod]);
							unset($item_paramquery[nextpage]);
							$item_params = http_build_query($item_paramquery, '', '&#38;');
							
							$tmp_name = $value[name];
							unset($result_array[$key][name]);
							$result_array[$key][name][content] = $tmp_name;
							$result_array[$key][name][link] = $item_params;
							
							$result_array[$key][symbol][content] = "<img src='".File::get_icon_by_name($value[name])."' alt='' style='border: 0;'>";
							$result_array[$key][symbol][link] = $item_params;
							
							$result_array[$key][type] = "File";
						}
						
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
						
						if (is_numeric($value[method_id]))
						{
							$result_array[$key][symbol] = "<img src='images/icons/method.png' alt='' style='border: 0;'>";
							$result_array[$key][type] = "Method";
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
				$list->add_row("Name", "name", true, null);
				$list->add_row("Type", "type", false, null);
				$list->add_row("Datetime", "datetime", true, null);
				$list->add_row("Sample", "sample_name", true, null);
				
				// print_r($result_array);
				
				$template = new Template("languages/en-gb/template/search/data_search_result.html");
			
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
}
