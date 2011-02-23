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
 * Main Navigation IO Class
 * @todo problems with back button and tabbed browsing
 * @todo solve dependencies
 * @package base
 */
class MainNavigation_IO
{
	private $current_tab;
	
	private $in_project;
	private $in_sample;
	
	function __construct()
	{
		global $session;
		
		if ($_GET[change_tab] == "true")
		{
			$session->write_value("CURRENT_IN_PROJECT", false, true);
			$session->write_value("CURRENT_IN_SAMPLE", false, true);
			
			switch($_GET[nav]):
			
				case("home"):
					$session->write_value("CURRENT_TAB", "HOME", true);
				break;
				
				case("projects"):
					$session->write_value("CURRENT_TAB", "PROJECTS", true);
				break;
				
				case("samples"):
					$session->write_value("CURRENT_TAB", "SAMPLES", true);
				break;
				
				case("data"):
					$session->write_value("CURRENT_TAB", "DATA", true);
				break;
				
				case("search"):
					$session->write_value("CURRENT_TAB", "SEARCH", true);
				break;
				
				case("organiser"):
					$session->write_value("CURRENT_TAB", "ORGANISER", true);
				break;
				
				case("extensions"):
					$session->write_value("CURRENT_TAB", "EXTENSIONS", true);
				break;
				
				case("administration"):
					$session->write_value("CURRENT_TAB", "ADMINISTRATION", true);
				break;
			
			endswitch;
			
			unset($_GET[change_tab]);
		}
		else
		{
			unset($_GET[change_tab]);
		}

		if ($_GET[nav] == "projects" and is_numeric($_GET[project_id]))
		{
			// In Projekt Tab und Projekt
			$session->write_value("CURRENT_IN_PROJECT", true, true);
			$session->write_value("CURRENT_IN_SAMPLE", false, true);
			$session->write_value("CURRENT_TAB", "PROJECTS", true);
		}
		elseif($_GET[nav] == "projects" and !$_GET[project_id])
		{
			// In Projekt Tab und außerhalb Project
			$session->write_value("CURRENT_IN_PROJECT", false, true);
			$session->write_value("CURRENT_IN_SAMPLE", false, true);
			$session->write_value("CURRENT_TAB", "PROJECTS", true);
		}
		elseif($_GET[nav] == "samples" and is_numeric($_GET[sample_id]))
		{
			// In Sample Tab und Sample
			$session->write_value("CURRENT_IN_PROJECT", false, true);
			$session->write_value("CURRENT_IN_SAMPLE", true, true);
			$session->write_value("CURRENT_TAB", "SAMPLES", true);
		}
		elseif($_GET[nav] == "samples" and !$_GET[sample_id])
		{
			// In Sample Tab und außerhalb Sample
			if ($_GET[run] != "add_to_project" and 
				$_GET[run] != "new_project_sample" and 
				$_GET[run] != "associate")
			{
				$session->write_value("CURRENT_IN_PROJECT", false, true);
				$session->write_value("CURRENT_IN_SAMPLE", false, true);
				$session->write_value("CURRENT_TAB", "SAMPLES", true);
			}
		}
		
		if ($_GET[nav] == "projects" and $_GET[run] == "organ_unit")
		{
			$session->write_value("CURRENT_TAB", "PROJECTS", true);
		}

		if ($session->is_value("CURRENT_TAB"))
		{
			$this->current_tab 	= $session->read_value("CURRENT_TAB");
			$this->in_project 	= $session->read_value("CURRENT_IN_PROJECT");
			$this->in_sample	= $session->read_value("CURRENT_IN_SAMPLE");
		}
		else
		{
			$session->write_value("CURRENT_TAB", "HOME", true);
			$session->write_value("CURRENT_IN_PROJECT", false, true);
			$session->write_value("CURRENT_IN_SAMPLE", false, true);
			$this->current_tab	= "HOME";
			$this->in_project 	= false;
			$this->in_sample	= false;
		}
	}
	
	public function is_in_project()
	{
		return $this->in_project;	
	}
	
	public function is_in_sample()
	{
		return $this->in_sample;
	}
	
	/**
	 * @todo a lot of
	 */
	public function output()
	{
		global $user;
		
		// Tabs
		
		$template = new Template("languages/en-gb/template/navigation/main_navigation_header.html");
		$template->output();

		// HOME

		$paramquery[username] = $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav] = "home";
		$paramquery[change_tab] = "true";
		$params = http_build_query($paramquery,'','&#38;');

		if ($_GET[nav] == "home" or !$_GET[nav])
		{
			$template = new Template("languages/en-gb/template/navigation/tabs/blue_tab_active.html");
			$template->set_var("params", $params);
			$template->set_var("title", "Home");
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/navigation/tabs/blue_tab.html");
			$template->set_var("params", $params);
			$template->set_var("title", "Home");
			$template->output();
		}
		
		$module_navigation_array = SystemHandler::list_module_navigations_entries();
		
		if (is_array($module_navigation_array) and count($module_navigation_array) >= 1)
		{
			foreach($module_navigation_array as $key => $value)
			{
				$module_name = SystemHandler::get_module_name_by_module_id($value[module_id]);
				
				$paramquery[username] = $_GET[username];
				$paramquery[session_id] = $_GET[session_id];
				$paramquery[nav] = $module_name;
				$params = http_build_query($paramquery,'','&#38;');
				
				switch ($value[colour]):
				
					case "blue":
						if ($_GET[nav] == $module_name)
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/blue_tab_active.html");
							$current_module = $module_name;
							$current_color = $value[colour];
						}
						else
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/blue_tab.html");
						}
					break;
					
					case "green":
						if ($_GET[nav] == $module_name)
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/green_tab_active.html");
							$current_module = $module_name;
							$current_color = $value[colour];
						}
						else
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/green_tab.html");
						}
					break;
					
					case "orange";
						if ($_GET[nav] == $module_name)
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/orange_tab_active.html");
							$current_module = $module_name;
							$current_color = $value[colour];
						}
						else
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/orange_tab.html");
						}
					break;
					
					case "lightgreen":
						if ($_GET[nav] == $module_name)
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/lightgreen_tab_active.html");
							$current_module = $module_name;
							$current_color = $value[colour];
						}
						else
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/lightgreen_tab.html");
						}
					break;
						
					case "lightblue":
						if ($_GET[nav] == $module_name)
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/lightblue_tab_active.html");
							$current_module = $module_name;
							$current_color = $value[colour];
						}
						else
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/lightblue_tab.html");
						}
					break;
				
					default:
						if ($_GET[nav] == $module_name)
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/grey_tab_active.html");
							$current_module = $module_name;
							$current_color = $value[colour];
						}
						else
						{
							$template = new Template("languages/en-gb/template/navigation/tabs/grey_tab.html");
						}
					break;
					
				endswitch;
				
				$template->set_var("params", $params);
				$template->set_var("title", $value[display_name]);
				$template->output();
			}
		}
				
		$info_paramquery[username] = $_GET[username];
		$info_paramquery[session_id] = $_GET[session_id];
		$info_paramquery[nav] = "static";
		$info_paramquery[run] = "info";
		$info_params = http_build_query($info_paramquery,'','&#38;');
		
		$logout_paramquery[username] = $_GET[username];
		$logout_paramquery[session_id] = $_GET[session_id];
		$logout_paramquery[run] = "logout";
		$logout_params = http_build_query($logout_paramquery,'','&#38;');
			
		$template = new Template("languages/en-gb/template/navigation/main_navigation_middle.html");
		$template->set_var("info_params", $info_params);
		$template->set_var("logout_params", $logout_params);
		$template->output();
		
		
		// Submenu
		
		if ($_GET[nav] == "home")
		{
			$template = new Template("languages/en-gb/template/navigation/sub/blue.html");
			
			$sub_menu = array();
			
			$my_profile_paramquery[username] = $_GET[username];
			$my_profile_paramquery[session_id] = $_GET[session_id];
			$my_profile_paramquery[nav] = "user";
			$my_profile_params = http_build_query($my_profile_paramquery,'','&#38;');
			
			$sub_menu[0][params] = $my_profile_params;
			$sub_menu[0][title] = "My Profile";
			
			$my_organisation_units_paramquery[username] = $_GET[username];
			$my_organisation_units_paramquery[session_id] = $_GET[session_id];
			$my_organisation_units_paramquery[nav] = "static";
			$my_organisation_units_paramquery[run] = "myorgan";
			$my_organisation_units_params = http_build_query($my_organisation_units_paramquery,'','&#38;');
			
			$sub_menu[1][params] = $my_organisation_units_params;
			$sub_menu[1][title] = "My Organisation Units";
			
			$system_messages_paramquery[username] = $_GET[username];
			$system_messages_paramquery[session_id] = $_GET[session_id];
			$system_messages_paramquery[nav] = "static";
			$system_messages_paramquery[run] = "sysmsg";
			$system_messages_params = http_build_query($system_messages_paramquery,'','&#38;');
			
			$sub_menu[2][params] = $system_messages_params;
			$sub_menu[2][title] = "System Messages";
			
			$template->set_var("sub_menu", $sub_menu);
			$template->set_var("search_bar", false);
			
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/navigation/sub/".$current_color.".html");
			
			$template->set_var("sub_menu", false);
			$template->set_var("search_bar", false);
			
			$template->output();
		}
		
		
		/*
		switch(strtoupper($_GET[nav])):
		
			case "HOME":
				
				$my_profile_paramquery[username] = $_GET[username];
				$my_profile_paramquery[session_id] = $_GET[session_id];
				$my_profile_paramquery[nav] = "user";
				$my_profile_params = http_build_query($my_profile_paramquery,'','&#38;');
				
				$my_organisation_units_paramquery[username] = $_GET[username];
				$my_organisation_units_paramquery[session_id] = $_GET[session_id];
				$my_organisation_units_paramquery[nav] = "static";
				$my_organisation_units_paramquery[run] = "myorgan";
				$my_organisation_units_params = http_build_query($my_organisation_units_paramquery,'','&#38;');
				
				$system_messages_paramquery[username] = $_GET[username];
				$system_messages_paramquery[session_id] = $_GET[session_id];
				$system_messages_paramquery[nav] = "static";
				$system_messages_paramquery[run] = "sysmsg";
				$system_messages_params = http_build_query($system_messages_paramquery,'','&#38;');
				
				$template = new Template("languages/en-gb/template/navigation/sub/home.html");
				$template->set_var("my_profile_params", $my_profile_params);
				$template->set_var("my_organ_units_params", $my_organisation_units_params);
				$template->set_var("system_messages_params", $system_messages_params);
				$template->output();
				
			break;
			
			case "PROJECTS":
			
				$new_project_paramquery[username] = $_GET[username];
				$new_project_paramquery[session_id] = $_GET[session_id];
				$new_project_paramquery[nav] = "projects";
				$new_project_paramquery[run] = "new";
				$new_project_paramquery[change_tab] = "true";
				$new_project_params = http_build_query($new_project_paramquery,'','&#38;');
			
				$my_projects_paramquery[username] = $_GET[username];
				$my_projects_paramquery[session_id] = $_GET[session_id];
				$my_projects_paramquery[nav] = "projects";
				$my_projects_paramquery[change_tab] = "true";
				$my_projects_params = http_build_query($my_projects_paramquery,'','&#38;');
			
				$mini_search_paramquery[username] = $_GET[username];
				$mini_search_paramquery[session_id] = $_GET[session_id];
				$mini_search_paramquery[nav] = "search";
				$mini_search_paramquery[run] = "project";
				$mini_search_paramquery[change_tab] = "true";
				$mini_search_paramquery[nextpage] = "1";
				$mini_search_params = http_build_query($mini_search_paramquery,'','&#38;');
			
				$template = new Template("languages/en-gb/template/navigation/sub/projects.html");
				$template->set_var("new_project_params", $new_project_params);
				$template->set_var("my_projects_params", $my_projects_params);
				$template->set_var("mini_search_params", $mini_search_params);
				$template->output();
				
			break;
			
			case "SAMPLES":
				
				$new_sample_paramquery[username] = $_GET[username];
				$new_sample_paramquery[session_id] = $_GET[session_id];
				$new_sample_paramquery[nav] = "samples";
				$new_sample_paramquery[run] = "new";
				$new_sample_paramquery[change_tab] = "true";
				$new_sample_params = http_build_query($new_sample_paramquery,'','&#38;');
			
				$my_samples_paramquery[username] = $_GET[username];
				$my_samples_paramquery[session_id] = $_GET[session_id];
				$my_samples_paramquery[nav] = "samples";
				$my_samples_paramquery[change_tab] = "true";
				$my_samples_params = http_build_query($my_samples_paramquery,'','&#38;');
				
				$mini_search_paramquery[username] = $_GET[username];
				$mini_search_paramquery[session_id] = $_GET[session_id];
				$mini_search_paramquery[nav] = "search";
				$mini_search_paramquery[run] = "sample";
				$mini_search_paramquery[change_tab] = "true";
				$mini_search_paramquery[nextpage] = "1";
				$mini_search_params = http_build_query($mini_search_paramquery,'','&#38;');
				
				$template = new Template("languages/en-gb/template/navigation/sub/samples.html");
				$template->set_var("new_sample_params", $new_sample_params);
				$template->set_var("my_samples_params", $my_samples_params);
				$template->set_var("mini_search_params", $mini_search_params);
				$template->output();
				
			break;
			
			case "DATA":
				$mini_search_paramquery[username] = $_GET[username];
				$mini_search_paramquery[session_id] = $_GET[session_id];
				$mini_search_paramquery[nav] = "search";
				$mini_search_paramquery[run] = "ffv";
				$mini_search_paramquery[change_tab] = "true";
				$mini_search_paramquery[nextpage] = "1";
				$mini_search_params = http_build_query($mini_search_paramquery,'','&#38;');
				
				if ($_GET[folder_id]) {
					$folder_id = $_GET[folder_id];
				}else{
					$folder_id = 1;
				}
				
				$template = new Template("languages/en-gb/template/navigation/sub/data.html");
				$template->set_var("mini_search_params", $mini_search_params);
				$template->set_var("mini_search_folder_id", $folder_id);
				$template->output();
			break;
			
			case "SEARCH":
				
				$project_search_paramquery[username] = $_GET[username];
				$project_search_paramquery[session_id] = $_GET[session_id];
				$project_search_paramquery[nav] = "search";
				$project_search_paramquery[run] = "project";
				$project_search_paramquery[change_tab] = "true";
				$project_search_params = http_build_query($project_search_paramquery,'','&#38;');
				
				$sample_search_paramquery[username] = $_GET[username];
				$sample_search_paramquery[session_id] = $_GET[session_id];
				$sample_search_paramquery[nav] = "search";
				$sample_search_paramquery[run] = "sample";
				$sample_search_paramquery[change_tab] = "true";
				$sample_search_params = http_build_query($sample_search_paramquery,'','&#38;');
				
				$ffv_search_paramquery[username] = $_GET[username];
				$ffv_search_paramquery[session_id] = $_GET[session_id];
				$ffv_search_paramquery[nav] = "search";
				$ffv_search_paramquery[run] = "ffv";
				$ffv_search_paramquery[change_tab] = "true";
				$ffv_search_params = http_build_query($ffv_search_paramquery,'','&#38;');
				
				$data_search_paramquery[username] = $_GET[username];
				$data_search_paramquery[session_id] = $_GET[session_id];
				$data_search_paramquery[nav] = "search";
				$data_search_paramquery[run] = "data";
				$data_search_paramquery[change_tab] = "true";
				$data_search_params = http_build_query($data_search_paramquery,'','&#38;');
				
				$user_search_paramquery[username] = $_GET[username];
				$user_search_paramquery[session_id] = $_GET[session_id];
				$user_search_paramquery[nav] = "search";
				$user_search_paramquery[run] = "user";
				$user_search_paramquery[change_tab] = "true";
				$user_search_params = http_build_query($user_search_paramquery,'','&#38;');
				
				$full_text_search_paramquery[username] = $_GET[username];
				$full_text_search_paramquery[session_id] = $_GET[session_id];
				$full_text_search_paramquery[nav] = "search";
				$full_text_search_paramquery[run] = "full_text";
				$full_text_search_paramquery[change_tab] = "true";
				$full_text_search_params = http_build_query($full_text_search_paramquery,'','&#38;');
				
				$template = new Template("languages/en-gb/template/navigation/sub/search.html");
				$template->set_var("project_search_params", $project_search_params);
				$template->set_var("sample_search_params", $sample_search_params);
				$template->set_var("ffv_search_params", $ffv_search_params);
				$template->set_var("data_search_params", $data_search_params);
				$template->set_var("user_search_params", $user_search_params);
				$template->set_var("full_text_search_params", $full_text_search_params);
				$template->output();
				
			break;
			
			case "ORGANISER":
				$template = new Template("languages/en-gb/template/navigation/sub/organiser.html");
				$template->output();
			break;
			
			case "EXTENSIONS":
				$template = new Template("languages/en-gb/template/navigation/sub/extensions.html");
				$template->output();
			break;
			
			case "ADMINISTRATION":
				$template = new Template("languages/en-gb/template/navigation/sub/administration.html");
				$template->output();
			break;
			
			default:
				
			break;
		
		
		endswitch; */
		
		$template = new Template("languages/en-gb/template/navigation/main_navigation_footer.html");
		$template->output();
	}
	
}
?>
