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
 * Sample Search IO Class
 * @package sample
 */
class SampleSearchIO
{
	/**
	 * @param integer $lanugage_id
	 * @return string
	 */
	public static function get_description($language_id)
	{
		return "Finds Samples in Organisation Units via Name, ID and/or Template.";
	}
	
	/**
	 * @return string
	 */
	public static function get_icon()
	{
		return "images/icons_large/sample_search_50.png";
	}
	
	/**
	 * @todo permission check
	 */
	public static function search()
	{
		global $user, $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[page] or $_GET[sortvalue] or $_GET[sortmethod])
			{
				$name = $session->read_value("SEARCH_SAMPLE_NAME");
				$organisation_unit_array = $session->read_value("SEARCH_SAMPLE_ORGANISATION_UNIT_ARRAY");
				$template_array = $session->read_value("SEARCH_SAMPLE_TEMPLATE_ARRAY");
				$in_id = $session->read_value("SEARCH_SAMPLE_IN_ID");
				$in_name = $session->read_value("SEARCH_SAMPLE_IN_NAME");
			}
			else
			{
				if ($_GET[nextpage] == "1")
				{
					$name = $_POST[name];
					$session->delete_value("SEARCH_SAMPLE_NAME");
					$session->delete_value("SEARCH_SAMPLE_ORGANISATION_UNIT_ARRAY");
					$session->delete_value("SEARCH_SAMPLE_TEMPLATE_ARRAY");
					$session->delete_value("SEARCH_SAMPLE_IN_ID");
					$session->delete_value("SEARCH_SAMPLE_IN_NAME");
				}
				else
				{
					$name = $_POST[name];
					$organisation_unit_array = $session->read_value("SEARCH_SAMPLE_ORGANISATION_UNIT_ARRAY");
					$template_array = $session->read_value("SEARCH_SAMPLE_TEMPLATE_ARRAY");
					$in_id = $session->read_value("SEARCH_SAMPLE_IN_ID");
					$in_name = $session->read_value("SEARCH_SAMPLE_IN_NAME");
				}
			}
			$no_error = true;
		}
		else
		{
			$no_error = false;
		}
		
		if ($no_error == false)
		{
			$template = new HTMLTemplate("sample/search/search.html");
			
			$paramquery = $_GET;
			unset($paramquery[page]);
			$paramquery[nextpage] = "1";
			$params = http_build_query($paramquery,'','&#38;');
					
			$template->set_var("params",$params);
			
			$template->set_var("error", "");
			
			$result = array();
			$counter = 0;
			
			$organisation_unit_array = OrganisationUnit::list_entries();
			
			if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
			{
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
			
					if ($organisation_unit->is_permission($user->get_user_id()))
					{
						$result[$counter][value] = $value;
						$result[$counter][content] = $organisation_unit->get_name();		
						$result[$counter][selected] = "";
		
						$counter++;
					}
				}
			}
			
			if (!$result)
			{
				$result[$counter][value] = "0";
				$result[$counter][content] = "NO ORGANISATION UNIT FOUND!";
			}
			
			$template->set_var("organ_unit",$result);
			
			
			$result = array();
			$counter = 0;
				
			$sample_template_array = SampleTemplateCat::list_entries();
			
			if (is_array($sample_template_array))
			{
				foreach($sample_template_array as $key => $value)
				{
					$sample_template_cat = new SampleTemplateCat($value);					
					$result[$counter][value] = "0";
					$result[$counter][content] = $sample_template_cat->get_name();		
					$result[$counter][selected] = "";
	
					$counter++;
					
					$sample_template_sub_array =  SampleTemplate::list_entries_by_cat_id($value);
					
					if (is_array($sample_template_sub_array))
					{
						foreach($sample_template_sub_array as $sub_key => $sub_value)
						{
							$sample_sub_template = new SampleTemplate($sub_value);
							
							$result[$counter][value] = $sub_value;
							$result[$counter][content] = "&nbsp;".$sample_sub_template->get_name();		
							$result[$counter][selected] = "";
		
							$counter++;
						}
					}
					unset($sample_template_sub_array);
				}
			}
			else
			{
				$result[$counter][value] = "0";
				$result[$counter][content] = "NO TEMPLATES FOUND!";	
			}
	
			$template->set_var("template",$result);
			
			$template->output();
		}
		else
		{
			if(!$organisation_unit_array)
			{			
				if (!$_POST[organisation_unit])
				{
					$organisation_unit_array = array();
					
					$organisation_unit_array = OrganisationUnit::list_entries();
					
					if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
					{
						foreach($organisation_unit_array as $key => $value)
						{
							$organisation_unit = new OrganisationUnit($value);
					
							if ($organisation_unit->is_permission($user->get_user_id()))
							{
								array_push($organisation_unit_array, $value);
							}
						}
					}
					$search_organisation_unit_name = "All";
				}
				else
				{
					$organisation_unit_array = array();
					$organisation_unit_array[0] = $_POST[organisation_unit];
					$organisation_unit = new OrganisationUnit($_POST[organisation_unit]);
					$search_organisation_unit_name = $organisation_unit->get_name();
				}
			}
			else
			{
				if (count($organisation_unit_array) == 1)
				{
					$organisation_unit = new OrganisationUnit($organisation_unit_array[0]);
					$search_organisation_unit_name = $organisation_unit->get_name();
				}
				else
				{
					$search_organisation_unit_name = "All";
				}
			}
			
			if (!$template_array)
			{
				if (!$_POST[template])
				{
					$template_array = null;
					$search_template_name = "All";
				}
				else
				{
					$template_array = array();
					$template_array[0] = $_POST[template];
					$sample_template = new SampleTemplate($_POST[template]);
					$search_template_name = $sample_template->get_name();
				}
			}
			
			if (!isset($in_id))
			{
				if ($_POST[in_id] == 1)
				{
					$in_id = true;
				}
				else
				{
					$in_id = false;
				}
			}
			
			if (!isset($in_name))
			{
				if ($_POST[in_name] == 1)
				{
					$in_name = true;
				}
				else
				{
					$in_name = false;
				}
			}

			$session->write_value("SEARCH_SAMPLE_NAME", $name, true);
			$session->write_value("SEARCH_SAMPLE_ORGANISATION_UNIT_ARRAY", $organisation_unit_array, true);
			$session->write_value("SEARCH_SAMPLE_TEMPLATE_ARRAY", $template_array, true);
			$session->write_value("SEARCH_SAMPLE_IN_ID", $in_id, true);
			$session->write_value("SEARCH_SAMPLE_IN_NAME", $in_name, true);

			/* --------------- */
			
			
			$argument_array = array();
			$argument_array[0][0] = "name";
			$argument_array[0][1] = $name;
			$argument_array[1][0] = "organisation_unit_array";
			$argument_array[1][1] = $organisation_unit_array;
			$argument_array[2][0] = "template_array";
			$argument_array[2][1] = $template_array;
			$argument_array[3][0] = "in_id";
			$argument_array[3][1] = $in_id;
			$argument_array[4][0] = "in_name";
			$argument_array[4][1] = $in_name;
					
			$list = new List_IO("SampleSearch", "ajax.php?nav=sample", "search_sample_list_samples", "search_sample_count_samples", $argument_array, "SampleSearch");
		
			$list->add_column("","symbol",false,"16px");
			$list->add_column("Smpl. ID","id",true,"11%");
			$list->add_column("Sample Name","name",true,null);
			$list->add_column("Date","datetime",true,null);
			$list->add_column("Type/Tmpl.","template",true,null);
			$list->add_column("Curr. Loc.","location",true,null);
			$list->add_column("AV","av",false,"16px");
			
			$template = new HTMLTemplate("sample/search/search_result.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			unset($paramquery[page]);
			unset($paramquery[sortvalue]);
			unset($paramquery[sortmethod]);
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("name", $name);
			$template->set_var("organisation_units", $search_organisation_unit_name);
			$template->set_var("templates", $search_template_name);
				
			$template->set_var("list", $list->get_list());		
	
			$template->output();	
		}
	}
}