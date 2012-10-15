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
 * Workflow Element Path Class
 * @package workflow
 */
class WorkflowElementPath extends WorkflowElement // implements WorkflowInterface
{
	private $path_length_array = array();
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_path_length()
	{
		return $this->path_length_array;	
	}
	
	public function get_longest_path_length()
	{
		if (is_array($this->path_length_array) and count($this->path_length_array) >= 1)
		{
			$longest_path_length = 0;
			
			foreach($this->path_length_array as $key => $value)
			{
				if ($value > $longest_path_length)
				{
					$longest_path_length = $value;
				}
			}
			
			return $longest_path_length;
		}
		else
		{
			return 0;
		}
	}
	
	public function set_path_length($path_length_array)
	{
		$this->path_length_array = $path_length_array;
	}
}
?>