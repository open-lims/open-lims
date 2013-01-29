<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Project Has Folder Access Class
 * @package project
 */
class ProjectStatusHasFolder_Access
{
	private $project_id;
	private $project_status_id;
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
			$sql = "SELECT * FROM ".constant("PROJECT_STATUS_HAS_FOLDER_TABLE")." WHERE folder_id='".$folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['project_id'])
			{
				$this->project_id			= $data['project_id'];
				$this->project_status_id	= $data['project_status_id'];
				$this->folder_id			= $folder_id;
			}
			else
			{
				$this->project_id			= null;
				$this->project_status_id	= null;
				$this->folder_id			= null;
			}
		}	
	}
	
	function __destruct()
	{
		unset($this->project_id);
		unset($this->project_status_id);
		unset($this->folder_id);
	}
	
	/**
	 * @param integer $project_id
	 * @param integer $folder_id
	 * @return boolean
	 */
	public function create($project_id, $project_status_id, $folder_id)
	{
		global $db;
		
		if (is_numeric($project_id) and is_numeric($project_status_id) and is_numeric($folder_id))
		{	
			$sql_write = "INSERT INTO ".constant("PROJECT_STATUS_HAS_FOLDER_TABLE")." (project_id,project_status_id,folder_id) " .
					"VALUES (".$project_id.",".$project_status_id.",".$folder_id.")";
					
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
			
		if ($this->project_id and $this->folder_id)
		{
			$sql = "DELETE FROM ".constant("PROJECT_STATUS_HAS_FOLDER_TABLE")." WHERE project_id = ".$this->project_id." AND folder_id = ".$this->folder_id."";
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
	public function get_project_id()
	{
		if ($this->project_id)
		{
			return $this->project_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_project_status_id()
	{
		if ($this->project_status_id)
		{
			return $this->project_status_id;
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
	 * @param integer $project_id
	 * @param integer $project_status_id
	 * @return integer
	 */
	public static function get_entry_by_project_id_and_project_status_id($project_id, $project_status_id)
	{
		global $db;
		
		if (is_numeric($project_id) and is_numeric($project_status_id))
		{	
			$sql = "SELECT folder_id FROM ".constant("PROJECT_STATUS_HAS_FOLDER_TABLE")." WHERE project_id='".$project_id."' AND project_status_id='".$project_status_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['folder_id'])
			{
				return $data['folder_id'];
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