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
 * 
 */
require_once("interfaces/project_template.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{		
	require_once("access/project_template.access.php");
}

/**
 * Project Template Management Class
 * @package project
 */
class ProjectTemplate implements ProjectTemplateInterface
{
	private $project_template_id;
	private $project_template;
	
	/**
	 * @see ProjectTemplateInterface::__construct()
	 * @param integer $project_tempalte_id
	 * @throws ProjectTemplateNotFoundException
	 */
	function __construct($project_template_id)
	{
		if (is_numeric($project_template_id))
		{
			if (ProjectTemplate_Access::exist_id($project_template_id) == true)
			{
				$this->project_template_id = $project_template_id;
				$this->project_template = new ProjectTemplate_Access($project_template_id);
			}
			else
			{
				throw new ProjectTemplateNotFoundException();
			}
		}
		else
		{
			$this->project_template_id = null;
			$this->project_template = new ProjectTemplate_Access(null);
		}
	}
	
	function __destruct()
	{
		if ($this->project_template_id)
		{
			unset($this->project_template_id);
			unset($this->project_template);
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::create()
	 * @param integer $data_entity_id
	 * @param integer $category_id
	 * @param bool $parent_template
	 * @return bool
	 * @throws ProjectTemplateCreateException
	 * @throws ProjectTemplateCreateOLDLNotFoundException
	 * @throws ProjectTemplateCreateOLDLCreateException
	 */
	public function create($data_entity_id, $category_id, $parent_template)
	{
		global $transaction;
		
		if ($this->project_template and is_numeric($data_entity_id) and is_numeric($category_id) and isset($parent_template))
		{
			$xml_cache = new XmlCache($data_entity_id);
    		$xml_array = $xml_cache->get_xml_array();

			$oldl_found = false;
			$title_found = false;
			$id_found = false;
			$id = null;
			$title = "";
			
			if (is_array($xml_array) and count($xml_array) >= 1)
			{
				foreach($xml_array as $key => $value)
				{
					$value[1] = trim(strtolower($value[1]));
					$value[2] = trim($value[2]);
					
					if ($value[1] == "oldl" and $value[2] != "#")
					{
						if ($value[3]['type'])
						{
							if ($value[3]['type'] != "project")
							{
								return false;		
							}
						}
						$oldl_found = true;
					}
					
					if ($value[1] == "title" and $value[2] != "#")
					{
						if ($value[2])
						{
							$title = trim($value[2]);
							$title_found = true;
						}
					}
					
					if ($value[1] == "id" and $value[2] != "#")
					{
						if ($value[2])
						{
							if (is_numeric(trim($value[2])))
							{
								$id = (int)trim($value[2]);
								$id_found = true;
							}
						}
					}
				}
				
				if ($oldl_found == false or $title_found == false)
				{
					throw new ProjectTemplateCreateOLDLNotFoundException();
				}
				
				$transaction_id = $transaction->begin();
				
				$oldl = new Oldl(null);
				if (($oldl_id = $oldl->create($data_entity_id)) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new ProjectTemplateCreateOLDLCreateException();
				}
		
				if ($this->project_template->create($id, $title, $category_id, $parent_template, $oldl_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new ProjectTemplateCreateException("DB Failed");
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}	
			}
			else
			{
				throw new ProjectTemplateCreateException("XML File Empty or Corrupt");
			}
		}
		else
		{
			throw new ProjectTemplateCreateException("Missing Information");
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::delete()
	 * @return bool
	 * @throws ProjectTemplateDeleteException
	 * @throws ProjectTemplateDeleteInUseException
	 * @throws ProjectTemplateDeleteOLDLDeleteExcepion
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->project_template and $this->project_template_id)
		{
			$project_array = Project::list_entries_by_template_id($this->project_template_id);
			if (is_array($project_array))
			{
				if (count($project_array) != 0)
				{
					throw new ProjectTemplateDeleteInUseException();
				}
			}
			
			$transaction_id = $transaction->begin();
				
			$oldl = new Oldl($this->project_template->get_template_id());
			
			if ($this->project_template->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new ProjectTemplateDeleteException("DB delete failed");
			}
			
			if ($oldl->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new ProjectTemplateDeleteOLDLDeleteException();
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				return true;
			}
		}
		else
		{
			throw new ProjectTemplateDeleteException("Missing Information");
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->project_template and $this->project_template_id)
		{
			return $this->project_template->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_parent_template()
	 * @return bool
	 */
	public function get_parent_template()
	{
		if ($this->project_template)
		{
			return $this->project_template->get_parent_template();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::is_required_requirements()
	 * @return bool
	 */	
	public function is_required_requirements()
	{
		if ($this->project_template and $this->project_template_id)
		{
			$oldl = new Oldl($this->project_template->get_template_id());
	    	$xml_array = $oldl->get_cutted_xml_array("required");	
	    	    	    
		    if (is_array($xml_array) and count($xml_array) >= 1)
		    {
		    	return true;	
		    }
		    else
		    {
		    	return false;
		    }
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_required_requirements()
	 * @return array
	 */
	public function get_required_requirements()
	{
		if ($this->project_template and $this->project_template_id)
		{
			$oldl = new Oldl($this->project_template->get_template_id());	    
		    $xml_array = $oldl->get_cutted_xml_array("required");
		    
		    $return_array = array();
		    $counter = 0;
		    $in_status = false;
		    
		    if (is_array($xml_array) and count($xml_array) >= 1)
		    {
			    foreach($xml_array as $key => $value)
			    {
			    	$value[0] = trim(strtolower($value[0]));
					$value[1] = trim(strtolower($value[1]));
					$value[2] = trim(strtolower($value[2]));
			
		    		if ($value[3]['id'] != "#" and $value[3]['type'] != "#")
		    		{
		    			$return_array[$counter]					= $value[3];
			    		$return_array[$counter]['xml_element'] 	= $value[1];
			    		$counter++;
		    		}
		    		else
		    		{
		    			$return_array[$counter]['xml_element'] 	= $value[1];
		    			$return_array[$counter]['close']		= "1";
		    			$counter++;
		    		}
			    } 
		    }
		    else
		    {
		    	$return_array = array();
		    }
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_status_requirements()
	 * @param integer $status_id
	 * @return array
	 */
	public function get_status_requirements($status_id)
	{
		if ($this->project_template and $this->project_template_id)
		{
			$oldl = new Oldl($this->project_template->get_template_id());
		    $xml_array = $oldl->get_cutted_xml_array("body");
		    
		    $return_array = array();
		    $counter = 0;
		    $in_status = false;
		    
		    foreach($xml_array as $key => $value)
		    {
		    	$value[0] = trim(strtolower($value[0]));
				$value[1] = trim(strtolower($value[1]));
				$value[2] = trim(strtolower($value[2]));
	
		    	if ($value[1] == "status" and $value[2] == "#")
		    	{
		    		$in_status = false;
		    	}
		    	
		    	if ($in_status == true)
		    	{
		    		if ($value[3]['id'] != "#" and $value[3]['type'] != "#")
		    		{
			    		$return_array[$counter] 				= $value[3];
			    		$return_array[$counter]['xml_element'] 	= $value[1];
			    		$counter++;
		    		}
		    		else
		    		{
		    			$return_array[$counter]['xml_element'] 	= $value[1];
		    			$return_array[$counter]['close']		= "1";
		    			$counter++;
		    		}
		    	}
		    	
		    	if ($value[1] == "status" and $value[3]['id'] == $status_id)
		    	{
					$in_status = true;
		    	}
		    }
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_workflow_object()
	 * @return array
	 */
	public function get_workflow_object()
	{
		if ($this->project_template and $this->project_template_id)
		{
			$oldl = new Oldl($this->project_template->get_template_id());
		    $xml_array = $oldl->get_cutted_xml_array("body");
		    
		    $workflow = new Workflow();
		    
		    $decision_counter = 0;
		    $parallel_counter = 0;
		    
		    $option_counter = array();
		    $option_element_counter = array();
		    
		    $path_counter = array();
		    $path_element_counter = array();
		    
		    $last_elements_array = array();
		    $last_elements_array[0] = array();
		    $use_last_elemets = false;
		    
		    
		    foreach($xml_array as $key => $value)
		    {
		    	$value[0] = trim(strtolower($value[0]));
				$value[1] = trim(strtolower($value[1]));
				$value[2] = trim(strtolower($value[2]));

				if ($value[1] == "decision" and $value[2] != "#")
		    	{
		    		$workflow_element_decision[$decision_counter] = new WorkflowElementOr();
		    		
		    		if ($use_last_elemets == true)
		    		{
		    			$workflow->add_element($workflow_element_decision[$decision_counter], true, $current_last_elements_array);
		    			$use_last_elemets = false;
		    			$current_last_elements_array = array();
		    		}
		    		else
		    		{
		    			$workflow->add_element($workflow_element_decision[$decision_counter]);
		    		}
		    		
		    		$decision_counter++;
		    		$option_counter[$decision_counter] = 0;
		    		$option_element_counter[$decision_counter] = array();
		    		$last_elements_array[$decision_counter] = array();
		    	}
		    	
		    	if ($value[1] == "decision" and $value[2] == "#")
		    	{
		    		// Dem n�chsten Element mitteilen, dass es die letzten Elemente der Options verarbeitet
		    		$use_last_elemets = true;
		    		// $last_elements_array[$decision_counter-1] = array_merge($last_elements_array[$decision_counter-1],$last_elements_array[$decision_counter]);
		    		
		    		if (is_array($current_last_elements_array) and count($current_last_elements_array) >= 1)
		    		{
		    			$current_last_elements_array = array_merge($current_last_elements_array, $last_elements_array[$decision_counter]);
		    		}
		    		else
		    		{
		    			$current_last_elements_array = $last_elements_array[$decision_counter];
		    		}
		    		
		    		// !!! -> Altes Array in Temp array und das in Kind-EL Schreiben
		    		
		    		// Problem, wenn das Descision in Option liegt und danach weitere Options folgen
		    		// Weitere Options m�ssen $use_last_elements ignorieren

		    		// Pfad schreiben, wenn dc > 1, dann l�ngsten pfad ermitteln und zu oc addieren
		    		
		    		$workflow_element_decision[$decision_counter-1]->set_path_length($option_element_counter[$decision_counter]);
		    		$decision_counter--;
		    		
		    		if ($decision_counter > 0)
		    		{
		    			$option_element_counter[$decision_counter][$option_counter[$decision_counter]] = $workflow_element_decision[$decision_counter]->get_longest_path_length()+1;
		    		}
		    	}
		    	
		   		if ($value[1] == "option" and $value[2] != "#")
		    	{
		    		if ($use_last_elemets == true)
		    		{
		    			$use_last_elemets = false;
		    		}
		    		
		    		$workflow->set_current_element($workflow_element_decision[$decision_counter-1]);
		    		
		    		$option_counter[$decision_counter]++;
		    		$option_element_counter[$decision_counter][$option_counter[$decision_counter]] = 0;
		    		
		    		// Zeiger vom Workflow wieder auf das Decision-Element
		    	}
		    	
		    	if ($value[1] == "option" and $value[2] == "#")
		    	{
		    		// Alle letzten option-elemente sind (wenn kein goto) vor-element 
		    		// des n�chsten Elements nach decision
		    		
		    		if ($goto == true)
		    		{
		    			$goto = false;
		    		}
		    		else
		    		{
			    		if (!in_array($workflow_element_status, $last_elements_array[$decision_counter]))
			    		{
			    			array_push($last_elements_array[$decision_counter], $workflow_element_status);
			    		}
		    		}
		    	}
		    	
		    	if ($value[1] == "parallel" and $value[2] != "#")
		    	{
		    		
		    	}
		    	
		    	if ($value[1] == "parallel" and $value[2] == "#")
		    	{
		    		
		    	}
		    	
		   		if ($value[1] == "path" and $value[2] != "#")
		    	{
		    		
		    	}
		    	
		   		if ($value[1] == "path" and $value[2] == "#")
		    	{
		    		
		    	}
		    	
		    	
		  		if ($value[1] == "goto" and $value[2] != "#")
		    	{
		    		$goto = true;
		    		// Zwischenspeichern und nach der foreach-schleife bearbeiten
		    	}
		    	
				
		    	if ($value[1] == "status" and is_numeric($value[3]['id']))
		    	{
		    		if ($decision_counter != 0 and $option_counter != 0)
		    		{
		    			$option_element_counter[$decision_counter][$option_counter[$decision_counter]]++;
		    		}
		    		
		    		$workflow_element_status = new WorkflowElementActivity($value[3][id]);
		    		
		    		if ($value[3]['requirement'] == "optional")
    				{
    					$workflow_element_status->attach("optional", true);
    				}
    				else
    				{
    					$workflow_element_status->attach("optional", false);
    				}
		    		
		    		if ($use_last_elemets == true)
		    		{
		    			$workflow->add_element($workflow_element_status, true, $current_last_elements_array);
		    			$use_last_elemets = false;
		    			$current_last_elements_array = array();
		    		}
		    		else
		    		{
		    			$workflow->add_element($workflow_element_status);
		    		}
		    	}
		    }
		    
			$workflow_element_status = new WorkflowElementActivity(2);
		    		
    		if ($use_last_elemets == true)
    		{
    			$workflow->add_element($workflow_element_status, true, $current_last_elements_array);
    			$use_last_elemets = false;
    			$current_last_elements_array = array();
    		}
    		else
    		{
    			$workflow->add_element($workflow_element_status);
    		}
		    
		    // $workflow->print_elements();
		    
			return $workflow;
		
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_next_status()
	 * @param status_id
	 * @return integer
	 */
	public function get_next_status($status_id)
	{
		if ($this->project_template and $this->project_template_id and is_numeric($status_id))
		{
			$oldl = new Oldl($this->project_template->get_template_id());		    
		    $xml_array = $oldl->get_cutted_xml_array("body");
		    
		    $return_array = array();
		    $status_found = false;
		    
		    if (is_array($xml_array) and count($xml_array) >= 1)
		    {
			    foreach($xml_array as $key => $value)
			    {
			    	$value[0] = trim(strtolower($value[0]));
					$value[1] = trim(strtolower($value[1]));
					$value[2] = trim(strtolower($value[2]));
			    	
		    		if ($value[1] == "status" and is_numeric($value[3]['id']))
		    		{
			    		if ($status_found == false)
			    		{
			    			if ($value[3]['id'] == $status_id)
			    			{
			    				$status_found = true;
			    			}
			    		}
			    		else
			    		{
			    			return $value[3]['id'];
			    		}
			    	}
			    }
		    }
		    return 2;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @see ProjectTemplateInterface::get_gid_attributes()
	 * @param integer $gid
	 * @param integer $status_id
	 * @return array
	 */
	public function get_gid_attributes($gid, $status_id)
	{
		if ($this->project_template and $this->project_template_id and is_numeric($gid) and is_numeric($status_id))
		{
			$status_requirements = $this->get_status_requirements($status_id);
			
			if (is_array($status_requirements) and count($status_requirements) >= 1)
			{
				$item_counter = 0;
				$return_array = array();
				
				foreach($status_requirements as $key => $value)
				{
					if ($value['xml_element'] == "item" and !$value['close'])
					{
						if ($item_counter == $gid or $value['gid'] === $gid)
						{
				    		if (is_numeric($value['gid']))
				    		{
				    			$return_array['gid']	  		= $value['gid'];
				    		}
				    		
				    		if ($value['classify'])
				    		{
				    			$return_array['classify']		= $value['classify'];
				    		}
				    		
				    		if ($value['requirement'])
				    		{
				    			$return_array['requirement']	= $value['requirement'];
				    		}
				    		
				    		if ($value['occurrence'])
				    		{
				    			$return_array['occurrence']		= $value['occurrence'];
				    		}
				    		
				    		if ($value['name'])
				    		{
				    			$return_array['name']			= $value['name'];
				    		}
				    		
				    		if ($value['class'])
				    		{
				    			$return_array['class']			= $value['class'];
				    		}
				    		
				    		if ($value['folder'])
				    		{
				    			$return_array['folder']			= $value['folder'];
				    		}
						}
					}
					
					if ($value['xml_element'] == "item" and $value['close'] == "1")
					{
						$item_counter++;
					}
				}
				return $return_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_status_attributes()
	 * @param integer $status_id
	 * @return array
	 */
	public function get_status_attributes($status_id)
	{
		if ($this->project_template and $this->project_template_id and is_numeric($status_id))
		{
			$oldl = new Oldl($this->project_template->get_template_id());	    
		    $xml_array = $oldl->get_cutted_xml_array("body");
		    
		    $return_array = array();
		    $counter = 0;
		    $in_status = false;
		    
		    foreach($xml_array as $key => $value)
		    {
		    	$value[0] = trim(strtolower($value[0]));
				$value[1] = trim(strtolower($value[1]));
				$value[2] = trim(strtolower($value[2]));
		    	
		    	if ($value[1] == "status" and $value[3]['id'] == $status_id and $value[3]['id'] != "#" and $value[3]['type'] != "#")
		    	{ 				    		
		    		if ($value[3]['id'])
		    		{
		    			$return_array['id']	  			= $value[3]['id'];
		    		}
		    		if ($value[3]['requirement'])
		    		{
		    			$return_array['requirement']	= $value[3]['requirement'];
		    		}
		    	}
		    }
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_status_item()
	 * @param integer $gid
	 * @param integer $status_id
	 * @return array
	 */
	public function get_status_item($gid, $status_id)
	{
		if ($this->project_template and $this->project_template_id and is_numeric($status_id) and is_numeric($gid))
		{
			$status_requirements = $this->get_status_requirements($status_id);
			
			if (is_array($status_requirements) and count($status_requirements) >= 1)
			{
				$counter = 0;
				$item_counter = 0;
				$return_array = array();
								
				foreach($status_requirements as $key => $value)
				{
					if ($value['xml_element'] == "item" and !$value['close'])
					{
						if ($item_counter == $gid or $value[$counter]['gid'] == $gid)
						{
							$in_item = true;
						}
					}
					
					if ($in_item == true)
					{
						array_push($return_array, $value);
					}
					
					if ($value['xml_element'] == "item" and $value['close'] == "1")
					{
						$in_item = false;
						$item_counter++;
					}
					$counter++;
				}
				return $return_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectTemplateInterface::get_status_class()
	 * @param integer $status_id
	 * @param string $class_name
	 * @return array
	 */
	public function get_status_class($status_id, $class_name)
	{
		if ($this->project_template and $this->project_template_id and is_numeric($status_id) and $class_name)
		{
			$status_requirements = $this->get_status_requirements($status_id);
			
			if (is_array($status_requirements) and count($status_requirements) >= 1)
			{
				$counter = 1;
				$return_array = array();
				
				foreach($status_requirements as $key => $value)
				{
					if ($value['xml_element'] == "class" and $value['name'] == $class_name and !$value['close'])
					{
						$in_class = true;	
					}
					
					if ($in_class == true)
					{
						array_push($return_array, $value);
					}
					
					if ($value['xml_element'] == "class" and $value['close'] == "1")
					{
						$in_class = false;
					}
				}
				return $return_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}

	
	/**
	 * @see ProjectTemplateInterface::exist_id()
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		return ProjectTemplate_Access::exist_id($id);
	}
	
	/**
	 * @see ProjectTemplateInterface::list_entries()
	 * @return array
	 */
	public static function list_entries()
	{
		return ProjectTemplate_Access::list_entries();
	}
		
	/**
	 * @see ProjectTemplateInterface::list_entries_by_cat_id()
	 * @param integer $cat_id
	 * @return array
	 */	
	public static function list_entries_by_cat_id($cat_id)
	{
		return ProjectTemplate_Access::list_entries_by_cat_id($cat_id);
	}

}
?>