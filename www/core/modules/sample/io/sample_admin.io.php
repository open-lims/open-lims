<?php
/**
 * @package sample
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
 * Sample Admin IO Class
 * @package sample
 */
class SampleAdminIO
{
	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function rename()
	{
		global $user;
		
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
					$template = new HTMLTemplate("sample/int_admin/rename.html");
				
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
						Common_IO::step_proceed($params, "Rename Sample", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Rename Sample", "Operation Failed" ,null);	
					}
				}
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function user_permission()
	{
		global $user;
		
		if ($_GET[sample_id])
		{
			$sample_id = $_GET[sample_id];
		
			$sample = new Sample($sample_id);
		
			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				$argument_array = array();
				$argument_array[0][0] = "sample_id";
				$argument_array[0][1] = $_GET[sample_id];
	
				$list = new List_IO("SampleAdminPermissionUser", "ajax.php?nav=sample", "admin_list_user_permissions", "admin_count_user_permissions", $argument_array, "SampleAdminPermissionUser");
				
				$list->add_column("","symbol",false,"16px");
				$list->add_column("Username","username",true,null);
				$list->add_column("Full Name","name",true,null);
				$list->add_column("Read","read",true,"70px");
				$list->add_column("Write","write",true,"70px");
				$list->add_column("Delete","delete",false,"70px");

				$template = new HTMLTemplate("sample/int_admin/user_permission.html");
				
				$add_user_paramquery = $_GET;
				$add_user_paramquery[run] = "admin_permission_user_add";
				$add_user_params = http_build_query($add_user_paramquery,'','&#38;');
				
				$template->set_var("add_user_params", $add_user_params);
				
				$template->set_var("list", $list->get_list());	
					
				$template->output();			
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function user_permission_add()
	{
		global $user;

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
					$template = new HTMLTemplate("sample/int_admin/user_permission_add_page_1.html");
					
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
						$template = new HTMLTemplate("sample/int_admin/user_permission_add_page_2.html");
						
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
						$paramquery[nav] = "sample";
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
							Common_IO::step_proceed($params, "Add Permission", "Operation Successful" ,null);
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
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 * @throws SamplePermissionUserIDMissingException
	 */
	public static function user_permission_delete()
	{
		global $user;
		
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
						$template = new HTMLTemplate("sample/int_admin/ou_permission_delete.html");
						
						$paramquery = $_GET;
						$paramquery[sure] = "true";
						$params = http_build_query($paramquery);
						
						$template->set_var("yes_params", $params);
								
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[sure]);
						$paramquery[nav] = "sample";
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
						$paramquery[nav] = "sample";
						$paramquery[run] = "admin_permission_user";
						$params = http_build_query($paramquery);
						
						$entry_id = $sample_security->get_entry_by_user_id($_GET[id]);
													
						if ($sample_security->delete_user($entry_id))
						{							
							Common_IO::step_proceed($params, "Delete Sample", "Operation Successful" ,null);
						}
						else
						{							
							Common_IO::step_proceed($params, "Delete Sample", "Operation Failed" ,null);
						}	
					}
				}
				else
				{
					throw new SampleSecurityAccessDeniedException();
				}
			}
			else
			{
				throw new SamplePermissionUserIDMissingException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function ou_permission()
	{
		global $user;
		
		if ($_GET[sample_id])
		{
			$sample_id = $_GET[sample_id];
		
			$sample = new Sample($sample_id);
		
			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				$argument_array = array();
				$argument_array[0][0] = "sample_id";
				$argument_array[0][1] = $_GET[sample_id];
				
				$list = new List_IO("SampleAdminPermissionOrganisationUnit", "ajax.php?nav=sample", "admin_list_organisation_unit_permissions", "admin_count_organisation_unit_permissions", $argument_array, "SampleAdminPermissionOrganisationUnit");
				
				$list->add_column("","symbol",false,"16px");
				$list->add_column("Name","name",true,null);
				$list->add_column("Delete","delete",false,"70px");
		
				$template = new HTMLTemplate("sample/int_admin/ou_permission.html");
				
				$add_ou_paramquery = $_GET;
				$add_ou_paramquery[run] = "admin_permission_ou_add";
				$add_ou_params = http_build_query($add_ou_paramquery,'','&#38;');
				
				$template->set_var("add_ou_params", $add_ou_params);
				
				$template->set_var("list", $list->get_list());	
				
				$template->output();	
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function ou_permission_add()
	{
		global $user;

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
					$template = new HTMLTemplate("sample/int_admin/ou_permission_add.html");
					
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
					$paramquery[nav] = "sample";
					$paramquery[run] = "admin_permission_ou";
					$params = http_build_query($paramquery);
					
					if ($sample_security->create_organisation_unit($_POST[ou]))
					{							
						Common_IO::step_proceed($params, "Add Permission", "Operation Successful" ,null);
					}
					else
					{							
						Common_IO::step_proceed($params, "Add Permission", "Operation Failed" ,null);
					}
				}
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 * @throws SamplePermissionOrganisationUnitIDMissingException
	 */
	public static function ou_permission_delete()
	{
		global $user;
		
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
						$template = new HTMLTemplate("sample/int_admin/ou_permission_delete.html");
						
						$paramquery = $_GET;
						$paramquery[sure] = "true";
						$params = http_build_query($paramquery);
						
						$template->set_var("yes_params", $params);
								
						$paramquery = $_GET;
						unset($paramquery[nextpage]);
						unset($paramquery[sure]);
						$paramquery[nav] = "sample";
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
						$paramquery[nav] = "sample";
						$paramquery[run] = "admin_permission_ou";
						$params = http_build_query($paramquery);
						
						$entry_id = $sample_security->get_entry_by_organisation_unit_id($_GET[id]);
													
						if ($sample_security->delete_organisation_unit($entry_id))
						{							
							Common_IO::step_proceed($params, "Delete Sample", "Operation Successful" ,null);
						}
						else
						{							
							Common_IO::step_proceed($params, "Delete Sample", "Operation Failed" ,null);
						}	
					}
				}
				else
				{
					throw new SampleSecurityAccessDeniedException();
				}
			}
			else
			{
				throw new SamplePermissionOrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}

}

?>
