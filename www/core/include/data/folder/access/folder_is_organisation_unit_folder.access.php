<?php
/**
 * @package data
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
 * Folder Is OrganisationUnit Folder Access Class
 * @package data
 */
class FolderIsOrganisationUnitFolder_Access
{
	private $organisation_unit_id;
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
			$sql = "SELECT * FROM ".constant("FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE")." WHERE folder_id='".$folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[organisation_unit_id])
			{
				$this->organisation_unit_id	= $data[organisation_unit_id];
				$this->folder_id	= $folder_id;
			}
			else
			{
				$this->organisation_unit_id	= null;
				$this->folder_id	= null;
			}
		}	
	}
	
	function __destruct()
	{
		unset($this->organisation_unit_id);
		unset($this->folder_id);
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $folder_id
	 * @return boolean
	 */
	public function create($organisation_unit_id, $folder_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id) and is_numeric($folder_id))
		{	
			$sql_write = "INSERT INTO ".constant("FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE")." (organisation_unit_id,folder_id) " .
					"VALUES (".$organisation_unit_id.",".$folder_id.")";
					
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
			
		if ($this->organisation_unit_id and $this->folder_id)
		{
			$sql = "DELETE FROM ".constant("FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE")." WHERE organisation_unit_id = ".$this->organisation_unit_id." AND folder_id = ".$this->folder_id."";
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
	public function get_organisation_unit_id()
	{
		if ($this->organisation_unit_id)
		{
			return $this->organisation_unit_id;
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
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function get_entry_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{	
			$sql = "SELECT folder_id FROM ".constant("FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE")." WHERE organisation_unit_id='".$organisation_unit_id."'";
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