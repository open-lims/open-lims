<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * User Access Class
 * @package base
 */
class User_Access
{
	const USER_PK_SEQUENCE = 'core_users_id_seq';
	
	private $user_id;
	
	private $username;
	private $password;

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
			$sql = "SELECT * FROM ".constant("USER_TABLE")." WHERE id= :user_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":user_id", $user_id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->user_id 				= $user_id;
				
				$this->username				= $data['username'];
				$this->password				= $data['password'];
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
	
			unset($this->username);
			unset($this->password);
		}
	}
	
	/**
	 * @param integer $username
	 * @param integer $password
	 * @return integer
	 */
	public function create($username, $password)
	{
		global $db;
		
		$datetime = date("Y-m-d H:i:s");
		
		if ($username and strlen($password) == 32)
		{	
			$sql_write = "INSERT INTO ".constant("USER_TABLE")." (id," .
															"username," .
															"password) " .
						"VALUES (nextval('".self::USER_PK_SEQUENCE."'::regclass)," .
															":username," .
															":password)";
																	
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":username", $username, PDO::PARAM_STR);
			$db->bind_value($res_write, ":password", $password, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT id FROM ".constant("USER_TABLE")." WHERE id = currval('".self::USER_PK_SEQUENCE."'::regclass)";
				$res_read = $db->prepare($sql_read);
				$db->execute($res_read);
				$data_read = $db->fetch($res_read);
				
				self::__construct($data_read['id']);
				
				return $data_read['id'];
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

		if ($this->user_id)
		{	
			$id_tmp = $this->user_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("USER_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id_tmp, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res) == 1)
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
	public function get_username()
	{
		if ($this->username)
		{
			return $this->username;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_password()
	{
		if ($this->password)
		{
			return $this->password;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $username
	 * @return bool
	 */	
	public function set_username($username)
	{
		global $db;

		if ($this->user_id and $username)
		{
			$sql = "UPDATE ".constant("USER_TABLE")." SET username = :username WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":username", $username, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->username = $username;
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
	 * @param string $password
	 * @return bool
	 */
	public function set_password($password)
	{
		global $db;
			
		if ($this->user_id and strlen($password) == 32)
		{
			$sql = "UPDATE ".constant("USER_TABLE")." SET password = :password WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":password", $password, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->password = $password;
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
	 * @param string $username
	 * @return integer
	 */	
	public static function get_user_id_by_username($username)
	{
		global $db;
		
		if ($username)
		{						
			$sql = "SELECT id FROM ".constant("USER_TABLE")." WHERE LOWER(username) = :username";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":username", $username, PDO::PARAM_STR);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				return $data['id'];
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
											
		$sql = "SELECT id FROM ".constant("USER_TABLE")." ORDER BY id";
		$res = $db->prepare($sql);
		$db->execute($res);
		while ($data = $db->fetch($res))
		{
			array_push($return_array, $data['id']);
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
	 * @return bool
	 */
	public static function exist_user($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$return_array = array();	
												
			$sql = "SELECT id FROM ".constant("USER_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $user_id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
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
	public static function count_users()
	{
		global $db;
											
		$sql = "SELECT COUNT(id) AS result FROM ".constant("USER_TABLE")."";
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);
		
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
