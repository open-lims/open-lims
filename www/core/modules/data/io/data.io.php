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
 * Data IO Class
 * @package data
 */
class DataIO
{
	/**
	 * @todo remove legacy code
	 * @throws DataSecuriyAccessDeniedException
	 */
	public static function browser()
	{
		global $content;
		
		$data_browser = new DataBrowser();

		if ($_GET[clear] == "delete_stack")
		{
			$data_path = new DataPath(null, null);
			$data_path->delete_stack();
			unset($_GET[clear]);
			unset($_GET[vfolder_id]);
		}
		
		if ($_GET[vfolder_id])
		{
			$virtual_folder = new VirtualFolder($_GET[vfolder_id]);
			
			$virtual_folder_id = $_GET[vfolder_id];
			$folder_id = null;
			$data_path = new DataPath(null, $_GET[vfolder_id]);
		}
		elseif ($_GET[folder_id])
		{
			$folder = Folder::get_instance($_GET[folder_id]);
			
			if ($folder->is_read_access() == false)
			{
				throw new DataSecurityAccessDeniedException();
			}
			else
			{
				$virtual_folder_id = null;
				$folder_id = $_GET[folder_id];
				$data_path = new DataPath($_GET[folder_id], null);
			}
		}
		else
		{
			$data_path = new DataPath(null, null);
			if ($data_path->get_last_entry_type() == true)
			{
				$virtual_folder_id = $data_path->get_last_entry_id();
				$folder_id = null;
			}
			else
			{
				$virtual_folder_id = null;
				$folder_id = $data_path->get_last_entry_id();
			}
		}

		if ($folder_id == null and $virtual_folder_id == null)
		{
			$folder_id = $data_browser->get_folder_id();
			
			$argument_array = array();
			$argument_array[0][0] = "folder_id";
			$argument_array[0][1] = $folder_id;
			$argument_array[1][0] = "virtual_folder_id";
			$argument_array[1][1] = null;
		}

		$argument_array = array();
		$argument_array[0][0] = "folder_id";
		$argument_array[0][1] = $folder_id;
		$argument_array[1][0] = "virtual_folder_id";
		$argument_array[1][1] = $virtual_folder_id;	

		$list = new List_IO("DataBrowser", "ajax.php?nav=data", "list_data_browser", "count_data_browser", $argument_array, "DataBrowserAjax");	
		
		$list->add_column("","delete_checkbox",false,22); 
		$list->add_column("","symbol",false,22);
		$list->add_column("Name","name",true,null);
		$list->add_column("Type","type",true,null);
		$list->add_column("Ver.","version",false,null);
		$list->add_column("Date/Time","datetime",true,null);
		$list->add_column("Size","size",true,null);
		$list->add_column("Owner","owner",true,null);
		$list->add_column("Permission","permission",false,null);

		// !!! [...] !!!
		
		$folder = Folder::get_instance($folder_id);	
		
		$template = new HTMLTemplate("data/data_browser.html");

		if ($folder_id and !$virtual_folder_id)
		{
			if ($folder->is_write_access() == true)
			{
				$template->set_var("add_file", true);
			}
			else
			{
				$template->set_var("add_file", false);
			}
			
			if ($folder->is_folder_image_content() == true)
			{
				$template->set_var("folder_image", true);
			}
			else
			{
				$template->set_var("folder_image", false);
			}
			
			if (($folder->can_change_permission() or 
				$folder->can_add_folder() or 
				$folder->can_command_folder() or 
				$folder->can_rename_folder()) and
				($folder->is_write_access() or 
				$folder->is_delete_access() or
				$folder->is_control_access()))
			{
				$template->set_var("folder_administration", true);
			}
			else
			{
				$template->set_var("folder_administration", false);
			}
			
			$template->set_var("item_administration", false);
		}
		else
		{
			$template->set_var("add_file", false);
			$template->set_var("folder_image", false);
			$template->set_var("folder_administration", false);
			$template->set_var("item_administration", false);
		}
		
		$paramquery = $_GET;
		$paramquery[action] = "image_browser_detail";
		$paramquery[folder_id] = $folder_id;
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
						
		$template->set_var("folder_image_params", $params);
		
		
		$paramquery = $_GET;
		$paramquery[action] = "file_add";
		$paramquery[folder_id] = $folder_id;
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
						
		$template->set_var("add_file_params", $params);


		$paramquery = $_GET;
		$paramquery[clear] = "delete_stack";
		unset($paramquery[folder_id]);
		unset($paramquery[vfolder_id]);
		$params = http_build_query($paramquery,'','&#38;');
						
		$template->set_var("home_folder_params", $params);
		

		$paramquery = $_GET;
		$paramquery[action] = "folder_administration";
		$paramquery[folder_id] = $folder_id;
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
						
		$template->set_var("folder_administration_params", $params);
		
		$paramquery = $_GET;
		$paramquery[action] = "item_administration_folder";
		$paramquery[folder_id] = $folder_id;
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
						
		$template->set_var("item_administration_params", $params);		


		$template->set_var("title","Data Browser");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	/**
	 * @throws FolderIDMissingException
	 * @throws FolderIsEmptyException
	 * @throws DataSecuriyAccessDeniedException
	 */
	public static function image_browser_multi()
	{
		if ($_GET[folder_id])
		{
			$folder_id = $_GET[folder_id];
			$folder = Folder::get_instance($folder_id);
			
			if ($folder->is_read_access() == true)
			{
				$image_browser_array = DataBrowser::get_image_browser_array($folder_id);
				
				if (is_array($image_browser_array) and count($image_browser_array) >= 1)
				{
					if (!$_GET[page])
					{
						$page = 1;
						$address = 0;
					}
					else
					{
						if ($_GET[page] > count($image_browser_array))
						{
							$page = count($image_browser_array);
							$address = count($image_browser_array)-1;
						}
						else
						{
							$page = $_GET[page];
							$address = $_GET[page]-1;
						}
					}
				
					$template = new HTMLTemplate("data/data_image_browser_multi.html");
					
					$paramquery = $_GET;
					$paramquery[nav] = "data";
					$paramquery[run] = "image_browser_multi";
					$paramquery[folder_id] = $folder_id;
					unset($paramquery[nextpage]);
					$params = http_build_query($paramquery,'','&#38;');
									
					$template->set_var("multi_params", $params);
					
					
					$paramquery = $_GET;
					$paramquery[nav] = "data";
					$paramquery[run] = "image_browser_detail";
					$paramquery[folder_id] = $folder_id;
					unset($paramquery[nextpage]);
					$params = http_build_query($paramquery,'','&#38;');
									
					$template->set_var("detail_params", $params);
						
					$content_array = array();
					$counter = 0;
					
					for ($i=0;$i<=2;$i++)
					{
						for ($j=0; $j<=3; $j++)
						{
							$current_address = ($address*12)+$counter;
							
							if ($image_browser_array[$current_address])
							{
								$content_array[$counter][display_image] = true;
								
								$file = File::get_instance($image_browser_array[$current_address]);
						
								$paramquery[session_id] = $_GET[session_id];
								$paramquery[file_id] = $image_browser_array[$current_address];
								$paramquery[multithumb] = "true";
								$params = http_build_query($paramquery,'','&#38;');
												
								$content_array[$counter][image_params] = $params;
								
								$paramquery = $_GET;
								$paramquery[page] = $current_address+1;
								$paramquery[run] = "image_browser_detail";
								$params = http_build_query($paramquery,'','&#38;');
								
								$content_array[$counter][image_click_params] = $params;
								
								$content_array[$counter][name] = $file->get_name();
								$content_array[$counter][version] = $file->get_version();
							}
							else
							{
								$content_array[$counter][display_image] = false;
							}
							
							if ($j==3)
							{
								$content_array[$counter][display_tr] = true;
							}
							else
							{
								$content_array[$counter][display_tr] = false;
							}
							$counter++;
						}
					}
					
					$template->set_var("content_array", $content_array);
	
					$template->set_var("page_bar",Common_IO::page_bar($page, ceil(count($image_browser_array)/12), $_GET));
	
					$template->output();
				}
				else
				{
					throw new FolderIsEmptyException();
				}
			}
			else
			{
				throw new DataSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new FolderIDMissingException();
		}
	}

	/**
	 * @throws FolderIDMissingException
	 * @throws FolderIsEmptyException
	 * @throws DataSecuriyAccessDeniedException
	 */
	public static function image_browser_detail()
	{
		if ($_GET[folder_id])
		{
			$folder_id = $_GET[folder_id];
			$folder = Folder::get_instance($folder_id);
			
			if ($folder->is_read_access() == true)
			{
				$image_browser_array = DataBrowser::get_image_browser_array($folder_id);
				
				if (is_array($image_browser_array) and count($image_browser_array) >= 1)
				{
					if (!$_GET[page])
					{
						$page = 0;
					}
					else
					{
						if ($_GET[page] > count($image_browser_array))
						{
							$page = count($image_browser_array)-1;
						}
						else
						{
							$page = $_GET[page]-1;
						}
					}
				
					if ($image_browser_array[$page])
					{
						$file = File::get_instance($image_browser_array[$page]);
				
						$template = new HTMLTemplate("data/data_image_browser_detail.html");
						
						if ($_GET[version] and is_numeric($_GET[version])) 
						{
							$file->open_internal_revision($_GET[version]);
							$internal_revision = $_GET[version];
						}
						else
						{
							$internal_revision = $file->get_internal_revision();
						}
						
						$file_version_array = $file->get_file_internal_revisions();
						
						if (is_array($file_version_array) and count($file_version_array) > 0)
						{	
							$result = array();
							$counter = 1;
						
							$result[0][version] = 0;
							$result[0][text] = "----------------------------------------------";
							
							foreach($file_version_array as $key => $value)
							{
								$file_version = File::get_instance($image_browser_array[$page]);
								$file_version->open_internal_revision($value);
								
								$result[$counter][version] = $file_version->get_internal_revision();
								$result[$counter][text] = "Version ".$file_version->get_version()." - ".$file_version->get_datetime();
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
								$result[$counter][value] = $value;
								$result[$counter][key] = $key;
								$counter++;
							}
						}
						
						$template->set_var("get",$result);
						
						
						$paramquery = $_GET;
						$paramquery[nav] = "data";
						$paramquery[run] = "image_browser_multi";
						$paramquery[folder_id] = $folder_id;
						$paramquery[page] = floor($page/12)+1;
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery,'','&#38;');
										
						$template->set_var("multi_params", $params);
						
						
						$paramquery = $_GET;
						$paramquery[nav] = "data";
						$paramquery[run] = "image_browser_detail";
						$paramquery[folder_id] = $folder_id;
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery,'','&#38;');
										
						$template->set_var("detail_params", $params);
											
						
						$paramquery[session_id] = $_GET[session_id];
						$paramquery[file_id] = $image_browser_array[$page];
						$paramquery[version] = $internal_revision;
						$params = http_build_query($paramquery,'','&#38;');
										
						$template->set_var("image_params", $params);
						
						
						$paramquery[session_id] = $_GET[session_id];
						$paramquery[file_id] = $image_browser_array[$page];
						$paramquery[full] = "true";
						$paramquery[version] = $internal_revision;
						$params = http_build_query($paramquery,'','&#38;');
						
						$template->set_var("image_click_params", $params);
						
						
						$template->set_var("filename",	$file->get_name());
						$template->set_var("version", $file->get_version());
						$template->set_var("datetime", $file->get_datetime());
	
						$template->set_var("page_bar",Common_IO::page_bar($page+1, count($image_browser_array), $_GET));
	
						$template->output();
					
					}
				}
				else
				{
					throw new FolderIsEmptyException();
				}
			}
			else
			{
				throw new DataSecuriyAccessDeniedException();
			}
		}
		else
		{
			throw new FolderIDMissingException();
		}
	}

	/**
	 * @throws FolderIDMissingException
	 * @throws DataSecuriyAccessDeniedException
	 */
	public static function permission()
	{
		global $user;
		
		if ($_GET[file_id] xor $_GET[value_id])
		{
			if ($_GET[file_id])
			{
				$id = $_GET[file_id];
				$object = File::get_instance($id);
				$type = "file";
				$title = $object->get_name();
			}
			
			if ($_GET[value_id])
			{
				$id = $_GET[value_id];
				$object = Value::get_instance($id);
				$type = "value";
				$title = $object->get_type_name();
			}
		}
		else
		{
			if ($_GET[folder_id])
			{
				$id = $_GET[folder_id];
				$object = Folder::get_instance($id);
				$type = "folder";
				$title = $object->get_name();
			}
			else
			{
				throw new FolderIDMissingException();
			}
		}
		
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
			$data_permission = new DataPermission($type, $id);
			
			if (!$_GET[nextpage])
			{
				$template = new HTMLTemplate("data/data_permission.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params", $params);
				
				$paramquery = $_GET;
				$paramquery[action] = "chown";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params_chown", $params);
				
				$paramquery = $_GET;
				$paramquery[action] = "chgroup";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params_chgroup", $params);
				
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
				
				$template->set_var("back_link", $params);
				
				$template->output();	
			}
			else
			{
				if ($_POST[save])
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$params = http_build_query($paramquery,'','&#38;');
				}
				else
				{
					if ($type == folder)
					{
						$paramquery = $_GET;
						unset($paramquery[action]);
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery,'','&#38;');
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[action]);
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery,'','&#38;');
					}
				}
				
				if ($data_permission->set_permission_array($_POST) == true)
				{
					Common_IO::step_proceed($params, "Permission: ".$title."", "Changes saved succesful" ,null);
				}
				else
				{
					Common_IO::step_proceed($params, "Permission: ".$title."", "Operation failed" ,null);
				}
			}
		}
		else
		{
			throw new DataSecuriyAccessDeniedException();
		}
	}
	
	public static function change_permission($permission_array, $type)
	{
		$permissions = (array)$permission_array;
		switch($type):
			case "File": 
				$id = $_GET[file_id];
			break;
			case "Folder": 
				$id = $_GET[folder_id];
			break;
			case "Value": 
				$id = $_GET[value_id];
			break;
			
		endswitch;
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

	/**
	 * @throws FolderIDMissingException
	 */
	public static function change_owner()
	{
		if ($_GET[file_id] xor $_GET[value_id])
		{
			if ($_GET[file_id])
			{
				$id = $_GET[file_id];
				$object = File::get_instance($id);
				$type = "file";
				$title = $object->get_name();
			}
			if ($_GET[value_id])
			{
				$id = $_GET[value_id];
				$object = Value::get_instance($id);
				$type = "value";
				$title = $object->get_type_name();
			}
		}
		else
		{
			if ($_GET[folder_id])
			{
				$id = $_GET[folder_id];
				$object = Folder::get_instance($id);
				$type = "folder";
				$title = $object->get_name();
			}
			else
			{
				throw new FolderIDMissingException();
			}
		}
		
		if ($object->is_control_access() == true)
		{
			$data_permission = new DataPermission($type, $id);
			
			if (!$_GET[nextpage])
			{
				$template = new HTMLTemplate("data/data_change_owner.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("title",$title);
				$template->set_var("error","");
				
				$user_array = User::list_entries();
				
				$result = array();
				$counter = 0;
				
				foreach($user_array as $key => $value)
				{
					$user = new User($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $user->get_username()." (".$user->get_full_name(false).")";
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$paramquery = $_GET;
				$paramquery[action] = "permission";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("back_link", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				$paramquery[action] = "permission";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($data_permission->set_owner_id($_POST[user]) == true)
				{
					Common_IO::step_proceed($params, "Permission: ".$title."", "Changes saved succesful" ,null);
				}
				else
				{
					Common_IO::step_proceed($params, "Permission: ".$title."", "Operation failed" ,null);
				}
			}
		}
		else
		{
			throw new DataSecuriyAccessDeniedException();
		}
	}
	
	/**
	 * @throws FolderIDMissingException
	 */
	public static function change_group()
	{
		if ($_GET[file_id] xor $_GET[value_id])
		{
			if ($_GET[file_id])
			{
				$id = $_GET[file_id];
				$object = File::get_instance($id);
				$type = "file";
				$title = $object->get_name();
			}
			if ($_GET[value_id])
			{
				$id = $_GET[value_id];
				$object = Value::get_instance($id);
				$type = "value";
				$title = $object->get_type_name();
			}
		}
		else
		{
			if ($_GET[folder_id])
			{
				$id = $_GET[folder_id];
				$object = Folder::get_instance($id);
				$type = "folder";
				$title = $object->get_name();
			}
			else
			{
				throw new FolderIDMissingException();
			}
		}
		
		if ($object->is_control_access() == true)
		{
			$data_permission = new DataPermission($type, $id);
			
			if (!$_GET[nextpage])
			{
				$template = new HTMLTemplate("data/data_change_group.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				$template->set_var("title",$title);
				$template->set_var("error","");
				
				$group_array = Group::list_groups();
				
				$result = array();
				$counter = 0;
				
				foreach($group_array as $key => $value)
				{
					$group = new Group($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $group->get_name();
					$counter++;
				}
				
				$template->set_var("option",$result);
				
				$paramquery = $_GET;
				$paramquery[action] = "permission";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("back_link", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				$paramquery[action] = "permission";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				if ($data_permission->set_owner_group_id($_POST[group]) == true)
				{
					Common_IO::step_proceed($params, "Permission: ".$title."", "Changes saved succesful" ,null);
				}
				else
				{
					Common_IO::step_proceed($params, "Permission: ".$title."", "Operation failed" ,null);
				}
			}
		}
		else
		{
			throw new DataSecuriyAccessDeniedException();
		}
	}

	/**
	 * @throws UserIDMissingException
	 */
	public static function change_quota()
	{
		if ($_GET[id])
		{
			$user = new User($_GET[id]);
			$user_data = new DataUserData($_GET[id]);
						
			if ($_GET[nextpage] == 1)
			{
				if (is_numeric($_POST[quota]))
				{
					$page_1_passed = true;
				}
				else
				{
					$page_1_passed = false;
					$error = "You must enter a valid quota.";
				}
			}
			elseif($_GET[nextpage] > 1)
			{
				$page_1_passed = true;
			}
			else
			{
				$page_1_passed = false;
				$error = "";
			}
			
			if ($page_1_passed == false)
			{
				$template = new HTMLTemplate("data/admin/user/change_user_quota.html");

				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				$template->set_var("error",$error);
				
				if ($_POST[quota])
				{
					$template->set_var("mail", $_POST[quota]);
				}
				else
				{
					$template->set_var("quota", $user_data->get_quota());
				}
				$template->output();
			}
			else
			{
				if ($_GET[retrace])
				{
					$params = http_build_query(Retrace::resolve_retrace_string($_GET[retrace]),'','&#38;');
				}
				else
				{
					$paramquery[username] = $_GET[username];
					$paramquery[session_id] = $_GET[session_id];
					$paramquery[nav] = "home";
					$params = http_build_query($paramquery,'','&#38;');
				}
			
				if ($user_data->set_quota($_POST[quota]))
				{
					Common_IO::step_proceed($params, "Change User Quota", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Change User Quota", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new UserIDMissingException();
		}
	}
	
	/**
	 * @param integer $user_id
	 * @return array
	 */
	public static function get_user_module_detail_setting($user_id)
	{
		if ($user_id)
		{
			$data_user_data = new DataUserData($user_id);
			
			$paramquery = $_GET;
			$paramquery[run] = "module_value_change";
			$paramquery[dialog] = "user_quota";
			$paramquery[retrace] = Retrace::create_retrace_string();
			$params = http_build_query($paramquery, '', '&#38;');
			
			$return_array = array();
			$return_array[value] = Convert::convert_byte_1024($data_user_data->get_quota());
			$return_array[params] = $params;
			return $return_array;	
		}
		else
		{
			return null;
		}
	}
}

?>
