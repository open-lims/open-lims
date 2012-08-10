<?php
/**
 * @package project
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
 * Project Task IO Class
 * @package project
 */
class ProjectTaskIO
{
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function add()
	{
		global $user, $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(3, false) == true)
			{
				$project_id = $_GET[project_id];
				$project = new Project($project_id);
			
				if (!$_GET[nextpage])
				{
					$template = new HTMLTemplate("project/tasks/add.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->output();
				}
				else
				{
					switch($_POST[type]):
						case 1:
							if ($_GET[nextpage] == "2")
							{
								$page_2_passed = true;
								
								if ((!is_numeric($_POST[time1]) or $_POST[time1] > 24 or !is_numeric($_POST[time2]) or $_POST[time2] > 59))
								{
									if ($_POST[wholeday] != 1)
									{
										$error[2] = "<br />Enter a time or select \"whole day\"!";
										$page_2_passed = false;
									}
								}
								
								if (!$_POST[startdate])
								{
									$error[0] = "<br />Select a start date.";
									$page_2_passed = false;
								}
								else
								{
									$datetime_handler = new DatetimeHandler($_POST[date]);
									if ($datetime_handler->less_then_current())
									{
										$error[0] = "<br />Select a date in the future.";
										$page_2_passed = false;
									}
								}
								
								if (!$_POST[enddate])
								{
									$error[1] = "<br />Select an end date";
									$page_2_passed = false;
								}
								else
								{
									$datetime_handler = new DatetimeHandler($_POST[date]);
									if ($datetime_handler->less_then_current())
									{
										$error[1] = "<br />Select a date in the future.";
										$page_2_passed = false;
									}
								}
								
								if ($_POST[status])
								{
									if ($_POST[finalise] == 1)
									{
										$finalise = true;
									}
									else
									{
										$finalise = false;
									}
									
									if ($_POST[auto_connect] == 1)
									{
										$auto_connect = true;
									}
									else
									{
										$auto_connect = false;
									}
									
									$project_task = new ProjectTask(null);
									
									if ($project_task->check_status_process($project_id, $_POST[status], $finalise, $auto_connect) == true)
									{
										$error[3] = "<br />You have already created this task.";
										$page_2_passed = false;
									}	
								}
							}
							else
							{
								$page_2_passed = false;
							}
						
							if ($page_2_passed == false)
							{
								$template = new HTMLTemplate("project/tasks/add_status_process.html");
							
								$paramquery = $_GET;
								$paramquery[nextpage] = "2";
								$params = http_build_query($paramquery,'','&#38;');
								
								$template->set_var("params",$params);
								
								if ($error[0])
								{
									$template->set_var("error0", $error[0]);
								}
								else
								{
									$template->set_var("error0", "");
								}
								
								if ($error[1])
								{
									$template->set_var("error1", $error[1]);
								}
								else
								{
									$template->set_var("error1", "");
								}
								
								if ($error[2])
								{
									$template->set_var("error2", $error[2]);
								}
								else
								{
									$template->set_var("error2", "");
								}
								
								if ($error[3])
								{
									$template->set_var("error3", $error[3]);
								}
								else
								{
									$template->set_var("error3", "");
								}
								
								if ($_POST[startdate])
								{
									$template->set_var("startdate", $_POST[startdate]);
								}
								else
								{
									$template->set_var("startdate", date("Y-m-d"));
								}
								
								if ($_POST[enddate])
								{
									$template->set_var("enddate", $_POST[enddate]);
								}
								else
								{
									$template->set_var("enddate", "");
								}
								
								if ($_POST[time1])
								{
									$template->set_var("time1", $_POST[time1]);
								}
								else
								{
									$template->set_var("time1", "");
								}
								
								if ($_POST[time2])
								{
									$template->set_var("time2", $_POST[time2]);
								}
								else
								{
									$template->set_var("time2", "");
								}
								
								if ($_POST[comment])
								{
									$template->set_var("comment", $_POST[comment]);
								}
								else
								{
									$template->set_var("comment", "");
								}
								
								if ($_POST[task] == "2")
								{
									$template->set_var("achive_checked", "checked='checked'");
									$template->set_var("finalise_checked", "");
								}
								else
								{
									$template->set_var("achive_checked", "");
									$template->set_var("finalise_checked", "checked='checked'");
								}
								
								if ($_POST[wholeday] == "1")
								{
									$template->set_var("whole_day_checked", "checked='checked'");
								}
								else
								{
									$template->set_var("whole_day_checked", "");
								}
								
								$status_array = $project->get_all_status_array();
														
								if (is_array($status_array) and count($status_array) >= 1)
								{
									$status_content_array = array();
									$counter = 0;
									
									foreach($status_array as $key => $value)
									{
										if ($value[status] == 0)
										{
											$project_status = new ProjectStatus($value[id]);
											
											$status_content_array[$counter][value] = $value[id];
											$status_content_array[$counter][content] = $project_status->get_name();
											
											$counter++;
										}
									}
									$template->set_var("status",$status_content_array);
								}
								else
								{
									$status_content_array[0][value] = 0;
									$status_content_array[0][content] = "NO STATUS FOUND";
									
									$template->set_var("status",$status_content_array);		
								}
								
								$template->set_var("type", $_POST[type]);
								
								$template->output();
							}
							else
							{
								$paramquery = $_GET;
								unset($paramquery[nextpage]);
								$paramquery[run] = "detail";
								$params = http_build_query($paramquery);
								
								if ($_POST[wholeday] == 1)
								{
									$time = null;
									$whole_day = true;
								}
								else
								{
									$time = $_POST[time1].":".$_POST[time2].":00";
									$whole_day = false;
								}
								
								if ($_POST[finalise] == 1)
								{
									$finalise = true;
								}
								else
								{
									$finalise = false;
								}
								
								if ($_POST[auto_connect] == 1)
								{
									$auto_connect = true;
								}
								else
								{
									$auto_connect = false;
								}
								
								$project_task = new ProjectTask(null);
								
								if ($project_task->create_status_process($project_id, $user->get_user_id(), $_POST[comment], $_POST[startdate], null, $_POST[enddate], $time, $whole_day, $_POST[status], $finalise, $auto_connect))
								{
									Common_IO::step_proceed($params, "Add Project Task", "Operation Successful" ,null);
								}
								else
								{
									Common_IO::step_proceed($params, "Add Project Task", "Operation Failed" ,null);	
								}
							}
						break;
						
						case 2:
							if ($_GET[nextpage] == "2")
							{
								$page_2_passed = true;
								
								if ((!is_numeric($_POST[time1]) or $_POST[time1] > 24 or !is_numeric($_POST[time2]) or $_POST[time2] > 59))
								{
									if ($_POST[wholeday] != 1)
									{
										$error[3] = "<br />Enter a time or select \"whole day\"!";
										$page_2_passed = false;
									}
								}
								
								if (!$_POST[name])
								{
									$error[0] = "<br />Enter a name";
									$page_2_passed = false;
								}
								
								if (!$_POST[startdate])
								{
									$error[1] = "<br />Select a start date";
									$page_2_passed = false;
								}
								else
								{
									$datetime_handler = new DatetimeHandler($_POST[date]);
									if ($datetime_handler->less_then_current())
									{
										$error[1] = "<br />Select a date in the future";
										$page_2_passed = false;
									}
								}
								
								if (!$_POST[enddate])
								{
									$error[2] = "<br />Select an end date";
									$page_2_passed = false;
								}
								else
								{
									$datetime_handler = new DatetimeHandler($_POST[date]);
									if ($datetime_handler->less_then_current())
									{
										$error[2] = "<br />Select a date in the future";
										$page_2_passed = false;
									}
								}
							}
							else
							{
								$page_2_passed = false;
							}
						
							if ($page_2_passed == false)
							{
								$template = new HTMLTemplate("project/tasks/add_process.html");
								
								$paramquery = $_GET;
								$paramquery[nextpage] = "2";
								$params = http_build_query($paramquery,'','&#38;');
								
								$template->set_var("params",$params);
								
								if ($error[0])
								{
									$template->set_var("error0", $error[0]);
								}
								else
								{
									$template->set_var("error0", "");
								}
								
								if ($error[1])
								{
									$template->set_var("error1", $error[1]);
								}
								else
								{
									$template->set_var("error1", "");
								}
								
								if ($error[2])
								{
									$template->set_var("error2", $error[2]);
								}
								else
								{
									$template->set_var("error2", "");
								}
								
								if ($error[3])
								{
									$template->set_var("error3", $error[3]);
								}
								else
								{
									$template->set_var("error3", "");
								}
								
								if ($_POST[name])
								{
									$template->set_var("name", $_POST[name]);
								}
								else
								{
									$template->set_var("name", "");
								}
								
								if ($_POST[startdate])
								{
									$template->set_var("startdate", $_POST[startdate]);
								}
								else
								{
									$template->set_var("startdate", date("Y-m-d"));
								}
								
								if ($_POST[enddate])
								{
									$template->set_var("enddate", $_POST[enddate]);
								}
								else
								{
									$template->set_var("enddate", "");
								}
								
								if ($_POST[time1])
								{
									$template->set_var("time1", $_POST[time1]);
								}
								else
								{
									$template->set_var("time1", "");
								}
								
								if ($_POST[time2])
								{
									$template->set_var("time2", $_POST[time2]);
								}
								else
								{
									$template->set_var("time2", "");
								}
								
								if ($_POST[comment])
								{
									$template->set_var("comment", $_POST[comment]);
								}
								else
								{
									$template->set_var("comment", "");
								}
								
								$template->set_var("type", $_POST[type]);
								
								$template->output();
							}
							else
							{
								$paramquery = $_GET;
								unset($paramquery[nextpage]);
								$paramquery[run] = "detail";
								$params = http_build_query($paramquery);
								
								if ($_POST[wholeday] == 1)
								{
									$time = null;
									$whole_day = true;
								}
								else
								{
									$time = $_POST[time1].":".$_POST[time2].":00";
									$whole_day = false;
								}
								
								if ($_POST[finalise] == 1)
								{
									$finalise = true;
								}
								else
								{
									$finalise = false;
								}
								
								if ($_POST[auto_connect] == 1)
								{
									$auto_connect = true;
								}
								else
								{
									$auto_connect = false;
								}
								
								$project_task = new ProjectTask(null);
								
								if ($project_task->create_process($project_id, $user->get_user_id(), $_POST[comment], $_POST[startdate], null, $_POST[enddate], $time, $whole_day, $_POST[name], $auto_connect))
								{
									Common_IO::step_proceed($params, "Add Project Task", "Operation Successful" ,null);
								}
								else
								{
									Common_IO::step_proceed($params, "Add Project Task", "Operation Failed" ,null);	
								}		
							}
						break;
						
						case 3:
							if ($_GET[nextpage] == "2")
							{
								$page_2_passed = true;
								
								if (!$_POST[name])
								{
									$error[0] = "<br />Enter a name";
									$page_2_passed = false;
								}
								
								if (!$_POST[enddate])
								{
									$error[1] = "<br />Select an end date";
									$page_2_passed = false;
								}
								else
								{
									$datetime_handler = new DatetimeHandler($_POST[date]);
									if ($datetime_handler->less_then_current())
									{
										$error[1] = "<br />Select a date in the future";
										$page_2_passed = false;
									}
								}
							}
							else
							{
								$page_2_passed = false;
							}
						
							if ($page_2_passed == false)
							{
								$template = new HTMLTemplate("project/tasks/add_milestone.html");
								
								$paramquery = $_GET;
								$paramquery[nextpage] = "2";
								$params = http_build_query($paramquery,'','&#38;');
								
								$template->set_var("params",$params);
								
								if ($error[0])
								{
									$template->set_var("error0", $error[0]);
								}
								else
								{
									$template->set_var("error0", "");
								}
								
								if ($error[1])
								{
									$template->set_var("error1", $error[1]);
								}
								else
								{
									$template->set_var("error1", "");
								}
								
								if ($_POST[name])
								{
									$template->set_var("name", $_POST[name]);
								}
								else
								{
									$template->set_var("name", "");
								}
								
								if ($_POST[enddate])
								{
									$template->set_var("enddate", $_POST[enddate]);
								}
								else
								{
									$template->set_var("enddate", "");
								}
								
								if ($_POST[time1])
								{
									$template->set_var("time1", $_POST[time1]);
								}
								else
								{
									$template->set_var("time1", "");
								}
								
								if ($_POST[time2])
								{
									$template->set_var("time2", $_POST[time2]);
								}
								else
								{
									$template->set_var("time2", "");
								}
								
								if ($_POST[comment])
								{
									$template->set_var("comment", $_POST[comment]);
								}
								else
								{
									$template->set_var("comment", "");
								}
								
								$template->set_var("type", $_POST[type]);
								
								$template->output();
							}
							else
							{
								$paramquery = $_GET;
								unset($paramquery[nextpage]);
								$paramquery[run] = "detail";
								$params = http_build_query($paramquery);
								
								if (is_numeric($_POST[time1]) and is_numeric($_POST[time2]))
								{
									$time = $_POST[time1].":".$_POST[time2].":00";
								}
								else
								{
									$time = null;
								}
								
								if ($_POST[auto_connect] == 1)
								{
									$auto_connect = true;
								}
								else
								{
									$auto_connect = false;
								}
								
								$project_task = new ProjectTask(null);
								
								if ($project_task->create_milestone($project_id, $user->get_user_id(), $_POST[comment], $_POST[enddate], $time, $_POST[name], $auto_connect))
								{
									Common_IO::step_proceed($params, "Add Project Task", "Operation Successful" ,null);
								}
								else
								{
									Common_IO::step_proceed($params, "Add Project Task", "Operation Failed" ,null);	
								}
							}
						break;		
					endswitch;
				}
			}
			else
			{
				throw new ProjectSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectTaskIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function delete()
	{
		global $project_security;
		
		if ($_GET[id])
		{
			if ($project_security->is_access(3, false) == true)
			{
				if ($_GET[sure] != "true")
				{
					$template = new HTMLTemplate("project/tasks/delete.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "task_detail";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[id]);
					unset($paramquery[sure]);
					$paramquery[run] = "show_tasks";
					$params = http_build_query($paramquery);
					
					$project_task = new ProjectTask($_GET[id]);
					
					if ($project_task->delete())
					{							
						Common_IO::step_proceed($params, "Delete Project Task", "Operation Successful" ,null);
					}
					else
					{							
						Common_IO::step_proceed($params, "Delete Project Task", "Operation Failed" ,null);
					}			
				}
			}
			else
			{
				throw new ProjectSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectTaskIDMissingException();
		}
	}

	/**
	 * @throws ProjectTaskIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function edit_start()
	{
		global $project_security;
		
		if ($_GET[id])
		{
			if ($project_security->is_access(3, false) == true)
			{
				$project_task = new ProjectTask($_GET[id]);
			
				if (!$_GET[nextpage])
				{
					$template = new HTMLTemplate("project/tasks/edit_start.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params", $params);
					
					if ($_POST[startdate])
					{
						$template->set_var("startdate", $_POST[startdate]);
					}
					else
					{
						$template->set_var("startdate", $project_task->get_start_date());
					}
					
					if ($project_task->get_start_time() != null)
					{
						$time = explode(":", $project_task->get_start_time());
						
						if ($_POST[time1])
						{
							$template->set_var("time1", $_POST[time1]);
						}
						else
						{
							$template->set_var("time1", $time[0]);
						}
						
						if ($_POST[time2])
						{
							$template->set_var("time2", $_POST[time2]);
						}
						else
						{
							$template->set_var("time2", $time[1]);
						}
					}
					else
					{
						if ($_POST[time1])
						{
							$template->set_var("time1", $_POST[time1]);
						}
						else
						{
							$template->set_var("time1", "");
						}
						
						if ($_POST[time2])
						{
							$template->set_var("time2", $_POST[time2]);
						}
						else
						{
							$template->set_var("time2", "");
						}
					}
					
					$template->set_var("error0", "");
					$template->set_var("error1", "");
					
					$template->output();	
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "task_detail";
					$params = http_build_query($paramquery,'','&#38;');
					
					if (!$_POST[time1] or !$_POST[time2])
					{
						$time = null;
					}
					else
					{
						$time = $_POST[time1].":".$_POST[time2].":00";
					}
					
					if ($project_task->set_start($_POST[startdate], $time))
					{
						Common_IO::step_proceed($params, "Change Start Date/Time", "Operation Successful" ,null);
					}
					else
					{
						Common_IO::step_proceed($params, "Change Start Date/Time", "Operation Failed" ,null);	
					}
				}
			}
			else
			{
				throw new ProjectSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectTaskIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectTaskIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function edit_end()
	{
		global $project_security;
		
		if ($_GET[id])
		{
			if ($project_security->is_access(3, false) == true)
			{
				$project_task = new ProjectTask($_GET[id]);
			
				if (!$_GET[nextpage])
				{
					$template = new HTMLTemplate("project/tasks/edit_end.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params", $params);
					
					if ($_POST[enddate])
					{
						$template->set_var("enddate", $_POST[enddate]);
					}
					else
					{
						$template->set_var("enddate", $project_task->get_end_date());
					}
					
					if ($project_task->get_uf_end_time() != -1)
					{
						$time = explode(":", $project_task->get_uf_end_time());
						
						if ($_POST[wholeday] == 1)
						{
							$template->set_var("whole_day_checked", "checked");
						}
						else
						{
							$template->set_var("whole_day_checked", "");
						}
						
						if ($_POST[time1])
						{
							$template->set_var("time1", $_POST[time1]);
						}
						else
						{
							$template->set_var("time1", $time[0]);
						}
						
						if ($_POST[time2])
						{
							$template->set_var("time2", $_POST[time2]);
						}
						else
						{
							$template->set_var("time2", $time[1]);
						}
					}
					else
					{
						if ($_POST[wholeday] == 1 or !$_POST[wholeday])
						{
							$template->set_var("whole_day_checked", "checked");
						}
						else
						{
							$template->set_var("whole_day_checked", "");
						}
						
						if ($_POST[time1])
						{
							$template->set_var("time1", $_POST[time1]);
						}
						else
						{
							$template->set_var("time1", "");
						}
						
						if ($_POST[time2])
						{
							$template->set_var("time2", $_POST[time2]);
						}
						else
						{
							$template->set_var("time2", "");
						}
					}
					
					$template->set_var("error0", "");
					$template->set_var("error1", "");
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "task_detail";
					$params = http_build_query($paramquery,'','&#38;');
					
					if ($_POST[wholeday] == 1)
					{
						$time = null;
						$whole_day = true;
					}
					else
					{
						$time = $_POST[time1].":".$_POST[time2].":00";
						$whole_day = false;
					}
					
					if ($project_task->set_end($_POST[enddate], $time, $whole_day))
					{
						Common_IO::step_proceed($params, "Change End Date/Time", "Operation Successful" ,null);
					}
					else
					{
						Common_IO::step_proceed($params, "Change End Date/Time", "Operation Failed" ,null);	
					}
				}
			}
			else
			{
				throw new ProjectSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectTaskIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectTaskIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function detail()
	{
		global $user, $project_security;
	
		if ($_GET[id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project_task = new ProjectTask($_GET[id]);
				$project_task_owner = new User($project_task->get_owner_id());
			
				if ($project_task->get_start_time())
				{
					$project_task_start = new DatetimeHandler($project_task->get_start_date()." ".$project_task->get_start_time());
				}
				else
				{
					$project_task_start = new DatetimeHandler($project_task->get_start_date()." 00:00:00");
				}
				
				if ($project_task->get_uf_end_time() != -1)
				{
					$project_task_end = new DatetimeHandler($project_task->get_end_date()." ".$project_task->get_end_time());
				}
				else
				{
					$project_task_end = new DatetimeHandler($project_task->get_end_date()." 23:59:59");
				}
				
				$project_task_created_at = new DatetimeHandler($project_task->get_created_at());
				
				$template = new HTMLTemplate("project/tasks/detail.html");
				
				switch($project_task->get_type()):
					case 1:
						$template->set_var("type", "Status Related Task");
						$template->set_var("task_type", "1");
						$template->set_var("progress", $project_task->get_progress()."%");
						
						$project_status = new ProjectStatus($project_task->get_begin_status_id());
						
						$template->set_var("begin_status", $project_status->get_name());
					break;
					
					case 2:
						$template->set_var("type", "Task");
						$template->set_var("task_type", "2");
						$template->set_var("progress", $project_task->get_progress()."%");
					break;
					
					case 3:
						$template->set_var("type", "Milestone");
						$template->set_var("task_type", "3");
					break;
					
					default:
						$template->set_var("type", "Undefined");
						$template->set_var("task_type", "0");
					break;
				endswitch;
				
				$template->set_var("owner", $project_task_owner->get_full_name(false));
				$template->set_var("start", $project_task_start->get_formatted_string("l, jS F Y H:i"));
				$template->set_var("end", $project_task_end->get_formatted_string("l, jS F Y H:i"));
				
				if ($project_task->get_auto_connect() == true)
				{
					$template->set_var("auto_connect", "Yes");
				}
				else
				{
					$template->set_var("auto_connect", "No");
				}
				
				$template->set_var("created_at", $project_task_created_at->get_formatted_string("l, jS F Y H:i"));
				
				$template->set_var("name", $project_task->get_name());
				
				if ($user->get_user_id() == $project_task->get_owner_id() or $user->is_admin())
				{
					$template->set_var("task_admin", true);
					
					$paramquery = $_GET;
					$paramquery[run] = "task_edit_start";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("task_edit_start_params", $params);
					
					
					$paramquery = $_GET;
					$paramquery[run] = "task_edit_end";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("task_edit_end_params", $params);
					
					
					$paramquery = $_GET;
					$paramquery[run] = "task_delete";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("delete_params", $params);
				}
				else
				{
					$template->set_var("task_admin", false);
				}
				$template->output();
			}
			else
			{
				throw new ProjectSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectTaskIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function table_view()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$argument_array = array();
				$argument_array[0][0] = "project_id";
				$argument_array[0][1] = $_GET[project_id];
				
				$list = new List_IO("ProjectTaskTableList", "ajax.php?nav=project", "list_project_tasks", "count_project_tasks", $argument_array, "ProjectTaskTableList");

				$list->add_column("Name/Task","name",true,null);
				$list->add_column("Type","type",true,null);
				$list->add_column("Start Date","start_date",true,null);
				$list->add_column("End Date","end_date",true,null);
				$list->add_column("End Time","end_time",true,null);
				$list->add_column("Progress","progress",false,null);
				
				$template = new HTMLTemplate("project/tasks/table_view.html");
			
				$table_view_paramquery = $_GET;
				$table_view_paramquery[show] = "table";
				$table_view_params = http_build_query($table_view_paramquery, '', '&#38;');
				
				$template->set_var("table_view_params", $table_view_params);
				
				$gantt_view_paramquery = $_GET;
				$gantt_view_paramquery[show] = "gantt";
				$gantt_view_params = http_build_query($gantt_view_paramquery, '', '&#38;');
				
				$template->set_var("gantt_view_params", $gantt_view_params);
				
				$cal_view_paramquery = $_GET;
				$cal_view_paramquery[show] = "cal";
				$cal_view_params = http_build_query($cal_view_paramquery, '', '&#38;');
				
				$template->set_var("cal_view_params", $cal_view_params);
			
				$template->set_var("list", $list->get_list());
		
				$template->output();
			}
			else
			{
				throw new ProjectSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function gantt_view()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project_task_array = ProjectTask::list_tasks($_GET[project_id]);
				
				$left_limit = 0;
				$right_limit = 0;
				
				if (is_array($project_task_array) and count($project_task_array) >= 1)
				{
					foreach($project_task_array as $key => $value)
					{
						$project_task = new ProjectTask($value);
						if ($project_task->get_start_date())
						{
							$begin_timestamp = $project_task->get_start_date()." 00:00:00";
							
							if ($project_task->get_uf_end_time() != "-1")
							{
								$end_timestamp = $project_task->get_end_date()." ".$project_task->get_end_time();
							}
							else
							{
								$end_timestamp = $project_task->get_end_date()." 00:00:00";	
							}
							
							$begin_datetime_handler = new DatetimeHandler($begin_timestamp);
							$end_datetime_handler = new DatetimeHandler($end_timestamp);
							
							$begin_mktime = $begin_datetime_handler->get_mktime();
							$end_mktime = $end_datetime_handler->get_mktime();
										
							if ($left_limit == 0 or $left_limit > $begin_mktime)
							{
								$left_limit = $begin_mktime;
							}
							
							if ($right_limit == 0 or $right_limit < $end_mktime)
							{
								$right_limit = $end_mktime;
							}
						}
					}
					
					$left_limit_handler = new DatetimeHandler($left_limit);
					$right_limit_handler = new DatetimeHandler($right_limit);
					
					$day_distance = $left_limit_handler->day_distance($right_limit_handler);
					
					$date_range = $right_limit - $left_limit;
		
					if ($day_distance < 30)
					{
						if ($day_distance < 15)
						{
							if ($day_distance < 7)
							{
								$date_range_addition = ceil($date_range/($day_distance*4));
								$date_mktime = $left_limit;
								
								$number_of_rows = ($day_distance*4) + 4;	
							}
							else
							{
								$date_range_addition = ceil($date_range/($day_distance*2));
								$date_mktime = $left_limit;
								
								$number_of_rows = ($day_distance*2) + 2;	
							}
						}
						else
						{
							$date_range_addition = ceil($date_range/$day_distance);
							$date_mktime = $left_limit;
							
							$number_of_rows = $day_distance + 1;	
						}	
					}
					else
					{
						$date_range_addition = ceil($date_range/29);
						$date_mktime = $left_limit;
						
						$number_of_rows = 30;	
					}
					
					$width_of_row = 705/$number_of_rows;
							
					
					$gantt_array = array();
					
					for ($i=0; $i<=($number_of_rows-1); $i++)
					{
						$day = date("d", $date_mktime);
						$month = date("M", $date_mktime);
						
						$gantt_array[$i][title] = $day."<br />".$month;
						$gantt_array[$i][mktime] = $date_mktime;
						
						$date_mktime = $date_mktime + $date_range_addition;
					}
					
					$template = new HTMLTemplate("project/tasks/gantt_view.html");
				
					$table_view_paramquery = $_GET;
					$table_view_paramquery[show] = "table";
					$table_view_params = http_build_query($table_view_paramquery, '', '&#38;');
					
					$template->set_var("table_view_params", $table_view_params);
					
					$gantt_view_paramquery = $_GET;
					$gantt_view_paramquery[show] = "gantt";
					$gantt_view_params = http_build_query($gantt_view_paramquery, '', '&#38;');
					
					$template->set_var("gantt_view_params", $gantt_view_params);
					
					$cal_view_paramquery = $_GET;
					$cal_view_paramquery[show] = "cal";
					$cal_view_params = http_build_query($cal_view_paramquery, '', '&#38;');
					
					$template->set_var("cal_view_params", $cal_view_params);
					
					
					$table = "<table class='ProjectTaskGanttTable'>";
					
					$table .= "<tr>";
					
					foreach ($gantt_array as $key => $value)
					{
						$table .= "<th width='".$width_of_row."px'>".$value[title]."</th>";
					}
					
					$table .= "</tr>";
					
					foreach($project_task_array as $key => $value)
					{
						$project_task = new ProjectTask($value);
						
						$begin_timestamp = $project_task->get_start_date()." 00:00:00";
						if ($project_task->get_uf_end_time() != "-1")
						{
							$end_timestamp = $project_task->get_end_date()." ".$project_task->get_end_time();
						}
						else
						{
							$end_timestamp = $project_task->get_end_date()." 23:59:59";	
						}
						
						$begin_datetime_handler = new DatetimeHandler($begin_timestamp);
						$end_datetime_handler = new DatetimeHandler($end_timestamp);
						
						$begin_mktime = $begin_datetime_handler->get_mktime();
						$end_mktime = $end_datetime_handler->get_mktime();
						
						$table .= "<tr>";
			
						$paramquery = $_GET;
						$paramquery[run] = "task_detail";
						$paramquery[id] = $value;
						$params = http_build_query($paramquery,'','&#38;');
			
						$table .= "<td colspan='30'><span class='smallText'><a href='index.php?".$params."'>".$project_task->get_name()." (".$begin_datetime_handler->get_formatted_string("j/n/Y")." - ".$end_datetime_handler->get_formatted_string("j/n/Y").")</a></span></td>";
						
						$table .= "</tr><tr class='ProjectTaskGanttBlockEnd'>";
						
						$begin_key = -1;
						$end_key = -1;
						
						foreach ($gantt_array as $fe_key => $fe_value)
						{
							if ($fe_value[mktime] >= $begin_mktime)
							{
								if ($begin_key == -1)
								{
									$begin_key = $fe_key;
								}
							}
							if ($begin_key != -1 and $fe_value[mktime] <= $end_mktime)
							{
								$end_key = $fe_key;
							}
						}
									
						$key_range = $end_key - $begin_key + 1;
						$in_task = false;
						
						for ($i=0; $i<=($number_of_rows-1); $i++)
						{
							if ($i == $begin_key)
							{
								$in_task = true;
								$width = $key_range * ($width_of_row + 1);
								if ($project_task->get_type() == 3)
								{
									$table .= "<td colspan='".$key_range."' style='text-align: center;'><img src='images/icons/milestone.png' alt='' /></td>";
								}
								else
								{
									$table .= "<td colspan='".$key_range."'><img src='core/images/status_bar.php?length=".$width."&height=15&linecolor=A0A0A0&bgcolor=EAEAEA&color=".$project_task->get_color()."&value=".$project_task->get_progress()."' /></td>";
								}
							}	
							if ($in_task == false)
							{
								$table .= "<td></td>";
							}
							if ($i == $end_key)
							{
								$in_task = false;
							}
						}
						$table .= "</tr>";
					}
					$table .= "</table>";
					$template->set_var("table", $table);
					
					$template->output();	
				}
				else
				{
					$template = new HTMLTemplate("project/tasks/gantt_view.html");
				
					$table_view_paramquery = $_GET;
					$table_view_paramquery[show] = "table";
					$table_view_params = http_build_query($table_view_paramquery, '', '&#38;');
					
					$template->set_var("table_view_params", $table_view_params);
					
					$gantt_view_paramquery = $_GET;
					$gantt_view_paramquery[show] = "gantt";
					$gantt_view_params = http_build_query($gantt_view_paramquery, '', '&#38;');
					
					$template->set_var("gantt_view_params", $gantt_view_params);
					
					$cal_view_paramquery = $_GET;
					$cal_view_paramquery[show] = "cal";
					$cal_view_params = http_build_query($cal_view_paramquery, '', '&#38;');
					
					$template->set_var("cal_view_params", $cal_view_params);
				
					$template->set_var("table", "<span class='italic'>No Entries Found</span>");
				
					$template->output();
				}
			}
			else
			{
				throw new ProjectSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecuriyAccessDeniedException
	 */
	public static function calendar_view() {
		
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project_task_array = ProjectTask::list_tasks($_GET[project_id]);
				
				$calendar_array = array();
				$counter = 0;
				
				if (is_array($project_task_array) and count($project_task_array) >= 1)
				{
					foreach($project_task_array as $key => $value)
					{
						$project_task = new ProjectTask($value);
						
						if ($project_task->get_start_time())
						{
							$start_time = $project_task->get_start_time();
						}
						else
						{
							$start_time = "00:00:00";
						}
						
						if ($project_task->get_uf_end_time() > 0)
						{
							$end_time = $project_task->get_end_time();
						}
						else
						{
							$end_time = "23:59:59";
						}
						
						$paramquery = $_GET;
						$paramquery[run] = "task_detail";
						$paramquery[id] = $value;
						$params = http_build_query($paramquery,'','&#38;');
						
						$calendar_array[$counter][name] 		= $project_task->get_name();
						$calendar_array[$counter][start_date]	= $project_task->get_start_date();
						$calendar_array[$counter][start_time]	= $start_time;
						$calendar_array[$counter][end_date]		= $project_task->get_end_date();
						$calendar_array[$counter][end_time]		= $end_time;
						$calendar_array[$counter][color]		= "4284d3";
						$calendar_array[$counter][link]			= $params;
						$calendar_array[$counter][id]			= $value;
						$calendar_array[$counter][serial_id]	= 0;
						
						$counter++;
					}
				}
						
				$template = new HTMLTemplate("project/tasks/calendar_view.html");
				
				$table_view_paramquery = $_GET;
				$table_view_paramquery[show] = "table";
				$table_view_params = http_build_query($table_view_paramquery, '', '&#38;');
				
				$template->set_var("table_view_params", $table_view_params);
				
				$gantt_view_paramquery = $_GET;
				$gantt_view_paramquery[show] = "gantt";
				$gantt_view_params = http_build_query($gantt_view_paramquery, '', '&#38;');
				
				$template->set_var("gantt_view_params", $gantt_view_params);
				
				$cal_view_paramquery = $_GET;
				$cal_view_paramquery[show] = "cal";
				$cal_view_params = http_build_query($cal_view_paramquery, '', '&#38;');
				
				$template->set_var("cal_view_params", $cal_view_params);
				
				require_once("core/modules/organiser/io/organiser_library.io.php");
				$organiser_library_io = new OrganiserLibraryIO(31);
								
				$organiser_library_io->set_calendar_array($calendar_array);
				
				$template->set_var("content", $organiser_library_io->get_content());
				
				$template->output();
			}
			else
			{
				throw new ProjectSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
		
	public static function show()
	{
		switch($_GET[show]):
			case "gantt":
				self::gantt_view();
			break;
			
			case "cal":
				self::calendar_view();
			break;
			
			case "table":
			default:
				self::table_view();
			break;
		endswitch;
	}
	
	public static function list_upcoming_tasks()
	{
		$template = new HTMLTemplate("project/tasks/list_upcoming_tasks.html");
		
		$project_task = new ProjectTask(null);
		$project_task_array = $project_task->list_upcoming_tasks();
		
		if (is_array($project_task_array) and count($project_task_array) >= 1) {
			
			$template->set_var("exist_project_task", true);
			
			$content_array = array();
			$counter = 0;
			
			foreach ($project_task_array as $key => $value) {
				
				$paramquery = $_GET;
				$paramquery[nav] = "project";
				$paramquery[run] = "detail";
				$paramquery[project_id] = $value[project_id];
				$params = http_build_query($paramquery, '', '&#38;');
				
				if ($value[status] == 1) {
					$content_array[$counter][name] = "<span class='HomeTodayOverdueEntry'><a href='index.php?".$params."'>".$value[project_name]."</a> - ".$value[task_name]." - ".$value[end_date]."</span>";
				}else{
					$content_array[$counter][name] = "<a href='index.php?".$params."'>".$value[project_name]."</a> - ".$value[task_name]." - ".$value[end_date];
				}
				
				
				$counter++;
				
			}
			
			$template->set_var("project_task_array", $content_array);
			
		}else{
			$template->set_var("exist_project_task", false);
		}
		
		return $template->get_string();
	}
}

?>
