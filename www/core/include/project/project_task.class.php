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
require_once("interfaces/project_task.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_task.access.php");
	
	require_once("access/project_task_milestone.access.php");
	require_once("access/project_task_process.access.php");
	require_once("access/project_task_status_process.access.php");
	
	require_once("access/project_task_has_previous_task.access.php");
	
	require_once("access/project.wrapper.access.php");
}

/**
 * Project Task Management Class
 * @package project
 */
class ProjectTask implements ProjectTaskInterface, EventListenerInterface
{
	private $task_id;
	
	private $task;
	private $task_type;
	
	private $progress;

	/**
	 * @see ProjectTaskInterface::__construct()
	 * @param integer $task_id
	 */
    function __construct($task_id)
    {
    	if ($task_id)
    	{
			$this->task_id = $task_id;
			$this->task = new ProjectTask_Access($task_id);
			
			switch ($this->task->get_type_id()):
			
				case 1:
					$this->task_type = new ProjectTaskStatusProcess_Access($task_id);
				break;
				
				case 2:
					$this->task_type = new ProjectTaskProcess_Access($task_id);
				break;
				
				case 3:
					$this->task_type = new ProjectTaskMilestone_Access($task_id);
				break;
			
			endswitch;			
		}
		else
		{
			$this->log_id = null;
			$this->task = new ProjectTask_Access(null);
			$this->task_type = null;
		}	
    }
    
    function __destruct()
    {
    	unset($this->task_id);
		unset($this->task);
		unset($this->task_type);
    }
    

    /**
     * @see ProjectTaskInterface::create_status_process()
     * @param integer $project_id
     * @param integer $user_id
     * @param string $comment
     * @param string $start_date
     * @param string $start_time
     * @param string $end_date
     * @param string $end_time
     * @param bool $whole_day
     * @param integer $end_status_id
     * @param bool $finalise
     * @param bool $auto_connect
     * @return integer
     */
    public function create_status_process($project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, $end_status_id, $finalise, $auto_connect)
    {
    	
    	global $transaction;
    	
    	if (is_numeric($project_id) and is_numeric($user_id) and $end_date and ($end_time or $whole_day == true) and $end_status_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$project = new Project($project_id);
    		$project_task_point = new ProjectTaskPoint($project_id);
    		
    		$start_status_id = $project->get_current_status_id();
    		    		
    		if ($auto_connect == true)
    		{
    			// Get all autoconnected tasks of the project
    			$task_array = ProjectTask_Access::list_auto_connected_entries_by_project_id($project_id);
    			
    			// Create Task
    			$current_task_object = new ProjectTask_Access(null);
    			$current_task_id = $current_task_object->create(1, $project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, true);
    			
    			if ($current_task_id)
    			{
	    			$current_task_type_object = new ProjectTaskStatusProcess_Access(null);
	    			if ($current_task_type_object->create($current_task_id, $start_status_id, $end_status_id, $project_task_point->get_current_achieved_points(null), $finalise) == null)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return null;
	    			}
	
	    			if (is_array($task_array) and count($task_array) >= 1)
	    			{
	    				foreach ($task_array as $key => $value)
	    				{
	    					$task_object = new ProjectTask_Access($value);
	    					
	    					if ($task_object->get_type_id() == 1)
	    					{				
	    						$task_type_object = new ProjectTaskStatusProcess_Access($value);
	    						
	    						$project_start_status_relation = new ProjectStatusRelation($project_id, $task_type_object->get_begin_status_id());    							
	    						$project_end_status_relation = new ProjectStatusRelation($project_id, $task_type_object->get_end_status_id());
	    						
	    						if ($project_end_status_relation->get_next() == $start_status_id)
	    						{
	    							// Case 1: End-status of an existing task will be start-status of the new task
	    							
	    							// Attach task directly
	    							$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access(null, null);
	    							if ($project_task_has_previous_task->create($current_task_id, $value) == null)
	    							{
	    								if ($transaction_id != null)
	    								{
											$transaction->rollback($transaction_id);
										}
										return null;
	    							}	    							
	    						}
	    						else
	    						{
	    							// Case 2: Start-status is inside an existing task
	    							
	    							// Case 2a: New task will be included after an existing task, existing task will be shortened
	    							// (e.g. include a task with the same begin like an existing task and choose a later status)

	    							$object_datetime_handler = new DatetimeHandler($task_object->get_end_date());
	    							$current_datetime_handler = new DatetimeHandler($end_date);    							
	    							
	    							if ($project_end_status_relation->is_less($end_status_id) and
	    								$current_datetime_handler->distance($object_datetime_handler) < 0)
	    							{
	    								// Check if interlinked
	    								
						    			$next_task_array = ProjectTaskHasPreviousTask_Access::list_tasks_by_previous_task_id($value);
	    								$connect_task = true;
	    								
						    			if (is_array($next_task_array) and count($next_task_array) >= 1)
						    			{
						    				foreach ($next_task_array as $next_key => $next_value)
						    				{
						    					$check_task_object = new ProjectTask_Access($next_value);
						    					
								    			if ($check_task_object->get_type_id() == 1)
								    			{
				    								$check_task_type_object = new ProjectTaskStatusProcess_Access($value);
							    				
								    				$check_project_start_status_relation = new ProjectStatusRelation($project_id, $check_task_type_object->get_begin_status_id());    							
					    							$check_project_end_status_relation = new ProjectStatusRelation($project_id, $check_task_type_object->get_end_status_id());
					    							
					    							if ($check_task_object->get_end_time() != null)
					    							{
					    								$check_object_datetime_handler = new DatetimeHandler($check_task_object->get_end_date()." ".$check_task_object->get_end_time());
					    							}
					    							else
					    							{
					    								$check_object_datetime_handler = new DatetimeHandler($check_task_object->get_end_date()." 23:59:59");
					    							}
					    							
					    							if ($whole_day == false and $end_time)
					    							{
					    								$check_current_datetime_handler = new DatetimeHandler($end_date." ".$end_time);
					    							}
					    							else
					    							{
					    								$check_current_datetime_handler = new DatetimeHandler($end_date." 23:59:59");
					    							}
					    							
					    							if ($check_project_end_status_relation->is_less($end_status_id) and
	    												$check_current_datetime_handler->distance($check_object_datetime_handler) < 0)
	    											{
					    								$connect_task = false;
					    							}
								    			}
						    				}
						    			}
	    								
	    	
	    								if ($connect_task == true)
	    								{
		    								
		    								if ($task_object->get_whole_day() == true)
		    								{	
		    									$new_start_datetime_handler = new DatetimeHandler($task_object->get_end_date());
		    									$new_start_datetime_handler->add_day(1);
		    									
		    									if ($current_task_object->set_start_date($new_start_datetime_handler->get_formatted_string("Y-m-d")) == false)
		    									{
		    										if ($transaction_id != null)
		    										{
														$transaction->rollback($transaction_id);
													}
													return null;
		    									}
		    								}
		    								else
		    								{
		    									$new_start_datetime_handler = new DatetimeHandler($task_object->get_end_date()." ".$task_object->get_end_time());
		    									$new_start_datetime_handler->add_second(1);
		    									
		    									if ($current_task_object->set_start_date($new_start_datetime_handler->get_formatted_string("Y-m-d")) == false)
		    									{
		    										if ($transaction_id != null)
		    										{
														$transaction->rollback($transaction_id);
													}
													return null;
		    									}
		    									
		    									if ($current_task_object->set_start_time($new_start_datetime_handler->get_formatted_string("H:i:s")) == false)
		    									{
		    										if ($transaction_id != null)
		    										{
														$transaction->rollback($transaction_id);
													}
													return null;
		    									}
		    								}
		    								
							    			if ($current_task_type_object->set_begin_status_id($task_type_object->get_end_status_id()) == false)
							    			{
							    				if ($transaction_id != null)
							    				{
													$transaction->rollback($transaction_id);
												}
												return null;
							    			}
							    			
							    			// Change later tasks
							    			 
							    			$next_task_array = ProjectTaskHasPreviousTask_Access::list_tasks_by_previous_task_id($value);
							    			
							    			if (is_array($next_task_array) and count($next_task_array) >= 1)
							    			{
							    				foreach ($next_task_array as $next_key => $next_value)
							    				{
							    					$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($next_value, $value);
							    					if ($project_task_has_previous_task->set_previous_task_id($current_task_id) == null)
							    					{
							    						if ($transaction_id != null)
							    						{
															$transaction->rollback($transaction_id);
														}
														return null;
							    					}
							    				}
							    			}
							    			
							    			// Connect Tasks	
							    			$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access(null, null);
		    								if ($project_task_has_previous_task->create($current_task_id, $value) == null)
		    								{
		    									if ($transaction_id != null)
		    									{
													$transaction->rollback($transaction_id);
												}
												return null;
		    								}
	    								}					
	    							} 
	    							
	    							// Case 2b: New task will be included before an existing task
	    							if (($project_start_status_relation->is_less($end_status_id) or $task_type_object->get_begin_status_id() == $end_status_id) and 
	    								$project_end_status_relation->is_more($end_status_id) and
	    								$current_datetime_handler->distance($object_datetime_handler) >= 0)
	    							{
	    								
	    								// Change Date and Status
						    			if ($whole_day == true)
						    			{
						    				$new_end_datetime_handler = new DatetimeHandler($end_date);
						    				$new_end_datetime_handler->add_day(1);
						    				
						    				if ($task_object->set_start_date($new_end_datetime_handler->get_formatted_string("Y-m-d")) == false)
						    				{
						    					if ($transaction_id != null)
						    					{
													$transaction->rollback($transaction_id);
												}
												return null;
						    				}
						    			}
						    			else
						    			{
						    				$new_end_datetime_handler = new DatetimeHandler($end_date." ".$end_time);
						    				$new_end_datetime_handler->add_second(1);
						    				
						    				if ($task_object->set_start_date($new_end_datetime_handler->get_formatted_string("Y-m-d")) == false)
						    				{
						    					if ($transaction_id != null)
						    					{
													$transaction->rollback($transaction_id);
												}
												return null;
						    				}
						    				
		    								if ($task_object->set_start_time($new_end_datetime_handler->get_formatted_string("H:i:s")) == false)
		    								{
		    									if ($transaction_id != null)
		    									{
													$transaction->rollback($transaction_id);
												}
												return null;
		    								}
						    			}

						    			if ($task_type_object->set_begin_status_id($end_status_id) == false)
						    			{
						    				if ($transaction_id != null)
						    				{
												$transaction->rollback($transaction_id);
											}
											return null;
						    			}
						    			
						    			
						    			// Change previous tasks
						    			$previous_task_array = ProjectTaskHasPreviousTask_Access::list_previous_tasks_by_task_id($value);
						    			if (is_array($previous_task_array) and count($previous_task_array) >= 1)
						    			{
						    				foreach ($previous_task_array as $previous_key => $previous_value)
						    				{
						    					$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($value, $previous_value);
						    					if ($project_task_has_previous_task->set_task_id($current_task_id) == false)
						    					{
						    						if ($transaction_id != null)
						    						{
														$transaction->rollback($transaction_id);
													}
													return null;
						    					}
						    				}
						    			}
						    			
						    			// Connect Tasks
						    			$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access(null, null);
	    								if ($project_task_has_previous_task->create($value, $current_task_id) == null)
	    								{
	    									if ($transaction_id != null)
	    									{
												$transaction->rollback($transaction_id);
											}
											return null;
	    								}				    			
	    							}
	    						}
	    					}
	    				}
	    			}
		    		
		    		if ($transaction_id != null)
		    		{
						$transaction->commit($transaction_id);
					}
		    		return $current_task_id;
    			}
    			else
    			{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
    				return null;
    			}
    		}
    		else
    		{
				$current_task_object = new ProjectTask_Access(null);
    			if (($current_task_id = $current_task_object->create(1, $project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, false)) != null)
    			{
	    			$current_task_type_object = new ProjectTaskStatusProcess_Access(null);
	    			if ($current_task_type_object->create($current_task_id, $start_status_id, $end_status_id, $project_task_point->get_current_achieved_points(), $finalise) != null)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->commit($transaction_id);
						}
	    				return $current_task_id;
	    			}
	    			else
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return null;
	    			}
    			}
    			else
    			{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
					return null;
    			}
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::create_process()
     * @param integer $project_id
     * @param integer $user_id
     * @param string $comment
     * @param string $start_date
     * @param string $start_time
     * @param string $end_date
     * @param string $end_time
     * @param bool $whole_day
     * @param string $name
     * @param bool $auto_connect
     * @return integer
     */
    public function create_process($project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, $name, $auto_connect)
    {
    	global $transaction;
    	
    	if (is_numeric($project_id) and is_numeric($user_id) and $end_date and ($end_time or $whole_day == true) and $name)
    	{
    		$transaction_id = $transaction->begin();
    		
    		if ($auto_connect == true)
    		{
    			$task_array = ProjectTask_Access::list_auto_connected_entries_by_project_id($project_id);
    			
    			$current_task_object = new ProjectTask_Access(null);
    			$current_task_id = $current_task_object->create(2, $project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, true);
    			
    			if ($current_task_id)
    			{
	    			$current_task_type_object = new ProjectTaskProcess_Access(null);
	    			if ($current_task_type_object->create($current_task_id, $name) == null)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return null;
	    			}
	    			
	    			if (is_array($task_array) and count($task_array) >= 1)
	    			{
	    				foreach ($task_array as $key => $value)
	    				{
	    					$task_object = new ProjectTask_Access($value);
	    					
	    					if ($task_object->get_type_id() == 2)
	    					{
	    						if ($task_object->get_end_time() == null)
	    						{
	    							$object_datetime_handler = new DatetimeHandler($task_object->get_end_date()." 23.59:59");
	    						}
	    						else
	    						{
	    							$object_datetime_handler = new DatetimeHandler($task_object->get_end_date()." ".$task_object->get_end_time());
	    						}
	    						
	    						if (!$end_time)
	    						{
	    							$current_datetime_handler = new DatetimeHandler($end_date." 23:59:59");  
	    						}
	    						else
	    						{
	    							$current_datetime_handler = new DatetimeHandler($end_date." ".$end_time);  
	    						}
	    												
	    						if ($object_datetime_handler->distance($current_datetime_handler) == 0)
	    						{
	    							$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access(null, null);
	    							if ($project_task_has_previous_task->create($current_task_id, $value) == null)
	    							{
	    								if ($transaction_id != null)
	    								{
											$transaction->rollback($transaction_id);
										}
										return null;
	    							}
	    						}
	    					}
	    				}
	    			}
	    			
	    			if ($transaction_id != null)
	    			{
						$transaction->commit($transaction_id);
					}
	    			return $current_task_id;
    			}
    			else
    			{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
					return null;
    			}
    		}
    		else
    		{
    			$current_task_object = new ProjectTask_Access(null);
    			if (($current_task_id = $current_task_object->create(2, $project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, false)) != null)
    			{
    				$current_task_type_object = new ProjectTaskProcess_Access(null);
	    			if ($current_task_type_object->create($current_task_id, $name) != null)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->commit($transaction_id);
						}
	    				return $current_task_id;
	    			}
	    			else
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return null;
	    			}
    			}
    			else
    			{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
					return null;
    			}
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::create_milestone()
     * @param integer $project_id
     * @param integer $user_id
     * @param string $date
     * @param string $time
     * @param string $name
     * @param bool $auto_connect
     * @return integer
     */
    public function create_milestone($project_id, $user_id, $comment, $date, $time, $name, $auto_connect)
    {
    	global $transaction;
    	
    	if (is_numeric($project_id) and is_numeric($user_id) and $date and $name)
    	{
    		$transaction_id = $transaction->begin();
    		
    		if ($auto_connect == true)
    		{		
    			$task_array = ProjectTask_Access::list_auto_connected_entries_by_project_id($project_id);
    			
    			if ($time == null)
    			{
    				$whole_day = true;
    			}
    			else
    			{
    				$whole_day = false;
    			}
    			
    			$current_task_object = new ProjectTask_Access(null);
    			$current_task_id = $current_task_object->create(3, $project_id, $user_id, $comment, $date, $time, $date, $time, $whole_day, true);
    			
    			if ($current_task_id)
    			{
	    			$current_task_type_object = new ProjectTaskMilestone_Access(null);
	    			if ($current_task_type_object->create($current_task_id, $name) == null)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return null;
	    			}
	    			
	    			if (is_array($task_array) and count($task_array) >= 1)
	    			{
	    				foreach ($task_array as $key => $value)
	    				{
	    					$task_object = new ProjectTask_Access($value);
	    					
	    					switch ($task_object->get_type_id()):
	    						case 1:
	    							if ($task_object->get_end_time() == null)
	    							{
		    							$object_datetime_handler = new DatetimeHandler($task_object->get_end_date()." 23.59:59");
		    						}
		    						else
		    						{
		    							$object_datetime_handler = new DatetimeHandler($task_object->get_end_date()." ".$task_object->get_end_time());
		    						}
		    						
		    						if (!$time)
		    						{
		    							$current_datetime_handler = new DatetimeHandler($date." 23:59:59");  
		    						}
		    						else
		    						{
		    							$current_datetime_handler = new DatetimeHandler($date." ".$time);  
		    						}
		    												
		    						if ($object_datetime_handler->distance($current_datetime_handler) === 1)
		    						{
		    							$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access(null, null);
		    							if ($project_task_has_previous_task->create($current_task_id, $value) == null)
		    							{
		    								if ($transaction_id != null)
		    								{
												$transaction->rollback($transaction_id);
											}
											return null;
		    							}
		    						}
	    						break;
	    						
	    						case 2:
		    						if ($task_object->get_end_time() == null)
		    						{
		    							$object_datetime_handler = new DatetimeHandler($task_object->get_end_date()." 23.59:59");
		    						}
		    						else
		    						{
		    							$object_datetime_handler = new DatetimeHandler($task_object->get_end_date()." ".$task_object->get_end_time());
		    						}
		    						
		    						if (!$time)
		    						{
		    							$current_datetime_handler = new DatetimeHandler($date." 23:59:59");  
		    						}
		    						else
		    						{
		    							$current_datetime_handler = new DatetimeHandler($date." ".$time);  
		    						}
		    												
		    						if ($object_datetime_handler->distance($current_datetime_handler) === 1)
		    						{
		    							$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access(null, null);
		    							if ($project_task_has_previous_task->create($current_task_id, $value) == null)
		    							{
		    								if ($transaction_id != null)
		    								{
												$transaction->rollback($transaction_id);
											}
											return null;
		    							}
		    						}
	    						break;
	    					endswitch;
	    				}
	    			}
	    			
	    			if ($transaction_id != null)
	    			{
						$transaction->commit($transaction_id);
					}
	    			return $current_task_id;	
    			}
    			else
    			{
    				return null;
    			}	
    		}
    		else
    		{
    			$current_task_object = new ProjectTask_Access(null);
    			if (($current_task_id = $current_task_object->create(3, $project_id, $user_id, $comment, null, null, $date, $time, false, false)) != null)
    			{
    				$current_task_type_object = new ProjectTaskMilestone_Access(null);
	    			if ($current_task_type_object->create($current_task_id, $name) != null)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->commit($transaction_id);
						}
	    				return $current_task_id;
	    			}
	    			else
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
						return null;
	    			}	
    			}
    			else
    			{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
    			}
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Deletes a status procedd
     * @return bool
     */
    private function delete_status_process()
    {
    	global $transaction;
    	
    	if ($this->task_id and $this->task and $this->task_type)
    	{
    		$transaction_id = $transaction->begin();
    		
    		if ($this->task->get_auto_connect())
    		{
    			$previous_project_task_has_previous_task_array 	= ProjectTaskHasPreviousTask_Access::list_previous_tasks_by_task_id($this->task_id);
    			$next_project_task_has_previous_task_array 		= ProjectTaskHasPreviousTask_Access::list_tasks_by_previous_task_id($this->task_id);
    			
    			$project_relation_array							= array();
    			
    			if (is_array($previous_project_task_has_previous_task_array) and count($previous_project_task_has_previous_task_array) >= 1)
    			{
    				foreach ($previous_project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task_access = new ProjectTask_Access($value);
    					
    					switch($project_task_access->get_type_id()):
    						case 1:
    							$project_task_type = new ProjectTaskStatusProcess_Access($value);
    							
    							if ($project_task_type->set_end_status_id($this->task_type->get_end_status_id()) == false)
    							{
    								if ($transaction_id != null)
    								{
										$transaction->rollback($transaction_id);
									}
									return false;
    							}
    							
    							if ($project_task_access->set_end_date($this->task->get_end_date()) == false)
    							{
    								if ($transaction_id != null)
    								{
										$transaction->rollback($transaction_id);
									}
									return false;
    							}
    							
    							if ($this->task->get_whole_day() == false)
    							{	
		    						if ($project_task_access->set_end_time($this->task->get_end_time()) == false)
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
    								if ($project_task_access->set_whole_day($this->task->get_whole_day()) == false)
    								{
		    							if ($transaction_id != null)
		    							{
											$transaction->rollback($transaction_id);
										}
										return false;
		    						}
    							}
    						break;
    						
    						case 2:
	    						if ($project_task_access->set_end_date($this->task->get_end_date()) == false)
	    						{
	    							if ($transaction_id != null)
	    							{
										$transaction->rollback($transaction_id);
									}
									return false;
	    						}
	    						
	    						if ($this->task->get_whole_day() == false)
	    						{
		    						if ($project_task_access->set_end_time($this->task->get_end_time()) == false)
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
		    						if ($project_task_access->set_whole_day($this->task->get_whole_day()) == false)
		    						{
		    							if ($transaction_id != null)
		    							{
											$transaction->rollback($transaction_id);
										}
										return false;
		    						}
	    						}	
    						break;
    					endswitch;
    					
    					if (is_array($next_project_task_has_previous_task_array) and count($next_project_task_has_previous_task_array) >= 1)
    					{
    						foreach ($next_project_task_has_previous_task_array as $sub_key => $sub_value)
    						{
    							$temp_array 			= array();
    							$temp_array[previous] 	= $value;
    							$temp_array[next]		= $sub_value;
    							if (!in_array($temp_array, $project_relation_array))
    							{
    								array_push($project_relation_array, $temp_array);
    							}
    						}
    					}
    					
    					$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($this->task_id, $value);
    					if ($project_task_has_previous_task->delete() == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
							return false;
    					}
    				}
    			}

				if (is_array($next_project_task_has_previous_task_array) and count($next_project_task_has_previous_task_array) >= 1)
				{
					foreach ($next_project_task_has_previous_task_array as $key => $value)
					{
						if (ProjectTaskHasPreviousTask_Access::exist_entry($value, $this->task_id) == true)
						{
							$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($value, $this->task_id);
	    					if ($project_task_has_previous_task->delete() == false)
	    					{
	    						if ($transaction_id != null)
	    						{
									$transaction->rollback($transaction_id);
								}
	    						return false;
	    					}
						}
					}
				}
				
				if (is_array($project_relation_array) and count($project_relation_array) >= 1)
				{
					foreach ($project_relation_array as $key => $value)
					{
						$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access(null, null);
						if ($project_task_has_previous_task->create($value[next], $value[previous]) == null) {
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}
    		}
    		else
    		{
    			$project_task_has_previous_task_array = ProjectTaskHasPreviousTask_Access::list_previous_tasks_by_task_id($this->task_id);
    			
    			if (is_array($project_task_has_previous_task_array) and count($project_task_has_previous_task_array) >= 1)
    			{
    				foreach ($project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($this->task_id, $value);
    					if ($project_task_has_previous_task->delete() == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
    						return false;
    					}
    				}	
    			}
    			
    			$project_task_has_previous_task_array = ProjectTaskHasPreviousTask_Access::list_tasks_by_previous_task_id($this->task_id);
    			
    			if (is_array($project_task_has_previous_task_array) and count($project_task_has_previous_task_array) >= 1)
    			{
    				foreach ($project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($value, $this->task_id);
    					if ($project_task_has_previous_task->delete() == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
    						return false;
    					}
    				}
    			}   
    		}
    		
    		if ($this->task_type->delete() == true)
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
     * Deletes a process or a milestone
     * @return bool
     */
    private function delete_process_or_milestone()
    {
    	global $transaction;
    	
    	if ($this->task_id and $this->task and $this->task_type)
    	{
    		$transaction_id = $transaction->begin();
    		
    		if ($this->task->get_auto_connect())
    		{	
    			$previous_project_task_has_previous_task_array 	= ProjectTaskHasPreviousTask_Access::list_previous_tasks_by_task_id($this->task_id);
    			$next_project_task_has_previous_task_array 		= ProjectTaskHasPreviousTask_Access::list_tasks_by_previous_task_id($this->task_id);
    			
    			$project_relation_array						= array();
    			
    			if (is_array($previous_project_task_has_previous_task_array) and count($previous_project_task_has_previous_task_array) >= 1)
    			{
    				foreach ($previous_project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task_access = new ProjectTask_Access($value);
    					
    					if ($project_task_access->get_type_id() == 1 or $project_task_access->get_type_id() == 2)
    					{
    						if ($project_task_access->set_end_date($this->task->get_end_date()) == false)
    						{
    							if ($transaction_id != null)
    							{
									$transaction->rollback($transaction_id);
								}
								return false;
    						}
    						
    						if ($this->task->get_whole_day() == false)
    						{
	    						if ($project_task_access->set_end_time($this->task->get_end_time()) == false)
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
    							if ($project_task_access->set_whole_day($this->task->get_whole_day()) == false)
    							{
    								if ($transaction_id != null)
    								{
										$transaction->rollback($transaction_id);
									}
									return false;
    							}
    						}
    					}
    					
    					if (is_array($next_project_task_has_previous_task_array) and count($next_project_task_has_previous_task_array) >= 1)
    					{
    						foreach ($next_project_task_has_previous_task_array as $sub_key => $sub_value)
    						{
    							$temp_array 			= array();
    							$temp_array[previous] 	= $value;
    							$temp_array[next]		= $sub_value;
    							if (!in_array($temp_array, $project_relation_array))
    							{
    								array_push($project_relation_array, $temp_array);
    							}
    						}
    					}
    					
    					$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($this->task_id, $value);
    					if ($project_task_has_previous_task->delete() == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
							return false;
    					}
    				}
    			}

				if (is_array($next_project_task_has_previous_task_array) and count($next_project_task_has_previous_task_array) >= 1)
				{
					foreach ($next_project_task_has_previous_task_array as $key => $value)
					{
						$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($value, $this->task_id);
    					if ($project_task_has_previous_task->delete() == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
							return false;
    					}
					}
				}
				
				if (is_array($project_relation_array) and count($project_relation_array) >= 1)
				{
					foreach ($project_relation_array as $key => $value)
					{
						$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access(null, null);
						if ($project_task_has_previous_task->create($value[next], $value[previous]) == false) {
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}
    		}
    		else
    		{
    			$project_task_has_previous_task_array = ProjectTaskHasPreviousTask_Access::list_previous_tasks_by_task_id($this->task_id);
    			
    			if (is_array($project_task_has_previous_task_array) and count($project_task_has_previous_task_array) >= 1)
    			{
    				foreach ($project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($this->task_id, $value);
    					if ($project_task_has_previous_task->delete() == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
							return false;
    					}
    				}
    			}
    			
    			$project_task_has_previous_task_array = ProjectTaskHasPreviousTask_Access::list_tasks_by_previous_task_id($this->task_id);
    			
    			if (is_array($project_task_has_previous_task_array) and count($project_task_has_previous_task_array) >= 1)
    			{
    				foreach ($project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task_has_previous_task = new ProjectTaskHasPreviousTask_Access($value, $this->task_id);
    					if ($project_task_has_previous_task->delete() == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
							return false;
    					}
    				}
    			}   
    		}
    		
    		if ($this->task_type->delete() == true)
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
     * @see ProjectTaskInterface::delete()
     * @return bool
     */
    public function delete()
    {
    	if ($this->task_id and $this->task and $this->task_type)
    	{
	    	switch ($this->task->get_type_id()):
	    		case 1:
	    			$delete_successful = $this->delete_status_process();
	    		break;
	    		
	    		case 2:
	    		case 3:
	    			$delete_successful = $this->delete_process_or_milestone();
	    		break;
	    	endswitch;
	    	
	    	if ($delete_successful == true)
	    	{
	    		return $this->task->delete();	    		
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
     * @see ProjectTaskInterface::check_status_process()
     * @param integer $project_id
     * @param integer $end_status_id
     * @param bool $finalise
     * @param bool $auto_connect
     * @return bool
     */
    public function check_status_process($project_id, $end_status_id, $finalise, $auto_connect)
    {
    	if (is_numeric($project_id) and $end_status_id and isset($finalise))
    	{
    		if ($auto_connect == false)
    		{
    			return false;
    		}
    		else
    		{
 
    			$task_array = ProjectTask_Access::list_auto_connected_entries_by_project_id($project_id);
    			
    			if (is_array($task_array) and count($task_array) >= 1)
    			{
    				foreach ($task_array as $key => $value)
    				{
    					$project_task = new ProjectTask($value);
    					
    					if ($project_task->get_type() == 1)
    					{
    						if ($project_task->get_end_status_id() == $end_status_id and
    							$project_task->get_finalise() == $finalise)
    						{
    							return true;		
    						}
    					}
    				}
    				return false;
    			}
    			else
    			{
    				return false;
    			}
    		}
    	}
    	else
    	{
    		return true;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_type()
     * @return integer
     */
    public function get_type()
    {
    	if ($this->task_type and $this->task and $this->task_id)
    	{
    		return $this->task->get_type_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_type_name()
     * @return string
     */
    public function get_type_name()
    {
    	if ($this->task)
    	{
    		switch($this->task->get_type_id()):
    			case 1:
    				return "Status Rel. Task";
    			break;
    			
    			case 2:
    				return "Task";
    			break;
    			
    			case 3:
    				return "Milestone";
    			break;
    		endswitch;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_project_id()
     * @return integer
     */    
    public function get_project_id()
    {
    	if ($this->task)
    	{
    		return $this->task->get_project_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_owner_id()
     * @return integer
     */
    public function get_owner_id()
    {
    	if ($this->task)
    	{
    		return $this->task->get_owner_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_comment()
     * @return string
     */
    public function get_comment()
    {
    	if ($this->task)
    	{
    		return $this->task->get_comment();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_start_date()
     * @return string
     */
    public function get_start_date()
    {
    	if ($this->task)
    	{
    		return $this->task->get_start_date();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_start_time()
     * @return string
     */
    public function get_start_time()
    {
    	if ($this->task)
    	{
    		return $this->task->get_start_time();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_end_date()
     * @return string
     */
    public function get_end_date()
    {
    	if ($this->task)
    	{
    		return $this->task->get_end_date();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_end_time()
     * @return string
     */
    public function get_end_time()
    {
    	if ($this->task)
    	{
    		if ($this->task->get_whole_day())
    		{
    			return "Whole Day";
    		}
    		else
    		{
    			return $this->task->get_end_time();
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_uf_end_time()
     * @return string
     */
    public function get_uf_end_time()
    {
    	if ($this->task)
    	{
    		if ($this->task->get_whole_day())
    		{
    			return -1;
    		}
    		else
    		{
    			return $this->task->get_end_time();
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_auto_connect()
     * @return bool
     */
    public function get_auto_connect()
    {
    	if ($this->task)
    	{
    		return $this->task->get_auto_connect();
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_created_at()
     * @return string
     */
    public function get_created_at()
    {
    	if ($this->task)
    	{
    		return $this->task->get_created_at();
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_name()
     * @return string
     */
    public function get_name()
    {
    	if ($this->task_type and $this->task and $this->task_id)
    	{
    		if ($this->task->get_type_id() == 1)
    		{
    			$project_status = new ProjectStatus($this->task_type->get_end_status_id());
    			
    			if ($this->task_type->get_finalise() == true)
    			{
    				return "Finalise ".$project_status->get_name();
    			}
    			else
    			{
    				return "Achieve ".$project_status->get_name();
    			}
    		}
    		else
    		{
    			return $this->task_type->get_name();
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::get_color()
     * @return string
     */
    public function get_color()
    {
    	if (!isset($this->progress))
    	{
    		$this->calc_progress();
    	}
    	
    	if ($this->task and $this->task_id)
    	{
	    	if ($this->progress < 100)
	    	{
		    	if($this->get_uf_end_time() == -1)
		    	{
		    		$end_datetime_handler = new DatetimeHandler($this->task->get_end_date()." 23:59:59");
		    	}
		    	else
		    	{
		    		$end_datetime_handler = new DatetimeHandler($this->task->get_end_date()." ".$this->task->get_end_date());
		    	}
		    	
		    	$remaining_mktime = $end_datetime_handler->get_mktime() - mktime();
		    	
		    	if ($remaining_mktime < 86400)
		    	{	
					if ($remaining_mktime < 0)
					{
						$color = "FF0000";
					}
					elseif($remaining_mktime < 3600)
					{
						$color = "FF4500";
					}
					elseif($remaining_mktime < 36000)
					{
						$color = "FFA500";
					}
					else
					{
						$color = "FFD700";
					}
				}
				else
				{
					$color = "ADFF2F";
				}
				return $color;
	    	}
	    	else
	    	{
	    		return "ADFF2F";
	    	}
    	}
    	else
    	{
    		return "FFFFFF";
    	}
    }  
    
    /**
     * @see ProjectTaskInterface::get_begin_status_id()
     * @return integer
     */
    public function get_begin_status_id()
    {
    	if ($this->task_type and $this->task and $this->task_id)
    	{	
    		if ($this->task->get_type_id() == 1)
    		{
    			return $this->task_type->get_begin_status_id();
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
     * @see ProjectTaskInterface::get_end_status_id()
     * @return integer
     */
    public function get_end_status_id()
    {
    	if ($this->task_type and $this->task and $this->task_id)
    	{	
    		if ($this->task->get_type_id() == 1)
    		{
    			return $this->task_type->get_end_status_id();
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
     * @see ProjectTaskInterface::get_subtraction_points()
     * @return integer
     */
    public function get_subtraction_points()
    {
    	if ($this->task_type and $this->task and $this->task_id)
    	{	
    		if ($this->task->get_type_id() == 1)
    		{
    			return $this->task_type->get_subtraction_points();
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
     * @see ProjectTaskInterface::get_finalise()
     * @return bool
     */
    public function get_finalise()
    {
    	if ($this->task_type and $this->task and $this->task_id)
    	{	
    		if ($this->task->get_type_id() == 1)
    		{
    			return $this->task_type->get_finalise();
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
 	 * @see ProjectTaskInterface::get_progress()
 	 * @return float
 	 */
    public function get_progress()
    {
    	if (isset($this->progress))
    	{
    		return $this->progress;
    	}
    	else
    	{
    		$this->calc_progress();
    		if (isset($this->progress))
    		{
    			return $this->progress;
    		}
    		else
    		{
    			return null;
    		}
    	}
    }

	/**
	 * @see ProjectTaskInterface::set_name()
	 * @param string $name
	 * @return bool
	 */
    public function set_name($name)
    {
    	if ($this->task and $this->task_type and $name)
    	{
    		if ($this->task->get_type_id() != 1)
    		{
    			return $this->task_type->set_name($name);
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
     * @see ProjectTaskInterface::set_start()
     * @param string $date
     * @param string $time
     * @return bool
     */
    public function set_start($date, $time)
    {
    	global $transaction;
    	
    	if ($date)
    	{
    		$transaction_id = $transaction->begin();
    		
    		if ($this->get_uf_end_time() != -1)
    		{
    			$current_end_datetime_handler = new DatetimeHandler($this->get_end_date()." ".$this->get_uf_end_time());
    		}
    		else
    		{
    			$current_end_datetime_handler = new DatetimeHandler($this->get_end_date()." 23:59:59");
    		}
    		
    		if ($time)
    		{
    			$new_start_datetime_handler = new DatetimeHandler($date." ".$time);
    		}
    		else
    		{
    			$new_start_datetime_handler = new DatetimeHandler($date." 00:00:00");
    		}
    		
    		$new_previous_task_end_datetime_handler = clone $new_start_datetime_handler;
    		
    		if (!$time)
    		{
    			$new_previous_task_end_datetime_handler->sub_day(1);
    		}
    		else
    		{
    			$new_previous_task_end_datetime_handler->sub_second(1);
    		}
    		
    		if ($new_start_datetime_handler->distance($current_end_datetime_handler) > 0)
    		{
    			$previous_project_task_has_previous_task_array = ProjectTaskHasPreviousTask_Access::list_previous_tasks_by_task_id($this->task_id);
  
    			if (is_array($previous_project_task_has_previous_task_array) and count($previous_project_task_has_previous_task_array) >= 1)
    			{
    				foreach($previous_project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task = new ProjectTask($value);
    					
    					if ($project_task->get_start_time() != null)
    					{
			    			$current_previous_task_end_datetime_handler = new DatetimeHandler($project_task->get_start_date()." ".$project_task->get_start_time());
			    		}
			    		else
			    		{
			    			$current_previous_task_end_datetime_handler = new DatetimeHandler($project_task->get_start_date()." 00:00:00");
			    		}
			    		
			    		if ($new_start_datetime_handler->distance($current_previous_task_end_datetime_handler) >= 0)
			    		{
			    			if ($transaction_id != null)
			    			{
								$transaction->rollback($transaction_id);
							}
			    			return false;
			    		}
    				}
    				
    				foreach($previous_project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task_access = new ProjectTask_Access($value);
    					
    					if ($project_task_access->set_end_date($new_previous_task_end_datetime_handler->get_formatted_string("Y-m-d")) == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
							return false;
    					}
    					
    					if (!$time)
    					{
    						if ($project_task_access->set_end_time("23:59:59") == false)
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
    						if ($project_task_access->set_end_time($new_previous_task_end_datetime_handler->get_formatted_string("H:i:s")) == false)
    						{
    							if ($transaction_id != null)
    							{
									$transaction->rollback($transaction_id);
								}
								return false;
    						}
    					}
    				}
    			}
    			
    			if ($this->task->set_start_date($date) == false)
    			{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
    				return false;
    			}
    			
    			if ($time)
    			{
	    			if ($this->task->set_start_time($time) == false)
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
     * @see ProjectTaskInterface::set_end()
     * @param string $date
     * @param string $time
     * @param bool $whole_day
     * @return bool
     */
    public function set_end($date, $time, $whole_day)
    {
    	global $transaction;
    	
    	if ($date and ($time or $whole_day == true))
    	{
    		$transaction_id = $transaction->begin();
    		
    		if ($this->get_start_time() != null)
    		{
    			$current_start_datetime_handler = new DatetimeHandler($this->get_start_date()." ".$this->get_start_time());
    		}
    		else
    		{
    			$current_start_datetime_handler = new DatetimeHandler($this->get_start_date()." 00:00:00");
    		}
    		
    		if ($time and $whole_day == false)
    		{
    			$new_end_datetime_handler = new DatetimeHandler($date." ".$time);
    		}
    		else
    		{
    			$new_end_datetime_handler = new DatetimeHandler($date." 23:59:59");
    		}
    		
    		$new_next_task_start_datetime_handler = clone $new_end_datetime_handler;
    		
    		if ($whole_day == true)
    		{
    			$new_next_task_start_datetime_handler->add_day(1);
    		}
    		else
    		{
    			$new_next_task_start_datetime_handler->add_second(1);
    		}
    		
    		if ($current_start_datetime_handler->distance($new_end_datetime_handler) > 0)
    		{
    			$next_project_task_has_previous_task_array 	= ProjectTaskHasPreviousTask_Access::list_tasks_by_previous_task_id($this->task_id);
    			
    			if (is_array($next_project_task_has_previous_task_array) and count($next_project_task_has_previous_task_array) >= 1)
    			{
    				foreach($next_project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task = new ProjectTask($value);
    					
    					if ($project_task->get_uf_end_time() != -1)
    					{
			    			$current_next_task_end_datetime_handler = new DatetimeHandler($project_task->get_end_date()." ".$project_task->get_uf_end_time());
			    		}
			    		else
			    		{
			    			$current_next_task_end_datetime_handler = new DatetimeHandler($project_task->get_end_date()." 23:59:59");
			    		}
			    		
			    		if ($new_end_datetime_handler->distance($current_next_task_end_datetime_handler) <= 0)
			    		{
			    			if ($transaction_id != null)
			    			{
								$transaction->rollback($transaction_id);
							}
			    			return false;
			    		}	
    				}
    				
    				foreach($next_project_task_has_previous_task_array as $key => $value)
    				{
    					$project_task_access = new ProjectTask_Access($value);
    					
    					if ($project_task_access->set_start_date($new_next_task_start_datetime_handler->get_formatted_string("Y-m-d")) == false)
    					{
    						if ($transaction_id != null)
    						{
								$transaction->rollback($transaction_id);
							}
							return false;
    					}
    					
    					if ($whole_day == true)
    					{
    						if ($project_task_access->set_start_time("00:00:00") == false)
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
    						if ($project_task_access->set_start_time($new_next_task_start_datetime_handler->get_formatted_string("H:i:s")) == false)
    						{
    							if ($transaction_id != null)
    							{
									$transaction->rollback($transaction_id);
								}
								return false;
    						}
    					}
    				}
    			}
    			
    			if ($this->task->set_end_date($date) == false)
    			{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
    				return false;
    			}
    			
    			if ($time)
    			{
	    			if ($this->task->set_end_time($time) == false)
	    			{
	    				if ($transaction_id != null)
	    				{
							$transaction->rollback($transaction_id);
						}
	    				return false;
	    			}
    			}
    			
    			if ($this->task->set_whole_day($whole_day) == false)
    			{
    				if ($transaction_id != null)
    				{
						$transaction->rollback($transaction_id);
					}
    				return false;
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
    		return false;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::set_progress()
     * @param float $progress
     * @return bool
     */
    public function set_progress($progress)
    {
    	if ($this->task and $this->task_type and is_float($progress))
    	{
    		if ($this->task->get_type_id() == 2)
    		{
    			return $this->task_type->set_progress($progress);
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
     * @return bool
     */
    private function get_over_time()
    {
    	if ($this->task_id and $this->task)
    	{
    		return $this->task->get_over_time();
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @param bool $over_time
     * @return bool
     */
    private function set_over_time($over_time)
    {
    	if ($this->task_id and $this->task and isset($over_time))
    	{
    		return $this->task->set_over_time($over_time);
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @param $finished
     * @return bool
     */
    private function set_finished($finished)
    {
    	if ($this->task_id and $this->task and isset($finished))
    	{
    		return $this->task->set_finished($finished);
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * Calculates the progres of the task
     * @return integer
     */
    private function calc_progress()
    {
    	if ($this->task and $this->task_id and $this->task_type)
    	{
    		switch($this->task->get_type_id()):
    			case 1:
    				if ($this->task->get_finished() == false)
    				{
	    				$project_task_point = new ProjectTaskPoint($this->task->get_project_id());
	 
	    				$task_max_points = $project_task_point->get_task_max_points($this->task_id);
	    				$task_achieved_points = $project_task_point->get_task_achieved_points($this->task_id);
	
	    				if ($task_achieved_points > $task_max_points)
	    				{
	    					$task_achieved_points = $task_max_points;
	    				}
	    				
	    				if ($task_achieved_points != 0)
	    				{
	    					$percent = ceil(($task_achieved_points/$task_max_points)*100);
	    					if ($percent < 0 or $percent > 100)
	    					{
	    						$percent = 0;
	    					}
	    				}
	    				else
	    				{
	    					$percent = 0;
	    				}
	    				
	    				$this->progress = $percent;
    				
    					if ($this->progress == 100)
    					{
    						$this->task->set_finished(true);
    					}
    				}
    				else
    				{
    					$this->progress = 100;
    				}
    			break;
    			
    			case 2:
    				if ($this->task_type->get_progress())
    				{
    					$this->progress = $this->task_type->get_progress();
    				}
    				else
    				{
    					$this->progress = 0;
    				}	
    			break;
    			
    			case 3:
    			default:
    				$this->progress = null;
    			break;
    		endswitch; 
    	}
    	else
    	{
    		$this->progress = null;
    	}
    }
    
    
    /**
     * @see ProjectTaskInterface::list_tasks()
     * @param integer $project_id
     * @return array
     */
    public static function list_tasks($project_id)
    {
    	return ProjectTask_Access::list_entries_by_project_id($project_id);
    }
      
    /**
     * @see ProjectTaskInterface::check_over_time_tasks()
     * @param integer $project_id
     * @return bool
     */
    public static function check_over_time_tasks($project_id)
    {
    	if (is_numeric($project_id))
    	{
    		$task_array = ProjectTask_Access::list_over_time_entries_by_project_id($project_id);
    		if (is_array($task_array) and count($task_array) >= 1)
    		{
    			foreach($task_array as $key => $value)
    			{
    				$project_task = new ProjectTask($value);
    				if ($project_task->get_progress() >= 100)
    				{
						if ($project_task->set_finished(true) == false)
						{
							return false;
						}
					}
    			}
    		}
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ProjectTaskInterface::list_upcoming_tasks()
     * @return array
     * @todo tasks ending today
     * @todo tasks ending this week
     * @todo tasks ending next 30 days
     */
    public static function list_upcoming_tasks()
    {
    	global $user;
     	
     	$upcoming_task_array = array();
     	
     	$over_time_task_array = Project_Wrapper_Access::list_not_finished_over_time_project_tasks_by_user_id($user->get_user_id(),  date("Y-m-d H:i:s"));
     	
     	if (is_array($over_time_task_array) and count($over_time_task_array) >= 1)
     	{
     		foreach($over_time_task_array as $key => $value)
     		{	
     			$project_task = new ProjectTask($value);
     			$project = new Project($project_task->get_project_id());
     			
 				if ($project_task->get_uf_end_time() == -1)
 				{
					$end_task_datetime = new DatetimeHandler($project_task->get_end_date()." 23:59:59");
				}
				else
				{
					$end_task_datetime = new DatetimeHandler($project_task->get_end_date()." ".$project_task->get_uf_end_time());
				}
     			
     			if ($project_task->get_over_time() == true)
     			{
     				$temp_array 				= array();
					$temp_array[project_id]		= $project_task->get_project_id();
					$temp_array[project_name]	= $project->get_name();
					$temp_array[task_name]		= $project_task->get_name();
					$temp_array[end_date]		= $end_task_datetime->get_formatted_string("d/m/Y");
					$temp_array[status] 		= 1;
					array_push($upcoming_task_array, $temp_array);
					unset($temp_array);
     			}
     			else
     			{
     				if ($project_task->get_progress() < 100)
     				{
						$temp_array 				= array();
						$temp_array[project_id]		= $project_task->get_project_id();
						$temp_array[project_name]	= $project->get_name();
						$temp_array[task_name]		= $project_task->get_name();
						$temp_array[end_date]		= $end_task_datetime->get_formatted_string("d/m/Y");
						$temp_array[status] 		= 1;
						array_push($upcoming_task_array, $temp_array);
						unset($temp_array);
						$project_task->set_over_time(true);
					}
					else
					{
						$project_task->set_finished(true);
					}
     			}     			
     		}
     	}
     	return $upcoming_task_array;		
    }

    /**
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeletePrecheckEvent)
    	{
    		$project_task_array = ProjectTask_Access::list_entries_by_owner_id($event_object->get_user_id());
			
			if (is_array($project_task_array))
			{
				if (count($project_task_array) >= 1)
				{
					return false;
				}
			}
    	}
    	
    	return true;
    }
}

?>