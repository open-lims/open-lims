<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Project Status Admin IO Class
 * @package project
 */
class AdminProjectStatusIO
{
	public static function home()
	{	
		$list = new List_IO("ProjectAdminStatus", "ajax.php?nav=project", "admin_project_status_list_status", "admin_project_status_count_status", "0", "ProjectAdminStatus");

		$list->add_column(Language::get_message("ProjectGeneralListColumnID", "general"), "id", true, null);
		$list->add_column(Language::get_message("ProjectGeneralListColumnName", "general"), "name", true, null);
		$list->add_column(Language::get_message("ProjectGeneralListColumnEdit", "general"), "edit", false, "15%");
		$list->add_column(Language::get_message("ProjectGeneralListColumnDelete", "general"), "delete", false, "15%");

		$template = new HTMLTemplate("project/admin/project_status/list.html");	
	
		$paramquery = $_GET;
		$paramquery['action'] = "add";
		unset($paramquery['nextpage']);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
	
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}

	public static function create()
	{
		if ($_GET['nextpage'] == 1)
		{
			$page_1_passed = true;
			
			if (!$_POST['name'])
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
			$template = new HTMLTemplate("project/admin/project_status/add.html");
			
			$paramquery = $_GET;
			$paramquery['nextpage'] = "1";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params",$params);
			
			if ($error)
			{
				$template->set_var("error", $error);
			}
			else
			{
				$template->set_var("error", "");	
			}
												 
			if ($_POST['name'])
			{
				$template->set_var("name", $_POST['name']);
			}
			else
			{
				$template->set_var("name", "");
			}
						
			$template->output();
		}
		else
		{				
			$project_status = new ProjectStatus(null);

			$paramquery = $_GET;
			unset($paramquery['action']);
			unset($paramquery['nextpage']);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($project_status->create($_POST['name'], null))
			{
				Common_IO::step_proceed($params, "Add Project Status", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Add Project Status", "Operation Failed" ,null);	
			}
		}
	}
	
	/**
	 * @throws ProjectStatusIDMissingException
	 */
	public static function delete()
	{
		if ($_GET['id'])
		{
			if ($_GET['sure'] != "true")
			{
				$template = new HTMLTemplate("project/admin/project_status/delete.html");
				
				$paramquery = $_GET;
				$paramquery['sure'] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery['sure']);
				unset($paramquery['action']);
				unset($paramquery['id']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery['sure']);
				unset($paramquery['action']);
				unset($paramquery['id']);
				$params = http_build_query($paramquery,'','&#38;');
				
				$project_status = new ProjectStatus($_GET['id']);
				
				if ($project_status->delete())
				{							
					Common_IO::step_proceed($params, "Delete Project Status", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Project Status", "Operation Failed" ,null);
				}	
			}
		}
		else
		{
			throw new ProjectStatusIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectStatusIDMissingException
	 */
	public static function edit()
	{
		if ($_GET['id'])
		{
			$project_status = new ProjectStatus($_GET['id']);
		
			if ($_GET['nextpage'] == 1)
			{
				$page_1_passed = true;
				
				if (!$_POST['name'])
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
				$template = new HTMLTemplate("project/admin/project_status/edit.html");
				
				$paramquery = $_GET;
				$paramquery['nextpage'] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params",$params);
				
				if ($error)
				{
					$template->set_var("error", $error);
				}
				else
				{
					$template->set_var("error", "");	
				}
													 
				if ($_POST['name'])
				{
					$template->set_var("name", $_POST['name']);
				}
				else
				{
					$template->set_var("name", $project_status->get_name());
				}
							
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery['nextpage']);
				unset($paramquery['action']);
				$params = http_build_query($paramquery);
				
				if ($project_status->set_name($_POST['name']))
				{
					Common_IO::step_proceed($params, "Edit Project Status", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Edit Project Status", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new ProjectStatusIDMissingException();
		}
	}

	public static function handler()
	{
		switch($_GET['action']):
			case "add":
				self::create();
			break;
			
			case "edit":
				self::edit();
			break;
			
			case "delete":
				self::delete();
			break;
						
			default:
				self::home();
			break;
		endswitch;
	}
	
}

?>
