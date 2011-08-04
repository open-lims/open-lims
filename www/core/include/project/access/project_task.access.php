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
 * Project Link Access Class
 * @package project
 */
class ProjectTask_Access
{
	const PROJECT_TASK_PK_SEQUENCE = 'core_project_tasks_id_seq';

	private $task_id;

	private $type_id;
	private $project_id;
	private $owner_id;
	private $comment;
	private $start_date;
	private $start_time;
	private $end_date;
	private $end_time;
	private $whole_day;
	private $auto_connect;
	private $finished;
	private $created_at;
	private $finished_at;
	private $over_time;

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
			$sql = "SELECT * FROM ".constant("PROJECT_TASK_TABLE")." WHERE id='".$task_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->task_id				= $task_id;
				
				$this->type_id				= $data[type_id];
				$this->project_id			= $data[project_id];
				$this->owner_id				= $data[owner_id];
				$this->comment				= $data[comment];
				$this->start_date			= $data[start_date];
				$this->start_time			= $data[start_time];
				$this->end_date				= $data[end_date];
				$this->end_time				= $data[end_time];
				$this->created_at			= $data[created_at];
				$this->finished_at			= $data[finished_at];
			
				if ($data[whole_day] == "t")
				{
					$this->whole_day		= true;
				}
				else
				{
					$this->whole_day		= false;
				}
				
				if ($data[auto_connect] == "t")
				{
					$this->auto_connect		= true;
				}
				else
				{
					$this->auto_connect		= false;
				}
				
				if ($data[finished] == "t")
				{
					$this->finished		= true;
				}
				else
				{
					$this->finished		= false;
				}

				if ($data[over_time] == "t")
				{
					$this->over_time	= true;
				}
				else
				{
					$this->over_time	= false;
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

			unset($this->type_id);
			unset($this->project_id);
			unset($this->owner_id);
			unset($this->comment);
			unset($this->start_date);
			unset($this->start_time);
			unset($this->end_date);
			unset($this->end_time);
			unset($this->whole_day);
			unset($this->auto_connect);
			unset($this->finished);
			unset($this->created_at);
			unset($this->finished_at);
			unset($this->over_time);
		}
	}
	
	/**
	 * @param integer $type_id
	 * @param integer $project_id
	 * @param integer $user_id
	 * @param string $comment
	 * @param string $start_date
	 * @param string $start_time
	 * @param string $end_date
	 * @param string $end_time
	 * @param bool $whole_day
	 * @param bool $auto_connect
	 * @return integer
	 */
	public function create($type_id, $project_id, $user_id, $comment, $start_date, $start_time, $end_date, $end_time, $whole_day, $auto_connect)
	{
		global $db, $user;
		
		if (is_numeric($type_id) and 
			is_numeric($project_id) and 
			$end_date and 
			($whole_day == true or $end_time) and 
			is_numeric($user_id))
		{
			
 			if ($whole_day == true)
 			{
				$whole_day_insert = "t";
			}
			else
			{
				$whole_day_insert = "f";
			}
			
			if ($auto_connect == true)
			{
				$auto_connect_insert = "t";
			}
			else
			{
				$auto_connect_insert = "f";
			}

			if (!$start_time)
			{
				$start_time_insert = "NULL";
			}
			else
			{
				$start_time_insert = "'".$start_time."'";
			}

			if (!$start_date)
			{
				$start_date_insert = "NULL";
			}
			else
			{
				$start_date_insert = "'".$start_date."'";
			}

			if (!$end_time)
			{
				$end_time_insert = "NULL";
			}
			else
			{
				$end_time_insert = "'".$end_time."'";
			}

			if (!$comment)
			{
				$comment_insert = "NULL";
			}
			else
			{
				$comment_insert = "'".$comment."'";
			}
			
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("PROJECT_TASK_TABLE")." (id, type_id, project_id, owner_id, comment, start_date, start_time, end_date, end_time, whole_day, auto_connect, finished, created_at, finished_at, over_time) " .
					"VALUES (nextval('".self::PROJECT_TASK_PK_SEQUENCE."'::regclass),".$type_id.",".$project_id.",".$user_id.",".$comment_insert.",".$start_date_insert.",".$start_time_insert.",'".$end_date."',".$end_time_insert.",'".$whole_day_insert."','".$auto_connect_insert."','f','".$datetime."', NULL, 'f')";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PROJECT_TASK_TABLE")." WHERE id = currval('".self::PROJECT_TASK_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read[id]);
				
				return $data_read[id];
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->task_id)
		{
			$tmp_task_id = $this->task_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("PROJECT_TASK_TABLE")." WHERE id = ".$tmp_task_id."";
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
	public function get_type_id()
	{
		if ($this->type_id)
		{
			return $this->type_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_project_id()
	{
		if ($this->project_id)
		{
			return $this->project_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_owner_id()
	{
		if ($this->owner_id)
		{
			return $this->owner_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_comment()
	{
		if ($this->comment)
		{
			return $this->commnet;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return stirng
	 */
	public function get_start_date()
	{
		if ($this->start_date)
		{
			return $this->start_date;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_start_time()
	{
		if ($this->start_time)
		{
			return $this->start_time;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_end_date()
	{
		if ($this->end_date)
		{
			return $this->end_date;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_end_time()
	{
		if ($this->end_time)
		{
			return $this->end_time;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_whole_day()
	{
		if (isset($this->whole_day))
		{
			return $this->whole_day;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_auto_connect()
	{
		if (isset($this->auto_connect))
		{
			return $this->auto_connect;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_finished()
	{
		if (isset($this->finished)) 
		{
			return $this->finished;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_created_at()
	{
		if ($this->created_at)
		{
			return $this->created_at;
		}
		else
		{
			return "2008-01-01 12:00:00";
		}
	}
	
	/**
	 * @return string
	 */
	public function get_finished_at()
	{
		if ($this->finished_at)
		{
			return $this->finished_at;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_over_time()
	{
		if (isset($this->over_time))
		{
			return $this->over_time;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $type_id
	 * @return bool
	 */
	public function set_type_id($type_id)
	{	
		global $db;
		
		if ($this->task_id and is_numeric($type_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET type_id = '".$type_id."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->type_id = $type_id;
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
	 * @param $project_id
	 * @return bool
	 */
	public function set_project_id($project_id)
	{
		global $db;

		if ($this->task_id and is_numeric($project_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET project_id = '".$project_id."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->project_id = $project_id;
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
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id)
	{
		global $db;

		if ($this->task_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET owner_id = '".$owner_id."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->owner_id = $owner_id;
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
	 * @param string $comment
	 * @return bool
	 */
	public function set_comment($comment)
	{
		global $db;

		if ($this->task_id and $comment)
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET comment = '".$comment."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->comment = $comment;
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
	 * @param string $start_date
	 * @return bool
	 */
	public function set_start_date($start_date)
	{
		global $db;
	
		if ($this->task_id and $start_date)
		{			
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET start_date = '".$start_date."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->start_date = $start_date;
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
	 * @param string $start_date
	 * @return bool
	 */
	public function set_start_time($start_time)
	{
		global $db;

		if ($this->task_id and $start_time)
		{		
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET start_time = '".$start_time."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->start_time = $start_time;
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
	 * @param string $end_date
	 * @return bool
	 */
	public function set_end_date($end_date)
	{
		global $db;

		if ($this->task_id and $end_date)
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET end_date = '".$end_date."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->end_date = $end_date;
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
	 * @param string $end_time
	 * @return bool
	 */
	public function set_end_time($end_time)
	{
		global $db;
	
		if ($this->task_id and $end_time)
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET end_time = '".$end_time."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->end_time = $end_time;
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
	 * @param bool $whole_day
	 * @return bool
	 */
	public function set_whole_day($whole_day)
	{
		global $db;
	
		if ($this->task_id and isset($whole_day))
		{
			if ($whole_day == true)
			{
				$whole_day_insert = "t";
			}
			else
			{
				$whole_day_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET whole_day = '".$whole_day_insert."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->whole_day = $whole_day;
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
	 * @param bool $auto_connect
	 * @return bool
	 */
	public function set_auto_connect($auto_connect)
	{
		global $db;

		if ($this->task_id and isset($auto_connect))
		{
			if ($auto_connect == true)
			{
				$auto_connect_insert = "t";
			}
			else
			{
				$auto_connect_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET auto_connect = '".$auto_connect_insert."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->auto_connect = $auto_connect;
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
	 * @param string $finished
	 * @return bool
	 */
	public function set_finished($finished)
	{
		global $db;
			
		if ($this->task_id and isset($finished))
		{
			if ($finished == true)
			{
				$finished_insert = "t";
			}
			else
			{
				$finished_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET finished = '".$finished_insert."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->finished = $finished;
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
	 * @param string $created_at
	 * @return bool
	 */
	public function set_created_at($created_at)
	{
		global $db;

		if ($this->task_id and $created_at)
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET created_at = '".$created_at."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->created_at = $created_at;
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
	 * @param string $finished_ar
	 * @return bool
	 */
	public function set_finished_at($finished_at)
	{	
		global $db;

		if ($this->task_id and $finished_at)
		{
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET finished_at = '".$finished_at."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->finished_at = $finished_at;
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
	 * @param bool $over_time
	 * @return bool
	 */
	public function set_over_time($over_time)
	{	
		global $db;

		if ($this->task_id and isset($over_time))
		{
			if ($over_time == true)
			{
				$over_time_insert = "t";
			}
			else
			{
				$over_time_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PROJECT_TASK_TABLE")." SET over_time = '".$over_time_insert."' WHERE id = '".$this->task_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->over_time = $over_time;
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
	 * @param integer $project_id
	 * @return array
	 */
	public static function list_auto_connected_entries_by_project_id($project_id)
	{
		global $db;
		
		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TASK_TABLE")." WHERE project_id = ".$project_id." AND auto_connect = 't' ORDER BY start_date ASC";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @param integer $project_id
	 * @return array
	 */
	public static function list_entries_by_project_id($project_id)
	{
		global $db;
			
		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TASK_TABLE")." WHERE project_id = ".$project_id." ORDER BY start_date ASC";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @param integer $project_id
	 * @return array
	 */
	public static function list_over_time_entries_by_project_id($project_id)
	{
		global $db;

		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TASK_TABLE")." WHERE project_id = ".$project_id." AND over_time = 't'";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @param integer $owner_id
	 * @return array
	 */
	public static function list_entries_by_owner_id($owner_id)
	{
		global $db;

		if (is_numeric($owner_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TASK_TABLE")." WHERE owner_id = ".$owner_id." ORDER BY start_date ASC";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
				
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("PROJECT_TASK_TABLE")." ORDER BY start_date ASC";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data[id]);
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
	
}
?>
