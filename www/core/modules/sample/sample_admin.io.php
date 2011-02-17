<?php
/**
 * @package sample
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
 * Sample Admin IO Class
 * @package sample
 */
class SampleAdminIO
{
	public static function delete()
	{
		global $common, $user;
		
		if ($_GET[sample_id])
		{	
			if ($user->is_admin())
			{
				if ($_GET[sure] != "true")
				{
					$template = new Template("languages/en-gb/template/samples/admin/delete.html");
					
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
					unset($paramquery[sample_id]);
					$paramquery[nav] = "samples";
					$params = http_build_query($paramquery);
					
					$sample = new Sample($_GET[sample_id]);
								
					if ($sample->delete())
					{							
						$common->step_proceed($params, "Delete Sample", "Operation Successful" ,null);
					}
					else
					{							
						$common->step_proceed($params, "Delete Sample", "Operation Failed" ,null);
					}			
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function delete_project_association()
	{
		global $common, $user;
		
		if ($_GET[sample_id])
		{
			if ($_GET[project_id])
			{
				$sample = new Sample($_GET[sample_id]);
				$project_id = $_GET[project_id];		
				$project_item = new ProjectItem($project_id);
			
				if ($sample->get_owner_id() == $user->get_user_id() or
					$user->is_admin() == true)
				{
					if ($_GET[sure] != "true")
					{
						$template = new Template("languages/en-gb/template/samples/admin/delete_project_association.html");
						
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
						$paramquery[nav] = "samples";
						$params = http_build_query($paramquery);
			
						$item_id = $sample->get_item_id();
						
						$project_item->set_item_id($item_id);
						
						if ($project_item->unlink_item())
						{							
							$common->step_proceed($params, "Delete Project Association", "Operation Successful" ,null);
						}
						else
						{							
							$common->step_proceed($params, "Delete Project Association", "Operation Failed" ,null);
						}			
					}
				}
				else
				{
					$exception = new Exception("", 1);
					$error_io = new Error_IO($exception, 250, 40, 2);
					$error_io->display_error();
				}
			}
			else
			{
				$exception = new Exception("", 2);
				$error_io = new Error_IO($exception, 250, 40, 3);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function rename()
	{
		global $common, $user;
		
		if ($_GET[sample_id])
		{
			$sample_id = $_GET[sample_id];		
			$sample = new Sample($sample_id);
			
			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				if ($_GET[nextpage] == 1)
				{
					if ($_POST[name])
					{
						$page_1_passed = true;
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
					$template = new Template("languages/en-gb/template/samples/admin/rename.html");
				
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
						$template->set_var("name",trim($sample->get_name()));
					}
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "detail";
					$params = http_build_query($paramquery);
					
					if ($sample->set_name($_POST[name]))
					{
						$common->step_proceed($params, "Rename Sample", "Operation Successful", null);
					}
					else
					{
						$common->step_proceed($params, "Rename Sample", "Operation Failed" ,null);	
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function user_permission()
	{
		global $user;
		
		if ($_GET[sample_id])
		{
			$sample_id = $_GET[sample_id];
		
			$sample = new Sample($sample_id);
			$sample_security = new SampleSecurity($sample_id);
			$sample_user_array = $sample_security->list_users();
		
			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				$template = new Template("languages/en-gb/template/samples/admin/user_permission.html");
				
				$add_user_paramquery = $_GET;
				$add_user_paramquery[run] = "admin_permission_user_add";
				$add_user_params = http_build_query($add_user_paramquery,'','&#38;');
				
				$template->set_var("add_user_params", $add_user_params);
		
				$table_io = new TableIO("OverviewTable");
					
				$table_io->add_row("Username","username",false,null);
				$table_io->add_row("Full Name","fullname",false,null);
				$table_io->add_row("Read","read",false,50);
				$table_io->add_row("Write","write",false,50);
				$table_io->add_row("Delete","delete",false,50);	
				
				$content_array = array();
				
				if (is_array($sample_user_array) and count($sample_user_array) >= 1)
				{
					foreach($sample_user_array as $key => $value)
					{
						$column_array = array();
						
						$column_user = new User($value);
						
						if ($sample->get_owner_id() == $value)
						{
							$column_array[username] = $column_user->get_username()." (owner)";
						}
						else
						{
							$column_array[username] = $column_user->get_username();
						}
						
						$column_array[fullname] = $column_user->get_full_name(false);
						
						$access_array = $sample_security->get_access_by_user_id($value);
						
						if ($access_array[read] == true)
						{
							$column_array[read] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$column_array[read] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
						
						if ($access_array[write] == true)
						{
							$column_array[write] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$column_array[write] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
						
						$delete_paramquery = $_GET;
						$delete_paramquery[run] = "admin_permission_user_delete";
						$delete_paramquery[id] = $value;
						unset($delete_paramquery[sure]);
						$delete_params = http_build_query($delete_paramquery,'','&#38;');
						
						if ($sample->get_owner_id() == $value)
						{
							$column_array[delete][link] = "";
							$column_array[delete][content] = "";
						}
						else
						{
							$column_array[delete][link] = $delete_params;
							$column_array[delete][content] = "delete";
						}
						array_push($content_array, $column_array);
					}
				}
				else
				{
					$table_io->override_last_line("<span class='italic'>No Samples Found!</span>");
				}
				
				$table_io->add_content_array($content_array);	
			
				$template->set_var("table", $table_io->get_content($_GET[page]));		
					
				$template->output();			
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function user_permission_add()
	{
		global $user, $common;

		if ($_GET[sample_id])
		{
			$sample_id = $_GET[sample_id];	
			$sample = new Sample($sample_id);	
			$sample_security = new SampleSecurity($sample_id);
			
			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[user]))
					{
						if ($sample_security->is_user($_POST[user]) == true)
						{
							$page_1_passed = false;
							$error = "This user was already added.";
						}
						else
						{
							$page_1_passed = true;
						}
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
					$template = new Template("languages/en-gb/template/samples/admin/user_permission_add_page_1.html");
					
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
						$page_2_passed = true;
					}
					else
					{
						$page_2_passed = false;
					}
					
					if ($page_2_passed == false)
					{
						$template = new Template("languages/en-gb/template/samples/admin/user_permission_add_page_2.html");
						
						$paramquery = $_GET;
						$paramquery[nextpage] = "2";
						$params = http_build_query($paramquery,'','&#38;');
						
						$template->set_var("params",$params);
						
						$template->set_var("user", $_POST[user]);
						
						$template->output();
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[sure]);
						$paramquery[nav] = "samples";
						$paramquery[run] = "admin_permission_user";
						$params = http_build_query($paramquery);
						
						if ($_POST[read] == "1")
						{
							$read = true;
						}
						else
						{
							$read = false;
						}
						
						if ($_POST[write] == "1")
						{
							$write = true;
						}
						else
						{
							$write = false;
						}
						
						if ($sample_security->create_user($_POST[user], $read, $write) != null)
						{							
							$common->step_proceed($params, "Add Permission", "Operation Successful" ,null);
						}
						else
						{							
							$common->step_proceed($params, "Add Permission", "Operation Failed" ,null);
						}
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function user_permission_delete()
	{
		global $common, $user;
		
		if ($_GET[sample_id])
		{
			if ($_GET[id])
			{
				$sample_id = $_GET[sample_id];		
				$sample = new Sample($sample_id);
				$sample_security = new SampleSecurity($sample_id);
		
				if ($sample->get_owner_id() == $user->get_user_id() or
					$user->is_admin() == true)
				{
					if ($_GET[sure] != "true")
					{
						$template = new Template("languages/en-gb/template/samples/admin/ou_permission_delete.html");
						
						$paramquery = $_GET;
						$paramquery[sure] = "true";
						$params = http_build_query($paramquery);
						
						$template->set_var("yes_params", $params);
								
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[sure]);
						$paramquery[nav] = "samples";
						$paramquery[run] = "admin_permission_user";
						$params = http_build_query($paramquery);
						
						$template->set_var("no_params", $params);
						
						$template->output();
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[sure]);
						$paramquery[nav] = "samples";
						$paramquery[run] = "admin_permission_user";
						$params = http_build_query($paramquery);
						
						$entry_id = $sample_security->get_entry_by_user_id($_GET[id]);
													
						if ($sample_security->delete_user($entry_id))
						{							
							$common->step_proceed($params, "Delete Sample", "Operation Successful" ,null);
						}
						else
						{							
							$common->step_proceed($params, "Delete Sample", "Operation Failed" ,null);
						}	
					}
				}
				else
				{
					$exception = new Exception("", 1);
					$error_io = new Error_IO($exception, 250, 40, 2);
					$error_io->display_error();
				}
			}
			else
			{
				$exception = new Exception("", 0);
				$error_io = new Error_IO($exception, 250, 40, 3);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function ou_permission()
	{
		global $user;
		
		if ($_GET[sample_id])
		{
			$sample_id = $_GET[sample_id];
		
			$sample = new Sample($sample_id);
			$sample_security = new SampleSecurity($sample_id);
			$sample_ou_array = $sample_security->list_organisation_units();
		
			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				$template = new Template("languages/en-gb/template/samples/admin/ou_permission.html");
				
				$add_ou_paramquery = $_GET;
				$add_ou_paramquery[run] = "admin_permission_ou_add";
				$add_ou_params = http_build_query($add_ou_paramquery,'','&#38;');
				
				$template->set_var("add_ou_params", $add_ou_params);
		
				$table_io = new TableIO("OverviewTable");
					
				$table_io->add_row("Name","name",false,null);
				$table_io->add_row("Delete","delete",false,50);	
				
				$content_array = array();

				if (is_array($sample_ou_array) and count($sample_ou_array) >= 1)
				{
					foreach($sample_ou_array as $key => $value)
					{
						$column_array = array();

						$column_ou = new OrganisationUnit($value);
						
						$column_array[name] = $column_ou->get_name();
						
						$delete_paramquery = $_GET;
						$delete_paramquery[run] = "admin_permission_ou_delete";
						$delete_paramquery[id] = $value;
						unset($delete_paramquery[sure]);
						$delete_params = http_build_query($delete_paramquery,'','&#38;');
						
						$column_array[delete][link] = $delete_params;
						$column_array[delete][content] = "delete";
											
						array_push($content_array, $column_array);	
					}
				}
				else
				{
					$table_io->override_last_line("<span class='italic'>No Entries Found!</span>");
				}
			
				$table_io->add_content_array($content_array);	
		
				$template->set_var("table", $table_io->get_content($_GET[page]));		
				
				$template->output();	
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function ou_permission_add()
	{
		global $user, $common;

		if($_GET[sample_id])
		{
			$sample_id = $_GET[sample_id];		
			$sample = new Sample($sample_id);
			$sample_security = new SampleSecurity($sample_id);

			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[ou]))
					{
						if ($sample_security->is_organisation_unit($_POST[ou]) == true)
						{
							$page_1_passed = false;
							$error = "This organisation unit was already added.";
						}
						else
						{
							$page_1_passed = true;
						}
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
					$template = new Template("languages/en-gb/template/samples/admin/ou_permission_add.html");
					
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
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[sure]);
					$paramquery[nav] = "samples";
					$paramquery[run] = "admin_permission_ou";
					$params = http_build_query($paramquery);
					
					if ($sample_security->create_organisation_unit($_POST[ou]))
					{							
						$common->step_proceed($params, "Add Permission", "Operation Successful" ,null);
					}
					else
					{							
						$common->step_proceed($params, "Add Permission", "Operation Failed" ,null);
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function ou_permission_delete()
	{
		global $common, $user;
		
		if ($_GET[sample_id])
		{
			if ($_GET[id])
			{
				$sample_id = $_GET[sample_id];		
				$sample = new Sample($sample_id);
				$sample_security = new SampleSecurity($sample_id);
		
				if ($sample->get_owner_id() == $user->get_user_id() or
					$user->is_admin() == true)
				{
					if ($_GET[sure] != "true")
					{
						$template = new Template("languages/en-gb/template/samples/admin/ou_permission_delete.html");
						
						$paramquery = $_GET;
						$paramquery[sure] = "true";
						$params = http_build_query($paramquery);
						
						$template->set_var("yes_params", $params);
								
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[sure]);
						$paramquery[nav] = "samples";
						$paramquery[run] = "admin_permission_ou";
						$params = http_build_query($paramquery);
						
						$template->set_var("no_params", $params);
						
						$template->output();
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[sure]);
						$paramquery[nav] = "samples";
						$paramquery[run] = "admin_permission_ou";
						$params = http_build_query($paramquery);
						
						$entry_id = $sample_security->get_entry_by_organisation_unit_id($_GET[id]);
													
						if ($sample_security->delete_organisation_unit($entry_id))
						{							
							$common->step_proceed($params, "Delete Sample", "Operation Successful" ,null);
						}
						else
						{							
							$common->step_proceed($params, "Delete Sample", "Operation Failed" ,null);
						}	
					}
				}
				else
				{
					$exception = new Exception("", 1);
					$error_io = new Error_IO($exception, 250, 40, 2);
					$error_io->display_error();
				}
			}
			else
			{
				$exception = new Exception("", 0);
				$error_io = new Error_IO($exception, 250, 40, 3);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}

}

?>
