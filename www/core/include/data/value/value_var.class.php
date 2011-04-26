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
    private $project_id;
    private $sample_id;
    
    private $project;
    private $sample;
    
    private $item_array;
    private $project_array;
    private $sample_array;
    
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
     * @param integer $project_id
     * @todo remove method
     */
    public function set_project_id($project_id)
    {
    	if (is_numeric($project_id))
    	{
    		$this->project_id = $project_id;
    		$this->project = new Project($project_id);
    	}
    }
    
    /**
     * @param integer $sample_id
     * @todo remove method
     */
    public function set_sample_id($sample_id)
    {
    	if (is_numeric($sample_id)) {
    		$this->sample_id = $sample_id;
    		$this->sample = new Sample($sample_id);
    	}
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
	    			if ($this->project_id)
	    			{
	    				array_push($this->stack, "project");
	    				array_push($this->stack, $this->project_id);
	    			}
	    			elseif($this->sample_id)
	    			{
	    				array_push($this->stack, "sample");
	    				array_push($this->stack, $this->sample_id);
	    			}
	    			else
	    			{
	    				// Exception
	    			}
	    		break;
	    		
	    		case "project":
	    			array_push($this->stack, "project");
	    		break;
	    		
	    		case "sample":
	    			if (count($this->stack) <= 0)
	    			{
	    				array_push($this->stack, "sample");
	    			}
	    			else
	    			{
	    				if ($this->stack[count($this->stack)-1] == "item")
	    				{
	    					array_push($this->stack, "sample");
	    				}
	    				else
	    				{
	    					// Exception
	    				}
	    			}
	    		break;
	    		
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
	    			elseif ($this->stack[count($this->stack)-2] == "sample")
	    			{
	    				/**
	    				 * @deprecated: method no more exists
	    				 */
	    				$parent_sample = $this->sample->list_parent_samples();
	    				/**
	    				 * @deprecated: method no more exists
	    				 */
	    				$parent_project = $this->sample->get_parent_project();
	    				
	    				// [!]
	    				if ((!count($parent_sample) == 1) and (!count($parent_sample) == 0 or !count($parent_project) == 1)) {
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
	    			elseif ($this->stack[count($this->stack)-2] == "sample")
	    			{
	    				if (is_object($this->sample))
	    				{
		    				/**
		    				 * @todo
		    				 */
	    					// $parent_sample = $this->sample->list_parent_samples();
		    				
		    				if (count($parent_sample) >= 1)
		    				{
								if (!is_array($this->result))
								{
									$this->result = array();
								}
								
								foreach($parent_sample as $key => $value)
								{
									array_push($this->result, $value);
								}
								
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
	    		
	    		case "typeof":
	    			if ($number_of_statements >= 1)
	    			{
	    				$statement_array = explode(".", $address);
			    		$type_of_id = $statement_array[1];
			    		
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
			    		$address = $statement_string;
			    		
			    		switch ($this->stack[count($this->stack)-2]):
	    					case "value":
	    					
	    						$tmp_result = $this->result;
								$this->result = array();

	    						if (is_array($tmp_result) and count($tmp_result) >= 1)
	    						{
	    							foreach ($tmp_result as $fe_key => $fe_value)
	    							{
	    								$value = new Value($fe_value);
	    								
	    								if ($value->get_type_id() == $type_of_id)
	    								{
	    									array_push($this->result, $fe_value);
	    								}
	    							}
	    						}
	    					break;
	    				
	    					case "sample":
								$tmp_result = $this->result;
								$this->result = array();

	    						if (is_array($tmp_result) and count($tmp_result) >= 1)
	    						{
	    							foreach ($tmp_result as $fe_key => $fe_value)
	    							{
	    								$sample = new Sample($fe_value);
	    								$sample_template = new SampleTemplate($sample->get_template_id());
	    								
	    								if ($sample_template->get_cat_id() == $type_of_id)
	    								{
	    									array_push($this->result, $fe_value);
	    								}
	    							}
	    						}
	    					break;
	    				endswitch;	    
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
	    		
	    		case "active":
	    			if ($this->stack[count($this->stack)-2] == "sample" and $this->project_id)
	    			{
						$tmp_result = $this->result;
						$this->result = array();

						if (is_array($tmp_result) and count($tmp_result) >= 1)
						{
							foreach ($tmp_result as $fe_key => $fe_value)
							{
								$sample = new Sample($fe_value);
								$project_item = new ProjectItem($this->project_id);
								$project_item->set_item_id($sample->get_item_id());
								
								if ($project_item->is_active() == true)
								{
									array_push($this->result, $fe_value);
								}
							}	
						}
    				}	
	    		break;
	    		
	    		case "inactive":
	    			if ($this->stack[count($this->stack)-2] == "sample" and $this->project_id)
	    			{
						$tmp_result = $this->result;
						$this->result = array();

						if (is_array($tmp_result) and count($tmp_result) >= 1)
						{
							foreach ($tmp_result as $fe_key => $fe_value)
							{
								$sample = new Sample($fe_value);
								$project_item = new ProjectItem($this->project_id);
								$project_item->set_item_id($sample->get_item_id());
								
								if ($project_item->is_active() == false)
								{
									array_push($this->result, $fe_value);
								}
							}
						}
    				}
	    		break;
	    		
	    		case "list":
	    			if ($this->stack[count($this->stack)-1] == "item")
	    			{   				
	    				if ($this->stack[count($this->stack)-3] == "project" or
	    					$this->stack[count($this->stack)-3] == "sample" or
	    					is_numeric($this->stack[count($this->stack)-2]))
	    				{
	    					if (is_array($this->item_array) and count($this->item_array) >= 1)
	    					{
								$this->result = $this->item_array;
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
	    			elseif ($this->stack[count($this->stack)-1] == "sample")
	    			{
	    				if ($this->stack[count($this->stack)-2] == "item" and $this->item_array)
	    				{
	    					if (is_array($this->item_array) and count($this->item_array) >= 1)
	    					{
	    						$result_array = array();
	    						foreach($this->item_array as $fe_key => $fe_value)
	    						{
	    							if (Sample::is_kind_of("sample", $fe_value) == true)
	    							{
	    								$sample_id = Sample::get_entry_by_item_id($fe_value);
	    								array_push($result_array, $sample_id);
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
	    			elseif($this->stack[count($this->stack)-1] == "value")
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
	    			if ($this->stack[count($this->stack)-1] == "list")
	    			{
	    				if ($this->stack[count($this->stack)-3] == "sample" or 
	    					$this->stack[count($this->stack)-3] == "project")
	    				{
		    				switch ($this->stack[count($this->stack)-3]):
		    					case "project":
		    					
		    					break;
		    					
		    					case "sample":
		    						if (is_array($this->result) and count($this->result) >= 1)
		    						{
		    							$tmp_array = $this->result;
		    							$this->result = array();
		    							foreach ($tmp_array as $key => $value)
		    							{
		    								$sample = new Sample($value);
		    								$this->result[$value] = $sample->get_name();
		    							}
		    						}
		    					break;
	
		    				endswitch;	    
	    				}
	    				else
	    				{
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
		    					
		    					case "sample":
		    						if (is_array($this->result) and count($this->result) >= 1)
		    						{
		    							$tmp_array = $this->result;
		    							$this->result = array();
		    							foreach ($tmp_array as $fe_key => $fe_value)
		    							{
		    								$sample = new Sample($fe_value);
		    								$this->result[$fe_value] = $sample->get_name();
		    							}
		    						}
		    					break;
		    				endswitch;	    	
	    				}
	    			}
	    			else
	    			{
	    				switch ($this->stack[count($this->stack)-2]):
	    					case "project":
	    						$this->result = $this->project->get_name();
	    					break;
	    				
	    					case "sample":
	    						$this->result = $this->sample->get_name();
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
	    						
	    						case "sample":
	    							array_push($this->stack, $current_statement);
	    							$this->sample_id = $current_statement;
	    							$this->sample = new Sample($current_statement);
	    						break;
	    						
	    						case "status":
	    							array_push($this->stack, $current_statement);
	    						break;
	    						
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
	    					if ($this->stack[count($this->stack)-5] == "status" and
	    						is_numeric($this->stack[count($this->stack)-4]))
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
		    						
		    						$array_address = $this->stack[count($this->stack)-1] - 1;
		    						
		    						if ($result_array[$array_address])
		    						{
		    							$value = new Value($result_array[$array_address]);
		    							$value_array = unserialize($value->get_value());
		    							
		    							if ($value_array[$current_statement])
		    							{
		    								$this->result = $value_array[$current_statement];
		    							}
		    						}	
			    				}else{
			    					// Exception
			    				}	
	    					}else{
	    						// Exception - No Status
	    					}
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