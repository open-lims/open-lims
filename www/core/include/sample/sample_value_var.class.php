<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * @package sample
 */
class SampleValueVar implements ValueVarCaseInterface
{
	private static $instance;
	
	private $sample_id;
	private $sample;
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
    				if ($this->stack[count($this->stack)-3] == "sample")
    				{
						// [...]
    				}
    			}
    			else
    			{
    				switch ($this->stack[count($this->stack)-2]):
    					case "sample":
  							if ($this->sample instanceof Sample)
  							{
  								$this->result = $this->sample->get_name();
  							}
  							else
  							{
  								$this->sample = new Sample($this->stack[count($this->stack)-1]);
  								$this->result = $this->sample->get_name();
  							}
  							return $this->result;
    					break;
    				endswitch;
    			}
    		break;
    		
    		case "item":
	    		$sample_item = new SampleItem($this->sample_id);
    			$this->temp = $sample_item->get_sample_items();
	    		
	    		array_push($this->stack, "item");
	    		
    			$item_value_var = new ItemValueVar();
    			$this->result = $item_value_var->get_content($this->string_array, $this->stack, $this->result, $this->temp);
    			$this->stack = $item_value_var->get_stack();
    			$this->string_array = $item_value_var->get_string_array();
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
				$this->sample_id = $this->stack[$stack_length-1];
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
			if (($sample_id = SampleFolder::get_sample_id_by_folder_id($folder_id)) != null)
			{
				return $sample_id;
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
		if (self::$instance instanceof SampleValueVar)
		{
			return self::$instance;
		}
		else
		{
			self::$instance = new SampleValueVar();
			return self::$instance;
		}
	}
	
}