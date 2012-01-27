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
 * Project Template Category Admin IO Class
 * @package project
 */
class AdminProjectTemplateCatIO
{
	public static function home()
	{
		$list = new List_IO("ProjectAdminTemplateCat", "ajax.php?nav=project", "admin_project_template_cat_list_categories", "admin_project_template_cat_count_categories", "0", "ProjectAdminTemplateCat");
		
		$list->add_column("Name", "name", true, null);
		$list->add_column("Edit", "edit", false, "10%");
		$list->add_column("Delete", "delete", false, "10%");

		$template = new HTMLTemplate("project/admin/project_template_cat/list.html");	
	
		$paramquery = $_GET;
		$paramquery[action] = "add";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
	
		$template->set_var("list", $list->get_list());		
		
		$template->output();
	}

	public static function create()
	{
		if ($_GET[nextpage] == 1)
		{
			$page_1_passed = true;
				
			if ($_POST[name])
			{
				if (ProjectTemplateCat::exist_name($_POST[name]) == true)
				{
					$page_1_passed = false;
					$error = "This name already exists";
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
			$template = new HTMLTemplate("project/admin/project_template_cat/add.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = "1";
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

			if ($_POST[name])
			{
				$template->set_var("name", $_POST[name]);
			}
			else
			{
				$template->set_var("name", "");	
			}
			
			$template->output();
		}
		else
		{				
			$project_template_cat = new ProjectTemplateCat(null);
								
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[nextpage]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($project_template_cat->create($_POST[name]))
			{
				Common_IO::step_proceed($params, "Add Project Template Categories", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Add Project Template Categories", "Operation Failed" ,null);	
			}
		}
	}
	
	/**
	 * @throws ProjectTemplateCategoryIDMissingException
	 */
	public static function delete()
	{
		if ($_GET[id])
		{
			if ($_GET[sure] != "true")
			{
				$template = new HTMLTemplate("project/admin/project_template_cat/delete.html");
				
				$paramquery = $_GET;
				$paramquery[sure] = "true";
				$params = http_build_query($paramquery);
				
				$template->set_var("yes_params", $params);
						
				$paramquery = $_GET;
				unset($paramquery[sure]);
				unset($paramquery[action]);
				unset($paramquery[id]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("no_params", $params);
				
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[sure]);
				unset($paramquery[action]);
				unset($paramquery[id]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$project_template_cat = new ProjectTemplateCat($_GET[id]);
				
				if ($project_template_cat->delete())
				{							
					Common_IO::step_proceed($params, "Delete Project Template Category", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Project Template Category", "Operation Failed" ,null);
				}			
			}
		}
		else
		{
			throw new ProjectTemplateCategoryIDMissingException();
		}
	}
	
	/**
	 * @throws ProjectTemplateCategoryIDMissingException
	 */
	public static function edit()
	{
		if ($_GET[id])
		{
			$project_template_cat = new ProjectTemplateCat($_GET[id]);
		
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST[name])
				{
					if (ProjectTemplateCat::exist_name($_POST[name]) == true and $project_template_cat->get_name() != $_POST[name])
					{
						$page_1_passed = false;
						$error = "This name already exists";
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
				$template = new HTMLTemplate("project/admin/project_template_cat/edit.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
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
													 
				if ($_POST[name])
				{
					$template->set_var("name", $_POST[name]);
				}
				else
				{
					$template->set_var("name", $project_template_cat->get_name());
				}		
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[nextpage]);
				unset($paramquery[action]);
				$params = http_build_query($paramquery);
				
				if ($project_template_cat->set_name($_POST[name]))
				{
					Common_IO::step_proceed($params, "Edit Project Template Category", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Edit Project Tempalte Category", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new ProjectTemplateCategoryIDMissingException();
		}
	}
	
	public static function handler()
	{
		switch($_GET[action]):
			case "add":
				self::create();
			break;

			case "delete":
				self::delete();
			break;

			case "edit":
				self::edit();
			break;	
				
			default:
				self::home();
			break;
		endswitch;
	}
	
}

?>