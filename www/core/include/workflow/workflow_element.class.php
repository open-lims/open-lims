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
 * Workflow Element Class
 * @package workflow
 */
class WorkflowElement // implements WorkflowInterface
{
	protected $previous_array = array();
	protected $next_array = array();
	
	private $attechments = array();
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @param object $element
	 */
	public function add_next($element, $add_previous = true)
	{
		if ($element instanceof WorkflowElement)
		{
			if (!in_array($element, $this->next_array))
			{
				if ($add_previous == true)
				{
					$element->add_previous($this, false);
				}
				array_push($this->next_array, $element);
			}
		}
	}
	
	/**
	 * @param object $element
	 */
	public function add_previous($element, $add_next = true)
	{
		if ($element instanceof WorkflowElement)
		{
			if (!in_array($element, $this->previous_array))
			{
				if ($add_next == true)
				{
					$element->add_next($this, false);
				}
				array_push($this->previous_array, $element);
			}
		}
	}
	
	public function get_next()
	{
		return $this->next_array;
	}
	
	public function get_previous()
	{
		return $this->previous_array;
	}

	public function get_attachment($address)
	{
		return $this->attechments[$address];
	}
	
	public function attach($address, $object)
	{
		$this->attechments[$address] = $object;
	}
}
?>