<?php
/**
 * @package data
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
 * Data Search IO Class
 * @package data
 */
class DataSearchIO
{
	public static function get_description($language_id)
	{
		return "Finds Files, Values or Folders in Folders via Name or Extension.";
	}
	
	public static function get_icon()
	{
		return "images/icons_large/ffv_search_50.png";
	}
	
	public static function search()
	{
		global $user, $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				if ($_GET[nextpage] == "2" and $_POST[name])
				{
					$name = $_POST[name];
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
				else
				{
					$name = $session->read_value("SEARCH_FFV_NAME");
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
			}
			else
			{
				if ($_GET[page])
				{
					$name = $session->read_value("SEARCH_FFV_NAME");
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
				else
				{
					if ($_GET[nextpage] == "1")
					{
						$name = $_POST[name];
						if ($_GET[folder_id])
						{
							$folder_id = $_GET[folder_id];
						}
						else
						{
							$folder_id = UserFolder::get_folder_by_user_id($user->get_user_id());
						}
						$session->delete_value("SEARCH_FFV_NAME");
						$session->delete_value("SEARCH_FFV_FOLDER_ID");
					}
					else
					{
						$name = $_POST[name];
						$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
					}
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
			$template = new Template("template/data/search/ffv_search.html");
			
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
			if (!$folder_id)
			{
				$folder_id = $_POST[folder_id];
			}

			$session->write_value("SEARCH_FFV_NAME", $name, true);
			$session->write_value("SEARCH_FFV_FOLDER_ID", $folder_id, true);
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Data_Wrapper::list_search_ffv($folder_id, $name, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = Data_Wrapper::list_search_ffv($folder_id, $name, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Data_Wrapper::list_search_ffv($folder_id, $name, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = Data_Wrapper::list_search_ffv($folder_id, $name, null, null, 0, 20);
				}	
			}
			
			$list = new List_IO(Data_Wrapper::count_search_ffv($folder_id, $name), 20);
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
					$owner = new User($value[owner]);
					$result_array[$key][owner] = $owner->get_full_name(true);
					
					if (is_numeric($value[file_id]))
					{
						$file = new File($value[file_id]);
						
						$paramquery = $_GET;
						$paramquery[nav] = "file";
						$paramquery[run] = "detail";
						$paramquery[file_id] = $value[file_id];
						unset($paramquery[sortvalue]);
						unset($paramquery[sortmethod]);
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery, '', '&#38;');
						
						$tmp_name = $value[name];
						if (strlen($tmp_name) > 20)
						{
							$tmp_name = substr($tmp_name,0 ,20)."...";
						}
						unset($result_array[$key][name]);
						$result_array[$key][name][content] = $tmp_name;
						
						if ($file->is_read_access() == true)
						{
							$result_array[$key][symbol][link] = $params;
							$result_array[$key][symbol][content] = "<img src='".File::get_icon_by_name($value[name])."' alt='' style='border:0;' />";
							$result_array[$key][name][link] = $params;
						}
						else
						{
							$result_array[$key][symbol][link] = "";
							$result_array[$key][symbol][content] = "<img src='core/images/denied_overlay.php?image=".File::get_icon_by_name($value[name])."' alt='' border='0' />";
							$result_array[$key][name][link] = "";
						}
						
						$result_array[$key][type] = "File";
						
						$result_array[$key][version] = $file->get_version();
						$result_array[$key][size] = Misc::calc_size($file->get_size());
						$result_array[$key][permission] = $file->get_permission_string();
					}
					
					if (is_numeric($value[value_id]))
					{
						$value_obj = new Value($value[value_id]);
						
						$paramquery = $_GET;
						$paramquery[nav] = "value";
						$paramquery[run] = "detail";
						$paramquery[value_id] = $value[value_id];
						unset($paramquery[sortvalue]);
						unset($paramquery[sortmethod]);
						unset($paramquery[nextpage]);
						$item_params = http_build_query($paramquery, '', '&#38;');
						
						$tmp_name = $value[name];
						if (strlen($tmp_name) > 20)
						{
							$tmp_name = substr($tmp_name,0 ,20)."...";
						}
						unset($result_array[$key][name]);
						$result_array[$key][name][content] = $tmp_name;
						
						if ($value_obj->is_read_access() == true)
						{
							$result_array[$key][symbol][link] = $params;
							$result_array[$key][symbol][content] = "<img src='images/fileicons/16/unknown.png' alt='' style='border: 0;'>";
							$result_array[$key][name][link] = $params;
						}
						else
						{
							$result_array[$key][symbol][link] = "";
							$result_array[$key][symbol][content] = "<img src='core/images/denied_overlay.php?image=images/fileicons/16/unknown.png' alt='' border='0' />";
							$result_array[$key][name][link] = "";
						}
						
						$result_array[$key][type] = "Value";
						
						$result_array[$key][version] = $value_obj->get_version();
						$result_array[$key][permission] = $value_obj->get_permission_string();
					}
				}	
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
	
			$list->add_row("", "symbol", false, "16px");
			$list->add_row("Name", "name", true, null);
			$list->add_row("Type", "type", false, null);
			$list->add_row("Version", "version", false, null);
			$list->add_row("Datetime", "datetime", true, null);
			$list->add_row("Size", "size", true, null);
			$list->add_row("Owner", "owner", true, null);
			$list->add_row("Permission", "permission", false, null);
			
			$folder = Folder::get_instance($folder_id);
			
			$template = new Template("template/data/search/ffv_search_result.html");
		
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("name", $name);
			$template->set_var("folder", $folder->get_name());
				
			$template->set_var("table", $list->get_list($result_array, $_GET[page]));		
	
			$template->output();
		}
	}
}
?>