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
 * Sample IO Class
 * @package sample
 */
class SampleIO
{
	/**
	 * NEW
	 */
	public static function list_user_related_samples($user_id)
	{
		global $user;
		
		$list = new List_IO(Sample_Wrapper::count_user_samples($user->get_user_id()), 20);

		$list->add_row("","symbol",false,"16px");
		$list->add_row("Smpl. ID","id",true,"11%");
		$list->add_row("Sample Name","name",true,null);
		$list->add_row("Date/Time","datetime",true,null);
		$list->add_row("Type/Tmpl.","template",true,null);
		$list->add_row("Curr. Loc.","location",true,null);
		$list->add_row("AV","av",false,"16px");
		
		if ($_GET[page])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = Sample_Wrapper::list_user_samples($user->get_user_id(), $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
			}
			else
			{
				$result_array = Sample_Wrapper::list_user_samples($user->get_user_id(), null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
			}				
		}
		else
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = Sample_Wrapper::list_user_samples($user->get_user_id(), $_GET[sortvalue], $_GET[sortmethod], 0, 20);
			}
			else
			{
				$result_array = Sample_Wrapper::list_user_samples($user->get_user_id(), null, null, 0, 20);
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
		
		$template = new Template("languages/en-gb/template/samples/list_user.html");	

		$template->set_var("table", $list->get_list($result_array, $_GET[page]));
		
		$template->output();
	}
	
	/**
	 * NEW
	 */
	public static function list_organisation_unit_related_samples()
	{
		if ($_GET[ou_id])
		{
			$list = new List_IO(Sample_Wrapper::count_organisation_unit_samples($_GET[ou_id]), 12);

			$list->add_row("","symbol",false,"16px");
			$list->add_row("Smpl. ID","id",true,"11%");
			$list->add_row("Sample Name","name",true,null);
			$list->add_row("Date/Time","datetime",true,null);
			$list->add_row("Type/Tmpl.","template",true,null);
			$list->add_row("Curr. Loc.","location",true,null);
			$list->add_row("AV","av",false,"16px");
		
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_organisation_unit_samples($_GET[ou_id], $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*12)-12, ($_GET[page]*12));
				}
				else
				{
					$result_array = Sample_Wrapper::list_organisation_unit_samples($_GET[ou_id], null, null, ($_GET[page]*12)-12, ($_GET[page]*12));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_organisation_unit_samples($_GET[ou_id], $_GET[sortvalue], $_GET[sortmethod], 0, 12);
				}
				else
				{
					$result_array = Sample_Wrapper::list_organisation_unit_samples($_GET[ou_id], null, null, 0, 12);
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
			
			require_once("core/modules/organisation_unit/organisation_unit.io.php");
			$organisation_unit_io = new OrganisationUnitIO;
			$organisation_unit_io->detail();
			
			$template = new Template("languages/en-gb/template/samples/list.html");

			$template->set_var("table", $list->get_list($result_array, $_GET[page]));
			
			$template->output();
		}
		else
		{
			// Error
		}
	}
	
	/**
	 * NEW
	 * @todo error
	 */
	public static function list_samples_by_item_id($item_id)
	{
		if (is_numeric($item_id))
		{
			$list = new List_IO(Sample_Wrapper::count_samples_by_item_id($item_id), 20);

			$list->add_row("","symbol",false,"16px");
			$list->add_row("Smpl. ID","id",true,"11%");
			$list->add_row("Sample Name","name",true,null);
			$list->add_row("Date","datetime",true,null);
			$list->add_row("Type/Tmpl.","template",true,null);
			$list->add_row("Curr. Loc.","location",true,null);
			$list->add_row("Owner","owner",true,null);
			$list->add_row("AV","av",false,"16px");
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_samples_by_item_id($item_id, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = Sample_Wrapper::list_samples_by_item_id($item_id, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_samples_by_item_id($item_id, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = Sample_Wrapper::list_samples_by_item_id($item_id, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				$today_begin = new DatetimeHandler(date("Y-m-d")." 00:00:00");
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");
				
					if ($result_array[$key][owner])
					{
						$user = new User($result_array[$key][owner]);
					}
					else
					{
						$user = new User(1);
					}
					
					$result_array[$key][owner] = $user->get_full_name(true);
					
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
			
			$template = new Template("languages/en-gb/template/samples/list_parents.html");

			$template->set_var("table", $list->get_list($result_array, $_GET[page]));
			
			$template->output();
		}
		else
		{
			// Error
		}
	}
	
	/**
	 * NEW
	 */
	public static function list_sample_items($sql)
	{
		if ($sql)
		{
			$list = new List_IO(Sample_Wrapper::count_item_samples($sql), 20);

			$list->add_row("","symbol",false,"16px");
			$list->add_row("Smpl. ID","id",true,"11%");
			$list->add_row("Sample Name","name",true,null);
			$list->add_row("Date","datetime",true,null);
			$list->add_row("Type/Tmpl.","template",true,null);
			$list->add_row("Curr. Loc.","location",true,null);
			$list->add_row("Owner","owner",true,null);
			$list->add_row("AV","av",false,"16px");
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_item_samples($sql, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = Sample_Wrapper::list_item_samples($sql, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Sample_Wrapper::list_item_samples($sql, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = Sample_Wrapper::list_item_samples($sql, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");
				
					if ($result_array[$key][owner])
					{
						$user = new User($result_array[$key][owner]);
					}
					else
					{
						$user = new User(1);
					}
					
					$result_array[$key][owner] = $user->get_full_name(true);
					
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
			
			$template = new Template("languages/en-gb/template/samples/list.html");

			$add_sample_paramquery = $_GET;
			$add_sample_paramquery[username] = $_GET[username];
			$add_sample_paramquery[session_id] = $_GET[session_id];
			$add_sample_paramquery[run] = "item_add";
			$add_sample_paramquery[dialog] = "sample";
			$add_sample_paramquery[retrace] = Misc::create_retrace_string();
			unset($add_sample_paramquery[key]);
			unset($add_sample_paramquery[nextpage]);
			$add_sample_params = http_build_query($add_sample_paramquery,'','&#38;');
			
			$template->set_var("add_sample_params", $add_sample_params);
			$template->set_var("table", $list->get_list($result_array, $_GET[page]));
			
			$template->output();
		}
		else
		{
			// Error
		}
	}
	
	/**
	 * NEW
	 */
	public static function create($type_array, $category_array, $organisation_unit_id)
	{
		global $user, $session, $sample_security;
		
		try
		{
			if(($_GET[run] == "new_sample_sample" or $_GET[run] == "new_parent_sample") and $_GET[sample_id])
			{
				$sample_id = $_GET[sample_id];
				
				$sample_item = new SampleItem($sample_id);
				$sample_item->set_gid($_GET[key]);
				
				$description_required = $sample_item->is_description();
				$keywords_required = $sample_item->is_keywords();
			}
					
			if (!$_GET[nextpage])
			{
				$session->delete_value("SAMPLE_LAST_SCREEN");
				$session->delete_value("SAMPLE_CURRENT_SCREEN");
				
				$session->delete_value("SAMPLE_ORGAN_UNIT");
				$session->delete_value("SAMPLE_TEMPLATE");
				$session->delete_value("SAMPLE_NAME");
				$session->delete_value("SAMPLE_MANUFACTURER");
				$session->delete_value("SAMPLE_MANUFACTURER_NAME");
				$session->delete_value("SAMPLE_LOCATION");
				$session->delete_value("SAMPLE_EXPIRY");
				$session->delete_value("SAMPLE_EXPIRY_WARNING");
				$session->delete_value("SAMPLE_DESC");		
				$session->delete_value("SAMPLE_TEMPLATE_DATA_TYPE");
				$session->delete_value("SAMPLE_TEMPLATE_DATA_TYPE_ID");	
				$session->delete_value("SAMPLE_TEMPALTE_DATA_ARRAY");
				
				if($_GET[run] == "item_add")
				{	
					$session->write_value("SAMPLE_ORGAN_UNIT", $organisation_unit_id, true);
					$session->write_value("SAMPLE_CURRENT_SCREEN", 2, true);
				}
			}
			else
			{
				$sample_template		= $session->read_value("SAMPLE_TEMPLATE");
				$sample_template_obj 	= new SampleTemplate($sample_template);
				if ($sample_template_obj->is_required_requirements() == true)
				{
					$sample_template_specific_information = true;
				}
				else
				{
					$sample_template_specific_information = false;
				}
			}	
	
	
			switch ($_GET[nextpage]) :
				case 1:
					if (!$_GET[tpage])
					{
						if (is_numeric($_POST[organ_unit]) and $_POST[organ_unit] != 0)
						{
							$session->write_value("SAMPLE_ORGAN_UNIT",$_POST[organ_unit], true);
							$session->write_value("SAMPLE_CURRENT_SCREEN", 2, true);
						}
						else
						{
							$error[0] = "Select an orangisation unit!";
						}
					}
					else
					{
						$session->write_value("SAMPLE_CURRENT_SCREEN", $_GET[tpage], true);
						if (is_numeric($_POST[organ_unit]))
						{
							$session->write_value("SAMPLE_ORGAN_UNIT",$_POST[organ_unit], true);	
						}
						unset($_GET[tpage]);
					}
				break;
				
				case 2:
					if (!$_GET[tpage])
					{
						if ($_POST[submitbutton] == "previous")
						{
							$session->write_value("SAMPLE_CURRENT_SCREEN", 1, true);
							if (is_numeric($_POST[template]))
							{
								$session->write_value("SAMPLE_TEMPLATE",$_POST[template],true);
								$sample_template_obj = new SampleTemplate($_POST[template]);
							}
						}
						else
						{
							if (is_numeric($_POST[template]) and $_POST[template] != 0)
							{
								$session->write_value("SAMPLE_TEMPLATE",$_POST[template],true);
								$session->write_value("SAMPLE_CURRENT_SCREEN", 3, true);
								$sample_template_obj = new SampleTemplate($_POST[template]);
							}
							else
							{
								$error[0] = "Select a template!";
							}
						}
					}
					else
					{
						$session->write_value("SAMPLE_CURRENT_SCREEN", $_GET[tpage], true);
						if (is_numeric($_POST[template]))
						{
							$session->write_value("SAMPLE_TEMPLATE",$_POST[template],true);
						}
						unset($_GET[tpage]);
					}
				break;
				
				case 3:
					if (!$_GET[tpage])
					{
						if ($_POST[submitbutton] == "previous")
						{
							$session->write_value("SAMPLE_CURRENT_SCREEN", 2, true);
							if ($_POST[name])
							{
								$session->write_value("SAMPLE_NAME", $_POST[name], true);
							}
							if ($_POST[manufacturer])
							{
								$session->write_value("SAMPLE_MANUFACTURER", $_POST[manufacturer], true);
							}
							if ($_POST[manufacturer_name])
							{
								$session->write_value("SAMPLE_MANUFACTURER_NAME", $_POST[manufacturer_name], true);
							}
							if ($_POST[location])
							{
								$session->write_value("SAMPLE_LOCATION", $_POST[location], true);
							}
							if ($_POST[expiry])
							{
								$session->write_value("SAMPLE_EXPIRY", $_POST[expiry], true);
							}
							if ($_POST[expiry])
							{
								$session->write_value("SAMPLE_EXPIRY_WARNING", $_POST[expiry], true);
							}
							if ($_POST[desc])
							{
								$session->write_value("SAMPLE_DESC", $_POST[desc], true);
							}
						}
						else
						{
							$information_fields = $sample_template_obj->get_information_fields();

							if ($information_fields[manufacturer][name] and $information_fields[manufacturer][requirement] != "optional")
							{
								$check_manufacturer = true;
							}
							else
							{
								$check_manufacturer = false;
							}
							
							if ($information_fields[expiry][name] and $information_fields[expiry][requirement] != "optional")
							{
								$check_expiry = true;
							}
							else
							{
								$check_expiry = false;
							}
							
							if ($information_fields[location][name] and $information_fields[location][requirement] != "optional")
							{
								$check_location = true;
							}
							else
							{
								$check_location = false;
							}
							
							if (!$_POST[manufacturer] and $check_manufacturer == true)
							{
								$error[1] = "Enter a manufacturer!";	
							}
							if (!$_POST[expiry] and $check_expiry == true)
							{
								$error[2] = "Enter a date of expiry!";	
							}
							if (!$_POST[location] and $check_location == true)
							{
								$error[3] = "Select a location!";	
							}
							
							if ($_POST[name])
							{
								if (!$error[1] and !$error[2] and !$error[3])
								{
									if ($sample_template_specific_information == true)
									{
										$session->write_value("SAMPLE_CURRENT_SCREEN", 4, true);
									}
									else
									{
										$session->write_value("SAMPLE_CURRENT_SCREEN", 5, true);	
									}
								}
							}
							else
							{
								if (!$_POST[name])
								{
									$error[0] = "Enter a name!";	
								}
							}
							if ($_POST[name]) 
							{
								$session->write_value("SAMPLE_NAME", $_POST[name], true);
							}
							if ($_POST[manufacturer])
							{
								$session->write_value("SAMPLE_MANUFACTURER", $_POST[manufacturer], true);
							}
							if ($_POST[manufacturer_name])
							{
								$session->write_value("SAMPLE_MANUFACTURER_NAME", $_POST[manufacturer_name], true);
							}
							if ($_POST[location])
							{
								$session->write_value("SAMPLE_LOCATION", $_POST[location], true);
							}
							if ($_POST[expiry])
							{
								$session->write_value("SAMPLE_EXPIRY", $_POST[expiry], true);
							}
							if ($_POST[expiry_warning])
							{
								$session->write_value("SAMPLE_EXPIRY_WARNING", $_POST[expiry_warning], true);
							}
							if ($_POST[desc])
							{
								$session->write_value("SAMPLE_DESC", $_POST[desc], true);
							}
						}
					}
					else
					{
						$session->write_value("SAMPLE_CURRENT_SCREEN", $_GET[tpage], true);
						if ($_POST[name])
						{
							$session->write_value("SAMPLE_NAME", $_POST[name], true);
						}
						if ($_POST[manufacturer])
						{
							$session->write_value("SAMPLE_MANUFACTURER", $_POST[manufacturer], true);
						}
						if ($_POST[manufacturer_name])
						{
							$session->write_value("SAMPLE_MANUFACTURER_NAME", $_POST[manufacturer_name], true);
						}
						if ($_POST[location])
						{
							$session->write_value("SAMPLE_LOCATION", $_POST[location], true);
						}
						if ($_POST[expiry])
						{
							$session->write_value("SAMPLE_EXPIRY", $_POST[expiry], true);
						}
						if ($_POST[expiry_warning])
						{
							$session->write_value("SAMPLE_EXPIRY_WARNING", $_POST[expiry_warning], true);
						}
						if ($_POST[desc])
						{
							$session->write_value("SAMPLE_DESC", $_POST[desc], true);
						}
						unset($_GET[tpage]);
					}
				break;
				
				case 4:
					if (!$_GET[tpage])
					{
						if ($_POST[submitbutton] == "previous")
						{
							$session->write_value("SAMPLE_CURRENT_SCREEN", 3, true);
							if ($_POST[template_data_type])
							{
								$session->write_value("SAMPLE_TEMPLATE_DATA_TYPE", $_POST[template_data_type], true);	
								
								$template_data_array = array();
								
								foreach($_POST as $key => $value)
								{
									if (strpos($key, "-vartype") === false and $key != "submitbutton" and $key != "template_data_type")
									{
										$template_data_array[$key] = $value;
									}
									else
									{
										// type-check
									}
								}
								
								$session->write_value("SAMPLE_TEMPLATE_DATA_TYPE_ID", $_POST[template_data_type_id], true);
								$session->write_value("SAMPLE_TEMPALTE_DATA_ARRAY", $template_data_array, true);
							}
						}
						else
						{
							if ($_POST[template_data_type])
							{
								$session->write_value("SAMPLE_CURRENT_SCREEN", 5, true);
								
								$session->write_value("SAMPLE_TEMPLATE_DATA_TYPE", $_POST[template_data_type], true);	
								
								$template_data_array = array();
								
								foreach($_POST as $key => $value)
								{
									if (strpos($key, "-vartype") === false and $key != "submitbutton" and $key != "template_data_type")
									{
										$template_data_array[$key] = $value;
									}
									else
									{
										// type-check
									}
								}
								
								$session->write_value("SAMPLE_TEMPLATE_DATA_TYPE_ID", $_POST[template_data_type_id], true);
								$session->write_value("SAMPLE_TEMPALTE_DATA_ARRAY", $template_data_array, true);	
							}
						}
					}
					else
					{
						$session->write_value("SAMPLE_CURRENT_SCREEN", $_GET[tpage], true);
						
						$session->write_value("SAMPLE_TEMPLATE_DATA_TYPE", $_POST[template_data_type], true);	
								
						$template_data_array = array();
						
						foreach($_POST as $key => $value)
						{
							if (strpos($key, "-vartype") === false and $key != "submitbutton" and $key != "template_data_type")
							{
								$template_data_array[$key] = $value;
							}
							else
							{
								// type-check
							}
						}
						
						$session->write_value("SAMPLE_TEMPLATE_DATA_TYPE_ID", $_POST[template_data_type_id], true);
						$session->write_value("SAMPLE_TEMPALTE_DATA_ARRAY", $template_data_array, true);
						
						unset($_GET[tpage]);
					}
				break;
				
				case 5:
					if (!$_GET[tpage])
					{
						if ($_POST[submitbutton] == "previous")
						{
							if ($sample_template_specific_information == true)
							{
								$session->write_value("SAMPLE_CURRENT_SCREEN", 4, true);
							}
							else
							{
								$session->write_value("SAMPLE_CURRENT_SCREEN", 3, true);	
							}
						}
						elseif ($_POST[submitbutton] == "finish")
						{
							$session->write_value("SAMPLE_CURRENT_SCREEN", 6, true);
						}
					}
					else
					{
						$session->write_value("SAMPLE_CURRENT_SCREEN", $_GET[tpage], true);
						unset($_GET[tpage]);
					}
				break;
			
			endswitch;
	
			if ($session->is_value("SAMPLE_CURRENT_SCREEN"))
			{
				$current_screen = $session->read_value("SAMPLE_CURRENT_SCREEN");
			}
			else
			{
				$current_screen = 1;
				$session->write_value("SAMPLE_CURRENT_SCREEN", 1, true);
			}
			
			if ($session->is_value("SAMPLE_LAST_SCREEN"))
			{
				$last_screen = $session->read_value("SAMPLE_LAST_SCREEN");
			}
			
			if ($_GET[nextpage])
			{
				$sample_organ_unit 				= $session->read_value("SAMPLE_ORGAN_UNIT");
				$sample_template				= $session->read_value("SAMPLE_TEMPLATE");
				$sample_name					= $session->read_value("SAMPLE_NAME");
				$sample_manufacturer			= $session->read_value("SAMPLE_MANUFACTURER");	
				$sample_manufacturer_name		= $session->read_value("SAMPLE_MANUFACTURER_NAME");			
				$sample_location				= $session->read_value("SAMPLE_LOCATION");
				$sample_expiry					= $session->read_value("SAMPLE_EXPIRY");
				$sample_expiry_warning			= $session->read_value("SAMPLE_EXPIRY_WARNING");
				$sample_desc					= $session->read_value("SAMPLE_DESC");
				$sample_template_data_type  	= $session->read_value("SAMPLE_TEMPLATE_DATA_TYPE");	
				$sample_template_data_type_id	= $session->read_value("SAMPLE_TEMPLATE_DATA_TYPE_ID");	
				$sample_template_data_array		= $session->read_value("SAMPLE_TEMPALTE_DATA_ARRAY");	
			}
					
			switch ($current_screen):
				case 1:
				
					// Page 1
					if ($session->read_value("SAMPLE_LAST_SCREEN") < 1)
					{
						$session->write_value("SAMPLE_LAST_SCREEN", 1, true);
						$last_screen = 1;
					}
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 1;
					$params = http_build_query($paramquery,'','&#38;');
					
					// Assistant Bar Begin			
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					if (is_numeric($sample_toid) or is_numeric($sample_project_toid))
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", "");
					}
					else
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					}
					
					$assistant_bar_io->add_screen(2, "Sample Type", $paramquery);
					$assistant_bar_io->add_screen(3, "Sample Information", $paramquery);
					if ($sample_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
					// Assistant Bar End
				
					$template = new Template("languages/en-gb/template/samples/new_sample_page_1.html");	
					$template->set_var("bar",$assistant_bar_io->get_content());
					$template->set_var("link",$params);	
		
					if ($error[0])
					{
						$template->set_var("error",$error[0]);
					}
					else
					{
						$template->set_var("error","");
					}
		
					$result = array();
					$counter = 0;
						
					$organisation_unit_array = OrganisationUnit::list_entries();
					
					foreach($organisation_unit_array as $key => $value)
					{
						$organisation_unit = new OrganisationUnit($value);
				
						if ($organisation_unit->is_permission($user->get_user_id()) and $organisation_unit->get_stores_data() == true)
						{
							$result[$counter][value] = $value;
							$result[$counter][content] = $organisation_unit->get_name();		
		
							if ($sample_organ_unit == $value)
							{
								$result[$counter][selected] = "selected";
							}
							else
							{
								$result[$counter][selected] = "";
							}
							$counter++;
						}
					}
					
					if (!$result)
					{
						$result[$counter][value] = "0";
						$result[$counter][content] = "NO ORGANISATION UNIT FOUND!";
					}
			
					$template->set_var("option",$result);
					
					$template->output();
				break;
			
				case 2:
					// Page 2
					if ($session->read_value("SAMPLE_LAST_SCREEN") < 2)
					{
						$session->write_value("SAMPLE_LAST_SCREEN", 2, true);
						$last_screen = 2;
					}
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 2;
					unset($paramquery[idk_unique_id]);
					$params = http_build_query($paramquery,'','&#38;');
								
					// Assistant Bar Begin			
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					if (is_numeric($sample_toid) or is_numeric($sample_project_toid))
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", "");
					}
					else
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					}
					
					$assistant_bar_io->add_screen(2, "Sample Type", $paramquery);
					$assistant_bar_io->add_screen(3, "Sample Information", $paramquery);
					if ($sample_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
					// Assistant Bar End
	
					$template = new Template("languages/en-gb/template/samples/new_sample_page_2.html");	
					$template->set_var("bar",$assistant_bar_io->get_content());
					$template->set_var("link",$params);	
					
					if ($error[0])
					{
						$template->set_var("error",$error[0]);
					}
					else
					{
						$template->set_var("error","");
					}

					if (!is_array($type_array) or count($type_array) == 0)
					{
						$type_array = null;
					}
					
					$result = array();
					$counter = 0;
						
					$sample_template_array = SampleTemplate::list_entries();
					
					if (is_array($sample_template_array))
					{
						foreach($sample_template_array as $key => $value)
						{
							if ($type_array == null or in_array($value, $type_array))
							{
								$sample_sub_template = new SampleTemplate($value);
								
								$result[$counter][value] = $value;
								$result[$counter][content] = $sample_sub_template->get_name();		
			
								if ($sample_template == $value)
								{
									$result[$counter][selected] = "selected";
								}
								else
								{
									$result[$counter][selected] = "";
								}
								$counter++;
							}
						}
					}
					else
					{
						$result[$counter][value] = "0";
						$result[$counter][content] = "NO TEMPLATES FOUND!";
					}
					$template->set_var("option",$result);
					
					if ($session->is_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET[idk_unique_id]) == true)
					{
						$template->set_var("keywords", $session->read_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET[idk_unique_id]));
					}
					else
					{
						$template->set_var("keywords", "");
					}
					
					if ($session->is_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET[idk_unique_id]) == true)
					{
						$template->set_var("description", $session->read_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET[idk_unique_id]));
					}
					else
					{
						$template->set_var("description", "");
					}
					
					$template->output();
				break;
				
				case 3:
					// Page 3
					if ($session->read_value("SAMPLE_LAST_SCREEN") < 3)
					{
						$session->write_value("SAMPLE_LAST_SCREEN", 3, true);
						$last_screen = 3;
					}
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 3;
					$params = http_build_query($paramquery,'','&#38;');
					
					// Assistant Bar Begin			
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					if (is_numeric($sample_toid) or is_numeric($sample_project_toid))
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", "");
					}
					else
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					}
					
					$assistant_bar_io->add_screen(2, "Sample Type", $paramquery);
					$assistant_bar_io->add_screen(3, "Sample Information", $paramquery);
					if ($sample_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++){
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
					// Assistant Bar End
	
					$template = new Template("languages/en-gb/template/samples/new_sample_page_3.html");
					$template->set_var("bar",$assistant_bar_io->get_content());
					$template->set_var("link",$params);	
				
					if ($error[0])
					{
						$template->set_var("error0",$error[0]);
					}
					else
					{
						$template->set_var("error0","");
					}
					
					if ($error[1])
					{
						$template->set_var("error1",$error[1]);
					}
					else
					{
						$template->set_var("error1","");
					}
					
					if ($error[2])
					{
						$template->set_var("error2",$error[2]);
					}
					else
					{
						$template->set_var("error2","");
					}
					
					if ($error[3])
					{
						$template->set_var("error3",$error[3]);
					}
					else
					{
						$template->set_var("error3","");
					}
					
					if ($sample_name)
					{
						$template->set_var("name",$sample_name);
					}
					else
					{
						$template->set_var("name","");
					}
					
					$information_fields = $sample_template_obj->get_information_fields();

					if ($information_fields[manufacturer][name])
					{
						require_once("core/modules/manufacturer/manufacturer.io.php");
						$template->set_var("show_manufacturer",true);
						$template->set_var("manufacturer_html",ManufacturerIO::dialog());
					}
					else
					{
						$template->set_var("show_manufacturer",false);
						$template->set_var("manufacturer_html","");
					}
					
					if ($information_fields[expiry][name])
					{
						$template->set_var("show_expiry",true);
					}
					else
					{
						$template->set_var("show_expiry",false);
					}
					
					if ($information_fields[location][name])
					{
						$template->set_var("show_location",true);
						
						$result = array();
						$counter = 0;
							
						$sample_location_array = Location::list_entries();
						
						if (is_array($sample_location_array) and count($sample_location_array) >= 1)
						{
							foreach($sample_location_array as $key => $value)
							{
								$sample_location_obj = new Location($value);
												
								$result[$counter][value] = $value;
								$result[$counter][content] = $sample_location_obj->get_name(true);		
			
								if ($sample_location == $value)
								{
									$result[$counter][selected] = "selected";
								}
								else
								{
									$result[$counter][selected] = "";
								}
								$counter++;
							}
						}
						else
						{
							$result[$counter][value] = "0";
							$result[$counter][content] = "NO LOCATIONS FOUND!";
						}
						$template->set_var("location",$result);
					}
					else
					{
						$template->set_var("show_location",false);
					}
					
					if ($sample_manufacturer)
					{
						$template->set_var("manufacturer",$sample_manufacturer);
					}
					else
					{
						$template->set_var("manufacturer","");
					}
					
					if ($sample_manufacturer_name)
					{
						$template->set_var("manufacturer_name",$sample_manufacturer_name);
					}
					else
					{
						$template->set_var("manufacturer_name","");
					}
					
					if ($sample_expiry)
					{
						$template->set_var("expiry",$sample_expiry);
					}
					else
					{
						$template->set_var("expiry","");
					}
					
					if ($sample_expiry_warning)
					{
						$template->set_var("expiry_warning",$sample_expiry_warning);
					}
					else
					{
						$template->set_var("expiry_warning",constant("SAMPLE_EXIRY_WARNING"));
					}
					
					if ($sample_desc)
					{
						$template->set_var("desc",$sample_desc);
					}
					else
					{
						$template->set_var("desc","");
					}
					
					$template->set_var("keywords", $_POST[keywords]);
					$template->set_var("description", $_POST[description]);
					$template->output();
				break;
				
				case 4:
					// Page 4
					if ($session->read_value("SAMPLE_LAST_SCREEN") < 4)
					{
						$session->write_value("SAMPLE_LAST_SCREEN", 4, true);
						$last_screen = 4;
					}
				
					$paramquery = $_GET;
					$paramquery[nextpage] = 4;
					$params = http_build_query($paramquery,'','&#38;');
				
					// Assistant Bar Begin			
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					if (is_numeric($sample_toid) or is_numeric($sample_project_toid))
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", "");
					}
					else
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					}
					
					$assistant_bar_io->add_screen(2, "Sample Type", $paramquery);
					$assistant_bar_io->add_screen(3, "Sample Information", $paramquery);
					
					if ($sample_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", "");
					}
					
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
					// Assistant Bar End
				
					$sample_template_obj = new SampleTemplate($sample_template);
					$required_array = $sample_template_obj->get_required_requirements();
					
					if (is_array($required_array) and count($required_array) >= 1)
					{
						$value_type_id = 0;
						$sample_count = 0;
						$is_value = false;
						$is_sample = false;
						
						foreach($required_array as $key => $value)
						{						
							if ($value[xml_element] == "item")
							{
								if ($value[type] == "value")
								{
									$is_value = true;
								}
								elseif($value[type] == "parentsample")
								{
									$is_sample = true;
									$sample_count++;
								}
							}
							
							if ($value[xml_element] == "type" and !$value[close] and $is_value == true)
							{
								$value_type_id = $value[id];
							}
						} 
						
						if ($is_value == true xor $is_sample == true)
						{
							if ($is_value == true)
							{
								$template = new Template("languages/en-gb/template/samples/new_sample_page_4_value.html");
								$template->set_var("bar",$assistant_bar_io->get_content());
								$template->set_var("link",$params);	
								
								$value_obj = new Value(null);
								if ($sample_template_data_type == "value")
								{
									$value->set_content_array($sample_template_data_array);
								}	
								$value_html = $value_obj->get_html_form(null, $value_type_id, null);
								$template->set_var("content",$value_html);
								
								$template->set_var("template_data_type_id", $value_type_id);
								$template->set_var("keywords", $_POST[keywords]);
								$template->set_var("description", $_POST[description]);
								$template->output();
							}
							else
							{
								$template = new Template("languages/en-gb/template/samples/new_sample_page_4_sample.html");
								$template->set_var("bar",$assistant_bar_io->get_content());
								$template->set_var("link",$params);	
								
								if ($sample_count > 0)
								{
									$result = array();
									$sample_array = Sample::list_user_related_samples($user->get_user_id());
									
									for($i=0;$i<=$sample_count-1;$i++)
									{
										$result[$i][id] = $i+1;
										
										if ($sample_template_data_type == "sample")
										{
											if ($sample_template_data_array['sample-'.$result[$i][id].''])
											{
												$selected_id = $sample_template_data_array['sample-'.$result[$i][id].''];
											}
										}	
										
										if (is_array($sample_array) and count($sample_array) >= 1)
										{
											$counter = 0;
											
											foreach($sample_array as $key => $value)
											{
												$sample = new Sample($value);
												
												$result[$i][$counter][value] = $value;
												$result[$i][$counter][content] = $sample->get_name();
												if ($selected_id == $value)
												{
													$result[$i][$counter][selected] = "selected";
												}
												else
												{
													$result[$i][$counter][selected] = "";
												}
												
												$counter++;
											}
										}
										else
										{
											$result[$i][0][value] = 0;
											$result[$i][0][content] = "You have no samples";
											$result[$i][0][selected] = "";
										}
										unset($selected_id);
									}
									$template->set_var("sample", $result);
								}	
								$template->set_var("keywords", $_POST[keywords]);
								$template->set_var("description", $_POST[description]);	
								$template->output();
							}
						}
						else
						{
							$template = new Template("languages/en-gb/template/samples/new_sample_page_4_error.html");
							$template->set_var("bar",$assistant_bar_io->get_content());
							$template->set_var("link",$params);
							$template->set_var("keywords", $_POST[keywords]);
							$template->set_var("description", $_POST[description]);	
							$template->output();
						}
					}
					else
					{
						$template = new Template("languages/en-gb/template/samples/new_sample_page_4_error.html");
						$template->set_var("bar",$assistant_bar_io->get_content());
						$template->set_var("link",$params);	
						$template->output();
					}
				break;
				
				case 5:
					// Page 5
					if ($session->read_value("SAMPLE_LAST_SCREEN") < 5)
					{
						$session->write_value("SAMPLE_LAST_SCREEN", 5, true);
						$last_screen = 5;
					}
					
					$paramquery = $_GET;
					$paramquery[nextpage] = 5;
					$params = http_build_query($paramquery,'','&#38;');
					
					// Assistant Bar Begin			
					require_once("core/modules/base/assistant_bar.io.php");
					$assistant_bar_io = new AssistantBarIO;
					if (is_numeric($sample_toid) or is_numeric($sample_project_toid))
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", "");
					}
					else
					{
						$assistant_bar_io->add_screen(1, "Organisation Unit", $paramquery);
					}
					$assistant_bar_io->add_screen(2, "Sample Type", $paramquery);
					$assistant_bar_io->add_screen(3, "Sample Information", $paramquery);
					if ($sample_template_specific_information == true)
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", $paramquery);
					}
					else
					{
						$assistant_bar_io->add_screen(4, "Sample Specific Information", "");
					}
					$assistant_bar_io->add_screen(5, "Summary", $paramquery);
					for ($i=1; $i<=$last_screen; $i++)
					{
						if ($i != $current_screen)
						{
							$assistant_bar_io->set_visited($i);
						}
						else
						{
							$assistant_bar_io->set_active($i);
						}
					}
					// Assistant Bar End
	
					$template = new Template("languages/en-gb/template/samples/new_sample_page_5.html");
					$template->set_var("bar",$assistant_bar_io->get_content());
					$template->set_var("link",$params);	
				
					$organisation_unit = new OrganisationUnit($sample_organ_unit);
					$template->set_var("organ_unit",$organisation_unit->get_name());
				
					$sample_template_obj = new SampleTemplate($sample_template);
					$template->set_var("template",$sample_template_obj->get_name());
				
					$template->set_var("name",$sample_name);
					
					if ($sample_manufacturer)
					{
						$template->set_var("manufacturer",$sample_manufacturer_name);
					}
					else
					{
						$template->set_var("manufacturer",false);
					}
					
					if ($sample_location)
					{
						$sample_location_obj = new Location($sample_location);
						$template->set_var("location",$sample_location_obj->get_name(true));
					}
					else
					{
						$template->set_var("location",false);
					}
				
					if ($sample_expiry)
					{
						$template->set_var("date_of_expiry",$sample_expiry);
					}
					else
					{
						$template->set_var("date_of_expiry",false);
					}
					
					if ($sample_desc)
					{
						$sample_desc_display = str_replace("\n", "<br />", $sample_desc);
						$template->set_var("desc",$sample_desc_display);
					}
					else
					{
						$template->set_var("desc","<span class='italic'>None</span>");
					}
				
					$template->set_var("keywords", $_POST[keywords]);
					$template->set_var("description", $_POST[description]);
			
					$template->output();
				break;
				
				case 6:
					try
					{
						$sample = new Sample(null);
		
						$sample->set_template_data($sample_template_data_type, $sample_template_data_type_id, $sample_template_data_array);
		
						if (($sample_id = $sample->create($sample_organ_unit, $sample_template, $sample_name, $sample_manufacturer, $sample_location, $sample_desc, null, $sample_expiry, $sample_expiry_warning)) != null)
						{
							$session->delete_value("SAMPLE_LAST_SCREEN");
							$session->delete_value("SAMPLE_CURRENT_SCREEN");
	
							if ($_GET[run] == "item_add")
							{
								return $sample->get_item_id();				
							}
							else
							{
								$paramquery = $_GET;
								unset($paramquery[nextpage]);
								$paramquery[run] = "detail";
								$paramquery[sample_id] = $sample_id;
								$params = http_build_query($paramquery);
								
								Common_IO::step_proceed($params, "Add Sample", "Operation Successful", null);
							}
						}
						else
						{

							$paramquery = $_GET;
							unset($paramquery[nextpage]);
							$paramquery[run] = "mysamples";
							$params = http_build_query($paramquery);
							
							$session->delete_value("SAMPLE_LAST_SCREEN");
							$session->delete_value("SAMPLE_CURRENT_SCREEN");
							Common_IO::step_proceed($params, "Add Sample", "Operation Failed", null);
						}
					
						$session->delete_value("SAMPLE_LAST_SCREEN");
						$session->delete_value("SAMPLE_CURRENT_SCREEN");
						
						$session->delete_value("SAMPLE_ORGAN_UNIT");
						$session->delete_value("SAMPLE_TEMPLATE");
						$session->delete_value("SAMPLE_NAME");
						$session->delete_value("SAMPLE_MANUFACTURER");
						$session->delete_value("SAMPLE_MANUFACTURER_NAME");
						$session->delete_value("SAMPLE_LOCATION");
						$session->delete_value("SAMPLE_EXPIRY");
						$session->delete_value("SAMPLE_EXPIRY_WARNING");
						$session->delete_value("SAMPLE_DESC");		
						$session->delete_value("SAMPLE_TEMPLATE_DATA_TYPE");
						$session->delete_value("SAMPLE_TEMPLATE_DATA_TYPE_ID");	
						$session->delete_value("SAMPLE_TEMPALTE_DATA_ARRAY");
					}
					catch (SampleCreationFailedException $e)
					{
						$error_io = new Error_IO($e, 250, 30, 1);
						$error_io->display_error();
					}
				break;	
			endswitch;
		}
		catch (SampleSecurityException $e)
		{
			$error_io = new Error_IO($e, 250, 40, 2);
			$error_io->display_error();
		}		
	}
	
	/**
	 * NEW
	 * @param array $type_array
	 * @param array $category_array
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function add_sample_item($type_array, $category_array, $organisation_unit_id, $folder_id)
	{
		global $session;
		
		if (!$_GET[selectpage])
		{
			$unique_id = uniqid();
			
			if ($_POST[keywords])
			{
				$session->write_value("ADD_ITEM_TEMP_KEYWORDS_".$unique_id, $_POST[keywords], true);
			}
			
			if ($_POST[description])
			{
				$session->write_value("ADD_ITEM_TEMP_DESCRIPTION_".$unique_id, $_POST[description], true);
			}
			
			$template = new Template("languages/en-gb/template/samples/add_as_item.html");
		
			$result = array();
			$counter = 0;
			
			foreach ($_GET as $key => $value)
			{
				$result[$counter][name] = $key;
				$result[$counter][value] = $value;
				$counter++;
			}
		
			$template->set_var("get_value", $result);
			$template->set_var("unique_id", $unique_id);
			
			$template->output();
		}
		else
		{			
			if ($_GET[selectpage] == 1)
			{
				return self::create($type_array, $category_array, $organisation_unit_id);
			}
			else
			{
				return self::associate($type_array, $category_array);
			}
		}
	}

	/**
	 * NEW
	 */
	public static function associate($type_array, $category_array)
	{
		global $user, $session;
					
		if ($_GET[nextpage] < 2)
		{
			$template = new Template("languages/en-gb/template/samples/associate.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = 2;
			unset($paramquery[idk_unique_id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
								
			$result = array();
			$sample_array = Sample::list_user_related_samples($user->get_user_id());
			
			if (!is_array($type_array) or count($type_array) == 0)
			{
				$type_array = null;
			}

			if (is_array($sample_array) and count($sample_array) >= 1)
			{
				$counter = 0;
				
				foreach($sample_array as $key => $value)
				{
					$sample = new Sample($value);
					
					if ($type_array == null or in_array($sample->get_template_id(), $type_array))
					{
						$result[$counter][value] = $value;
						$result[$counter][content] = $sample->get_name();
						if ($_POST[sample] == $value)
						{
							$result[$counter][selected] = "selected";
						}
						else
						{
							$result[$counter][selected] = "";
						}
						$counter++;
					}
				}
			}
			else
			{
				$result[0][value] = 0;
				$result[0][content] = "You have no samples";
				$result[0][selected] = "";
			}
			$template->set_var("sample", $result);
			
			if ($session->is_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET[idk_unique_id]) == true)
			{
				$template->set_var("keywords", $session->read_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET[idk_unique_id]));
			}
			else
			{
				$template->set_var("keywords", "");
			}
			
			if ($session->is_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET[idk_unique_id]) == true)
			{
				$template->set_var("description", $session->read_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET[idk_unique_id]));
			}
			else
			{
				$template->set_var("description", "");
			}
			
			$template->output();
		}
		else
		{
			$sample = new Sample($_POST[sample]);
			return  $sample->get_item_id();
		}
	}
				
	public static function detail()
	{
		global $sample_security, $user;
		
		if ($_GET[sample_id])
		{
			if ($sample_security->is_access(1, false))
			{
				$sample = new Sample($_GET[sample_id]);
				$owner = new User($sample->get_owner_id());
			
				$template = new Template("languages/en-gb/template/samples/detail.html");
				
				$template->set_var("id", $sample->get_formatted_id());
				$template->set_var("name", $sample->get_name());
				$template->set_var("owner", $owner->get_full_name(false));
				$template->set_var("template", $sample->get_template_name());
				$template->set_var("permissions", $sample_security->get_access_string());
			
				$datetime = new DatetimeHandler($sample->get_datetime());
				$template->set_var("datetime", $datetime->get_formatted_string("dS M Y H:i"));
				
				if ($sample->get_date_of_expiry())
				{
					$date_of_expiry = new DatetimeHandler($sample->get_date_of_expiry());
					$template->set_var("date_of_expiry", $date_of_expiry->get_formatted_string("dS M Y"));
				}
				else
				{
					$template->set_var("date_of_expiry", false);
				}
				
				if ($sample->get_current_location_name())
				{
					$template->set_var("location", $sample->get_current_location_name());
				}
				else
				{
					$template->set_var("location", false);
				}
				
				if ($sample->get_manufacturer_id())
				{
					$manufacturer = new Manufacturer($sample->get_manufacturer_id());
					$template->set_var("manufacturer", $manufacturer->get_name());
				}
				else
				{
					$template->set_var("manufacturer", false);
				}
				
				if ($sample->get_availability() == true)
				{
					$template->set_var("status", "available");
					$template->set_var("new_status", "not available");
				}
				else
				{
					$template->set_var("status", "not available");
					$template->set_var("new_status", "available");
				}
				
				if ($sample->get_owner_id() == $user->get_user_id() or $user->is_admin() == true)
				{
					$template->set_var("is_owner", true);
				}
				else
				{
					$template->set_var("is_owner", false);	
				}
				
				if ($user->is_admin() == true)
				{
					$template->set_var("is_admin", true);
				}
				else
				{
					$template->set_var("is_admin", false);	
				}
				
				$owner_paramquery = array();
				$owner_paramquery[username] = $_GET[username];
				$owner_paramquery[session_id] = $_GET[session_id];
				$owner_paramquery[nav] = "sample";
				$owner_paramquery[run] = "common_dialog";
				$owner_paramquery[dialog] = "user_detail";
				$owner_paramquery[id] = $sample->get_owner_id();
				$owner_params = http_build_query($owner_paramquery,'','&#38;');
				
				$template->set_var("owner_params", $owner_params);	
				
				$location_history_paramquery = $_GET;
				$location_history_paramquery[run] = "location_history";
				$location_history_params = http_build_query($location_history_paramquery,'','&#38;');
				
				$template->set_var("location_history_params", $location_history_params);	
				
				// Buttons
				
				$sample_template 				= new SampleTemplate($sample->get_template_id());
				$current_requirements 			= $sample->get_requirements();
				$current_fulfilled_requirements = $sample->get_fulfilled_requirements();
				
				$result = array();
				$counter = 0;
				
				if (is_array($current_requirements) and count($current_requirements) >= 1)
				{
					foreach($current_requirements as $key => $value)
					{						
						$paramquery = array();
						$paramquery[username] = $_GET[username];
						$paramquery[session_id] = $_GET[session_id];
						$paramquery[nav] = "sample";
						$paramquery[run] = "item_add";
						$paramquery[sample_id] = $_GET[sample_id];
						$paramquery[dialog] = $value[type];
						$paramquery[key] = $key;
						$paramquery[retrace] = Misc::create_retrace_string();
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery,'','&#38;');

						$result[$counter][name] = $value[name];

						if ($current_fulfilled_requirements[$key] == true)
						{
							if ($value[occurrence] == "multiple")
							{
								$result[$counter][status] = 2;
							}
							else
							{
								$result[$counter][status] = 0;
							}
						}
						else
						{
							$result[$counter][status] = 1;
						}

						if ($value[requirement] == "optional")
						{
							$result[$counter][name] = $result[$counter][name]." (optional)";
						}
						
						$result[$counter][params] = $params;
												
						if ($sample_security->is_access(2, false))
						{
							$result[$counter][permission] = true;
						}
						else
						{
							$result[$counter][permission] = false;
						}
						$counter++;
					}			
				}
				
				$template->set_var("action",$result);
			
				$move_paramquery = $_GET;
				$move_paramquery[run] = "move";
				unset($move_paramquery[nextpage]);
				$move_params = http_build_query($move_paramquery,'','&#38;');
				
				$template->set_var("move_params",$move_params);
				
				
				$availability_paramquery = $_GET;
				$availability_paramquery[run] = "set_availability";
				unset($availability_paramquery[nextpage]);
				$availability_params = http_build_query($availability_paramquery,'','&#38;');
				
				$template->set_var("availability_params",$availability_params);
			
			
				$rename_paramquery = $_GET;
				$rename_paramquery[run] = "rename";
				unset($rename_paramquery[nextpage]);
				$rename_params = http_build_query($rename_paramquery,'','&#38;');
			
				$template->set_var("rename_params",$rename_params);
			
				$user_permissions_paramquery = $_GET;
				$user_permissions_paramquery[run] = "admin_permission_user";
				unset($user_permissions_paramquery[nextpage]);
				$user_permissions_params = http_build_query($user_permissions_paramquery,'','&#38;');
				
				$template->set_var("user_permissions_params",$user_permissions_params);
				
				$ou_permissions_paramquery = $_GET;
				$ou_permissions_paramquery[run] = "admin_permission_ou";
				unset($ou_permissions_paramquery[nextpage]);
				$ou_permissions_params = http_build_query($ou_permissions_paramquery,'','&#38;');
				
				$template->set_var("ou_permissions_params",$ou_permissions_params);
				
				$delete_paramquery = $_GET;
				$delete_paramquery[run] = "delete";
				unset($delete_paramquery[nextpage]);
				$delete_params = http_build_query($delete_paramquery,'','&#38;');
				
				$template->set_var("delete_params",$delete_params);
				
	
				$add_subsample_paramquery = $_GET;
				$add_subsample_paramquery[run] = "new_subsample";
				unset($add_subsample_paramquery[nextpage]);
				$add_subsample_params = http_build_query($add_subsample_paramquery,'','&#38;');
				
				$template->set_var("add_subsample_params",$add_subsample_params);
				
				$template->output();
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}

	public static function move()
	{
		global $user, $sample_security;

		if ($_GET[sample_id])
		{
			if ($sample_security->is_access(2, false))
			{
				$sample_id = $_GET[sample_id];		
				$sample = new Sample($sample_id);
				
				if ($_GET[nextpage] == 1)
				{
					if (is_numeric($_POST[location]))
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must select a location.";
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
					$template = new Template("languages/en-gb/template/samples/move.html");
						
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->set_var("error",$error);
					
					$result = array();
					$counter = 0;
					
					$sample_location_array = Location::list_entries();
						
					if (is_array($sample_location_array) and count($sample_location_array) >= 1)
					{
						foreach($sample_location_array as $key => $value)
						{
							$sample_location_obj = new Location($value);
											
							$result[$counter][value] = $value;
							$result[$counter][content] = $sample_location_obj->get_name(true);		
		
							$counter++;
						}
					}
					else
					{
						$result[$counter][value] = "0";
						$result[$counter][content] = "NO LOCATIONS FOUND!";
					}

					$template->set_var("option",$result);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					$paramquery[run] = "detail";
					$params = http_build_query($paramquery);
					
					if ($sample->add_location($_POST[location]))
					{
						Common_IO::step_proceed($params, "Move Sample", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Move Sample", "Operation Failed" ,null);	
					}
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function set_availability()
	{
		global $sample_security;
		
		if ($_GET[sample_id])
		{
			if ($sample_security->is_access(2, false))
			{
				if ($_GET[sure] != "true")
				{
					$template = new Template("languages/en-gb/template/samples/set_availability.html");
					
					$paramquery = $_GET;
					$paramquery[sure] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[id]);
					$paramquery[run] = "admin_permission";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$sample = new Sample($_GET[sample_id]);
					
					$paramquery = $_GET;
					unset($paramquery[nextpage]);
					unset($paramquery[sure]);
					$paramquery[run] = "detail";
					$params = http_build_query($paramquery);
					
					if ($sample->get_availability() == true)
					{
						if ($sample->set_availability(false))
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Successful" ,null);
						}
						else
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Failed" ,null);
						}
					}
					else
					{
						if ($sample->set_availability(true))
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Successful" ,null);
						}
						else
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Failed" ,null);
						}
					}		
				}
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}

	/**
	 * NEW
	 */
	public static function location_history()
	{
		global $sample_security;
	
		if ($_GET[sample_id])
		{
			if ($sample_security->is_access(1, false))
			{
				$list = new List_IO(Sample_Wrapper::count_sample_locations($_GET[sample_id]), 20);
	
				$list->add_row("","symbol",false,"16px");
				$list->add_row("Name","name",true,null);
				$list->add_row("Date","datetime",true,null);
				$list->add_row("User","user",true,null);
				
				if ($_GET[page])
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = Sample_Wrapper::list_sample_locations($_GET[sample_id], $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
					}
					else
					{
						$result_array = Sample_Wrapper::list_sample_locations($_GET[sample_id], null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
					}				
				}
				else
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = Sample_Wrapper::list_sample_locations($_GET[sample_id], $_GET[sortvalue], $_GET[sortmethod], 0, 20);
					}
					else
					{
						$result_array = Sample_Wrapper::list_sample_locations($_GET[sample_id], null, null, 0, 20);
					}	
				}
				
				if (is_array($result_array) and count($result_array) >= 1)
				{
					foreach($result_array as $key => $value)
					{
						$result_array[$key][symbol] = "<img src='images/icons/sample.png' alt='' style='border:0;' />";
						
						$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
						$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
						if ($result_array[$key][user])
						{
							$user = new User($result_array[$key][user]);
						}
						else
						{
							$user = new User(1);
						}
						
						$result_array[$key][user] = $user->get_full_name(false);
					}
				}
				else
				{
					$list->override_last_line("<span class='italic'>No results found!</span>");
				}
	
				$template = new Template("languages/en-gb/template/samples/location_history.html");
				
				$sample = new Sample($_GET[sample_id]);
				
				$template->set_var("sample_id",$sample->get_formatted_id());
				$template->set_var("sample_name","(".$sample->get_name().")");
				
				$template->set_var("table", $list->get_list($result_array, $_GET[page]));
				
				$paramquery = $_GET;
				$paramquery[run] = "detail";
				unset($paramquery[sortvalue]);
				unset($paramquery[sortmethod]);
				$params = http_build_query($paramquery,'','&#38;');	
				
				$template->set_var("back_link",$params);
				
				$template->output();
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 250, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 250, 40, 3);
			$error_io->display_error();
		}
	}
	
	public static function method_handler()
	{
		global $sample_security, $session, $transaction;
		
		try
		{
			if ($_GET[sample_id])
			{
				if (Sample::exist_sample($_GET[sample_id]) == false)
				{
					throw new SampleNotFoundException("",1);
				}
				else
				{
					$sample_security = new SampleSecurity($_GET[sample_id]);
					
					require_once("sample_common.io.php");
 					SampleCommon_IO::tab_header();
				}
			}
			else
			{
				$sample_security = new SampleSecurity(null);
			}
			
			switch($_GET[run]):
				case ("new"):
				case ("new_subsample"):
					self::create(null,null,null);
				break;
				
				case ("organ_unit"):
					self::list_organisation_unit_related_samples();
				break;
				
				case("detail"):
					self::detail();
				break;
				
				case("move"):
					self::move();
				break;
				
				case("set_availability"):
					self::set_availability();
				break;
				
				case("location_history"):
					self::location_history();
				break;
	
				// Administration
				
				case ("delete"):
					require_once("sample_admin.io.php");
					SampleAdminIO::delete();
				break;
								
				case ("rename"):
					require_once("sample_admin.io.php");
					SampleAdminIO::rename();
				break;
				
				case("admin_permission_user"):
					require_once("sample_admin.io.php");
					SampleAdminIO::user_permission();
				break;
				
				case("admin_permission_user_add"):
					require_once("sample_admin.io.php");
					SampleAdminIO::user_permission_add();
				break;
				
				case("admin_permission_user_delete"):
					require_once("sample_admin.io.php");
					SampleAdminIO::user_permission_delete();
				break;
				
				case("admin_permission_ou"):
					require_once("sample_admin.io.php");
					SampleAdminIO::ou_permission();
				break;
				
				case("admin_permission_ou_add"):
					require_once("sample_admin.io.php");
					SampleAdminIO::ou_permission_add();
				break;
				
				case("admin_permission_ou_delete"):
					require_once("sample_admin.io.php");
					SampleAdminIO::ou_permission_delete();
				break;
	
				
				case("list_ou_equipment"):
					require_once("core/modules/equipment/equipment.io.php");
					EquipmentIO::list_organisation_unit_related_equipment_handler();
				break;
				
				
				// Item Lister
				/**
				 * @todo errors
				 */
				case("item_list"):
					if ($sample_security->is_access(1, false) == true)
					{
						if ($_GET[dialog])
						{
							if ($_GET[dialog] == "data")
							{
								$path_stack_array = array();
								
						    	$folder_id = SampleFolder::get_folder_by_sample_id($_GET[sample_id]);
						    	$folder = Folder::get_instance($folder_id);
						    	$init_array = $folder->get_object_id_path();
						    	
						    	foreach($init_array as $key => $value)
						    	{
						    		$temp_array = array();
						    		$temp_array[virtual] = false;
						    		$temp_array[id] = $value;
						    		array_unshift($path_stack_array, $temp_array);
						    	}
								
								$session->write_value("stack_array", $path_stack_array, true);
							}
							
							$sql = " SELECT item_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE sample_id = ".$_GET[sample_id]."";
							$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_list", $_GET[dialog]);
							
							if (file_exists($module_dialog[class_path]))
							{
								require_once($module_dialog[class_path]);
								
								if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
								{
									$module_dialog['class']::$module_dialog[method]($sql);
								}
								else
								{
									// Error
								}
							}
							else
							{
								// Error
							}
						}
						else
						{
							// error
						}
					}
					else
					{
						$exception = new Exception("", 1);
						$error_io = new Error_IO($exception, 250, 40, 2);
						$error_io->display_error();
					}
				break;
				
				case("item_add"):
					if ($sample_security->is_access(2, false) == true)
					{
						if ($_GET[dialog])
						{
							$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_add", $_GET[dialog]);
	
							if (is_array($module_dialog) and $module_dialog[class_path])
							{
								if (file_exists($module_dialog[class_path]))
								{
									require_once($module_dialog[class_path]);
									
									if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
									{
										$sample_item = new SampleItem($_GET[sample_id]);
										$sample_item->set_gid($_GET[key]);
										
										$description_required = $sample_item->is_description_required();
										$keywords_required = $sample_item->is_keywords_required();
										
										if (($description_required and !$_POST[description] and !$_GET[idk_unique_id]) or ($keywords_required and !$_POST[keywords] and !$_GET[idk_unique_id]))
										{
											require_once("core/modules/item/item.io.php");
											ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
										}
										else
										{
											$transaction_id = $transaction->begin();
											
											$sample = new Sample($_GET[sample_id]);
											$current_requirements = $sample->get_requirements();
											
											$folder_id = SampleFolder::get_folder_by_sample_id($_GET[sample_id]);
											
											$sub_folder_id = $sample->get_sub_folder($folder_id, $_GET[key]);				
							
											if (is_numeric($sub_folder_id))
											{
												$folder_id = $sub_folder_id;
											}
											
											$return_value = $module_dialog['class']::$module_dialog[method]($current_requirements[$_GET[key]][type_id], $current_requirements[$_GET[key]][category_id], null, $folder_id);
											
											if (is_numeric($return_value))
											{
												if ($_GET[retrace])
												{
													$params = http_build_query(Misc::resovle_retrace_string($_GET[retrace]),'','&#38;');
												}
												else
												{
													$paramquery[username] = $_GET[username];
													$paramquery[session_id] = $_GET[session_id];
													$paramquery[nav] = "home";
													$params = http_build_query($paramquery,'','&#38;');
												}
												
												if ($_GET[dialog] == "parentsample")
												{
													$parent_sample_id = Sample::get_entry_by_item_id($return_value);
													
													if (SampleItemFactory::create($parent_sample_id, $sample->get_item_id(), ($_GET[key]*-1), $_POST[keywords], $_POST[description]) == true)
													{
														if ($transaction_id != null)
														{
															$transaction->commit($transaction_id);
														}
														Common_IO::step_proceed($params, "Add Item", "Succeed." ,null);
													}
													else
													{
														if ($transaction_id != null)
														{
															$transaction->rollback($transaction_id);
														}
														Common_IO::step_proceed($params, "Add Item", "Failed." ,null);	
													}
												}
												else
												{
													if (SampleItemFactory::create($_GET[sample_id], $return_value, $_GET[key], $_POST[keywords], $_POST[description]) == true)
													{
														if ($transaction_id != null)
														{
															$transaction->commit($transaction_id);
														}
														Common_IO::step_proceed($params, "Add Item", "Succeed." ,null);
													}
													else
													{
														if ($transaction_id != null)
														{
															$transaction->rollback($transaction_id);
														}
														Common_IO::step_proceed($params, "Add Item", "Failed." ,null);	
													}
												}
											}
											else
											{
												if ($return_value === false)
												{
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new ModuleDialogFailedException("",1);
												}
												else
												{
													if ($transaction_id != null)
													{
														$transaction->commit($transaction_id);
													}
												}
											}
										}
									}
									else
									{
										throw new ModuleDialogCorruptException(null, null);
									}
								}
								else
								{
									throw new ModuleDialogCorruptException(null, null);
								}
							}
							else
							{
								throw new ModuleDialogNotFoundException(null, null);
							}
						}
						else
						{
							throw new ModuleDialogMissingException(null, null);
						}
					}
					else
					{
						$exception = new Exception("", 1);
						$error_io = new Error_IO($exception, 250, 40, 2);
						$error_io->display_error();
					}
				break;
				
				// Parent Item Lister
				case("parent_item_list"):
					if ($sample_security->is_access(1, false) == true)
					{
						if ($_GET[dialog])
						{
							$sample = new Sample($_GET[sample_id]);
							$item_id = $sample->get_item_id();
							$module_dialog = ModuleDialog::get_by_type_and_internal_name("parent_item_list", $_GET[dialog]);
							
							if (file_exists($module_dialog[class_path]))
							{
								require_once($module_dialog[class_path]);
								
								if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
								{
									$module_dialog['class']::$module_dialog[method]($item_id);
								}
								else
								{
									// Error
								}
							}
							else
							{
								// Error
							}
						}
						else
						{
							// error
						}
					}
					else
					{
						$exception = new Exception("", 1);
						$error_io = new Error_IO($exception, 250, 40, 2);
						$error_io->display_error();
					}
				break;
				
				// Common Dialogs
				/**
				 * @todo errors, exceptions
				 */
				case("common_dialog"):
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("common_dialog", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]();
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				break;
				
				// Search
				/**
				 * @todo errors, exceptions
				 */
				case("search"):
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("search", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]();
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				break;
				
				default:
					self::list_user_related_samples(null);
				break;
			
			endswitch;
		}
		catch (SampleNotFoundException $e)
		{
			$error_io = new Error_IO($e, 250, 40, 1);
			$error_io->display_error();
		}
	}
	
}

?>
