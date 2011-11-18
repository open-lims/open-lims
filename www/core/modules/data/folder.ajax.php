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
 * 
 */
require_once("../base/ajax.php");

/**
 * Folder AJAX Class
 * @package data
 */
class FolderAjax extends Ajax
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_array()
	{
		global $session;

		if ($session->is_valid())
		{
			if ($session->is_value("FILE_SEARCH_ARRAY"))
			{
				echo json_encode($session->read_value("FILE_SEARCH_ARRAY"));
			}
			else
			{
				$return_array = array();
										
				$folder = Folder::get_instance(1);
			
				$data_array = $folder->get_subfolder_array();
				
				if (is_array($data_array) and count($data_array) >= 1)
				{
					$counter = 0;
					
					foreach($data_array as $key => $value)
					{
						$folder = Folder::get_instance($value);
					
						$return_array[$counter][0] = -1;
						$return_array[$counter][1] = $value;
						$return_array[$counter][2] = $folder->get_name();
						$return_array[$counter][3] = "folder.png";
						
						if ($folder->is_read_access() == true)
						{
							$return_array[$counter][4] = true;
						}
						else
						{
							$return_array[$counter][4] = false;	
						}
						
						$return_array[$counter][5] = true; // Clickable
						
						$paramquery['username'] = $_GET['username'];
						$paramquery['session_id'] = $_GET['session_id'];
						$paramquery['nav'] = "data";
						$paramquery['folder_id'] = $value;
						$params = http_build_query($paramquery, '', '&#38;');
						
						$return_array[$counter][6] = $params; //link
						$return_array[$counter][7] = false; //open
						
						$counter++;
					}
				}
				
				echo json_encode($return_array);
			}
		}
	}
	
	public function get_children($id)
	{
		if (is_numeric($id) and $id != 0)
		{
			$return_array = array();

			$folder = Folder::get_instance($id);
					
			$folder_array = $folder->get_subfolder_array();
			
			if (is_array($folder_array) and count($folder_array) >= 1)
			{
				$counter = 0;
				
				foreach($folder_array as $key => $value)
				{
		
					$folder = Folder::get_instance($value);
					
					$return_array[$counter][0] = -1;
					$return_array[$counter][1] = $value;
					$return_array[$counter][2] = $folder->get_name();
					$return_array[$counter][3] = "folder.png";
					
					if ($folder->is_read_access() == true)
					{
						$return_array[$counter][4] = true;
					}
					else
					{
						$return_array[$counter][4] = false;	
					}
					
					$return_array[$counter][5] = true; // Clickable
					
					$paramquery['username'] = $_GET['username'];
					$paramquery['session_id'] = $_GET['session_id'];
					$paramquery['nav'] = "data";
					$paramquery['folder_id'] = $value;
					$params = http_build_query($paramquery, '', '&#38;');
					
					$return_array[$counter][6] = $params; //link
					$return_array[$counter][7] = false; //open
					
					$counter++;
					
				}
			}
			echo json_encode($return_array);
		}
	}
	
	private function get_data_browser_link_html_and_button_handler($action) 
	{
		$html;
		$html_caption;
		$button_handler;
		$button_handler_caption;
		$template;
		$paramquery = $_GET;	
		unset($paramquery[run]);
		switch($action):
			case "folder_delete":
				$paramquery[sure] = "true";
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery);
				$template = new Template("../../../template/data/folder_delete_window.html");
				$template->set_var("params", $params);
				$button_handler = "
					$.ajax({
						type : \"GET\",
						url : \"../../../core/modules/data/folder.ajax.php\",
						data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&folder_id=".$_GET['folder_id']."&run=delete_folder\",
						success : function(data) {
							close_ui_window_and_reload();
						}
					});
				";
				$button_handler_caption = "Delete";
				$html_caption = "Delete Folder";
				$html = $template->get_string();
			break;
			case "folder_rename":
				$template = new Template("../../../template/data/folder_rename_window.html");
				$button_handler = "";
				$button_handler_caption = "Rename";
				$html_caption = "Rename Folder";
				$html = $template->get_string();
			break;
			case "permission":
				require_once("data.io.php");
				if(isset($_GET[permissions])) //second call
				{
					$success = DataIO::change_permission(json_decode($_GET[permissions]), "Folder");
					return $success;
				}
				else //first call
				{
					$permission = DataIO::permission_window();
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
							url : \"../../../core/modules/data/folder.ajax.php\",
							data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&folder_id=".$_GET['folder_id']."&nav=data&run=get_data_browser_link_html_and_button_handler&action=permission&permissions=\"+json,
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
		endswitch;
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption);
		return json_encode($array);
	}
	
	private function add_folder($folder_id, $folder_name)
	{
		global $session;
		if($folder_name == null) //first call
		{
			$button_handler = "
				var new_name = $('#NewFolderName').val();
				$.ajax({
					type : \"GET\",
					url : \"../../../core/modules/data/folder.ajax.php\",
					data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&folder_id=".$_GET['folder_id']."&run=add_folder&folder_name=\"+new_name,
					success : function(data) {
						close_ui_window_and_reload();
					}
				});
			";
			$button_handler_caption = "Add";
			$html_caption = "Add Folder";
			$template = new Template("../../../template/data/folder_add_window.html");
			$html = $template->get_string();
			$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption);
			return json_encode($array);
		}
		else //second call
		{
			$internal_name = trim(strtolower(str_replace(" ","_",$folder_name)));
	  		$base_folder = Folder::get_instance($folder_id);
			$path = new Path($base_folder->get_path());
			$path->add_element($internal_name);
			$folder = Folder::get_instance(null);
			if (($folder_id = $folder->create($folder_name, $folder_id, $path->get_path_string(), $session->get_user_id(), null)) == null)
			{
			 	return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	private function delete_folder($folder_id) {
		$folder = Folder::get_instance($folder_id);
		$folder->delete(true, true); //?
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):	

				case "get_array":
					$this->get_array();
					break;
				case "get_children":
					$this->get_children($_GET['id']);
					break;	
				case "get_data_browser_link_html_and_button_handler":
					echo $this->get_data_browser_link_html_and_button_handler($_GET[action]);
					break;
				case "add_folder":
					echo $this->add_folder($_GET[folder_id],$_GET[folder_name]);
					break;
				case "delete_folder":
					echo $this->delete_folder($_GET[folder_id]);
					break;
			endswitch;
		}
	}
}

$folder_ajax = new FolderAjax;
$folder_ajax->method_handler();

?>
