<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Project Log AJAX IO Class
 * @package project
 */
class ProjectLogAjax
{	
	/**
	 * @param string $get_array
	 * @param intger $page
	 * @return integer
	 * @throws ProjectSecurityAccessDeniedException
	 * @throws ProjectIDMissingException
	 */
	public static function get_list($get_array, $page)
	{
		global $project_security, $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project_log_array = ProjectLog::list_entries_by_project_id($_GET['project_id']);
			
				if (!$page)
		    	{
					$page = 1;
				}
			
				$entry_count = count($project_log_array);
				$number_of_pages = ceil($entry_count/constant("PROJECT_LOG_ENTRIES_PER_PAGE"));
			
				$template = new HTMLTemplate("project/ajax/log.html");
				
				$template->set_var("get_array",$get_array);
				$template->set_var("page",$page);
				$template->set_var("number_of_pages",$number_of_pages);
				
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
						
						$result[$counter]['id'] = $value;
						$result[$counter]['show_more'] = false;
						
						$datetime_handler = new DatetimeHandler($project_log->get_datetime());
						$result[$counter]['date'] = $datetime_handler->get_date();
						$result[$counter]['time'] = $datetime_handler->get_time();
						$result[$counter]['user'] = $user->get_full_name(false);
						
						if (($content = $project_log->get_content()) != null)
						{
							$content = str_replace("\n","<br />", $content);
							if (strlen($content) > 500)
							{
								$content = substr($content,0,500)."...";
								$result[$counter]['show_more'] = true;
							}
							
							$result[$counter]['content'] = $content;
						}
						else
						{
							$result[$counter]['content'] = false;
						}
						
						$status_id = $project_log->get_status_id();
						
						if ($status_id != null)
						{
							$project_status = new ProjectStatus($status_id);
							$result[$counter]['status'] = $project_status->get_name();
						}
						else
						{
							$result[$counter]['status'] = false;
						}
						
						if ($project_log->get_important() == true)
						{
							$result[$counter]['important'] = true;
						}
						else
						{
							$result[$counter]['important'] = false;
						}
						
						$item_array = $project_log->list_items();
						$number_of_items = count($item_array);
						
						if ($number_of_items == 0)
						{
							$result[$counter]['items'] = false;
						}
						else
						{
							if ($number_of_items == 1)
							{
								$result[$counter]['items'] = $number_of_items." Item was added";
							}
							else
							{
								$result[$counter]['items'] = $number_of_items." Items were added";
							}
						}
						
						
						$detail_paramquery = $_GET;
						$detail_paramquery['run'] = "log_detail";
						$detail_paramquery['id'] = $value;
						$detail_params = http_build_query($detail_paramquery,'','&#38;');
						
						$result[$counter]['detail_params'] = $detail_params;
						
						if ($user->is_admin())
						{
							$result[$counter]['delete'] = true;
						}
						else
						{
							$result[$counter]['delete'] = false;
						}
						
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
					$pagebar = "<div id='ProjectLogActionSelect'></div><div class='ResultNextPageBar' id='ProjectLogPageBar'></div>";	
					$template->set_var("page_bar", $pagebar);
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
	 * @param string $get_array
	 * @return string
	 */
	public static function create($get_array)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			$project = new Project($_GET['project_id']);

			$template = new HTMLTemplate("project/log_create_window.html");
			$array['content_caption'] = "Create New Log Entry";
			$array['height'] = 430;
			$array['width'] = 400;

			$array['continue_caption'] = "Create";
			$array['cancel_caption'] = "Cancel";
			$array['content'] = $template->get_string();
			$array['container'] = "#ProjectLogCreateWindow";
			
			$continue_handler_template = new JSTemplate("project/js/log_create.js");
			$continue_handler_template->set_var("session_id", $_GET['session_id']);
			$continue_handler_template->set_var("get_array", $get_array);
			
			$array['continue_handler'] = $continue_handler_template->get_string();
			
			return json_encode($array);
		}
	}
	
	/**
	 * @param array $get_array
	 * @param string $comment
	 * @param string $important
	 * @return string
	 * @throws ProjectSecurityAccessDeniedException
	 * @throws ProjectIDMissingException
	 */
	public static function create_handler($get_array, $comment, $important)
	{
		global $project_security;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			if ($project_security->is_access(3, false) == true)
			{
				if ($important == "1")
				{
					$important = true;
				}
				else
				{
					$important = false;
				}
				
				$project_log = new ProjectLog(null);
				$project_log->create($_GET['project_id'], $comment, false, $important);
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
	 * @param integer $id
	 * @return string
	 * @throws ProjectLogIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function get_more($id)
	{
		global $project_security;
		
		if ($project_security->is_access(3, false) == true)
		{
			if (is_numeric($id))
			{
				$return_json_array = array();
				
				$project_log = new ProjectLog($id);
				
				$content = $project_log->get_content();
				$content = str_replace("\n","<br />",$content);
				
				if (($content = $project_log->get_content()) != null)
				{
					$content = str_replace("\n","<br />", $content);
					if (strlen($content) > 500)
					{
						$content = substr($content,500,strlen($content));
					}
					
					$return_json_array[0] = $content;
				}
				else
				{
					$return_json_array[0] = false;
				}
				
				$return_json_array[1] = "";
				$return_json_array[2] = "";
				$return_json_array[3] = "show less";
				
				return json_encode($return_json_array);
			}
			else
			{
				throw new ProjectLogIDMissingException();
			}
		}
		else
		{
			throw new ProjectSecurityAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $id
	 * @return string
	 * @throws ProjectLogIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function get_less($id)
	{
		global $project_security;
		
		if ($project_security->is_access(3, false) == true)
		{
			if (is_numeric($id))
			{
				$return_json_array = array();
				
				$project_log = new ProjectLog($id);
				
				if (($content = $project_log->get_content()) != null)
				{
					$content = str_replace("\n","<br />", $content);
					if (strlen($content) > 500)
					{
						$content = substr($content,0,500)."...";
					}
					
					$return_json_array[0] = $content;
				}
				else
				{
					$return_json_array[0] = false;
				}
				
				$status_id = $project_log->get_status_id();
				
				if ($status_id != null)
				{
					$project_status = new ProjectStatus($status_id);
					$return_json_array[1] = $project_status->get_name();
				}
				else
				{
					$return_json_array[1] = false;
				}
				
				$item_array = $project_log->list_items();
				$number_of_items = count($item_array);
				
				if ($number_of_items == 0)
				{
					$return_json_array[2] = false;
				}
				else
				{
					if ($number_of_items == 1)
					{
						$return_json_array[2] = $number_of_items." Item was added";
					}
					else
					{
						$return_json_array[2] = $number_of_items." Items were added";
					}
				}
				
				$return_json_array[3] = "Show more";
				
				return json_encode($return_json_array);
			}
			else
			{
				throw new ProjectLogIDMissingException();
			}
		}
		else
		{
			throw new ProjectSecurityAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $id
	 * @return string
	 * @throws ProjectLogIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function delete($id)
	{
		global $user;
		
		if (is_numeric($id))
		{
			if ($user->is_admin())
			{
				$project_log = new ProjectLog($id);
				$project_log->delete();
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
}