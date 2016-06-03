<?php
/**
 * @package project
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
require_once("interfaces/project_task_point.interface.php");

/**
 * Project Task Point Class
 * @package project
 */
class ProjectTaskPoint implements ProjectTaskPointInterface
{
	private $project_id;
	private $project;
	
	private $project_status_array;

	/**
	 * @see ProjectTaskPointInterface::__construct()
	 * @param integer $project_id
	 */
    function __construct($project_id)
    {    	
    	if (is_numeric($project_id))
    	{
    		$this->project_id = $project_id;
    		$this->project = new Project($project_id);
    		$this->project_status_array = $this->project->get_all_status_array();
    	}
    	else
    	{
    		$this->project_id = null;
    		$this->project = null;
    		$this->project_status_array = null;
    	}
    }
    
    function __destruct()
    {
    	unset($this->project_id);
    	unset($this->project);
    }
    
    /**
     * @see ProjectTaskPointInterface::get_status_achieved_points()
     * @param integer $status_id
     * @param string $datetime
     * @return integer
     */
    public function get_status_achieved_points($status_id, $datetime)
    {
    	if ($this->project_id and $this->project and is_numeric($status_id))
    	{
    		$project_status_array = $this->project_status_array;
    		
    		if (is_array($project_status_array) and count($project_status_array) >= 1)
    		{
    			$array_key = -1;
    			$status = 0;
    			
    			foreach($project_status_array as $key => $value)
    			{
    				if ($value['id'] == $status_id)
    				{
    					$array_key = $key;
    					$status = $value['status'];
    					$status_datetime = $value['datetime'];
    				}
    			}	
    			
    			if ($array_key >= 0)
    			{
    				if ($status == 2)
    				{
    					if (is_object($datetime))
    					{
    						$status_datetime_handler = new DatetimeHandler($status_datetime);
    						if ($status_datetime_handler->distance($datetime) < 0)
    						{
    							return 0;
    						}
    						else
    						{
    							return $this->get_status_max_points($status_id) + 1;
    						}
    					}
    					else
    					{
    						return $this->get_status_max_points($status_id) + 1;
    					}
    				}
    				elseif($status == 1)
    				{
    					return $this->get_current_achieved_points($datetime);
    				}
    				else
    				{
    					return 0;
    				} 				
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
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskPointInterface::get_current_achieved_points()
     * @param string $datetime
     * @return integer
     */    
    public function get_current_achieved_points($datetime)
    {	
    	if ($this->project_id and $this->project)
    	{
    		$status_requirement_array 		= $this->project->get_current_status_requirements(); 
    		
    		if (is_array($status_requirement_array) and 
    			is_array($fulfilled_requirement_array) and 
    			count($status_requirement_array ) >= 1 and 
    			count($fulfilled_requirement_array) >= 1)
    		{
    			$points = 0;
    				
    			foreach ($status_requirement_array as $key => $value)
    			{
    				if ($value['fulfilled'] == true and $value['element_type'] == "item")
    				{    					
    					if ($value['requirement'] == "force")
    					{
    						if ($value['occurrence'] == "once")
    						{
    							$points = $points + 2;
    						}
    						else
    						{
    							$points = $points + 1;
    						}
    					}
    				}
    			}	
    			return $points;
    		}
    		else
    		{
    			return 0;
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskPointInterface::get_status_max_points()
     * @param integer $status_id
     * @return integer
     */ 
    public function get_status_max_points($status_id)
    {
    	if ($this->project_id and $this->project and is_numeric($status_id))
    	{
    		$status_requirement_array = $this->project->get_status_requirements($status_id);
    		
    		if (is_array($status_requirement_array) and 
    			count($status_requirement_array ) >= 1)
    		{
    			$points = 0;
    				
    			foreach ($status_requirement_array  as $key => $value)
    			{
					if ($value['requirement'] == "force")
					{
						if ($value['occurrence'] == "once")
    					{
    						$points = $points + 2;
    					}
    					else
    					{
    						$points = $points + 1;
    					}	
					}	    				
    			}		
    			return $points;	
    		}
    		else
    		{
    			return 0;
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskPointInterface::get_task_max_points()
     * @param integer $task_id
     * @return integer
     */
    public function get_task_max_points($task_id)
    {
    	if ($this->project_id and $this->project and is_numeric($task_id))
    	{
    		$project_task = new ProjectTask($task_id);

    		if ($project_task->get_type() == 1)
    		{			
    			$project_status_array = $this->project_status_array;
    			
    			$begin_status_id = $project_task->get_begin_status_id();
    			$end_status_id = $project_task->get_end_status_id();

    			if (is_array($project_status_array) and count($project_status_array) >= 1)
    			{
 					$use_status = false;
  					$points = 0;
  
    				foreach($project_status_array as $key => $value)
    				{
    					if ($value['id'] == $begin_status_id)
    					{
    						$use_status = true;
    					}
    					
    					if ($use_status == true)
    					{
    						if ($value['id'] == $end_status_id)
    						{
	    						if ($project_task->get_finalise() == true)
	    						{
	    							$points = $points + $this->get_status_max_points($value['id']);
	    						}
	    					}
	    					else
	    					{
	    						$points = $points + $this->get_status_max_points($value['id']) + 1;
	    					}
    					}
    					
    					if ($value['id'] == $end_status_id)
    					{
    						$use_status = false;
    					}
    				}
    				
    				if ($begin_status_id == $end_status_id)
    				{
    					$points = $points + 1;
    				}
    				
    				$points = $points - $project_task->get_subtraction_points();
    				
    				return $points;
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
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskPointInterface::get_task_achieved_points()
     * @param integer $task_id
     * @return integer
     */
    public function get_task_achieved_points($task_id)
    {
    	if ($this->project_id and $this->project and is_numeric($task_id))
    	{
    		$project_task = new ProjectTask($task_id);
    		if ($project_task->get_uf_end_time() == -1)
    		{
    			$project_task_end_datetime_handler = new DatetimeHandler($project_task->get_end_date()." 23:59:59");
    		}
    		else
    		{
    			$project_task_end_datetime_handler = new DatetimeHandler($project_task->get_end_date()." ".$project_task->get_end_time());
    		}

    		if ($project_task->get_type() == 1)
    		{  			
    			$project_status_array = $this->project_status_array;
    			
    			$begin_status_id = $project_task->get_begin_status_id();
    			$end_status_id = $project_task->get_end_status_id();
    			
    			if (is_array($project_status_array) and count($project_status_array) >= 1)
    			{
 					$use_status = false;
  					$points = 0;
  
    				foreach($project_status_array as $key => $value)
    				{
    					if ($value['id'] == $begin_status_id)
    					{
    						$use_status = true;
    					}
    					
    					if ($use_status == true)
    					{
	    					if ($value['id'] == $end_status_id)
	    					{
	    						if ($project_task->get_finalise() == true)
	    						{
	    							$points = $points + $this->get_status_achieved_points($value['id'], $project_task_end_datetime_handler);
	    						}
	    					}
	    					else
	    					{
	    						$points = $points + $this->get_status_achieved_points($value['id'], $project_task_end_datetime_handler);
	    					}
    					}
    					
    					if ($value['id'] == $end_status_id)
    					{
    						$use_status = false;
    					}
    				}
    				$points = $points - $project_task->get_subtraction_points();
    				return $points;
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
    	else
    	{
    		return null;
    	}
    }
    
}
?>