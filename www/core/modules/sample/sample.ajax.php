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
 * 
 */
require_once("../base/ajax.php");

/**
 * Sample AJAX IO Class
 * @package sample
 */
class SampleAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	
	private function list_user_related_samples($json_row_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		$list_request = new ListRequest_IO();
		$list_request->set_row_array($json_row_array);
		
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
				
				if (strlen($tmp_name) > 17)
				{
					$list_array[$key][name][label] = $tmp_name;
					$list_array[$key][name][content] = substr($tmp_name,0,17)."...";
				}
				else
				{
					$list_array[$key][name][label] = $tmp_name;
					$list_array[$key][name][content] = $tmp_name;
				}
				
				$tmp_template = trim($list_array[$key][template]);
				unset($list_array[$key][template]);
				
				if (strlen($tmp_template) > 25)
				{
					$list_array[$key][template][label] = $tmp_template;
					$list_array[$key][template][content] = substr($tmp_template,0,25)."...";
				}
				else
				{
					$list_array[$key][template][label] = $tmp_template;
					$list_array[$key][template][content] = $tmp_template;
				}
				
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
	
	private function list_organisation_unit_related_samples($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$organisation_unit_id = $argument_array[0][1];
		
		if (is_numeric($organisation_unit_id))
		{
			$list_request = new ListRequest_IO();
			$list_request->set_row_array($json_row_array);
		
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
					
					if (strlen($tmp_name) > 17)
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = substr($tmp_name,0,17)."...";
					}
					else
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = $tmp_name;
					}
					
					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					
					if (strlen($tmp_template) > 25)
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = substr($tmp_template,0,25)."...";
					}
					else
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = $tmp_template;
					}
					
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
	
	private function list_sample_items($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
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
			
			$list_request->set_row_array($json_row_array);
									
			if (is_array($list_array) and count($list_array) >= 1)
			{
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($list_array as $key => $value)
				{
					$tmp_name = trim($list_array[$key][name]);
					unset($list_array[$key][name]);
					
					if (strlen($tmp_name) > 17)
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = substr($tmp_name,0,17)."...";
					}
					else
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = $tmp_name;
					}
					
					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					
					if (strlen($tmp_template) > 25)
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = substr($tmp_template,0,25)."...";
					}
					else
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = $tmp_template;
					}
									
					if ($argument_array[3][1] == true)
					{
						$row_array = json_decode($json_row_array);
						if (is_array($row_array) and count($row_array) >= 1)
						{
							foreach ($row_array as $row_key => $row_value)
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
	
	private function list_samples_by_item_id($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
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
			
			$list_request->set_row_array($json_row_array);
						
			if (is_array($list_array) and count($list_array) >= 1)
			{				
				$today_begin = new DatetimeHandler(date("Y-m-d")." 00:00:00");
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($list_array as $key => $value)
				{
					$tmp_name = trim($list_array[$key][name]);
					unset($list_array[$key][name]);
					
					if (strlen($tmp_name) > 17)
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = substr($tmp_name,0,17)."...";
					}
					else
					{
						$list_array[$key][name][label] = $tmp_name;
						$list_array[$key][name][content] = $tmp_name;
					}
					
					$tmp_template = trim($list_array[$key][template]);
					unset($list_array[$key][template]);
					
					if (strlen($tmp_template) > 25)
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = substr($tmp_template,0,25)."...";
					}
					else
					{
						$list_array[$key][template][label] = $tmp_template;
						$list_array[$key][template][content] = $tmp_template;
					}
					
					if ($argument_array[1][1] == true)
					{
						$row_array = json_decode($json_row_array);
						if (is_array($row_array) and count($row_array) >= 1)
						{
							foreach ($row_array as $row_key => $row_value)
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
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
	
				case "list_user_related_samples":
					echo $this->list_user_related_samples($_POST[row_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "list_organisation_unit_related_samples":
					echo $this->list_organisation_unit_related_samples($_POST[row_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "list_sample_items":
					echo $this->list_sample_items($_POST[row_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "list_samples_by_item_id":
					echo $this->list_samples_by_item_id($_POST[row_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				default:
				break;
			
			endswitch;
		}
	}
}

$sample_ajax = new SampleAjax;
$sample_ajax->method_handler();
?>