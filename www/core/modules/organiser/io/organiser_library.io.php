<?php
/**
 * @package organiser
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
 * Organiser Library IO Class
 * Class handles different calendar and todo-views.
 * @package organiser
 */
class OrganiserLibraryIO {
	
	private $year;
	private $month;
	private $week;
	private $work_week;
	private $day;
	private $todo;
	
	private $calendar_array;
	private $todo_array;
	
	/**
	 * @param integer $views
	 * 1 = Year
	 * 2 = Month
	 * 4 = Week
	 * 8 = Work Week
	 * 16 = Day
	 * 32 = Todo
	 */
	function __construct($views)
	{
		 if ($views)
		 {
		 	$views_bin = str_pad(strrev(decbin($views)), 6, '0', STR_PAD_RIGHT);
		 	
		 	if ($views_bin{0} == 1)
		 	{
		 		$this->year 		= true;
		 	}
		 	else
		 	{
		 		$this->year			= false;
		 	}
		 	
		 	if ($views_bin{1} == 1)
		 	{
		 		$this->month		= true;
		 	}
		 	else
		 	{
		 		$this->month		= false;
		 	}
		 	
		 	if ($views_bin{2} == 1)
		 	{
		 		$this->week			= true;
		 	}
		 	else
		 	{
		 		$this->week			= false;
		 	}
		 	
		 	if ($views_bin{3} == 1)
		 	{
		 		$this->work_week	= true;
		 	}
		 	else
		 	{
		 		$this->work_week	= false;
		 	}
		 	
		 	if ($views_bin{4} == 1)
		 	{
		 		$this->day			= true;
		 	}
		 	else
		 	{
		 		$this->day			= false;
		 	}
		 	
		 	if ($views_bin{5} == 1)
		 	{
		 		$this->todo			= true;
		 	}
		 	else
		 	{
		 		$this->todo			= false;
		 	}
		 }
		 else
		 {
		 	$this->year			= false;
			$this->month		= false;
			$this->week			= false;
			$this->work_week	= false;
			$this->day			= false;
			$this->todo			= false;
		 }
	}

	/**
	 * @param integer $start_mktime
	 * @param integer $end_mktime
	 * @return integer
	 */
	private function mktime_has_date($start_mktime, $end_mktime)
	{
		if (is_numeric($start_mktime) and is_numeric($end_mktime))
		{
			if (is_array($this->calendar_array) and count($this->calendar_array) >= 1)
			{
				$return_array = array();
				
				foreach($this->calendar_array as $key => $value)
				{
					$date_start_datetime_handler = new DatetimeHandler($value[start_date]." ".$value[start_time]);
					$date_end_datetime_handler = new DatetimeHandler($value[end_date]." ".$value[end_time]);
					
					$date_start_mktime = $date_start_datetime_handler->get_mktime();
					$date_end_mktime = $date_end_datetime_handler->get_mktime();
					
					if ($date_start_mktime <= $end_mktime and $date_end_mktime >= $start_mktime)
					{
						array_push($return_array, $key);
					}
				}
				
				if (count($return_array) >= 1)
				{
					return $return_array;
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	private function year_view()
	{
		$today = date("j-m-Y");
		$today_datetime_handler = new DatetimeHandler($today);
		
		if (!$_GET[page])
		{
			$date = date("Y");
		}
		else
		{
			$date = $_GET[page];
		}
		
		$datetime_handler = new DatetimeHandler("1-1-".$date);
		
		$next_year_datetime_handler = clone $datetime_handler;
		$next_year_datetime_handler->add_year(1);
		
		$prev_year_datetime_handler = clone $datetime_handler;
		$prev_year_datetime_handler->sub_year(1);
		
		
		$year = date("Y", $datetime_handler->get_mktime());
		
		$prev_year = date("Y", $prev_year_datetime_handler->get_mktime());
		$next_year = date("Y", $next_year_datetime_handler->get_mktime());
		
		$content_array = array();
		
		for ($i=0;$i<=11;$i++)
		{
			$mktime = $datetime_handler->get_mktime();

			$number_of_days = date("t", $mktime);
			$first_day_of_month = date("w", $mktime);
			$first_day_of_month = ($first_day_of_month + 6) % 7;					
			$number_of_weeks = ceil(($number_of_days+$first_day_of_month)/7)-1;
			
			$month_name = date("F", $mktime);
			
			$content_array[$i][monthname] = $month_name;
			
			$begin = false;		
			for ($j=0; $j<=$number_of_weeks; $j++)
			{
				$mktime = $datetime_handler->get_mktime();
							
				$week = date("W", $mktime);
				
				$content_array[$i][$j][week] = $week;
				
				for ($k=0; $k<=6;$k++)
				{
					$mktime = $datetime_handler->get_mktime();
				
					if ($j == 0 and $k == $first_day_of_month)
					{						
						$begin = true;
		
						$end_mktime = $mktime + 86399;
		
						$date_array = $this->mktime_has_date($mktime, $end_mktime);
						
						if (is_array($date_array) and count($date_array) >= 1)
						{
							$content_array[$i][$j][$k][date] = true;
						}
						else
						{
							$content_array[$i][$j][$k][date] = false;
						}
	
						$day = date("d", $mktime);
	
						if ($datetime_handler->get_formatted_string("Y-m-d") == $today_datetime_handler->get_formatted_string("Y-m-d"))
						{
							$content_array[$i][$j][$k][content] = "<span class='CalendarToday'>".$day."</span>";
						}
						else
						{
							$content_array[$i][$j][$k][content] = $day;
						}
						
						if ($this->calendar_array[$date_array[0]][link])
						{
							$content_array[$i][$j][$k][link] = true;
							$content_array[$i][$j][$k][link_params] = $this->calendar_array[$date_array[0]][link];
						}
						else
						{
							$content_array[$i][$j][$k][link] = false;
						}
												
						$datetime_handler->add_day(1);
					}
					elseif($begin == true)
					{
						$end_mktime = $mktime + 86399;
						
						$date_array = $this->mktime_has_date($mktime, $end_mktime);
						
						if (is_array($date_array) and count($date_array) >= 1)
						{
							$content_array[$i][$j][$k][date] = true;
						}
						else
						{
							$content_array[$i][$j][$k][date] = false;
						}
						
						$day = date("d", $mktime);
						
						if ($datetime_handler->get_formatted_string("Y-m-d") == $today_datetime_handler->get_formatted_string("Y-m-d"))
						{
							$content_array[$i][$j][$k][content] = "<span class='CalendarToday'>".$day."</span>";
						}
						else
						{
							$content_array[$i][$j][$k][content] = $day;
						}
						
						if ($this->calendar_array[$date_array[0]][link])
						{
							$content_array[$i][$j][$k][link] = true;
							$content_array[$i][$j][$k][link_params] = $this->calendar_array[$date_array[0]][link];
						}
						else
						{
							$content_array[$i][$j][$k][link] = false;
						}
											
						$datetime_handler->add_day(1);
						
						if ($day == $number_of_days)
						{
							$begin = false;
						}
					}
					else
					{
						$content_array[$i][$j][$k][content] = "";
					}			
				}
			}
				
			if ((($i+1) % 4) == 0)
			{
				$content_array[$i][display_tr] = true;
			}
			else
			{
				$content_array[$i][display_tr] = false;
			}
		}
				
		$template = new HTMLTemplate("organiser/views/year.html");
		
		$paramquery_prev = $_GET;
		$paramquery_prev[page] = $prev_year;
		$params_prev = http_build_query($paramquery_prev,'','&#38;');
		
		$paramquery_next = $_GET;
		$paramquery_next[page] = $next_year;
		$params_next = http_build_query($paramquery_next,'','&#38;');
		
		$template->set_var("previous_params", $params_prev);
		$template->set_var("next_params", $params_next);
		
		$template->set_var("year", $year);
		$template->set_var("content_array", $content_array);
		
		return $template->get_string();
	}
	
	private function month_view()
	{
		$today = date("j-m-Y");
		$today_datetime_handler = new DatetimeHandler($today);
		
		if (!$_GET[page])
		{
			$date = date("m-Y");
		}
		else
		{
			$date = $_GET[page];
		}
		
		$datetime_handler = new DatetimeHandler("1-".$date);
		
		$next_month_datetime_handler = clone $datetime_handler;
		$next_month_datetime_handler->add_month(1);
		
		$prev_month_datetime_handler = clone $datetime_handler;
		$prev_month_datetime_handler->sub_month(1);
		
		$mktime = $datetime_handler->get_mktime();
		
		$number_of_days = date("t",$mktime);				
		$first_day_of_month = date("w", $mktime);
		
		$first_day_of_month = ($first_day_of_month + 6) % 7;
		
		$number_of_weeks = ceil(($number_of_days+$first_day_of_month)/7)-1;
		
		$begin = false;
		
		$year 		= date("Y", $mktime);
		$month 		= date("m", $mktime);
		$month_name = date("F", $mktime);
				
		$content_array = array();		
		
		for ($i=0; $i<=$number_of_weeks; $i++)
		{
			$mktime = $datetime_handler->get_mktime();
			
			$week = date("W", $mktime);
			
			$content_array[$i][week] = $week; 
						
			for ($j=0; $j<=6;$j++)
			{
				$mktime = $datetime_handler->get_mktime();
			
				if ($i == 0 and $j == $first_day_of_month)
				{
					$begin = true;
			
					$date_array = $this->mktime_has_date($mktime, $mktime+21599);
					if (is_array($date_array) and count($date_array) >= 1)
					{
						$content_array[$i][$j][value1] = $this->calendar_array[$date_array[0]][color];
					}
					else
					{
						$content_array[$i][$j][value1] = "FFFFFF";
					}
									
					$date_array = $this->mktime_has_date($mktime+21601, $mktime+43199);
					if (is_array($date_array) and count($date_array) >= 1)
					{
						$content_array[$i][$j][value2] = $this->calendar_array[$date_array[0]][color];
					}
					else
					{
						$content_array[$i][$j][value2] = "FFFFFF";	
					}
									
					$date_array = $this->mktime_has_date($mktime+43201, $mktime+64799);
					if (is_array($date_array) and count($date_array) >= 1)
					{
						$content_array[$i][$j][value3] = $this->calendar_array[$date_array[0]][color];
					}
					else
					{
						$content_array[$i][$j][value3] = "FFFFFF";	
					}
									
					$date_array = $this->mktime_has_date($mktime+64801, $mktime+86399);
					if (is_array($date_array) and count($date_array) >= 1)
					{
						$content_array[$i][$j][value4] = $this->calendar_array[$date_array[0]][color];
					}
					else
					{
						$content_array[$i][$j][value4] = "FFFFFF";	
					}

					$day = date("d", $mktime);
					
					if ($datetime_handler->get_formatted_string("Y-m-d") == $today_datetime_handler->get_formatted_string("Y-m-d"))
					{
						$content_array[$i][$j][content] = "<span class='CalendarToday'>".$day."</span>";
					}
					else
					{
						$content_array[$i][$j][content] = $day;
					}
					
					if ($this->calendar_array[$date_array[0]][link])
					{
						$content_array[$i][$j][link] = true;
						$content_array[$i][$j][link_params] = $this->calendar_array[$date_array[0]][link];
					}
					else
					{
						$content_array[$i][$j][link] = false;
					}
					
					$content_array[$i][$j][today] = false;
											
					$datetime_handler->add_day(1);
				}
				elseif($begin == true)
				{
					$date_array = $this->mktime_has_date($mktime, $mktime+21599);
					if (is_array($date_array) and count($date_array) >= 1)
					{
						$content_array[$i][$j][value1] = $this->calendar_array[$date_array[0]][color];
					}
					else
					{
						$content_array[$i][$j][value1] = "FFFFFF";
					}
									
					$date_array = $this->mktime_has_date($mktime+21601, $mktime+43199);
					if (is_array($date_array) and count($date_array) >= 1)
					{
						$content_array[$i][$j][value2] = $this->calendar_array[$date_array[0]][color];
					}
					else
					{
						$content_array[$i][$j][value2] = "FFFFFF";	
					}
									
					$date_array = $this->mktime_has_date($mktime+43201, $mktime+64799);
					if (is_array($date_array) and count($date_array) >= 1)
					{
						$content_array[$i][$j][value3] = $this->calendar_array[$date_array[0]][color];
					}
					else
					{
						$content_array[$i][$j][value3] = "FFFFFF";	
					}
									
					$date_array = $this->mktime_has_date($mktime+64801, $mktime+86399);
					if (is_array($date_array) and count($date_array) >= 1)
					{
						$content_array[$i][$j][value4] = $this->calendar_array[$date_array[0]][color];
					}
					else
					{
						$content_array[$i][$j][value4] = "FFFFFF";	
					}					
					
					$day = date("d", $mktime);
					
					if ($datetime_handler->get_formatted_string("Y-m-d") == $today_datetime_handler->get_formatted_string("Y-m-d"))
					{
						$content_array[$i][$j][content] = "<span class='CalendarToday'>".$day."</span>";
					}
					else
					{
						$content_array[$i][$j][content] = $day;
					}
					
					if ($this->calendar_array[$date_array[0]][link])
					{
						$content_array[$i][$j][link] = true;
						$content_array[$i][$j][link_params] = $this->calendar_array[$date_array[0]][link];
					}
					else
					{
						$content_array[$i][$j][link] = false;
					}
					
					$content_array[$i][$j][today] = false;
										
					$datetime_handler->add_day(1);
					
					if ($day == $number_of_days)
					{
						$begin = false;
					}
				}
				else
				{
					$content_array[$i][$j][content] = "";
					$content_array[$i][$j][value1] = "FFFFFF";
					$content_array[$i][$j][value2] = "FFFFFF";
					$content_array[$i][$j][value3] = "FFFFFF";
					$content_array[$i][$j][value4] = "FFFFFF";
				}		
			}
		}
		
		$template = new HTMLTemplate("organiser/views/month.html");
		
		$paramquery_prev = $_GET;
		$paramquery_prev[page] = date("m-Y", $prev_month_datetime_handler->get_mktime());
		$params_prev = http_build_query($paramquery_prev,'','&#38;');
		
		$paramquery_next = $_GET;
		$paramquery_next[page] = date("m-Y", $next_month_datetime_handler->get_mktime());
		$params_next = http_build_query($paramquery_next,'','&#38;');
		
		$template->set_var("previous_params", $params_prev);
		$template->set_var("next_params", $params_next);
		
		$template->set_var("year", $year);
		$template->set_var("month_name", $month_name);
		$template->set_var("content_array", $content_array);
		
		return $template->get_string();	
	}
	
	private function week_view()
	{
		$today = date("j-m-Y");
		$today_datetime_handler = new DatetimeHandler($today);
		
		if (!$_GET[page])
		{
			$date = date("W-Y");
		}
		else
		{
			$date = $_GET[page];
		}
		
		$date_aray = explode("-",$date);
		
		$week_mktime = mktime(0,0,0,1,1,$date_aray[1]);
		$week_mktime = $week_mktime+(86400*($date_aray[0])*7);
		$act_day_of_week = (date("w",$week_mktime) + 6) % 7;
		$week_mktime = $week_mktime-($act_day_of_week*86400);
		
		$datetime_handler = new DatetimeHandler($week_mktime);
		$datetime_handler->add_hour(5);

		$next_week_datetime_handler = clone $datetime_handler;
		$next_week_datetime_handler->add_day(7);
		
		$prev_week_datetime_handler = clone $datetime_handler;
		$prev_week_datetime_handler->sub_day(7);

		$week = date("W", $week_mktime);
		$year = date("Y", $week_mktime);
		
		$content_array = array();		
				
		$color_count = 0;
		
		$day_date_array[1] = array();
		$day_date_array[2] = array();
		$day_date_array[3] = array();
		$day_date_array[4] = array();
		$day_date_array[5] = array();
		$day_date_array[6] = array();
		$day_date_array[7] = array();
		
		for ($i=0;$i<=18;$i++)
		{
			if ($color_count % 2)
			{
				$tr_class = " class='CalendarTrGrey'";
			}
			else
			{
				$tr_class = "";
			}
		
			$content_array[$i][tr_class] = $tr_class;
					
			if ($i==0)
			{
				$content_array[$i][head] = true;
				
				$content_array[$i][0][content] = "";
			
				for ($j=1;$j<=7;$j++)
				{
					$mktime = $datetime_handler->get_mktime();

					$day = date("D jS M",$mktime);
					
					if ($datetime_handler->get_formatted_string("Y-m-d") == $today_datetime_handler->get_formatted_string("Y-m-d"))
					{
						$content_array[$i][$j][content] = "<span class='CalendarToday'>".$day."</span>";
					}
					else
					{
						$content_array[$i][$j][content] = $day;
					}

					$datetime_handler->add_day(1);
				}
			}
			else
			{
				$datetime_handler->sub_day(7);
				
				$content_array[$i][head] = false;
				
				$content_array[$i][0][time] = true;
				$content_array[$i][0][content] = $datetime_handler->get_formatted_string("H:i");
				
				for ($j=1;$j<=7;$j++)
				{
					$content_array[$i][$j][time] = false;
					
					$mktime = $datetime_handler->get_mktime();
					
					$date_array = $this->mktime_has_date($mktime, $mktime+3599);
					
					if (is_array($date_array) and count($date_array >= 1))
					{
						$counter = 0;
						foreach ($date_array as $key => $value)
						{
							if (!in_array($value, $day_date_array[$j]))
							{
								if ($content_array[$i][$j][content])
								{
									if ($this->calendar_array[$value][link])
									{
										$content_array[$i][$j][content] .= "<div class='CalendarWeekEntry'><a href='index.php?".$this->calendar_array[$value][link]."'>".$this->calendar_array[$value][name]."</a></div>";
									}
									else
									{
										$content_array[$i][$j][content] .= "<div class='CalendarWeekEntry'>".$this->calendar_array[$value][name]."</div>";
									}
								}
								else
								{
									if ($this->calendar_array[$value][link])
									{
										$content_array[$i][$j][content] = "<div class='CalendarWeekEntry'><a href='index.php?".$this->calendar_array[$value][link]."'>".$this->calendar_array[$value][name]."</a></div>";
									}
									else
									{
										$content_array[$i][$j][content] = "<div class='CalendarWeekEntry'>".$this->calendar_array[$value][name]."</div>";
									}
								}
								array_push($day_date_array[$j],$value);
							}
							else
							{
								$content_array[$i][$j][content] = "";
							}
							
							$content_array[$i][$j][$counter][value] = $this->calendar_array[$value][color];
								
							$counter++;		
						}
					}
					else
					{
						$content_array[$i][$j][content] = "";
					}
					$datetime_handler->add_day(1);
				}
				$datetime_handler->add_hour(1);
			}
			$color_count++;
		}
		
		$template = new HTMLTemplate("organiser/views/week.html");
		
		$paramquery_prev = $_GET;
		$paramquery_prev[page] = date("W-Y", $prev_week_datetime_handler->get_mktime());
		$params_prev = http_build_query($paramquery_prev,'','&#38;');
		
		$paramquery_next = $_GET;
		$paramquery_next[page] = date("W-Y", $next_week_datetime_handler->get_mktime());
		$params_next = http_build_query($paramquery_next,'','&#38;');
		
		$template->set_var("previous_params", $params_prev);
		$template->set_var("next_params", $params_next);
		
		$template->set_var("week", $week);
		$template->set_var("year", $year);
		$template->set_var("content_array", $content_array);
		
		return $template->get_string();
	}

	private function work_week_view()
	{
		$today = date("j-m-Y");
		$today_datetime_handler = new DatetimeHandler($today);
		
		if (!$_GET[page])
		{
			$date = date("W-Y");
		}
		else
		{
			$date = $_GET[page];
		}
		
		$date_aray = explode("-",$date);
		
		$week_mktime = mktime(0,0,0,1,1,$date_aray[1]);
		$week_mktime = $week_mktime+(86400*($date_aray[0])*7);
		$act_day_of_week = (date("w",$week_mktime) + 6) % 7;
		$week_mktime = $week_mktime-($act_day_of_week*86400);
		
		$datetime_handler = new DatetimeHandler($week_mktime);
		$datetime_handler->add_hour(5);

		$next_week_datetime_handler = clone $datetime_handler;
		$next_week_datetime_handler->add_day(7);
		
		$prev_week_datetime_handler = clone $datetime_handler;
		$prev_week_datetime_handler->sub_day(7);

		$week = date("W", $week_mktime);
		$year = date("Y", $week_mktime);
		
		$content_array = array();		
				
		$color_count = 0;
		
		$day_date_array[1] = array();
		$day_date_array[2] = array();
		$day_date_array[3] = array();
		$day_date_array[4] = array();
		$day_date_array[5] = array();
		
		for ($i=0;$i<=18;$i++)
		{
			if ($color_count % 2)
			{
				$tr_class = " class='CalendarTrGrey'";
			}
			else
			{
				$tr_class = "";
			}
		
			$content_array[$i][tr_class] = $tr_class;
					
			if ($i==0)
			{
				$content_array[$i][head] = true;
				
				$content_array[$i][0][content] = "";
			
				for ($j=1;$j<=5;$j++)
				{
					$mktime = $datetime_handler->get_mktime();

					$day = date("D jS M",$mktime);
					
					if ($datetime_handler->get_formatted_string("Y-m-d") == $today_datetime_handler->get_formatted_string("Y-m-d"))
					{
						$content_array[$i][$j][content] = "<span class='CalendarToday'>".$day."</span>";
					}
					else
					{
						$content_array[$i][$j][content] = $day;
					}

					$datetime_handler->add_day(1);
				}
			}
			else
			{
				$datetime_handler->sub_day(5);
				
				$content_array[$i][head] = false;
				
				$content_array[$i][0][time] = true;
				$content_array[$i][0][content] = $datetime_handler->get_formatted_string("H:i");
				
				for ($j=1;$j<=5;$j++)
				{
					$content_array[$i][$j][time] = false;
					
					$mktime = $datetime_handler->get_mktime();
					
					$date_array = $this->mktime_has_date($mktime, $mktime+3599);
					
					if (is_array($date_array) and count($date_array >= 1))
					{
						$counter = 0;
						foreach ($date_array as $key => $value)
						{
							if (!in_array($value, $day_date_array[$j]))
							{
								if ($content_array[$i][$j][content])
								{
									if ($this->calendar_array[$value][link])
									{
										$content_array[$i][$j][content] .= "<div class='CalendarWeekEntry'><a href='index.php?".$this->calendar_array[$value][link]."'>".$this->calendar_array[$value][name]."</a></div>";
									}
									else
									{
										$content_array[$i][$j][content] .= "<div class='CalendarWeekEntry'>".$this->calendar_array[$value][name]."</div>";
									}
								}
								else
								{
									if ($this->calendar_array[$value][link])
									{
										$content_array[$i][$j][content] = "<div class='CalendarWeekEntry'><a href='index.php?".$this->calendar_array[$value][link]."'>".$this->calendar_array[$value][name]."</a></div>";
									}
									else
									{
										$content_array[$i][$j][content] = "<div class='CalendarWeekEntry'>".$this->calendar_array[$value][name]."</div>";
									}
								}
								array_push($day_date_array[$j],$value);
							}
							else
							{
								$content_array[$i][$j][content] = "";
							}
							
							$content_array[$i][$j][$counter][value] = $this->calendar_array[$value][color];
								
							$counter++;	
						}
					}
					else
					{
						$content_array[$i][$j][content] = "";
					}
					$datetime_handler->add_day(1);	
				}
				$datetime_handler->add_hour(1);
			}
			$color_count++;
		}
		
		$template = new HTMLTemplate("organiser/views/work_week.html");
		
		$paramquery_prev = $_GET;
		$paramquery_prev[page] = date("W-Y", $prev_week_datetime_handler->get_mktime());
		$params_prev = http_build_query($paramquery_prev,'','&#38;');
		
		$paramquery_next = $_GET;
		$paramquery_next[page] = date("W-Y", $next_week_datetime_handler->get_mktime());
		$params_next = http_build_query($paramquery_next,'','&#38;');
		
		$template->set_var("previous_params", $params_prev);
		$template->set_var("next_params", $params_next);
		
		$template->set_var("week", $week);
		$template->set_var("year", $year);
		$template->set_var("content_array", $content_array);
		
		return $template->get_string();
	}
	
	private function day_view()
	{
		$today = date("j-m-Y");
		$today_datetime_handler = new DatetimeHandler($today);
		
		if (!$_GET[page])
		{
			$date = date("j-m-Y");
		}
		else
		{
			$date = $_GET[page];
		}
		
		$datetime_handler = new DatetimeHandler($date);
		$datetime_handler->add_hour(6);
		
		$next_day_datetime_handler = clone $datetime_handler;
		$next_day_datetime_handler->add_day(1);
		
		$prev_day_datetime_handler = clone $datetime_handler;
		$prev_day_datetime_handler->sub_day(1);
		
		$mktime = $datetime_handler->get_mktime();
		
		$day = date("jS", $mktime);
		$month = date("m", $mktime);
		$month_name = date("F", $mktime);
		
		$year = date("Y", $mktime);

		$color_count = 1;
		
		$date_array = $this->mktime_has_date($mktime, $mktime+86399);
		$rows = count($date_array);
		$column_array = array();
		$column_array_count = 0;
		
		if ($datetime_handler->get_formatted_string("Y-m-d") == $today_datetime_handler->get_formatted_string("Y-m-d"))
		{
			$display_date = "<span class='CalendarToday'>".$datetime_handler->get_formatted_string("l jS F Y")."</span>";
		}
		else
		{
			$display_date = $datetime_handler->get_formatted_string("l jS F Y");
		}
		
		for ($i=0;$i<=17;$i++)
		{
			$mktime = $datetime_handler->get_mktime();
		
			$date_array = $this->mktime_has_date($mktime, $mktime+3599);
		
			if (is_array($date_array) and count($date_array >= 1))
			{
				foreach ($date_array as $key => $value)
				{
					if (!in_array($value, $column_array))
					{
						if ($this->calendar_array[$value][link])
						{
							$content_array[$i][$column_array_count][name] = "<a href='index.php?".$this->calendar_array[$value][link]."'>".$this->calendar_array[$value][name]."</a>";
						}
						else
						{
							$content_array[$i][$column_array_count][name] = $this->calendar_array[$value][name];
						}
						$content_array[$i][$column_array_count][value] = $this->calendar_array[$value][color];
						$content_array[$i][$column_array_count][datebegin] = true;
						
						$start_datetime_handler = new DatetimeHandler($this->calendar_array[$value][start_date]." ".$this->calendar_array[$value][start_time]);
						$end_datetime_handler = new DatetimeHandler($this->calendar_array[$value][end_date]." ".$this->calendar_array[$value][end_time]);
						
						$content_array[$i][$column_array_count][range] = $start_datetime_handler->get_formatted_string("j-n-Y (H:i)")." - ".$end_datetime_handler->get_formatted_string("j-n-Y (H:i)");
						
						$column_array_count++;
						array_push($column_array, $value);
					}
				}
				for ($j=0; $j<=($rows-1); $j++)
				{
					if (!$content_array[$i][$j][content])
					{
						if (isset($column_array[$j]) and in_array($column_array[$j], $date_array))
						{
							$content_array[$i][$j][value] = $this->calendar_array[$column_array[$j]][color];
							if ($content_array[$i][$j][datebegin] != true)
							{
								$content_array[$i][$j][datebegin] = false;
							}
						}
						else
						{
							$content_array[$i][$j][value] = "";
							$content_array[$i][$j][datebegin] = false;
						}
					}
				}
			}
			else
			{
				$content_array[$i][0][datebegin] = false;
				$content_array[$i][0][value] = "";
			}
			
			$content_array[$i][time] = $datetime_handler->get_formatted_string("H:i");
			
			$datetime_handler->add_hour(1);
			
			if ($color_count % 2)
			{
				$tr_class = " class='CalendarTrGrey'";
			}
			else
			{
				$tr_class = "";
			}
			
			$content_array[$i][tr_class] = $tr_class;
					
			$color_count++;
		}
		
		$template = new HTMLTemplate("organiser/views/day.html");
		
		$paramquery_prev = $_GET;
		$paramquery_prev[page] = date("j-m-Y", $prev_day_datetime_handler->get_mktime());
		$params_prev = http_build_query($paramquery_prev,'','&#38;');
		
		$paramquery_next = $_GET;
		$paramquery_next[page] = date("j-m-Y", $next_day_datetime_handler->get_mktime());
		$params_next = http_build_query($paramquery_next,'','&#38;');
			
		$template->set_var("previous_params", $params_prev);
		$template->set_var("next_params", $params_next);
		
		$template->set_var("date", $display_date);
		$template->set_var("content_array", $content_array);
		
		return $template->get_string();	
	}
	
	private function todo_view()
	{
		// Later
	}
	
	/**
	 * @param array $calendar_array
	 */
	public function set_calendar_array($calendar_array)
	{
		if (is_array($calendar_array))
		{
			$this->calendar_array = $calendar_array;
			return true;
		}
		else
		{
			return false;
		}
		/*
		 * [name]
		 * [startdate]
		 * [starttime]
		 * [enddate]
		 * [endtime]
		 * [name]
		 * [color]
		 * [link]
		 * [id]
		 * [serial]
		 * [serialid]
		 */
	}
	
	/**
	 * @param array $todo_array
	 */
	public function set_todo_array($todo_array)
	{
		if (is_array($todo_array))
		{
			$this->todo_array = $todo_array;
			return true;
		}
		else
		{
			return false;
		}
		/*
		 * [symbol]
		 * [name]
		 * [enddate]
		 * [endtime]
		 * [done]
		 * [link]
		 * [id]
		 * [user_id]
		 */
	}
	
	public function get_content()
	{
		$template = new HTMLTemplate("organiser/abstract_header.html");
		
		if ($this->year == true)
		{
			$template->set_var("year", true);
		}
		else
		{
			$template->set_var("year", false);
		}
		
		if ($this->month == true)
		{
			$template->set_var("month", true);
		}
		else
		{
			$template->set_var("month", false);
		}
		
		if ($this->week == true)
		{
			$template->set_var("week", true);
		}
		else
		{
			$template->set_var("week", false);
		}
		
		if ($this->work_week == true)
		{
			$template->set_var("workweek", true);
		}
		else
		{
			$template->set_var("workweek", false);
		}
		
		if ($this->day == true)
		{
			$template->set_var("day", true);
		}
		else
		{
			$template->set_var("day", false);
		}
		
		if ($this->todo == true)
		{
			$template->set_var("todo", true);
		}
		else
		{
			$template->set_var("todo", false);
		}
		
		$paramquery = $_GET;
		unset($paramquery[page]);
		unset($paramquery[view]);
		$params = http_build_query($paramquery, '', '&#38;');

		$template->set_var("params", $params);
			
		$return = $template->get_string();
		
		switch($_GET[view]):
		
			case "year":
				$return .= $this->year_view();
			break;
			
			case "week":
				$return .= $this->week_view();
			break;
			
			case "workweek":
				$return .= $this->work_week_view();
			break;
			
			case "day":
				$return .= $this->day_view();
			break;
			
			case "todo":
				$return .= $this->todo_view();
			break;
			
			case "month":
			default:
				$return .= $this->month_view();
			break;

		endswitch;

		return $return;
	}
	
}

?>
