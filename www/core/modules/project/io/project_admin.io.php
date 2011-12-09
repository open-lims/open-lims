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
 * Project Admin IO Class
 * @package project
 */
class ProjectAdminIO
{
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function permission()
	{
		global $project_security, $user;
		
		if ($_GET[project_id])
		{
			$project_id = $_GET[project_id];
			$project = new Project($project_id);
			$project_permission_array = ProjectPermission::list_entries_by_project_id($project_id);
			
			if ($user->get_user_id() == $project->get_owner_id() or
				$project_security->is_access(2, false) == true or
				$project_security->is_access(4, false) == true or
				$project_security->is_access(7, false) == true)
			{		
				$argument_array = array();
				$argument_array[0][0] = "project_id";
				$argument_array[0][1] = $_GET[project_id];
				
				$list = new List_IO("ProjectPermission", "ajax.php?nav=project", "list_project_permissions", "count_project_permissions", $argument_array, "ProjectAjaxMyProjects");
				
				$list->add_column("", "symbol", false, "16px");
				$list->add_column("User/Group","name",true,null);
				$list->add_column("Type","type",true,null);
				$list->add_column("Full Name","fullname",true,null);
				$list->add_column("Created by","createdby",true,null);
				$list->add_column("RE","re",false,"25px");
				$list->add_column("SR","sr",false,"25px");
				$list->add_column("WR","wr",false,"25px");
				$list->add_column("SW","sw",false,"25px");
				$list->add_column("RA","ra",false,"25px");
				$list->add_column("DE","de",false,"25px");
				$list->add_column("SP","sp",false,"25px");
				$list->add_column("E","e",false,"16px");
				$list->add_column("D","d",false,"16px");
				
				$template = new Template("template/projects/admin/permission.html");
				
				$add_user_paramquery = $_GET;
				$add_user_paramquery[run] = "admin_permission_add_user";
				$add_user_params = http_build_query($add_user_paramquery,'','&#38;');
				
				$template->set_var("add_user_params", $add_user_params);
				
				$add_group_paramquery = $_GET;
				$add_group_paramquery[run] = "admin_permission_add_group";
				$add_group_params = http_build_query($add_group_paramquery,'','&#38;');
				
				$template->set_var("add_group_params", $add_group_params);
				
				$add_ou_paramquery = $_GET;
				$add_ou_paramquery[run] = "admin_permission_add_ou";
				$add_ou_params = http_build_query($add_ou_paramquery,'','&#38;');
				
				$template->set_var("add_ou_params", $add_ou_params);
				
				$template->set_var("list", $list->get_list());
		
				$template->output();
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function permission_add_user()
	{
		global $project_security, $user;

		if ($_GET[project_id])
		{
			$project_id = $_GET[project_id];		
			$project = new Project($project_id);
			
			if ($user->get_user_id() == $project->get_owner_id() or
				$project_security->is_access(2, false) == true or
				$project_security->is_access(4, false) == true or
				$project_security->is_access(7, false) == true)
			{
			
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[user]))
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must select an user.";
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
					$template = new Template("template/projects/admin/permission_add_user.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->set_var("error",$error);
					
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
					
					$template->output();
				}
				else
				{
					if ($_GET[nextpage] == 2)
					{
						if ($_POST[re] == "1" or 
							$_POST[sr] == "1" or 
							$_POST[wr] == "1" or 
							$_POST[sw] == "1" or 
							$_POST[ra] == "1" or 
							$_POST[de] == "1" or 
							$_POST[sp] == "1")
						{
							$page_2_passed = true;
						}
						else
						{
							$page_2_passed = false;
							$error = "You must select min. one permission.";
						}
					}
					elseif($_GET[nextpage] > 2)
					{
						$page_2_passed = true;
					}
					else
					{
						$page_2_passed = false;
						$error = "";
					}
					
					if ($page_2_passed == false)
					{
						$template = new Template("template/projects/admin/permission_add_edit.html");
						
						$template->set_var("add_edit","Add");
						
						$new_user = new User($_POST[user]);
						
						$paramquery = $_GET;
						$paramquery[nextpage] = "2";
						$params = http_build_query($paramquery,'','&#38;');
						
						$template->set_var("params",$params);
						
						$template->set_var("name",$new_user->get_username());
						$template->set_var("type","user");
						
						$template->set_var("error",$error);
						
						if ($project_security->is_access(2, false) or $project->get_owner_id() == $user->get_user_id())
						{				
							$template->set_var("disabled_re", "");
						}
						else
						{
							$template->set_var("disabled_re", "disabled");	
						}
						
						if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_sr", "");
						}
						else
						{
							$template->set_var("disabled_sr", "disabled");		
						}
						
						if ($project_security->is_access(4, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_wr", "");
						}
						else
						{
							$template->set_var("disabled_wr", "disabled");		
						}
						
						if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_sw", "");
						}
						else
						{
							$template->set_var("disabled_sw", "disabled");		
						}
						
						if ($project_security->is_access(7, false))
						{
							$template->set_var("disabled_ra", "");	
							$template->set_var("disabled_de", "");	
							$template->set_var("disabled_sp", "");
						}
						else
						{
							$template->set_var("disabled_ra", "disabled");	
							$template->set_var("disabled_de", "disabled");	
							$template->set_var("disabled_sp", "disabled");						
						}
						
						$template->set_var("checked_re", "");
						$template->set_var("checked_sr", "");
						$template->set_var("checked_wr", "");
						$template->set_var("checked_sw", "");
						$template->set_var("checked_ra", "");	
						$template->set_var("checked_de", "");	
						$template->set_var("checked_sp", "");
						
						$template->set_var("user",$_POST[user]);
						$template->set_var("group","");
						$template->set_var("ou","");
						
						$template->output();
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[id]);
						$paramquery[run] = "admin_permission";
						$params = http_build_query($paramquery);
						
						$project_permission = new ProjectPermissionUser(null);
						
						$new_permssion = 0;
						
						if ($_POST[re] == "1")
						{
							$new_permission = $new_permission + 1;
						}
						if ($_POST[sr] == "1")
						{
							$new_permission = $new_permission + 2;
						}
						if ($_POST[wr] == "1")
						{
							$new_permission = $new_permission + 4;
						}
						if ($_POST[sw] == "1")
						{
							$new_permission = $new_permission + 8;
						}
						if ($_POST[ra] == "1")
						{
							$new_permission = $new_permission + 16;
						}
						if ($_POST[de] == "1")
						{
							$new_permission = $new_permission + 32;
						}
						if ($_POST[sp] == "1")
						{
							$new_permission = $new_permission + 64;
						}
						
						if ($project_permission->create($_POST[user], $project_id, $new_permission, $user->get_user_id(), null))
						{
							Common_IO::step_proceed($params, "Add Permission", "Operation Successful", null);
						}
						else
						{
							Common_IO::step_proceed($params, "Add Permission", "Operation Failed" ,null);	
						}
					}
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function permission_add_group()
	{
		global $project_security, $user;

		if ($_GET[project_id])
		{
			$project_id = $_GET[project_id];		

			$project = new Project($project_id);
			
			if ($user->get_user_id() == $project->get_owner_id() or
				$project_security->is_access(2, false) == true or
				$project_security->is_access(4, false) == true or
				$project_security->is_access(7, false) == true)
			{
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[group]))
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must select a group.";
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
					$template = new Template("template/projects/admin/permission_add_group.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->set_var("error",$error);
					
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
					
					$template->output();
				}
				else
				{
					if ($_GET[nextpage] == 2)
					{
						if ($_POST[re] == "1" or 
							$_POST[sr] == "1" or 
							$_POST[wr] == "1" or 
							$_POST[sw] == "1" or 
							$_POST[ra] == "1" or 
							$_POST[de] == "1" or 
							$_POST[sp] == "1")
						{
							$page_2_passed = true;
						}
						else
						{
							$page_2_passed = false;
							$error = "You must select min. one permission.";
						}
					}
					elseif($_GET[nextpage] > 2)
					{
						$page_2_passed = true;
					}
					else
					{
						$page_2_passed = false;
						$error = "";
					}
					
					if ($page_2_passed == false)
					{
						$template = new Template("template/projects/admin/permission_add_edit.html");
						
						$template->set_var("add_edit","Add");
						
						$new_group = new Group($_POST[group]);
						
						$paramquery = $_GET;
						$paramquery[nextpage] = "2";
						$params = http_build_query($paramquery,'','&#38;');
						
						$template->set_var("params",$params);
						
						$template->set_var("name",$new_group->get_name());
						$template->set_var("type","user");
						
						$template->set_var("error",$error);
						
						if ($project_security->is_access(2, false) or $project->get_owner_id() == $user->get_user_id())
						{				
							$template->set_var("disabled_re", "");
						}
						else
						{
							$template->set_var("disabled_re", "disabled");	
						}
						
						if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_sr", "");
						}
						else
						{
							$template->set_var("disabled_sr", "disabled");		
						}
						
						if ($project_security->is_access(4, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_wr", "");
						}
						else
						{
							$template->set_var("disabled_wr", "disabled");		
						}
						
						if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_sw", "");
						}
						else
						{
							$template->set_var("disabled_sw", "disabled");		
						}
						
						if ($project_security->is_access(7, false))
						{
							$template->set_var("disabled_ra", "");	
							$template->set_var("disabled_de", "");	
							$template->set_var("disabled_sp", "");
						}
						else
						{
							$template->set_var("disabled_ra", "disabled");	
							$template->set_var("disabled_de", "disabled");	
							$template->set_var("disabled_sp", "disabled");						
						}
						
						$template->set_var("checked_re", "");
						$template->set_var("checked_sr", "");
						$template->set_var("checked_wr", "");
						$template->set_var("checked_sw", "");
						$template->set_var("checked_ra", "");	
						$template->set_var("checked_de", "");	
						$template->set_var("checked_sp", "");
						
						$template->set_var("user","");
						$template->set_var("group",$_POST[group]);
						$template->set_var("ou","");
						
						$template->output();
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[id]);
						$paramquery[run] = "admin_permission";
						$params = http_build_query($paramquery);
						
						$project_permission = new ProjectPermissionGroup(null);
						
						$new_permssion = 0;
						
						if ($_POST[re] == "1")
						{
							$new_permission = $new_permission + 1;
						}
						if ($_POST[sr] == "1")
						{
							$new_permission = $new_permission + 2;
						}
						if ($_POST[wr] == "1")
						{
							$new_permission = $new_permission + 4;
						}
						if ($_POST[sw] == "1")
						{
							$new_permission = $new_permission + 8;
						}
						if ($_POST[ra] == "1") 
						{
							$new_permission = $new_permission + 16;
						}
						if ($_POST[de] == "1")
						{
							$new_permission = $new_permission + 32;
						}
						if ($_POST[sp] == "1")
						{
							$new_permission = $new_permission + 64;
						}
						
						if ($project_permission->create($_POST[group], $project_id, $new_permission, $user->get_user_id(), null))
						{
							Common_IO::step_proceed($params, "Add Permission", "Operation Successful", null);
						}
						else
						{
							Common_IO::step_proceed($params, "Add Permission", "Operation Failed" ,null);	
						}
					}
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function permission_add_organisation_unit()
	{
		global $project_security, $user;

		if ($_GET[project_id])
		{
			$project_id = $_GET[project_id];		
			$project = new Project($project_id);
			
			if ($user->get_user_id() == $project->get_owner_id() or
				$project_security->is_access(2, false) == true or
				$project_security->is_access(4, false) == true or
				$project_security->is_access(7, false) == true)
			{
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[ou]))
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must select an organisation unit.";
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
					$template = new Template("template/projects/admin/permission_add_ou.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->set_var("error",$error);
					
					$organisation_unit_array = OrganisationUnit::list_entries();
					
					$result = array();
					$counter = 0;
					
					foreach($organisation_unit_array as $key => $value)
					{
						$organisation_unit = new OrganisationUnit($value);
						$result[$counter][value] = $value;
						$result[$counter][content] = $organisation_unit->get_name();
						$counter++;
					}
					
					$template->set_var("option",$result);
					
					$template->output();
				}
				else
				{
					if ($_GET[nextpage] == 2)
					{
						if ($_POST[re] == "1" or 
							$_POST[sr] == "1" or 
							$_POST[wr] == "1" or 
							$_POST[sw] == "1" or 
							$_POST[ra] == "1" or 
							$_POST[de] == "1" or 
							$_POST[sp] == "1")
						{
							$page_2_passed = true;
						}
						else
						{
							$page_2_passed = false;
							$error = "You must select min. one permission.";
						}
					}
					elseif($_GET[nextpage] > 2)
					{
						$page_2_passed = true;
					}
					else
					{
						$page_2_passed = false;
						$error = "";
					}
					
					if ($page_2_passed == false)
					{
						$template = new Template("template/projects/admin/permission_add_edit.html");
						
						$template->set_var("add_edit","Add");
						
						$new_ou = new OrganisationUnit($_POST[ou]);
						
						$paramquery = $_GET;
						$paramquery[nextpage] = "2";
						$params = http_build_query($paramquery,'','&#38;');
						
						$template->set_var("params",$params);
						
						$template->set_var("name",$new_ou->get_name());
						$template->set_var("type","user");
						
						$template->set_var("error",$error);
						
						if ($project_security->is_access(2, false) or $project->get_owner_id() == $user->get_user_id())
						{				
							$template->set_var("disabled_re", "");
						}
						else
						{
							$template->set_var("disabled_re", "disabled");	
						}
						
						if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_sr", "");
						}
						else
						{
							$template->set_var("disabled_sr", "disabled");		
						}
						
						if ($project_security->is_access(4, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_wr", "");
						}
						else
						{
							$template->set_var("disabled_wr", "disabled");		
						}
						
						if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_sw", "");
						}
						else
						{
							$template->set_var("disabled_sw", "disabled");		
						}
						
						if ($project_security->is_access(7, false))
						{
							$template->set_var("disabled_ra", "");	
							$template->set_var("disabled_de", "");	
							$template->set_var("disabled_sp", "");
						}
						else
						{
							$template->set_var("disabled_ra", "disabled");	
							$template->set_var("disabled_de", "disabled");	
							$template->set_var("disabled_sp", "disabled");						
						}
						
						$template->set_var("checked_re", "");
						$template->set_var("checked_sr", "");
						$template->set_var("checked_wr", "");
						$template->set_var("checked_sw", "");
						$template->set_var("checked_ra", "");	
						$template->set_var("checked_de", "");	
						$template->set_var("checked_sp", "");
						
						$template->set_var("user","");
						$template->set_var("group","");
						$template->set_var("ou",$_POST[ou]);
						
						$template->output();
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[id]);
						$paramquery[run] = "admin_permission";
						$params = http_build_query($paramquery);
						
						$project_permission = new ProjectPermissionOrganisationUnit(null);
						
						$new_permssion = 0;
						
						if ($_POST[re] == "1")
						{
							$new_permission = $new_permission + 1;
						}
						if ($_POST[sr] == "1")
						{
							$new_permission = $new_permission + 2;
						}
						if ($_POST[wr] == "1")
						{
							$new_permission = $new_permission + 4;
						}
						if ($_POST[sw] == "1")
						{
							$new_permission = $new_permission + 8;
						}
						if ($_POST[ra] == "1")
						{
							$new_permission = $new_permission + 16;
						}
						if ($_POST[de] == "1")
						{
							$new_permission = $new_permission + 32;
						}
						if ($_POST[sp] == "1")
						{
							$new_permission = $new_permission + 64;
						}
						
						if ($project_permission->create($_POST[ou],$project_id, $new_permission, $user->get_user_id(), null))
						{
							Common_IO::step_proceed($params, "Add Permission", "Operation Successful", null);
						}
						else
						{
							Common_IO::step_proceed($params, "Add Permission", "Operation Failed" ,null);	
						}
					}
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 * @throws ProjectPermissionIDMissingException
	 */
	public static function permission_edit()
	{
		global $project_security, $user;
		
		if ($_GET[project_id])
		{
			if ($_GET[id])
			{
				$project_id = $_GET[project_id];
				$project = new Project($project_id);
				$project_permission = ProjectPermission::get_instance($_GET[id]);
				
				if ($user->get_user_id() == $project->get_owner_id() or
					$project_security->is_access(2, false) == true or
					$project_security->is_access(4, false) == true or
					$project_security->is_access(7, false) == true)
				{
					if ($_GET[nextpage] == 1)
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
						$template = new Template("template/projects/admin/permission_add_edit.html");
								
						$template->set_var("add_edit","Edit");
						
						$paramquery = $_GET;
						$paramquery[nextpage] = "1";
						$params = http_build_query($paramquery,'','&#38;');
						
						$template->set_var("params",$params);
						
						$template->set_var("error",$error);
						
						$user_id = $project_permission->get_user_id();
						$group_id = $project_permission->get_group_id();
						$organ_unit_id = $project_permission->get_organisation_unit_id();
						
						if ($user_id)
						{
							$permission_user = new User($user_id);
							$template->set_var("name",$permission_user->get_username());
							$template->set_var("type","user");
						}
						elseif($group_id)
						{
							$group = new Group($group_id);
							$template->set_var("name",$group->get_name());
							$template->set_var("type","group");
						}
						else
						{
							$organisation_unit = new OrganisationUnit($organ_unit_id);
							$template->set_var("name",$organisation_unit->get_name());
							$template->set_var("type","organisation unit");
						}
						
						$permission_array = $project_permission->get_permission_array();
								
						if ($permission_array[read] == true)
						{
							$template->set_var("checked_re", "checked='checked'");
						}
						else
						{
							$template->set_var("checked_re", "");
						}
						
						if ($permission_array[set_readable] == true)
						{
							$template->set_var("checked_sr", "checked='checked'");
						}
						else
						{
							$template->set_var("checked_sr", "");
						}
						
						if ($permission_array[write] == true)
						{
							$template->set_var("checked_wr", "checked='checked'");
						}
						else
						{
							$template->set_var("checked_wr", "");
						}
						
						if ($permission_array[set_writeable] == true)
						{
							$template->set_var("checked_sw", "checked='checked'");
						}
						else
						{
							$template->set_var("checked_sw", "");
						}
						
						if ($permission_array[reactivate] == true)
						{
							$template->set_var("checked_ra", "checked='checked'");
						}
						else
						{
							$template->set_var("checked_ra", "");
						}
						
						if ($permission_array[delete] == true)
						{
							$template->set_var("checked_de", "checked='checked'");
						}
						else
						{
							$template->set_var("checked_de", "");
						}
						
						if ($permission_array[set_permissions] == true)
						{
							$template->set_var("checked_sp", "checked='checked'");
						}
						else
						{
							$template->set_var("checked_sp", "");
						}
						
						
						if ($project_security->is_access(2, false) or $project->get_owner_id() == $user->get_user_id())
						{				
							$template->set_var("disabled_re", "");
						}
						else
						{
							$template->set_var("disabled_re", "disabled='disabled'");
						}
						
						if ($project_security->is_access(7, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_sr", "");
							$template->set_var("disabled_sw", "");
						}
						else
						{
							$template->set_var("disabled_sr", "disabled='disabled'");
							$template->set_var("disabled_sw", "disabled='disabled'");
						}
			
						if ($project_security->is_access(4, false) or $project->get_owner_id() == $user->get_user_id())
						{
							$template->set_var("disabled_wr", false);
						}
						else
						{
							$template->set_var("disabled_wr", "disabled='disabled'");
						}
			
						if ($project_security->is_access(7, false))
						{
							$template->set_var("disabled_ra", "");
							$template->set_var("disabled_de", "");
							$template->set_var("disabled_sp", "");
						}
						else
						{
							$template->set_var("disabled_ra", "disabled='disabled'");
							$template->set_var("disabled_de", "disabled='disabled'");
							$template->set_var("disabled_sp", "disabled='disabled'");
						}
						
						$template->output();
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[id]);
						$paramquery[run] = "admin_permission";
						$params = http_build_query($paramquery);
						
						$new_permission = 0;
						
						if ($_POST[re] == "1")
						{
							$new_permission = $new_permission + 1;
						}
						if ($_POST[sr] == "1")
						{
							$new_permission = $new_permission + 2;
						}
						if ($_POST[wr] == "1")
						{
							$new_permission = $new_permission + 4;
						}
						if ($_POST[sw] == "1")
						{
							$new_permission = $new_permission + 8;
						}
						if ($_POST[ra] == "1")
						{
							$new_permission = $new_permission + 16;
						}
						if ($_POST[de] == "1")
						{
							$new_permission = $new_permission + 32;
						}
						if ($_POST[sp] == "1")
						{
							$new_permission = $new_permission + 64;
						}
						
						if ($project_permission->set_permission($new_permission))
						{
							Common_IO::step_proceed($params, "Edit Permission", "Operation Successful", null);
						}
						else
						{
							Common_IO::step_proceed($params, "Edit Permission", "Operation Failed" ,null);	
						}
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			}
			else
			{
				throw new ProjectPermissonIDMissingException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}	
	}
	
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 * @throws ProjectPermissionIDMissingException
	 */
	public static function permission_delete()
	{
		global $project_security, $user;
		
		if ($_GET[project_id])
		{
			if ($_GET[id])
			{
				$project = new Project($_GET[project_id]);
			
				if ($user->get_user_id() == $project->get_owner_id() or
					$project_security->is_access(2, false) == true or
					$project_security->is_access(4, false) == true or
					$project_security->is_access(7, false) == true)
				{
					if ($_GET[sure] != "true")
					{
						$template = new Template("template/projects/admin/permission_delete.html");
						
						$paramquery = $_GET;
						$paramquery[sure] = "true";
						$params = http_build_query($paramquery);
						
						$template->set_var("yes_params", $params);
								
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[id]);
						$paramquery[run] = "admin_permission";
						$params = http_build_query($paramquery);
						
						$template->set_var("no_params", $params);
						
						$template->output();	
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[id]);
						unset($paramquery[sure]);
						$paramquery[run] = "admin_permission";
						$params = http_build_query($paramquery);
						
						$project_permission = ProjectPermission::get_instance($_GET[id]);
						
						if ($project_permission->delete())
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Successful" ,null);
						}
						else
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Failed" ,null);
						}			
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			}
			else
			{
				throw new ProjectPermissionIDMissingException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function rename()
	{
		global $project_security, $user;
		
		if ($_GET[project_id])
		{
			$project_id = $_GET[project_id];		
			$project = new Project($project_id);
			
			if ($user->get_user_id() == $project->get_owner_id() or
				$project_security->is_access(7, false) == true)
			{
				if ($_GET[nextpage] == 1)
				{
					if ($_POST[name])
					{
						if ($project->get_organisation_unit_id())
						{
							if (Project::exist_project_name($project->get_organisation_unit_id(),null, $_POST[name]) == false)
							{
								$page_1_passed = true;
							}
							else
							{
								$page_1_passed = false;
								$error = "This name already exists";
							}
						}
						else
						{
							if (Project::exist_project_name(null,$project->get_project_toid(), $_POST[name]) == false)
							{
								$page_1_passed = true;
							}
							else
							{
								$page_1_passed = false;
								$error = "This name already exists";
							}
						}
						
					}
					else
					{
						$page_1_passed = false;
						$error = "You must enter a name";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "";
				}
				
				if ($page_1_passed == false)
				{
					$template = new Template("template/projects/admin/rename.html");
				
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
				
					$template->set_var("params",$params);
				
					$template->set_var("error",$error);
					
					if ($_POST[name])
					{
						$template->set_var("name",$_POST[name]);
					}
					else
					{
						$template->set_var("name",trim($project->get_name()));
					}
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "admin";
					$params = http_build_query($paramquery);
					
					if ($project->set_name($_POST[name]))
					{
						Common_IO::step_proceed($params, "Rename Project", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Rename Project", "Operation Failed" ,null);	
					}
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function chown()
	{
		global $project_security;
	
		if ($_GET[project_id])
		{
			if ($project_security->is_access(7, false) == true)
			{
				$project_id = $_GET[project_id];		
				$project = new Project($project_id);
				
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[user]))
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must select an user";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "";
				}
				
				if ($page_1_passed == false)
				{
					$template = new Template("template/projects/admin/chown.html");
				
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
				
					$template->set_var("params",$params);
				
					$template->set_var("error",$error);
					
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
				
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "admin";
					$params = http_build_query($paramquery);
					
					if ($project->set_owner_id($_POST[user]))
					{
						Common_IO::step_proceed($params, "Change Project Owner", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Change Project Owner", "Operation Failed" ,null);	
					}
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function move()
	{
		global $project_security, $user;

		if ($_GET[project_id])
		{
			$project = new Project($_GET[project_id]);
			
			if ($user->get_user_id() == $project->get_owner_id() or
				$project_security->is_access(7, false) == true)
			{
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[type]))
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must make a selection.";
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
					$template = new Template("template/projects/admin/move_page_1.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->set_var("error", $error);
					
					$template->output();
				}
				else
				{
					if ($_POST[type] == 1)
					{
						if ($_GET[nextpage] == 2)
						{
							if (is_numeric($_POST[ou]))
							{
								$page_2_passed = true;
							}
							else
							{
								$page_2_passed = false;
								$error = "You must select an organisation unit.";
							}
						}
						elseif($_GET[nextpage] > 3)
						{
							$page_2_passed = true;
						}
						else
						{
							$page_2_passed = false;
							$error = "";
						}
						
						if ($page_2_passed == false)
						{
							$template = new Template("template/projects/admin/move_page_2_ou.html");
			
							$paramquery = $_GET;
							$paramquery[nextpage] = "2";
							$params = http_build_query($paramquery,'','&#38;');
							
							$template->set_var("params",$params);
			
							$template->set_var("error", $error);
			
							$organisation_unit_array = OrganisationUnit::list_entries();
							
							$result = array();
							$counter = 0;
							
							foreach($organisation_unit_array as $key => $value)
							{
								$organisation_unit = new OrganisationUnit($value);
								$result[$counter][value] = $value;
								$result[$counter][content] = $organisation_unit->get_name();
								$counter++;
							}
							
							$template->set_var("option",$result);
			
							$template->set_var("type",$_POST[type]);
			
							$template->output();
						}
						else
						{
							$project = new Project($_GET[project_id]);
							
							$paramquery = $_GET;
							unset($paramquery[nextpage]);
							$paramquery[run] = "admin";
							$params = http_build_query($paramquery);
							
							if ($project->move_to_organisation_unit($_POST[ou]))
							{
								Common_IO::step_proceed($params, "Move Project", "Operation Successful", null);
							}
							else
							{
								Common_IO::step_proceed($params, "Move Project", "Operation Failed" ,null);	
							}
						}
					}
					else
					{
						if ($_GET[nextpage] == 2)
						{
							if (is_numeric($_POST[project]))
							{
								$page_2_passed = true;
							}
							else
							{
								$page_2_passed = false;
								$error = "You must select a project.";
							}
						}
						elseif($_GET[nextpage] > 3)
						{
							$page_2_passed = true;
						}
						else
						{
							$page_2_passed = false;
							$error = "";
						}
						
						if ($page_2_passed == false)
						{
							$template = new Template("template/projects/admin/move_page_2_project.html");
			
							$paramquery = $_GET;
							$paramquery[nextpage] = "2";
							$params = http_build_query($paramquery,'','&#38;');
							
							$template->set_var("params",$params);
			
							$template->set_var("error", $error);
			
							$result = array();
							$counter = 0;
								
							$project = new Project(null);
							$project_array = $project->get_project_tree();
							
							foreach($project_array as $key => $value)
							{
								$project = new Project($value[id]);
		
								for($i=1;$i<=$value[layer];$i++)
								{
									$pre_content .= "&nbsp;";
								}
						
								$result[$counter][value] = $value[id];
								$result[$counter][content] = $pre_content."".$project->get_name();		
			
								$counter++;
								
								unset($pre_content);
							}
							
							if (!$result)
							{
								$result[$counter][value] = "0";
								$result[$counter][content] = "NO PROJECT FOUND!";		
							}
					
							$template->set_var("option",$result);
			
							$template->set_var("type",$_POST[type]);
			
							$template->output();
						}
						else
						{
							$project = new Project($_GET[project_id]);
							
							$paramquery = $_GET;
							unset($paramquery[nextpage]);
							$paramquery[run] = "admin";
							$params = http_build_query($paramquery);
							
							if ($project->move_to_project($_POST[project]))
							{
								Common_IO::step_proceed($params, "Move Project", "Operation Successful", null);
							}
							else
							{
								Common_IO::step_proceed($params, "Move Project", "Operation Failed" ,null);	
							}
						}
					}
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function quota()
	{
		global $project_security;
	
		if ($_GET[project_id])
		{
			if ($project_security->is_access(7, false) == true)
			{
				$project_id = $_GET[project_id];		
				$project = new Project($project_id);
				
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[quota]))
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must enter a value";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "";
				}
				
				if ($page_1_passed == false)
				{
					$template = new Template("template/projects/admin/quota.html");
				
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
				
					$template->set_var("params",$params);
				
					$template->set_var("error",$error);
					
					if ($_POST[name])
					{
						$template->set_var("quota",$_POST[quota]);
					}
					else
					{
						$template->set_var("quota",trim($project->get_quota()));
					}
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "admin";
					$params = http_build_query($paramquery);
					
					if ($project->set_quota($_POST[quota]))
					{
						Common_IO::step_proceed($params, "Change Quota", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Change Quota", "Operation Failed" ,null);	
					}
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function delete()
	{
		global $project_security;
		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(6, false) == true)
			{
				$project_id = $_GET[project_id];		
				$project = new Project($project_id);
			
				if ($_GET[sure] != "true")
				{
					$template = new Template("template/projects/admin/delete.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[id]);
					$paramquery[run] = "admin";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[sure]);
					$paramquery[run] = "detail";
					$params = http_build_query($paramquery);
					
					$project_permission = ProjectPermission::get_instance($_GET[id]);
					
					if ($project->mark_as_deleted())
					{							
						Common_IO::step_proceed($params, "Delete Project", "Operation Successful" ,null);
					}
					else
					{							
						Common_IO::step_proceed($params, "Delete Project", "Operation Failed" ,null);
					}			
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function full_delete()
	{
		global $user;
		
		if ($_GET[project_id])
		{
			if ($user->is_admin())
			{
				$project_id = $_GET[project_id];		
				$project = new Project($project_id);
			
				if ($_GET[sure] != "true")
				{
					$template = new Template("template/projects/admin/full_delete.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[id]);
					$paramquery[run] = "admin";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[sure]);
					unset($paramquery[run]);
					$params = http_build_query($paramquery);
					
					$project_permission = ProjectPermission::get_instance($_GET[id]);
					
					if ($project->delete())
					{							
						Common_IO::step_proceed($params, "Full Project Delete", "Operation Successful" ,null);
					}
					else
					{							
						Common_IO::step_proceed($params, "Full Project Delete", "Operation Failed" ,null);
					}			
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function restore()
	{
		global $user;
	
		if ($_GET[project_id])
		{
			if ($user->is_admin())
			{
				$project_id = $_GET[project_id];		
				$project = new Project($project_id);
			
				if ($_GET[sure] != "true")
				{
					$template = new Template("template/projects/admin/restore.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[id]);
					$paramquery[run] = "admin";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[sure]);
					$paramquery[run] = "detail";
					$params = http_build_query($paramquery);
					
					$project_permission = ProjectPermission::get_instance($_GET[id]);
					
					if ($project->mark_as_undeleted())
					{							
						Common_IO::step_proceed($params, "Restore Project", "Operation Successful" ,null);
					}
					else
					{							
						Common_IO::step_proceed($params, "Restore Project", "Operation Failed" ,null);
					}			
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function cancel()
	{
		global $project_security, $user;
	
		if ($_GET[project_id])
		{
			$project_id = $_GET[project_id];		
			$project = new Project($project_id);
			
			if ($user->get_user_id() == $project->get_owner_id() or
				$project_security->is_access(3, false) == true)
			{
				if ($_GET[nextpage] == 1)
				{
					if ($_POST[reason])
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must enter a reason";
					}
				}
				else
				{
					$page_1_passed = false;
					$error = "";
				}
				
				if ($page_1_passed == false)
				{
					$template = new Template("template/projects/admin/cancel.html");
					
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
				
					$template->set_var("params",$params);
				
					$template->set_var("error",$error);
					
					if ($_POST[reason])
					{
						$template->set_var("reason", $_POST[reason]);
					}
					else
					{
						$template->set_var("reason","");
					}
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "detail";
					$params = http_build_query($paramquery);
					
					if ($project->mark_as_canceled($_POST[reason]))
					{
						Common_IO::step_proceed($params, "Change Quota", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Change Quota", "Operation Failed" ,null);	
					}
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function reactivate()
	{		
		if ($_GET[project_id])
		{
			if ($project_security->is_access(5, false) == true)
			{
				$project_id = $_GET[project_id];		
				$project = new Project($project_id);
			
				if ($_GET[sure] != "true")
				{
					$template = new Template("template/projects/admin/reactivate.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[id]);
					$paramquery[run] = "admin";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[sure]);
					$paramquery[run] = "detail";
					$params = http_build_query($paramquery);
					
					$project_permission = ProjectPermission::get_instance($_GET[id]);
					
					if ($project->mark_as_reactivated())
					{							
						Common_IO::step_proceed($params, "Reactivate Project", "Operation Successful" ,null);
					}
					else
					{							
						Common_IO::step_proceed($params, "Reactivate Project", "Operation Failed" ,null);
					}			
				}
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
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function menu()
	{
		global $user, $project_security;
	
		if ($_GET[project_id])
		{
			$project = new Project($_GET[project_id]);
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
			
				$template = new Template("template/projects/admin/menu.html");
				
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
				
				
				$delete_paramquery = $_GET;
				$delete_paramquery[run] = "admin_delete";
				unset($delete_paramquery[nextpage]);
				unset($delete_paramquery[sure]);
				$delete_params = http_build_query($delete_paramquery,'','&#38;');
				
				$template->set_var("delete_params", $delete_params);
				
				
				$full_delete_paramquery = $_GET;
				$full_delete_paramquery[run] = "admin_full_delete";
				unset($full_delete_paramquery[nextpage]);
				unset($full_delete_paramquery[sure]);
				$full_delete_params = http_build_query($full_delete_paramquery,'','&#38;');
				
				$template->set_var("full_delete_params", $full_delete_params);
		
		
				$restore_paramquery = $_GET;
				$restore_paramquery[run] = "admin_restore";
				unset($restore_paramquery[nextpage]);
				unset($restore_paramquery[sure]);
				$restore_params = http_build_query($restore_paramquery,'','&#38;');
				
				$template->set_var("restore_params", $restore_params);
			
			
				$cancel_paramquery = $_GET;
				$cancel_paramquery[run] = "admin_cancel";
				unset($cancel_paramquery[nextpage]);
				unset($cancel_paramquery[sure]);
				$cancel_params = http_build_query($cancel_paramquery,'','&#38;');
				
				$template->set_var("cancel_params", $cancel_params);
				
				
				$reactivate_paramquery = $_GET;
				$reactivate_paramquery[run] = "admin_reactivate";
				unset($reactivate_paramquery[nextpage]);
				unset($reactivate_paramquery[sure]);
				$reactivate_params = http_build_query($reactivate_paramquery,'','&#38;');
				
				$template->set_var("reactivate_params", $reactivate_params);
				
				
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
				
				
				$template->output();
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

}

?>
