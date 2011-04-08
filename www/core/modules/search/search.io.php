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
		
		$paramquery = $_GET;
		$paramquery[run] = "project";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("project_search_params", $params);
		
		
		$paramquery = $_GET;
		$paramquery[run] = "sample";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("sample_search_params", $params);
		
		
		$paramquery = $_GET;
		$paramquery[run] = "ffv";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("ffv_search_params", $params);
		
		
		$paramquery = $_GET;
		$paramquery[run] = "data";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("data_search_params", $params);
		
		
		$paramquery = $_GET;
		$paramquery[run] = "user";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("user_search_params", $params);
		
		
		$paramquery = $_GET;
		$paramquery[run] = "full_text";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("full_text_search_params", $params);
		
		
		$template->output();		
	}
	
	/**
	 * @todo use ListIO
	 * @todo use SQL Join for permission check
	 */
	private static function project_search()
	{
		global $user, $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[page])
			{
				$name = $session->read_value("SEARCH_PROJECT_NAME");
				$organisation_unit_array = $session->read_value("SEARCH_PROJECT_ORGANISATION_UNIT_ARRAY");
				$template_array = $session->read_value("SEARCH_PROJECT_TEMPLATE_ARRAY");
				$sub_projects = $session->read_value("SEARCH_PROJECT_SUB_PROJECTS");
			}
			else
			{
				if ($_GET[nextpage] == "1")
				{
					$name = $_POST[name];
					$session->delete_value("SEARCH_PROJECT_NAME");
					$session->delete_value("SEARCH_PROJECT_ORGANISATION_UNIT_ARRAY");
					$session->delete_value("SEARCH_PROJECT_TEMPLATE_ARRAY");
					$session->delete_value("SEARCH_PROJECT_SUB_PROJECTS");
				}
				else
				{
					$name = $_POST[name];
					$organisation_unit_array = $session->read_value("SEARCH_PROJECT_ORGANISATION_UNIT_ARRAY");
					$template_array = $session->read_value("SEARCH_PROJECT_TEMPLATE_ARRAY");
					$sub_projects = $session->read_value("SEARCH_PROJECT_SUB_PROJECTS");
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
			$template = new Template("languages/en-gb/template/search/project_search.html");
			
			$paramquery = $_GET;
			unset($paramquery[page]);
			$paramquery[nextpage] = "1";
			$params = http_build_query($paramquery,'','&#38;');
					
			$template->set_var("params",$params);
			
			$template->set_var("error", "");
			
			$result = array();
			$counter = 0;
			
			$organisation_unit_array = OrganisationUnit::list_entries();
			
			if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
			{
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
			
					if ($organisation_unit->is_permission($user->get_user_id()))
					{
						$result[$counter][value] = $value;
						$result[$counter][content] = $organisation_unit->get_name();		
						$result[$counter][selected] = "";
		
						$counter++;
					}
				}
			}
			
			if (!$result)
			{
				$result[$counter][value] = "0";
				$result[$counter][content] = "NO ORGANISATION UNIT FOUND!";	
			}
			
			$template->set_array("organ_unit",$result);
			
			
			$result = array();
			$counter = 0;
				
			$project_template_array = ProjectTemplateCat::list_entries();
			
			if (is_array($project_template_array))
			{
				foreach($project_template_array as $key => $value)
				{
					$project_template_cat = new ProjectTemplateCat($value);	
					$result[$counter][value] = "0";
					$result[$counter][content] = $project_template_cat->get_name();		
					$result[$counter][selected] = "";
	
					$counter++;
					
					$project_template_sub_array = ProjectTemplate::list_entries_by_cat_id($value);
					
					if (is_array($project_template_sub_array))
					{
						foreach($project_template_sub_array as $sub_key => $sub_value)
						{
							$project_sub_template = new ProjectTemplate($sub_value);
	
							
							$result[$counter][value] = $sub_value;
							$result[$counter][content] = "&nbsp;".$project_sub_template->get_name();		
							$result[$counter][selected] = "";
			
							$counter++;
						}
					}
					unset($project_template_sub_array);
				}
			}
			else
			{
				$result[$counter][value] = "0";
				$result[$counter][content] = "NO TEMPLATES FOUND!";
			}
	
			$template->set_array("template",$result);
					
			$template->output();	
		}
		else
		{
			if(!$organisation_unit_array)
			{			
				if (!$_POST[organisation_unit])
				{
					$organisation_unit_array = array();
					
					$organisation_unit_array = OrganisationUnit::list_entries();
					
					if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
					{
						foreach($organisation_unit_array as $key => $value)
						{
							$organisation_unit = new OrganisationUnit($value);
							if ($organisation_unit->is_permission($user->get_user_id()))
							{
								array_push($organisation_unit_array, $value);
							}
						}
					}
					$search_organisation_unit_name = "All";
				}
				else
				{
					$organisation_unit_array = array();
					$organisation_unit_array[0] = $_POST[organisation_unit];
					$organisation_unit = new OrganisationUnit($_POST[organisation_unit]);
					$search_organisation_unit_name = $organisation_unit->get_name();
				}
			}
			else
			{
				if (count($organisation_unit_array) == 1)
				{
					$organisation_unit = new OrganisationUnit($organisation_unit_array[0]);
					$search_organisation_unit_name = $organisation_unit->get_name();
				}
				else
				{
					$search_organisation_unit_name = "All";
				}
			}
			
			if (!$template_array)
			{
				if (!$_POST[template])
				{
					$template_array = null;
					$search_template_name = "All";
				}
				else
				{
					$template_array = array();
					$template_array[0] = $_POST[template];
					$project_template = new ProjectTemplate($_POST[template]);
					$search_template_name = $project_template->get_name();
				}
			}
			else
			{
				if (count($template_array) == 1)
				{
					$project_template = new ProjectTemplate($template_array[0]);
					$search_template_name = $project_template->get_name();
				}
				else
				{
					$search_template_name = "All";
				}
			}
			
			if (!isset($sub_projects))
			{
				if ($_POST[sub_projects] == 1)
				{
					$sub_projects = true;
				}
				else
				{
					$sub_projects = false;
				}
			}

			$session->write_value("SEARCH_PROJECT_NAME", $name, true);
			$session->write_value("SEARCH_PROJECT_ORGANISATION_UNIT_ARRAY", $organisation_unit_array, true);
			$session->write_value("SEARCH_PROJECT_TEMPLATE_ARRAY", $template_array, true);
			$session->write_value("SEARCH_PROJECT_SUB_PROJECTS", $sub_projects, true);

			$project_array = Project::search_projects($name, $organisation_unit_array, $template_array, $sub_projects);
			
			/* --------------- */
			
			$content_array = array();
		
			$table_io = new TableIO("OverviewTable");
			
			$table_io->add_row("","symbol",false,16);
			$table_io->add_row("Name","name",false,null);
			$table_io->add_row("Organisation Unit","unit",false,null);
			$table_io->add_row("Date/Time","datetime",false,null);
			$table_io->add_row("Template","template",false,null);
			$table_io->add_row("Status","status",false,null);
			
			$project_array_cardinality = count($project_array);
			
			$counter = 0;
	
			if (!$_GET[page] or $_GET[page] == 1)
			{
				$page = 1;
				$counter_begin = 0;
				if ($project_array_cardinality > 25)
				{
					$counter_end = 24;
				}
				else
				{
					$counter_end = $project_array_cardinality-1;
				}
			}
			else
			{
				if ($_GET[page] >= ceil($project_array_cardinality/25))
				{
					$page = ceil($project_array_cardinality/25);
					$counter_end = $project_array_cardinality;
				}
				else
				{
					$page = $_GET[page];
					$counter_end = (25*$page)-1;
				}
				$counter_begin = (25*$page)-25;
			}
			
			if (is_array($project_array) and count($project_array) >= 1)
			{
				foreach ($project_array as $key => $value)
				{
					if ($counter >= $counter_begin and $counter <= $counter_end)
					{
						$column_array = array();
						
						$project 			= new Project($value);
						$project_security	= new ProjectSecurity($value);
						$organisation_unit 	= new OrganisationUnit($project->get_organisation_unit_id());
						
						if ($project_security->is_access(1, false) == true)
						{					
							if ($value[subprojects] == true)
							{
								$symbol = "<img src='images/icons/projects.png' alt='S' border='0' />";	
							}
							else
							{
								$symbol = "<img src='images/icons/project.png' alt='N' border='0' />";
							}
						}
						else
						{
							if ($value[subprojects] == true)
							{
								$symbol = "<img src='core/images/denied_overlay.php?image=images/icons/projects.png' alt='S' border='0' />";	
							}
							else
							{
								$symbol = "<img src='core/images/denied_overlay.php?image=images/icons/project.png' alt='N' border='0' />";
							}
						}
						
						if (strlen($value[template]) > 20)
						{
							$project_template = substr($project->get_template_name(),0,20).".";
						}
						else
						{
							$project_template = $project->get_template_name();
						}
			
						if ($_GET[run] == "accessdata")
						{
							$paramquery = $_GET;
							$paramquery[nav] = "objects";
							$paramquery[project_id] = $value;
							unset($paramquery[run]);
							unset($paramquery[page]);
							$params = http_build_query($paramquery,'','&#38;');
						}
						else
						{
							$paramquery = $_GET;
							$paramquery[nav] = "project";
							$paramquery[run] = "detail";
							$paramquery[project_id] = $value;
							unset($paramquery[page]);
							$params = http_build_query($paramquery,'','&#38;');
						}
						
						if ($project_security->is_access(1, false) == true)
						{	
							$column_array[symbol][link] = $params;
							$column_array[name][link] = $params;
						}
						else
						{
							$column_array[symbol][link] = "";
							$column_array[name][link] = "";
						}
						
						$column_array[symbol][content] = $symbol;
						$column_array[name][content] = $project->get_name();
						
						if ($project->get_organisation_unit_id())
						{
							$column_array[unit] = $organisation_unit->get_name();
						}
						else
						{
							$column_array[unit] = "<span class='italic'>Sub-Project</span>";
						}
						
						$column_array[datetime] = $project->get_datetime();
						$column_array[template] = $project_template;
						$column_array[status] = $project->get_current_status_name();
		
						array_push($content_array, $column_array);
					}
					$counter++;	
				}
			}
			else
			{
				$content_array = null;
				$table_io->override_last_line("<span class='italic'>No Projects Found!</span>");
			}
			
			$template = new Template("languages/en-gb/template/search/project_search_result.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("name", $name);
			$template->set_var("organisation_units", $search_organisation_unit_name);
			$template->set_var("templates", $search_template_name);
			
			$table_io->add_content_array($content_array);	
				
			$template->set_var("table", $table_io->get_table($page ,$project_array_cardinality));		
	
			$template->output();
		}
	}
	
	
	
	private static function ffv_search()
	{
		global $user, $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				if ($_GET[nextpage] == "2" and $_POST[string])
				{
					$string = $_POST[string];
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
				else
				{
					$string = $session->read_value("SEARCH_FFV_STRING");
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
			}
			else
			{
				if ($_GET[page])
				{
					$string = $session->read_value("SEARCH_FFV_STRING");
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
				else
				{
					if ($_GET[nextpage] == "1")
					{
						$string = $_POST[string];
						$session->delete_value("SEARCH_FFV_STRING");
						$session->delete_value("SEARCH_FFV_FOLDER_ID");
					}
					else
					{
						$string = $_POST[string];
						$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
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
			$template = new Template("languages/en-gb/template/search/ffv_search.html");
			
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
			if (!$folder_id)
			{
				$folder_id = $_POST[folder_id];
			}

			$session->write_value("SEARCH_FFV_STRING", $string, true);
			$session->write_value("SEARCH_FFV_FOLDER_ID", $folder_id, true);
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = DataSearch_Wrapper::search_ffv($folder_id, $string, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = DataSearch_Wrapper::search_ffv($folder_id, $string, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = DataSearch_Wrapper::search_ffv($folder_id, $string, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = DataSearch_Wrapper::search_ffv($folder_id, $string, null, null, 0, 20);
				}	
			}
			
			$list = new List_IO(DataSearch_Wrapper::count_search_ffv($folder_id, $string), 20);
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
					if (is_numeric($value[file_id]))
					{
						$file = new File($value[file_id]);
						
						$paramquery = $_GET;
						$paramquery[nav] = "file";
						$paramquery[run] = "detail";
						$paramquery[file_id] = $value[file_id];
						unset($paramquery[sortvalue]);
						unset($paramquery[sortmethod]);
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery, '', '&#38;');
						
						$tmp_name = $value[name];
						if (strlen($tmp_name) > 20)
						{
							$tmp_name = substr($tmp_name,0 ,20)."...";
						}
						unset($result_array[$key][name]);
						$result_array[$key][name][content] = $tmp_name;
						
						if ($file->is_read_access() == true)
						{
							$result_array[$key][symbol][link] = $params;
							$result_array[$key][symbol][content] = "<img src='".File::get_icon_by_name($value[name])."' alt='' style='border:0;' />";
							$result_array[$key][name][link] = $params;
						}
						else
						{
							$result_array[$key][symbol][link] = "";
							$result_array[$key][symbol][content] = "<img src='core/images/denied_overlay.php?image=".File::get_icon_by_name($value[name])."' alt='' border='0' />";
							$result_array[$key][name][link] = "";
						}
						
						$result_array[$key][type] = "File";
						
						$result_array[$key][version] = $file->get_version();
						$result_array[$key][size] = Misc::calc_size($file->get_size());
						$result_array[$key][permission] = $file->get_permission_string();
					}
					
					if (is_numeric($value[value_id]))
					{
						$value_obj = new Value($value[value_id]);
						
						$paramquery = $_GET;
						$paramquery[nav] = "value";
						$paramquery[run] = "detail";
						$paramquery[value_id] = $value[value_id];
						unset($paramquery[sortvalue]);
						unset($paramquery[sortmethod]);
						unset($paramquery[nextpage]);
						$item_params = http_build_query($paramquery, '', '&#38;');
						
						$tmp_name = $value[name];
						if (strlen($tmp_name) > 20)
						{
							$tmp_name = substr($tmp_name,0 ,20)."...";
						}
						unset($result_array[$key][name]);
						$result_array[$key][name][content] = $tmp_name;
						
						if ($value_obj->is_read_access() == true)
						{
							$result_array[$key][symbol][link] = $params;
							$result_array[$key][symbol][content] = "<img src='images/fileicons/16/unknown.png' alt='' style='border: 0;'>";
							$result_array[$key][name][link] = $params;
						}
						else
						{
							$result_array[$key][symbol][link] = "";
							$result_array[$key][symbol][content] = "<img src='core/images/denied_overlay.php?image=images/fileicons/16/unknown.png' alt='' border='0' />";
							$result_array[$key][name][link] = "";
						}
						
						$result_array[$key][type] = "Value";
						
						$result_array[$key][version] = $value_obj->get_version();
						$result_array[$key][permission] = $value_obj->get_permission_string();
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
			$list->add_row("Version", "version", false, null);
			$list->add_row("Datetime", "datetime", true, null);
			$list->add_row("Size", "size", true, null);
			$list->add_row("Owner", "owner", true, null);
			$list->add_row("Permission", "permission", false, null);
			
			$folder = Folder::get_instance($folder_id);
			
			$template = new Template("languages/en-gb/template/search/ffv_search_result.html");
		
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("string", $string);
			$template->set_var("folder", $folder->get_name());
				
			$template->set_var("table", $list->get_list($result_array, $_GET[page]));		
	
			$template->output();
		}
	}
	
	/**
	 * @todo use SQL-Join for permission check
	 */
	private static function data_search()
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
	
	/**
	 * @todo use ListIO
	 */
	private static function user_search()
	{
		global $user, $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[page])
			{
				$string = $session->read_value("SEARCH_USER_STRING");
				$type = $session->read_value("SEARCH_USER_TYPE");
			}
			else
			{
				if ($_GET[nextpage] == "1")
				{
					$string = $_POST[string];
					$session->delete_value("SEARCH_USER_STRING");
					$session->delete_value("SEARCH_USER_TYPE");
				}
				else
				{
					$string = $_POST[string];
					$type = $session->read_value("SEARCH_USER_TYPE");
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
			$template = new Template("languages/en-gb/template/search/user_search.html");
			
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
			if (!$type)
			{
				$type = $_POST[search_type];
			}
			
			$session->write_value("SEARCH_USER_STRING", $string, true);
			$session->write_value("SEARCH_USER_TYPE", $type, true);
			
			$content_array = array();
			
			$table_io = new TableIO("OverviewTable");
			
			if ($type == 1)
			{
				$result_array = User::search_users($string);
			
				$table_io->add_row("","symbol",false,16);
				$table_io->add_row("Username","username",false,null);
				$table_io->add_row("Full Name","fullname",false,null);
				
				$type_name = "User";
			}
			else
			{
				$result_array = Group::search_groups($string);
				
				$table_io->add_row("","symbol",false,16);
				$table_io->add_row("Group Name","name",false,null);
				
				$type_name = "Group";
			}
			
			$result_array_cardinality = count($result_array);
			
			$counter = 0;
	
			if (!$_GET[page] or $_GET[page] == 1)
			{
				$page = 1;
				$counter_begin = 0;
				if ($result_array_cardinality > 25)
				{
					$counter_end = 24;
				}
				else
				{
					$counter_end = $result_array_cardinality-1;
				}
			}
			else
			{
				if ($_GET[page] >= ceil($result_array_cardinality/25))
				{
					$page = ceil($result_array_cardinality/25);
					$counter_end = $result_array_cardinality;
				}
				else
				{
					$page = $_GET[page];
					$counter_end = (25*$page)-1;
				}
				$counter_begin = (25*$page)-25;
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach ($result_array as $key => $value)
				{
					if ($counter >= $counter_begin and $counter <= $counter_end)
					{
						$column_array = array();
	
						if ($type == 1)
						{
							$user = new User($value);
							
							$column_array[symbol]		= "<img src='images/icons/user.png' alt='' />";					
							$column_array[username] 	= $user->get_username();	
							$column_array[fullname] 	= $user->get_full_name(false);
						}
						else
						{
							$group = new Group($value);
							
							$column_array[symbol]		= "<img src='images/icons/groups.png' alt='' />";					
							$column_array[name] 	= $group->get_name();	
						}
						array_push($content_array, $column_array);		
					}
					$counter++;	
				}
			}
			else
			{
				$content_array = null;
				$table_io->override_last_line("<span class='italic'>No Results Found!</span>");
			}
			
			$template = new Template("languages/en-gb/template/search/user_search_result.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("string", $string);
			$template->set_var("type", $type_name);
			
			$table_io->add_content_array($content_array);	
				
			$template->set_var("table", $table_io->get_table($page ,$result_array_cardinality));		
	
			$template->output();
		}
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
			case "project":
				self::project_search();
			break;
			
			case "sample":
				require_once("/core/modules/sample/sample_search.io.php");
				SampleSearchIO::search();
			break;
			
			case "ffv":
				self::ffv_search();
			break;
			
			case "data":
				self::data_search();
			break;
			
			case "user":
				self::user_search();
			break;
			
			case "full_text":
				self::full_text_search();
			break;
			
			default:
				self::main();
			break;
		endswitch;
	}
	
}
?>
