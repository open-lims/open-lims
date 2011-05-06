<?php
/**
 * @package data
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
 * 
 */
require_once("interfaces/value_var.interface.php");  

/**
 * Manages requests of OLVDL var requests
 * @package data
 * @todo create an interface for adapting other subsystem information
 * @todo remove project dependency, create class in project subsystem
 * @todo remove sample dependency, create class in sample subsystem
 */
class ValueVar implements ValueVarInterface
{
    private $item_array;
    private $result;
    private $stack;
    
    function __construct()
    {
    	$this->stack = array();
    }
    
    function __destruct()
    {
    	unset($this->stack);
    }

    
    /**
     * Returns the content of given address
     * @param string $address
     * @todo remove dependecies
     */
    public function get_var_content($address)
    {
    	$number_of_statements = substr_count($address, ".");
    	
    	if ($number_of_statements >= 0)
    	{
    		if ($number_of_statements == 0)
    		{
    			$current_statement = $address;
    		}
    		else
    		{
	    		$statement_array = explode(".", $address);
	    		$current_statement = $statement_array[0];
    		}
	    	
	    	switch($current_statement):
	    	
	    		case "external":
	    			
	    			if ($number_of_statements == 0)
	    			{
		    			$external_statement = null;
		    		}
		    		else
		    		{
		    			if ($number_of_statements == 1)
		    			{
		    				$external_statement = $statement_array[1];
		    			}
		    			else
		    			{
				    		$statement_array = explode(".", $address);
				    		$statement_string = "";
				    		for ($i=1;$i<=$number_of_statements;$i++)
				    		{
				    			if (!$statement_string)
				    			{
				    				$statement_string = $statement_array[$i];
				    			}
				    			else
				    			{
				    				$statement_string .= ".".$statement_array[$i];
				    			}
				    		}
		    			}
			    		$external_statement = $statement_string;
		    		}
		    		
		    		$value_external_var = new ValueExternalVar();
		    		return $value_external_var->get_var_content($statement_string);
	    		break;
	    	
	    		case "this":
	    			
	    		break;

	    		
	    		case "item":
	    			if ($this->stack[count($this->stack)-2] == "status")
	    			{						
	    				if (is_numeric($this->stack[count($this->stack)-1]))
	    				{
	    					if ($this->stack[count($this->stack)-1] != 0)
	    					{
		    					array_push($this->stack, "item");
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
									$this->item_array = $result_array;
								}
	    					}
	    					else
	    					{
	    						array_push($this->stack, "item");
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
									$this->item_array = $result_array;
								}
	    					}	
	    				}
	    				else
	    				{
	    					// Exception
	    				}
	    			}
	    			elseif ($this->stack[count($this->stack)-2] == "sample")
	    			{
	    				if (is_numeric($this->stack[count($this->stack)-1]))
	    				{
	    					array_push($this->stack, "item");
	    					$sample_item = new ProjectItem($this->sample_id);
							$this->item_array = $project_item->get_sample_items();
	    				}
	    				else
	    				{
	    					// Exception
	    				}
	    			}
	    			elseif ($this->stack[count($this->stack)-2] == "project")
	    			{
	    				if (is_numeric($this->stack[count($this->stack)-1]))
	    				{
	    					array_push($this->stack, "item");
	    					$project_item = new ProjectItem($this->project_id);
							$this->item_array = $project_item->get_project_items();
	    				}
	    				else
	    				{
	    					// Exception
	    				}
	    			}
	    			else
	    			{
	    				// Exception
	    			}
	    		break;
	    		
	    		case "value":
	    			if ($this->stack[count($this->stack)-1] == "item")
	    			{
	    				array_push($this->stack, "value");
	    			}
	    			else
	    			{
	    				// Exception
	    			}
	    		break;
	    		
	
	    		case "list":
	    			if($this->stack[count($this->stack)-1] == "value")
	    			{
	    				if ($this->stack[count($this->stack)-2] == "item" and $this->item_array)
	    				{
	    					if (is_array($this->item_array) and count($this->item_array) >= 1)
	    					{
	    						$result_array = array();
	    						
	    						foreach($this->item_array as $fe_key => $fe_value)
	    						{
	    							if (DataEntity::is_kind_of("value", $fe_value) == true)
	    							{
	    								$data_entity_id = DataEntity::get_entry_by_item_id($fe_value);
	    								if (($value_id = Value::get_value_id_by_data_entity_id($data_entity_id)) != null)
	    								{
	    									array_push($result_array, $value_id);
	    								}
	    							}
	    						}
	    						$this->result = $result_array;
	    						$this->item_array = $result_array;
	    					}
	    					else
	    					{
	    						$this->result = null;
	    					}
	    				}
	    				else
	    				{
	    					// Exception
	    				}
	    			}
	    			elseif ($this->stack[count($this->stack)-1] == "parents")
	    			{
	    				array_pop($this->stack);
	    			}
	    			array_push($this->stack, "list");
	    		break;
	    		
	    		case "getName":
	    				switch ($this->stack[count($this->stack)-2]):
		    					case "value":
		    						if (is_array($this->result) and count($this->result) >= 1)
		    						{
		    							$tmp_array = $this->result;
		    							$this->result = array();
		    							foreach ($tmp_array as $fe_key => $fe_value)
		    							{
		    								$value = new Value($fe_value);
		    								$this->result[$fe_value] = $value->get_type_name();
		    							}
		    						}
		    				break;
		    			endswitch;	    	
	    		break;
	    		
	    		default:
	    			if (is_numeric($current_statement))
	    			{	
	    				if (count($this->stack) >= 1)
	    				{
	    					switch ($this->stack[count($this->stack)-1]):
	    						case "value":
	    							array_push($this->stack, $current_statement);
	    						break;
	    					
		    					default:
		    						// Exception
		    					break;
	    					endswitch;	
	    				}
	    			}
	    			else
	    			{
	    				if ($this->stack[count($this->stack)-2] == "value" and
	    					is_numeric($this->stack[count($this->stack)-1]))
	    				{
	    					
	    				}
	    				else
	    				{
	    					// // Exception - Undefined
	    				}
	    			}
	    		break;    	
	
	    	endswitch;
	    	
	    	if ($number_of_statements == 0)
	    	{
    			return $this->result;
    		}
    		else
    		{
    			if ($number_of_statements == 1)
    			{
    				$statement_string = $statement_array[1];
    			}
    			else
    			{
		    		$statement_array = explode(".", $address);
		    		$statement_string = "";
		    		for ($i=1;$i<=$number_of_statements;$i++)
		    		{
		    			if (!$statement_string)
		    			{
		    				$statement_string = $statement_array[$i];
		    			}
		    			else
		    			{
		    				$statement_string .= ".".$statement_array[$i];
		    			}
		    		}
    			}
	    		return $this->get_var_content($statement_string);
    		}
    	}
    	else
    	{
    		// Exception
    	}
    }

}
?>