<?php
/**
 * @package manufacturer
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * Manufacturer Access Class
 * @package manufacturer
 */
class Manufacturer_Access
{
	const MANUFACTURER_PK_SEQUENCE = 'core_manufacturers_id_seq';

	private $manufacturer_id;

	private $name;
	private $user_id;
	private $datetime;
	
	/**
	 * @param integer $manufacturer_id
	 */
	function __construct($manufacturer_id)
	{
		global $db;

		if ($manufacturer_id == null)
		{
			$this->manufacturer_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("MANUFACTURER_TABLE")." WHERE id='".$manufacturer_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->manufacturer_id 	= $manufacturer_id;
				
				$this->name				= $data[name];
				$this->user_id			= $data[user_id];
				$this->datetime			= $data[datetime];
			}
			else
			{
				$this->manufacturer_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->manufacturer_id)
		{
			unset($this->manufacturer_id);
			unset($this->name);
			unset($this->user_id);
			unset($this->datetime);
		}
	}
	
	/**
	 * @param string $name
	 * @param integer $user_id
	 * @return integer
	 */
	public function create($name, $user_id)
	{
		global $db;
		
		if ($name and is_numeric($user_id))
		{
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("MANUFACTURER_TABLE")." (id,name,user_id,datetime) " .
					"VALUES (nextval('".self::MANUFACTURER_PK_SEQUENCE."'::regclass),'".$name."',".$user_id.",'".$datetime."')";

			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("MANUFACTURER_TABLE")." WHERE id = currval('".self::MANUFACTURER_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->manufacturer_id)
		{
			$tmp_manufacturer_id = $this->manufacturer_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("MANUFACTURER_TABLE")." WHERE id = ".$tmp_manufacturer_id."";
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;

		if ($this->manufacturer_id and $name)
		{			
			$sql = "UPDATE ".constant("MANUFACTURER_TABLE")." SET name = '".$name."' WHERE id = '".$this->manufacturer_id."'";
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
	 * @param integer $user_id
	 * @return bool
	 */
	public function set_user_id($user_id)
	{
		global $db;

		if ($this->manufacturer_id and is_numeric($user_id))
		{			
			$sql = "UPDATE ".constant("MANUFACTURER_TABLE")." SET user_id = ".$user_id." WHERE id = '".$this->manufacturer_id."'";
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;

		if ($this->manufacturer_id and $datetime)
		{			
			$sql = "UPDATE ".constant("MANUFACTURER_TABLE")." SET datetime = '".$datetime."' WHERE id = '".$this->manufacturer_id."'";
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
	
	
	public static function list_manufacturers($number_of_entries, $start_entry, $start_string)
	{
		global $db;

		if (is_numeric($number_of_entries) and is_numeric($start_entry))
		{
			$end_entry = $start_entriy + $number_of_entries;
						
			if ($start_string)
			{
				$start_string = trim(strtolower($start_string))."%";
				
				$sql = "SELECT id,name FROM ".constant("MANUFACTURER_TABLE")." WHERE TRIM(LOWER(name)) LIKE '".$start_string."' ORDER BY name";
			}
			else
			{
				$sql = "SELECT id,name FROM ".constant("MANUFACTURER_TABLE")." ORDER BY name";
			}
			
			$return_array = array();
			
			$res = $db->db_query($sql);
			
			if (is_numeric($start_entry) and is_numeric($end_entry))
			{
				for ($i = 0; $i<=$end_entry-1; $i++)
				{
					if (($data = $db->db_fetch_assoc($res)) == null)
					{
						break;
					}
					
					if ($i >= $start)
					{
						array_push($return_array, $data);
					}
				}
			}
			else
			{
				while ($data = $db->db_fetch_assoc($res))
				{
					array_push($return_array, $data);
				}
			}
			return $return_array;
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
	public static function exist_name($name)
	{
		global $db;
		
		if ($name)
		{	
			$name = trim(strtolower($name));
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("MANUFACTURER_TABLE")." WHERE TRIM(LOWER(name))='".$name."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
						
			if ($data[id])
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