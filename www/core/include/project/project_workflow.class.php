<?php
/**
 * @package project
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
// require_once("interfaces/project_workflow.interface.php");


/**
 * Project Template Management Class
 * @package project
 */
class ProjectWorkflow // implements ProjectWorkflowInterface
{
	private static $visited_elements = array();
	
	/**
	 * @param object $element
	 * @param array $active_elements
	 * @param integer $number_of_visible_elements
	 * @param integer $number_of_parent_children
	 * @param integer $child_id
	 * @return array
	 */
	public static function get_status_list($element, $active_elements, $number_of_visible_elements, $number_of_parent_children, $child_id)
	{
		$next_array = &$element->get_next();
		$next_array_count = count($next_array);
		
		array_push(self::$visited_elements, $element);

		if($next_array_count <= 0)
		{
			if ($number_of_visible_elements == 0 and $number_of_visible_elements != null)
			{
				if (in_array($element, $active_elements))
				{
					return array(array(array(&$element, true)));	// Override maximum
				}
				else
				{
					// return null;
					return array(array(array(&$element, false)));
				}
			}
			else
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
					if ($number_of_visible_elements != null)
					{
						$child_number_of_visible_elements = $number_of_visible_elements - $i;
						if ($child_number_of_visible_elements < 0)
						{
							$child_number_of_visible_elements = 0;
						}
					}
					else
					{
						$child_number_of_visible_elements = null;
					}
					
					
					$array = self::get_status_list($next_array[$i], 
							$active_elements, 
							$child_number_of_visible_elements, 
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
								// Lines
								if ($element_counter_highest < $element_counter)
								{
									$element_counter_highest = $element_counter;
								}
								$element_counter = $element_counter_default;
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
								$line_counter++;
							}
						}
						
						if ($element_counter_highest < $element_counter)
						{
							$element_counter_highest = $element_counter;
						}
						$element_counter_default = $element_counter_highest;
					}
				}
			}
	
			/*
			if ($number_of_visible_elements == 0)
			{
				if (!in_array($element, $active_elements) and $active_found == false)
				{
					return null;
				}
			} 
			*/

			
			// Bereinigung
			// Versuche maximal $number_of_visible_elements im Array zu halten
			
			foreach ($return_array as $line_key => $line_value)
			{
				// Zeilen
				foreach ($line_value as $element_key => $element_value)
				{
					// Elemente
					
					
				}
			}
			
			return $return_array;
			
		}
		
		// Rückgabe (maximal $number_of_visible_elements, minimum 0 bei inaktiv, 1 bei aktiv
	}
}
?>