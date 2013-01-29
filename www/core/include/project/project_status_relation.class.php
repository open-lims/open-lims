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
require_once("interfaces/project_status_relation.interface.php");

/**
 * @deprecated remove while introducing project workflow system
 * Project Status Relation Class
 * @package project
 */
class ProjectStatusRelation implements ProjectStatusRelationInterface
{
	private $project_id;
	private $status_id;
	
	private $project;

	/**
	 * @see ProjectStatusRelationInterface::__construct()
	 * @param integer $project_id
	 * @param integer $status_id
	 */
    function __construct($project_id, $status_id)
    {
    	if (is_numeric($project_id) and is_numeric($status_id))
    	{
    		$this->project_id = $project_id;
    		$this->status_id = $status_id;
    		$this->project = new Project($project_id);    		
    	}
    	else
    	{
    		$this->project_id = null;
    		$this->status_id = null;
    		$this->project = null;
    	}
    }
    
    /**
     * @see ProjectStatusRelationInterface::is_less()
     * @param integer $status_id
     * @return bool
     */
    public function is_less($status_id)
    {
    	if ($this->project)
    	{
    		$project_status_array = $this->project->get_all_status_array();
    		
    		if (is_array($project_status_array) and count($project_status_array) >= 1)
    		{
    			foreach($project_status_array as $key => $value)
    			{
    				if ($value['id'] == $this->status_id)
    				{
    					return true;
    				}
    				
    				if ($value['id'] == $status_id)
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
    
    /**
     * @see ProjectStatusRelationInterface::is_more()
     * @param integer $status_id
     * @return bool
     */
    public function is_more($status_id)
    {
    	if ($this->project)
    	{
    		$project_status_array = $this->project->get_all_status_array();
    		
    		if (is_array($project_status_array) and count($project_status_array) >= 1) {
    			
    			foreach($project_status_array as $key => $value) {
    				
    				if ($value['id'] == $this->status_id) {
    					return false;
    				}
    				
    				if ($value['id'] == $status_id) {
    					return true;
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
    
    /**
     * @see ProjectStatusRelationInterface::get_current()
     * @return integer
     */
    public function get_current()
    {
    	return $this->status_id;
    }
    
    /**
     * @see ProjectStatusRelationInterface::get_next()
     * @return integer
     */
 	public function get_next()
 	{
 		if ($this->project)
 		{
    		$project_status_array = $this->project->get_all_status_array();
    		
    		$return_next = false;
    		
    		if (is_array($project_status_array) and count($project_status_array) >= 1)
    		{
    			foreach($project_status_array as $key => $value)
    			{
    				if ($return_next == true)
    				{
    					return $value['id'];
    				}
    				
    				if ($value['id'] == $this->status_id)
    				{
    					$return_next = true;
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
     * @see ProjectStatusRelationInterface::get_previous()
     * @return integer
     */
 	public function get_previous()
 	{
 		if ($this->project)
 		{
    		$project_status_array = $this->project->get_all_status_array();
    		
    		$previous_id = 0;
    		
    		if (is_array($project_status_array) and count($project_status_array) >= 1)
    		{
    			foreach($project_status_array as $key => $value)
    			{
    				if ($value['id'] == $this->status_id)
    				{
    					return $previous_id;
    				}
    				$previous_id = $this->value['id'];
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
     * @see ProjectStatusRelationInterface::set_next()
     * @return boolean
     */
 	public function set_next()
 	{
 		if ($this->status_id)
 		{
 			$this->status_id = $this->get_next();
 		}
 	}
    
	/**
     * @see ProjectStatusRelationInterface::set_previous()
     * @return boolean
     */
 	public function set_previous()
 	{
 		if ($this->status_id)
 		{
 			$this->status_id = $this->get_previous();
 		}
 	}
}
?>