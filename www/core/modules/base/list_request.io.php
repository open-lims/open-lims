<?php
/**
 * @package base
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
 * List Request IO Class
 * @package base
 */
class ListRequest_IO
{
	private $entries;
	private $css_page_id;
	private $css_row_sort_id;
	private $entries_per_page;
	
	private $display_header;
	private $display_footer;
	
	private $array;
	private $rows;
	
    function __construct($entries, $css_page_id, $css_row_sort_id, $entries_per_page = 20, $global_class_id = null, $display_header = true, $display_footer = true)
    {
    	if (is_numeric($entries) and $css_page_id)
    	{
    		$this->entries = $entries;
    		$this->css_page_id = $css_page_id;
    		$this->css_row_sort_id = $css_row_sort_id;
    		$this->entries_per_page = $entries_per_page;
    		$this->display_header = $display_header;
    		$this->display_footer = $display_footer;
    	}
    }

    public function empty_message($message)
    {
    	
    }
    
   	public function get_page($page)
   	{
   		if (!$page)
    	{
			$page = 1;
		}
    	
    	if (is_array($this->rows))
    	{
    		if (count($this->array) >= 1)
    		{
				$number_of_pages = ceil($this->entries/$this->entries_per_page);
    		}
    		else
    		{
    			$number_of_pages = 1;
    		}
    		
			// !!! CSS !!!
			if ($this->display_header == true)
			{
				$return = "<div class='OverviewTableLeft'>".Common_IO::results_on_page($this->entries, $number_of_pages)."</div>" .
							"<div class='OverviewTableRight'>".$this->top_right_text."</div>" .
							"<div class='OverviewTableClear'>&nbsp;</div>";
			}
    		
    			
    		$return .= "<table class='OverviewTable'><tr>";	
    			
			foreach ($this->rows as $key => $value)
			{
				if ($value[3] == true)
				{
					$paramquery = $_GET;
					unset($paramquery[sortvalue]);
					unset($paramquery[sortmethod]);
					$params = http_build_query($paramquery, '', '&#38;');
					
					if ($value[2] != null)
					{
						if ($_GET[sortvalue] == $value[1])
						{
							if (!$_GET[sortmethod] or $_GET[sortmethod] == "asc")
							{
								$return .= "<th width='".$value[2]."' class='".$this->css_row_sort_id."' id='".$this->css_row_sort_id."".$value[1]."'>" .
												"<a href='#'>".$value[0]."</a>" .
												"&nbsp;<a href='#'>" .
														"<img src='images/downside.png' alt='' border='0' />" .
												"</a>" .
												"</th>";
							}
							else
							{
								$return .= "<th width='".$value[2]."' class='".$this->css_row_sort_id."' id='".$this->css_row_sort_id."".$value[1]."'>" .
												"<a href='#'>".$value[0]."</a>" .
												"&nbsp;<a href='#'>" .
														"<img src='images/upside.png' alt='' border='0' />" .
												"</a>" .
												"</th>";
							}
						}
						else
						{
							$return .= "<th width='".$value[2]."' class='".$this->css_row_sort_id."' id='".$this->css_row_sort_id."".$value[1]."'>" .
											"<a href='#'>".$value[0]."</a>" .
											"&nbsp;<a href='#'>" .
													"<img src='images/nosort.png' alt='' border='0' />" .
											"</a>" .
											"</th>";
						}
					}
					else
					{
						if ($_GET[sortvalue] == $value[1])
						{
							if (!$_GET[sortmethod] or $_GET[sortmethod] == "asc")
							{
								$return .= "<th class='".$this->css_row_sort_id."' id='".$this->css_row_sort_id."".$value[1]."'>" .
												"<a href='#'>".$value[0]."</a>" .
												"&nbsp;<a href='#'>" .
														"<img src='images/downside.png' alt='' border='0' />" .
												"</a>" .
												"</th>";
							}
							else
							{
								$return .= "<th class='".$this->css_row_sort_id."' id='".$this->css_row_sort_id."".$value[1]."'>" .
												"<a href='#'>".$value[0]."</a>" .
												"&nbsp;<a href='#'>" .
														"<img src='images/upside.png' alt='' border='0' />" .
												"</a>" .
												"</th>";
							}
						}
						else
						{
							$return .= "<th class='".$this->css_row_sort_id."' id='".$this->css_row_sort_id."".$value[1]."'>" .
											"<a href='#'>".$value[0]."</a>" .
											"&nbsp;<a href='#'>" .
													"<img src='images/nosort.png' alt='' border='0' />" .
											"</a>" .
											"</th>";
						}
					}
				}
				else
				{
					if ($value[2] != null)
					{
						$return .= "<th width='".$value[2]."'>".$value[0]."</th>";
					}
					else
					{
						$return .= "<th>".$value[0]."</th>";
					}
				}	
			}
    		
    		$return .= "</tr>";

    		if (is_array($this->first_line_entry))
    		{
    			$return .= "<tr class ='trLightGrey'>";
    			
    			foreach ($this->rows as $key => $value)
				{
					if (is_array($this->first_line_entry[$value[1]]))
					{
						
						if ($this->first_line_entry[$value[1]][link] and $this->first_line_entry[$value[1]][content])
						{
							$return .= "<td><a href='index.php?".$this->first_line_entry[$value[1]][link]."'>".$this->first_line_entry[$value[1]][content]."</a></td>";
						}
						elseif(!$this->first_line_entry[$value[1]][link] and $this->first_line_entry[$value[1]][content])
						{
							$return .= "<td>".$this->first_line_entry[$value[1]][content]."</td>";
						}else
						{
							$return .= "<td></td>";
						}
													
					}
					else
					{
						$return .= "<td>".$this->first_line_entry[$value[1]]."</td>";
						
					}
				}
				
				$return .= "</tr>";
    		}
    		
			if (is_array($this->array))
			{
			
				$color_count = 0;
			
				foreach ($this->array as $key => $value)
				{
	    		
	    			if ($color_count % 2) {
						$tr_class = " class ='trLightGrey'";
					}else{
						$tr_class = "";
					}
	    			
	    			$return .= "<tr".$tr_class.">";
									
					$content = $value;
					
					foreach ($this->rows as $row_key => $row_value)
					{
						$return .= "<td>";
						
						if ($row_value[4])
						{
							if ($this->array[$key]['id'])
							{
								$return .= "<div class='".$row_value[4]."' id='".$row_value[4]."".$this->array[$key]['id']."'>";
							}
							else
							{
								$return .= "<div class='".$row_value[4]."'>";
							}
						}
						
						// !! LABEL !!
						if (is_array($content[$row_value[1]]))
						{
							if ($content[$row_value[1]][label])
							{
								$return .= "<span title='".$content[$row_value[1]][label]."'>";
							}
							
							if ($content[$row_value[1]][link] and $content[$row_value[1]][content])
							{
								$return .= "<a href='index.php?".$content[$row_value[1]][link]."'>".$content[$row_value[1]][content]."</a>";
							}
							elseif(!$content[$row_value[1]][link] and $content[$row_value[1]][content])
							{
								$return .= $content[$row_value[1]][content];
							}

							if ($content[$row_value[1]][label])
							{
								$return .= "</span>";
							}
						}
						else
						{
							$return .= $content[$row_value[1]];
						}
						
						if ($row_value[4])
						{
							$return .= "</div>";
						}
						
						$return .= "</td>";
						
					}
									
					$return .= "<tr>";
					
					$color_count++;	
				}
			}
			

			if ($this->last_line_text)
			{
				$return .= "<tr><td colspan='".count($this->rows)."'>".$this->last_line_text."</td></tr>";
			}

			$return .= "</table>";
			
			if ($this->display_footer == true)
			{
				$return .= "<div class='ResultNextPageBar'>";
		
				$return .= "<table style='display: inline;'><tr><td><span class='smallTextBlack'>Page ".$page." of ".$number_of_pages."</span></td>";
		
				// Previous
				if ($page == 1)
				{
					$return .= "<td><img src='images/icons/previous_d.png' alt='Previous' border='0' /></td>";		
				}
				else
				{
					
					$previous_page = $page - 1;
					
					if ($this->css_page_id)
					{
						$return .= "<td><a href='#' class='".$this->css_page_id."' id='".$this->css_page_id."".$previous_page."'><img src='images/icons/previous.png' alt='Previous' border='0' /></a></td>";
					}
				}	
				
				$displayed = false;
							
				for ($i=1;$i<=$number_of_pages;$i++)
				{
					$display = false;
					
					if ($max_page < 5)
					{
						$display = true;
					}
					else
					{
	
						if ($i <= 2) {
							$display = true;
						}
						
						if ($i > $max_page-2) {
							$display = true;
						}
						
						if ($display == false and $page+1 == $i) {
							$display = true;
						}
						
						if ($display == false and $page-1 == $i) {
							$display = true;
						}
						
						if ($display == false and $page == $i) {
							$display = true;
						}
						if ($i == $page+10 and $display == false) {
							$display = true;
						}
						
						if ($i == $page-10 and $display == false) {
							$display = true;
						}
	
					}
					
					if ($display == true)
					{
						if ($page == $i)
						{
							if ($this->css_page_id)
							{
								$return .= "<td><span class='bold'><a href='#' class='".$this->css_page_id."' id='".$this->css_page_id."".$i."'>".$i."</a></span></td>";
							}
						}
						else
						{
							if ($this->css_page_id)
							{
								$return .= "<td><a href='#' class='".$this->css_page_id."' id='".$this->css_page_id."".$i."'>".$i."</a></td>";	
							}
						}						
						$displayed = true;
					}
					elseif ($displayed == true)
					{
						$return .= "<td>..</td>";
					}
					
					if ($display == false)
					{
						$displayed = false;
					}
				}
	
				// Next
				if($page == $number_of_pages)
				{
					$return .= "<td><img src='images/icons/next_d.png' alt='Next' border='0' /></td>";		
				}
				else
				{
					$next_page = $page + 1;
					
					if ($this->css_page_id)
					{
						$return .= "<td><a href='#' class='".$this->css_page_id."' id='".$this->css_page_id."".$next_page."'><img src='images/icons/next.png' alt='Previous' border='0' /></a></td>";
					}
				}
				
				$return .= "</tr></table>";
				
				$return .= "</div>";
			}			
			return $return;	
    			
    	}
   	}
   	
   	public function set_row_array($json_row_array)
   	{
   		$this->rows = json_decode($json_row_array);
   		
   		// print_r($row_array);
   	}
   	
   	public function set_array($array)
   	{
   		$this->array = $array;
   	}
}