<?php
/**
 * @package data
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
 * @package data
 */
class ItemValueVar implements ValueVarCaseInterface
{
	private static $instance;
	
	private $project_id;
  	private $temp;
    private $result;
    private $stack;
    
    private $string_array;
    
    private $handling_class;
    
	private function interpret($word)
    {
    	switch($word):

    		case "list":
    			if (is_array($this->temp))
    			{
    				array_push($this->stack, "list");
    				$this->result = $this->temp;
    				unset($this->temp);	
    			}
    		break;
    		
    		case "typeof":
    			array_push($this->stack, "typeof");
    		break;
    	
    		case "getName":
    			$item_type_array = Item::list_types();
    			
    			if (is_array($this->result))
    			{
    				$new_result = array();
    				
    				foreach ($this->result as $key => $value)
    				{
	    				if (is_array($item_type_array) and count($item_type_array) >= 1)
	    				{
	    					foreach($item_type_array as $item_type_key => $item_type_value)
	    					{
	    						if (class_exists($item_type_value))
	    						{
	    							if ($item_type_value::is_kind_of($item_type_key, $value))
	    							{
	    								$instance = $item_type_value::get_instance_by_item_id($value);
	    								if (!in_array($instance->get_item_object_name(), $new_result))
	    								{
	    									array_push($new_result, $instance->get_item_object_name());
	    								}
	    							}
	    						}
	    					}
	    				}
    				}
    				
    				$this->result = $new_result;
    			}
    			else
    			{
    				if (is_array($item_type_array) and count($item_type_array) >= 1)
    				{
    					foreach($item_type_array as $key => $value)
    					{
    						if (class_exists($value))
    						{
    							if ($value::is_kind_of($key, $this->stack[count($this->stack)-1]))
    							{
    								$instance = $value::get_instance_by_item_id($this->stack[count($this->stack)-1]);
    								$this->result = $instance->get_item_object_name();
    							}
    						}
    					}
    				}
    			}
    		break;
    		
    		case "parents":
    			
    			$item_type_array = Item::list_types();
    			if (is_array($item_type_array) and count($item_type_array) >= 1)
    			{
    				foreach($item_type_array as $key => $value)
    				{
    					if (class_exists($value))
    					{
    						if ($value::is_kind_of($key, $this->stack[count($this->stack)-1]))
    						{
    							$instance = $value::get_instance_by_item_id($this->stack[count($this->stack)-1]);
    							$this->temp = $instance->get_item_parents();
    						}
    					}
    				}
    			}
    				
    		break;
    		
    		default:
    			if (is_numeric($word) or (strpos(strtolower($word), "c") === 0))
    			{
    				if ($this->stack[count($this->stack)-1] == "typeof")
    				{
    					if (strpos(strtolower($word), "c") === 0)
    					{
    						$category_id = (int)str_replace("c","" , $word);
    						$type_id = null;
    					}
    					else
    					{
    						$category_id = null;
    						$type_id = $word;
    					}
    					
    					$handling_class = $this->handling_class;
    					if (is_array($this->result) and count($this->result) >= 1)
    					{
    						$new_result = array();
    						
    						foreach ($this->result as $key => $value)
    						{
    							if ($handling_class::is_type_or_category($category_id, $type_id, $value) == true)
    							{
    								array_push($new_result, $value);
    							}
    						}
    						$this->result = $new_result;
    					}
    				} 
    			}
    			else
    			{
    				$this->handling_class = Item::get_handling_class_by_type($word);
    				$handling_class = $this->handling_class;
    				if (class_exists($handling_class))
    				{
    					if (is_array($this->temp) and count($this->temp) >= 1)
    					{
    						$new_temp = array();
    						
    						foreach ($this->temp as $key => $value)
    						{
    							if ($handling_class::is_kind_of($word, $value) == true)
    							{
    								array_push($new_temp, $value);
    							}
    						}
    						$this->temp = $new_temp;
    					}
    					else
    					{
    						// Too slow
    					}
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
			$this->temp = $temp;
			$this->result = $result;
    		
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
		return false;
	}
	
	/**
	 * @see ValueVarCaseInterface::get_instance()
	 */
	public static function get_instance()
	{
		if (self::$instance instanceof ItemValueVar)
		{
			return self::$instance;
		}
		else
		{
			self::$instance = new ItemValueVar();
			return self::$instance;
		}
	}
}

?>