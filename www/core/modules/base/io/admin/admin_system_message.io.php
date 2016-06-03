<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * System Messages Admin IO Class
 * @package base
 */
class AdminSystemMessageIO
{
	public static function home()
	{
		define(SYSTEM_MESSAGE_ENTRIES_PER_PAGE, 6);
		
		$system_message_array = SystemMessage::list_entries();
	
		if (!$_GET['page'])
    	{
			$page = 1;
		}
		else
		{
			$page = $_GET['page'];	
		}

		$entry_count = count($system_message_array);
		$number_of_pages = ceil($entry_count/SYSTEM_MESSAGE_ENTRIES_PER_PAGE);
		
		$template = new HTMLTemplate("base/admin/system_message/list.html");	
		
		$paramquery = $_GET;
		$paramquery['action'] = "add";
		unset($paramquery['nextpage']);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
		
		if (is_array($system_message_array) and count($system_message_array) >= 1)
		{	
			$template->set_var("no_entry",false);
			
			$result = array();
			$counter = 0;
			
			if (count($system_message_array) < ($page*SYSTEM_MESSAGE_ENTRIES_PER_PAGE))
			{
				$max_for = (count($system_message_array) % SYSTEM_MESSAGE_ENTRIES_PER_PAGE) - 1;
			}
			else
			{
				$max_for = SYSTEM_MESSAGE_ENTRIES_PER_PAGE-1;
			}
			
			for ($i=0;$i<=$max_for;$i++)
			{
				$entry = ($page*SYSTEM_MESSAGE_ENTRIES_PER_PAGE)+$i-SYSTEM_MESSAGE_ENTRIES_PER_PAGE; // Erzeugt Entry-ID
				$value = $system_message_array[$entry];
				
				$system_message 	= new SystemMessage($value);
				$user 				= new User($system_message->get_user_id());
				$datetime_handler 	= new DatetimeHandler($system_message->get_datetime());
				
				$content = str_replace("\n", "<br />", $system_message->get_content());
				$content = str_replace("\\", "", $content);
				
				$result[$counter]['user'] = $user->get_full_name(false);
				$result[$counter]['datetime'] = $datetime_handler->get_date()." at ".$datetime_handler->get_time();
				$result[$counter]['content'] = $content;
				
				
				$paramquery = $_GET;
				$paramquery['action'] = "edit";
				$paramquery['id'] = $value;
				unset($paramquery['nextpage']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$result[$counter]['edit_params'] = $params;
				
				
				$paramquery = $_GET;
				$paramquery['action'] = "delete";
				$paramquery['id'] = $value;
				unset($paramquery['nextpage']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$result[$counter]['delete_params'] = $params;
				
				$counter++;
			}
			$template->set_var("message_array", $result);
		}
		else
		{
			$template->set_var("no_entry",true);
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

	public static function create()
	{
		global $user;
		
		if ($_GET['nextpage'] == 1)
		{
			$page_1_passed = true;
			
			if (!$_POST['content'])
			{
				$page_1_passed = false;
				$error = "You must enter a text";
			}
		}
		else
		{
			$page_1_passed = false;
			$error = "";
		}

		if ($page_1_passed == false)
		{
			$template = new HTMLTemplate("base/admin/system_message/add.html");
			
			$paramquery = $_GET;
			$paramquery['nextpage'] = "1";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params",$params);
			
			if ($error)
			{
				$template->set_var("error", $error);
			}
			else
			{
				$template->set_var("error", "");	
			}
						
			if ($_POST['content'])
			{
				$template->set_var("content", $_POST['content']);
			}
			else
			{
				$template->set_var("content", "");
			}
						
			$template->output();
		}
		else
		{
			$paramquery = $_GET;
			unset($paramquery['nextpage']);
			unset($paramquery['action']);
			$params = http_build_query($paramquery);
			
			$system_message = new SystemMessage(null);
			
			if ($system_message->create($user->get_user_id(), $_POST['content']))
			{
				Common_IO::step_proceed($params, "Add System Message", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Add System Message", "Operation Failed" ,null);	
			}				
		}
	}
	
	/**
	 * @throws SystemMessageIDMissingException
	 */
	public static function delete()
	{
		if ($_GET['id'])
		{
			$system_message = new SystemMessage($_GET['id']);
		
			if ($_GET['sure'] != "true")
			{
				$template = new HTMLTemplate("base/admin/system_message/delete.html");
				
				$paramquery = $_GET;
				$paramquery['sure'] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery['sure']);
				unset($paramquery['action']);
				unset($paramquery['id']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery['sure']);
				unset($paramquery['action']);
				unset($paramquery['id']);
				$params = http_build_query($paramquery,'','&#38;');
								
				if ($system_message->delete())
				{							
					Common_IO::step_proceed($params, "Delete System Message", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete System Message", "Operation Failed" ,null);
				}		
			}
		}
		else
		{
			throw new SystemMessageIDMissingException();
		}
	}
	
	/**
	 * @throws SystemMessageIDMissingException
	 */
	public static function edit()
	{
		if ($_GET['id'])
		{
			$system_message = new SystemMessage($_GET['id']);
		
			if ($_GET['nextpage'] == 1)
			{
				$page_1_passed = true;
				
				if (!$_POST['content'])
				{
					$page_1_passed = false;
					$error = "You must enter a text";
				}
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
	
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("base/admin/system_message/edit.html");
				
				$paramquery = $_GET;
				$paramquery['nextpage'] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				if ($error)
				{
					$template->set_var("error", $error);
				}
				else
				{
					$template->set_var("error", "");	
				}
				
				$content = str_replace("\\", "", $system_message->get_content());
							
				if ($_POST['content'])
				{
					$template->set_var("content", $_POST['content']);
				}
				else
				{
					$template->set_var("content", $content);
				}
							
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery['nextpage']);
				unset($paramquery['action']);
				$params = http_build_query($paramquery);

				if ($system_message->set_content($_POST['content']))
				{
					Common_IO::step_proceed($params, "Add System Message", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Add System Message", "Operation Failed" ,null);	
				}			
			}
		}
		else
		{
			throw new SystemMessageIDMissingException();
		}
	}
	
	public static function handler()
	{
		switch($_GET['action']):
			case "add":
				self::create();
			break;
			
			case "edit":
				self::edit();
			break;
			
			case "delete":
				self::delete();
			break;
						
			default:
				self::home();
			break;
		endswitch;
	}
	
}

?>
