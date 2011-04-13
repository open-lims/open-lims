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
 * Sample Search IO Class
 * @package sample
 */
class SampleSearchIO
{
	public static function get_description($language_id)
	{
		return "Finds Samples in Organisation Units via Name, ID and/or Template.";
	}
	
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
			$template = new Template("languages/en-gb/template/samples/search/search.html");
			
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
			
			$template->set_array("organ_unit",$result);
			
			
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
	
			$template->set_array("template",$result);
			
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
					$project_template = new ProjectTemplate($_POST[template]);
					$search_template_name = $project_template->get_name();
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
			
			$list = new List_IO(Sample_Wrapper::count_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name), 20);

			$list->add_row("","symbol",false,"16px");
			$list->add_row("Smpl. ID","id",true,"11%");
			$list->add_row("Sample Name","name",true,null);
			$list->add_row("Date","datetime",true,null);
			$list->add_row("Type/Tmpl.","template",true,null);
			$list->add_row("Curr. Depos.","depository",true,null);
			$list->add_row("AV","av",false,"16px");
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = Sample_Wrapper::list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = Sample_Wrapper::list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, null, null, 0, 20);
				}	
			}

			if (is_array($result_array) and count($result_array) >= 1)
			{
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");
	
					if ($result_array[$key][av] == "f")
					{
						$result_array[$key][av] = "<img src='images/icons/grey_point.png' alt='' />";
					}
					else
					{
						if ($result_array[$key][date_of_expiry] and $result_array[$key][expiry_warning])
						{
							$date_of_expiry = new DatetimeHandler($result_array[$key][date_of_expiry]." 23:59:59");
							$warning_day = clone $date_of_expiry;
							$warning_day->sub_day($result_array[$key][expiry_warning]);
						
							if ($date_of_expiry->distance($today_end) > 0)
							{
								$result_array[$key][av] = "<img src='images/icons/red_point.png' alt='' />";
							}
							else
							{
								if ($warning_day->distance($today_end) > 0)
								{
									$result_array[$key][av] = "<img src='images/icons/yellow_point.png' alt='' />";
								}
								else
								{
									$result_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
								}
							}
						}
						else
						{
							$result_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
						}
					}
					
					if (strlen($result_array[$key][name]) > 17)
					{
						$result_array[$key][name] = substr($result_array[$key][name],0,17)."...";
					}
					else
					{
						$result_array[$key][name] = $result_array[$key][name];
					}
					
					if (strlen($result_array[$key][template]) > 25)
					{
						$result_array[$key][template] = substr($result_array[$key][template],0,25)."...";
					}
					else
					{
						$result_array[$key][template] = $result_array[$key][template];
					}
					
					$sample_id = $result_array[$key][id];
					$sample_security = new SampleSecurity($sample_id);
					
					if ($sample_security->is_access(1, false))
					{
						$paramquery = array();
						$paramquery[username] = $_GET[username];
						$paramquery[session_id] = $_GET[session_id];
						$paramquery[nav] = "sample";
						$paramquery[run] = "detail";
						$paramquery[sample_id] = $sample_id;
						$params = http_build_query($paramquery,'','&#38;');
						
						$result_array[$key][symbol][link]		= $params;
						$result_array[$key][symbol][content] 	= "<img src='images/icons/sample.png' alt='' style='border:0;' />";
					
						unset($result_array[$key][id]);
						$result_array[$key][id][link] 			= $params;
						$result_array[$key][id][content]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
					
						$sample_name = $result_array[$key][name];
						unset($result_array[$key][name]);
						$result_array[$key][name][link] 		= $params;
						$result_array[$key][name][content]		= $sample_name;
					}
					else
					{
						$result_array[$key][symbol]	= "<img src='core/images/denied_overlay.php?image=images/icons/sample.png' alt='N' border='0' />";
						$result_array[$key][id]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
					}
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			$template = new Template("languages/en-gb/template/samples/search/search_result.html");
			
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
				
			$template->set_var("table", $list->get_list($result_array, $_GET[page]));		
	
			$template->output();	
		}
	}
}