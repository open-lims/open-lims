<?php
/**
 * @package workflow
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
 * 
 */
// require_once("interfaces/workflow.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{

}

/**
 * Workflow Class
 * @package workflow
 */
class Workflow // implements WorkflowInterface
{
	private static $printed_elements = array();
	private static $visited_elements = array();
	
	private $start_element;
	private $current_element;
	
	private $status_elements = array();
	private $active_status_elements = array();
	private $visited_status_elements = array();
	
	
	function __construct()
	{
		
	}
	
	function __sleep()
	{
		$active_element = array();
	}
	
	public function add_element($element, $set_as_current = true, $last_elements_array = null)
	{
		if ($element instanceof WorkflowElement)
		{
			if ($element instanceof WorkflowElementActivity)
			{
				$this->status_elements[$element->get_id()] = &$element;
			}
			
			if (!is_object($this->start_element))
			{
				$this->start_element = $element;
				$this->current_element = $element;
			}
			else
			{
				if (is_array($last_elements_array))
				{
					$this->current_element = $element;
					foreach ($last_elements_array as $key => $value)
					{
						$this->current_element->add_previous($value);
					}
				}
				else
				{
					$this->current_element->add_next($element);
					if ($set_as_current == true)
					{
						$this->current_element = $element;
					}
				}
			}
		}
	}
	
	public function get_current_element()
	{
		return $this->current_element;
	}
	
	public function get_start_element()
	{
		return $this->start_element;
	}
	
	public function get_status_element($status_id)
	{
		return $this->status_elements[$status_id];
	}
	
	public function get_all_status_elements()
	{
		return $this->status_elements;
	}
	
	public function get_all_active_elements()
	{
		return $this->active_status_elements;
	}
	
	public function set_current_element($element)
	{
		$this->current_element = $element;
	}
	
	public function set_status_active($status_id)
	{
		if ($this->status_elements[$status_id] instanceof WorkflowElementActivity)
		{
			$this->active_status_elements[$status_id] = &$this->status_elements[$status_id];
		}
	}
	
	public function set_status_visited($status_id)
	{
		if ($this->status_elements[$status_id] instanceof WorkflowElementActivity)
		{
			$this->visited_status_elements[$status_id] = &$this->status_elements[$status_id];
		}
	}
	
	public function is_visited($element)
	{
		if (in_array($element, $this->visited_status_elements))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function is_active($element)
	{
		if (in_array($element, $this->active_status_elements))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function is_finished()
	{
		
	}
	
	public function print_elements($element = null)
	{
		if ($element == null)
		{
			$element = $this->start_element;
		}

		$next_array = $element->get_next();
		$previous_array = $element->get_previous();
		
		if ($element instanceof WorkflowElementActivity)
		{
			echo "Status: ".$element->get_id()."; ";
		}
		
		if ($element instanceof WorkflowElementOr)
		{
			echo "Decision; ";
		}
		
		if ($element instanceof WorkflowElementAnd)
		{
			echo "Parallel; ";
		}
		
		array_push(self::$printed_elements, $element);
		
		echo "Children: ";
		
		foreach($next_array as $key => $value)
		{
			if ($value instanceof WorkflowElementActivity)
			{
				echo $value->get_id()." ";
			}
			
			if ($value instanceof WorkflowElementOr)
			{
				echo "Decision ";
			}
			
			if ($value instanceof WorkflowElementAnd)
			{
				echo "Parallel ";
			}
		}
		
		echo "; Parents: ";
		
		foreach($previous_array as $key => $value)
		{
			if ($value instanceof WorkflowElementActivity)
			{
				echo $value->get_id()." ";
			}
			
			if ($value instanceof WorkflowElementOr)
			{
				echo "Decision ";
			}
			
			if ($value instanceof WorkflowElementAnd)
			{
				echo "Parallel ";
			}
		}

		echo "<br />";
		echo "<br />";
			
		foreach($next_array as $key => $value)
		{	
			if (!in_array($value, self::$printed_elements))
			{
				$this->print_elements($value);
			}
		}
	}

	/**
	 * @param object $element
	 * @param array $active_elements
	 * @param integer $number_of_parent_children
	 * @param integer $child_id
	 * @return array
	 */
	public static function get_drawable_element_list($element, $active_elements = null, $number_of_parent_children = null, $child_id = null)
	{
		$next_array = &$element->get_next();
		$next_array_count = count($next_array);
				
		array_push(self::$visited_elements, $element);

		if($next_array_count <= 0)
		{
			if (in_array($element, $active_elements))
			{
				return array(array(array(&$element, true)));	// Override maximum
			}
			else
			{
				return array(array(array(&$element, false)));
			}
		}
		else
		{	
			$return_array = array();
			
			// Element Itself
			if (in_array($element, $active_elements))
			{
				$return_array[0][0] = array(&$element, true);
			}
			else
			{
				$return_array[0][0] = array(&$element, false);
			}
			
			$element_counter_default = 0;
			$element_counter_highest = 0;
			$element_counter = 0;
			$active_found = false;
			
			$children_array = array();
			$max_children = null;

			if ($element instanceof WorkflowElementPath)
			{
				$path_length = $element->get_path_length();
				$max_path_length = $element->get_longest_path_length();
			}
			
			for($i = 0; $i <= $next_array_count-1; $i++)
			{	
				if (!in_array($next_array[$i], self::$visited_elements))
				{
					$array = self::get_drawable_element_list($next_array[$i], 
							$active_elements, 
							$next_array_count, 
							$i);

					
					// BEGIN
					/*
					if ($array != null)
					{
						foreach ($array as $line_key => $line_value)
						{
							// Zeilen
							foreach ($line_value as $element_key => $element_value)
							{
								// Elemente
							
								if ($element_value[0] instanceof WorkflowElementActivity)
								{
									echo $element_value[0]->get_id()." (".$element_key.")&nbsp;";
								}
								else
								{
									if ($element_value[0] == null)
									{
										echo "N&nbsp;";
									}
									else
									{
										echo "EL (".$element_key.")&nbsp;";
									}
								}
								
							}
							echo "<br />";
						}		
						
						echo "<br /><br />";
					}
					*/
					// END
					
					if ($array != null)
					{
						$line_path_length = $path_length[$i+1];
						$array_count = count($array);
						
						$line_counter = 1;
						
						// Path
						for ($j = 1; $j <= $array_count; $j++)
						{
							if ($max_path_length >= 1 and $line_path_length >= 1 and $j == 1 and $line_counter <= $max_path_length)
							{
								$element_counter_default_path_backup = $element_counter_default;
								$element_counter_highest_path_backup = $element_counter_highest;
								$element_counter_backup = $element_counter;
							}
							
							if ($max_path_length >= 1 and $line_path_length >= 1 and $j > $line_path_length and $line_counter <= $max_path_length)
							{
								for ($k = $element_counter_highest; $k <= $element_counter_default; $k++)
								{
									$return_array[$line_counter][$k] = array(null, null);
								}
								$line_counter++;
								$j--;
							}
							else
							{	
								if ($max_path_length >= 1 and $line_path_length >= 1 and $j > $max_path_length)
								{
									$element_counter_default = $element_counter_default_path_backup;
									$element_counter_highest = $element_counter_highest_path_backup;
									$element_counter = $element_counter_backup;
								}
								
								// Lines
								if ($element_counter_highest < $element_counter)
								{
									$element_counter_highest = $element_counter;
								}
								$element_counter = $element_counter_default;
								if (is_array($array[$j-1]))
								{
									foreach ($array[$j-1] as $element_key => $element_value)
									{
										// Elements
										$return_array[$line_counter][($element_counter_default+$element_key)] = $element_value;
										$element_counter++;
										
										if ($element_value[1] == true)
										{
											$active_found = true;
										}
									}
								}
								$line_counter++;
							}
						}
						
						if ($element_counter_highest < $element_counter)
						{
							$element_counter_highest = $element_counter;
						}
						$element_counter_default = $element_counter_highest;
						
						// Bei Pfaden die nicht enden, wird das ende entsprechend aufgefüllt
						if ($line_counter <= $max_path_length)
						{
							for ($j = $line_counter; $j <= $max_path_length; $j++)
							{
								for ($k = $element_counter_highest; $k <= $element_counter_default; $k++)
								{
									$return_array[$j][$k-1] = array(null, null);
								}
							}
						}
					}
				}
			}
			
			return $return_array;
			
		}
	}
}
?>