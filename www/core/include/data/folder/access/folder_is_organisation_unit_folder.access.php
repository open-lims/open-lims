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
 * Folder Is Organisation Unit Folder Access Class
 * @package data
 */
class FolderIsOrganisationUnitFolder_Access
{
	const FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE = 'core_folder_is_organisation_unit_folder';
	const FOLDER_IS_ORGANISATION_UNIT_FOLDER_PK_SEQUENCE = 'core_folder_is_organisation_unit_folder_primary_key_seq';
	
	private $primary_key;
	
	private $organisation_unit_id;
	private $folder_id;
	
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
			$sql = "SELECT * FROM ".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				$this->primary_key 			= $primary_key;
				
				$this->organisation_unit_id	= $data[organisation_unit_id];
				$this->folder_id			= $data[folder_id];
			}
			else
			{
				$this->primary_key			= null;
			}
		}
	} 

	function __destruct()
	{
		if ($this->folder_id)
		{
			unset($this->primary_key);
		
			unset($this->organisation_unit_id);
			unset($this->folder_id);
		}
	}

	/**
	 * @param integer $organisation_unit_id
	 * @param integer $folder_id
	 * @return integer
	 */
	public function create($organisation_unit_id, $folder_id)
	{
		global $db, $session;

		if ($organisation_unit_id and $folder_id)
		{
			$sql_write = "INSERT INTO ".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE." (primary_key, organisation_unit_id, folder_id) " .
								"VALUES (nextval('".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_PK_SEQUENCE."'::regclass), ".$organisation_unit_id.",".$folder_id.")";		
				
			$res_write = $db->db_query($sql_write);
				
			if ($db->db_affected_rows($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT primary_key FROM ".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE." WHERE primary_key = currval('".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_PK_SEQUENCE."'::regclass)";
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

			$sql = "DELETE FROM ".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE." WHERE primary_key = ".$primary_key_tmp."";
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
	 * @return bool
	 */
	public function set_organisation_unit_id($organisation_unit_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($organisation_unit_id))
		{
			$sql = "UPDATE ".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE." SET organisation_unit_id = ".$organisation_unit_id." WHERE primary_key = ".$this->primary_key."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->organisation_unit_id = $organisation_unit_id;
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
	 * @param integer $folder_id
	 * @return bool
	 */
	public function set_folder_id($folder_id)
	{
		global $db;
		
		if ($this->primary_key and is_numeric($folder_id))
		{
			$sql = "UPDATE ".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE." SET folder_id = ".$folder_id." WHERE primary_key = ".$this->primary_key."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->folder_id = $folder_id;
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
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function get_entry_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
		
		if ($organisation_unit_id)
		{
			$sql = "SELECT primary_key FROM ".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE." WHERE organisation_unit_id = ".$organisation_unit_id."";
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
	 * @param integer $folder_id
	 * @return integer
	 */
	public static function get_entry_by_folder_id($folder_id)
	{
		global $db;
		
		if ($folder_id)
		{
			$sql = "SELECT primary_key FROM ".self::FOLDER_IS_ORGANISATION_UNIT_FOLDER_TABLE." WHERE folder_id = ".$folder_id."";
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

}

?>
