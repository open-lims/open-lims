<?php
/**
 * @package data
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
 * Data AJAX IO Class
 * @package data
 */
class DataAjax
{	
	public static function list_data_browser($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}	
		
		if ($json_argument_array)
		{
			$argument_array = json_decode($json_argument_array);
		}
		
		$folder_id = $argument_array[0][1];
		$virtual_folder_id = $argument_array[1][1];
		
		if (!$folder_id and !$virtual_folder_id)
		{
			$data_path = new DataPath(null,null);
			$folder_id = $data_path->get_folder_id();
		}
		
		if (is_numeric($folder_id) or is_numeric($virtual_folder_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = DataBrowser::get_data_browser_array($folder_id, $virtual_folder_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			if ($folder_id)
			{
				$data_path = new DataPath($folder_id, null);
			}
			elseif($virtual_folder_id)
			{
				$data_path = new DataPath(null, $virtual_folder_id);
			}
			else
			{
				$data_path = new DataPath(null, null);
			}
			
			if ($folder_id != 1 or $virtual_folder_id != null)
			{
				if ($data_path->get_previous_entry_virtual() == true)
				{			
					$paramquery = $_GET;
					$paramquery[vfolder_id] = $data_path->get_previous_entry_id();
					unset($paramquery[folder_id]);
					unset($paramquery[nextpage]);
					unset($paramquery[page]);
					$params = http_build_query($paramquery,'','&#38;');
				}
				else
				{
					$paramquery = $_GET;
					$paramquery[folder_id] = $data_path->get_previous_entry_id();
					unset($paramquery[nextpage]);
					unset($paramquery[vfolder_id]);
					unset($paramquery[page]);
					$params = http_build_query($paramquery,'','&#38;');
				}		
				
				$first_line_array[symbol][link] = $params;
				$first_line_array[symbol][content] = "<img src='images/icons/parent_folder.png' alt='' style='border:0;' />";
				$first_line_array[name][link] = $params;
				$first_line_array[name][content] = "[parent folder]";
				$first_line_array[type] = "Parent Folder";
				$first_line_array[version] = "";
				$first_line_array[datetime] = "";
				$first_line_array[size] = "";
				$first_line_array[owner] = "";
				$first_line_array[permission] = "";
				
				$list_request->add_first_line($first_line_array, "DataBrowserParentFolderRow");
			} 
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					// Common
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
										
					if ($list_array[$key][owner_id])
					{
						$user = new User($list_array[$key][owner_id]);
					}
					else
					{
						$user = new User(1);
					}
					
					$list_array[$key][owner] = $user->get_full_name(true);

					// Special
					if ($list_array[$key][file_id])
					{						
						$file = File::get_instance($list_array[$key][file_id]);

						if ($file->is_read_access() == true)
						{
							$paramquery = $_GET;
							$paramquery[nav] = $_GET[nav];
							$paramquery[file_id] = $list_array[$key][file_id];
							$paramquery[action] = "file_detail";
							$params = http_build_query($paramquery,'','&#38;');
						
							$list_array[$key][symbol][link] = $params;
							$list_array[$key][symbol][content] = "<img src='".$file->get_icon()."' alt='' style='border:0;' />";
							
							
							$tmp_name = $list_array[$key][name];
							unset($list_array[$key][name]);
							$list_array[$key][name][content] = $tmp_name;
							$list_array[$key][name][link] = $params;
						}
						else
						{
							$list_array[$key][symbol] = "<img src='core/images/denied_overlay.php?image=".$file->get_icon()."' alt='' border='0' />";
						}
						
						$list_array[$key][type] = "File";
						$list_array[$key][version] = $file->get_version();
						$list_array[$key][size] = Convert::convert_byte_1024($list_array[$key][size]);
						$list_array[$key][permission] = $file->get_permission_string();
						
						if($file->is_delete_access())
						{
							$list_array[$key][delete_checkbox] = "<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name=''></input>";
						}
						else
						{
							$list_array[$key][delete_checkbox] = "<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name='' disabled='disabled'></input>";
						}
					}
					elseif ($list_array[$key][value_id])
					{
						$value = Value::get_instance($list_array[$key][value_id]);

						if ($value->is_read_access() == true)
						{
							$paramquery = $_GET;
							$paramquery[nav] = $_GET[nav];
							$paramquery[value_id] = $list_array[$key][value_id];
							$paramquery[action] = "value_detail";
							$params = http_build_query($paramquery,'','&#38;');
						
							$list_array[$key][symbol][link] = $params;
							$list_array[$key][symbol][content] = "<img src='images/fileicons/16/unknown.png' alt='' style='border:0;' />";
														
							$tmp_name = $list_array[$key][name];
							unset($list_array[$key][name]);
							$list_array[$key][name][content] = $tmp_name;
							$list_array[$key][name][link] = $params;
						}
						else
						{
							$list_array[$key][symbol] = "<img src='core/images/denied_overlay.php?image=images/fileicons/16/unknown.png' alt='' border='0' />";
						}
						
						$list_array[$key][type] = "Value";
						$list_array[$key][version] = $value->get_version();
						$list_array[$key][permission] = $value->get_permission_string();
						
						if($value->is_delete_access())
						{
							$list_array[$key][delete_checkbox] = "<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name=''></input>";
						}
						else
						{
							$list_array[$key][delete_checkbox] = "<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name='' disabled='disabled'></input>";
						}
					}
					elseif ($list_array[$key][folder_id])
					{	
						$sub_folder = Folder::get_instance($list_array[$key][folder_id]);				
						if ($sub_folder->is_read_access() == true)
						{
							$paramquery = $_GET;
							$paramquery[folder_id] = $list_array[$key][folder_id];
							unset($paramquery[nextpage]);
							unset($paramquery[vfolder_id]);
							unset($paramquery[page]);
							$params = http_build_query($paramquery,'','&#38;');
							
							$list_array[$key][symbol][content] = "<img src='images/icons/folder.png' alt='' style='border:0;' />";
							$list_array[$key][symbol][link] = $params;
							
							$tmp_name = $list_array[$key][name];
							unset($list_array[$key][name]);
							$list_array[$key][name][content] = $tmp_name;
							$list_array[$key][name][link] = $params;
							$list_array[$key][name]['class'] = "DataBrowserIsFolder";
						}
						else
						{
							$list_array[$key][symbol] = "<img src='core/images/denied_overlay.php?image=images/icons/folder.png' alt='' border='0' />";
						}
						
						$list_array[$key][type] = "Folder";
						$list_array[$key][permission] = $sub_folder->get_permission_string();
						
						if($sub_folder->is_delete_access())
						{
							$list_array[$key][delete_checkbox] = "<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name=''></input>";
						}
						else
						{
							$list_array[$key][delete_checkbox] = "<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name='' disabled='disabled'></input>";
						}
					}
					elseif ($list_array[$key][virtual_folder_id])
					{
						$paramquery = $_GET;
						$paramquery[vfolder_id] = $list_array[$key][virtual_folder_id];
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery,'','&#38;');
						
						$list_array[$key][symbol][content] = "<img src='images/icons/virtual_folder.png' alt='' style='border:0;' />";
						$list_array[$key][symbol][link] = $params;
						
						$tmp_name = $list_array[$key][name];
						unset($list_array[$key][name]);
						$list_array[$key][name][content] = $tmp_name;
						$list_array[$key][name][link] = $params;
						
						$list_array[$key][type] = "Virtual Folder";
						$list_array[$key][permission] = "automatic";
						
						$list_array[$key][delete_checkbox] = "<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name='' disabled='disabled'></input>";
					}
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			// Error
		}
	}
	
	public static function count_data_browser($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
				
		$folder_id = $argument_array[0][1];
		$virtual_folder_id = $argument_array[1][1];
		
		if (!$folder_id and !$virtual_folder_id)
		{
			$data_path = new DataPath(null,null);
			$folder_id = $data_path->get_folder_id();
			$virtual_folder_id = $data_path->get_virtual_folder_id();
		}
		
		if (is_numeric($folder_id) or is_numeric($virtual_folder_id))
		{
			return DataBrowser::count_data_browser_array($folder_id, $virtual_folder_id);
		}
		else
		{
			return null;
		}
	}
	
	public static function permission_window()
	{
		global $user;
		
		try
		{
			if ($_POST[file_id] xor $_POST[value_id])
			{
				if ($_POST[file_id])
				{
					$id = $_POST[file_id];
					$object = File::get_instance($id);
					$type = "file";
					$title = $object->get_name();
				}
				
				if ($_POST[value_id])
				{
					$id = $_POST[value_id];
					$object = Value::get_instance($id);
					$type = "value";
					$title = $object->get_type_name();
				}
			}
			else
			{
				if ($_POST[folder_id])
				{
					$id = $_POST[folder_id];
					$object = Folder::get_instance($id);
					$type = "folder";
					$title = $object->get_name();
				}
				else
				{
					throw new FolderIDMissingException();
				}
			}
			
			$data_permission = new DataPermission($type, $id);
											
			$template = new HTMLTemplate("data/data_permission_window.html");
				
			$template->set_var("title", $title);
			
			$user = new User($data_permission->get_owner_id());
			$group = new Group($data_permission->get_owner_group_id());
			
			$template->set_var("owner", $user->get_full_name(false));
			$template->set_var("owner_group", $group->get_name());

			if ($object->can_set_automatic())
			{
				$disable_automatic = false;
			}
			else
			{
				$disable_automatic = true;
			}
			
			if ($object->can_set_data_entity())
			{
				$disable_project = false;
			}
			else
			{
				$disable_project = true;
			}
			
			if ($object->can_set_control())
			{
				$disable_control = false;
			}
			else
			{
				$disable_control = true;
			}
			
			if ($object->can_set_remain())
			{
				$disable_remain = false;
			}
			else
			{
				$disable_remain = true;
			}
			
			if ($disable_automatic == true)
			{
				$template->set_var("disabled_automatic","disabled='disabled'");
			}
			else
			{
				$template->set_var("disabled_automatic","");
			}
			
			if ($data_permission->get_automatic() == true) {
				$template->set_var("checked_automatic","checked='checked'");
				if ($disable_automatic == true)
				{
					$template->set_var("hidden_automatic","<input type='hidden' name='automatic' value='1' />");
				}
				else
				{
					$template->set_var("hidden_automatic","");
				}
			}else{
				$template->set_var("checked_automatic","");
				$template->set_var("hidden_automatic","");
			}
			

			$permission_array = $data_permission->get_permission_array();

			for ($i=1;$i<=4;$i++)
			{
				for ($j=1;$j<=4;$j++)
				{
					$checked_name = "checked_".$i."_".$j;
					$disabled_name = "disabled_".$i."_".$j;
					$hidden_name = "hidden_".$i."_".$j;
					
					if ($i==3 and $disable_project == true)
					{
						$template->set_var($disabled_name,"disabled='disabled'");
						$disabled = true;
					}
					else
					{
						if (($j==3 or $j==4) and $disable_control == true)
						{
							$template->set_var($disabled_name,"disabled='disabled'");
							$disabled = true;
						}
						else
						{
							if ($disable_remain == true)
							{
								$template->set_var($disabled_name,"disabled='disabled'");
								$disabled = true;
							}
							else
							{
								$template->set_var($disabled_name,"");
								$disabled = false;
							}
						}
					}
					
					if ($permission_array[$i][$j] == true)
					{
						$template->set_var($checked_name,"checked='checked'");
						if ($disabled == true)
						{
							$template->set_var($hidden_name, "<input type='hidden' name='".$checked_name."' value='1' />");
						}
						else
						{
							$template->set_var($hidden_name, "");
						}
					}
					else
					{
						$template->set_var($checked_name,"");
						$template->set_var($hidden_name, "");
					}
					$disabled = false;
				}
			}

			$paramquery = $_GET;
			$paramquery[nav] = "data";
			unset($paramquery[action]);
			$params = http_build_query($paramquery,'','&#38;');
			
			return $template->get_string();	
		}
		catch (IdMissingException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function change_permission($permission_array, $type)
	{
		global $user;
		
		$permissions = (array)$permission_array;
		switch($type):
			case "File": 
				$id = $_POST[file_id];
				$object = File::get_instance($id);
			break;
			case "Folder": 
				$id = $_POST[folder_id];
				$object = Folder::get_instance($id);
			break;
			case "Value": 
				$id = $_POST[value_id];
				$object = Value::get_instance($id);
			break;
		endswitch;
		
		if ($object->is_control_access() == true)
		{
			$full_access = true;
		}
		else{
			$full_access = false;
		}
		
		if ($object->get_owner_id() == $user->get_user_id())
		{
			$user_access = true;
		}
		else
		{
			$user_access = false;
		}
		
		if ($full_access == true or $user_access == true)
		{
			$type = strtolower($type);
			$id = intval($id);
			echo $id;
			$data_permission = new DataPermission($type, $id);
			
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[nextpage]);
			$params = http_build_query($paramquery,'','&#38;');
			if ($data_permission->set_permission_array($permissions) == true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			throw new DataSecurityAccessDeniedException();
		}
	}
	
	public static function get_allowed_image_types()
	{
		$array = array();
		$array[] = "jpg";
		$array[] = "jpeg";
		$array[] = "png";
		$array[] = "tif";
		$array[] = "tiff";
		$array[] = "bmp";
		$array[] = "gif";
		return json_encode($array);
	}
}
?>