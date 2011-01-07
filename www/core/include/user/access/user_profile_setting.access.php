<?php
/**
 * @package user
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
 * User Profile Setting Access Class
 * @package user
 */
class UserProfileSetting_Access
{
	
	const USER_PROFILE_SETTING_TABLE = 'core_user_profile_settings';
	
	private $user_id;
	
	private $language_id;
	private $timezone_id;
	
	/**
	 * @param integer $user_id
	 */
	function __construct($user_id)
	{
		global $db;
		
		if ($user_id == null)
		{
			$this->user_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::USER_PROFILE_SETTING_TABLE." WHERE id='".$user_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->user_id 			= $user_id;
				
				$this->language_id		= $data[language_id];
				$this->timezone_id		= $data[timezone_id];
			}
			else
			{
				$this->user_id			= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->user_id)
		{
			unset($this->user_id);

			unset($this->language_id);
			unset($this->timezone_id);		
		}
	}
	
	/**
	 * @param integer $user_id
	 * @return integer
	 */
	public function create($user_id)
	{
		global $db;
		
		$datetime = date("Y-m-d H:i:s");
		
		if ($user_id)
		{
			$sql_write = "INSERT INTO ".self::USER_PROFILE_SETTING_TABLE." (id," .
															"language_id," .
															"timezone_id) " .
											"VALUES (".$user_id."," .
															"1," .
															"NULL)";
																	
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$this->__construct($user_id);
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
	public function delete()
	{
		global $db;

		if ($this->user_id)
		{
				
			$user_id_tmp = $this->user_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".self::USER_PROFILE_SETTING_TABLE." WHERE id = ".$user_id_tmp."";
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
	public function get_language_id()
	{
		if ($this->language_id)
		{
			return $this->language_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_timezone_id()
	{
		if ($this->timezone_id)
		{
			return $this->timezone_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $language_id
	 * @return bool
	 */
	public function set_language_id($language_id)
	{
		global $db;
			
		if ($this->user_id and is_numeric($language_id))
		{
			$sql = "UPDATE ".self::USER_PROFILE_SETTING_TABLE." SET language_id = ".$language_id." WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->language_id = $language_id;
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
	 * @param integer $timezone_id
	 * @return bool
	 */
	public function set_timezone_id($timezone_id)
	{
		global $db;
			
		if ($this->user_id and is_numeric($timezone_id))
		{
			$sql = "UPDATE ".self::USER_PROFILE_SETTING_TABLE." SET timezone_id = ".$timezone_id." WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->timezone_id = $timezone_id;
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
