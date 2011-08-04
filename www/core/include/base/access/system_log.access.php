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
 * System Log Access Class
 * This access-class has no setter (except stack trace)
 * @package base
 */
class SystemLog_Access
{
	const SYSTEM_LOG_PK_SEQUENCE = 'core_system_log_id_seq';
	
	private $log_id;
	
	private $type_id;
	private $user_id;
	private $datetime;
	private $ip;
	private $content_int;
	private $content_string;
	private $content_errorno;
	private $file;
	private $line;
	private $link;
	private $stack_trace;
	
	/**
	 * @param integer $log_id
	 */
	function __construct($log_id)
	{
		global $db;
		
		if ($log_id == null)
		{
			$this->log_id = null;
		}
		else
		{	
			$sql = "SELECT * FROM ".constant("SYSTEM_LOG_TABLE")." WHERE id = ".$log_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->log_id			= $data[id];
				
				$this->type_id			= $data[type_id];
				$this->user_id			= $data[user_id];
				$this->datetime			= $data[datetime];
				$this->ip				= $data[ip];
				$this->content_int		= $data[content_int];
				$this->content_string	= htmlentities($data[content_string]);
				$this->content_errorno	= $data[content_errorno];
				$this->file				= $data[file];
				$this->line				= $data[line];
				$this->link				= $data[link];
				$this->stack_trace		= $data[stack_trace];
			}
			else
			{
				$this->log_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->log_id)
		{
			unset($this->log_id);
					
			unset($this->type_id);
			unset($this->user_id);
			unset($this->datetime);
			unset($this->ip);
			unset($this->content_int);
			unset($this->content_string);
			unset($this->content_errorno);
			unset($this->file);
			unset($this->line);
			unset($this->link);
		}
	}
	
	/**
	 * @param integer $user_id
	 * @param integer $type_id
	 * @param integer $content_int
	 * @param string $content_string
	 * @param string $content_errorno
	 * @param string $file
	 * @param integer $line
	 * @param string $link
	 * @return integer
	 */
	public function create($user_id, $type_id, $content_int, $content_string, $content_errorno, $file, $line, $link)
	{
		global $db;
		
		if ($type_id)
		{
			$content_string = $db->db_escape_string($content_string);
		
			$datetime = date("Y-m-d H:i:s");
			$ip = $_SERVER['REMOTE_ADDR'];
			
			if ($user_id)
			{
				$user_id_insert = $user_id;
			}
			else
			{
				$user_id_insert = "NULL";
			}
			
			if ($content_int)
			{
				$content_int_insert = $content_int;
			}
			else
			{
				$content_int_insert = "NULL";
			}
			
			if ($line)
			{
				$line_insert = $line;
			}
			else
			{
				$line_insert = "NULL";
			}
			
			if ($link)
			{
				$link_insert = "'".$link."'";
			}
			else
			{
				$link_insert = "NULL";
			}
			
			if ($content_errorno)
			{
				 $content_errorno_insert = "'".$content_errorno."'";
			}
			else
			{
				 $content_errorno_insert = "NULL";
			}
			
			$sql_write = "INSERT INTO ".constant("SYSTEM_LOG_TABLE")." (id,type_id,user_id,datetime,ip,content_int,content_string,content_errorno,file,line,link) " .
							"VALUES (nextval('".self::SYSTEM_LOG_PK_SEQUENCE."'::regclass),".$type_id.",".$user_id_insert.",'".$datetime."','".$ip."',".$content_int_insert.",'".$content_string."',".$content_errorno_insert.",'".$file."',".$line_insert.",".$link_insert.")";
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("SYSTEM_LOG_TABLE")." WHERE id = currval('".self::SYSTEM_LOG_PK_SEQUENCE."'::regclass)";
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
    	
    	if ($this->log_id)
    	{
    		$tmp_log_id = $this->log_id;
    		
    		$this->__destruct();

    		$sql = "DELETE FROM ".constant("SYSTEM_LOG_TABLE")." WHERE id = ".$tmp_log_id."";
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
	public function get_content_int()
	{
		if ($this->content_int)
		{
			return $this->content_int;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_content_string()
	{
		if ($this->content_string)
		{
			return $this->content_string;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_content_errorno()
	{
		if ($this->content_errorno)
		{
			return $this->content_errorno;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_file()
	{
		if ($this->file)
		{
			return $this->file;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return integer
	 */
	public function get_line()
	{
		if ($this->line)
		{
			return $this->line;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_link()
	{
		if ($this->link)
		{
			return $this->link;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_stack_trace()
	{
		if ($this->stack_trace)
		{
			return $this->stack_trace;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @param string $stack_trace
	 * @return bool
	 */
	public function set_stack_trace($stack_trace)
	{
		global $db;

		if ($this->log_id and $stack_trace)
		{
			$sql = "UPDATE ".constant("SYSTEM_LOG_TABLE")." SET stack_trace = '".$stack_trace."' WHERE id = ".$this->log_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->stack_trace = $stack_trace;
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
		
		$sql = "SELECT id FROM ".constant("SYSTEM_LOG_TABLE")." ORDER BY datetime";
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
	
	/**
	 * @param integer $type_id
	 * @return array
	 */
	public function list_entries_by_type_id($type_id)
	{
		global $db;
			
		if (is_numeric($type_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("SYSTEM_LOG_TABLE")." WHERE type_id = ".$type_id." ORDER BY datetime";
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
	 * @param integer $user_id
	 * @return array
	 */
	public static function list_entries_by_user_id($user_id)
	{
		global $db;

		if (is_numeric($user_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("SYSTEM_LOG_TABLE")." WHERE user_id = ".$user_id." ORDER BY datetime";
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
	 * @param integer $user_id
	 * @return bool
	 */
	public static function set_user_id_on_null($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$sql = "UPDATE ".constant("SYSTEM_LOG_TABLE")." SET user_id = NULL WHERE user_id = ".$user_id."";
			$res = $db->db_query($sql);
				
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
		
		if (is_numeric($id))
		{
			$sql = "SELECT id FROM ".constant("SYSTEM_LOG_TABLE")." WHERE id = '".$id."'";
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
	}
	
	/**
	 * @param intger $ip
	 * @return bool
	 */
	public static function exist_ip($ip) {
		global $db;
		
		if ($ip)
		{
			$sql = "SELECT id FROM ".constant("SYSTEM_LOG_TABLE")." WHERE ip = '".$ip."'";
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
	}
	
	/**
	 * @param string $ip
	 * @param string $begin
	 */
	public static function count_ip_failed_logins_with_begin($ip, $begin)
	{
		global $db;
		
		if ($ip and $begin)
		{
			$sql = "SELECT COUNT(id) AS result FROM ".constant("SYSTEM_LOG_TABLE")." WHERE type_id = 1 AND LOWER(content_errorno) = 'login' AND content_int IS NULL AND ip = '".$ip."' AND datetime > '".$begin."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[result])
			{
				return $data[result];
			}
			else
			{
				return null;
			}
		}
	}
	
	/**
	 * @param string $ip
	 * @return integer
	 */
	public static function count_ip_failed_logins($ip)
	{
		global $db;
		
		if ($ip)
		{
			$sql = "SELECT COUNT(id) AS result FROM ".constant("SYSTEM_LOG_TABLE")." WHERE type_id = 1 AND LOWER(content_errorno) = 'login' AND content_int IS NULL AND ip = '".$ip."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[result])
			{
				return $data[result];
			}
			else
			{
				return null;
			}
		}
	}
	
	/**
	 * @param string $ip
	 * @return integer
	 */
	public static function count_ip_successful_logins($ip)
	{
		global $db;
		
		if ($ip)
		{
			$sql = "SELECT COUNT(id) AS result FROM ".constant("SYSTEM_LOG_TABLE")." WHERE type_id = 1 AND LOWER(content_errorno) = 'login' AND content_int = '1' AND ip = '".$ip."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[result])
			{
				return $data[result];
			}
			else
			{
				return null;
			}
		}
	}
	
	/**
	 * @param intger $ip
	 * @return array
	 */
	public static function list_ip_users($ip)
	{
		global $db;
		
		if ($ip)
		{
			$return_array = array();
			
			$sql = "SELECT user_id FROM ".constant("SYSTEM_LOG_TABLE")." WHERE user_id IS NOT NULL AND ip = '".$ip."' GROUP BY user_id";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res)) 
			{
				array_push($return_array, $data[user_id]);
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

}

?>
