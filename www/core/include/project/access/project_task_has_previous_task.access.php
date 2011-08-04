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
 * Project Has Previous Task Access Class
 * @package project
 */
class ProjectTaskHasPreviousTask_Access
{
	private $task_id;
	private $previous_task_id;
	
	/**
	 * @param integer $task_id
	 * @param integer $previous_task_id
	 */
	function __construct($task_id, $previous_task_id)
	{
		global $db;

		if ($task_id == null)
		{
			$this->task_id = null;
			$this->previous_task_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PROJECT_TASK_HAS_PREVIOUS_TASK_TABLE")." WHERE task_id='".$task_id."' AND previous_task_id='".$previous_task_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[task_id])
			{
				$this->task_id				= $task_id;
				$this->previous_task_id		= $previous_task_id;
			}
			else
			{
				$this->task_id = null;
				$this->previous_task_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->task_id and $this->previous_task_id)
		{
			unset($this->task_id);
			unset($this->previous_task_id);
		}
	}
	
	/**
	 * @param integer $task_id
	 * @param integer $previous_task_id
	 * @return bool
	 */
	public function create($task_id, $previous_task_id)
	{
		global $db;
		
		if (is_numeric($task_id) and is_numeric($previous_task_id))
		{
			$sql_write = "INSERT INTO ".constant("PROJECT_TASK_HAS_PREVIOUS_TASK_TABLE")." (task_id, previous_task_id) " .
					"VALUES (".$task_id.", ".$previous_task_id.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->task_id and $this->previous_task_id)
		{
			$tmp_task_id = $this->task_id;
			$tmp_previous_task_id = $this->previous_task_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("PROJECT_TASK_HAS_PREVIOUS_TASK_TABLE")." WHERE task_id = ".$tmp_task_id." AND previous_task_id = ".$tmp_previous_task_id."";
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
	 * @param integer $task_id
	 * @return bool
	 */
	public function set_task_id($task_id)
	{
		global $db;

		if ($this->task_id and is_numeric($task_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_HAS_PREVIOUS_TASK_TABLE")." SET task_id = '".$task_id."' WHERE task_id = ".$this->task_id." AND previous_task_id = ".$this->previous_task_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->task_id = $task_id;
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
	 * @param integer $previous_task_id
	 * @return bool
	 */
	public function set_previous_task_id($previous_task_id)
	{
		global $db;

		if ($this->task_id and is_numeric($previous_task_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_HAS_PREVIOUS_TASK_TABLE")." SET previous_task_id = '".$previous_task_id."' WHERE task_id = ".$this->task_id." AND previous_task_id = ".$this->previous_task_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->previous_task_id = $previous_task_id;
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
	 * @param integer $task_id
	 * @return array
	 */
	public static function list_previous_tasks_by_task_id($task_id)
	{
		global $db;

		if (is_numeric($task_id))
		{
			$return_array = array();
			
			$sql = "SELECT previous_task_id FROM ".constant("PROJECT_TASK_HAS_PREVIOUS_TASK_TABLE")." WHERE task_id = ".$task_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[previous_task_id]);
			}
			
			if (is_array($return_array))
			{
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
	 * @param integer $previous_task_id
	 * @return array
	 */
	public static function list_tasks_by_previous_task_id($previous_task_id)
	{
		global $db;

		if (is_numeric($previous_task_id))
		{
			$return_array = array();
			
			$sql = "SELECT task_id FROM ".constant("PROJECT_TASK_HAS_PREVIOUS_TASK_TABLE")." WHERE previous_task_id = ".$previous_task_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[task_id]);
			}
			
			if (is_array($return_array))
			{
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
	 * @param integer $task_id
	 * @param integer $previous_task_id
	 * @return bool
	 */
	public static function exist_entry($task_id, $previous_task_id)
	{
		global $db;

		if (is_numeric($task_id) and is_numeric($previous_task_id))
		{
			$sql = "SELECT * FROM ".constant("PROJECT_TASK_HAS_PREVIOUS_TASK_TABLE")." WHERE task_id='".$task_id."' AND previous_task_id='".$previous_task_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[task_id])
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
	
}

?>
