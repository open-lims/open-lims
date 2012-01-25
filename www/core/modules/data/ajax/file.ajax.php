<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz, Roman Quiring
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
 * File AJAX IO Class
 * @package data
 */
class FileAjax 
{
	public static function list_file_items($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$handling_class = Item::get_holder_handling_class_by_name($argument_array[0][1]);
		if ($handling_class)
		{
			$sql = $handling_class::get_item_list_sql($argument_array[1][1]);
		}
		
		if ($sql)
		{
			$list_request = new ListRequest_IO();
			
			if ($argument_array[2][1] == true)
			{
				$list_array = Data_Wrapper::list_item_files($sql, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
			}
			else
			{
				$list_array = Data_Wrapper::list_item_files($sql, $sortvalue, $sortmethod, 0, null);
			}
			$list_request->set_column_array($json_column_array);
					
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					if ($argument_array[3][1] == true)
					{
						$column_array = json_decode($json_column_array);
						if (is_array($column_array) and count($column_array) >= 1)
						{
							foreach ($column_array as $row_key => $row_value)
							{
								if ($row_value[1] == "checkbox")
								{
									if ($row_value[4])
									{
										$checkbox_class = $row_value[4];
										break;
									}
								}
							}
						}
						
						if ($checkbox_class)
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='file-".$list_array[$key][item_id]."' value='1' class='".$checkbox_class."' />";
						}
						else
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='file-".$list_array[$key][item_id]."' value='1' />";
						}
					} 
					
					$file = File::get_instance($list_array[$key][id]);
					$list_array[$key][symbol] = "<img src='".$file->get_icon()."' alt='' style='border:0;' />";
					
					$list_array[$key][size] = Convert::convert_byte_1024($list_array[$key][size]);
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No Files found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
			
		}
		
	}
	
	public static function count_file_items($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$handling_class = Item::get_holder_handling_class_by_name($argument_array[0][1]);
		if ($handling_class)
		{
			$sql = $handling_class.get_item_list_sql($argument_array[1][1]);
		}
		
		if ($sql)
		{
			return Data_Wrapper::count_item_files($sql);
		}
		else
		{
			return null;
		}
	}
	
	public static function get_data_browser_link_html_and_button_handler($action)
	{
		$html;
		$html_caption;
		$button_handler;
		$button_handler_caption;
		$template;
		$paramquery = $_GET;	
		unset($paramquery[run]);
		switch($action):
			case "file_update":
				$unique_id = uniqid();
				$paramquery[unique_id] = $unique_id;
				$paramquery[file_id] = $_POST['file_id'];
				$params = http_build_query($paramquery, '', '&#38;');
				$template = new HTMLTemplate("data/file_update_window.html");
				$template->set_var("params", $params);
				$template->set_var("unique_id", $unique_id);
				$template->set_var("session_id", $_GET[session_id]);
				$button_handler_template = new JSTemplate("data/js/file_update_window.js");
				$button_handler = $button_handler_template->get_string();
				$button_handler_caption = "Upload";
				$html_caption = "Upload newer version";
				$html = $template->get_string();
				break;
			case "file_update_minor":
				$unique_id = uniqid();
				$paramquery[unique_id] = $unique_id;
				$paramquery[file_id] = $_POST['file_id'];
				$params = http_build_query($paramquery, '', '&#38;');
				$template = new HTMLTemplate("data/file_update_window.html");
				$template->set_var("params", $params);
				$template->set_var("unique_id", $unique_id);
				$template->set_var("session_id", $_GET[session_id]);
				$button_handler_template = new JSTemplate("data/js/file_update_window.js");
				$button_handler = $button_handler_template->get_string();
				$button_handler_caption = "Upload";
				$html_caption = "Upload minor version";
				$html = $template->get_string();
				break;	
			case "permission":
				require_once("data.ajax.php");
				
				if(isset($_POST[permissions])) //second call
				{
					$success = DataAjax::change_permission(json_decode($_POST[permissions]), "File");
					return $success;
				}
				else //first call
				{
					$permission = DataAjax::permission_window();
					$button_handler_template = new JSTemplate("data/js/file_permission_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("file_id", $_GET['file_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Change";
					$html_caption = "Change permission";
					$html = $permission;	
				}
				break;
			case "file_delete":
				if(isset($_POST['sure']))
				{					
					self::delete_file($_POST['file_id']);
				}
				else
				{
					$template = new HTMLTemplate("data/file_delete_window.html");
					$button_handler_template = new JSTemplate("data/js/file_delete_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("file_id", $_GET['file_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Delete";
					$html_caption = "Delete File";
					$html = $template->get_string();
				}
				break;
		endswitch;
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption);
		return json_encode($array);
	}
	
	public static function add_file($folder_id)
	{
		$paramquery = $_GET;
		$unique_id = uniqid();
		$paramquery[unique_id] = $unique_id;
		$params = http_build_query($paramquery);
		$template = new HTMLTemplate("data/file_upload_window.html");
		$template->set_var("params", $params);
		$template->set_var("unique_id", $unique_id);
		$template->set_var("session_id", $_GET[session_id]);
		$button_handler_template = new JSTemplate("data/js/file_upload_window.js");
		$button_handler = $button_handler_template->get_string();
		$button_handler_caption = "Add";
		$html_caption = "Add File";
		$html = $template->get_string();
		$additional_script_template = new JSTemplate("data/js/file_upload_window_additional.js");
		$additional_script_template->set_var("session_id", $_GET['session_id']);
		$additional_script_template->set_var("unique_id", $unique_id);
		$additional_script = $additional_script_template->get_string();
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption, "additional_script"=>$additional_script);
		return json_encode($array);
	}
	
	private static function delete_file($file_id) 
	{
		$file = File::get_instance($file_id);
		$file->delete();
	}
}

?>