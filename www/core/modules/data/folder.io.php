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
 * Folder IO Class
 * @package data
 */
class FolderIO
{
	public static function add()
	{
		global $user;
		
		if ($_GET[folder_id])
		{
			$folder = Folder::get_instance($_GET[folder_id]);
			
			if ($folder->is_flag_add_folder())
			{
				if ($_GET[nextpage] == 1)
				{
					if ($_POST[name])
					{
						if ($folder->exist_subfolder_name($_POST[name]) == false)
						{
							$page_1_passed = true;
						}
						else
						{
							$page_1_passed = false;
							$error = "This folder already exists.";
						}	
					}
					else
					{
						$page_1_passed = false;
						$error = "You must enter a name.";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "";
				}
				
				if ($page_1_passed == false)
				{
					$template = new Template("languages/en-gb/template/data/folder_add.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->set_var("error",$error);
					
					if ($_POST[name])
					{
						$template->set_var("name", $_POST[name]);
					}
					else
					{
						$template->set_var("name","");
					}
					$template->output();
				}
				else
				{
					$new_folder = Folder::get_instance(null);
					
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[action]);
					$paramquery[nav] = "data";
					$params = http_build_query($paramquery);
							
					if ($new_folder->create($_POST[name], $_GET[folder_id], null, $user->get_user_id(), null))
					{
						if (!$user->is_admin())
						{
							$new_folder->set_flag(64);
						}
						Common_IO::step_proceed($params, "Add Folder", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Add Folder", "Operation Failed" ,null);	
					}
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
	
	public static function delete()
	{
		global $user;
		
		if ($_GET[folder_id])
		{
			$folder = Folder::get_instance($_GET[folder_id]);
		
			if ($folder->is_flag_cmd_folder())
			{
				if ($_GET[nextpage] == 1)
				{
					$page_1_passed = true;
				}
				else
				{
					$page_1_passed = false;
				}
				
				if ($page_1_passed == false)
				{
					$template = new Template("languages/en-gb/template/data/folder_delete.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->output();
				}
				else
				{		
					$parent_folder_data_entity_id = $folder->get_parent_folder();
					
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[action]);
					$paramquery[nav] = "data";
					$paramquery[folder_id] = Folder::get_folder_id_by_data_entity_id($parent_folder_data_entity_id);
					$params = http_build_query($paramquery);
							
					if ($folder->delete(true, true))
					{
						Common_IO::step_proceed($params, "Delete Folder", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Delete Folder", "Operation Failed" ,null);	
					}
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
	
	public static function move()
	{		
		if ($_GET[folder_id])
		{
			$folder = Folder::get_instance($_GET[folder_id]);
		
			if ($folder->is_flag_cmd_folder())
			{
				if ($_GET[nextpage] == 1)
				{
					$page_1_passed = true;
				}
				else
				{
					$page_1_passed = false;
				}
				
				if ($page_1_passed == false)
				{
					$template = new Template("languages/en-gb/template/data/folder_move.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					$paramquery[nav] = "data";
					unset($paramquery[nextpage]);
					unset($paramquery[action]);
					$params = http_build_query($paramquery);
							
					if ($folder->move_folder($_POST[folder_id], false))
					{
						Common_IO::step_proceed($params, "Move Folder", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Move Folder", "Operation Failed" ,null);	
					}
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
	
	/**
	 * @todo Rename folder is currently not supported.
	 */
	public static function rename()
	{
		
	}
	
	public static function folder_administration()
	{
		global $user;
		
		if ($_GET[folder_id])
		{
			$folder = Folder::get_instance($_GET[folder_id]);
		
			if ($folder->is_flag_change_permission() or 
				$folder->is_flag_add_folder() or 
				$folder->is_flag_cmd_folder() or 
				$folder->is_flag_rename_folder())
			{
				$template = new Template("languages/en-gb/template/data/folder_administration.html");
				
				if ($folder->is_flag_change_permission())
				{
					$template->set_var("change_permission", true);
				}
				else
				{
					$template->set_var("change_permission", false);
				}
				
				if ($folder->is_flag_add_folder())
				{
					$template->set_var("add", true);
				}
				else
				{
					$template->set_var("add", false);
				}
				
				if ($folder->is_flag_cmd_folder())
				{
					$template->set_var("cmd", true);
				}
				else
				{
					$template->set_var("cmd", false);
				}
				
				if ($folder->is_flag_rename_folder())
				{
					$template->set_var("rename", true);
				}
				else
				{
					$template->set_var("rename", false);
				}
				
				$paramquery = $_GET;
				$paramquery[action] = "folder_add";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
								
				$template->set_var("add_folder_params", $params);
		
		
				$paramquery = $_GET;
				$paramquery[action] = "folder_delete";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
								
				$template->set_var("delete_folder_params", $params);
				
				
				$paramquery = $_GET;
				$paramquery[action] = "folder_move";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
								
				$template->set_var("move_folder_params", $params);
		
		
				$paramquery = $_GET;
				$paramquery[action] = "permission";
				unset($paramquery[value_id]);
				unset($paramquery[file_id]);
				$params = http_build_query($paramquery,'','&#38;');	
				$template->set_var("change_permission_params",$params);
				
				$template->output();
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

}

?>
