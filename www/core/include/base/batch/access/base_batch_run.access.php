<?php
/**
 * @package base
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
 * Base Batch Run Access Class
 * @package base
 */
class BaseBatchRun_Access
{
	const BASE_BATCH_RUN_PK_SEQUENCE = 'core_base_batch_runs_id_seq';

	private $batch_id;
	private $binary_id;
	private $status;
	private $create_datetime;
	private $start_datetime;
	private $end_datetime;
	private $last_lifesign;
	private $user_id;
	private $type_id;
	
	/**
	 * @param integer $batch_id
	 */
	function __construct($batch_id)
	{
		global $db;

		if ($batch_id == null)
		{
			$this->batch_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("BASE_BATCH_RUN_TABLE")." WHERE id='".$batch_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->batch_id 		= $batch_id;
				$this->binary_id 		= $data['binary_id'];
				$this->status 			= $data['status'];
				$this->create_datetime 	= $data['create_datetime'];
				$this->start_datetime 	= $data['start_datetime'];
				$this->end_datetime 	= $data['end_datetime'];
				$this->last_lifesign 	= $data['last_lifesign'];
				$this->user_id			= $data['user_id'];
				$this->type_id			= $data['type_id'];
			}
			else
			{
				$this->batch_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->batch_id)
		{
			unset($this->batch_id);
			unset($this->binary_id);
			unset($this->status);
			unset($this->create_datetime);
			unset($this->start_datetime);
			unset($this->end_datetime);
			unset($this->last_lifesign);
			unset($this->user_id);
			unset($this->type_id);
		}
	}
	
	/**
	 * @param string $type
	 * @param integer $binary_id
	 * @return integer
	 */
	public function create($type_id, $binary_id, $user_id)
	{
		global $db;
		
		if ($type_id and is_numeric($binary_id))
		{
			if (is_numeric($user_id))
			{
				$user_id_insert = $user_id;
			}
			else
			{
				$user_id_insert = "NULL";
			}
			
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("BASE_BATCH_RUN_TABLE")." (id,binary_id,status,create_datetime,start_datetime,end_datetime,last_lifesign,user_id,type_id) " .
						"VALUES (nextval('".self::BASE_BATCH_RUN_PK_SEQUENCE."'::regclass),".$binary_id.",0,'".$datetime."',NULL,NULL,NULL,".$user_id_insert.",".$type_id.")";

			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_BATCH_RUN_TABLE")." WHERE id = currval('".self::BASE_BATCH_RUN_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read['id']);
			
				return $data_read['id'];
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
		
		if ($this->batch_id)
		{
			$tmp_batch_id = $this->batch_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("BASE_BATCH_RUN_TABLE")." WHERE id = ".$tmp_batch_id."";
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
	public function get_type()
	{
		if ($this->type)
		{
			return $this->type;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_binary_id()
	{
		if ($this->binary_id)
		{
			return $this->binary_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return status
	 */
	public function get_status()
	{
		if ($this->status)
		{
			return $this->status;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_create_datetime()
	{
		if ($this->create_datetime)
		{
			return $this->create_datetime;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_start_datetime()
	{
		if ($this->start_datetime)
		{
			return $this->start_datetime;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_end_datetime()
	{
		if ($this->end_datetime)
		{
			return $this->end_datetime;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_last_lifesign()
	{
		if ($this->last_lifesign)
		{
			return $this->last_lifesign;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_user_id()
	{
		if ($this->user_id)
		{
			return $this->user_id;
		}
		else
		{
			return null;
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
	 * @param integer $binary_id
	 * @return bool
	 */
	public function set_binary_id($binary_id)
	{
		global $db;
			
		if ($this->batch_id and is_numeric($binary_id))
		{
			$sql = "UPDATE ".constant("BASE_BATCH_RUN_TABLE")." SET binary_id = '".$binary_id."' WHERE id = '".$this->batch_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->binary_id = $binary_id;
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
	 * @param integer $status
	 * @return bool
	 */
	public function set_status($status)
	{
		global $db;
			
		if ($this->batch_id and is_numeric($status))
		{
			$sql = "UPDATE ".constant("BASE_BATCH_RUN_TABLE")." SET status = '".$status."' WHERE id = '".$this->batch_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->status = $status;
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
	 * @param string $create_datetime
	 * @return bool
	 */
	public function set_create_datetime($create_datetime)
	{
		global $db;
			
		if ($this->batch_id and $create_datetime)
		{
			$sql = "UPDATE ".constant("BASE_BATCH_RUN_TABLE")." SET create_datetime = '".$create_datetime."' WHERE id = '".$this->batch_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->create_datetime = $create_datetime;
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
	 * @param string $start_datetime
	 * @return bool
	 */
	public function set_start_datetime($start_datetime)
	{
		global $db;
			
		if ($this->batch_id and $start_datetime)
		{
			$sql = "UPDATE ".constant("BASE_BATCH_RUN_TABLE")." SET start_datetime = '".$start_datetime."' WHERE id = '".$this->batch_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->start_datetime = $start_datetime;
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
	 * @param string $end_datetime
	 * @return bool
	 */
	public function set_end_datetime($end_datetime)
	{
		global $db;
			
		if ($this->batch_id and $end_datetime)
		{
			$sql = "UPDATE ".constant("BASE_BATCH_RUN_TABLE")." SET end_datetime = '".$end_datetime."' WHERE id = '".$this->batch_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->end_datetime = $end_datetime;
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
	 * @param string $last_lifesign
	 * @return bool
	 */
	public function set_last_lifesign($last_lifesign)
	{
		global $db;
			
		if ($this->batch_id and $last_lifesign)
		{
			$sql = "UPDATE ".constant("BASE_BATCH_RUN_TABLE")." SET last_lifesign = '".$last_lifesign."' WHERE id = '".$this->batch_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->last_lifesign = $last_lifesign;
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
	 * @param integer $user_id
	 * @return bool
	 */
	public function set_user_id($user_id)
	{
		global $db;
			
		if ($this->batch_id and is_numeric($user_id))
		{
			$sql = "UPDATE ".constant("BASE_BATCH_RUN_TABLE")." SET user_id = '".$user_id."' WHERE id = '".$this->batch_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->user_id = $user_id;
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
	 * @param integer $type_id
	 * @return bool
	 */
	public function set_type_id($type_id)
	{
		global $db;
			
		if ($this->batch_id and is_numeric($type_id))
		{
			$sql = "UPDATE ".constant("BASE_BATCH_RUN_TABLE")." SET type_id = '".$type_id."' WHERE id = '".$this->batch_id."'";
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
}