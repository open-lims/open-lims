<?php
/**
 * @package data
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
 * Virtual Folder Is Project Access Class
 * @package data
 */
class VirtualFolderIsProject_Access
{	
	private $virtual_folder_id;
	
	/**
	 * @param integer $virutal_folder_id
	 */
	function __construct($virtual_folder_id)
	{
		global $db;
		
		if ($virtual_folder_id == null)
		{
			$this->virtual_folder_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("VIRTUAL_FOLDER_IS_PROJECT_TABLE")." WHERE id='".$virtual_folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->virtual_folder_id	= $virtual_folder_id;			
			}
			else
			{
				$this->virtual_folder_id			= null;
			}
		}
	} 

	function __destruct()
	{
		if ($this->virtual_folder_id)
		{
			unset($this->virtual_folder_id);
		}
	}

	/**
	 * @param integer $virtual_folder_id
	 * @return bool
	 */
	public function create($virtual_folder_id)
	{
		global $db, $session;

		if (is_numeric($virtual_folder_id))
		{
			$datetime = date("Y-m-d H:i:s");
					
			$sql_write = "INSERT INTO ".constant("VIRTUAL_FOLDER_IS_PROJECT_TABLE")." (id) " .
								"VALUES (".$virtual_folder_id.")";		
			
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
			return null;
		}
	}

	/**
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->virtual_folder_id)
		{	
			$virtual_folder_id_tmp = $this->virtual_folder_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("VIRTUAL_FOLDER_IS_PROJECT_TABLE")." WHERE id = ".$virtual_folder_id_tmp."";
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
	 * @param integer $virtual_folder_id
	 * @return bool
	 */
	public static function is_entry($virtual_folder_id)
	{
		global $db;
		
		if (is_numeric($virtual_folder_id))
		{
			$sql = "SELECT * FROM ".constant("VIRTUAL_FOLDER_IS_PROJECT_TABLE")." WHERE id='".$virtual_folder_id."'";
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
		else
		{
			return false;
		}
	}
		
}

?>