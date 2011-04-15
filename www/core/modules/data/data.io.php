<?php
/**
 * @package data
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
 * Data IO Class
 * @package data
 */
class DataIO
{
	/**
	 * Remove Exception Dependency
	 */
	public static function browser()
	{
		global $content;
		
		try
		{
			$data_browser = new DataBrowser();
	
			if ($_GET[run] == "delete_stack")
			{
				$data_path = new DataPath(null, null);
				$data_path->delete_stack();
				unset($_GET[run]);
				unset($_GET[vfolder_id]);
			}
			
			if ($_GET[vfolder_id])
			{
				
				if (VirtualFolder::exist_vfolder($_GET[vfolder_id]) == false)
				{
					throw new DataException("",1);
				}
				else
				{
					$virtual_folder_id = $_GET[vfolder_id];
					$folder_id = null;
					$data_path = new DataPath(null, $_GET[vfolder_id]);
				}
			}
			elseif ($_GET[folder_id])
			{
				$folder = Folder::get_instance($_GET[folder_id]);
				
				if ($folder->exist_folder() == false)
				{
					throw new DataException("",1);
				}
				else
				{
					if ($folder->is_read_access() == false)
					{
						throw new DataSecurityException("",1);
					}
					else
					{
						$virtual_folder_id = null;
						$folder_id = $_GET[folder_id];
						$data_path = new DataPath($_GET[folder_id], null);
					}
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
			}
					
				
			// !!!!! ---------- !!!!!!!!!!!!!1
			$folder = Folder::get_instance($folder_id);	

			$list = new List_IO(DataBrowser::count_data_browser_array($folder_id, $virtual_folder_id), 20);

			$list->set_top_right_text($data_path->get_stack_path());
			
			$list->add_row("","symbol",false,16);
			$list->add_row("Name","name",true,null);
			$list->add_row("Type","type",true,null);
			$list->add_row("Ver.","version",false,null);
			$list->add_row("Date/Time","datetime",true,null);
			$list->add_row("Size","size",true,null);
			$list->add_row("Owner","owner",true,null);
			$list->add_row("Permission","permission",false,null);
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = DataBrowser::get_data_browser_array($folder_id, $virtual_folder_id, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = DataBrowser::get_data_browser_array($folder_id, $virtual_folder_id, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = DataBrowser::get_data_browser_array($folder_id, $virtual_folder_id, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = DataBrowser::get_data_browser_array($folder_id, $virtual_folder_id, null, null, 0, 20);
				}	
			}
			
			if ($folder_id != 1 or $virtual_folder_id != null)
			{
				$column_array = array();
				
				
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
				
				$list->add_first_line($first_line_array);
			} 
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					// Common
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
					if ($result_array[$key][owner_id])
					{
						$user = new User($result_array[$key][owner_id]);
					}
					else
					{
						$user = new User(1);
					}
					
					$result_array[$key][owner] = $user->get_full_name(true);

					// Special
					if ($result_array[$key][file_id])
					{						
						$file = new File($result_array[$key][file_id]);

						if ($file->is_read_access() == true)
						{
							$paramquery = $_GET;
							$paramquery[file_id] = $result_array[$key][file_id];
							$paramquery[action] = "file_detail";
							unset($paramquery[nextpage]);
							unset($paramquery[version]);
							$params = http_build_query($paramquery,'','&#38;');
						
							$result_array[$key][symbol][link] = $params;
							$result_array[$key][symbol][content] = "<img src='".$file->get_icon()."' alt='' style='border:0;' />";
							
							$tmp_name = $result_array[$key][name];
							unset($result_array[$key][name]);
							$result_array[$key][name][content] = $tmp_name;
							$result_array[$key][name][link] = $params;
						}
						else
						{
							$result_array[$key][symbol] = "<img src='core/images/denied_overlay.php?image=".$file->get_icon()."' alt='' border='0' />";
						}
						
						$result_array[$key][type] = "File";
						$result_array[$key][version] = $file->get_version();
						$result_array[$key][size] = Misc::calc_size($result_array[$key][size]);
						$result_array[$key][permission] = $file->get_permission_string();
					}
					elseif ($result_array[$key][value_id])
					{
						$value = new Value($result_array[$key][value_id]);

						if ($value->is_read_access() == true)
						{
							$paramquery = $_GET;
							$paramquery[value_id] = $result_array[$key][value_id];
							$paramquery[action] = "value_detail";
							unset($paramquery[nextpage]);
							unset($paramquery[version]);
							$params = http_build_query($paramquery,'','&#38;');
						
							$result_array[$key][symbol][link] = $params;
							$result_array[$key][symbol][content] = "<img src='images/fileicons/16/unknown.png' alt='' style='border:0;' />";
							
							$tmp_name = $result_array[$key][name];
							unset($result_array[$key][name]);
							$result_array[$key][name][content] = $tmp_name;
							$result_array[$key][name][link] = $params;
						}
						else
						{
							$result_array[$key][symbol] = "<img src='core/images/denied_overlay.php?image=images/fileicons/16/unknown.png' alt='' border='0' />";
						}
						
						$result_array[$key][type] = "Value";
						$result_array[$key][version] = $value->get_version();
						$result_array[$key][permission] = $value->get_permission_string();
					}
					elseif ($result_array[$key][folder_id])
					{	
						$sub_folder = Folder::get_instance($result_array[$key][folder_id]);				
						if ($sub_folder->is_read_access() == true)
						{
							$paramquery = $_GET;
							$paramquery[folder_id] = $result_array[$key][folder_id];
							unset($paramquery[nextpage]);
							unset($paramquery[vfolder_id]);
							$params = http_build_query($paramquery,'','&#38;');
							
							$result_array[$key][symbol][content] = "<img src='images/icons/folder.png' alt='' style='border:0;' />";
							$result_array[$key][symbol][link] = $params;
							
							$tmp_name = $result_array[$key][name];
							unset($result_array[$key][name]);
							$result_array[$key][name][content] = $tmp_name;
							$result_array[$key][name][link] = $params;
						}
						else
						{
							$result_array[$key][symbol] = "<img src='core/images/denied_overlay.php?image=images/icons/folder.png' alt='' border='0' />";
						}
						
						$result_array[$key][type] = "Folder";
						$result_array[$key][permission] = $folder->get_permission_string();
					}
					elseif ($result_array[$key][virtual_folder_id])
					{
						$paramquery = $_GET;
						$paramquery[vfolder_id] = $result_array[$key][virtual_folder_id];
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery,'','&#38;');
						
						$result_array[$key][symbol][content] = "<img src='images/icons/virtual_folder.png' alt='' style='border:0;' />";
						$result_array[$key][symbol][link] = $params;
								
						$tmp_name = $result_array[$key][name];
						unset($result_array[$key][name]);
						$result_array[$key][name][content] = $tmp_name;
						$result_array[$key][name][link] = $params;
						
						$result_array[$key][type] = "Virtual Folder";
						$result_array[$key][permission] = "automatic";
					}
				}
				
			}else{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}

			
			
			$template = new Template("languages/en-gb/template/data/data_browser.html");
	
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
				
				if ($folder->is_flag_change_permission() or 
					$folder->is_flag_add_folder() or 
					$folder->is_flag_cmd_folder() or 
					$folder->is_flag_rename_folder())
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
			$paramquery[action] = "delete_stack";
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
			
			$template->set_var("table", $list->get_list($result_array, $_GET[page]));	
			
			$template->output();
		}
		catch (DataException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 1);
			$error_io->display_error();
		}
		catch (ProjectException $e)
		{
			$error_io = new Error_IO($e, 200, 40, 1);
			$error_io->display_error();
		}
		catch (SampleException $e)
		{
			$error_io = new Error_IO($e, 250, 40, 1);
			$error_io->display_error();
		}
		catch (DataSecurityException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 2);
			$error_io->display_error();
		}
		catch (ProjectSecurityException $e)
		{
			$error_io = new Error_IO($e, 200, 40, 2);
			$error_io->display_error();
		}
		catch (SampleSecurityException $e)
		{
			$error_io = new Error_IO($e, 250, 40, 2);
			$error_io->display_error();
		}
	}

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
				
					$template = new Template("languages/en-gb/template/data/data_image_browser_multi.html");
					
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
								
								$file = new File($image_browser_array[$current_address]);
						
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
					$exception = new Exception("", 4);
					$error_io = new Error_IO($exception, 20, 40, 1);
					$error_io->display_error();
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 20, 40, 3);
			$error_io->display_error();
		}
	}

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
						$file = new File($image_browser_array[$page]);
				
						$template = new Template("languages/en-gb/template/data/data_image_browser_detail.html");
						
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
								$file_version = new File($image_browser_array[$page]);
								$file_version->open_internal_revision($value);
								
								$result[$counter][version] = $file_version->get_internal_revision();
								$result[$counter][text] = "Version ".$file_version->get_version()." - ".$file_version->get_datetime();
								$counter++;
							}
							$template->set_array("version_option",$result);
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
						
						$template->set_array("get",$result);
						
						
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
					$exception = new Exception("", 4);
					$error_io = new Error_IO($exception, 20, 40, 1);
					$error_io->display_error();
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 20, 40, 3);
			$error_io->display_error();
		}
	}

	public static function permission()
	{
		global $common, $user;
		
		try
		{
			if ($_GET[file_id] xor $_GET[value_id])
			{
				if ($_GET[file_id])
				{
					$id = $_GET[file_id];
					$object = new File($id);
					$type = "file";
					$title = $object->get_name();
				}
				
				if ($_GET[value_id])
				{
					$id = $_GET[value_id];
					$object = new Value($id);
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
					throw new IdMissingException("", 0);
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
					$template = new Template("languages/en-gb/template/data/data_permission.html");
					
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
						
					}
					else
					{
						$disable_automatic = true;
					}
					
					if ($object->can_set_data_entity())
					{
						
					}
					else
					{
						$disable_project = true;
					}
					
					if ($object->can_set_control())
					{
						
					}
					else
					{
						$disable_control = true;
					}
					
					if ($object->can_set_remain())
					{
						
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
						$common->step_proceed($params, "Permission: ".$title."", "Changes saved succesful" ,null);
					}
					else
					{
						$common->step_proceed($params, "Permission: ".$title."", "Operation failed" ,null);
					}
				}
			}
			else
			{
				switch ($type):
				
					case "folder":
						$exception = new Exception("", 1);
					break;
					
					case "file":
						$exception = new Exception("", 2);
					break;
					
					case "value":
						$exception = new Exception("", 3);
					break;
				
				endswitch;
				
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		catch (IdMissingException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 3);
			$error_io->display_error();
		}
	}

	public static function change_owner()
	{
		global $common;
		
		try
		{
			if ($_GET[file_id] xor $_GET[value_id])
			{
				if ($_GET[file_id])
				{
					$id = $_GET[file_id];
					$object = new File($id);
					$type = "file";
					$title = $object->get_name();
				}
				if ($_GET[value_id])
				{
					$id = $_GET[value_id];
					$object = new Value($id);
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
					throw new IdMissingException("", 0);
				}
			}
			
			if ($object->is_control_access() == true)
			{
				$data_permission = new DataPermission($type, $id);
				
				if (!$_GET[nextpage])
				{
					$template = new Template("languages/en-gb/template/data/data_change_owner.html");
					
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
					
					$template->set_array("option",$result);
					
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
						$common->step_proceed($params, "Permission: ".$title."", "Changes saved succesful" ,null);
					}
					else
					{
						$common->step_proceed($params, "Permission: ".$title."", "Operation failed" ,null);
					}
				}
			}
			else
			{
				switch ($type):
				
					case "folder":
						$exception = new Exception("", 1);
					break;
					
					case "file":
						$exception = new Exception("", 2);
					break;
					
					case "value":
						$exception = new Exception("", 3);
					break;
				
				endswitch;
				
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		catch (IdMissingException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function change_group()
	{
		global $common;
		
		try
		{
			if ($_GET[file_id] xor $_GET[value_id])
			{
				if ($_GET[file_id])
				{
					$id = $_GET[file_id];
					$object = new File($id);
					$type = "file";
					$title = $object->get_name();
				}
				if ($_GET[value_id])
				{
					$id = $_GET[value_id];
					$object = new Value($id);
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
					throw new IdMissingException("", 0);
				}
			}
			
			if ($object->is_control_access() == true)
			{
				$data_permission = new DataPermission($type, $id);
				
				if (!$_GET[nextpage])
				{
					$template = new Template("languages/en-gb/template/data/data_change_group.html");
					
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
					
					$template->set_array("option",$result);
					
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
						$common->step_proceed($params, "Permission: ".$title."", "Changes saved succesful" ,null);
					}
					else
					{
						$common->step_proceed($params, "Permission: ".$title."", "Operation failed" ,null);
					}
				}
			}
			else
			{
				switch ($type):
				
					case "folder":
						$exception = new Exception("", 1);
					break;
					
					case "file":
						$exception = new Exception("", 2);
					break;
					
					case "value":
						$exception = new Exception("", 3);
					break;
				
				endswitch;
				
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		catch (IdMissingException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 3);
			$error_io->display_error();
		}
	}

	public static function method_handler()
	{	
		try
		{
			if ($_GET[file_id])
			{
				if (File::exist_file($_GET[file_id]) == false) {
					throw new FileNotFoundException("",2);
				}
			}
			
			if ($_GET[value_id])
			{
				if (Value::exist_value($_GET[value_id]) == false)
				{
					throw new ValueNotFoundException("",3);
				}
			}
			
			if ($_GET[folder_id])
			{
				$folder = Folder::get_instance($_GET[folder_id]);
				if ($folder->exist_folder() == false)
				{
					throw new FolderNotFoundException("",1);
				}
			}
			
			switch($_GET[action]):
				case("permission"):
					self::permission();
				break;
				
				case("chown"):
					self::change_owner();
				break;
				
				case("chgroup"):
					self::change_group();
				break;
	
				case("image_browser_detail"):
					self::image_browser_detail();
				break;
				
				case("image_browser_multi"):
					self::image_browser_multi();
				break;
	
				
				case("value_detail"):
					require_once("value.io.php");
					ValueIO::detail();
				break;
				
				case("value_history"):
					require_once("value.io.php");
					ValueIO::history();
				break;
					
				case("value_delete_version"):
					require_once("value.io.php");
					ValueIO::delete_version();
				break;

				
				case("file_add"):
					require_once("file.io.php");
					FileIO::upload();
				break;
				
				case("file_update"):
				case("file_update_minor"):
					require_once("file.io.php");
					FileIO::update();
				break;
	
				case("file_detail"):
					require_once("file.io.php");
					FileIO::detail();
				break;
				
				case("file_history"):
					require_once("file.io.php");
					FileIO::history();
				break;
				
				case("file_delete"):
					require_once("file.io.php");
					FileIO::delete();
				break;
				
				case("file_delete_version"):
					require_once("file.io.php");
					FileIO::delete_version();
				break;
				
				
				case("folder_add"):
					require_once("folder.io.php");
					FolderIO::add();	
				break;
				
				case("folder_delete"):
					require_once("folder.io.php");
					FolderIO::delete();
				break;
				
				case("folder_move"):
					require_once("folder.io.php");
					FolderIO::move();
				break;
	
				case("folder_administration"):
					require_once("folder.io.php");
					FolderIO::folder_administration();	
				break;
				
				// Search
				/**
				 * @todo errors, exceptions
				 */
				case("search"):
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("search", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]();
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				break;
				
				default:
					self::browser();
				break;
				
			endswitch;	
		}
		catch (FileNotFoundException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 1);
			$error_io->display_error();
		}
		catch (ValueNotFoundException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 1);
			$error_io->display_error();
		}
		catch (FolderNotFoundException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 1);
			$error_io->display_error();
		}
		
	}
	
}

?>
