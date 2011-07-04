<?php
/**
 * @package base
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
 * @package base
 * @deprecated use List_IO as Table/List Handler Class
 */
class TableIO {

	private $rows;
	private $content_array;
	private $css_class;
	private $last_line_text;
	
	private $top_right_text;
	
	private $finalised;

    function __construct($class)
    {
    	$this->rows = array();
    	$this->css_class = $class;
    	$this->finalised = false;
    }
    
    function __destruct()
    {
    	unset($this->rows);
    	unset($this->css_class);
    	unset($this->finalised);
    }
    
    public function add_row($title, $array_name, $sortable, $width)
    {
    	if ($this->finalise == false)
    	{
	    	if ($array_name)
	    	{
	    		$row_array = array();
	    		$row_array[title] = $title;
	    		$row_array[array_name] = $array_name;
	    		
	    		if ($width != null and is_numeric($width))
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
    
    public function add_content_array($array)
    {
    	if (is_array($array))
    	{
    		$this->finalised = true;
    		$this->content_array = $array;
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    public function override_last_line($text)
    {
    	if ($text)
    	{
    		$this->last_line_text = $text;	
    	}
    }
    
    public function set_bottom_right_text($bottom_right_text)
    {
    	if ($bottom_right_text)
    	{
    		$this->bottom_right_text = $bottom_right_text;	
    	}
    }
    
    public function get_content($page)
    {
    	if (!$page)
    	{
			$page = 1;
		}
    	
    	if (is_array($this->rows))
    	{
    		$entry_count = count($this->content_array);

			$number_of_pages = ceil($entry_count/20);
    		
			$return = "<div class='OverviewTableLeft'>".Common_IO::results_on_page($entry_count, $number_of_pages)."</div>" .
						"<div class='OverviewTableRight'>".$this->bottom_right_text."</div>";
    		
    			
    		$return .= "<table class='".$this->css_class."'><tr>";	
    			
			foreach ($this->rows as $key => $value)
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
    		
    		$return .= "</tr>";

    		if ($number_of_pages <= $page)
    		{
				$max_for = $entry_count-(($number_of_pages-1)*20)-1;
				$page = $number_of_pages;
			}
			else
			{
				$max_for = 20-1; 
			}
			
			if (is_array($this->content_array))
			{
				$color_count = 0;
			
				for ($i=0;$i<=$max_for;$i++)
				{
	    			if ($color_count % 2)
	    			{
						$tr_class = " class ='trLightGrey'";
					}
					else
					{
						$tr_class = "";
					}
	    			
	    			$return .= "<tr".$tr_class.">";
	    			
					$entry = ($page*20)+$i-20; // Erzeugt Entry-ID
									
					$content = $this->content_array[$entry];
					
					foreach ($this->rows as $key => $value)
					{
					
						if (is_array($content[$value[array_name]]))
						{
							if ($content[$value[array_name]][link] and $content[$value[array_name]][content])
							{
								$return .= "<td><a href='index.php?".$content[$value[array_name]][link]."'>".$content[$value[array_name]][content]."</a></td>";
							}
							elseif(!$content[$value[array_name]][link] and $content[$value[array_name]][content])
							{
								$return .= "<td>".$content[$value[array_name]][content]."</td>";
							}
							else
							{
								$return .= "<td></td>";
							}							
						}
						else
						{
							$return .= "<td>".$content[$value[array_name]]."</td>";
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
				$return .= Common_IO::page_bar($page, $number_of_pages, $_GET);
			}
			
			return $return;		
    	}
    }

	public function get_table($page, $number_of_results)
    {
    	if (!$page)
    	{
			$page = 1;
		}
    	
    	if (is_array($this->rows))
    	{
			$number_of_pages = ceil($number_of_results/20);
    		
			$return = "<div class='OverviewTableLeft'>".Common_IO::results_on_page($number_of_results, $number_of_pages)."</div>" .
						"<div class='OverviewTableRight'>".$this->bottom_right_text."</div>";
    		
    			
    		$return .= "<table class='".$this->css_class."'><tr>";	
    			
			foreach ($this->rows as $key => $value)
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
    		
    		$return .= "</tr>";

    		if ($number_of_pages <= $page)
    		{
				$max_for = count($this->content_array) - 1;
				$page = $number_of_pages;
			}
			else
			{
				$max_for = count($this->content_array) - 1; 
			}
			
			if (is_array($this->content_array))
			{
				$color_count = 0;
				for ($i=0;$i<=$max_for;$i++)
				{
	    			if ($color_count % 2)
	    			{
						$tr_class = " class ='trLightGrey'";
					}
					else
					{
						$tr_class = "";
					}
	    			
	    			$return .= "<tr".$tr_class.">";
									
					$content = $this->content_array[$i];
					
					foreach ($this->rows as $key => $value)
					{
						if (is_array($content[$value[array_name]]))
						{
							if ($content[$value[array_name]][link] and $content[$value[array_name]][content])
							{
								$return .= "<td><a href='index.php?".$content[$value[array_name]][link]."'>".$content[$value[array_name]][content]."</a></td>";
							}
							elseif(!$content[$value[array_name]][link] and $content[$value[array_name]][content])
							{
								$return .= "<td>".$content[$value[array_name]][content]."</td>";
							}
							else
							{
								$return .= "<td></td>";
							}							
						}
						else
						{
							$return .= "<td>".$content[$value[array_name]]."</td>";
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
				$return .= Common_IO::page_bar($page, $number_of_pages, $_GET);
			}
			
			return $return;		
    	}
    }    
    
}
?>