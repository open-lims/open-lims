<?php
/**
 * @package base
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
 * Timezone Access Class
 * @package base
 */
class Timezone_Access
{
	const TIMEZONE_PK_SEQUENCE = 'core_timezones_id_seq';
	
	private $timezone_id;
	
	private $title;
	private $php_title;
	private $deviation;
	
	/**
	 * @param integer $timezone_id
	 */
	function __construct($timezone_id)
	{
		global $db;
		
		if ($timezone_id == null)
		{
			$this->timezone_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("TIMEZONE_TABLE")." WHERE id='".$timezone_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->timezone_id		= $timezone_id;
			
				$this->title 			= $data[title];
				$this->php_title		= $data[php_title];
				$this->deviation		= $data[deviation];
			}
			else
			{
				$this->timezone_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->timezone_id)
		{
			unset($this->timezone_id);	
			unset($this->title);
			unset($this->php_title);
			unset($this->deviation);
		}
	}
	
	/**
	 * @param string $title
	 * @param string $php_title
	 * @param double $deviation
	 * @return integer
	 */
	public function create($title, $php_title, $deviation)
	{
		global $db;
		
		if ($title and $php_title and is_numeric($deviation))
		{
	 		$sql_write = "INSERT INTO ".constant("TIMEZONE_TABLE")." (id, title, php_title, deviation) " .
								"VALUES (nextval('".self::TIMEZONE_PK_SEQUENCE."'::regclass),'".$title."','".$php_title."','".$deviation."')";		
				
			$res_write = $db->db_query($sql_write);
		
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("TIMEZONE_TABLE")." WHERE id = currval('".self::TIMEZONE_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("TIMEZONE_TABLE")." WHERE id = '".$id_tmp."'";
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
	public function get_title()
	{
		if ($this->title)
		{
			return $this->title;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_php_title()
	{
		if ($this->php_title)
		{
			return $this->php_title;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return double
	 */
	public function get_deviation()
	{
		if ($this->deviation)
		{
			return $this->deviation;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $title
	 * @return bool
	 */
	public function set_title($title)
	{
		global $db;

		if ($this->timezone_id and $title)
		{
			$sql = "UPDATE ".constant("TIMEZONE_TABLE")." SET title = '".$title."' WHERE id = ".$this->timezone_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->title = $title;
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
	 * @param string $php_title
	 * @return bool
	 */
	public function set_php_title($php_title)
	{
		global $db;

		if ($this->timezone_id and $php_title)
		{
			$sql = "UPDATE ".constant("TIMEZONE_TABLE")." SET php_title = '".$php_title."' WHERE id = ".$this->timezone_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->php_title = $php_title;
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
	 * @param double $deviation
	 * @return bool
	 */
	public function set_deviation($deviation)
	{
		global $db;

		if ($this->timezone_id and $deviation)
		{
			$sql = "UPDATE ".constant("TIMEZONE_TABLE")." SET deviation = '".$deviation."' WHERE id = ".$this->timezone_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->deviation = $deviation;
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
		
		$sql = "SELECT id FROM ".constant("TIMEZONE_TABLE")." ORDER BY deviation, title";
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
