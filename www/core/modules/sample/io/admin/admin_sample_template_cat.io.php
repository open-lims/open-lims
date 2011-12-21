<?php
/**
 * @package sample
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
 * Sample Template Category Admin IO Class
 * @package sample
 */
class AdminSampleTemplateCatIO
{
	public static function home()
	{
		$list = new ListStat_IO(Sample_Wrapper::count_list_sample_template_categories(), 20);
		
		$list->add_column("Name", "name", true, null);
		$list->add_column("Edit", "edit", false, "10%");
		$list->add_column("Delete", "delete", false, "10%");
		
		if ($_GET[page])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = Sample_Wrapper::list_sample_template_categories($_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
			}
			else
			{
				$result_array = Sample_Wrapper::list_sample_template_categories(null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
			}				
		}
		else
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = Sample_Wrapper::list_sample_template_categories($_GET[sortvalue], $_GET[sortmethod], 0, 20);
			}
			else
			{
				$result_array = Sample_Wrapper::list_sample_template_categories(null, null, 0, 20);
			}	
		}
		
		if (is_array($result_array) and count($result_array) >= 1)
		{	
			foreach($result_array as $key => $value)
			{
				$paramquery = $_GET;
				$paramquery[id] = $result_array[$key][id];
				$paramquery[action] = "edit";
				unset($paramquery[sortvalue]);
				unset($paramquery[sortmethod]);
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery, '', '&#38;');

				$result_array[$key][edit][link] = $params;
				$result_array[$key][edit][content] = "edit";
				
				
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
		
		$template = new Template("template/sample/admin/sample_template_cat/list.html");	
	
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
		if ($_GET[nextpage] == 1)
		{
			$page_1_passed = true;
				
			if ($_POST[name])
			{
				if (SampleTemplateCat::exist_name($_POST[name]) == true)
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
			$template = new Template("template/sample/admin/sample_template_cat/add.html");
			
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
			$sample_template_cat = new SampleTemplateCat(null);
								
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[nextpage]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($sample_template_cat->create($_POST[name]))
			{
				Common_IO::step_proceed($params, "Add Sample Template Categories", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Add Sample Template Categories", "Operation Failed" ,null);	
			}
		}
	}
	
	/**
	 * @throws SampleTemplateCategoryIDMissingException
	 */
	public static function delete()
	{
		if ($_GET[id])
		{
			if ($_GET[sure] != "true")
			{
				$template = new Template("template/sample/admin/sample_template_cat/delete.html");
				
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
				
				$sample_template_cat = new SampleTemplateCat($_GET[id]);
				
				if ($sample_template_cat->delete())
				{							
					Common_IO::step_proceed($params, "Delete Sample Template Category", "Operation Successful" ,null);
				}
				else
				{							
					Common_IO::step_proceed($params, "Delete Sample Template Category", "Operation Failed" ,null);
				}	
			}
		}
		else
		{
			throw new SampleTemplateCategoryIDMissingException();
		}
	}
	
	/**
	 * @throws SampleTemplateCategoryIDMissingException
	 */
	public static function edit()
	{
		if ($_GET[id])
		{
			$sample_template_cat = new SampleTemplateCat($_GET[id]);
		
			if ($_GET[nextpage] == 1)
			{
				$page_1_passed = true;
				
				if ($_POST[name])
				{
					if (SampleTemplateCat::exist_name($_POST[name]) == true and $sample_template_cat->get_name() != $_POST[name])
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
				$template = new Template("template/sample/admin/sample_template_cat/edit.html");
				
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
					$template->set_var("name", $sample_template_cat->get_name());
				}
							
				$template->output();
			}
			else
			{
				$paramquery = $_GET;
				unset($paramquery[nextpage]);
				unset($paramquery[action]);
				$params = http_build_query($paramquery);
				
				if ($sample_template_cat->set_name($_POST[name]))
				{
					Common_IO::step_proceed($params, "Edit Sample Template Category", "Operation Successful", null);
				}
				else
				{
					Common_IO::step_proceed($params, "Edit Sample Tempalte Category", "Operation Failed" ,null);	
				}
			}
		}
		else
		{
			throw new SampleTemplateCategoryIDMissingException();
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