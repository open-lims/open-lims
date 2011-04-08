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
	/**
	 * @todo use ListIO
	 * @todo use SQL Join for permission check
	 */
	public static function search()
	{
		global $user, $session;
		
		if ($_GET[nextpage])
		{
			if ($_GET[page])
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

			$sample = new Sample(null);
			$sample_array = $sample->search_samples($name, $organisation_unit_array, $template_array, $in_id, $in_name);
			
			/* --------------- */
			
			$content_array = array();
		
			$table_io = new TableIO("OverviewTable");
			
			$table_io->add_row("","symbol",false,16);
			$table_io->add_row("Sample ID","id",false,null);
			$table_io->add_row("Sample Name","name",false,null);
			$table_io->add_row("Date/Time","datetime",false,null);
			$table_io->add_row("Type/Template","template",false,null);
			$table_io->add_row("Current Depository","depository",false,null);
			$table_io->add_row("Owner","owner",false,null);
			$table_io->add_row("AV","av",false,null);
			
			$sample_array_cardinality = count($sample_array);
			
			$counter = 0;
	
			if (!$_GET[page] or $_GET[page] == 1)
			{
				$page = 1;
				$counter_begin = 0;
				if ($sample_array_cardinality > 25)
				{
					$counter_end = 24;
				}
				else
				{
					$counter_end = $sample_array_cardinality-1;
				}
			}
			else
			{
				if ($_GET[page] >= ceil($sample_array_cardinality/25))
				{
					$page = ceil($sample_array_cardinality/25);
					$counter_end = $sample_array_cardinality;
				}
				else
				{
					$page = $_GET[page];
					$counter_end = (25*$page)-1;
				}
				$counter_begin = (25*$page)-25;
			}

			if (is_array($sample_array) and count($sample_array) >= 1)
			{
				foreach ($sample_array as $key => $value)
				{
					if ($counter >= $counter_begin and $counter <= $counter_end)
					{
						$sample	= new Sample($value);
						$sample_security = new SampleSecurity($value);
						$owner = new User($sample->get_owner_id());
						
						$column_array = array();
						
						$paramquery = $_GET;
						$paramquery[nav] = "sample";
						$paramquery[run] = "detail";
						$paramquery[sample_id] = $value;
						unset($paramquery[page]);
						$params = http_build_query($paramquery,'','&#38;');
						
						if ($sample_security->is_access(1, false) == true)
						{					
							$column_array[symbol][link] 	= $params;
							$column_array[symbol][content] 	= "<img src='images/icons/sample.png' alt='' style='border: 0;' />";	
							$column_array[id][link] 		= $params;
						}
						else
						{
							$column_array[symbol][link]		= "";
							$column_array[symbol][content]	= "<img src='core/images/denied_overlay.php?image=images/icons/sample.png' alt='N' border='0' />";
							$column_array[id][link]			= "";
						}
						
						$column_array[id][content] 		= $sample->get_formatted_id();	
						$column_array[name][content] 	= $sample->get_name();	
						$column_array[datetime] 		= $sample->get_datetime();
						$column_array[template]			= $sample->get_template_name();
						$column_array[depository]		= $sample->get_current_depository_name();
						$column_array[owner] 			= $owner->get_full_name(true);
						
						if ($sample->get_availability() == true)
						{
							$column_array[av]			= "<img src='images/icons/green_point.png' alt='' />";
						}
						else
						{
							$column_array[av]			= "<img src='images/icons/grey_point.png' alt='' />";
						}
						array_push($content_array, $column_array);	
					}
					$counter++;
				}
			}
			else
			{
				$content_array = null;
				$table_io->override_last_line("<span class='italic'>No Samples Found!</span>");
			}
			
			$template = new Template("languages/en-gb/template/samples/search/search_result.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("name", $name);
			$template->set_var("organisation_units", $search_organisation_unit_name);
			$template->set_var("templates", $search_template_name);
			
			$table_io->add_content_array($content_array);	
				
			$template->set_var("table", $table_io->get_table($page ,$sample_array_cardinality));		
	
			$template->output();	
		}
	}
}