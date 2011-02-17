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
 * Sample Has Sample Depository Access Class
 * @package sample
 */
class SampleHasSampleDepository_Access
{
	const SAMPLE_HAS_SAMPLE_DEPOSITORY_PK_SEQUENCE = 'core_sample_has_sample_depositories_primary_key_seq';

	private $primary_key;

	private $sample_id;
	private $sample_depository_id;
	private $datetime;
	private $user_id;

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
			$sql = "SELECT * FROM ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				$this->primary_key 			= $primary_key;
				
				$this->sample_id			= $data[sample_id];
				$this->sample_depository_id	= $data[sample_depository_id];
				$this->datetime				= $data[datetime];
				$this->user_id				= $data[user_id];
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
			
			unset($this->sample_id);
			unset($this->sample_depository_id);
			unset($this->datetime);
			unset($this->user_id);
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @param integer $sample_depository_id
	 * @param integer $user_id
	 * @return integer
	 */
	public function create($sample_id, $sample_depository_id, $user_id)
	{
		global $db;
		
		if (is_numeric($sample_id) and is_numeric($sample_depository_id) and is_numeric($user_id))
		{
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." (primary_key,sample_id,sample_depository_id,datetime,user_id) " .
					"VALUES (nextval('".self::SAMPLE_HAS_SAMPLE_DEPOSITORY_PK_SEQUENCE."'::regclass),".$sample_id.",".$sample_depository_id.",'".$datetime."',".$user_id.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." WHERE primary_key = currval('".self::SAMPLE_HAS_SAMPLE_DEPOSITORY_PK_SEQUENCE."'::regclass)";
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
						
			$sql = "DELETE FROM ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
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
	public function get_sample_id()
	{
		if ($this->sample_id)
		{
			return $this->sample_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_sample_depository_id()
	{
		if ($this->sample_depository_id)
		{
			return $this->sample_depository_id;
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
	 * @param integer $sample_id
	 * @return bool
	 */
	public function set_sample_id($sample_id)
	{
		global $db;
			
		if ($this->primary_key and is_numeric($sample_id))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." SET sample_id = '".$sample_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->sample_id = $sample_id;
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
	 * @param integer $sample_depository_id
	 * @return bool
	 */
	public function set_sample_depository_id($sample_depository_id)
	{
		global $db;
		
		if ($this->primary_key and is_numeric($sample_depository_id))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." SET sample_depository_id = '".$sample_depository_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->sample_depository_id = $sample_depository_id;
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
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." SET datetime = '".$datetime."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->datetime = $datetime;
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
		
		if ($this->primary_key and $user_id)
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." SET user_id = '".$user_id."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @return bool
	 */
	public function set_sample_depository_id_on_null()
	{
		global $db;
			
		if ($this->primary_key)
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." SET sample_depository_id = NULL WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->sample_depository_id = null;
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
	 * @param integer $sample_id
	 * @return array
	 */
	public static function list_entries_by_sample_id($sample_id)
	{
		global $db;
			
		if (is_numeric($sample_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." WHERE sample_id = ".$sample_id." ORDER BY datetime ASC";
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
	 * @param integer $sample_depository_id
	 * @return array
	 */
	public static function list_entries_by_sample_depository_id($sample_depository_id)
	{
		global $db;
			
		if (is_numeric($sample_depository_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")." WHERE sample_depository_id = ".$sample_depository_id."";
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
		
		$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_SAMPLE_DEPOSITORY_TABLE")."";
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
