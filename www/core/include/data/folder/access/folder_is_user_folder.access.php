<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * Folder Is User Folder Access Class
 * @package data
 */
class FolderIsUserFolder_Access
{
	private $user_id;
	private $folder_id;
	
	/**
	 * @param integer $folder_id
	 */
	function __construct($folder_id)
	{
		global $db;
		
		if ($folder_id == null)
		{
			$this->folder_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("FOLDER_IS_USER_FOLDER_TABLE")." WHERE folder_id='".$folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[user_id])
			{
				$this->user_id	= $data[user_id];
				$this->folder_id	= $folder_id;
			}
			else
			{
				$this->user_id	= null;
				$this->folder_id	= null;
			}
		}	
	}
	
	function __destruct()
	{
		unset($this->user_id);
		unset($this->folder_id);
	}
	
	/**
	 * @param integer $user_id
	 * @param integer $folder_id
	 * @return boolean
	 */
	public function create($user_id, $folder_id)
	{
		global $db;
		
		if (is_numeric($user_id) and is_numeric($folder_id))
		{	
			$sql_write = "INSERT INTO ".constant("FOLDER_IS_USER_FOLDER_TABLE")." (user_id,folder_id) " .
					"VALUES (".$user_id.",".$folder_id.")";
					
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
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
			
		if ($this->user_id and $this->folder_id)
		{
			$sql = "DELETE FROM ".constant("FOLDER_IS_USER_FOLDER_TABLE")." WHERE user_id = ".$this->user_id." AND folder_id = ".$this->folder_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res) == 1)
			{
				$this->__destruct();
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
	 * @return integer
	 */
	public function get_folder_id()
	{
		if ($this->folder_id)
		{
			return $this->folder_id;
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
	public static function get_entry_by_user_id($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{	
			$sql = "SELECT folder_id FROM ".constant("FOLDER_IS_USER_FOLDER_TABLE")." WHERE user_id='".$user_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[folder_id])
			{
				return $data[folder_id];
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
}
?>