<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Project Admin AJAX IO Class
 * @package project
 */
class ProjectAdminAjax
{
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_project_permissions($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		$argument_array = json_decode($json_argument_array);
		
		$project_id = $argument_array[0][1];
		
		if (is_numeric($project_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Project_Wrapper::list_project_permissions($project_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));

			if (is_array($list_array) and count($list_array) >= 1)
			{
				$project = new Project($project_id);
				$project_security = new ProjectSecurity($project_id);
				
				foreach($list_array as $key => $value)
				{					
					$list_array[$key][symbol] = "<img src='images/icons/permissions.png' alt='N' border='0' />";
					
					$project_permission = ProjectPermission::get_instance($list_array[$key][id]);
			
					$user_id = $project_permission->get_user_id();
					$group_id = $project_permission->get_group_id();
					$organ_unit_id = $project_permission->get_organisation_unit_id();
					
					if ($user_id)
					{
						$permission_user = new User($user_id);
						$list_array[$key][name] = $permission_user->get_username();
						$list_array[$key][type] = "user";
						$list_array[$key][fullname] = $permission_user->get_full_name(false);
					}
					elseif($group_id)
					{
						$list_array[$key][type] = "group";
					}
					else
					{
						$list_array[$key][type] = "organisation unit";
					}
			
					if ($project_permission->get_owner_id() == null)
					{
						$list_array[$key][createdby] = "system";
					}
					else
					{
						$created_by = new User($project_permission->get_owner_id());
						$list_array[$key][createdby] = $created_by->get_username();
					}
					
					$permission_array = $project_permission->get_permission_array();
						
					if ($project_security->is_access(2, false) or $project->get_owner_id() == $user->get_user_id())
					{				
						if ($permission_array[read] == true)
						{
							$list_array[$key][re] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key][re] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
					}
					else
					{
						if ($permission_array[read] == true)
						{
							$list_array[$key][re] = "<img src='images/icons/permission_ok_active_na.png' alt='' />";
						}
						else
						{
							$list_array[$key][re] = "<img src='images/icons/permission_denied_active_na.png' alt='' />";
						}
					}
					
					if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
					{
						if ($permission_array[set_readable] == true)
						{
							$list_array[$key][sr] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key][sr] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
					}
					else
					{
						if ($permission_array[set_readable] == true)
						{
							$list_array[$key][sr] = "<img src='images/icons/permission_ok_active_na.png' alt='' />";
						}
						else
						{
							$list_array[$key][sr] = "<img src='images/icons/permission_denied_active_na.png' alt='' />";
						}
					}
					
					if ($project_security->is_access(4, false) or $project->get_owner_id() == $user->get_user_id())
					{
						if ($permission_array[write] == true)
						{
							$list_array[$key][wr] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key][wr] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
					}
					else
					{
						if ($permission_array[write] == true)
						{
							$list_array[$key][wr] = "<img src='images/icons/permission_ok_active_na.png' alt='' />";
						}
						else
						{
							$list_array[$key][wr] = "<img src='images/icons/permission_denied_active_na.png' alt='' />";
						}
					}
					
					if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
					{
						if ($permission_array[set_writeable] == true)
						{
							$list_array[$key][sw] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key][sw] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
					}
					else
					{
						if ($permission_array[set_writeable] == true)
						{
							$list_array[$key][sw] = "<img src='images/icons/permission_ok_active_na.png' alt='' />";
						}
						else
						{
							$list_array[$key][sw] = "<img src='images/icons/permission_denied_active_na.png' alt='' />";
						}
					}
					
					if ($project_security->is_access(7, false))
					{
						if ($permission_array[reactivate] == true)
						{
							$list_array[$key][ra] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key][ra] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
						
						if ($permission_array[delete] == true)
						{
							$list_array[$key][de] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key][de] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
						
						if ($permission_array[set_permissions] == true)
						{
							$list_array[$key][sp] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key][sp] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
					}
					else
					{
						if ($permission_array[reactivate] == true)
						{
							$list_array[$key][ra] = "<img src='images/icons/permission_ok_active_na.png' alt='' />";
						}
						else
						{
							$list_array[$key][ra] = "<img src='images/icons/permission_denied_active_na.png' alt='' />";
						}
						
						if ($permission_array[delete] == true)
						{
							$list_array[$key][de] = "<img src='images/icons/permission_ok_active_na.png' alt='' />";
						}
						else
						{
							$list_array[$key][de] = "<img src='images/icons/permission_denied_active_na.png' alt='' />";
						}
						
						if ($permission_array[set_permissions] == true)
						{
							$list_array[$key][sp] = "<img src='images/icons/permission_ok_active_na.png' alt='' />";
						}
						else
						{
							$list_array[$key][sp] = "<img src='images/icons/permission_denied_active_na.png' alt='' />";
						}
					}
					
					$edit_paramquery = array();
					$edit_paramquery[username] = $_GET[username];
					$edit_paramquery[session_id] = $_GET[session_id];
					$edit_paramquery[nav] = "project";
					$edit_paramquery[run] = "admin_permission_edit";
					$edit_paramquery[project_id] = $project_id;
					$edit_paramquery[id] = $list_array[$key][id];
					$edit_params = http_build_query($edit_paramquery, '', '&#38;');
						
					$list_array[$key][e][link] = $edit_params;
					$list_array[$key][e][content] = "E";
					
					if ($project_permission->get_intention() == null)
					{
						$delete_paramquery = array();
						$delete_paramquery[username] = $_GET[username];
						$delete_paramquery[session_id] = $_GET[session_id];
						$delete_paramquery[nav] = "project";
						$delete_paramquery[run] = "admin_permission_delete";
						$delete_paramquery[project_id] = $project_id;
						$delete_paramquery[id] = $list_array[$key][id];
						$delete_params = http_build_query($delete_paramquery, '', '&#38;');
												
						$list_array[$key][d][link] = $delete_params;
						$list_array[$key][d][content] = "D";
					}
					else
					{
						$list_array[$key][d][content] = "";
					}
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No permissions found!</span>");
			}

			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_project_permissions($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$project_id = $argument_array[0][1];
		
		if (is_numeric($project_id))
		{
			return Project_Wrapper::count_project_permissions($project_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $get_array
	 * @return string
	 * @throws ProjectSecurityAccessDeniedException
	 * @throws ProjectIDMissingException
	 */
	public static function get_project_admin_menu($get_array)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET[project_id])
		{
			$project = new Project($_GET[project_id]);
			$project_security = new ProjectSecurity($_GET[project_id]);
			$project_owner = new User($project->get_owner_id());
			$organisation_unit_id = $project->get_organisation_unit_id();
			$parent_project_id = $project->get_project_toid();
			
			if ($user->get_user_id() == $project->get_owner_id() or
				$project_security->is_access(2, false) == true or
				$project_security->is_access(3, false) == true or
				$project_security->is_access(4, false) == true or
				$project_security->is_access(5, false) == true or
				$project_security->is_access(6, false) == true or
				$project_security->is_access(7, false) == true)
			{
				if ($organisation_unit_id)
				{
					$organisation_unit = new OrganisationUnit($organisation_unit_id);
					$parent = $organisation_unit->get_name();
					$parent_type = "Organisation Unit";
				}
				else
				{
					$parent_project = new Project($parent_project_id);
					$parent = $parent_project->get_name();
					$parent_type = "Project";
				}
			
				$template = new HTMLTemplate("project/ajax/admin/menu.html");
				
				$template->set_var("name", $project->get_name());
				$template->set_var("owner", $project_owner->get_full_name(false));
				$template->set_var("parent", $parent);
				$template->set_var("parent_type", $parent_type);
				
				if ($user->is_admin())
				{
					$template->set_var("admin", true);
				}
				else
				{
					$template->set_var("admin", false);
				}
				
				if ($project_security->is_access(7, false) == true or
					$project->get_owner_id() == $user->get_user_id())
				{
					$template->set_var("owner_permission", true);
				}
				else
				{
					$template->set_var("owner_permission", false);
				}
				
				if ($project_security->is_access(6, false) == true)
				{
					$template->set_var("delete", true);
				}
				else
				{
					$template->set_var("delete", false);
				}
				
				if ($project_security->is_access(3, false) == true)
				{
					$template->set_var("write", true);
				}
				else
				{
					$template->set_var("write", false);
				}
				
				if ($project->get_current_status_id() == 0)
				{
					$template->set_var("project_canceled", true);
				}
				else
				{
					$template->set_var("project_canceled", false);
				}
				
				if ($project->get_deleted() == true)
				{
					$template->set_var("project_deleted", true);
				}
				else
				{
					$template->set_var("project_deleted", false);
				}
				
				if ($project->get_quota() == 0)
				{
					$template->set_var("quota", "unlimited");
				}
				else
				{
					$template->set_var("quota", Convert::convert_byte_1024($project->get_quota()));
				}
			
				$permission_paramquery = $_GET;
				$permission_paramquery[run] = "admin_permission";
				unset($permission_paramquery[nextpage]);
				unset($permission_paramquery[sure]);
				$permission_params = http_build_query($permission_paramquery,'','&#38;');
				
				$template->set_var("permission_params", $permission_params);
				
				
				$rename_paramquery = $_GET;
				$rename_paramquery[run] = "admin_rename";
				unset($rename_paramquery[nextpage]);
				unset($rename_paramquery[sure]);
				$rename_params = http_build_query($rename_paramquery,'','&#38;');
				
				$template->set_var("rename_params", $rename_params);
				
				
				$chown_paramquery = $_GET;
				$chown_paramquery[run] = "admin_chown";
				unset($chown_paramquery[nextpage]);
				unset($chown_paramquery[sure]);
				$chown_params = http_build_query($chown_paramquery,'','&#38;');
				
				$template->set_var("chown_params", $chown_params);
				
				
				$move_paramquery = $_GET;
				$move_paramquery[run] = "admin_move";
				unset($move_paramquery[nextpage]);
				unset($move_paramquery[sure]);
				$move_params = http_build_query($move_paramquery,'','&#38;');
				
				$template->set_var("move_params", $move_params);
				
				
				$chquota_paramquery = $_GET;
				$chquota_paramquery[run] = "admin_quota";
				unset($chquota_paramquery[nextpage]);
				unset($chquota_paramquery[sure]);
				$chquota_params = http_build_query($chquota_paramquery,'','&#38;');
				
				$template->set_var("chquota_params", $chquota_params);
				
				
				return $template->get_string();
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @param string $get_array
	 * @return string
	 */
	public static function delete($get_array)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			$project = new Project($_GET['project_id']);
			
			if ($project->get_deleted() == true or $user->is_admin() == false)
			{
				$project_deleted = "true";
			}
			else
			{
				$project_deleted = "false";		
			}
			
			$template = new HTMLTemplate("project/admin/delete_window.html");
			
			$array['continue_caption'] = "Yes";
			$array['cancel_caption'] = "No";
			$array['content_caption'] = "Delete Project";
			$array['height'] = 200;
			$array['width'] = 400;
			$array['content'] = $template->get_string();
			$array['container'] = "#ProjectDeleteWindow";
			
			$continue_handler_template = new JSTemplate("project/admin/js/delete_continue_handler.js");
			$continue_handler_template->set_var("username", $_GET['username']);
			$continue_handler_template->set_var("session_id", $_GET['session_id']);
			$continue_handler_template->set_var("get_array", $get_array);
			$continue_handler_template->set_var("project_deleted", $project_deleted);
			
			$array['continue_handler'] = $continue_handler_template->get_string();
			
			return json_encode($array);
		}
	}
	
	/**
	 * @param string $get_array
	 * @return string
	 * @throws ProjectSecurityAccessDeniedException;
	 */
	public static function delete_handler($get_array)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			
			$project = new Project($_GET['project_id']);

			if ($project->get_deleted() == true)
			{
				if ($user->is_admin() == true)
				{
					$project->delete();
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			}
			else
			{
				$project_security = new ProjectSecurity($_GET[project_id]);
				
				if ($project_security->is_access(6, false) == true)
				{
					$project->mark_as_deleted();		
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}		
			}
		}
	}
	
	/**
	 * @param string $get_array
	 * @return string
	 */
	public static function restore($get_array)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			$project = new Project($_GET['project_id']);
			
			if ($project->get_deleted() == true)
			{
				$template = new HTMLTemplate("project/admin/restore_window.html");
				
				$array['continue_caption'] = "Yes";
				$array['cancel_caption'] = "No";
				$array['content_caption'] = "Restore Project";
				$array['height'] = 200;
				$array['width'] = 400;
				$array['content'] = $template->get_string();
				$array['container'] = "#ProjectRestoreWindow";
				
				$continue_handler_template = new JSTemplate("project/admin/js/restore_continue_handler.js");
				$continue_handler_template->set_var("session_id", $_GET['session_id']);
				$continue_handler_template->set_var("get_array", $get_array);
				
				$array['continue_handler'] = $continue_handler_template->get_string();
				
				return json_encode($array);
			}
		}
	}
	
	/**
	 * @param string $get_array
	 * @return string
	 * @throws ProjectException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function restore_handler($get_array)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			$project = new Project($_GET['project_id']);

			if ($user->is_admin() == true)
			{
				if ($project->get_deleted() == true)
				{
					if ($project->mark_as_undeleted() == false)
					{
						throw new ProjectException();
					}
				}
				else
				{
					throw new ProjectException();
				}
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
	}
	
	/**
	 * @param string $get_array
	 * @return string
	 */
	public static function cancel($get_array)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			$project = new Project($_GET['project_id']);
			
			if ($project->get_current_status_id() == 0)
			{
				$template = new HTMLTemplate("project/admin/reactivate_window.html");
				$array['content_caption'] = "Reactivate Project";
				$array['height'] = 200;
				$array['width'] = 400;
				$process_name = "reactivating";
			}
			else
			{
				$template = new HTMLTemplate("project/admin/cancel_window.html");
				$array['content_caption'] = "Cancel Project";
				$array['height'] = 430;
				$array['width'] = 400;
				$process_name = "canceling";
			}

			$array['continue_caption'] = "Yes";
			$array['cancel_caption'] = "No";
			$array['content'] = $template->get_string();
			$array['container'] = "#ProjectCancelWindow";
			
			$continue_handler_template = new JSTemplate("project/admin/js/cancel_continue_handler.js");
			$continue_handler_template->set_var("session_id", $_GET['session_id']);
			$continue_handler_template->set_var("get_array", $get_array);
			
			$array['continue_handler'] = $continue_handler_template->get_string();
			
			return json_encode($array);
		}
	}
	
	/**
	 * @param string $get_array
	 * @param string $comment
	 * @return stirng
	 * @throws ProjectException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function cancel_handler($get_array, $comment)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['project_id'])
		{
			$project = new Project($_GET['project_id']);

			if ($project->get_current_status_id() == 0)
			{
				if ($user->is_admin() == true)
				{
					if ($project->mark_as_reactivated() == false)
					{
						throw new ProjectException();
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			}
			else
			{
				$project_security = new ProjectSecurity($_GET[project_id]);
				
				if ($project_security->is_access(3, false) == true)
				{
					if ($project->mark_as_canceled($comment) == false)
					{
						throw new ProjectException();
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			}
		}
	}
}
?>