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
 * User IO Class
 * @package base
 */
class UserIO
{
	public static function profile()
	{
		global $user;
		
		$template = new HTMLTemplate("base/user/user_profile.html");
		
		if ($user->get_profile("gender") == "m")
		{
			$template->set_var("gender", true);
		}
		else
		{
			$template->set_var("gender", false);
		}
		
		if ($user->get_boolean_user_entry("can_change_password") == true)
		{
			$template->set_var("can_change_password", true);
		}
		else
		{
			$template->set_var("can_change_password", false);
		}
				
		$paramquery_personal = $_GET;
		$paramquery_personal[run] = "user_change_personal";
		$param_personal = http_build_query($paramquery_personal);
		
		$template->set_var("params_change_personal",$param_personal);
		
		
		$paramquery_my_settings = $_GET;
		$paramquery_my_settings[run] = "user_change_my_settings";
		$param_my_settings = http_build_query($paramquery_my_settings);
			
		$template->set_var("params_my_settings",$param_my_settings);
		
		
		$paramquery_password = $_GET;
		$paramquery_password[run] = "user_change_password";
		$param_password = http_build_query($paramquery_password);
			
		$template->set_var("params_change_password",$param_password);
		
		
		$template->output();
	}
	
	public static function change_personal()
	{
		global $user;
		
		$no_error = false;
		if ($_GET[nextpage] == 1)
		{
			$no_error = true;
			
			if (!$_POST[forename])
			{
				$no_error = false;
				$error[0] = "<br /><span class='formError'>This field cannot be empty</span>";
			}
			else
			{
				$error[0] = "";
			}
			
			if (!$_POST[surname])
			{
				$no_error = false;
				$error[1] = "<br /><span class='formError'>This field cannot be empty</span>";
			}
			else
			{
				$error[1] = "";
			}
			
			if (!$_POST[mail])
			{
				$no_error = false;
				$error[2] = "<br /><span class='formError'>This field cannot be empty</span>";
			}
			else
			{
				$error[2] = "";
			}
			
			if ($_POST[icq])
			{
				if (!is_numeric($_POST[icq]))
				{
					$no_error = false;
					$error[3] = "<br /><span class='formError'>The value is invalid</span>";
				}
				else
				{
					$error[3] = "";
				}
			}
			else
			{
				$error[3] = "";
			}
		}
		else
		{
			$error[0] = "";
			$error[1] = "";
			$error[2] = "";
			$error[3] = "";
		}
		
		
		if ($no_error == true)
		{
			$paramquery = $_GET;
			unset($paramquery[nextpage]);
			$paramquery[run] = "user_profile";
			$params = http_build_query($paramquery);
			
			Common_IO::step_proceed($params, "Change Personal Data", "Data Changed",null);
			
			$user->set_profile("gender",$_POST[gender]);
			$user->set_profile("title",$_POST[title]);
			$user->set_profile("forename",$_POST[forename]);
			$user->set_profile("surname",$_POST[surname]);
			
			$user->set_profile("mail",$_POST[mail]);
			$user->set_profile("institution",$_POST[institution]);
			$user->set_profile("department",$_POST[department]);
			$user->set_profile("street",$_POST[street]);
			$user->set_profile("zip",$_POST[zip]);
			$user->set_profile("city",$_POST[city]);
			$user->set_profile("country",$_POST[country]);
			$user->set_profile("phone",$_POST[phone]);
			
			$user->set_profile("icq",$_POST[icq]);
			$user->set_profile("msn",$_POST[msn]);
			$user->set_profile("yahoo",$_POST[yahoo]);
			$user->set_profile("aim",$_POST[aim]);
			$user->set_profile("skype",$_POST[skype]);
		}
		else
		{
			$template = new HTMLTemplate("base/user/user_change_personal.html");
			
			$template->set_var("error_0",$error[0]);
			$template->set_var("error_1",$error[1]);
			$template->set_var("error_2",$error[2]);
			$template->set_var("error_3",$error[3]);
			
			$paramquery = $_GET;
			$paramquery[nextpage] = 1;
			$params = http_build_query($paramquery);
			
			$template->set_var("params", $params);
			
			$gender = $user->get_profile("gender");
			
			if ($gender == "m")
			{
				$template->set_var("gender", true);
			}
			else
			{
				$template->set_var("gender", false);
			}
			
			if ($user->get_profile("forename"))
			{
				$template->set_var("forename",$user->get_profile("forename"));
			}
			else
			{
				$template->set_var("forename","");
			}
			
			if ($user->get_profile("surname"))
			{
				$template->set_var("surname",$user->get_profile("surname"));
			}
			else
			{
				$template->set_var("surname","");
			}
			
			if ($user->get_profile("title"))
			{
				$template->set_var("title",$user->get_profile("title"));
			}
			else
			{
				$template->set_var("title","");
			}
			
			
			if ($user->get_profile("mail"))
			{
				$template->set_var("mail",$user->get_profile("mail"));
			}
			else
			{
				$template->set_var("mail","");
			}
			
			if ($user->get_profile("institution"))
			{
				$template->set_var("institution",$user->get_profile("institution"));
			}
			else
			{
				$template->set_var("institution","");
			}
			
			if ($user->get_profile("department"))
			{
				$template->set_var("department",$user->get_profile("department"));
			}
			else
			{
				$template->set_var("department","");
			}
			
			if ($user->get_profile("street"))
			{
				$template->set_var("street",$user->get_profile("street"));
			}
			else
			{
				$template->set_var("street","");
			}
			
			if ($user->get_profile("zip"))
			{
				$template->set_var("zip",$user->get_profile("zip"));
			}
			else
			{
				$template->set_var("zip","");
			}
			
			if ($user->get_profile("city"))
			{
				$template->set_var("city",$user->get_profile("city"));
			}
			else
			{
				$template->set_var("city","");
			}
			
			if ($user->get_profile("country"))
			{
				$template->set_var("country",$user->get_profile("country"));
			}
			else
			{
				$template->set_var("country","");
			}
			
			if ($user->get_profile("phone"))
			{
				$template->set_var("phone",$user->get_profile("phone"));
			}
			else
			{
				$template->set_var("phone","");
			}


			if ($user->get_profile("icq"))
			{
				$template->set_var("icq",$user->get_profile("icq"));
			}
			else
			{
				$template->set_var("icq","");
			}
			
			if ($user->get_profile("msn"))
			{
				$template->set_var("msn",$user->get_profile("msn"));
			}
			else
			{
				$template->set_var("msn","");
			}
			
			if ($user->get_profile("yahoo"))
			{
				$template->set_var("yahoo",$user->get_profile("yahoo"));
			}
			else
			{
				$template->set_var("yahoo","");
			}
			
			if ($user->get_profile("aim"))
			{
				$template->set_var("aim",$user->get_profile("aim"));
			}
			else
			{
				$template->set_var("aim","");
			}
			
			if ($user->get_profile("skype"))
			{
				$template->set_var("skype",$user->get_profile("skype"));
			}
			else
			{
				$template->set_var("skype","");
			}
			$template->output();
		}
	}
	
	public static function change_my_settings()
	{
		global $regional;
		
		$template = new HTMLTemplate("base/user/user_settings.html");

		
		$language_array = Language::list_languages();
		
		$result = array();
		$counter = 0;
		
		if (is_array($language_array))
		{
			foreach($language_array as $key => $value)
			{
				$language = new Language($value);
				
				$result[$counter][value] = $value;
				$result[$counter][content] = $language->get_full_name();
				
				if ($value == $regional->get_language_id())
				{
					$result[$counter][selected] = "selected='selected'";
				}
				else
				{
					$result[$counter][selected] = "";
				}
				$counter++;		
			}
		}
		
		$template->set_var("language",$result);
		
		
		$timezone_array = Timezone::list_timezones();
			
		$result = array();
		$counter = 0;
		
		if (is_array($timezone_array))
		{
			foreach($timezone_array as $key => $value)
			{
				$timezone = new Timezone($value);
				
				$result[$counter][value] = $value;
				$result[$counter][content] = $timezone->get_name();
				
				if ($value == $regional->get_timezone_id())
				{
					$result[$counter][selected] = "selected='selected'";
				}
				else
				{
					$result[$counter][selected] = "";
				}
				$counter++;
			}
		}
		$template->set_var("timezone",$result);
			
		$template->output();
	}
	
	public static function change_password()
	{
		global $user;
		
		$no_error = false;
		
		if ($_GET[nextpage] == 1)
		{
			$no_error = true;
			
			if (!$_POST[current_password])
			{
				$no_error = false;
				$error[0] = "<br /><span class='formError'>this field cannot be empty</span>";
			}
			else
			{
				$error[0] = "";
			}
			
			if (!$_POST[new_password_1])
			{
				$no_error = false;
				$error[1] = "<br /><span class='formError'>this field cannot be empty</span>";
			}
			else
			{
				$error[1] = "";	
			}
			
			if (!$_POST[new_password_2])
			{
				$no_error = false;
				$error[2] = "<br /><span class='formError'>this field cannot be empty</span>";
			}
			else
			{
				$error[2] = "";
			}
			
			if ($_POST[new_password_1] and $_POST[new_password_2] and $_POST[new_password_1] != $_POST[new_password_2])
			{
				$no_error = false;
				$error[2] = "<br /><span class='formError'>the new passwords are not equal</span>";
			}
			elseif(!$error[2])
			{
				$error[2] = "";
			}
											
			if ($user->check_password($_POST[current_password]) == false)
			{
				$noerror = false;
				$error[0] = "<br /><span class='formError'>current password is wrong</span>";
			}
			elseif($error[0])
			{
				$error[0] = "";
			}
		}
		else
		{
			$error[0] = "";
			$error[1] = "";
			$error[2] = "";
		}
		
		if ($no_error == true)
		{
			$paramquery = $_GET;
			unset($paramquery[nextpage]);
			$paramquery[run] = "user_profile";
			$params = http_build_query($paramquery);
			
			Common_IO::step_proceed($params, "Change Password", "Password Changed",null);
			
			$user->set_password($_POST[new_password_1]);
		}
		else
		{
			$template = new HTMLTemplate("base/user/user_change_password.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = 1;
			$params = http_build_query($paramquery);
			
			$template->set_var("params", $params);
			
			$template->set_var("error_0",$error[0]);
			$template->set_var("error_1",$error[1]);
			$template->set_var("error_2",$error[2]);
			
			$template->output();
		}
	}

	public static function change_password_on_login()
	{
		global $user;
		
		$no_error = false;
		
		if ($_GET[nextpage] == 1)
		{
			$no_error = true;
			
			if (!$_POST[new_password_1])
			{
				$no_error = false;
				$error[1] = "<br /><span class='formError'>this field cannot be empty</span>";
			}
			else
			{
				$error[1] = "";	
			}
			
			if (!$_POST[new_password_2])
			{
				$no_error = false;
				$error[2] = "<br /><span class='formError'>this field cannot be empty</span>";
			}
			else
			{
				$error[2] = "";
			}
			
			if ($_POST[new_password_1] and $_POST[new_password_2] and $_POST[new_password_1] != $_POST[new_password_2])
			{
				$no_error = false;
				$error[2] = "<br /><span class='formError'>the new passwords are not equal</span>";
			}
			elseif(!$error[2])
			{
				$error[2] = "";
			}
		}
		else
		{
			$error[0] = "";
			$error[1] = "";
			$error[2] = "";
		}
		
		if ($no_error == true)
		{	
			$paramquery = array();
			$paramquery[username] = $_GET[username];
			$paramquery[session_id] = $_GET[session_id];
			$params = http_build_query($paramquery);
			
			if ($user->set_password_on_login($_POST[new_password_1]))
			{
				Common_IO::step_proceed($params, "Change Password Succesful", "Password Changed",null);
			}
			else
			{
				Common_IO::step_proceed($params, "Change Password Failed", "Password Changed",null);
			}
		}
		else
		{
			$template = new HTMLTemplate("base/user/user_change_password_on_login.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = 1;
			$params = http_build_query($paramquery);
			
			$template->set_var("params", $params);
			
			$template->set_var("error_0",$error[0]);
			$template->set_var("error_1",$error[1]);
			$template->set_var("error_2",$error[2]);
			
			$template->output();
		}
	}

	/**
	 * @throws UserIDMissingException
	 */
	public static function user_details()
	{
		if ($_GET['id'])
		{
			$user = new User($_GET['id']);
			
			$template = new HTMLTemplate("base/user/user_details.html");
			
			if ($user->get_username())
			{
				$template->set_var("username",$user->get_username());
			}
			else
			{
				$template->set_var("username","");
			}
			
			if ($user->get_profile("gender") == "m")
			{
				$template->set_var("gender", "male");
			}
			else
			{
				$template->set_var("gender", "female");
			}
			
			if ($user->get_profile("forename"))
			{
				$template->set_var("forename",$user->get_profile("forename"));
			}
			else
			{
				$template->set_var("forename","");
			}
			
			if ($user->get_profile("surname"))
			{
				$template->set_var("surname",$user->get_profile("surname"));
			}
			else
			{
				$template->set_var("surname","");
			}
			
			if ($user->get_profile("title"))
			{
				$template->set_var("title",$user->get_profile("title"));
			}
			else
			{
				$template->set_var("title","");
			}
			
			
			if ($user->get_profile("mail"))
			{
				$template->set_var("mail",$user->get_profile("mail"));
			}
			else
			{
				$template->set_var("mail","");
			}
			
			if ($user->get_profile("institution"))
			{
				$template->set_var("institution",$user->get_profile("institution"));
			}
			else
			{
				$template->set_var("institution","");
			}
			
			if ($user->get_profile("department"))
			{
				$template->set_var("department",$user->get_profile("department"));
			}
			else
			{
				$template->set_var("department","");
			}
			
			if ($user->get_profile("street"))
			{
				$template->set_var("street",$user->get_profile("street"));
			}
			else
			{
				$template->set_var("street","");
			}
			
			if ($user->get_profile("zip"))
			{
				$template->set_var("zip",$user->get_profile("zip"));
			}
			else
			{
				$template->set_var("zip","");
			}
			
			if ($user->get_profile("city"))
			{
				$template->set_var("city",$user->get_profile("city"));
			}
			else
			{
				$template->set_var("city","");
			}
			
			if ($user->get_profile("country"))
			{
				$template->set_var("country",$user->get_profile("country"));
			}
			else
			{
				$template->set_var("country","");
			}
			
			if ($user->get_profile("phone"))
			{
				$template->set_var("phone",$user->get_profile("phone"));
			}
			else
			{
				$template->set_var("phone","");
			}


			if ($user->get_profile("icq"))
			{
				$template->set_var("icq",$user->get_profile("icq"));
			}
			else
			{
				$template->set_var("icq","");
			}
			
			if ($user->get_profile("msn"))
			{
				$template->set_var("msn",$user->get_profile("msn"));
			}
			else
			{
				$template->set_var("msn","");
			}
			
			if ($user->get_profile("yahoo"))
			{
				$template->set_var("yahoo",$user->get_profile("yahoo"));
			}
			else
			{
				$template->set_var("yahoo","");
			}
			
			if ($user->get_profile("aim"))
			{
				$template->set_var("aim",$user->get_profile("aim"));
			}
			else
			{
				$template->set_var("aim","");
			}
			
			if ($user->get_profile("skype"))
			{
				$template->set_var("skype",$user->get_profile("skype"));
			}
			else
			{
				$template->set_var("skype","");
			}
			
			$group_array = Group::list_user_releated_groups($_GET[id]);
			$group_content_array = array();
			
			$counter = 0;
			
			if (is_array($group_array) and count($group_array) >= 1)
			{
				foreach($group_array as $key => $value) {
					
					$group = new Group($value);
					
					$paramquery = $_GET;
					$paramquery[dialog] = "group_detail";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$group_content_array[$counter][name] = $group->get_name();
					$group_content_array[$counter][params] = $params;
					
					$counter++;
				}
				$template->set_var("no_group", false);
			}
			else
			{
				$template->set_var("no_group", true);
			}
			
			$template->set_var("group", $group_content_array);
			
			$template->output();
		}
		else
		{
			throw new UserIDMissingException();
		}
	}

	/**
	 * @throws GroupIDMissingException
	 */
	public static function group_details()
	{
		if ($_GET['id'])
		{
			$group = new Group($_GET['id']);
			
			$template = new HTMLTemplate("base/user/group_details.html");

			$template->set_var("name", $group->get_name());
			
			$user_array = Group::list_group_releated_users($_GET[id]);
			$user_content_array = array();
			
			$counter = 0;
			
			if (is_array($user_array) and count($user_array) >= 1)
			{
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					
					$paramquery = $_GET;
					$paramquery[dialog] = "user_detail";
					$paramquery[id] = $value;
					$params = http_build_query($paramquery,'','&#38;');
					
					$user_content_array[$counter][username] = $user->get_username();
					$user_content_array[$counter][fullname] = $user->get_full_name(false);
					$user_content_array[$counter][params] = $params;
					
					$counter++;
				}
				$template->set_var("no_user", false);
			}
			else
			{
				$template->set_var("no_user", true);
			}
			
			$template->set_var("user", $user_content_array);
			
			
			$organisation_unit_array = OrganisationUnit::list_entries_by_group_id($_GET[id]);
			$organisation_unit_content_array = array();
			
			$counter = 0;
			
			if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
			{
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
					$organisation_unit_content_array[$counter][name] = $organisation_unit->get_name();
					$counter++;
				}
				$template->set_var("no_ou", false);
			}
			else
			{
				$template->set_var("no_ou", true);
			}
			
			$template->set_var("ou", $organisation_unit_content_array);
			
			
			$template->output();
		}
		else
		{
			throw new GroupIDMissingException();
		}
	}

}

?>
