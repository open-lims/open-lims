<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * Sample Has Sample Access Class
 * @package sample
 */
class SampleHasSample_Access
{
	const SAMPLE_HAS_SAMPLE_PK_SEQUENCE = 'core_sample_has_samples_primary_key_seq';

	private $primary_key;

	private $sample_pid;
	private $sample_cid;

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
			$sql = "SELECT * FROM ".constant("SAMPLE_HAS_SAMPLE_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				$this->primary_key 	= $primary_key;
				
				$this->sample_pid	= $data[sample_pid];
				$this->sample_cid	= $data[sample_cid];
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
			
			unset($this->sample_pid);
			unset($this->sample_cid);
		}
	}
	
	/**
	 * @param integer $sample_pid
	 * @param integer $sample_cid
	 * @return integer
	 */
	public function create($sample_pid, $sample_cid)
	{
		global $db;
		
		if (is_numeric($sample_pid) and is_numeric($sample_cid))
		{
			$sql_write = "INSERT INTO ".constant("SAMPLE_HAS_SAMPLE_TABLE")." (primary_key,sample_pid,sample_cid) " .
					"VALUES (nextval('".self::SAMPLE_HAS_SAMPLE_PK_SEQUENCE."'::regclass),".$sample_pid.",".$sample_cid.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_TABLE")." WHERE primary_key = currval('".self::SAMPLE_HAS_SAMPLE_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read[primary_key]);
				
				return $data_read[primary_key];
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
						
			$sql = "DELETE FROM ".constant("SAMPLE_HAS_SAMPLE_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
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
	public function get_sample_pid()
	{
		if ($this->sample_pid)
		{
			return $this->sample_pid;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_sample_cid()
	{
		if ($this->sample_cid)
		{
			return $this->sample_cid;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $sample_pid
	 * @return bool
	 */
	public function set_sample_pid($sample_pid)
	{	
		global $db;

		if ($this->primary_key and is_numeric($sample_pid))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_TABLE")." SET sample_pid = '".$sample_pid."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->sample_pid = $sample_pid;
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
	 * @param integer $sample_cid
	 * @return bool
	 */
	public function set_sample_cid($sample_cid)
	{
		global $db;
			
		if ($this->primary_key and is_numeric($sample_cid))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_TABLE")." SET sample_cid = '".$sample_cid."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->sample_cid = $sample_cid;
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
	public function set_sample_pid_on_null()
	{
		global $db;
			
		if ($this->primary_key)
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_TABLE")." SET sample_pid = NULL WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->sample_pid = null;
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
	public function set_sample_cid_on_null()
	{
		global $db;

		if ($this->primary_key)
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_TABLE")." SET sample_cid = NULL WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->sample_cid = null;
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
	 * @param integer $sample_pid
	 * @param integer $sample_cid
	 * @return array
	 */
	public static function get_entry_by_sample_pid_and_sample_cid($sample_pid, $sample_cid)
	{
		global $db;

		if (is_numeric($sample_pid) and is_numeric($sample_cid))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_TABLE")." WHERE sample_pid = ".$sample_pid." AND sample_cid = ".$sample_cid."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);

			if ($data[primary_key])
			{
				return $data[primary_key];
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
	 * @param integer $sample_pid
	 * @return array
	 */
	public static function list_entries_by_sample_pid($sample_pid)
	{
		global $db;
			
		if (is_numeric($sample_pid))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_TABLE")." WHERE sample_pid = ".$sample_pid."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[primary_key]);
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
	 * @param integer $sample_cid
	 * @return array
	 */
	public static function list_entries_by_sample_cid($sample_cid)
	{
		global $db;
			
		if (is_numeric($sample_cid))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_TABLE")." WHERE sample_cid = ".$sample_cid."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[primary_key]);
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
		
		$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data[primary_key]);
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
