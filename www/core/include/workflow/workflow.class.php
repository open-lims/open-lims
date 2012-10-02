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
	
	private $start_element;
	private $current_element;
	private $active_elements = array();
	
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
	
	public function set_current_element($element)
	{
		$this->current_element = $element;
	}
	
	public function set_status_active($status_id)
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
		
		if ($element instanceof WorkflowElementStatus)
		{
			echo "Status: ".$element->get_id()."; ";
		}
		
		if ($element instanceof WorkflowElementDecision)
		{
			echo "Decision; ";
		}
		
		if ($element instanceof WorkflowElementParallel)
		{
			echo "Parallel; ";
		}
		
		array_push(self::$printed_elements, $element);
		
		echo "Children: ";
		
		foreach($next_array as $key => $value)
		{
			if ($value instanceof WorkflowElementStatus)
			{
				echo $value->get_id()." ";
			}
			
			if ($value instanceof WorkflowElementDecision)
			{
				echo "Decision ";
			}
			
			if ($value instanceof WorkflowElementParallel)
			{
				echo "Parallel ";
			}
		}
		
		echo "; Parents: ";
		
		foreach($previous_array as $key => $value)
		{
			if ($value instanceof WorkflowElementStatus)
			{
				echo $value->get_id()." ";
			}
			
			if ($value instanceof WorkflowElementDecision)
			{
				echo "Decision ";
			}
			
			if ($value instanceof WorkflowElementParallel)
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
}
?>