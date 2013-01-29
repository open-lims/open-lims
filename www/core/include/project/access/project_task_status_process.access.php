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
 * Project Task Status Process Access Class
 * @package project
 */
class ProjectTaskStatusProcess_Access
{
	private $task_id;
	private $begin_status_id;
	private $end_status_id;
	private $finalise;
	private $subtraction_points;
	
	/**
	 * @param integer $task_id
	 */
	function __construct($task_id)
	{
		global $db;

		if ($task_id == null)
		{
			$this->task_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE")." WHERE task_id='".$task_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['task_id'])
			{
				$this->task_id				= $task_id;
				
				$this->begin_status_id		= $data['begin_status_id'];
				$this->end_status_id		= $data['end_status_id'];
				$this->subtraction_points	= $data['subtraction_points'];
				
				if ($data['finalise'] == "t")
				{
					$this->finalise		= true;
				}
				else
				{
					$this->finalise		= false;
				}
			}
			else
			{
				$this->task_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->task_id)
		{
			unset($this->task_id);
			unset($this->begin_status_id);
			unset($this->end_status_id);
			unset($this->subtraction_points);
			unset($this->finalise);
		}
	}
	
	/**
	 * @param integer $task_id
	 * @param integer $begin_status_id
	 * @param integer $end_status_id
	 * @param integer $substraction_points
	 * @param bool $finalise
	 * @return integer
	 */
	public function create($task_id, $begin_status_id, $end_status_id, $subtraction_points, $finalise)
	{
		global $db;
		
		if (is_numeric($task_id) and is_numeric($begin_status_id) and is_numeric($end_status_id) and isset($finalise))
		{
			if ($finalise == true)
			{
				$finalise_insert = "t";
			}
			else
			{
				$finalise_insert = "f";
			}

			if (!$subtraction_points)
			{
				$subtraction_points_insert = "0";
			}
			else
			{
				$subtraction_points_insert = $subtraction_points;
			}
			
			$sql_write = "INSERT INTO ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE")." (task_id, begin_status_id, end_status_id, finalise, subtraction_points) " .
					"VALUES (".$task_id.", ".$begin_status_id.", ".$end_status_id.", '".$finalise_insert."', ".$subtraction_points_insert.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$this->__construct($task_id);
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->task_id)
		{
			$tmp_task_id = $this->task_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE")." WHERE task_id = ".$tmp_task_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res) == 1)
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
	 * @return integer
	 */
	public function get_begin_status_id()
	{
		if ($this->begin_status_id)
		{
			return $this->begin_status_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_end_status_id()
	{
		if ($this->end_status_id)
		{
			return $this->end_status_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_finalise()
	{
		if (isset($this->finalise))
		{
			return $this->finalise;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_subtraction_points()
	{
		if (isset($this->subtraction_points))
		{
			return $this->subtraction_points;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $begin_status_id
	 * @return bool
	 */
	public function set_begin_status_id($begin_status_id)
	{	
		global $db;

		if ($this->task_id and is_numeric($begin_status_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE")." SET begin_status_id = '".$begin_status_id."' WHERE task_id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->begin_status_id = $begin_status_id;
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
	 * @param integer $end_status_id
	 * @return bool
	 */
	public function set_end_status_id($end_status_id)
	{
		global $db;

		if ($this->task_id and is_numeric($end_status_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE")." SET end_status_id = '".$end_status_id."' WHERE task_id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->end_status_id = $end_status_id;
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
	 * @param bool $finalise
	 * @return bool
	 */
	public function set_finalise($finalise)
	{
		global $db;

		if ($this->task_id and isset($finalise))
		{
			if ($finalise == true)
			{
				$finalise_insert = "t";
			}
			else
			{
				$finalise_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE")." SET finalise = '".$finalise_insert."' WHERE task_id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->finalise = $finalise;
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
	 * @param integer $subtraction_points
	 * @return bool
	 */
	public function set_subtraction_points($subtraction_points)
	{	
		global $db;
	
		if ($this->task_id and is_numeric($subtraction_points))
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_STATUS_PROCESS_TABLE")." SET subtraction_points = '".$subtraction_points."' WHERE task_id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->subtraction_points = $subtraction_points;
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

}

?>