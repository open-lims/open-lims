<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz, Roman Quiring
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
 * Folder AJAX Class
 * @package data
 */
class FolderAjax
{
	/**
	 * @return string
	 */
	public static function get_array()
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
		
		return json_encode($return_array);
	}
	
	/**
	 * @return string
	 */
	public static function get_children($id)
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
			return json_encode($return_array);
		}
	}
	
	/**
	 * @param string $action
	 * @return string
	 */
	public static function get_data_browser_link_html_and_button_handler($action) 
	{
		$html;
		$html_caption;
		$button_handler;
		$button_handler_caption;
		$template;
		$paramquery = $_GET;	
		unset($paramquery['run']);
		switch($action):
			case "folder_add":
				if(isset($_POST['folder_name'])) //second call
				{
					return self::add_folder($_POST['folder_id'], $_POST['folder_name']);
				}
				else //first call
				{
					$button_handler_template = new JSTemplate("data/js/folder_add_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("folder_id", $_POST['folder_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Add";
					$html_caption = "Add Folder";
					$template = new HTMLTemplate("data/folder_add_window.html");
					$html = $template->get_string();
				}
			break;
			case "folder_delete":
				if(isset($_POST['sure'])) //second call
				{
					return self::delete_folder($_POST['folder_id']);
				}
				else //first call
				{
					$paramquery['sure'] = "true";
					$paramquery['nextpage'] = "1";
					$params = http_build_query($paramquery);
					$template = new HTMLTemplate("data/folder_delete_window.html");
					$template->set_var("params", $params);
					$button_handler_template = new JSTemplate("data/js/folder_delete_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("folder_id", $_POST['folder_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Delete";
					$html_caption = "Delete Folder";
					$html = $template->get_string();
				}
			break;
			case "folder_rename":
				if(isset($_POST['folder_name'])) //second call
				{
					return self::rename_folder($_POST['folder_id'], $_POST['folder_name']);
				}
				else //first call
				{
					$template = new HTMLTemplate("data/folder_rename_window.html");
					$button_handler_template = new JSTemplate("data/js/folder_rename_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("folder_id", $_POST['folder_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Rename";
					$html_caption = "Rename Folder";
					$html = $template->get_string();
				}

			break;
			case "permission":
				require_once("data.ajax.php");
				if(isset($_POST['permissions'])) //second call
				{
					$success = DataAjax::change_permission(json_decode($_POST['permissions']), "Folder");
					return $success;
				}
				else //first call
				{
					$permission = DataAjax::permission_window();
					$button_handler_template = new JSTemplate("data/js/folder_permission_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("folder_id", $_POST['folder_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Change";
					$html_caption = "Change permission";
					$html = $permission;
				}
			break;
		endswitch;
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption);
		return json_encode($array);
	}
	
	/**
	 * @param integer $folder_id
	 * @param string $folder_name
	 * @return string
	 * @throws DataSecurityAccessDeniedException
	 */
	private static function add_folder($folder_id, $folder_name)
	{
		global $session;
	
		$internal_name = trim(strtolower(str_replace(" ","_",$folder_name)));
  		$base_folder = Folder::get_instance($folder_id);
  		
  		if ($base_folder->can_add_folder())
		{
			$path = new Path($base_folder->get_path());
			$path->add_element($internal_name);
			$folder = Folder::get_instance(null);
			if (($folder_id = $folder->create($folder_name, $folder_id, $path->get_path_string(), $session->get_user_id(), null)) == null)
			{
			 	return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			throw new DataSecurityAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $folder_id
	 * @param string $folder_name
	 * @return string
	 * @throws DataSecurityAccessDeniedException
	 */
	private static function rename_folder($folder_id, $folder_name)
	{
		$folder = Folder::get_instance($folder_id);
		if ($folder->can_rename_folder())
		{
			$folder->set_name($folder_name);
			return "1";
		}
		else
		{
			throw new DataSecurityAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $folder_id
	 * @return string
	 * @throws DataSecurityAccessDeniedException
	 */
	private static function delete_folder($folder_id) {
		$folder = Folder::get_instance($folder_id);
		if ($folder->can_command_folder())
		{
			$folder->delete(true, true);
			return "1";
		}
		else
		{
			throw new DataSecurityAccessDeniedException();
		}
	}

}
?>
