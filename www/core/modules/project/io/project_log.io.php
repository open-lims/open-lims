<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
 * Project Log IO Class
 * @package project
 */
class ProjectLogIO
{
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function list_project_related_logs()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				define(PROJECT_LOG_ENTRIES_PER_PAGE, 6);
			
				$project_log_array = ProjectLog::list_entries_by_project_id($_GET[project_id]);
			
				if (!$_GET[page])
		    	{
					$page = 1;
				}
				else
				{
					$page = $_GET[page];	
				}
			
				$entry_count = count($project_log_array);
				$number_of_pages = ceil($entry_count/PROJECT_LOG_ENTRIES_PER_PAGE);
			
				$template = new HTMLTemplate("project/project_log.html");
	
				if (is_array($project_log_array) and count($project_log_array) >= 1)
				{
					$template->set_var("no_log",false);
					
					$result = array();
					$counter = 0;
					
					if (count($project_log_array) < ($page*PROJECT_LOG_ENTRIES_PER_PAGE))
					{
						$max_for = (count($project_log_array) % PROJECT_LOG_ENTRIES_PER_PAGE) - 1;
					}
					else
					{
						$max_for = PROJECT_LOG_ENTRIES_PER_PAGE-1;
					}
					
					for ($i=0;$i<=$max_for;$i++)
					{
						$entry = ($page*PROJECT_LOG_ENTRIES_PER_PAGE)+$i-PROJECT_LOG_ENTRIES_PER_PAGE; // Erzeugt Entry-ID
						$value = $project_log_array[$entry];
						
						$project_log = new ProjectLog($value);
						
						$user = new User($project_log->get_owner_id());
						
						$result[$counter][datetime] = $project_log->get_datetime();
						$result[$counter][user] = $user->get_full_name(false);
						
						if (($content = $project_log->get_content()) != null)
						{
							$result[$counter][content] = $content;
						}
						else
						{
							$result[$counter][content] = false;
						}
						
						$status_id = $project_log->get_status_id();
						
						if ($status_id != null)
						{
							$project_status = new ProjectStatus($status_id);
							$result[$counter][status] = $project_status->get_name();
						}
						else
						{
							$result[$counter][status] = false;
						}
						
						if ($project_log->get_important() == true)
						{
							$result[$counter][important] = true;
						}
						else
						{
							$result[$counter][important] = false;
						}
						
						$item_array = $project_log->list_items();
						
						$result[$counter][items] = count($item_array);
						
						$detail_paramquery = $_GET;
						$detail_paramquery[run] = "log_detail";
						$detail_paramquery[id] = $value;
						$detail_params = http_build_query($detail_paramquery,'','&#38;');
						
						$result[$counter][detail_params] = $detail_params;
						
						$counter++;
					}
					$template->set_var("log_array", $result);
				}
				else
				{
					$template->set_var("no_log",true);
				}
				
				if ($number_of_pages > 1)
				{
					$template->set_var("page_bar",Common_IO::page_bar($page, $number_of_pages, $_GET));
				}
				else
				{
					$template->set_var("page_bar","");
				}
				
				$template->output();
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @todo implementing list-behav. of concrete items
	 * @throws ProjectIDMissingException
	 * @throws ProjectLogIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function detail()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($_GET[id])
			{	
				$project_log = new ProjectLog($_GET[id]);
				
				if ($project_security->is_access(1, false) == true)
				{
					$item_array = $project_log->list_items();
					$item_type_array = Item::list_types();
					
					if (is_array($item_array) and count($item_array) >= 1)
					{
						$result = array();
						$counter = 0;
						
						foreach($item_array as $key => $value)
						{
							if (is_array($item_type_array) and count($item_type_array) >= 1)
							{
								foreach ($item_type_array as $item_type => $item_handling_class)
								{
									if ($item_handling_class::is_kind_of($item_type, $value) == true)
									{
										if (class_exists($item_handling_class))
										{
										// Verhalten müssen IO Klassen der Items selbst zur verfügung stellen
										}
									}
								}
							}
							
							$counter++;
						}
					}
					else
					{
						$result = false;
					}
					
					$template = new HTMLTemplate("project/project_log_detail.html");
				
					$user = new User($project_log->get_owner_id());
				
					$template->set_var("datetime",$project_log->get_datetime());
					$template->set_var("user",$user->get_full_name(false));
		
					if (($content = $project_log->get_content()) != null)
					{
						$template->set_var("content",$content);
					}
					else
					{
						$template->set_var("content",false);
					}
					
					$status_id = $project_log->get_status_id();
					
					if ($status_id != null)
					{
						$project_status = new ProjectStatus($status_id);
						$template->set_var("status",$project_status->get_name());
					}
					else
					{
						$template->set_var("status",false);
					}
					
					if ($project_log->get_important() == true)
					{
						$template->set_var("important",true);
					}
					else
					{
						$template->set_var("important",false);
					}
		
					$template->set_var("item",$result);
					
					$paramquery = $_GET;
					$paramquery[run]  = "log";
					unset($paramquery[id]);
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("back_link",$params);
				
					$template->output();
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			}
			else
			{
				throw new ProjectLogIDMissingException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}

	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function add_comment()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(3, false) == true)
			{
				if ($_GET[nextpage] == "1")
				{
					$no_error = true;
					if (!$_POST[text])
					{
						$no_error = false;
						$error = "You must enter a text";	
					}
					else
					{
						$error = "";
					}
				}
				else
				{
					$no_error = false;
					$error = "";
				}
				
				if ($no_error == false)
				{
					$template = new HTMLTemplate("project/project_log_add.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 1;
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params", $params);
					
					if ($_POST[text])
					{
						$template->set_var("textarea_content",$_POST[text]);
					}
					else
					{
						$template->set_var("textarea_content","");	
					}
					
					if ($_POST[important] == "1")
					{
						$template->set_var("important",2);
					}
					else
					{
						$template->set_var("important",1);	
					}
					
					$template->set_var("error",$error);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					$paramquery[run] = "detail";
					unset($paramquery[nextpage]);
					$params = http_build_query($paramquery);
					
					if ($_POST[important] == "1")
					{
						$important = true;
					}
					else
					{
						$important = false;
					}
					
					$project_log = new ProjectLog(null);
					
					if ($project_log->create($_GET[project_id], $_POST[text], false, $important))
					{
						Common_IO::step_proceed($params, "Project Log", "comment added successful",null);
					}
					else
					{
						Common_IO::step_proceed($params, "Project Log", "operation failed",null);
					}
				}
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
}
	
?>
