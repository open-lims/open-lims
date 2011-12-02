<?php
/**
 * @package base
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
 * User Search IO Class
 * @package base
 */
class UserSearchIO
{
	/**
	 * @param integer $language_id
	 * @return string
	 */
	public static function get_description($language_id)
	{
		return "Finds Users or Groups.";
	}
	
	/**
	 * @return string
	 */
	public static function get_icon()
	{
		return "images/icons_large/user_search_50.png";
	}
	
	public static function search()
	{
		global $user, $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[page] or $_GET[sortvalue] or $_GET[sortmethod])
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
			$template = new Template("template/user/search/search.html");
			
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
			
			if ($type == 1)
			{
				$list = new ListStat_IO(User_Wrapper::count_search_users($string), 20);
	
				$list->add_row("","symbol",false,16);
				$list->add_row("Username","username",false,null);
				$list->add_row("Full Name","fullname",false,null);
				
				if ($_GET[page])
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = User_Wrapper::list_search_users($string, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
					}
					else
					{
						$result_array = User_Wrapper::list_search_users($string, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
					}				
				}
				else
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = User_Wrapper::list_search_users($string, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
					}
					else
					{
						$result_array = User_Wrapper::list_search_users($string, null, null, 0, 20);
					}	
				}
				
				$type_name = "User";
				
				if (is_array($result_array) and count($result_array) >= 1)
				{
					foreach ($result_array as $key => $value)
					{
						$result_array[$key][symbol] = "<img src='images/icons/user.png' alt='N' border='0' />";
	
						$user_paramquery = array();
						$user_paramquery[username] = $_GET[username];
						$user_paramquery[session_id] = $_GET[session_id];
						$user_paramquery[nav] = "search";
						$user_paramquery[run] = "common_dialog";
						$user_paramquery[dialog] = "user_detail";
						$user_paramquery[id] = $value[id];
						$user_params = http_build_query($user_paramquery, '', '&#38;');
						
						$tmp_username = $result_array[$key][username];
						unset($result_array[$key][username]);
						$result_array[$key][username][content] = $tmp_username;
						$result_array[$key][username][link] = $user_params;
						
						if ($result_array[$key][id] == 1)
						{
							$result_array[$key][fullname] = "Administrator";
						}
					}
				}
				else
				{
					$list->override_last_line("<span class='italic'>No results found!</span>");
				}
			}
			else
			{
				$list = new ListStat_IO(User_Wrapper::count_search_groups($string), 20);
	
				$list->add_row("","symbol",false,16);
				$list->add_row("Group Name","name",true,null);
				$list->add_row("Users","users",true,null);
				
				if ($_GET[page])
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = User_Wrapper::list_search_groups($string, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
					}
					else
					{
						$result_array = User_Wrapper::list_search_groups($string, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
					}				
				}
				else
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = User_Wrapper::list_search_groups($string, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
					}
					else
					{
						$result_array = User_Wrapper::list_search_groups($string, null, null, 0, 20);
					}	
				}
				
				$type_name = "Group";
				
				if (is_array($result_array) and count($result_array) >= 1)
				{
					foreach ($result_array as $key => $value)
					{
						$result_array[$key][symbol] = "<img src='images/icons/groups.png' alt='N' border='0' />";
	
						$group_paramquery = array();
						$group_paramquery[username] = $_GET[username];
						$group_paramquery[session_id] = $_GET[session_id];
						$group_paramquery[nav] = "search";
						$group_paramquery[run] = "common_dialog";
						$group_paramquery[dialog] = "group_detail";
						$group_paramquery[id] = $value[id];
						$group_params = http_build_query($group_paramquery, '', '&#38;');
						
						$tmp_name = $result_array[$key][name];
						unset($result_array[$key][name]);
						$result_array[$key][name][content] = $tmp_name;
						$result_array[$key][name][link] = $group_params;
					}
				}
				else
				{
					$list->override_last_line("<span class='italic'>No results found!</span>");
				}
			}
						
			$template = new Template("template/user/search/search_result.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			unset($paramquery[page]);
			unset($paramquery[sortvalue]);
			unset($paramquery[sortmethod]);
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("string", $string);
			$template->set_var("type", $type_name);
			
			$template->set_var("table", $list->get_list($result_array, $_GET[page]));
	
			$template->output();
		}
	}
}
?>