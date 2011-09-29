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
 * List IO Class
 * Handles lists
 * Use this instead of Table_IO
 * @package base
 */
class ListStat_IO
{
	private $rows;
	private $entries;
	private $entries_per_page;
	
	private $js_page_id;

	private $last_line_text;
	private $first_line_entry;
	private $top_right_text;
	
	private $finalised;

	/**
	 * @param integer $entries
	 * @param integer $entries_per_page
	 * @param string $js_page_id
	 */
    function __construct($entries, $entries_per_page, $js_page_id = null)
    {
    	$this->entries = $entries;
    	$this->entries_per_page = $entries_per_page;
    	
    	$this->js_page_id = $js_page_id;
    	
    	$this->rows = array();
    	$this->finalised = false;
    	$this->last_line_text = null;
    }
    
    function __destruct()
    {
    	unset($this->rows);
    	unset($this->css_entries);
    }
    
    /**
     * @param string $title
     * @param string $address
     * @param bool $sortable
     * @param integer $width
     * @return bool
     */
    public function add_row($title, $address, $sortable, $width, $js_sort_id = null)
    {
    	if ($this->finalised == false)
    	{
	    	if ($address)
	    	{
	    		$row_array = array();
	    		$row_array[title] = $title;
	    		$row_array[address] = $address;
	    		
	    		if ($width != null)
	    		{
	    			$row_array[width] = $width;
	    		}
	    		else
	    		{
	    			$row_array[width] = null;
	    		}
	    			    		
	    		if ($sortable == true)
	    		{
	    			$row_array[sortable] = true;
	    		}
	    		else
	    		{
	    			$row_array[sortable] = false;
	    		}
	    		
	    		if ($js_sort_id != null)
	    		{
	    			$row_array[js_sort_id] = $js_sort_id;
	    		}
	    		else
	    		{
	    			$row_array[js_sort_id] = null;
	    		}
	    		
	    		array_push($this->rows,$row_array);
	    	}
	    	else
	    	{
	    		return false;
	    	}
    	}
    	else
    	{
    		return false;
    	}
    }

    /**
     * @param string $text
     */
	public function override_last_line($text)
	{
    	if ($text)
    	{
    		$this->last_line_text = $text;	
    	}
    }

    /**
     * Adds a first line, which is everytime visible and it will be not sorted
     * (e.g. for [parent folder] in Data Browser
     * @param array $array
     * @return bool
     */
    public function add_first_line($array)
    {
    	if (is_array($array))
    	{
    		$this->first_line_entry = $array;
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    public function set_top_right_text($top_right_text)
    {
    	if ($top_right_text)
    	{
    		$this->top_right_text = $top_right_text;	
    	}
    }
    
    /**
     * @param array $array
     * @param integer $page
     * @return string
     */
	public function get_list($array, $page)
    {
    	if (!$page)
    	{
			$page = 1;
		}
    	
    	if (is_array($this->rows))
    	{
			$number_of_pages = ceil($this->entries/$this->entries_per_page);
    		
			$return = "<div class='OverviewTableLeft'>".Common_IO::results_on_page($this->entries, $number_of_pages)."</div>" .
						"<div class='OverviewTableRight'>".$this->top_right_text."</div>" .
						"<div class='OverviewTableClear'>&nbsp;</div>";
    		
    			
    		$return .= "<table class='OverviewTable'><tr>";	
    			
			foreach ($this->rows as $key => $value)
			{
				if ($value[sortable] == true)
				{
					$paramquery = $_GET;
					unset($paramquery[sortvalue]);
					unset($paramquery[sortmethod]);
					$params = http_build_query($paramquery, '', '&#38;');
					
					if ($value[width] != null)
					{
						if ($_GET[sortvalue] == $value[address])
						{
							if (!$_GET[sortmethod] or $_GET[sortmethod] == "asc")
							{
								if ($value[js_sort_id])
								{
									$return .= "<th width='".$value[width]."' id='".$value[js_sort_id]."'>" .
													"<a href='#'>".$value[title]."</a>" .
													"&nbsp;<a href='#'>" .
															"<img src='images/downside.png' alt='' border='0' />" .
													"</a>" .
													"</th>";
								}
								else
								{
									$return .= "<th width='".$value[width]."'>" .
													"<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=desc'>".$value[title]."</a>" .
													"&nbsp;<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=desc'>" .
															"<img src='images/downside.png' alt='' border='0' />" .
													"</a>" .
													"</th>";
								}
							}
							else
							{
								if ($value[js_sort_id])
								{
									$return .= "<th width='".$value[width]."' id='".$value[js_sort_id]."'>" .
													"<a href='#'>".$value[title]."</a>" .
													"&nbsp;<a href='#'>" .
															"<img src='images/upside.png' alt='' border='0' />" .
													"</a>" .
													"</th>";
								}
								else
								{
									$return .= "<th width='".$value[width]."'>" .
													"<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=asc'>".$value[title]."</a>" .
													"&nbsp;<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=asc'>" .
															"<img src='images/upside.png' alt='' border='0' />" .
													"</a>" .
													"</th>";
								}
							}
						}
						else
						{
							if ($value[js_sort_id])
							{
								$return .= "<th width='".$value[width]."' id='".$value[js_sort_id]."'>" .
												"<a href='#'>".$value[title]."</a>" .
												"&nbsp;<a href='#'>" .
														"<img src='images/nosort.png' alt='' border='0' />" .
												"</a>" .
												"</th>";
							}
							else
							{
								$return .= "<th width='".$value[width]."'>" .
												"<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=asc'>".$value[title]."</a>" .
												"&nbsp;<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=asc'>" .
														"<img src='images/nosort.png' alt='' border='0' />" .
												"</a>" .
												"</th>";
							}
						}
					}
					else
					{
						if ($_GET[sortvalue] == $value[address])
						{
							if (!$_GET[sortmethod] or $_GET[sortmethod] == "asc")
							{
								if ($value[js_sort_id])
								{
									$return .= "<th id='".$value[js_sort_id]."'>" .
													"<a href='#'>".$value[title]."</a>" .
													"&nbsp;<a href='#'>" .
															"<img src='images/downside.png' alt='' border='0' />" .
													"</a>" .
													"</th>";
								}
								else
								{
									$return .= "<th>" .
													"<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=desc'>".$value[title]."</a>" .
													"&nbsp;<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=desc'>" .
															"<img src='images/downside.png' alt='' border='0' />" .
													"</a>" .
													"</th>";
								}
							}
							else
							{
								if ($value[js_sort_id])
								{
									$return .= "<th id='".$value[js_sort_id]."'>" .
													"<a href='#'>".$value[title]."</a>" .
													"&nbsp;<a href='#'>" .
															"<img src='images/upside.png' alt='' border='0' />" .
													"</a>" .
													"</th>";
								}
								else
								{
									$return .= "<th>" .
													"<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=asc'>".$value[title]."</a>" .
													"&nbsp;<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=asc'>" .
															"<img src='images/upside.png' alt='' border='0' />" .
													"</a>" .
													"</th>";
								}
							}
						}
						else
						{
							if ($value[js_sort_id])
							{
								$return .= "<th id='".$value[js_sort_id]."'>" .
												"<a href='#'>".$value[title]."</a>" .
												"&nbsp;<a href='#'>" .
														"<img src='images/nosort.png' alt='' border='0' />" .
												"</a>" .
												"</th>";
							}
							else
							{
								$return .= "<th>" .
												"<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=asc'>".$value[title]."</a>" .
												"&nbsp;<a href='index.php?".$params."&#38;sortvalue=".$value[address]."&#38;sortmethod=asc'>" .
														"<img src='images/nosort.png' alt='' border='0' />" .
												"</a>" .
												"</th>";
							}
						}
					}
				}
				else
				{
					if ($value[width] != null)
					{
						$return .= "<th width='".$value[width]."'>".$value[title]."</th>";
					}
					else
					{
						$return .= "<th>".$value[title]."</th>";
					}
				}	
			}
    		
    		$return .= "</tr>";

    		if (is_array($this->first_line_entry))
    		{
    			$return .= "<tr class ='trLightGrey'>";
    			
    			foreach ($this->rows as $key => $value)
				{
					if (is_array($this->first_line_entry[$value[address]]))
					{
						
						if ($this->first_line_entry[$value[address]][link] and $this->first_line_entry[$value[address]][content])
						{
							$return .= "<td><a href='index.php?".$this->first_line_entry[$value[address]][link]."'>".$this->first_line_entry[$value[address]][content]."</a></td>";
						}
						elseif(!$this->first_line_entry[$value[address]][link] and $this->first_line_entry[$value[address]][content])
						{
							$return .= "<td>".$this->first_line_entry[$value[address]][content]."</td>";
						}else
						{
							$return .= "<td></td>";
						}
													
					}
					else
					{
						$return .= "<td>".$this->first_line_entry[$value[address]]."</td>";
						
					}
				}
				
				$return .= "</tr>";
    		}
    		
			if (is_array($array))
			{
			
				$color_count = 0;
			
				foreach ($array as $key => $value)
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
					
						if (is_array($content[$row_value[address]]))
						{
							
							if ($content[$row_value[address]][link] and $content[$row_value[address]][content])
							{
								$return .= "<td><a href='index.php?".$content[$row_value[address]][link]."'>".$content[$row_value[address]][content]."</a></td>";
							}
							elseif(!$content[$row_value[address]][link] and $content[$row_value[address]][content])
							{
								$return .= "<td>".$content[$row_value[address]][content]."</td>";
							}else
							{
								$return .= "<td></td>";
							}
														
						}
						else
						{
							$return .= "<td>".$content[$row_value[address]]."</td>";
							
						}
						
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
			
			if ($number_of_pages > 1)
			{
				//$return .= Common_IO::page_bar($page, $number_of_pages, $_GET);

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
					
					if ($this->js_page_id)
					{
						$return .= "<td><a href='#' class='".$this->js_page_id."' id='".$this->js_page_id."".$previous_page."'><img src='images/icons/previous.png' alt='Previous' border='0' /></a></td>";
					}
					else
					{
						$previous_paramquery = $_GET;
						$previous_paramquery[page] = $previous_page;
						unset($previous_paramquery[show]);
						$previous_link = http_build_query($previous_paramquery,'','&#38;');
						
						$return .= "<td><a href='index.php?".$previous_link."'><img src='images/icons/previous.png' alt='Previous' border='0' /></a></td>";
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
							if ($this->js_page_id)
							{
								$return .= "<td><span class='bold'><a href='#' class='".$this->js_page_id."' id='".$this->js_page_id."".$i."'>".$i."</a></span></td>";
							}
							else
							{
								$page_paramquery = $_GET;
								$page_paramquery[page] = $i;
								$page_link = http_build_query($page_paramquery,'','&#38;');
								
								$return .= "<td><span class='bold'><a href='index.php?".$page_link."'>".$i."</a></span></td>";
							}
						}
						else
						{
							if ($this->js_page_id)
							{
								$return .= "<td><a href='#' class='".$this->js_page_id."' id='".$this->js_page_id."".$i."'>".$i."</a></td>";	
							}
							else
							{
								$page_paramquery = $_GET;
								$page_paramquery[page] = $i;
								$page_link = http_build_query($page_paramquery,'','&#38;');
								
								$return .= "<td><a href='index.php?".$page_link."'>".$i."</a></td>";	
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
					
					if ($this->js_page_id)
					{
						$return .= "<td><a href='#' class='".$this->js_page_id."' id='".$this->js_page_id."".$next_page."'><img src='images/icons/next.png' alt='Previous' border='0' /></a></td>";
					}
					else
					{
						$next_paramquery = $_GET;
						$next_paramquery[page] = $next_page;
						unset($next_paramquery[show]);
						$next_link = http_build_query($next_paramquery,'','&#38;');
						
						$return .= "<td><a href='index.php?".$next_link."'><img src='images/icons/next.png' alt='Next' border='0' /></a></td>";
					}
				}
				
				$return .= "</tr></table>";
				
				$return .= "</div>";
			}
			
			return $return;	
    			
    	}
    	
    }    
    
}
?>