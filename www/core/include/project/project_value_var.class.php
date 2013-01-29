<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
	private $project;
	private $temp;
    private $result;
    private $stack;
    
    private $string_array;
	
    /**
     * @param string $word
     * @return mixed
     */
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
  							return $this->result;
    					break;
    				endswitch;
    			}
    		break;
    		
    		case "item":
    			if ($this->stack[count($this->stack)-2] == "status")
	    		{
    				$project_item = new ProjectItem($this->project_id);
					$this->temp = $project_item->get_project_status_items($this->stack[count($this->stack)-1], true);
	    		}
	    		else
	    		{
	    			$project_item = new ProjectItem($this->project_id);
    				$this->temp = $project_item->get_project_items(true);
	    		}
	    		
	    		array_push($this->stack, "item");
	    		
    			$item_value_var = new ItemValueVar();
    			$this->result = $item_value_var->get_content($this->string_array, $this->stack, $this->result, $this->temp);
    			$this->stack = $item_value_var->get_stack();
    			$this->string_array = $item_value_var->get_string_array();
    		break;
    		
    		case "current":
    			array_push($this->stack, "status");
    			if (($this->project instanceof Project) == false)
  				{
  					$this->project = new Project($this->stack[count($this->stack)-1]);
  				}
  				array_push($this->stack, $this->project->get_current_status_id());
    		break;
    		
    		case "status":
    			array_push($this->stack, "status");
    		break;
    		
    		case "required":
    			array_push($this->stack, 0);
    		break;
    		
    		default:
    			if (is_numeric($word))
    			{
    				if ($this->stack[count($this->stack)-1] == "status")
	    			{
	    				array_push($this->stack, $word);
	    			}
    			}
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

    /**
	 * @see ValueVarCaseInterface::get_content()
	 * @param array $string_array array of instructions
	 * @param array $stack stack of instructions
	 * @param mixed $result current result set
	 * @param mixed $temp current temp set
	 * @return mixed
	 */
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
	
	/**
	 * @see ValueVarCaseInterface::get_stack()
	 * @return array
	 */
	public function get_stack()
	{
		return $this->stack;
	}
	
	/**
	 * @see ValueVarCaseInterface::get_string_array()
	 * @return array
	 */
	public function get_string_array()
	{
		return $this->string_array;
	}
	
	
	/**
	 * @see ValueVarCaseInterface::is_case()
	 * @param integer $folder_id
	 * @return bool
	 */
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
	
	/**
	 * @see ValueVarCaseInterface::get_instance()
	 * @return object
	 */
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