<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Sample AJAX IO Class
 * @package sample
 */
class SampleAjax
{
	/**
	 * @param string $json_column_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_user_related_samples($json_column_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		$list_request = new ListRequest_IO();
		$list_request->set_column_array($json_column_array);
		
		if (!is_numeric($entries_per_page) or $entries_per_page < 1)
		{
			$entries_per_page = 20;
		}
		
		$list_array = Sample_Wrapper::list_user_samples($user->get_user_id(), $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
		if (is_array($list_array) and count($list_array) >= 1)
		{
			$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
			
			foreach($list_array as $key => $value)
			{
				$tmp_name = trim($list_array[$key][name]);
				unset($list_array[$key][name]);
				$list_array[$key][name][label] = $tmp_name;
				$list_array[$key][name][content] = $tmp_name;
				
				$tmp_template = trim($list_array[$key][template]);
				unset($list_array[$key][template]);
				$list_array[$key][template][label] = $tmp_template;
				$list_array[$key][template][content] = $tmp_template;
				
				$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
				$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");

				if ($list_array[$key][av] == "f")
				{
					$list_array[$key][av] = "<img src='images/icons/grey_point.png' alt='' />";
				}
				else
				{
					if ($list_array[$key][date_of_expiry] and $list_array[$key][expiry_warning])
					{
						$date_of_expiry = new DatetimeHandler($list_array[$key][date_of_expiry]." 23:59:59");
						$warning_day = clone $date_of_expiry;
						$warning_day->sub_day($list_array[$key][expiry_warning]);
					
						if ($date_of_expiry->distance($today_end) > 0)
						{
							$list_array[$key][av] = "<img src='images/icons/red_point.png' alt='' />";
						}
						else
						{
							if ($warning_day->distance($today_end) > 0)
							{
								$list_array[$key][av] = "<img src='images/icons/yellow_point.png' alt='' />";
							}
							else
							{
								$list_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
							}
						}
					}
					else
					{
						$list_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
					}
				}
				
				$sample_id = $list_array[$key][id];
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
					
					$list_array[$key][symbol][link]		= $params;
					$list_array[$key][symbol][content] 	= "<img src='images/icons/sample.png' alt='' style='border:0;' />";
				
					unset($list_array[$key][id]);
					$list_array[$key][id][link] 			= $params;
					$list_array[$key][id][content]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
				
					$list_array[$key][name][link] 		= $params;
				}
				else
				{
					$list_array[$key][symbol]	= "<img src='core/images/denied_overlay.php?image=images/icons/sample.png' alt='N' border='0' />";
					$list_array[$key][id]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
				}
			}	
		}
		else
		{
			$list_request->empty_message("<span class='italic'>You have no Samples at the moment!</span>");
		}
		
		$list_request->set_array($list_array);
		
		return $list_request->get_page($page);
	}

	/**
	 * @return integer
	 */
	public static function count_user_related_samples()
	{
		global $user;
		
		return Sample_Wrapper::count_user_samples($user->get_user_id());
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_organisation_unit_related_samples($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Sample_Wrapper::list_organisation_unit_samples($organisation_unit_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			if (is_array($list_array) and count($list_array) >= 1)
			{
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($list_array as $key => $value)
				{
					$tmp_name = trim($list_array[$key][name]);
					unset($list_array[$key][name]);
					$list_array[$key][name][label] = $tmp_name;
					$list_array[$key][name][content] = $tmp_name;
					
					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					$list_array[$key][template][label] = $tmp_template;
					$list_array[$key][template][content] = $tmp_template;
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");
	
					if ($list_array[$key][av] == "f")
					{
						$list_array[$key][av] = "<img src='images/icons/grey_point.png' alt='' />";
					}
					else
					{
						if ($list_array[$key][date_of_expiry] and $list_array[$key][expiry_warning])
						{
							$date_of_expiry = new DatetimeHandler($list_array[$key][date_of_expiry]." 23:59:59");
							$warning_day = clone $date_of_expiry;
							$warning_day->sub_day($list_array[$key][expiry_warning]);
						
							if ($date_of_expiry->distance($today_end) > 0)
							{
								$list_array[$key][av] = "<img src='images/icons/red_point.png' alt='' />";
							}
							else
							{
								if ($warning_day->distance($today_end) > 0)
								{
									$list_array[$key][av] = "<img src='images/icons/yellow_point.png' alt='' />";
								}
								else
								{
									$list_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
								}
							}
						}
						else
						{
							$list_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
						}
					}
					
					$sample_id = $list_array[$key][id];
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
						
						$list_array[$key][symbol][link]		= $params;
						$list_array[$key][symbol][content] 	= "<img src='images/icons/sample.png' alt='' style='border:0;' />";
					
						unset($list_array[$key][id]);
						$list_array[$key][id][link] 			= $params;
						$list_array[$key][id][content]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
					
						$list_array[$key][name][link] 		= $params;
					}
					else
					{
						$list_array[$key][symbol]	= "<img src='core/images/denied_overlay.php?image=images/icons/sample.png' alt='N' border='0' />";
						$list_array[$key][id]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
					}
				}	
			}
			else
			{
				$list_request->empty_message("<span class='italic'>You have no Samples at the moment!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			// Error
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_organisation_unit_related_samples($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{
			return Sample_Wrapper::count_organisation_unit_samples($organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_sample_items($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$handling_class = Item::get_holder_handling_class_by_name($argument_array[0][1]);
		if ($handling_class)
		{
			$sql = $handling_class::get_item_list_sql($argument_array[1][1]);
		}
		
		if ($sql)
		{
			$list_request = new ListRequest_IO();
			
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			if ($argument_array[2][1] == true)
			{	
				$list_array = Sample_Wrapper::list_item_samples($sql, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			}
			else
			{
				$list_array = Sample_Wrapper::list_item_samples($sql, $sortvalue, $sortmethod, 0, null);
			}
			
			$list_request->set_column_array($json_column_array);
									
			if (is_array($list_array) and count($list_array) >= 1)
			{
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($list_array as $key => $value)
				{
					$tmp_name = trim($list_array[$key][name]);
					unset($list_array[$key][name]);
					$list_array[$key][name][label] = $tmp_name;
					$list_array[$key][name][content] = $tmp_name;
					
					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					$list_array[$key][template][label] = $tmp_template;
					$list_array[$key][template][content] = $tmp_template;
									
					if ($argument_array[3][1] == true)
					{
						$column_array = json_decode($json_column_array);
						if (is_array($column_array) and count($column_array) >= 1)
						{
							foreach ($column_array as $row_key => $row_value)
							{
								if ($row_value[1] == "checkbox")
								{
									if ($row_value[4])
									{
										$checkbox_class = $row_value[4];
										break;
									}
								}
							}
						}
						
						if ($checkbox_class)
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='sample-".$list_array[$key][item_id]."' value='1' class='".$checkbox_class."' checked='checked' />";
						}
						else
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='sample-".$list_array[$key][item_id]."' value='1' checked='checked' />";
						}
						
						$list_array[$key][symbol] = "<img src='images/icons/sample.png' alt='' style='border:0;' />";
						$list_array[$key][sid] = "S".str_pad($list_array[$key][id], 8 ,'0', STR_PAD_LEFT);
					}
					else
					{
						if ($list_array[$key][av] == "f")
						{
							$list_array[$key][av] = "<img src='images/icons/grey_point.png' alt='' />";
						}
						else
						{
							if ($list_array[$key][date_of_expiry] and $list_array[$key][expiry_warning])
							{
								$date_of_expiry = new DatetimeHandler($list_array[$key][date_of_expiry]." 23:59:59");
								$warning_day = clone $date_of_expiry;
								$warning_day->sub_day($list_array[$key][expiry_warning]);
							
								if ($date_of_expiry->distance($today_end) > 0)
								{
									$list_array[$key][av] = "<img src='images/icons/red_point.png' alt='' />";
								}
								else
								{
									if ($warning_day->distance($today_end) > 0)
									{
										$list_array[$key][av] = "<img src='images/icons/yellow_point.png' alt='' />";
									}
									else
									{
										$list_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
									}
								}
							}
							else
							{
								$list_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
							}
						}
						
						$sample_id = $list_array[$key][id];
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
							
							$list_array[$key][symbol][link]		= $params;
							$list_array[$key][symbol][content] 	= "<img src='images/icons/sample.png' alt='' style='border:0;' />";
						
							unset($list_array[$key][id]);
							$list_array[$key][sid][link] 			= $params;
							$list_array[$key][sid][content]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
						
							$list_array[$key][name][link] 		= $params;
						}
						else
						{
							$list_array[$key][symbol]	= "<img src='core/images/denied_overlay.php?image=images/icons/sample.png' alt='N' border='0' />";
							$list_array[$key][sid]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
						}
					}
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");
				
					if ($list_array[$key][owner])
					{
						$user = new User($list_array[$key][owner]);
					}
					else
					{
						$user = new User(1);
					}
					
					$list_array[$key][owner] = $user->get_full_name(true);
	
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No Samples found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			// Error
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_sample_items($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		$handling_class = Item::get_holder_handling_class_by_name($argument_array[0][1]);
		if ($handling_class)
		{
			$sql = $handling_class::get_item_list_sql($argument_array[1][1]);
		}
		
		if ($sql)
		{
			return Sample_Wrapper::count_item_samples($sql);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_samples_by_item_id($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		$item_id = $argument_array[0][1];
		
		if (is_numeric($item_id))
		{
			$list_request = new ListRequest_IO();
			
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			if ($argument_array[2][1] == true)
			{	
				$list_array = Sample_Wrapper::list_samples_by_item_id($item_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			}
			else
			{
				$list_array = Sample_Wrapper::list_samples_by_item_id($item_id, $sortvalue, $sortmethod, 0, null);
			}
			
			$list_request->set_column_array($json_column_array);
						
			if (is_array($list_array) and count($list_array) >= 1)
			{				
				$today_begin = new DatetimeHandler(date("Y-m-d")." 00:00:00");
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($list_array as $key => $value)
				{
					$tmp_name = trim($list_array[$key][name]);
					unset($list_array[$key][name]);
					$list_array[$key][name][label] = $tmp_name;
					$list_array[$key][name][content] = $tmp_name;
					
					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					$list_array[$key][template][label] = $tmp_template;
					$list_array[$key][template][content] = $tmp_template;
					
					if ($argument_array[1][1] == true)
					{
						$column_array = json_decode($json_column_array);
						if (is_array($column_array) and count($column_array) >= 1)
						{
							foreach ($column_array as $row_key => $row_value)
							{
								if ($row_value[1] == "checkbox")
								{
									if ($row_value[4])
									{
										$checkbox_class = $row_value[4];
										break;
									}
								}
							}
						}
						
						if ($checkbox_class)
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='parent-sample-".$list_array[$key][id]."' value='1' class='".$checkbox_class."' checked='checked' />";
						}
						else
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='parent-sample-".$list_array[$key][id]."' value='1' checked='checked' />";
						}
						
						$list_array[$key][symbol] = "<img src='images/icons/sample.png' alt='' style='border:0;' />";
						$list_array[$key][sid] = "S".str_pad($list_array[$key][id], 8 ,'0', STR_PAD_LEFT);
					}
					else
					{					
						if ($list_array[$key][av] == "f")
						{
							$list_array[$key][av] = "<img src='images/icons/grey_point.png' alt='' />";
						}
						else
						{
							if ($list_array[$key][date_of_expiry] and $list_array[$key][expiry_warning])
							{
								$date_of_expiry = new DatetimeHandler($list_array[$key][date_of_expiry]." 23:59:59");
								$warning_day = clone $date_of_expiry;
								$warning_day->sub_day($list_array[$key][expiry_warning]);
							
								if ($date_of_expiry->distance($today_end) > 0)
								{
									$list_array[$key][av] = "<img src='images/icons/red_point.png' alt='' />";
								}
								else
								{
									if ($warning_day->distance($today_end) > 0)
									{
										$list_array[$key][av] = "<img src='images/icons/yellow_point.png' alt='' />";
									}
									else
									{
										$list_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
									}
								}
							}
							else
							{
								$list_array[$key][av] = "<img src='images/icons/green_point.png' alt='' />";
							}
						}
	
											
						$sample_id = $list_array[$key][id];
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
							
							$list_array[$key][symbol][link]		= $params;
							$list_array[$key][symbol][content] 	= "<img src='images/icons/sample.png' alt='' style='border:0;' />";
						
							unset($list_array[$key][id]);
							$list_array[$key][sid][link] 		= $params;
							$list_array[$key][sid][content]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
						
							$list_array[$key][name][link] 		= $params;
						}
						else
						{
							$list_array[$key][symbol]	= "<img src='core/images/denied_overlay.php?image=images/icons/sample.png' alt='N' border='0' />";
							$list_array[$key][sid]		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
						}
					}
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y");
				
					if ($list_array[$key][owner])
					{
						$user = new User($list_array[$key][owner]);
					}
					else
					{
						$user = new User(1);
					}
					
					$list_array[$key][owner] = $user->get_full_name(true);
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No Samples found!</span>");
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			// Error
		}
	}

	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_samples_by_item_id($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		$item_id = $argument_array[0][1];
		
		if (is_numeric($item_id))
		{
			return Sample_Wrapper::count_samples_by_item_id($item_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 */
	public static function list_location_history($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $sample_security;
		
		$argument_array = json_decode($json_argument_array);
		$sample_id = $argument_array[0][1];
		
		if (is_numeric($sample_id))
		{
			if ($sample_security->is_access(1, false))
			{
			
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = Sample_Wrapper::list_sample_locations($sample_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
				
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$list_array[$key][symbol] = "<img src='images/icons/sample.png' alt='' style='border:0;' />";
						
						$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
						$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
					
						if ($list_array[$key][user])
						{
							$user = new User($list_array[$key][user]);
						}
						else
						{
							$user = new User(1);
						}
						
						$list_array[$key][user] = $user->get_full_name(false);
					}
				}
				else
				{
					$list_request->empty_message("<span class='italic'>No results found!</span>");
				}
				
				$list_request->set_array($list_array);
				
				return $list_request->get_page($page);
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 */
	public static function count_location_history($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		$sample_id = $argument_array[0][1];
		
		if (is_numeric($sample_id))
		{
			return Sample_Wrapper::count_sample_locations($sample_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $get_array
	 * @param integer $sample_id
	 * @return string
	 */
	public static function associate($get_array, $sample_id)
	{
		global $session;
		
		if ($get_array and is_numeric($sample_id))
		{
			$_GET = unserialize($get_array);
			
			$post_array = array();
			$post_array['keywords'] = $session->read_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET[idk_unique_id]);
			$post_array['description'] = $session->read_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET[idk_unique_id]);	

			$sample = new Sample($sample_id);
			
			$item_add_event = new ItemAddEvent($sample->get_item_id(), $_GET, $post_array, true, "sample");
			$event_handler = new EventHandler($item_add_event);
			if ($event_handler->get_success() == true)
			{
				if ($_GET['retrace'])
				{
					$params = http_build_query(Retrace::resolve_retrace_string($_GET['retrace']),'','&');
					return "index.php?".$params;
				}
				else
				{
					$paramquery['username'] = $username;
					$paramquery['session_id'] = $session_id;
					$paramquery['nav'] = "home";
					$params = http_build_query($paramquery,'','&');
					return "index.php?".$params;
				}
			}
			else
			{
				return "0";
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $get_array
	 */
	public static function get_sample_menu($get_array)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET[sample_id])
		{
			$sample_security = new SampleSecurity($_GET[sample_id]);
			
			if ($sample_security->is_access(1, false))
			{
				$sample = new Sample($_GET[sample_id]);
				
				$template = new HTMLTemplate("sample/ajax/detail_menu.html");
				
				if ($sample->get_availability() == true)
				{
					$template->set_var("new_status", "not available");
				}
				else
				{
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
				
				if ($sample_security->is_access(2))
				{
					$sample_template 				= new SampleTemplate($sample->get_template_id());
					$current_requirements 			= $sample->get_requirements();
					
					$result = array();
					$counter = 0;
					
					if (is_array($current_requirements) and count($current_requirements) >= 1)
					{
						foreach($current_requirements as $key => $value)
						{	
							switch ($value['element_type']):
							
								case "item":
									$paramquery = array();
									$paramquery[username] = $_GET[username];
									$paramquery[session_id] = $_GET[session_id];
									$paramquery[nav] = "sample";
									$paramquery[sample_id] = $_GET[sample_id];
									
									require_once("core/modules/item/common/item_common.io.php");
									
									$menu_element_array = ItemCommonIO::get_menu_element($value, $key, $counter, $paramquery, "Sample", $_GET['sample_id']);
									$result = array_merge($result, $menu_element_array[0]);
									$counter = $menu_element_array[1];	
								break;
								
								case "extension":
									// Extension implementation in Sample
								break;
								
							endswitch;
						}			
					}
				
					$template->set_var("action",$result);
				}
				else
				{
					$template->set_var("action","");
				}
			
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
		}
	}
	
	/**
	 * @param string $get_array
	 */
	public static function get_sample_information($get_array)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET[sample_id])
		{
			$sample_security = new SampleSecurity($_GET[sample_id]);
			
			if ($sample_security->is_access(1, false))
			{
				$sample = new Sample($_GET[sample_id]);
				$owner = new User($sample->get_owner_id());	
				
				$template = new HTMLTemplate("sample/ajax/detail_information.html");
				
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
				}
				else
				{
					$template->set_var("status", "not available");
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
								
				$template->output();
			}
		}
	}
	
	/**
	 * @param string $get_array
	 */
	public static function delete($get_array)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['sample_id'])
		{
			$project = new Sample($_GET['sample_id']);
						
			$template = new HTMLTemplate("sample/int_admin/delete_window.html");
			
			$array['continue_caption'] = "Yes";
			$array['cancel_caption'] = "No";
			$array['content_caption'] = "Delete Sample";
			$array['height'] = 200;
			$array['width'] = 400;
			$array['content'] = $template->get_string();
			$array['container'] = "#SampleDeleteWindow";
			
			$continue_handler_template = new JSTemplate("sample/int_admin/js/delete_continue_handler.js");
			$continue_handler_template->set_var("username", $_GET['username']);
			$continue_handler_template->set_var("session_id", $_GET['session_id']);
			$continue_handler_template->set_var("get_array", $get_array);
			
			$array['continue_handler'] = $continue_handler_template->get_string();
			
			return json_encode($array);
		}
	}
	
	/**
	 * @param string $get_array
	 * @throws SampleException
	 * @throws SampleSecurityException
	 */
	public static function delete_handler($get_array)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		if ($_GET['sample_id'])
		{
			if ($user->is_admin())
			{
				$sample = new Sample($_GET['sample_id']);
				
				if ($sample->delete() == true)
				{
					return "1";
				}
				else
				{
					throw new SampleDeleteException();
				}
			}
			else
			{
				throw new SampleSecurityAccessDeniedExcpetion();
			}
		}
	}
}
?>