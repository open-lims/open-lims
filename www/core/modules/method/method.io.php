<?php
/**
 * @package method
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
 * Method IO Class
 * @package method
 */
class MethodIO
{
	/**
	 * @todo method causes problematic dependency
	 */
	public static function add_to_project()
	{
		global $common, $user, $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(3, false) == true)
			{
				$project_id = $_GET[project_id];
				$project = new Project($project_id);
				
				$project_item = new ProjectItem($project_id);
				$project_item->set_gid($_GET[key]);
				$project_item->set_status_id($project->get_current_status_id());
				
				$description_required = $project_item->is_description();
				$keywords_required = $project_item->is_keywords();
				
				if (($description_required and !$_POST[description]) or ($keywords_required and !$_POST[keywords]))
				{
					require_once("core/modules/item/item.io.php");
					ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
				}
				else
				{
					if (!isset($_GET[key]) and !$_GET[nextpage])
					{
						$template = new Template("languages/en-gb/template/methods/method_to_project_page_1.html");
						
						$template->set_var("keywords", $_POST[keywords]);
						$template->set_var("description", $_POST[description]);
						
						$template->output();
					}
					else
					{
						if ($_GET[nextpage] == "2")
						{
							if (!is_numeric($_POST[type_id]))
							{
								$page_2_passed = false;
							}
							else
							{
								$page_2_passed = true;
							}
						}
						else
						{
							$page_2_passed = false;
						}
						
						if ($page_2_passed == false)
						{
							$requirements_array = $project->get_current_status_requirements();
							$key_array = $requirements_array[$_GET[key]];
							
							if ((is_array($key_array[type_id]) and count($key_array[type_id]) >= 1) or ((is_array($key_array[category_id]) and count($key_array[category_id]) >= 1)))
							{
								$method_array = array();
								
								if ((is_array($key_array[type_id]) and count($key_array[type_id]) >= 1))
								{
									foreach($key_array[type_id] as $key => $value)
									{
										array_push($method_array, $value);
										$method_array = array_merge($method_array,MethodType::list_entries_by_id($value));
									}
								}
								
								if ((is_array($key_array[category_id]) and count($key_array[category_id]) >= 1))
								{
									foreach($key_array[category_id] as $key => $value)
									{
										$method_array = array_merge($method_array,MethodType::list_entries_by_cat_id($value));
									}
								}
							}
							else
							{
								$method_array = MethodType::list_entries();
							}	
						
							$template = new Template("languages/en-gb/template/methods/method_to_project_page_2.html");
							
							$paramquery = $_GET;
							$paramquery[nextpage] = 2;
							$params = http_build_query($paramquery,'','&#38;');
							
							$template->set_var("params",$params);
							
							$result = array();
							$counter = 0;
							
							if (is_array($method_array) and count($method_array) >= 1)
							{
								foreach($method_array as $key => $value)
								{
									$method_type = new MethodType($value);
									
									$result[$counter][value] = $value;
									$result[$counter][content] = $method_type->get_name()." (".$method_type->get_cat_name().")";
									
									$counter++;
								}
							}
							else
							{
								$result[0][value] = "0";
								$result[0][content] = "NO METHOD FOUND!";	
							}
							
							$template->set_var("select",$result);
							
							$template->set_var("keywords", $_POST[keywords]);
							$template->set_var("description", $_POST[description]);
							
							$template->output();
						}
						else
						{
							$paramquery = $_GET;
							$paramquery[nav] = "project";
							$paramquery[run] = "detail";
							unset($paramquery[nextpage]);
							unset($paramquery[key]);
							$params = http_build_query($paramquery,'','&#38;');
							
							$method = new Method($_POST[method_id]);
			
							$method_add_successful = $method->create($_POST[type_id], $user->get_user_id());
													
							if ($method_add_successful == true)
							{	
								$item_id = $method->get_item_id();
								
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
							}
			
							ProjectTask::check_over_time_tasks($project_id);
							
							if ($method_add_successful)
							{
								$common->step_proceed($params, "Add Method", "Method added successful." ,null);
							}
							else
							{
								$common->step_proceed($params, "Add Method", "Failed." ,null);	
							}
						}
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 200, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}
	
	/**
	 * @todo method causes problematic dependency
	 */
	public static function add_to_sample()
	{
		global $common, $user, $sample_security;
		
		if ($_GET[sample_id])
		{
			if ($sample_security->is_access(3, false) == true)
			{
				$sample_id = $_GET[sample_id];	
				$sample = new Sample($sample_id);			
				$sample_item = new SampleItem($sample_id);
				$sample_item->set_gid($_GET[key]);
				
				$description_required = $sample_item->is_description();
				$keywords_required = $sample_item->is_keywords();
				
				if (($description_required and !$_POST[description]) or ($keywords_required and !$_POST[keywords]))
				{	
					require_once("core/modules/item/item.io.php");
					ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
				}
				else
				{
					if (!isset($_GET[key]) and !$_GET[nextpage])
					{
						$template = new Template("languages/en-gb/template/methods/method_to_sample_page_1.html");
						
						$template->set_var("keywords", $_POST[keywords]);
						$template->set_var("description", $_POST[description]);
						
						$template->output();
					}
					else
					{
						if ($_GET[nextpage] == "2")
						{
							if (!is_numeric($_POST[type_id]))
							{
								$page_2_passed = false;
							}
							else
							{
								$page_2_passed = true;
							}
						}
						else
						{
							$page_2_passed = false;
						}
						
						if ($page_2_passed == false)
						{
							$requirements_array = $sample->get_requirements();
							$key_array = $requirements_array[$_GET[key]];
							
							if ((is_array($key_array[type_id]) and count($key_array[type_id]) >= 1) or ((is_array($key_array[category_id]) and count($key_array[category_id]) >= 1)))
							{
								$method_array = array();
								
								if ((is_array($key_array[type_id]) and count($key_array[type_id]) >= 1))
								{
									foreach($key_array[type_id] as $key => $value)
									{
										array_push($method_array, $value);
										$method_array = array_merge($method_array,MethodType::list_entries_by_id($value));
									}
								}
								
								if ((is_array($key_array[category_id]) and count($key_array[category_id]) >= 1))
								{
									foreach($key_array[category_id] as $key => $value)
									{
										$method_array = array_merge($method_array,MethodType::list_entries_by_cat_id($value));
									}
								}
							}
							else
							{
								$method_array = MethodType::list_entries();
							}					

							$template = new Template("languages/en-gb/template/methods/method_to_sample_page_2.html");
							
							$paramquery = $_GET;
							$paramquery[nextpage] = 2;
							$params = http_build_query($paramquery,'','&#38;');
							
							$template->set_var("params",$params);
							
							$result = array();
							$counter = 0;
							
							if (is_array($method_array) and count($method_array) >= 1)
							{
								foreach($method_array as $key => $value)
								{
									$method_type = new MethodType($value);
									
									$result[$counter][value] = $value;
									$result[$counter][content] = $method_type->get_name()." (".$method_type->get_cat_name().")";
									
									$counter++;
								}
							}
							else
							{
								$result[0][value] = "0";
								$result[0][content] = "NO METHOD FOUND!";
							}
							
							$template->set_var("select",$result);
							
							$template->set_var("keywords", $_POST[keywords]);
							$template->set_var("description", $_POST[description]);
							
							$template->output();
						}
						else
						{
							$paramquery = $_GET;
							$paramquery[nav] = "sample";
							$paramquery[run] = "detail";
							unset($paramquery[nextpage]);
							unset($paramquery[key]);
							$params = http_build_query($paramquery,'','&#38;');
							
							$method = new Method($_POST[method_id]);
			
							$method_add_successful = $method->create($_POST[type_id], $user->get_user_id());
													
							if ($method_add_successful)
							{		
								$item_id = $method->get_item_id();
								
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
							
							if ($method_add_successful)
							{
								$common->step_proceed($params, "Add Method", "Method added successful." ,null);
							}
							else
							{
								$common->step_proceed($params, "Add Method", "Failed." ,null);	
							}
						}
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 3);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}
	
	/**
	 * @todo method causes problematic dependency
	 */	
	public static function list_project_related_methods()
	{
		global $project_security;

		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project_item = new ProjectItem($_GET[project_id]);
		
				$item_array = $project_item->get_project_items();
		
				$template = new Template("languages/en-gb/template/methods/project_related_methods.html");
				
				$table_io = new TableIO("OverviewTable");
			
				$table_io->add_row("","symbol",false,16);
				$table_io->add_row("Method","method",false,null);
				$table_io->add_row("Type","type",false,null);
				$table_io->add_row("Status","status",false,null);
				$table_io->add_row("Date/Time","datetime",false,null);
				$table_io->add_row("","delete_button",false,16);
				
				$content_array = array();	
				$entry_found = false;
				
				if (is_array($item_array) and count($item_array) >= 1)
				{
					foreach($item_array as $key => $value)
					{
						if (Method::is_kind_of("method",$value) == true)
						{					
							$method_id = Method::get_entry_by_item_id($value);
							$method = new Method($method_id);
							$method_type = new MethodType($method->get_type_id());
					
							$column_array = array();
							
							$column_array[symbol][link] = "";
							$column_array[symbol][content] = "<img src='images/icons/method.png' alt='' />";
							$column_array[method][link] = "";
							$column_array[method][content] = $method_type->get_name();
							$column_array[type] = $method_type->get_cat_name();
							$column_array[status] = "";
							$column_array[datetime] = $method->get_datetime();
							$column_array[delete_button][content] = "<img src='images/icons/delete_method.png' alt='' />";
							
							array_push($content_array, $column_array);
							
							$entry_found = true;
						}
					}	
				}
				
				if ($entry_found == false)
				{
					$content_array = null;
					$table_io->override_last_line("<span class='italic'>No Methods Found!</span>");
				}
				
				$table_io->add_content_array($content_array);	
				
				$template->set_var("table", $table_io->get_content($_GET[page]));		
				
				$template->output();
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 200, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}

	/**
	 * @todo method causes problematic dependency
	 */
	public static function list_sample_related_methods()
	{
		global $sample_security;

		if ($_GET[sample_id])
		{
			if ($sample_security->is_access(1, false) == true)
			{
				$sample_item = new SampleItem($_GET[sample_id]);
		
				$item_array = $sample_item->get_sample_items();
		
				$template = new Template("languages/en-gb/template/methods/sample_related_methods.html");
				
				$table_io = new TableIO("OverviewTable");
			
				$table_io->add_row("","symbol",false,16);
				$table_io->add_row("Method","method",false,null);
				$table_io->add_row("Type","type",false,null);
				$table_io->add_row("Date/Time","datetime",false,null);
				$table_io->add_row("","delete_button",false,16);
				
				$content_array = array();	
				$entry_found = false;
				
				if (is_array($item_array) and count($item_array) >= 1)
				{
					foreach($item_array as $key => $value)
					{
						if (Method::is_kind_of("method",$value) == true)
						{					
							$method_id = Method::get_entry_by_item_id($value);
							$method = new Method($method_id);
							$method_type = new MethodType($method->get_type_id());
					
							$column_array = array();
							
							$column_array[symbol][link] = "";
							$column_array[symbol][content] = "<img src='images/icons/method.png' alt='' />";
							$column_array[method][link] = "";
							$column_array[method][content] = $method_type->get_name();
							$column_array[type] = $method_type->get_cat_name();
							$column_array[datetime] = $method->get_datetime();
							$column_array[delete_button][content] = "<img src='images/icons/delete_method.png' alt='' />";
							
							array_push($content_array, $column_array);
							
							$entry_found = true;
						}
					}
				}
				
				if ($entry_found == false)
				{
					$content_array = null;
					$table_io->override_last_line("<span class='italic'>No Methods Found!</span>");
				}
				
				$table_io->add_content_array($content_array);	
				
				$template->set_var("table", $table_io->get_content($_GET[page]));		
				
				$template->output();
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 3);
			$error_io = new Error_IO($exception, 50, 40, 3);
			$error_io->display_error();
		}
	}

	/**
	 * @todo method causes problematic dependency
	 */
	public static function method_handler()
	{
		global $project_security, $sample_security;
	
		try
		{
			if ($_GET[project_id])
			{
				if (Project::exist_project($_GET[project_id]) == false)
				{
					throw new ProjectException("",1);
				}
				else
				{
					$project_security = new ProjectSecurity($_GET[project_id]);
				}
			}
			else
			{
				$project_security = new ProjectSecurity(null);
			}
	
			if ($_GET[sample_id])
			{
				if (Sample::exist_sample($_GET[sample_id]) == false)
				{
					throw new SampleException("",3);
				}
				else
				{
					$sample_security = new SampleSecurity($_GET[sample_id]);
				}
			}
			else
			{
				$sample_security = new SampleSecurity(null);
			}
	
			switch($_GET[run]):
				case ("add_to_project"):
					self::add_to_project();
				break;
				
				case ("add_to_sample"):
					self::add_to_sample();
				break;
				
				case ("project_related_methods"):
					self::list_project_related_methods();
				break;
				
				case ("sample_related_methods"):
					self::list_sample_related_methods();
				break;
				
				default:
					
				break;	
			endswitch;
		}
		catch(ProjectException $e)
		{
			$error_io = new Error_IO($e, 50, 40, 1);
			$error_io->display_error();
		}
		catch(SampleException $e)
		{
			$error_io = new Error_IO($e, 50, 40, 1);
			$error_io->display_error();
		}
	}
	
}
?>

