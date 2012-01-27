<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
require_once("interfaces/session.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/session.access.php");
	require_once("access/session_value.access.php");
}

/**
 * Session Management Class
 * @package base
 */
class Session implements SessionInterface
{
	private $session_id;
	
	private $session;
	private $user_id;

	/**
	 * @see SessionInterface::__construct()
	 * @param string $session_id
	 */
    function __construct($session_id)
    {
    	if ($session_id == null)
    	{
    		$this->session_id 		= null;
			$this->session			= new Session_Access(null);
			$this->user_id			= null;
		}
		else
		{		
			$this->session_id 		= $session_id;
			$this->session			= new Session_Access($session_id);
			$this->user_id			= $this->session->get_user_id();
    	}
    }
    
    function __destruct()
    {
    	unset($this->session_id);
    	unset($this->session);
    	unset($this->user_id);
    }
    
    /**
     * @see SessionInterface::create()
     * @param integer $user_id
     * @return string
     */
    public function create($user_id)
    {
    	if (is_numeric($user_id))
    	{
	 		$session_id = md5(uniqid(mt_rand(), true));
	 						
			if ($this->session->create($session_id, $user_id) == false)
			{
				return null;
			}
			else
			{			
				$this->__construct($session_id);
				return $session_id;
			}
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @see SessionInterface::destroy()
     * @return bool
     */
    public function destroy()
    {
    	if ($this->session_id and $this->session)
    	{
    		$session_value_array = SessionValue_Access::list_entries_by_session_id($this->session_id);
    		
    		foreach($session_value_array as $key => $value)
    		{
    			$session_value_access = new SessionValue_Access($value['id']);
    			$session_value_access->delete();
    		}
    		
    		$this->session->delete();
    		return true;	
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see SessionInterface::is_valid()
     * @return bool
     */
    public function is_valid()
    {
    	if ($this->user_id and $this->session)
    	{
			$session_datetime = explode(" ",$this->session->get_datetime());
			$session_date = explode("-",$session_datetime[0]);
			$session_full_time = explode("+", $session_datetime[1]);
			$session_time = explode(":",$session_full_time[0]);
			$session_mktime = mktime((int)$session_time[0],(int)$session_time[1],(int)$session_time[2],(int)$session_date[1],(int)$session_date[2],(int)$session_date[0]);
			
			$current_mktime = mktime();
			
			$max_session_mktime = $session_mktime+constant("MAX_SESSION_PERIOD");
			
			if ($current_mktime > $max_session_mktime)
			{
				$this->destroy();
				return false;
			}
			else
			{
				$datetime = date("Y-m-d H:i:s"); 
				$this->session->set_datetime($datetime);
				return true;
			}	
    	}
    	else
    	{
    		
    		return false;
    	}
    }
    
    /**
     * @see SessionInterface::get_user_id()
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
     * @see SessionInterface::get_session_id()
     * @return integer
     */
    public function get_session_id()
    {
    	if ($this->session_id)
    	{
    		return $this->session_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SessionInterface::read_value()
     * @param string $address
     * @return mixed
     */
    public function read_value($address)
    {
    	if ($address and $this->session_id)
    	{
    		$session_value_id = SessionValue_Access::get_id_by_session_id_and_address($this->session_id,$address);
    		
    		if ($session_value_id)
    		{
    			$session_value_access = new SessionValue_Access($session_value_id);
    			$unserialized_mixed =  unserialize($session_value_access->get_value());
    			return $unserialized_mixed;
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
     * @see SessionInterface::write_value()
     * @param string $address
     * @param mixed $value
     * @param bool $force_overwrite (if false, existing values will not be overwritten)
     * @return bool
     */
    public function write_value($address, $value, $force_overwrite = true)
    {
    	if ($address and isset($value) and $this->session_id)
    	{
    		$session_value_id = SessionValue_Access::get_id_by_session_id_and_address($this->session_id,$address);

			if ($session_value_id)
			{
	    		if ($force_overwrite == false)
	    		{
	    			if ($this->is_value($address) == true)
	    			{
	    				return false;
	    			}
	    		}
	    		$session_value_access = new SessionValue_Access($session_value_id);
	    		return $session_value_access->set_value(serialize($value));
			}
			else
			{
				$session_value_access = new SessionValue_Access($session_value_id);
				return $session_value_access->create($this->session_id,$address, serialize($value));
			}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see SessionInterface::is_value()
     * @param string $address
     * @return bool
     */
    public function is_value($address)
    {
		if ($address and $this->session_id)
		{
    		$session_value_id = SessionValue_Access::get_id_by_session_id_and_address($this->session_id,$address);
    		
    		if ($session_value_id)
    		{
    			return true;
    		}
    		else
    		{
    			return null;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see SessionInterface::delete_value()
     * @param string $address
     * @return bool
     */
	public function delete_value($address)
	{
		if ($address and $this->session_id)
		{
    		$session_value_id = SessionValue_Access::get_id_by_session_id_and_address($this->session_id,$address);

			if ($session_value_id)
			{
				$session_value_access = new SessionValue_Access($session_value_id);
				return $session_value_access->delete();
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
     * @see SessionInterface::check_all()
     * Checks all existing sessions; destroys them, if invalid
     */
    public static function check_all()
    {
		$session_array = Session_Access::list_entries();
		
		if (is_array($session_array) and count($session_array) >= 1)
		{
			foreach($session_array as $key => $value)
			{
				$session_access = new Session_Access($value);
				
				$session_datetime = explode(" ",$session_access->get_datetime());
				$session_date = explode("-",$session_datetime[0]);
				$session_full_time = explode("+", $session_datetime[1]);
				$session_time = explode(":",$session_full_time[0]);
				$session_mktime = mktime($session_time[0],$session_time[1],$session_time[2],$session_date[1],$session_date[2],$session_date[0]);
				
				$current_mktime = mktime();
				
				$max_session_mktime = $session_mktime+constant("MAX_SESSION_PERIOD");
				
				if ($current_mktime > $max_session_mktime)
				{
					$session = new Session($value);
					$session->destroy();
				}	
			}	
		}	
    }
    
    /**
     * @see SessionInterface::list_all_session_values()
     * @param string $session_id
     * @return array
     * Returns an array with all session data
     */
    public static function list_all_session_values($session_id)
    {
    	if ($session_id)
    	{
    		return SessionValue_Access::list_entries_by_session_id($session_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SessionInterface::delete_user_sessions()
     * @param integer $user_id
     * @return bool
     */
    public static function delete_user_sessions($user_id)
    {
    	if (is_numeric($user_id))
    	{
    		$session_array = Session_Access::list_entries_by_user_id($user_id);
    		
    		if (is_array($session_array))
    		{
    			foreach($session_array as $key => $value)
    			{
    				$session = new Session($value);
    				if ($session->destroy() == false)
    				{
    					return false;
    				}
    			}
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