<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Sample Search AJAX IO Class
 * @package sample
 */
class SampleSearchAjax
{
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
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function list_samples($json_column_array, $json_argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		if (is_array($argument_array))
		{
			$name = $argument_array[0][1];
			$organisation_unit_array = $argument_array[1][1];
			$template_array = $argument_array[2][1];
			$in_id = $argument_array[3][1];
			$in_name = $argument_array[4][1];
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = Sample_Wrapper::list_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				$today_end = new DatetimeHandler(date("Y-m-d")." 23:59:59");
				
				foreach($list_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($list_array[$key]['datetime']);
					$list_array[$key]['datetime'] = $datetime_handler->get_datetime(false);
	
					if ($list_array[$key]['av'] == "f")
					{
						$list_array[$key]['av'] = "<img src='images/icons/grey_point.png' alt='' />";
					}
					else
					{
						if ($list_array[$key]['date_of_expiry'] and $list_array[$key]['expiry_warning'])
						{
							$date_of_expiry = new DatetimeHandler($list_array[$key]['date_of_expiry']." 23:59:59");
							$warning_day = clone $date_of_expiry;
							$warning_day->sub_day($list_array[$key]['expiry_warning']);
						
							if ($date_of_expiry->distance($today_end) > 0)
							{
								$list_array[$key]['av'] = "<img src='images/icons/red_point.png' alt='' />";
							}
							else
							{
								if ($warning_day->distance($today_end) > 0)
								{
									$list_array[$key]['av'] = "<img src='images/icons/yellow_point.png' alt='' />";
								}
								else
								{
									$list_array[$key]['av'] = "<img src='images/icons/green_point.png' alt='' />";
								}
							}
						}
						else
						{
							$list_array[$key]['av'] = "<img src='images/icons/green_point.png' alt='' />";
						}
					}
					
					$sample_id = $list_array[$key]['id'];
					$sample_security = new SampleSecurity($sample_id);
					
					if ($sample_security->is_access(1, false))
					{
						$paramquery = array();
						$paramquery['username'] = $_GET['username'];
						$paramquery['session_id'] = $_GET['session_id'];
						$paramquery['nav'] = "sample";
						$paramquery['run'] = "detail";
						$paramquery['sample_id'] = $sample_id;
						$params = http_build_query($paramquery,'','&#38;');
						
						$list_array[$key]['symbol']['link']		= $params;
						$list_array[$key]['symbol']['content'] 	= "<img src='images/icons/sample.png' alt='' style='border:0;' />";
					
						unset($list_array[$key]['id']);
						$list_array[$key]['id']['link'] 		= $params;
						$list_array[$key]['id']['content']		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
					
						$sample_name = $list_array[$key]['name'];
						unset($list_array[$key]['name']);
						$list_array[$key]['name']['link'] 		= $params;
						$list_array[$key]['name']['content']	= $sample_name;
					}
					else
					{
						$list_array[$key]['symbol']	= "<img src='core/images/denied_overlay.php?image=images/icons/sample.png' alt='N' border='0' />";
						$list_array[$key]['id']		= "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
					}
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
			throw new BaseAjaxArgumentMissingException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseAjaxArgumentMissingException
	 */
	public static function count_samples($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		
		if (is_array($argument_array))
		{
			$name = $argument_array[0][1];
			$organisation_unit_array = $argument_array[1][1];
			$template_array = $argument_array[2][1];
			$in_id = $argument_array[3][1];
			$in_name = $argument_array[4][1];
			
			return Sample_Wrapper::count_sample_search($name, $organisation_unit_array, $template_array, $in_id, $in_name);
		}
		else
		{
			throw new BaseAjaxArgumentMissingException();
		}
	}
}
?>