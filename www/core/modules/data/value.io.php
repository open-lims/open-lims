<?php
/**
 * @package data
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
 * Value IO Class
 * @package data
 */
class ValueIO
{
	private static function detail()
	{
		global $common, $user;
		
		try
		{
			if ($_GET[value_id])
			{
				$value = new Value($_GET[value_id]);
				
				if ($value->is_read_access())
				{
					if ($_GET[version])
					{
						if ($value->exist_value_version($_GET[version]) == false)
						{
							throw new ValueVersionNotFoundException("",6);
						}
					}
					
					if ($_GET[version] and is_numeric($_GET[version]))
					{
						$value->open_internal_revision($_GET[version]);
					}
					
					if ($_GET[nextpage])
					{
						$noerror = true;
						
						$autofield_array = array();
						$counter = 0;
						
						foreach ($_POST as $fe_key => $fe_value)
						{
							if (strpos($fe_key, "af-") !== false)
							{
								if (strpos($fe_key, "-vartype") !== false)
								{
									$autofield_array[$counter][1] = $fe_value;
								}
								elseif(strpos($fe_key, "-name") !== false)
								{
									$autofield_array[$counter][0] = $fe_value;
									$counter++;
								}
								else
								{
									$autofield_array[$counter][2] = $fe_value;
								}
							}
						}
						
						$autofield_array_string = serialize($autofield_array);
						$value->set_autofield_array_string($autofield_array_string);
					}
					else
					{
						$noerror = false;
					}
					
					if ($noerror == false)
					{
						if ($value->get_type_id() == 2)
						{
							$template = new Template("languages/en-gb/template/data/value_project_description_detail.html");
						
							$value_version_array = $value->get_value_internal_revisions();
								
							if (is_array($value_version_array) and count($value_version_array) > 0)
							{		
								$result = array();
								$counter = 1;
							
								$result[0][version] = 0;
								$result[0][text] = "----------------------------------------------";
								
								foreach($value_version_array as $key => $fe_value)
								{
									$value_version = new Value($_GET[value_id]);
									$value_version->open_internal_revision($fe_value);
									
									$result[$counter][version] = $value_version->get_internal_revision();
									$result[$counter][text] = "Version ".$value_version->get_version()." - ".$value_version->get_datetime();
									$counter++;
								}
								$template->set_array("version_option",$result);
							}
							
							$result = array();
							$counter = 0;
							
							foreach($_GET as $key => $fe_value)
							{
								if ($key != "version")
								{
									$result[$counter][value] = $fe_value;
									$result[$counter][key] = $key;
									$counter++;
								}
							}
							
							$template->set_array("get",$result);
							
							$template->set_var("version",$value->get_version());
							$template->set_var("version_datetime",$value->get_datetime());
						
							$paramquery = $_GET;
							$paramquery[run] = "permission";
							$paramquery[nav] = "data";
							$params = http_build_query($paramquery,'','&#38;');	
							$template->set_var("change_permission_params",$params);
							
							if ($value->is_control_access() == true or $value->get_owner_id() == $user->get_user_id())
							{
								$template->set_var("change_permission",true);
							}
							else
							{
								$template->set_var("change_permission",false);
							}
						
							if ($value->is_write_access() == true or $value->get_owner_id() == $user->get_user_id())
							{
								$template->set_var("write_permission",true);
							}
							else
							{
								$template->set_var("write_permission",false);
							}
						
							$paramquery = $_GET;
							$paramquery[run] = "history";
							$params = http_build_query($paramquery,'','&#38;');	
							
							$template->set_var("version_list_link",$params);
						
							$paramquery = $_GET;
							$paramquery[nextpage] = "1";
							$paramquery[version] = $value->get_internal_revision();
							$params = http_build_query($paramquery,'','&#38;');
							
							$template->set_var("params", $params);
							
							$template->set_var("title", $value->get_type_name());
							
							$value_string = unserialize($value->get_value());
							
							$template->set_var("desc", $value_string);
							$template->set_var("error","");
		
							$template->output();
						}
						else
						{
							$template = new Template("languages/en-gb/template/data/value_detail.html");
						
							$value_version_array = $value->get_value_internal_revisions();
								
							if (is_array($value_version_array) and count($value_version_array) > 0)
							{		
								$result = array();
								$counter = 1;
							
								$result[0][version] = 0;
								$result[0][text] = "----------------------------------------------";
								
								foreach($value_version_array as $key => $fe_value)
								{
									$value_version = new Value($_GET[value_id]);
									$value_version->open_internal_revision($fe_value);
									
									$result[$counter][version] = $value_version->get_internal_revision();
									$result[$counter][text] = "Version ".$value_version->get_version()." - ".$value_version->get_datetime();
									$counter++;
								}
								$template->set_array("version_option",$result);
							}
							
							$result = array();
							$counter = 0;
							
							foreach($_GET as $key => $fe_value)
							{
								if ($key != "version")
								{
									$result[$counter][value] = $fe_value;
									$result[$counter][key] = $key;
									$counter++;
								}
							}
							
							$template->set_array("get",$result);
							
							$template->set_var("version",$value->get_version());
							$template->set_var("version_datetime",$value->get_datetime());
						
							$paramquery = $_GET;
							$paramquery[run] = "permission";
							$paramquery[nav] = "data";
							$params = http_build_query($paramquery,'','&#38;');	
							$template->set_var("change_permission_params",$params);
						
							if ($value->is_control_access() == true or $value->get_owner_id() == $user->get_user_id())
							{
								$template->set_var("change_permission",true);
							}
							else
							{
								$template->set_var("change_permission",false);
							}
							
							if ($value->is_write_access() == true or $value->get_owner_id() == $user->get_user_id())
							{
								$template->set_var("write_permission",true);
							}
							else
							{
								$template->set_var("write_permission",false);
							}
						
							$paramquery = $_GET;
							$paramquery[run] = "history";
							$params = http_build_query($paramquery,'','&#38;');	
							
							$template->set_var("version_list_link",$params);
						
							$paramquery = $_GET;
							$paramquery[nextpage] = "1";
							$paramquery[version] = $value->get_internal_revision();
							$params = http_build_query($paramquery,'','&#38;');
							
							$template->set_var("params", $params);
							
							$template->set_var("title", $value->get_type_name());
							
							$template->set_var("value",$value->get_html_form(null, null));
				
							$template->output();
						}
					}
					else
					{
						$paramquery = $_GET;
						$paramquery[nav] = "data";
						unset($paramquery[run]);
						unset($paramquery[value_id]);
						$params = http_build_query($paramquery,'','&#38;');
			
						// Button prüfen
						if ($_POST[submitbutton] == "major")
						{
							$major = true;
						}
						else
						{
							$major = false;
						}
			
						if ($_GET[version])
						{
							$previous_version_id = $_GET[version];
						}
						else
						{
							$previous_version_id = null;
						}
						
						if (is_array($_POST) and count($_POST) >= 1)
						{
							$value_array = array();
							
							foreach ($_POST as $fe_key => $fe_value)
							{
								if ($fe_key != "template_data_type_id" and
								    $fe_key != "submitbutton" and
								    $fe_key != "description" and
								    $fe_key != "keywords")
								{
								    $value_array[$fe_key] = $fe_value;	
								}
							}
				
							if ($value->update($value_array, $previous_version_id, $major, true, false))
							{			
								$common->step_proceed($params, "Value Update", "Value Update Succeed" ,null);			
							}
							else
							{
								$common->step_proceed($params, "Value Update", "Value Update Failed" ,null);			
							}
						}
						else
						{
							$common->step_proceed($params, "Value Update", "Value Update Failed" ,null);	
						}
					}
				}
				else
				{
					$exception = new Exception("", 3);
					$error_io = new Error_IO($exception, 20, 40, 2);
					$error_io->display_error();
				}
			}
			else
			{
				$exception = new Exception("", 3);
				$error_io = new Error_IO($exception, 20, 40, 3);
				$error_io->display_error();			
			}
		}
		catch(ValueVersionNotFoundException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 1);
			$error_io->display_error();
		}
	}	

	private static function add_to_project()
	{
		global $common, $user;
		
		if ($_GET[project_id] and isset($_GET[key]))
		{
			$project_id = $_GET[project_id];
			$project = new Project($project_id);
			$project_security = new ProjectSecurity($project_id);
			
			if ($project_security->is_access(3, false) == true)
			{
				$project_item = new ProjectItem($project_id);
				$project_item->set_gid($_GET[key]);
				$project_item->set_status_id($project->get_current_status_id());
				
				$description_required = $project_item->is_description();
				$keywords_required = $project_item->is_keywords();
				
				$requirements_array = $project->get_current_status_requirements();
				
				if (($description_required and !$_POST[description]) or ($keywords_required and !$_POST[keywords]))
				{
					require_once("core/modules/item/item.io.php");
					ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
				}
				else
				{
					if($requirements_array[$_GET[key]][type] == "value")
					{
						if (count($requirements_array[$_GET[key]][type_id]) != 1 and !$_POST[type_id])
						{
							$result = array();
							$counter = 0;
							
							if (count($requirements_array[$_GET[key]][type_id]) == 0)
							{
								$value_obj = new Value(null);
								$value_type_array = ValueType::list_entries();
								
								foreach($value_type_array as $key => $value)
								{
									$value_type = new ValueType($value);
									$result[$counter][value] = $value;
									$result[$counter][content] = $value_type->get_name();
									
									$counter++;
								}
							}
							else
							{
								foreach($requirements_array[$_GET[key]][type_id] as $key => $value)
								{
									$value_type = new ValueType($value);
									$result[$counter][value] = $value;
									$result[$counter][content] = $value_type->get_name();
									
									$counter++;
								}
							}
						}
						elseif(count($requirements_array[$_GET[key]][type_id]) != 1 and $_POST[type_id])
						{
							$type_id = $_POST[type_id];
						}
						else
						{
							$type_id = $requirements_array[$_GET[key]][type_id][0];
						}
						
						if (!$type_id)
						{
							$template = new Template("languages/en-gb/template/data/value_select_list.html");
							
							$paramquery = $_GET;
							$paramquery[nextpage] = "1";
							$params = http_build_query($paramquery,'','&#38;');
							
							$template->set_var("params", $params);
							
							$template->set_var("select",$result);
							
							$template->set_var("keywords", $_POST[keywords]);
							$template->set_var("description", $_POST[description]);
							
							$template->output();
						}
						else
						{
							$folder_id = Folder::get_project_status_folder_by_status_id($project_id,$project->get_current_status_id());
							
							$sub_folder_id = $project->get_sub_folder($_GET[key], $project->get_current_status_id());
							
							if (is_numeric($sub_folder_id))
							{
								$folder_id = $sub_folder_id;
							}
							
							$folder = new Folder($folder_id);
									
							$value = new Value(null);
							$value_type = new ValueType($type_id);
							
							if (!$_GET[nextpage] or $_GET[nextpage] == "1")
							{
								$template = new Template("languages/en-gb/template/data/value_add.html");
										
								$paramquery = $_GET;
								$paramquery[nextpage] = "2";
								$params = http_build_query($paramquery,'','&#38;');
								
								$template->set_var("params", $params);
								
								$template->set_var("title", $value_type->get_name());
								
								$template->set_var("value",$value->get_html_form(null, $type_id));
					
								$template->set_var("type_id", $type_id);
					
								$template->set_var("keywords", $_POST[keywords]);
								$template->set_var("description", $_POST[description]);
					
								$template->output();
							}
							else
							{
								$paramquery = $_GET;
								$paramquery[nav] = "projects";
								$paramquery[run] = "detail";
								unset($paramquery[key]);
								unset($paramquery[nextpage]);
								$params = http_build_query($paramquery,'','&#38;');
					
								$value_add_successful = $value->create($folder_id, $user->get_user_id(), $type_id, $_POST, false);
															
								if ($value_add_successful == true)
								{
									$item_id = $value->get_item_id();
									
									$project_item->set_item_id($item_id);
									$project_item->link_item();
									$project_item->set_item_status();

									if (($class_name = $project_item->is_classified()) == true)
									{
										$project_item->set_class($class_name);
									}
									
									if ($description_required == true xor $keywords_required == true)
									{
										if ($description_required == false and $keywords_required == true)
										{
											$project_item->set_information(null,$_POST[keywords]);
										}
										else
										{
											$project_item->set_information($_POST[description],null);
										}
									}
									else
									{
										if ($description_required == true and $keywords_required == true)
										{
											$project_item->set_information($_POST[description],$_POST[keywords]);
										}
									}
									$project_item->create_log_entry();	
									
									ProjectTask::check_over_time_tasks($project_id);
								}
					
								if ($value_add_successful)
								{						
									$common->step_proceed($params, "Value Update", "Value Update Succeed" ,null);			
								}
								else
								{	
									$common->step_proceed($params, "Value Update", "Value Update Failed" ,null);			
								}
							}
						}
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 4);
			$error_io = new Error_IO($exception, 20, 40, 3);
			$error_io->display_error();			
		}
	}
	
	private static function add_to_sample()
	{
		global $common, $user;
		
		if ($_GET[sample_id] and isset($_GET[key]))
		{
			$sample_id = $_GET[sample_id];
			$sample = new Sample($sample_id);
			$sample_security = new SampleSecurity($sample_id);
			
			if ($sample_security->is_access(2, false))
			{
				$sample_item = new SampleItem($sample_id);
				$sample_item->set_gid($_GET[key]);
				
				$description_required = $sample_item->is_description();
				$keywords_required = $sample_item->is_keywords();
				
				$requirements_array = $sample->get_requirements();
				
				if (($description_required and !$_POST[description]) or ($keywords_required and !$_POST[keywords]))
				{
					require_once("core/modules/item/item.io.php");
					ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
				}
				else
				{
					if($requirements_array[$_GET[key]][type] == "value")
					{
						if (count($requirements_array[$_GET[key]][type_id]) != 1 and !$_POST[type_id])
						{
							$result = array();
							$counter = 0;
							
							if (count($requirements_array[$_GET[key]][type_id]) == 0)
							{
								$value_type_array = ValueType::list_value_types();
								
								foreach($value_type_array as $key => $value)
								{
									$value_type = new ValueType($value);
									$result[$counter][value] = $value;
									$result[$counter][content] = $value_type->get_name();
									
									$counter++;
								}
							}
							else
							{
								foreach($requirements_array[$_GET[key]][type_id] as $key => $value)
								{
									$value_type = new ValueType($value);
									$result[$counter][value] = $value;
									$result[$counter][content] = $value_type->get_name();
									
									$counter++;
								}
							}
						}
						elseif(count($requirements_array[$_GET[key]][type_id]) != 1 and $_POST[type_id])
						{
							$type_id = $_POST[type_id];
						}
						else
						{
							$type_id = $requirements_array[$_GET[key]][type_id][0];
						}
						
						if (!$type_id)
						{
							$template = new Template("languages/en-gb/template/data/value_select_list.html");
							
							$paramquery = $_GET;
							$paramquery[nextpage] = "1";
							$params = http_build_query($paramquery,'','&#38;');
							
							$template->set_var("params", $params);
							
							$template->set_var("select",$result);
							
							$template->set_var("keywords", $_POST[keywords]);
							$template->set_var("description", $_POST[description]);
							
							$template->output();
						}
						else
						{
							$folder_id = Folder::get_sample_folder_by_sample_id($sample_id);
							
							$sub_folder_id = $sample->get_sub_folder($folder_id, $_GET[key]);				
			
							if (is_numeric($sub_folder_id))
							{
								$folder_id = $sub_folder_id;
							}
							
							$folder = new Folder($folder_id);
									
							$value = new Value(null);
							$value_type = new ValueType($type_id);
							
							if (!$_GET[nextpage] or $_GET[nextpage] == "1")
							{
								$template = new Template("languages/en-gb/template/data/value_add.html");
										
								$paramquery = $_GET;
								$paramquery[nextpage] = "2";
								$params = http_build_query($paramquery,'','&#38;');
								
								$template->set_var("params", $params);
								
								$template->set_var("title", $value_type->get_name());
								
								$template->set_var("value",$value->get_html_form(null, $type_id));
					
								$template->set_var("type_id", $type_id);
					
								$template->set_var("keywords", $_POST[keywords]);
								$template->set_var("description", $_POST[description]);
					
								$template->output();
							}
							else
							{
								$paramquery = $_GET;
								$paramquery[nav] = "samples";
								$paramquery[run] = "detail";
								unset($paramquery[key]);
								unset($paramquery[nextpage]);
								$params = http_build_query($paramquery,'','&#38;');
					
								$value_add_successful = $value->create($folder_id, $user->get_user_id(), $type_id, $_POST, false);
															
								if ($value_add_successful == true)
								{
									$item_id = $value->get_item_id();
									$sample_item->set_item_id($item_id);
									$sample_item->link_item();
								
									if (($class_name = $sample_item->is_classified()) == true)
									{
										$sample_item->set_class($class_name);
									}
									
									if ($description_required == true xor $keywords_required == true)
									{
										if ($description_required == false and $keywords_required == true)
										{
											$sample_item->set_information(null,$_POST[keywords]);
										}
										else
										{
											$sample_item->set_information($_POST[description],null);
										}
									}
									else
									{
										if ($description_required == true and $keywords_required == true)
										{
											$sample_item->set_information($_POST[description],$_POST[keywords]);
										}
									}
								}
					
								if ($value_add_successful)
								{						
									$common->step_proceed($params, "Value Update", "Value Update Succeed" ,null);			
								}
								else
								{
									$common->step_proceed($params, "Value Update", "Value Update Failed" ,null);				
								}
							}
						}
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 5);
			$error_io = new Error_IO($exception, 20, 40, 3);
			$error_io->display_error();			
		}
	}
	
	/**
	 * @todo Adding values outside projects and/or sample is currently not supported
	 */
	private static function add()
	{
		
	}

	private static function history()
	{
		global $misc;
		
		if ($_GET[value_id])
		{
			$value = new Value($_GET[value_id]);
			
			if ($value->is_read_access())
			{
				$template = new Template("languages/en-gb/template/data/value_history.html");
	
				$template->set_var("title",$value->get_type_name());
				
				$table_io = new TableIO("OverviewTable");
				
				$table_io->add_row("","symbol",false,16);
				$table_io->add_row("Name","name",false,null);
				$table_io->add_row("Version","version",false,null);
				$table_io->add_row("Date/Time","datetime",false,null);
				$table_io->add_row("","delete",false,16);
				
				$content_array = array();
				
				$value_version_array = $value->get_value_internal_revisions();
				
				foreach($value_version_array as $key => $fe_value)
				{
					$column_array = array();
					
					$value_version = new Value($_GET[value_id]);
					$value_version->open_internal_revision($fe_value);
					
					$paramquery = $_GET;
					$paramquery[value_id] = $_GET[value_id];
					$paramquery[version] = $fe_value;
					$paramquery[nav] = "value";
					$paramquery[run] = "detail";
					unset($paramquery[nextpage]);
					$params = http_build_query($paramquery,'','&#38;');
					
					$column_array[symbol][link] = $params;
					$column_array[symbol][content] = "<img src='images/fileicons/16/unknown.png' alt='' style='border:0;' />";
					$column_array[name][link] = $params;
					$column_array[name][content] = $value_version->get_type_name();
					
					if ($value_version->is_current() == true)
					{
						$column_array[version] = $value_version->get_version()." <span class='italic'>current</span>";
					}
					else
					{
						$column_array[version] = $value_version->get_version();
					}
					
					$column_array[datetime] = $value_version->get_datetime();
					
					if ($value->is_control_access())
					{
						$paramquery = $_GET;
						$paramquery[value_id] = $_GET[value_id];
						$paramquery[version] = $fe_value;
						$paramquery[nav] = "value";
						$paramquery[run] = "delete_version";
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery,'','&#38;');
						
						$column_array[delete][link] = $params;
						$column_array[delete][content] = "<img src='images/icons/delete.png' alt='' style='border:0;' />";
					}
					else
					{
						$column_array[delete][link] = "";
						$column_array[delete][content] = "";
					}
					array_push($content_array, $column_array);
				}
				
				$table_io->add_content_array($content_array);
				
				$template->set_var("table", $table_io->get_content($_GET[page]));	
				
				$paramquery = $_GET;
				$paramquery[run] = "detail";
				$params = http_build_query($paramquery,'','&#38;');	
				
				$template->set_var("back_link",$params);
				
				$template->output();
			}
			else
			{
				$exception = new Exception("", 3);
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}	
		}
		else
		{
			$exception = new Exception("", 3);
			$error_io = new Error_IO($exception, 20, 40, 3);
			$error_io->display_error();	
		}
	}
	
	private static function delete_version()
	{
		global $common;
		
		if ($_GET[value_id] and $_GET[version])
		{
			$value = new Value($_GET[value_id]);
			
			if ($value->is_delete_access())
			{
				if ($_GET[sure] != "true")
				{
					$template = new Template("languages/en-gb/template/data/value_delete_version.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					$paramquery[run] = "history";
					unset($paramquery[sure]);
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					if (($return_value = $value->delete_version($_GET[version])) != 0)
					{
						if ($return_value == 1)
						{
							$paramquery = $_GET;
							$paramquery[run] = "history";
							unset($paramquery[sure]);
							unset($paramquery[version]);
							$params = http_build_query($paramquery);
						}
						else
						{
							$paramquery = $_GET;
							$paramquery[nav] = "data";
							unset($paramquery[sure]);
							unset($paramquery[run]);
							unset($paramquery[file_id]);
							$params = http_build_query($paramquery);
						}					
						$common->step_proceed($params, "Delete Value", "Operation Successful" ,null);
					}
					else
					{
						$paramquery = $_GET;
						$paramquery[nav] = "data";
						unset($paramquery[sure]);
						unset($paramquery[run]);
						unset($paramquery[file_id]);
						$params = http_build_query($paramquery);
								
						$common->step_proceed($params, "Delete Value", "Operation Failed" ,null);
					}			
				}
			}
			else
			{
				$exception = new Exception("", 3);
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 3);
			$error_io = new Error_IO($exception, 20, 40, 3);
			$error_io->display_error();	
		}
	}
	
	public static function method_handler()
	{
		try
		{
			if ($_GET[value_id])
			{
				if (Value::exist_value($_GET[value_id]) == false)
				{
					throw new ValueNotFoundException("",3);
				}
			}
			
			switch($_GET[run]):
				case("add_to_project"):
					self::add_to_project();
				break;
				
				case("add_to_sample"):
					self::add_to_sample();
				break;
				
				case("add"):
					self::add();
				break;
				
				case("detail"):
					self::detail();
				break;
				
				case("history"):
					self::history();
				break;
				
				case("delete_version"):
					self::delete_version();
				break;
				
				default:
				
				break;
			endswitch;
		}
		catch (ValueNotFoundException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 1);
			$error_io->display_error();
		}
	}
	
}

?>
