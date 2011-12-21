<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
<<<<<<< HEAD
 * @copyright (c) 2008-2011 by Roman Konertz
=======
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz, Roman Quiring
>>>>>>> uploader
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
 * 
 */
$GLOBALS['autoload_prefix'] = "../";
require_once("../../base/ajax.php");

/**
 * File AJAX IO Class
 * @package data
 */
class FileAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	public static function list_file_items($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$handling_class = Item::get_holder_handling_class_by_name($argument_array[0][1]);
		if ($handling_class)
		{
			$sql = $handling_class.get_item_list_sql($argument_array[1][1]);
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
	
	private function get_data_browser_link_html_and_button_handler($action, $file_id) //id really necessary?
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
				$paramquery[file_id] = $file_id;
				$params = http_build_query($paramquery, '', '&#38;');
				$template = new HTMLTemplate("data/file_update_window.html");
				$template->set_var("params", $params);
				$template->set_var("unique_id", $unique_id);
				$template->set_var("session_id", $_GET[session_id]);
				$button_handler = "uploader.start_upload();";
				$button_handler_caption = "Upload";
				$html_caption = "Upload newer version";
				$html = $template->get_string();
				break;
			case "file_update_minor":
				$unique_id = uniqid();
				$paramquery[unique_id] = $unique_id;
				$paramquery[file_id] = $file_id;
				$params = http_build_query($paramquery, '', '&#38;');
				$template = new HTMLTemplate("data/file_update_window.html");
				$template->set_var("params", $params);
				$template->set_var("unique_id", $unique_id);
				$template->set_var("session_id", $_GET[session_id]);
				$button_handler = "uploader.start_upload();";
				$button_handler_caption = "Upload";
				$html_caption = "Upload minor version";
				$html = $template->get_string();
				break;	
			case "permission":
				require_once("data.ajax.php");
				
				if(isset($_GET[permissions]))
				{
					$success = DataAjax::change_permission(json_decode($_GET[permissions]), "File");
					return $success;
				}
				else
				{
					$permission = DataAjax::permission_window();
					$button_handler = "
						var json = '{';
						$('#DataBrowserLoadedAjaxContent').find('input').each(function(){
							if($(this).attr('type') != 'hidden') 
							{
								if($(this).is(':checkbox:checked'))
								{
									json += '\"'+$(this).attr('name')+'\":\"'+$(this).attr('value')+'\",';
								}
								else
								{
									json += '\"'+$(this).attr('name')+'\":\"0\",';
								}
							}
						});
						json = json.substr(0,json.length-1); //cut last ,
						json += '}';
						$.ajax({
							type : \"GET\",
							url : \"../../../../core/modules/data/ajax/file.ajax.php\",
							data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&file_id=".$_GET['file_id']."&nav=data&run=get_data_browser_link_html_and_button_handler&action=permission&permissions=\"+json,
							success : function(data) {
								close_ui_window_and_reload();
							}
						});
					";
					$button_handler_caption = "Change";
					$html_caption = "Change permission";
					$html = $permission;	
				}
				break;
			case "file_delete":
				$paramquery[sure] = "true";
				$params = http_build_query($paramquery);
				$template = new HTMLTemplate("data/file_delete_window.html");
				$template->set_var("params", $params);
				$button_handler = "
					$.ajax({
						type : \"GET\",
						url : \"../../../../core/modules/data/ajax/file.ajax.php\",
						data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&file_id=".$_GET['file_id']."&run=delete_file\",
						success : function(data) {
							close_ui_window_and_reload();
						}
					});
				";
				$button_handler_caption = "Delete";
				$html_caption = "Delete File";
				$html = $template->get_string();
				break;
		endswitch;
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption);
		return json_encode($array);
	}
	
	private function add_file($folder_id)
	{
		$paramquery = $_GET;
		$unique_id = uniqid();
		$paramquery[unique_id] = $unique_id;
		$params = http_build_query($paramquery);
		$template = new HTMLTemplate("data/file_upload_window.html");
		$template->set_var("params", $params);
		$template->set_var("unique_id", $unique_id);
		$template->set_var("session_id", $_GET[session_id]);
		$button_handler = "
			uploader.start_upload();
			fadeout_ui_window();
			function check_if_uploader_finished()
			{
				if(uploader.is_finished() == true)
				{
					close_ui_window_and_reload();
				}
				else
				{
					setTimeout(check_if_uploader_finished , 200);
				}
			}
			check_if_uploader_finished();
			";
		$button_handler_caption = "Add";
		$html_caption = "Add File";
		$html = $template->get_string();
		$additional_script = "uploader = new base_upload(\"".$unique_id."\",\"".$_GET[session_id]."\");";
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption, "additional_script"=>$additional_script);
		return json_encode($array);
	}
	
	private function delete_file($file_id) {
		$file = File::get_instance($file_id);
		$file->delete();
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
	
				case "list_file_items":
					echo $this->list_file_items($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "get_data_browser_link_html_and_button_handler":
					echo $this->get_data_browser_link_html_and_button_handler($_GET[action], $_GET[file_id]);
				break;
				
				case "add_file":
					echo $this->add_file($_GET[folder_id]);
				break;
				
				case "delete_file":
					echo $this->delete_file($_GET[file_id]);
				break;
				
				default:
				break;

			endswitch;
		}
	}
}

$file_ajax = new FileAjax;
$file_ajax->method_handler();

?>