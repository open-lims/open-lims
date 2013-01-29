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
 * Project Has Project Status Access Class
 * @package project
 */
class ProjectHasProjectStatus_Access
{
	const PROJECT_HAS_PROJECT_STATUS_PK_SEQUENCE = 'core_project_has_project_status_primary_key_seq';

	private $primary_key;

	private $project_id;
	private $status_id;
	private $datetime;

	/**
	 * @param integer $primary_key
	 */
	function __construct($primary_key)
	{
		global $db;

		if ($primary_key == null)
		{
			$this->primary_key = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['primary_key'])
			{
				$this->primary_key 		= $primary_key;
				
				$this->project_id		= $data['project_id'];
				$this->status_id		= $data['status_id'];
				$this->datetime			= $data['datetime'];
			}
			else
			{
				$this->primary_key = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->primary_key)
		{
			unset($this->primary_key);
			
			unset($this->project_id);
			unset($this->status_id);
			unset($this->datetime);
		}
	}
	
	/**
	 * @todo remove second write SQL-Statement while proceeding
	 * @param integer $project_id
	 * @param integer $status_id
	 * @return integer
	 */
	public function create($project_id, $status_id)
	{
		global $db;
		
		if (is_numeric($project_id) and is_numeric($status_id))
		{
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write2 = "UPDATE ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." SET current='f' WHERE project_id='".$project_id."'";
			$res_write2 = $db->db_query($sql_write2);
			
			
			$sql_write = "INSERT INTO ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." (primary_key,project_id,status_id,datetime, current) " .
					"VALUES (nextval('".self::PROJECT_HAS_PROJECT_STATUS_PK_SEQUENCE."'::regclass),".$project_id.",".$status_id.",'".$datetime."', 't')";
			$res_write = $db->db_query($sql_write);

			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE primary_key = currval('".self::PROJECT_HAS_PROJECT_STATUS_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read['primary_key']);
				
				return $data_read['primary_key'];
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
		
		if ($this->primary_key)
		{
			$tmp_primary_key = $this->primary_key;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
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
	public function get_status_id()
	{
		if ($this->status_id)
		{
			return $this->status_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_datetime()
	{
		if ($this->datetime)
		{
			return $this->datetime;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $project_id
	 * @return bool
	 */
	public function set_project_id($project_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($project_id))
		{
			$sql = "UPDATE ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." SET project_id = '".$project_id."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param integer $status_id
	 * @return bool
	 */
	public function set_status_id($status_id)
	{
		global $db;
	
		if ($this->primary_key and is_numeric($status_id))
		{
			$sql = "UPDATE ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." SET status_id = '".$status_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->status_id = $status_id;
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;

		if ($this->primary_key and $datetime)
		{
			$sql = "UPDATE ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." SET datetime = '".$datetime."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->datetime= $datetime;
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
	 * @todo check usage
	 * @param integer $project_id
	 * @return array
	 */	
	public static function list_entries_by_project_id($project_id)
	{
		global $db;
		
		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE project_id = ".$project_id." ORDER BY datetime ASC";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['primary_key']);
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
	public static function list_status_id_by_project_id($project_id)
	{
		global $db;
		
		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT status_id FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE project_id = ".$project_id." ORDER BY datetime ASC";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[status_id]);
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
	public static function list_current_status_id_by_project_id($project_id)
	{
		global $db;
		
		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT status_id FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE project_id = ".$project_id." AND current='t'";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[status_id]);
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
	 * @param integer $status_id
	 * @return array
	 */
	public static function list_entries_by_status_id($status_id)
	{
		global $db;
		
		if (is_numeric($status_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")." WHERE status_id = ".$status_id." ORDER BY datetime ASC";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['primary_key']);
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
		
		$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_PROJECT_STATUS_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data['primary_key']);
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
