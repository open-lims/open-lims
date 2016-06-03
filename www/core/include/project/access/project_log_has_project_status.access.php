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
 * Project Log Has Project Status Access Class
 * @package project
 */
class ProjectLogHasProjectStatus_Access
{
	const PROJECT_LOG_HAS_PROJECT_STATUS_PK_SEQUENCE = 'core_project_log_has_project_status_primary_key_seq';

	private $primary_key;

	private $log_id;
	private $status_id;

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
			$sql = "SELECT * FROM ".constant("PROJECT_LOG_HAS_PROJECT_STATUS_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->fetch($res);
			
			if ($data['primary_key'])
			{
				$this->primary_key 		= $primary_key;
				
				$this->log_id			= $data['log_id'];
				$this->status_id		= $data['status_id'];
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
			unset($this->log_id);
			unset($this->status_id);
		}
	}
	
	/**
	 * @param integer $log_id
	 * @param integer $status_od
	 * @return integer
	 */
	public function create($log_id, $status_id)
	{
		global $db;
		
		if (is_numeric($log_id) and is_numeric($status_id))
		{
			$sql_write = "INSERT INTO ".constant("PROJECT_LOG_HAS_PROJECT_STATUS_TABLE")." (primary_key,log_id,status_id) " .
					"VALUES (nextval('".self::PROJECT_LOG_HAS_PROJECT_STATUS_PK_SEQUENCE."'::regclass),".$log_id.",".$status_id.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->row_count($res_write) == 1)
			{	
				$sql_read = "SELECT primary_key FROM ".constant("PROJECT_LOG_HAS_PROJECT_STATUS_TABLE")." WHERE primary_key = currval('".self::PROJECT_LOG_HAS_PROJECT_STATUS_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->fetch($res_read);
				
				self::__construct($data_read['primary_key']);
				
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
						
			$sql = "DELETE FROM ".constant("PROJECT_LOG_HAS_PROJECT_STATUS_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res) == 1)
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
	public function get_log_id()
	{
		if ($this->log_id)
		{
			return $this->log_id;
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
	 * @param integer $log_id
	 * @return bool
	 */	
	public function set_log_id($log_id)
	{
		global $db;
	
		if ($this->primary_key and is_numeric($log_id))
		{
			$sql = "UPDATE ".constant("PROJECT_LOG_HAS_PROJECT_STATUS_TABLE")." SET log_id = '".$log_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
			{
				$this->log_id = $log_id;
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
			$sql = "UPDATE ".constant("PROJECT_LOG_HAS_PROJECT_STATUS_TABLE")." SET status_id = '".$status_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
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
	 * @return array
	 */	
	public static function list_entries()
	{
		global $db;
	
		$return_array = array();
		
		$sql = "SELECT primary_key FROM ".constant("PROJECT_LOG_HAS_PROJECT_STATUS_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->fetch($res))
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
	
	/**
	 * @param integer $log_id
	 * @return integer
	 */
	public static function get_entry_by_log_id($log_id)
	{
		global $db;
	
		if (is_numeric($log_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("PROJECT_LOG_HAS_PROJECT_STATUS_TABLE")." WHERE log_id = '".$log_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);

			if ($data['primary_key'])
			{
				return $data['primary_key'];
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
