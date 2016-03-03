<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
	private $empty_message;
	
	private $first_line_entry_class;
	private $first_line_entry;
	
	private $array;
	private $rows;

    public function empty_message($message)
    {
    	$this->empty_message = $message;
    }
    
    public function add_first_line($array, $class = null)
    {
   		if (is_array($array))
    	{
    		$this->first_line_entry_class = $class;
    		$this->first_line_entry = $array;
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
   	public function get_page($page)
   	{
   		if (!$page)
    	{
			$page = 1;
		}
    	
    	if (is_array($this->rows))
    	{
    		if (is_array($this->first_line_entry))
    		{
    			if ($this->first_line_entry_class)
    			{
    				$return .= "<tr class ='ListTableRowEven ".$this->first_line_entry_class."'>";
    			}
    			else
    			{
    				$return .= "<tr class ='ListTableRowEven'>";
    			}
    			
    			
    			foreach ($this->rows as $key => $value)
				{
					if (is_array($this->first_line_entry[$value[1]]))
					{
						
						if ($this->first_line_entry[$value[1]]['link'] and $this->first_line_entry[$value[1]]['content'])
						{
							if (strpos($this->first_line_entry[$value[1]]['link'], "?") === false)
							{
								$return .= "<td><a href='index.php?".$this->first_line_entry[$value[1]]['link']."'>".$this->first_line_entry[$value[1]]['content']."</a></td>";
							}
							else
							{
								$return .= "<td><a href='".$this->first_line_entry[$value[1]]['link']."'>".$this->first_line_entry[$value[1]]['content']."</a></td>";
							}	
						}
						elseif(!$this->first_line_entry[$value[1]]['link'] and $this->first_line_entry[$value[1]]['content'])
						{
							$return .= "<td>".$this->first_line_entry[$value[1]]['content']."</td>";
						}else
						{
							$return .= "<td></td>";
						}
													
					}
					else
					{
						if($this->first_line_entry[$value[1]] == "")
						{
							$this->first_line_entry[$value[1]] = "&nbsp;"; //we all love IE
						}
						$return .= "<td>".$this->first_line_entry[$value[1]]."</td>";
					}
				}
				
				$return .= "</tr>";
    		}
    		
			if (is_array($this->array) and count($this->array) >= 1)
			{
			
				$color_count = 0;
			
				foreach ($this->array as $key => $value)
				{					
	    			if ($color_count % 2) {
						$tr_class = " class ='ListTableRowEven'";
					}else{
						$tr_class = " class ='ListTableRowOdd'";
					}
	    			
	    			$return .= "<tr".$tr_class.">";
									
					$content = $value;
					
					$row_count = count($this->rows);
					$current_row_count = 1;
					
					foreach ($this->rows as $row_key => $row_value)
					{
						if ($current_row_count == 1)
						{
							$return .= "<td class='ListTableColumnFirstEntry'>";
						}	
						elseif ($current_row_count == $row_count)
						{
							$return .= "<td class='ListTableColumnLastEntry'>";
						}	
						else
						{
							$return .= "<td>";
						}		
						
						$current_row_count++;
						
						$content_string = "";
						
						if (is_array($content[$row_value[1]]) and $content[$row_value[1]]['class'])
						{
							if ($row_value[4])
							{
								if ($this->array[$key]['id'])
								{
									$content_string .= "<div class='".$row_value[4]." ".$content[$row_value[1]]['class']."' id='".$row_value[4]."".$this->array[$key]['id']."'>";
								}
								else
								{
									$content_string .= "<div class='".$row_value[4]." ".$content[$row_value[1]]['class']."'>";
								}
							}
							else
							{
								$content_string .= "<div class='".$content[$row_value[1]]['class']."'>";
							}
						}
						else
						{
							if ($row_value[4])
							{
								if ($this->array[$key]['id'])
								{
									$content_string .= "<div class='".$row_value[4]."' id='".$row_value[4]."".$this->array[$key]['id']."'>";
								}
								else
								{
									$content_string .= "<div class='".$row_value[4]."'>";
								}
							}
						}
						
						
						// !! LABEL !!
						if (is_array($content[$row_value[1]]))
						{
							if ($content[$row_value[1]]['label'])
							{
								$content_string .= "<span title='".$content[$row_value[1]]['label']."'>";
							}
							
							if ($content[$row_value[1]]['link'] and $content[$row_value[1]]['content'])
							{
								if (strpos($content[$row_value[1]]['link'], "?") === false)
								{
									$content_string .= "<a href='index.php?".$content[$row_value[1]]['link']."'>".$content[$row_value[1]]['content']."</a>";
								}
								else
								{
									$content_string .= "<a href='".$content[$row_value[1]]['link']."'>".$content[$row_value[1]]['content']."</a>";
								}
							}
							elseif(!$content[$row_value[1]]['link'] and $content[$row_value[1]]['content'])
							{
								$content_string .= $content[$row_value[1]]['content'];
							}

							if ($content[$row_value[1]]['label'])
							{
								$content_string .= "</span>";
							}
						}
						else
						{
							$content_string .= $content[$row_value[1]];
						}
						
						if ($row_value[4])
						{
							$content_string .= "</div>";
						}
						
						if ($content_string == "")
						{
							$content_string = "&nbsp;";
							// I hate IE !
						}
						
						$return .= $content_string;
						$return .= "</td>";
						
					}
									
					$return .= "</tr>";
					
					$color_count++;	
				}
			}
			else
			{
				$return .= "<tr><td colspan='".count($this->rows)."'>".$this->empty_message."</td></tr>";
			}
			

			if ($this->last_line_text)
			{
				$return .= "<tr><td colspan='".count($this->rows)."'>".$this->last_line_text."</td></tr>";
			}
					
			return $return;	
    			
    	}
   	}
   	
   	public function set_column_array($json_column_array)
   	{
   		$this->rows = json_decode($json_column_array);
   	}
   	
   	public function set_array($array)
   	{
   		$this->array = $array;
   	}
}