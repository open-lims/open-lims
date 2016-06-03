<?php
/**
 * @package data
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
 * Data User Data Access Class
 * @package data
 */
class DataUserData_Access
{
	private $user_id;
	
	private $quota;
	private $filesize;
	
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
			$sql = "SELECT * FROM ".constant("DATA_USER_DATA_TABLE")." WHERE user_id= :user_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":user_id", $user_id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['user_id'])
			{
				$this->user_id	= $user_id;
				$this->quota	= $data['quota'];
				$this->filesize	= $data['filesize'];
			}
			else
			{
				$this->user_id 	= null;
			}
		}
	}
	
	function __destruct()
	{
		if (isset($this->id))
		{
			unset($this->user_id);
			unset($this->quota);
			unset($this->filesize);
		}	
	}
	
	/**
	 * @param integer $user_id
	 * @param integer $quota
	 * @return bool
	 */
	public function create($user_id, $quota)
	{
		global $db;
		
		if (is_numeric($user_id) and is_numeric($quota))
		{
			$sql_write = "INSERT INTO ".constant("DATA_USER_DATA_TABLE")." (user_id,quota,filesize) " .
					"VALUES (:user_id, :quota, 0)";
					
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":user_id", $user_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":quota", $quota, PDO::PARAM_INT);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
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
	public function delete()
	{
		global $db;
	
		if ($this->user_id)
		{
			$id_tmp = $this->user_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("DATA_USER_DATA_TABLE")." WHERE user_id = :id";
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
	 * @return integer
	 */
	public function get_quota()
	{
		if ($this->quota)
		{
			return $this->quota;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_filesize()
	{
		if ($this->filesize)
		{
			return $this->filesize;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $quota
	 * @return bool
	 */
	public function set_quota($quota)
	{
		global $db;
			
		if ($this->user_id and is_numeric($quota))
		{
			$sql = "UPDATE ".constant("DATA_USER_DATA_TABLE")." SET quota = :quota WHERE user_id = :user_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":user_id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":quota", $quota, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->quota = $quota;
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
	 * @param integer $filesize
	 * @return bool
	 */
	public function set_filesize($filesize)
	{
		global $db;
			
		if ($this->user_id and is_numeric($filesize))
		{
			$sql = "UPDATE ".constant("DATA_USER_DATA_TABLE")." SET filesize = :filesize WHERE user_id = :user_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":user_id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":filesize", $filesize, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->filesize = $filesize;
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
	public static function get_used_space()
   	{
   		global $db;
		
		$sql = "SELECT SUM(filesize) AS size FROM ".constant("DATA_USER_DATA_TABLE")."";
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);
		
		if ($data['size'])
		{
			return $data['size'];
		}
		else
		{
			return null;
		}
   	}
}
?>