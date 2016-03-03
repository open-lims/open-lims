<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * File IO Class
 * @package data
 */
class FileIO
{
	/**
	 * @throws FileIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function detail()
	{
		global $regional;
		
		if ($_GET['file_id'])
		{
			$file = File::get_instance($_GET['file_id']);
			
			if ($file->is_read_access())
			{
				$template = new HTMLTemplate("data/file_detail.html");
				
				$folder = Folder::get_instance($file->get_parent_folder_id());
				
				if ($_GET['version'] and is_numeric($_GET['version']))
				{	
					$file->open_internal_revision($_GET['version']);
					$internal_revision = $_GET['version'];
				}
				else
				{
					$internal_revision = $file->get_internal_revision();
				}
				
				$template = new HTMLTemplate("data/file_detail.html");
				
				$folder = Folder::get_instance($file->get_parent_folder_id());
				$user = new User($file->get_owner_id());
				
				$file_version_array = $file->get_file_internal_revisions();
				
				if (is_array($file_version_array) and count($file_version_array) > 0)
				{	
					$result = array();
					$counter = 1;
				
					$result[0]['version'] = 0;
					$result[0]['text'] = "----------------------------------------------";
					
					$file_version = File::get_instance($_GET['file_id'], true);
					
					foreach($file_version_array as $key => $value)
					{
						$file_version->open_internal_revision($value);
						
						$version_datetime_handler = new DatetimeHandler($file_version->get_datetime());
						
						$result[$counter]['version'] = $file_version->get_internal_revision();
						$result[$counter]['text'] = "Version ".$file_version->get_version()." - ".$version_datetime_handler->get_datetime();
						$counter++;
					}
					$template->set_var("version_option",$result);
				}
				
				$result = array();
				$counter = 0;
				
				foreach($_GET as $key => $value)
				{
					if ($key != "version")
					{
						$result[$counter]['value'] = $value;
						$result[$counter]['key'] = $key;
						$counter++;
					}
				}
				
				$template->set_var("get",$result);
				
				$template->set_var("version",$file->get_version());
				
				$paramquery = $_GET;
				$paramquery['action'] = "file_history";
				$params = http_build_query($paramquery,'','&#38;');	
				
				$template->set_var("version_list_link",$params);
				
				$template->set_var("title",$file->get_name());
				
				$template->set_var("name",$file->get_name());
				$template->set_var("path",$folder->get_object_path());
				
				$template->set_var("size",Convert::convert_byte_1024($file->get_size()));
				$template->set_var("size_in_byte",$regional->format_number($file->get_size()));
				
				$create_datetime_handler = new DatetimeHandler($file->get_datetime());
				$version_datetime_handler = new DatetimeHandler($file->get_version_datetime());
				
				$template->set_var("creation_datetime", $create_datetime_handler->get_datetime());
				$template->set_var("version_datetime", $version_datetime_handler->get_datetime());
				$template->set_var("mime_type",$file->get_mime_type());
				$template->set_var("owner",$user->get_full_name(false));
				$template->set_var("checksum",$file->get_checksum());
				$template->set_var("permission",$file->get_permission_string());
				$template->set_var("comment","");
				
				if ($file->is_image() == true)
				{
					$template->set_var("thumbnail_image","<img src='image.php?session_id=".$_GET['session_id']."&file_id=".$_GET['file_id']."&max_width=340&max_height=350' alt='' />");
				}
				else
				{
					$template->set_var("thumbnail_image","");
				}
				
				$paramquery = array();
				$paramquery['username'] = $_GET['username'];
				$paramquery['session_id'] = $_GET['session_id'];
				$paramquery['file_id'] = $_GET['file_id'];
				
				if ($_GET['version'])
				{
					$paramquery['version'] = $_GET['version'];
				}
				
				$params = http_build_query($paramquery,'','&#38;');	
				
				
				$template->set_var("download_params",$params);
				
				$paramquery = $_GET;
				$paramquery['action'] = "file_update";
				$paramquery['version'] = $internal_revision;
				$paramquery['retrace'] = Retrace::create_retrace_string();
				$params = http_build_query($paramquery,'','&#38;');	
				$template->set_var("update_params",$params);
				
				$paramquery = $_GET;
				$paramquery['action'] = "file_update_minor";
				$paramquery['version'] = $file->get_internal_revision();
				$paramquery['retrace'] = Retrace::create_retrace_string();
				$params = http_build_query($paramquery,'','&#38;');	
				$template->set_var("update_minor_params",$params);
				
				$paramquery = $_GET;
				$paramquery['action'] = "permission";
				$params = http_build_query($paramquery,'','&#38;');	
				$template->set_var("set_permission_params",$params);
				
				
				$template->set_var("write_access",$file->is_write_access());
	
				if ($file->is_control_access() == true or $file->get_owner_id() == $user->get_user_id())
				{
					$template->set_var("change_permission",true);
				}
				else
				{
					$template->set_var("change_permission",false);
				}
				
				$template->set_var("delete_access",$file->is_delete_access());
				
				
				$paramquery = $_GET;
				$paramquery['action'] = "file_delete";
				unset($paramquery['sure']);
				$params = http_build_query($paramquery,'','&#38;');	
				
				$template->set_var("delete_file_params",$params);
				
				
				$paramquery = $_GET;
				$paramquery['action'] = "file_delete_version";
				$paramquery['version'] = $internal_revision;
				unset($paramquery['sure']);
				$params = http_build_query($paramquery,'','&#38;');	
				
				$template->set_var("delete_file_version_params",$params);
				
				
				$paramquery = $_GET;
				unset($paramquery['file_id']);
				unset($paramquery['version']);
				unset($paramquery['action']);
				$params = http_build_query($paramquery,'','&#38;');	
				
				$template->set_var("back_link",$params);
				
				$template->output();
			}
			else
			{
				throw new DataSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new FileIDMissingException();
		}
	}

	/**
	 * @param string $item_holder_type
	 * @param integer $item_holder_id
	 * @param bool $as_page
	 * @param bool $in_assistant
	 * @param string $form_field_name
	 * @throws ItemHolderTypeMissingException
	 * @throws ItemHolderIDMissingException
	 */
	public static function list_file_items($item_holder_type, $item_holder_id, $as_page = true, $in_assistant = false, $form_field_name = null)
	{
		global $session, $user;
		
		if (!$item_holder_type)
		{
			throw new ItemHolderTypeMissingException();
		}
		
		if (!is_numeric($item_holder_id))
		{
			throw new ItemHolderIDMissingException();
		}
		
		$argument_array = array();
		$argument_array[0][0] = "item_holder_type";
		$argument_array[0][1] = $item_holder_type;
		$argument_array[1][0] = "item_holder_id";
		$argument_array[1][1] = $item_holder_id;
		$argument_array[2][0] = "as_page";
		$argument_array[2][1] = $as_page;
		$argument_array[3][0] = "in_assistant";
		$argument_array[3][1] = $in_assistant;
		
		if ($in_assistant == false)
		{	
			$list = new List_IO("DataFileItem", "/core/modules/data/ajax/file.ajax.php", "list_file_items", "count_file_items", $argument_array, "DataAjaxFiles", 20, true, true);
			
			$list->add_column("","symbol",false,16);
			$list->add_column(Language::get_message("DataGeneralListColumnName", "general"),"name",true,null);
			$list->add_column(Language::get_message("DataGeneralListColumnSize", "general"),"size",true,null);
			$list->add_column(Language::get_message("DataGeneralListColumnDateTime", "general"),"datetime",true,null);
		}
		else
		{	
			$list = new List_IO("DataFileItem", "/core/modules/data/ajax/file.ajax.php", "list_file_items", "count_file_items", $argument_array, "DataAjaxFiles", 20, false, false);
			
			$list->add_column("","checkbox",false,16, $form_field_name);
			$list->add_column("","symbol",false,16);
			$list->add_column(Language::get_message("DataGeneralListColumnName", "general"),"name",false,null);
			$list->add_column(Language::get_message("DataGeneralListColumnSize", "general"),"size",false,null);
			$list->add_column(Language::get_message("DataGeneralListColumnDateTime", "general"),"datetime",false,null);
		}
				
		$template = new HTMLTemplate("data/file_list.html");	
		
		$template->set_var("list", $list->get_list());
		
		return $template->get_string();
	}
	
	/**
	 * @param array $type_array
	 * @param array $category_array
	 * @param integer $organisation_unit_id
	 * @param integer $folder_id
	 * @throws FolderIDMissingException
	 */
	public static function upload_as_item($type_array, $category_array, $holder_class, $holder_id, $position_id)
	{		
		if (class_exists($holder_class))
		{
			$item_holder = new $holder_class($holder_id);
			
			if ($item_holder instanceof ItemHolderInterface)
			{
				$folder_id = $item_holder->get_item_holder_value("folder_id", $position_id);
			}
		}
		
		if (is_numeric($folder_id))
		{
			$template = new HTMLTemplate("data/file_upload_item.html");
			
			$unique_id = uniqid();
			
			$paramquery = $_GET;
			$paramquery['unique_id'] = $unique_id;
			$paramquery['folder_id'] = $folder_id;
			$params = http_build_query($paramquery, '', '&#38;');
			
			$template->set_var("params", $params);
			$template->set_var("unique_id", $unique_id);
			$template->set_var("session_id", $_GET['session_id']);
			
			if ($_GET['retrace'])
			{
				$js_retrace_array = array();
				$js_retrace_counter = 0;
				$retrace_array = unserialize(base64_decode($_GET['retrace']));
				foreach($retrace_array as $key => $value)
				{
					$js_retrace_array[$js_retrace_counter][0] = $key;
					$js_retrace_array[$js_retrace_counter][1] = $value;
					$js_retrace_counter++;
				}
				$template->set_var("retrace", serialize($js_retrace_array));
			}
			else
			{
				$template->set_var("retrace", "");
			}
			
			if ($_POST['keywords'])
			{
				$template->set_var("keywords", $_POST['keywords']);
			}
			else
			{
				$template->set_var("keywords", "");
			}
			
			if ($_POST['description'])
			{
				$template->set_var("description", $_POST['description']);
			}
			else
			{
				$template->set_var("description", "");	
			}
			
			$template->output();
		}
		else
		{
			throw new FolderIDMissingException();
		}	
	}

	/**
	 * @throws FolderIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function upload()
	{
		if ($_GET['folder_id'])
		{
			$folder = Folder::get_instance($_GET['folder_id']);
			
			if ($folder->is_write_access() == true)
			{
				$template = new HTMLTemplate("data/file_upload.html");
				
				$unique_id = uniqid();
				
				$paramquery = $_GET;
				$paramquery['unique_id'] = $unique_id;
				$params = http_build_query($paramquery, '', '&#38;');
				
				$template->set_var("params", $params);
				$template->set_var("unique_id", $unique_id);
				$template->set_var("session_id", $_GET['session_id']);
				
				if ($_GET['retrace'])
				{
					$js_retrace_array = array();
					$js_retrace_counter = 0;
					$retrace_array = unserialize(base64_decode($_GET['retrace']));
					foreach($retrace_array as $key => $value)
					{
						$js_retrace_array[$js_retrace_counter][0] = $key;
						$js_retrace_array[$js_retrace_counter][1] = $value;
						$js_retrace_counter++;
					}
					$template->set_var("retrace", serialize($js_retrace_array));
				}
				else
				{
					$template->set_var("retrace", "");
				}
				
				$template->output();
			}
			else
			{
				throw new DataSecurityAccessDeniedExcpetion();
			}		
		}
		else
		{
			throw new FolderIDMissingException();
		}
	}
	
	/**
	 * @throws FileIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function update()
	{
		if ($_GET['file_id'])
		{		
			$file = File::get_instance($_GET['file_id']);
			
			if ($file->is_write_access())
			{
				$template = new HTMLTemplate("data/file_update.html");
				
				$unique_id = uniqid();
				
				$paramquery = $_GET;
				$paramquery['unique_id'] = $unique_id;
				$params = http_build_query($paramquery, '', '&#38;');
				
				$template->set_var("params", $params);
				$template->set_var("unique_id", $unique_id);
				$template->set_var("session_id", $_GET['session_id']);
				
				if ($_GET['retrace'])
				{
					$js_retrace_array = array();
					$js_retrace_counter = 0;
					$retrace_array = unserialize(base64_decode($_GET['retrace']));
					foreach($retrace_array as $key => $value)
					{
						$js_retrace_array[$js_retrace_counter][0] = $key;
						$js_retrace_array[$js_retrace_counter][1] = $value;
						$js_retrace_counter++;
					}
					$template->set_var("retrace", serialize($js_retrace_array));
				}
				else
				{
					$template->set_var("retrace", "");
				}
				
				$template->output();
			}
			else
			{
				throw new DataSecurityAccessDeniedException();
			}	
		}
		else
		{
			throw new FileIDMissingException();
		}
	}

	/**
	 * @throws FileIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function delete()
	{		
		if ($_GET['file_id'])
		{
			$file = File::get_instance($_GET['file_id']);
			
			if ($file->is_delete_access())
			{
				if ($_GET['sure'] != "true")
				{
					$template = new HTMLTemplate("data/file_delete.html");
					
					$paramquery = $_GET;
					$paramquery['sure'] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					$paramquery['action'] = "file_detail";
					unset($paramquery['sure']);
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$file = File::get_instance($_GET['file_id']);
					
					if ($file->delete() == true)
					{
						$paramquery = $_GET;
						unset($paramquery['sure']);
						unset($paramquery['action']);
						unset($paramquery['file_id']);
						$params = http_build_query($paramquery);
								
						Common_IO::step_proceed($params, "Delete File", "Operation Successful" ,null);
					}
					else
					{
						$paramquery = $_GET;
						$paramquery['action'] = "file_detail";
						unset($paramquery['sure']);
						$params = http_build_query($paramquery);
								
						Common_IO::step_proceed($params, "Delete File", "Operation Failed" ,null);
					}			
				}
			}
			else
			{
				throw new DataSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new FileIDMissingException();
		}
	}
	
	/**
	 * @throws FileIDMissingException
	 * @throws FileVersionIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function delete_version()
	{		
		if ($_GET['file_id'])
		{
			if ($_GET['version'])
			{
				$file = File::get_instance($_GET['file_id']);
				
				if ($file->is_delete_access())
				{
					if ($_GET['sure'] != "true")
					{
						$template = new HTMLTemplate("data/file_delete_version.html");
						
						$paramquery = $_GET;
						$paramquery['sure'] = "true";
						$params = http_build_query($paramquery);
						
						$template->set_var("yes_params", $params);
								
						$paramquery = $_GET;
						$paramquery['action'] = "file_detail";
						unset($paramquery['sure']);
						$params = http_build_query($paramquery);
						
						$template->set_var("no_params", $params);
						
						$template->output();
					}
					else
					{
						$file = File::get_instance($_GET['file_id']);
						
						if (($return_value = $file->delete_version($_GET['version'])) != 0)
						{
							if ($return_value == 1)
							{
								$paramquery = $_GET;
								$paramquery['action'] = "file_detail";
								unset($paramquery['sure']);
								unset($paramquery['version']);
								$params = http_build_query($paramquery);
							}
							else
							{
								$paramquery = $_GET;
								unset($paramquery['sure']);
								unset($paramquery['action']);
								unset($paramquery['file_id']);
								$params = http_build_query($paramquery);
							}
							Common_IO::step_proceed($params, "Delete File", "Operation Successful" ,null);
						}
						else
						{
							$paramquery = $_GET;
							$paramquery['action'] = "file_detail";
							unset($paramquery['sure']);
							$params = http_build_query($paramquery);
									
							Common_IO::step_proceed($params, "Delete File", "Operation Failed" ,null);
						}			
					}
				}
				else
				{
					throw new DataSecurityAccessDeniedException();
				}
			}
			else
			{
				throw new FileVersionIDMissingException();
			}
		}
		else
		{
			throw new FileIDMissingException();
		}
	}

	/**
	 * @throws FileIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function history()
	{
		if ($_GET['file_id'])
		{
			$file = File::get_instance($_GET['file_id']);
			
			if ($file->is_read_access())
			{
				$argument_array = array();
				$argument_array[0] = "file_id";
				$argument_array[1] = $_GET['file_id'];
	
				$list = new List_IO("DataFileVersionHistory", "ajax.php?nav=data", "file_list_versions", "file_count_versions", $argument_array, "DataFileVersionHistory");

				$list->add_column("","symbol",false,"16px");
				$list->add_column(Language::get_message("DataGeneralListColumnName", "general"),"name",true,null);
				$list->add_column(Language::get_message("DataGeneralListColumnVersion", "general"),"version",false,null);
				$list->add_column(Language::get_message("DataGeneralListColumnDateTime", "general"),"datetime",true,null);
				$list->add_column(Language::get_message("DataGeneralListColumnUser", "general"),"user",true,null);
				$list->add_column("","delete",false,"16px");
				
				$template = new HTMLTemplate("data/file_history.html");

				$template->set_var("title", $file->get_name());	
				$template->set_var("list", $list->get_list());	
								
				$template->output();
			}
			else
			{
				throw new DataSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new FileIDMissingException();
		}		
	}

}

?>
