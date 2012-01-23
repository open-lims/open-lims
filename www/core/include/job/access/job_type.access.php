<?php
/**
 * @package job
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
 * Job Type Access Class
 * @package job
 */
class JobType_Access
{
	const JOB_TYPE_PK_SEQUENCE = 'core_job_types_id_seq';

	private $job_type_id;
	private $name;
	private $internal_name;
	private $binary_id;
	
	/**
	 * @param integer $job_type_id
	 */
	function __construct($job_type_id)
	{
		global $db;

		if ($job_type_id == null)
		{
			$this->job_type_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("JOB_TYPE_TABLE")." WHERE id='".$job_type_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->job_type_id 		= $job_type_id;
				$this->name 			= $data[name];
				$this->internal_name 	= $data[internal_name];
				$this->binary_id 		= $data[binary_id];
			}
			else
			{
				$this->job_type_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->job_type_id)
		{
			unset($this->job_type_id);
			unset($this->name);
			unset($this->internal_name);
			unset($this->binary_id);
		}
	}
	
	/**
	 * @param string $name
	 * @param string $internal_name
	 * @param integer $binary_id
	 * @return integer
	 */
	public function create($name, $internal_name, $binary_id)
	{
		global $db;
		
		if ($name and $internal_name and is_numeric($binary_id))
		{
			$sql_write = "INSERT INTO ".constant("JOB_TYPE_TABLE")." (id,name,internal_name,binary_id) " .
						"VALUES (nextval('".self::JOB_TYPE_PK_SEQUENCE."'::regclass),'".$name."','".$internal_name."',".$binary_id.")";

			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("JOB_TABLE")." WHERE id = currval('".self::JOB_TYPE_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->job_type_id)
		{
			$tmp_job_type_id = $this->job_type_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("JOB_TYPE_TABLE")." WHERE id = ".$tmp_job_type_id."";
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
	 * @return string
	 */
	public function get_internal_name()
	{
		if ($this->internal_name)
		{
			return $this->internal_name;
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;
			
		if ($this->job_type_id and $name)
		{
			$sql = "UPDATE ".constant("JOB_TYPE_TABLE")." SET name = '".$name."' WHERE id = '".$this->job_type_id."'";
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
	
	/**
	 * @param string $internal_name
	 * @return bool
	 */
	public function set_internal_name($internal_name)
	{
		global $db;
			
		if ($this->job_type_id and $internal_name)
		{
			$sql = "UPDATE ".constant("JOB_TYPE_TABLE")." SET internal_name = '".$internal_name."' WHERE id = '".$this->job_type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->internal_name = $internal_name;
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
	 * @param integer $binary_id
	 * @return bool
	 */
	public function set_binary_id($binary_id)
	{
		global $db;
			
		if ($this->job_type_id and is_numeric($binary_id))
		{
			$sql = "UPDATE ".constant("JOB_TYPE_TABLE")." SET binary_id = '".$binary_id."' WHERE id = '".$this->job_type_id."'";
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
}
?>