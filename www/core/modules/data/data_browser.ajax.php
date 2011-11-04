<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Quiring
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

require_once("../base/ajax.php");

/**
 * Data Browser AJAX IO Class
 * @package data
 */

class DataBrowserAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	private function get_data_browser_path($folder_id, $virtual_folder_id)
	{
		if($folder_id == "null")
			$folder_id = null;
		if($virtual_folder_id == "null")
			$virtual_folder_id = null;
		$data_path = new DataPath(null, null);
		$data_path->__construct($folder_id, $virtual_folder_id);		
		return $data_path->get_stack_path();
	}
	
	private function get_data_browser_path_cleared($folder_id, $virtual_folder_id)
	{
		if($folder_id == "null")
			$folder_id = null;
		if($virtual_folder_id == "null")
			$virtual_folder_id = null;
		$data_path = new DataPath(null, null);
		$data_path->delete_stack();
		$data_path->__construct($folder_id, $virtual_folder_id);		
		return $data_path->get_stack_path();
	}
	
	private function get_context_sensitive_file_menu($file_id)
	{
		global $user;
		$file = File::get_instance($file_id);
		$html = "";
		if ($file->is_read_access())
		{
			$download_link = "download.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&file_id=".$file_id;
			$html .= "<img src='images/icons/download.png' alt='' /><a href='".$download_link."' class='DataBrowserDialogLinkFollowDirectly'>Download</a><br/>";
			$history_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&file_id=".$file_id."&action=file_history";
			$html .= "<a href='".$history_link."' class='DataBrowserDialogLinkFollowDirectly'>History</a><br/>";
		}
		if ($file->is_write_access())
		{
			$version_num = $file->get_internal_revision();
			$update_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&file_id=".$file_id."&action=file_update&version=".$version_num;
			$html .= "<img src='images/icons/upload.png' alt='' /><a href='".$update_link."' class='DataBrowserDialogLinkUploadNewer'>Upload newer version</a><br/>";
			$update_minor_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&file_id=".$file_id."&action=file_update_minor&version=".$version_num;
			$html .= "<img src='images/icons/upload.png' alt='' /><a href='".$update_minor_link."' class='DataBrowserDialogLinkUploadMinor'>Upload minor version</a><br/>";
		}
		if ($file->is_control_access() == true or $file->get_owner_id() == $user->get_user_id())
		{
			$change_permission_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&file_id=".$file_id."&action=permission";
			$html .= "<img src='images/icons/permissions.png' alt='' /><a href='".$change_permission_link."' class='DataBrowserDialogLinkChangePermission'>Change permission</a><br/>";
		}
		if ($file->is_delete_access())
		{
			$delete_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&file_id=".$file_id."&action=file_delete";
			$html .= "<img src='images/icons/delete.png' alt='' /><a href='".$delete_link."' class='DataBrowserDialogLinkDelete'>Delete</a><br/>";
		}
		if ($file->is_read_access())
		{
			$open_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&file_id=".$file_id."&action=file_detail";
			$html .= "<img src='images/icons/open_data.png' alt='' /><a href='".$open_link."' class='DataBrowserDialogLinkFollowDirectly'>Open</a><br/>";
		}
		return $html;
	}
	
	private function get_context_sensitive_folder_menu($folder_id)
	{
		global $user;
		$folder = Folder::get_instance($folder_id);
		$html = "";

		if($folder->is_control_access() == true or $folder->get_owner_id() == $user->get_user_id())
		{
			$change_permission_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&folder_id=".$folder_id."&action=permission";
			$html .= "<img src='images/icons/permissions.png' alt='' /><a href='".$change_permission_link."' class='DataBrowserDialogLinkChangePermission'>Change permission</a><br/>";
		}
		if($folder->is_delete_access())
		{
			$delete_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&folder_id=".$folder_id."&action=folder_delete";
			$html .= "<img src='images/icons/delete.png' alt='' /><a href='".$delete_link."' class='DataBrowserDialogLinkDelete'>Delete</a><br/>";
		}
		if($folder->can_rename_folder())
		{
			$rename_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&folder_id=".$folder_id."&action=folder_rename";
			$html .= "<img src='images/icons/rename.png' alt='' /><a href='".$rename_link."' class='DataBrowserDialogLinkRename'>Rename</a><br/>";
		}
		if($folder->is_read_access())
		{
			$open_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&folder_id=".$folder_id;
			$html .= "<img src='images/icons/open_data.png' alt='' /><a href='".$open_link."' class='DataBrowserDialogLinkFollowDirectly'>Open</a><br/>";
		}
		return $html;
	}
	
	private function get_context_sensitive_value_menu($value_id)
	{
		global $user;
		$value= Value::get_instance($value_id);
		$html = "";
		if($value->is_read_access())
		{
			$history_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&value_id=".$value_id."&action=value_history";
			$html .= "<a href='".$history_link."' class='DataBrowserDialogLinkFollowDirectly'>History</a><br/>";
		}
		if($value->is_control_access() == true or $value->get_owner_id() == $user->get_user_id())
		{
			$change_permission_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&value_id=".$value_id."&action=permission";
			$html .= "<img src='images/icons/permissions.png' alt='' /><a href='".$change_permission_link."' class='DataBrowserDialogLinkChangePermission'>Change permission</a><br/>";
		}
		if($value->is_delete_access())
		{
			$delete_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&value_id=".$value_id."&action=value_delete";
			$html .= "<img src='images/icons/delete.png' alt='' /><a href='".$delete_link."' class='DataBrowserDialogLinkDelete'>Delete</a><br/>";
		}
		if($value->is_read_access())
		{
			$open_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&value_id=".$value_id."&action=value_detail";
			$html .= "<img src='images/icons/open_data.png' alt='' /><a href='".$open_link."' class='DataBrowserDialogLinkFollowDirectly'>Open / Edit</a><br/>";
		}
		return $html;
	}
	
	public function get_file_add_menu($folder_path)
	{
		$data_path = new DataPath(null, null);
				
		if($data_path->get_last_entry_type() == 1)
		{ //folder
			
			if($data_path->get_last_entry_id())
			{
		
			}	
		}
		$html = "";
		$folder_id = Folder::get_folder_by_path($folder_path);
		$folder = Folder::get_instance($folder_id);
		if($folder->is_write_access())
		{
			$add_file_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&folder_id=".$folder_id."&run=add_file";
			$add_folder_link = "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']."&nav=data&folder_id=".$folder_id."&run=add_folder";
			$html .= "<a href=\"javascript:data_browser.open_link_in_ui('folder','".$add_file_link."');\">Add File</a><br/>";
			$html .= "<a href=\"javascript:data_browser.open_link_in_ui('folder','".$add_folder_link."');\">Add Folder</a><br/>";
		}
		return $html;
	}
	
	public function get_folder_add_menu($folder_path)
	{

	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
				
				case "get_data_browser_path":
					echo $this->get_data_browser_path($_GET[folder_id],$_GET[virtual_folder_id]);
				break;
				
				case "get_data_browser_path_cleared":
					echo $this->get_data_browser_path_cleared($_GET[folder_id],$_GET[virtual_folder_id]);
				break;
				
				case "get_context_sensitive_file_menu":
					echo $this->get_context_sensitive_file_menu($_GET[file_id]);
				break;
				
				case "get_context_sensitive_folder_menu":
					echo $this->get_context_sensitive_folder_menu($_GET[file_id]);
				break;
				
				case "get_context_sensitive_value_menu":
					echo $this->get_context_sensitive_value_menu($_GET[file_id]);
				break;
				
				case "get_file_add_menu":
					echo $this->get_file_add_menu($_GET[folder_path]);
				break;
				
				default:
				break;
			
			endswitch;
		}
	}
}

$data_browser_ajax = new DataBrowserAjax;
$data_browser_ajax->method_handler();
?>