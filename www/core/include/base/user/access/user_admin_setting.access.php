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
 * User Admin Setting Access Class
 * @package base
 */
class UserAdminSetting_Access
{
	private $user_id;
	
	private $can_change_password;
	private $must_change_password;
	private $user_locked;
	private $user_inactive;
	private $secure_password;
	private $last_password_change;
	private $block_write;
	private $create_folder;
	
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
			$sql = "SELECT * FROM ".constant("USER_ADMIN_SETTING_TABLE")." WHERE id='".$user_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->user_id 				= $user_id;
				
				$this->last_password_change	= $data['last_password_change'];
				
				if ($data['can_change_password'] == "t")
				{
					$this->can_change_password = true;
				}
				else
				{
					$this->can_change_password = false;
				}
				
				if ($data['must_change_password'] == "t")
				{
					$this->must_change_password = true;
				}
				else
				{
					$this->must_change_password = false;
				}
				
				if ($data['user_locked'] == "t")
				{
					$this->user_locked = true;
				}
				else
				{
					$this->user_locked = false;
				}
				
				if ($data['user_inactive'] == "t")
				{
					$this->user_inactive = true;
				}
				else
				{
					$this->user_inactive = false;
				}
				
				if ($data['secure_password'] == "t")
				{
					$this->secure_password = true;
				}
				else
				{
					$this->secure_password = false;
				}
				
				if ($data['block_write'] == "t")
				{
					$this->block_write = true;
				}
				else
				{
					$this->block_write = false;
				}
				
				if ($data['create_folder'] == "t")
				{
					$this->create_folder = true;
				}
				else
				{
					$this->create_folder = false;
				}
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
			
			unset($this->can_change_password);
			unset($this->must_change_password);
			unset($this->user_locked);
			unset($this->user_inactive);
			unset($this->secure_password);
			unset($this->last_password_change);
			unset($this->block_write);
			unset($this->create_folder);
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
			$sql_write = "INSERT INTO ".constant("USER_ADMIN_SETTING_TABLE")." (id," .
															"can_change_password," .
															"must_change_password," .
															"user_locked," .
															"user_inactive," .
															"secure_password," .
															"last_password_change," .
															"block_write," .
															"create_folder) " .
											"VALUES (".$user_id."," .
															"'f'," .
															"'f'," .
															"'f'," .
															"'f'," .
															"'f'," .
															"'".$datetime."'," .
															"'f'," .
															"'f')";
																	
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

			$sql = "DELETE FROM ".constant("USER_ADMIN_SETTING_TABLE")." WHERE id = ".$user_id_tmp."";
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
	 * @return bool
	 */
	public function get_can_change_password()
	{
		if (isset($this->can_change_password))
		{
			return $this->can_change_password;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_must_change_password()
	{
		if (isset($this->must_change_password))
		{
			return $this->must_change_password;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_user_locked()
	{
		if (isset($this->user_locked))
		{
			return $this->user_locked;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_user_inactive()
	{
		if (isset($this->user_inactive))
		{
			return $this->user_inactive;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_secure_password()
	{
		if (isset($this->secure_password))
		{
			return $this->secure_password;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_last_password_change()
	{
		if ($this->last_password_change)
		{
			return $this->last_password_change;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */	
	public function get_block_write()
	{
		if (isset($this->block_write))
		{
			return $this->block_write;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_create_folder()
	{
		if (isset($this->create_folder))
		{
			return $this->create_folder;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param bool $can_change_password
	 * @return bool
	 */
	public function set_can_change_password($can_change_password)
	{
		global $db;

		if ($this->user_id and isset($can_change_password))
		{
			if ($can_change_password == true)
			{
				$can_change_password_insert = "t";
			}
			else
			{
				$can_change_password_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_ADMIN_SETTING_TABLE")." SET can_change_password = '".$can_change_password_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->can_change_password = $can_change_password;
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
	 * @param bool $must_change_password
	 * @return bool
	 */
	public function set_must_change_password($must_change_password)
	{
		global $db;
		
		if ($this->user_id and isset($must_change_password))
		{
			if ($must_change_password == true)
			{
				$must_change_password_insert = "t";
			}
			else
			{
				$must_change_password_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_ADMIN_SETTING_TABLE")." SET must_change_password = '".$must_change_password_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->must_change_password = $must_change_password;
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
	 * @param bool $user_locked
	 * @return bool
	 */
	public function set_user_locked($user_locked)
	{
		global $db;
			
		if ($this->user_id and isset($user_locked))
		{
			if ($user_locked == true)
			{
				$user_locked_insert = "t";
			}
			else
			{
				$user_locked_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_ADMIN_SETTING_TABLE")." SET user_locked = '".$user_locked_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->user_locked = $user_locked;
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
	 * @param bool $user_inactive
	 * @return bool
	 */
	public function set_user_inactive($user_inactive)
	{
		global $db;
			
		if ($this->user_id and isset($user_inactive))
		{
			if ($user_inactive == true)
			{
				$user_inactive_insert = "t";
			}
			else
			{
				$user_inactive_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_ADMIN_SETTING_TABLE")." SET user_inactive = '".$user_inactive_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->user_inactive = $user_inactive;
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
	 * @param bool $secure_password
	 * @return bool
	 */
	public function set_secure_password($secure_password)
	{
		global $db;
			
		if ($this->user_id and isset($secure_password))
		{
			if ($secure_password == true)
			{
				$secure_password_insert = "t";
			}
			else
			{
				$secure_password_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_ADMIN_SETTING_TABLE")." SET secure_password = '".$secure_password_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->secure_password = $secure_password;
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
	 * @param string $last_password_change
	 * @return bool
	 */
	public function set_last_password_change($last_password_change)
	{
		global $db;
			
		if ($this->user_id and $last_password_change)
		{
			$sql = "UPDATE ".constant("USER_ADMIN_SETTING_TABLE")." SET last_password_change = ".$last_password_change." WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->last_password_change = $last_password_change;
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
	 * @param bool $block_write
	 * @return bool
	 */
	public function set_block_write($block_write)
	{
		global $db;
			
		if ($this->user_id and isset($block_write))
		{
			if ($block_write == true)
			{
				$block_write_insert = "t";
			}
			else
			{
				$block_write_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_ADMIN_SETTING_TABLE")." SET block_write = '".$block_write_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->block_write = $block_write;
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
	 * @param bool $create_folder
	 * @return bool
	 */
	public function set_create_folder($create_folder)
	{
		global $db;
		
		if ($this->user_id and isset($create_folder))
		{
			if ($create_folder == true)
			{
				$create_folder_insert = "t";
			}
			else
			{
				$create_folder_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_ADMIN_SETTING_TABLE")." SET create_folder = '".$create_folder_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->create_folder = $create_folder;
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
	public static function get_number_of_inactive_users()
	{
		global $db;
									
		$sql = "SELECT COUNT(id) AS result FROM ".constant("USER_ADMIN_SETTING_TABLE")." WHERE user_inactive = 't'";
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data['result'])
		{
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public static function get_number_of_locked_users()
	{
		global $db;
									
		$sql = "SELECT COUNT(id) AS result FROM ".constant("USER_ADMIN_SETTING_TABLE")." WHERE user_locked = 't'";
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data['result'])
		{
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
}

?>
