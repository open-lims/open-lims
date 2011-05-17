<?php
/**
 * @package project
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
 * Manages requests of OLVDL var requests
 * @package project
 */
class ProjectValueVar implements ValueVarCaseInterface
{
	private static $instance;
	
	private $project_id;
	private $temp;
    private $result;
    private $stack;
    
    private $string_array;
	
    private function interpret($word)
    {
    	switch($word):

    		case "getName":
    			if ($this->stack[count($this->stack)-1] == "list")
    			{
    				if ($this->stack[count($this->stack)-3] == "project")
    				{
						// [...]
    				}
    			}
    			else
    			{
    				switch ($this->stack[count($this->stack)-2]):
    					case "project":
  							if ($this->project instanceof Project)
  							{
  								$this->result = $this->project->get_name();
  							}
  							else
  							{
  								$this->project = new Project($this->stack[count($this->stack)-1]);
  								$this->result = $this->project->get_name();
  							}
    						
    					break;
    				endswitch;
    			}
    		break;
    		
    		case "item":
    			if ($this->stack[count($this->stack)-2] == "status")
	    		{
	    			if ($this->stack[count($this->stack)-1] != 0)
	    			{
	    				$project_item = new ProjectItem($this->project_id);
						$project_item_array = $project_item->get_project_items();
								
						if (is_array($project_item_array) and count($project_item_array) >= 1)
						{
							$result_array = array();
									
							foreach($project_item_array as $fe_key => $fe_value)
							{
								if (ProjectItem::get_gid_by_item_id_and_status_id($fe_value, $this->stack[count($this->stack)-2]) !== null)
								{
									array_push($result_array, $fe_value);
								}
							}
							$this->temp = $result_array;
						}
	    			}
	    			else
	    			{
		    			$project_item = new ProjectItem($this->project_id);
						$project_item_array = $project_item->get_project_items();
							
						if (is_array($project_item_array) and count($project_item_array) >= 1)
						{
							$result_array = array();
								
							foreach($project_item_array as $fe_key => $fe_value)
							{
								$project_item = new ProjectItem($this->project_id);
								$project_item->set_item_id($fe_value);
										
								if ($project_item->is_required() == true)
								{
									array_push($result_array, $fe_value);
								}
							}
							$this->temp = $result_array;
						}
	    			}
	    		}
	    		else
	    		{
	    			$project_item = new ProjectItem($this->project_id);
    				$this->temp = $project_item->get_project_items();
	    		}
	    		
	    		array_push($this->stack, "item");
	    		
    			$item_value_var = new ItemValueVar();
    			$this->result = $item_value_var->get_content($this->string_array, $this->stack, $this->result, $this->temp);
    			$this->stack = $item_value_var->get_stack();
    			$this->string_array = $item_value_var->get_string_array();
    		break;
    		
    		case "current":
    			
    		break;
    		
    		case "status":
    			
    		break;
    		
    		case "required":
    			
    		break;
    		
    		default:
    			
    		break;    	

    	endswitch;
    	
    	if (count($this->string_array) == 0)
    	{
    		return $this->result;
    	}
    	else
    	{
    		return $this->get_content(null, null, null, null);
    	}
    }

	public function get_content($string_array, $stack, $result, $temp)
	{
		if (is_array($string_array) and is_array($stack))
		{
	    	$this->string_array = $string_array;
			$this->stack = $stack;
			$this->result = $result;
			$this->stack;
			
			$stack_length = count($stack);
			if ($stack_length >= 2)
			{
				$this->project_id = $this->stack[$stack_length-1];
			}
    		
    		return $this->interpret(array_shift($this->string_array));
		}
		elseif(is_array($this->string_array) and count($this->string_array) >= 1)
		{
			return $this->interpret(array_shift($this->string_array));
		}
		else
		{
			return null;
		}
	}
	
	public function get_stack()
	{
		return $this->stack;
	}
	
	public function get_string_array()
	{
		return $this->string_array;
	}
	
	
	public static function is_case($folder_id)
	{
		if (is_numeric($folder_id))
		{			
			if (($project_id = ProjectFolder::get_project_id_by_folder_id($folder_id)) != null)
			{
				return $project_id;
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
	
	public static function get_instance()
	{
		if (self::$instance instanceof ProjectValueVar)
		{
			return self::$instance;
		}
		else
		{
			self::$instance = new ProjectValueVar();
			return self::$instance;
		}
	}
}