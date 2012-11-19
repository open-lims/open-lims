<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Project Task Milestone Access Class
 * @package project
 */
class ProjectTaskMilestone_Access
{
	private $task_id;
	private $name;
	
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
			$sql = "SELECT * FROM ".constant("PROJECT_TASK_MILESTONE_TABLE")." WHERE task_id='".$task_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['task_id'])
			{
				$this->task_id		= $task_id;
				$this->name			= $data['name'];
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
			unset($this->name);
		}
	}
	
	/**
	 * @param integer $task_id
	 * @param string $name
	 * @return bool
	 */
	public function create($task_id, $name)
	{
		global $db;
		
		if (is_numeric($task_id) and $name)
		{
			$sql_write = "INSERT INTO ".constant("PROJECT_TASK_MILESTONE_TABLE")." (task_id, name) " .
					"VALUES (".$task_id.", '".$name."')";
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
		
		if ($this->task_id)
		{
			$tmp_task_id = $this->task_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("PROJECT_TASK_MILESTONE_TABLE")." WHERE task_id = ".$tmp_task_id."";
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
	 * @return string
	 */
	public function get_name()
	{
		if ($this->name)
		{
			return $this->name;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{	
		global $db;

		if ($this->task_id and $name)
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_MILESTONE_TABLE")." SET name = '".$name."' WHERE task_id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->name = $name;
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