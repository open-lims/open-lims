<?php
/**
 * @package base
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
 * 
 */
require_once("interfaces/system_log.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	
	
	require_once("access/system_log.access.php");
	require_once("access/system_log_type.access.php");
}

/**
 * System Log Management Class
 * @package base
 */
class SystemLog implements SystemLogInterface
{

	public $log_id;
	public $system_log;

	/**
	 * @param integer $log_id
	 */
	function __construct($log_id)
	{
		if ($log_id == null)
		{
			$this->log_id = null;
			$this->system_log = new SystemLog_Access(null);
		}
		else
		{
			$this->log_id = $log_id;
			$this->system_log = new SystemLog_Access($log_id);
		}
	}
	
	function __destruct()
	{
		unset($this->log_id);
		unset($this->system_log);
	}
	
	/**
	 * Creates a new entry
	 * @param integer $user_id
	 * @param integer $type_id
	 * @param integer $content_int
	 * @param string $content_string
	 * @param string $content_errorno
	 * @param string $file
	 * @param integer $line
	 * @param string $link
	 */
	public function create($user_id, $type_id, $content_int, $content_string, $content_errorno, $file, $line, $link)
	{
		global $db;

		if ($this->system_log)
		{
			$content_string = $db->db_escape_string($content_string);
			$log_id = $this->system_log->create($user_id, $type_id, $content_int, $content_string, $content_errorno, $file, $line, $link);
			if ($log_id)
			{
				$this->__construct($log_id);
				return $log_id;
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
	 * @return integer
	 */
	public function get_user_id()
	{
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->get_user_id();
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
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->get_datetime();
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
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->get_ip();
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
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->get_content_int();
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
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->get_content_string();
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
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->get_file();
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
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->get_line();
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
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->get_link();
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
		if ($this->log_id and $this->system_log) {
			return $this->system_log->get_stack_trace();
		}else{
			return null;
		}
	}
	
	/**
	 * @param string $stack_trace
	 * @return bool
	 */
	public function set_stack_trace($stack_trace)
	{
		if ($this->log_id and $this->system_log)
		{
			return $this->system_log->set_stack_trace($stack_trace);
		}
		else
		{
			return false;
		}
	}


	/**
	 * Sets all entries of an user on null (during user-delete)
	 * @param integer $user_id
	 * @return bool
	 */
	public static function set_user_id_on_null($user_id)
	{
		return SystemLog_Access::set_user_id_on_null($user_id);
	}
	
	/**
	 * @return array
	 */
	public static function list_types()
	{
		return SystemLogType_Access::list_entries();
	}
	
	/**
	 * @param integer $id
	 * @return string
	 */
	public static function get_type_name($id)
	{
		if (is_numeric($id))
		{
			$system_log_type = new SystemLogType_Access($id);
			if (($name = $system_log_type->get_name()) != null)
			{
				return $name;
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		return SystemLog_Access::exist_id($id);
	}
	
	/**
	 * @param string $ip
	 * @return bool
	 */
	public static function exist_ip($ip)
	{
		$preg_return = preg_match("#^((25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.){3}(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})$#", $ip);
		if ($preg_return == 1)
		{
			return SystemLog_Access::exist_ip($ip);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Counts all failed logins with $ip
	 * @param string $ip
	 * @return integer
	 */
	public static function count_ip_failed_logins($ip)
	{
		return SystemLog_Access::count_ip_failed_logins($ip);
	}
	
	/**
	 * Counts all successful logins with $ip
	 * @param string $ip
	 * @return integer
	 */
	public static function count_ip_successful_logins($ip)
	{
		return SystemLog_Access::count_ip_successful_logins($ip);
	}
	
	/**
	 * Returns an array with all users who have loged in with $ip
	 * @param string $ip
	 * @return array
	 */
	public static function list_ip_users($ip)
	{
		return SystemLog_Access::list_ip_users($ip);
	}

}
?>