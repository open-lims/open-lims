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
 * Session Access Class
 * @package base
 */
class Session_Access
{
	private $session_id;
	
	private $ip;
	private $user_id;
	private $datetime;
	
	/**
	 * @param string $session_id
	 */
	function __construct($session_id)
	{
		global $db;
		
		if ($session_id == null)
		{
			$this->session_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("SESSION_TABLE")." WHERE session_id='".$session_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[session_id])
			{
				$this->session_id 	= $session_id;

				$this->ip			= $data[ip];
				$this->user_id		= $data[user_id];
				$this->datetime		= $data[datetime];

			}
			else
			{
				$this->session_id	= null;
			}				
		}
	}
	
	function __destruct()
	{
		if ($this->session_id)
		{
			unset($this->session_id);
			unset($this->ip);
			unset($this->user_id);
			unset($this->datetime);
			unset($this->values);
		}
	}
	
	/**
	 * @param string $session_id
	 * @param integer $user_id
	 * @return bool
	 */
	public function create($session_id, $user_id)
	{
		global $db;

		if ($session_id and is_numeric($user_id))
		{
			$datetime = date("Y-m-d H:i:s");
			$ip = $_SERVER['REMOTE_ADDR'];
			
	 		$sql_write = "INSERT INTO ".constant("SESSION_TABLE")." (session_id, ip, user_id, datetime) " .
								"VALUES ('".$session_id."','".$ip."',".$user_id.",'".$datetime."')";		
				
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) != 1)
			{
				return false;
			}
			else
			{
				return true;
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

		if ($this->session_id)
		{
			$session_id_tmp = $this->session_id;
			$this->__destruct();

			$sql = "DELETE FROM ".constant("SESSION_TABLE")." WHERE session_id = '".$session_id_tmp."'";
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
	public function get_ip()
	{
		if ($this->ip)
		{
			return $this->ip;
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;

		if ($this->session_id and $datetime)
		{
			$sql = "UPDATE ".constant("SESSION_TABLE")." SET datetime = '".$datetime."' WHERE session_id = '".$this->session_id."'";
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
	 * @return array
	 */
	public static function list_entries_by_user_id($user_id)
	{
		global $db;

		if ($user_id)
		{
			$return_array = array();
			
			$sql = "SELECT session_id FROM ".constant("SESSION_TABLE")." WHERE user_id = ".$user_id."";
			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[session_id]);
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
		
		$sql = "SELECT session_id FROM ".constant("SESSION_TABLE")." ORDER BY user_id ASC";
		$res = $db->db_query($sql);
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data[session_id]);
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
