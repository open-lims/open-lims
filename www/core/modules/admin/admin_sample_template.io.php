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
 * Sample Template Admin IO Class
 * @package sample
 */
class AdminSampleTemplateIO
{
	public static function home()
	{
		$list = new List_IO(Sample_Wrapper::count_list_sample_templates(), 20);
		
		$list->add_row("ID", "id", true, null);
		$list->add_row("Name", "name", true, null);
		$list->add_row("File", "file", true, null);
		$list->add_row("Category", "category", true, null);
		$list->add_row("Delete", "delete", false, "7%");
		
		if ($_GET[page])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = Sample_Wrapper::list_sample_templates($_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
			}
			else
			{
				$result_array = Sample_Wrapper::list_sample_templates(null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
			}				
		}
		else
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = Sample_Wrapper::list_sample_templates($_GET[sortvalue], $_GET[sortmethod], 0, 20);
			}
			else
			{
				$result_array = Sample_Wrapper::list_sample_templates(null, null, 0, 20);
			}	
		}
		
		if (is_array($result_array) and count($result_array) >= 1)
		{		
			foreach($result_array as $key => $value)
			{
				$paramquery = $_GET;
				$paramquery[id] = $result_array[$key][id];
				$paramquery[action] = "delete";
				unset($paramquery[sortvalue]);
				unset($paramquery[sortmethod]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery, '', '&#38;');

				$result_array[$key][delete][link] = $params;
				$result_array[$key][delete][content] = "delete";
			}
		}
		else
		{
			$list->override_last_line("<span class='italic'>No results found!</span>");
		}
		
		$template = new Template("languages/en-gb/template/admin/sample_template/list.html");	
	
		$paramquery = $_GET;
		$paramquery[action] = "add";
		unset($paramquery[nextpage]);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
	
		$template->set_var("table", $list->get_list($result_array, $_GET[page]));			
		
		$template->output();
	}

	public static function create()
	{
		global $common;

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
			$template = new Template("languages/en-gb/template/admin/sample_template/add.html");
			
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

			$file_array = Object::get_file_array($GLOBALS[oldl_folder_id]);
			
			if (is_array($file_array))
			{								
				$result = array();
				$counter = 0;
				
				foreach($file_array as $key => $value)
				{
					$file = new File($value);
					$result[$counter][value] = $file->get_object_id();
					$result[$counter][content] = $file->get_name();
					$counter++;
				}
				$template->set_var("file",$result);
			}
			
			$category_array = SampleTemplateCat::list_entries();
			
			if (is_array($category_array))
			{	
				$result = array();
				$counter = 0;
				
				foreach($category_array as $key => $value)
				{
					$sample_template_cat = new SampleTemplateCat($value);				
					$result[$counter][value] = $value;
					$result[$counter][content] = $sample_template_cat->get_name();
					$counter++;
				}
				$template->set_var("category",$result);
			}
			$template->output();
		}
		else
		{			
			$sample_template = new SampleTemplate(null);
								
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[nextpage]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($sample_template->create($_POST[object_id], $_POST[category_id]))
			{
				$common->step_proceed($params, "Add Sample Template", "Operation Successful", null);
			}
			else
			{
				$common->step_proceed($params, "Add Sample Template", "Operation Failed" ,null);	
			}
		}
	}
	
	public static function delete()
	{
		global $common;
		
		if ($_GET[id])
		{
			if ($_GET[sure] != "true")
			{
				$template = new Template("languages/en-gb/template/admin/sample_template/delete.html");
				
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
				
				$sample_template = new SampleTemplate($_GET[id]);
				
				if ($sample_template->delete())
				{							
					$common->step_proceed($params, "Delete Sample Template", "Operation Successful" ,null);
				}
				else
				{							
					$common->step_proceed($params, "Delete Sample Template", "Operation Failed" ,null);
				}		
			}
		}
		else
		{
			$exception = new Exception("", 3);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function handler()
	{
		try
		{
			if ($_GET[id])
			{
				if (SampleTemplate::exist_id($_GET[id]) == false)
				{
					throw new Exception("",3);
				}
			}
			
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
		catch (Exception $e)
		{
			$error_io = new Error_IO($e, 250, 40, 1);
			$error_io->display_error();
		}
	}
	
}

?>
