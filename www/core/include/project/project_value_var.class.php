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
 * 
 */
// require_once("interfaces/project_value_var.interface.php");  

/**
 * Manages requests of OLVDL var requests
 * @package project
 */
class ProjectValueVar
{
	function __construct($project_id)
	{
		
	}
	
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
		
				case "parent":
	    			if ($this->stack[count($this->stack)-2] == "project")
	    			{
	    				if (($project_toid = $this->project->get_project_toid()) != null)
	    				{
	    					array_pop($this->stack);
	    					array_push($this->stack, $project_toid);
	    					$this->project = new Project($project_toid);
	    					$this->project_id = $project_toid;
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
	    		
	    		case "parents":
	    			if ($this->stack[count($this->stack)-2] == "project")
	    			{
	    				if (($project_toid = $this->project->get_project_toid()) != null)
	    				{
	    					if (!is_array($this->result))
	    					{
									$this->result = array();
							}
	    					
	    					array_push($this->result, $project_toid);
	    					array_push($this->stack, "parents");
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
	    		
	    		case "current":
	    			if ($this->stack[count($this->stack)-1] == "status")
	    			{
	    				array_push($this->stack, $this->project->get_current_status_id());
	    			}
	    			else
	    			{
	    				// Exception
	    			}
	    		break;
	    			    		
	    		case "status":
	    			if ($this->stack[count($this->stack)-2] == "project")
	    			{
	    				array_push($this->stack, "status");
	    			}
	    			else
	    			{
	    				// Exception
	    			}
	    		break;
	    		
	    		case "required":
	    			array_push($this->stack, "status");
	    			array_push($this->stack, 0);
	    		break;
	    		
	    		case "item":
	    			// An Item
	    		break;
	    		
	    		
	    	
	    		
	    		case "list":
	    			if ($this->stack[count($this->stack)-1] == "parents")
	    			{
	    				array_pop($this->stack);
	    			}
	    			array_push($this->stack, "list");
	    		break;
	    		
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
	    						$this->result = $this->project->get_name();
	    					break;
	    				endswitch;
	    			}
	    		break;
	    		
	    		default:
	    			if (is_numeric($current_statement))
	    			{	
	    				if (count($this->stack) >= 1)
	    				{
	    					switch ($this->stack[count($this->stack)-1]):
	    						case "project":
	    							array_push($this->stack, $current_statement);
	    							$this->project_id = $current_statement;
	    							$this->project = new Project($current_statement);
	    						break;
	    						
	    						case "status":
	    							array_push($this->stack, $current_statement);
	    						break;
	    					
		    					default:
		    						// Exception
		    					break;
	    					endswitch;	
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
	
	
	public static function init($address)
	{
		
	}
}