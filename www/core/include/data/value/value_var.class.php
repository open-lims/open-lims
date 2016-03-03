<?php
/**
 * @package data
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
 * 
 */
require_once("interfaces/value_var.interface.php");  

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/value_var_case.access.php");
}

/**
 * Manages requests of OLVDL var requests
 * @package data
 */
class ValueVar implements ValueVarInterface, EventListenerInterface
{
    private $folder_id;
    
    private $result;
    private $stack;
    
    private $string_array;
    
    /**
     * @param integer $folder_id
     */
    function __construct($folder_id)
    {
    	$this->stack = array();
    	$this->folder_id = $folder_id;
    }
    
    function __destruct()
    {
    	unset($this->stack);
    }

    /**
     * Returns the content of given address
     * @param string $word
     * @return mixed
     */
    private function interpret($word)
    {
    	switch($word):
    	
    		case "this":
    			$this_found = false;
    			$value_var_case_array = ValueVarCase_Access::list_entries();
    			
    			if (is_array($value_var_case_array) and count($value_var_case_array) >= 1)
    			{
    				foreach($value_var_case_array as $key => $value)
    				{
    					if (($id = $value::is_case($this->folder_id)) !== false)
    					{
    						array_push($this->stack, $key);
    						array_push($this->stack, $id);
    						$this_found = true;
    						
    						$instance = $value::get_instance();
    						$return = $instance->get_content($this->string_array, $this->stack, null, null);
    						$this->stack = $instance->get_stack();
    						$this->string_array = $instance->get_string_array();
    						return $return;
    					}
    				}
    				
    				if ($this_found == false)
    				{
    					return "Error: Not within a \"this\"-type!";
    				}
    			}
    			else
    			{
    				return "Error: No \"this\"-types found!";
    			}
    		break;

    		default:
    			if (is_numeric($word))
    			{
    				array_push($this->stack, $word);
    				if (!is_numeric($this->stack[count($this->stack)-2]))
    				{
    					$value_var_case_class = ValueVarCase_Access::get_handling_class_by_name($this->stack[count($this->stack)-2]);
    					if ($value_var_case_class)
    					{
	    					$instance = $value_var_case_class::get_instance();
	    					$return = $instance->get_content($this->string_array, $this->stack, null, null);
	    					$this->stack = $instance->get_stack();
	    					$this->string_array = $instance->get_string_array();
	    					return $return;
    					}
    					else
    					{
    						return "Error: No Type Found!";
    					}
    				}
    				else
    				{
    					return "Error: No Type!";
    				}
    			}
    			else
    			{
    				array_push($this->stack, $word);
    				return $this->get_content(null);
    			}
    		break;    	

    	endswitch;
    }

    /**
     * @see ValueVarInterface::get_content()
     * @param string $string
     * @return mixed
     */
    public function get_content($string)
    {
    	if ($string)
    	{
	    	$number_of_words = substr_count($string, ".");

    		if ($number_of_words == 0)
    		{
    			$this->string_array[0] = $string;
    		}
    		else
    		{
	    		$this->string_array = explode(".", $string);
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
     * @see ValueVarInterface::register_type()
     * @param string $name
     * @param stirng $handling_class
     * @param bool $ignore_this
     * @param integer $include_id
     * @return bool
     */
	public static function register_type($name, $handling_class, $ignore_this, $include_id)
	{
		$value_var_case = new ValueVarCase_Access(null);
		if ($value_var_case->create($name, $handling_class, $ignore_this, $include_id) != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ValueVarInterface::delete_by_include_id()
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_by_include_id($include_id)
	{
		return ValueVarCase_Access::delete_by_include_id($include_id);
	}
	
    /**
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {    	
    	if ($event_object instanceof IncludeDeleteEvent)
    	{
			if (ValueVarCase_Access::delete_by_include_id($event_object->get_include_id()) == false)
			{
				return false;
			}
    	}
    	
    	return true;
    }
}
?>