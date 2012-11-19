<?php
/**
 * @package base
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
			$template = new HTMLTemplate("base/user/search/search.html");
			
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
			
			$argument_array = array();
			$argument_array[0][0] = "string";
			$argument_array[0][1] = $string;
			
			if ($type == 1)
			{
				$list = new List_IO("UserUserSearch", "ajax.php?nav=base", "search_user_list_users", "search_user_count_users", $argument_array, "UserUserSearch");
				
				$list->add_column("","symbol",false,16);
				$list->add_column("Username","username",true,null);
				$list->add_column("Full Name","fullname",true,null);
				
				$type_name = "User";
			}
			else
			{	
				$list = new List_IO("UserGroupSearch", "ajax.php?nav=base", "search_user_list_groups", "search_user_count_groups", $argument_array, "UserGroupSearch");
				
				$list->add_column("","symbol",false,16);
				$list->add_column("Group Name","name",true,null);
				$list->add_column("Users","users",true,null);

				$type_name = "Group";
			}
						
			$template = new HTMLTemplate("base/user/search/search_result.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			unset($paramquery[page]);
			unset($paramquery[sortvalue]);
			unset($paramquery[sortmethod]);
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("string", $string);
			$template->set_var("type", $type_name);
			
			$template->set_var("list", $list->get_list());	
	
			$template->output();
		}
	}
}
?>