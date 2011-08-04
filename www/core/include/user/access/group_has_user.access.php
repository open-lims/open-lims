<?php
/**
 * @package user
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
 * Group Has User Access Class
 * @package user
 */
class GroupHasUser_Access
{
	const GROUP_HAS_USER_PK_SEQUENCE = 'core_group_has_users_primary_key_seq';
	
	private $primary_key;
	
	private $group_id;
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
			$sql = "SELECT * FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
			
				$this->primary_key 		= $primary_key;
				
				$this->group_id			= $data[group_id];
				$this->user_id			= $data[user_id];
	
			}
			else
			{
				$this->primary_key		= null;
			}
		}
	}
	
	function __destruct()
	{	
		if ($this->primary_key)
		{
			unset($this->primary_key);
			unset($this->group_id);
			unset($this->user_id);
		}
	}
	
	/**
	 * @param integer $group_id
	 * @param integer $user_id
	 * @return integer
	 */
	public function create($group_id, $user_id)
	{
		global $db;
		
		if (is_numeric($group_id) and is_numeric($user_id))
		{
			$sql_write = "INSERT INTO ".constant("GROUP_HAS_USER_TABLE")." (primary_key, group_id, user_id) " .
						"VALUES (nextval('".self::GROUP_HAS_USER_PK_SEQUENCE."'::regclass),".$group_id.",".$user_id.")";
																	
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT primary_key FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE primary_key = currval('".self::GROUP_HAS_USER_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read[primary_key]);
				
				return $data_read[primary_key];
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
			$primary_key_tmp = $this->primary_key;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE primary_key = ".$primary_key_tmp."";
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
	public function get_group_id()
	{
		if ($this->group_id)
		{
			return $this->group_id;
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
			return $this->group_id;
		}
	}
	
	/**
	 * @param integer $group_id
	 * @return bool
	 */
	public function set_group_id($group_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($group_id))
		{
			$sql = "UPDATE ".constant("GROUP_HAS_USER_TABLE")." SET group_id = ".$group_id." WHERE primary_key = ".$this->primary_key."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->group_id = $group_id;
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
		
		if ($this->primary_key and is_numeric($user_id))
		{
			$sql = "UPDATE ".constant("GROUP_HAS_USER_TABLE")." SET user_id = ".$user_id." WHERE primary_key = ".$this->primary_key."";
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
	 * @param integer $user_id
	 * @param integer $group_id
	 * @return integer
	 */
	public static function get_pk_by_user_id_and_group_id($user_id, $group_id)
	{
		global $db;
		
		if (is_numeric($user_id) and is_numeric($group_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE user_id = ".$user_id." AND group_id = ".$group_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				return $data[primary_key];
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
	public static function list_groups_by_user_id($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$return_array = array();
			
			$sql = "SELECT group_id FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE user_id = ".$user_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[group_id]);
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
	 * @param integer $group_id
	 * @return array
	 */
	public static function list_users_by_group_id($group_id)
	{
		global $db;
		
		if (is_numeric($group_id))
		{					
			$return_array = array();
			
			$sql = "SELECT user_id FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE group_id = ".$group_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[user_id]);
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
	 * @return integer
	 */
	public static function count_groups_by_user_id($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(group_id) AS result FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE user_id = ".$user_id."";
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
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $group_id
	 * @return integer
	 */
	public static function count_users_by_group_id($group_id)
	{
		global $db;
		
		if (is_numeric($group_id))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(user_id) AS result FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE group_id = ".$group_id."";
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
		else
		{
			return null;
		}
	
	}
	
	/**
	 * @param integer $user_id
	 * @return bool
	 */
	public static function delete_by_user_id($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$return_array = array();
			
			$sql = "DELETE FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE user_id = ".$user_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $group_id
	 * @return bool
	 */
	public static function delete_by_group_id($group_id)
	{
		global $db;
		
		if (is_numeric($group_id))
		{
			$return_array = array();
			
			$sql = "DELETE FROM ".constant("GROUP_HAS_USER_TABLE")." WHERE group_id = ".$group_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			return true;	
		}
		else
		{
			return false;
		}
	}
	
}
?>
