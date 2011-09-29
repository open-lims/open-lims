<?php
/**
 * @package project
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
 * 
 */
require_once("interfaces/project_item.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("events/project_item_link_event.class.php");
	
	require_once("access/project_has_item.access.php");
}

/**
 * Project Item Management Class
 * @package project
 */
class ProjectItem implements ProjectItemInterface, EventListenerInterface
{
	private $project_id;
	private $item_id;
	private $item_class_id;
	
	private $project_log_id;
	
	private $status_id;
	private $gid;

	/**
	 * @see ProjectItemInterface::__construct()
	 * @param integer $project_id
	 */
    function __construct($project_id)
    {
    	if ($project_id == null)
    	{
    		$this->project_id = null;
    	}
    	else
    	{
    		$this->project_id = $project_id;
    		$this->project = new Project($project_id);
    	}
    }
    
    function __destruct()
    {
    	unset($this->project_id);
    	unset($this->item_id);
    	unset($this->project_log_id);
    	unset($this->status_id);
    	unset($this->gid);
    	unset($this->item_class_id);
    }
    
    /**
     * @see ProjectItemInterface::link_item()
     * @return bool
     */
    public function link_item()
    {    	
    	global $transaction;
    	
    	if ($this->item_id and $this->project_id)
    	{
			$transaction_id = $transaction->begin();
			
			if (($project_folder_id = ProjectFolder::get_folder_by_project_id($this->project_id)) != null)
			{
				$project_has_item = new ProjectHasItem_Access(null);
	    		if ($project_has_item->create($this->project_id, $this->item_id, null) == null)
	    		{
	    			
	    		}
	    		
				$project_item_link_event = new ProjectItemLinkEvent($this->item_id, $project_folder_id);
				$event_handler = new EventHandler($project_item_link_event);
				
				if ($event_handler->get_success() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
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
				if ($transaction_id != null)
  				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::unlink_item()
     * @return bool
     */
    public function unlink_item()
    {    	
    	if ($this->item_id and $this->project_id)
    	{		
    		$primary_key = ProjectHasItem_Access::get_entry_by_item_id_and_project_id($this->item_id, $this->project_id);
    		$project_has_item = new ProjectHasItem_Access($primary_key);
    		
    		if ($project_has_item->delete())
    		{
    			// Event
	  			$item_unlink_event = new ItemUnlinkEvent($this->item_id);
				$event_handler = new EventHandler($item_unlink_event);
					
				if ($event_handler->get_success() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
    			return true;
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
     * @see ProjectItemInterface::unlink_item_full()
     * @return bool
     */
    public function unlink_item_full()
    {
    	global $transaction;
    	
    	if ($this->item_id)
    	{
    		$transaction_id = $transaction->begin();
    			
  			$project_has_item_pk_array = ProjectHasItem_Access::list_entries_by_item_id($this->item_id);

  			if (is_array($project_has_item_pk_array))
  			{
  				if (count($project_has_item_pk_array) >= 1)
  				{
	  				foreach ($project_has_item_pk_array as $key => $value)
	  				{
	  					$project_has_item = new ProjectHasItem_Access($value);
	  					if ($project_has_item->delete() == false)
	  					{
	  						if ($transaction_id != null)
	  						{
								$transaction->rollback($transaction_id);
							}
							return false;
	  					}
	  				} 
	  				
	  				if ($transaction_id != null)
	  				{
						$transaction->commit($transaction_id);
					}
	  				return true;
  				}
  				else
  				{
  					return true;
  				}				
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
     * @see ProjectItemInterface::set_active()
     * @param bool $active
     * @return bool
     */
    public function set_active($active)
    {    	
    	if ($this->item_id and $this->project_id and isset($active))
    	{	
    		$primary_key = ProjectHasItem_Access::get_entry_by_item_id_and_project_id($this->item_id, $this->project_id);
    		$project_has_item = new ProjectHasItem_Access($primary_key);
    		
    		if ($project_has_item->set_active($active))
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
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::set_required()
     * @param bool $required
     * @return bool
     */
    public function set_required($required)
    {    	
    	if ($this->item_id and $this->project_id and isset($required))
    	{	
    		$primary_key = ProjectHasItem_Access::get_entry_by_item_id_and_project_id($this->item_id, $this->project_id);
    		$project_has_item = new ProjectHasItem_Access($primary_key);
    		
    		if ($project_has_item->set_required($required))
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
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::is_active()
     * @return bool
     */
    public function is_active()
    {
    	if ($this->item_id and $this->project_id)
    	{
    		$primary_key = ProjectHasItem_Access::get_entry_by_item_id_and_project_id($this->item_id, $this->project_id);
    		$project_has_item = new ProjectHasItem_Access($primary_key);
    		return $project_has_item->get_active();
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::is_required()
     * @return bool
     */
    public function is_required()
    {
    	if ($this->item_id and $this->project_id)
    	{		
    		$primary_key = ProjectHasItem_Access::get_entry_by_item_id_and_project_id($this->item_id, $this->project_id);
    		$project_has_item = new ProjectHasItem_Access($primary_key);
    		return $project_has_item->get_required();
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @todo overhaul
     * @see ProjectItemInterface::get_project_items()
     * @return array
     */
    public function get_project_items()
    {
    	if ($this->project_id)
    	{
    		$project_has_item_array = ProjectHasItem_Access::list_entries_by_project_id($this->project_id);

    		if (is_array($project_has_item_array) and count($project_has_item_array) >= 1)
    		{
    			$return_array = array();
    			
    			foreach($project_has_item_array as $key => $value)
    			{
    				$project_has_item = new ProjectHasItem_Access($value);
    				array_push($return_array, $project_has_item->get_item_id());
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
     * @see ProjectItemInterface::get_project_status_items()
     * @return array
     */
    public function get_project_status_items($project_status_id)
    {
    	if ($this->project_id)
    	{
    		return ProjectHasItem_Access::list_entries_by_project_id_and_project_status_id($this->project_id, $project_status_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectItemInterface::set_item_id()
     * @param integer $item_id
     * @return bool
     */
    public function set_item_id($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$this->item_id = $item_id;
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::set_gid()
     * @param integer $gid
     * @return bool
     */
    public function set_gid($gid)
    {
    	if (is_numeric($gid))
    	{
    		$this->gid = $gid;
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::set_status_id()
     * @param integer $status_id
     * @return bool
     */
    public function set_status_id($status_id)
    {
    	if (is_numeric($status_id))
    	{
    		$this->status_id = $status_id;
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::set_item_status()
     * @return bool
     */
    public function set_item_status()
    {
    	global $transaction;

    	if ($this->item_id and $this->project_id and $this->status_id and is_numeric($this->gid))
    	{
    		$transaction_id = $transaction->begin();
			
			$primary_key = ProjectHasItem_Access::get_entry_by_item_id_and_project_id($this->item_id, $this->project_id);
			$project_has_item = new ProjectHasItem_Access($primary_key);
			
			if ($project_has_item->set_gid($this->gid) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
    		if ($project_has_item->set_project_status_id($this->status_id) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
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
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::unset_item_status()
     * @todo implementation
     * @return bool
     */
    public function unset_item_status()
    {
    	global $transaction;
    	
    	if ($this->item_id)
    	{
    		
    	}
    	else
    	{
    		
    	}
    }
    
    /**
     * Checks if a class already exists
     * @param string $class_name
     * @return integer
     */
    private function exist_class($class_name)
    {
    	if ($this->project_id)
    	{
    		$item_array = $this->get_project_items();
    		
    		if (is_array($item_array) and count($item_array) >= 1)
    		{
    			foreach($item_array as $key => $value)
    			{
 
    				$item_class_array = ItemClass::list_classes_by_item_id($value);
    				
    				if (is_array($item_class_array) and count($item_class_array) >= 1)
    				{
    					foreach($item_class_array as $item_key => $item_value)
    					{
    						$item_class = new ItemClass($item_value);
    						if (trim(strtolower($item_class->get_name())) == trim(strtolower($class_name)))
    						{
    							return $item_value;
    						}
    					}
    				}
    			}
    			return null;
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
     * @see ProjectItemInterface::set_class()
     * @param string $class_name
     * @return bool
     */
    public function set_class($class_name)
    {
    	global $user, $transaction;
    	
    	if ($this->item_id)
    	{
    		$transaction_id = $transaction->begin();
    	
	    	if (($item_class_id = $this->exist_class($class_name)) == null)
	    	{
	    		$item_class = new ItemClass(null);
	    		if (($item_class_id = $item_class->create($class_name, $user->get_user_id())) == null)
	    		{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
					return false;
	    		}
	    	}
	    	else
	    	{
	    		$item_class = new ItemClass($item_class_id);
	    	}
	    	
	    	$this->item_class_id = $item_class_id;
	    	
	    	if ($item_class->link_item($this->item_id) == true)
	    	{
	    		if ($transaction_id != null)
	    		{
					$transaction->commit($transaction_id);
				}
				return true;
	    	}
	    	else
	    	{
	    		if ($transaction_id != null)
	    		{
					$transaction->rollback($transaction_id);
				}
	    		return false;
	    	}
    	}
    	else
    	{
    		return false;
    	}
    	
    }
    
    /**
     * @see ProjectItemInterface::unset_class()
     * @todo implementation
     * @return bool
     */
    public function unset_class()
    {
    	
    }
    
    /**
     * @see ProjectItemInterface::set_information()
     * @param string $description
     * @param string $keywords
     * @return bool
     */   
	public function set_information($description, $keywords)
	{
    	global $transaction;
    	
    	if ($description or $keywords)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$item_information = new ItemInformation(null);
    		if ($item_information->create($description, $keywords) != null)
    		{
    			if ($this->is_item_information() != false or $this->is_class_information(true) != false)
    			{
	    			if ($this->is_item_information() != false)
	    			{
	    				$item_information->link_item($this->item_id);
		    		}
		    		
		    		if ($this->is_class_information(true) != false)
		    		{
		    			$item_information->link_class($this->item_class_id);
		    		}
		    		
		    		if ($transaction_id != null)
		    		{
						$transaction->commit($transaction_id);
					}
	    			return true;
    			}
    			else
    			{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
	    			return false;
    			}
    		}
    		else
    		{
    			if ($transaction_id != null)
    			{
					$transaction->rollback($transaction_id);
				}
    			return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * Checks if an Item needs item-information
     * @return bool
     */
    private function is_item_information()
    {
    	if ($this->project and $this->status_id and is_numeric($this->gid))
    	{
    		$project_template = new ProjectTemplate($this->project->get_template_id());
    		$status_item_array = $project_template->get_status_item($this->gid, $this->status_id);
    		
    		if (is_array($status_item_array) and count($status_item_array) >= 1)
    		{
    			foreach($status_item_array as $key => $value)
    			{
    				if ($value[xml_element] == "information")
    				{
    					$return_array = array();
    							
						if ($value[keywords] == "keywords")
						{
							$return_array[keywords] = true;
						}
						
						if ($value[description] == "description")
						{
							$return_array[description] = true;
						}
						
						if ($return_array[keywords] or $return_array[description])
						{
							return $return_array;
						}
						else
						{
							return false;
						}
					}	
    			}
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * Checks if an Item needs class-information
     * @param $no_class_exist_check If this is true, method will not check the existence of the class
     * @return bool
     */
    private function is_class_information($no_class_exist_check)
    {
    	if ($this->project and $this->status_id and is_numeric($this->gid))
    	{
    		$project_template = new ProjectTemplate($this->project->get_template_id());
    		$attribute_array = $project_template->get_gid_attributes($this->gid, $this->status_id);
    		
    		if (is_array($attribute_array) and count($attribute_array) >= 1)
    		{
    			if ($attribute_array['class'])
    			{
    				$class_name = $attribute_array['class'];
    				
    				if ($this->exist_class($class_name) and ($no_class_exist_check == false or $no_class_exist_check == null))
    				{
    					return false;
    				}
    				else
    				{
	    				$class_array = $project_template->get_status_class($this->status_id, $class_name);
	    				
	    				if (is_array($class_array) and count($class_array) >= 1)
	    				{
	    					foreach($class_array as $key => $value)
	    					{
	    						if ($value[xml_element] == "information")
	    						{
	    							$return_array = array();
	    							
	    							if ($value[keywords] == "keywords")
	    							{
	    								$return_array[keywords] = true;
	    							}
	    							
	    							if ($value[description] == "description")
	    							{
	    								$return_array[description] = true;
	    							}
	    							
	    							if ($return_array[keywords] or $return_array[description])
	    							{
	    								return $return_array;
	    							}
	    							else
	    							{
	    								return false;
	    							}
	    						}
	    					}
	    				}
	    				else
	    				{
	    					return false;
	    				}
    				}
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
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectItemInterface::is_description()
     * @return bool
     */
    public function is_description()
    {
    	if ($this->status_id and is_numeric($this->gid))
    	{
	    	$class_information = $this->is_class_information(true);
	    	$item_information = $this->is_item_information();
	    	
	    	if ($class_information)
	    	{
				if ($class_information[description] == true)
				{
					return true;
				}
	    	}
	    	elseif($item_information)
	    	{
	    		if ($item_information[description] == true)
	    		{
					return true;
				}
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
     * @see ProjectItemInterface::is_keywords()
     * @return bool
     */
    public function is_keywords()
    {
    	if ($this->status_id and is_numeric($this->gid))
    	{
	    	$class_information = $this->is_class_information(true);
	    	$item_information = $this->is_item_information();
	    	
	    	if ($class_information)
	    	{
				if ($class_information[keywords] == true)
				{
					return true;
				}
	    	}
	    	elseif($item_information)
	    	{
	    		if ($item_information[keywords] == true)
	    		{
					return true;
				}
	    	}
	    	else
	    	{
	    		return false;
	    	}
    	
    	}else{
    		return false;
    	}
    	
    }
    
	/**
     * @see ProjectItemInterface::is_description_required()
     * @return bool
     */
    public function is_description_required()
    {
    	if ($this->status_id and is_numeric($this->gid))
    	{
	    	$class_information = $this->is_class_information(false);
	    	$item_information = $this->is_item_information();
	    	
	    	if ($class_information)
	    	{
				if ($class_information[description] == true)
				{
					return true;
				}
	    	}
	    	elseif($item_information)
	    	{
	    		if ($item_information[description] == true)
	    		{
					return true;
				}
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
     * @see ProjectItemInterface::is_keywords_required()
     * @return bool
     */
    public function is_keywords_required()
    {
    	if ($this->status_id and is_numeric($this->gid))
    	{
	    	$class_information = $this->is_class_information(false);
	    	$item_information = $this->is_item_information();
	    	
	    	if ($class_information)
	    	{
				if ($class_information[keywords] == true)
				{
					return true;
				}
	    	}
	    	elseif($item_information)
	    	{
	    		if ($item_information[keywords] == true)
	    		{
					return true;
				}
	    	}
	    	else
	    	{
	    		return false;
	    	}
    	
    	}else{
    		return false;
    	}
    	
    }
    
    /**
     * @see ProjectItemInterface::is_classified()
     * @return bool
     */
    public function is_classified()
    {
    	if ($this->project and $this->status_id and is_numeric($this->gid))
    	{
    		$project_template = new ProjectTemplate($this->project->get_template_id());
    		if (is_array($attribute_array = $project_template->get_gid_attributes($this->gid, $this->status_id)))
    		{
    			if ($attribute_array['class'])
    			{
    				if ($attribute_array['classify'] == "force")
    				{
    					return $attribute_array['class'];
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
     * @see ProjectItemInterface::create_log_entry()
     * @return bool
     */
    public function create_log_entry()
    {
    	global $transaction;
    	
    	if ($this->project_id and $this->item_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		if ($this->project_log_id)
    		{
    			$project_log_id = $this->project_log_id;
    		}
    		else
    		{
    			$project_log = new ProjectLog(null);
				if (($project_log_id = $project_log->create($this->project_id, null, false, false, md5(rand(0,32768)))) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
    			$this->project_log_id = $project_log_id;
    		}
			
			$project_log_has_item = new ProjectLogHasItem($project_log_id);
			if ($project_log_has_item->link_item($this->item_id) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
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
    		return false;
    	}
    }
  
  
    /**
     * @see ProjectItemInterface::get_gid_by_item_id_and_project_id()
     * @param integer $item_id
     * @param integer $project_id
     * @param integer $project_status_id
     * @return array
     */
    public static function get_gid_by_item_id_and_project_id($item_id, $project_id, $project_status_id)
    {
    	return ProjectHasItem_Access::get_gid_by_item_id_and_project_id($item_id, $project_id, $project_status_id);
    }
    
    /**
     * @see ProjectItemInterface::get_gid_by_item_id_and_status_id()
     * @param integer $item_id
     * @param integer $project_id
     * @return array
     */
    public static function get_gid_by_item_id_and_status_id($item_id, $status_id)
    {
    	return ProjectHasItem_Access::get_gid_by_item_id_and_status_id($item_id, $status_id);
    }
    
  	/**
  	 * @see ProjectItemInterface::list_projects_by_item_id()
  	 * @return array
  	 */
  	public static function list_projects_by_item_id($item_id)
  	{
  		if (is_numeric($item_id))
  		{
  			$project_has_item_pk_array = ProjectHasItem_Access::list_entries_by_item_id($item_id);
  			  			
  			if (is_array($project_has_item_pk_array) and count($project_has_item_pk_array) >= 1)
  			{
  				$return_array = array();
  				
  				foreach ($project_has_item_pk_array as $key => $value)
  				{
  					$project_has_item = new ProjectHasItem_Access($value);
  					array_push($return_array, $project_has_item->get_project_id());
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
	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof ItemDeleteEvent)
    	{
    		$project_item = new ProjectItem(null);
			$project_item->set_item_id($event_object->get_item_id());
			if ($project_item->unlink_item_full() == false)
			{
				return false;
			}
    	}
    	
    	return true;
    }
  
}
?>