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
}
?>