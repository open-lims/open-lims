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
 * Project Template Admin IO Class
 * @package project
 */
class AdminProjectTemplateIO
{
	public static function home()
	{
		$list = new List_IO("ProjectAdminTemplate", "ajax.php?nav=project", "admin_project_template_list_templates", "admin_project_template_count_templates", "0", "ProjectAdminTemplate");
		
		$list->add_column("ID", "id", true, null);
		$list->add_column("Name", "name", true, null);
		$list->add_column("File", "file", true, null);
		$list->add_column("Category", "category", true, null);
		$list->add_column("Par.", "parent", true, null);
		$list->add_column("Delete", "delete", false, "7%");

		$template = new HTMLTemplate("project/admin/project_template/list.html");	
	
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
		}
		else
		{
			$page_1_passed = false;
			$error = "";
		}

		if ($page_1_passed == false)
		{
			$template = new HTMLTemplate("project/admin/project_template/add.html");
			
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
			
			$folder = Folder::get_instance(constant("OLDL_FOLDER_ID"));
			$data_entity_array = $folder->get_children();
			
			if (is_array($data_entity_array))
			{								
				$result = array();
				$counter = 0;
				
				foreach($data_entity_array as $key => $value)
				{
					if (($file_id = File::get_file_id_by_data_entity_id($value)) != null)
					{
						$file = File::get_instance($file_id);
						$result[$counter][value] = $value;
						$result[$counter][content] = $file->get_name();
						$counter++;
					}
				}
				$template->set_var("file",$result);
			}
			
			$category_array = ProjectTemplateCat::list_entries();
			
			if (is_array($category_array))
			{	
				$result = array();
				$counter = 0;
				
				foreach($category_array as $key => $value)
				{
					$project_template_cat = new ProjectTemplateCat($value);
					$result[$counter][value] = $value;
					$result[$counter][content] = $project_template_cat->get_name();
					$counter++;
				}
				$template->set_var("category",$result);
			}
			
			$template->output();
		}
		else
		{				
			$project_template = new ProjectTemplate(null);
				
			if ($_POST[parent] == "1")
			{
				$parent = true;
			}	
			else
			{
				$parent = false;
			}
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[nextpage]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($project_template->create($_POST[data_entity_id], $_POST[category_id], $parent))
			{
				Common_IO::step_proceed($params, "Add Project Template", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Add Project Template", "Operation Failed" ,null);	
			}
		}
	}
	
	/**
	 * @throws ProjectTemplateIDMissingException
	 */
	public static function delete()
	{
		if ($_GET[id])
		{
			if ($_GET[sure] != "true")
			{
				$template = new HTMLTemplate("project/admin/project_template/delete.html");
				
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
				
				$project_template = new ProjectTemplate($_GET[id]);
				
				if ($project_template->delete())
				{							
					Common_IO::step_proceed($params, "Delete Project Template", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Project Template", "Operation Failed" ,null);
				}		
			}
		}
		else
		{
			throw new ProjectTemplateIDMissingException();
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
						
			default:
				self::home();
			break;
		endswitch;
	}
	
}

?>
