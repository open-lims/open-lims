<?php
/**
 * @package base
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
 * Admin IO Class
 * @package base
 */
class AdminIO
{
	private static function home()
	{
		
		$template = new Template("languages/en-gb/template/admin/home.html");
		
		$template->set_var("base_dir", System::get_base_directory());
		$template->set_var("system_space", Misc::calc_size(System::get_system_space()));
		$template->set_var("user_used_space", Misc::calc_size(DataUserData::get_used_space()));
		$template->set_var("project_used_space", Misc::calc_size(Project::get_used_project_space()));
		$template->set_var("db_used_space", Misc::calc_size(System::get_used_database_space()));
		$template->set_var("free_space", Misc::calc_size(System::get_free_space()));
		
		$template->set_var("user_amount", User::count_users());
		$template->set_var("user_administrators", User::count_administrators());
		$template->set_var("group_amount", Group::count_groups());
		$template->set_var("ou_amount", OrganisationUnit::count_organisation_units());
		
		$template->output();
		
	}
		
	public static function method_handler()
	{
		global $user;
		
		if ($user->is_admin())
		{
			switch($_GET[run]):
			
				case "user":
					require_once("admin_user.io.php");
					AdminUserIO::handler();
				break;
				
				case "group":
					require_once("admin_group.io.php");
					AdminGroupIO::handler();
				break;
				
				case "organisation_unit":
					require_once("admin_organisation_unit.io.php");
					AdminOrganisationUnitIO::handler();
				break;
				
				case "project_template":
					require_once("admin_project_template.io.php");
					AdminProjectTemplateIO::handler();
				break;
				
				case "project_template_cat":
					require_once("admin_project_template_cat.io.php");
					AdminProjectTemplateCatIO::handler();
				break;
				
				case "sample_template":
					require_once("admin_sample_template.io.php");
					AdminSampleTemplateIO::handler();
				break;
				
				case "sample_template_cat":
					require_once("admin_sample_template_cat.io.php");
					AdminSampleTemplateCatIO::handler();
				break;
				
				case "value_template":
					require_once("admin_value_template.io.php");
					AdminValueTemplateIO::handler();
				break;
				
				case "project_status":
					require_once("admin_project_status.io.php");
					AdminProjectStatusIO::handler();
				break;
				
				case "method_cat":
					require_once("admin_method_cat.io.php");
					AdminMethodCatIO::handler();
				break;
				
				case "method_type":
					require_once("admin_method_type.io.php");
					AdminMethodTypeIO::handler();
				break;
				
				case "sample_depository":
					require_once("admin_sample_depository.io.php");
					AdminSampleDepositoryIO::handler();
				break;
				
				case "system_log":
					require_once("admin_system_log.io.php");
					AdminSystemLogIO::handler();
				break;
				
				case "system_message":
					require_once("admin_system_message.io.php");
					AdminSystemMessageIO::handler();
				break;
				
				default:
					self::home();
				break;
				
			endswitch;		
		}
		else
		{
			
			if ($_GET[run] == "organisation_unit" and 
				is_numeric($_GET[id]))
			{
				$organisation_unit = new OrganisationUnit($_GET[id]);
				
				if ($organisation_unit->get_owner_id() == $user->get_user_id())
				{
					if ($_GET[action] == "detail" or 
						$_GET[action] == "add_user" or 
						$_GET[action] == "delete_user" or
						$_GET[action] == "add_group" or
						$_GET[action] == "delete_group")
					{
						require_once("admin_organisation_unit.io.php");
						AdminOrganisationUnitIO::handler();
					}
					else
					{
						$exception = new Exception("", 1);
						$error_io = new Error_IO($exception, 1, 40, 2);
						$error_io->display_error();
					}
				}
				else
				{
					$exception = new Exception("", 1);
					$error_io = new Error_IO($exception, 1, 40, 2);
					$error_io->display_error();
				}	
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 1, 40, 2);
				$error_io->display_error();
			}
		}
		
	}
	
}
	
?>