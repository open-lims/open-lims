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
 * Folder Is Sample Folder Access Class
 * @package data
 */
class FolderIsSampleFolder_Access
{
	const FOLDER_IS_SAMPLE_FOLDER_TABLE = 'core_folder_is_sample_folder';
	const FOLDER_IS_SAMPLE_FOLDER_PK_SEQUENCE = 'core_folder_is_sample_folder_primary_key_seq';
	
	private $primary_key;
	
	private $sample_id;
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
			$sql = "SELECT * FROM ".self::FOLDER_IS_SAMPLE_FOLDER_TABLE." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				$this->primary_key 			= $primary_key;
				
				$this->sample_id			= $data[sample_id];
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
		
			unset($this->sample_id);
			unset($this->folder_id);
		}
	}

	/**
	 * @param integer $sample_id
	 * @param integer $folder_id
	 * @return integer
	 */
	public function create($sample_id, $folder_id)
	{
		global $db, $session;

		if ($sample_id and $folder_id)
		{			
			$sql_write = "INSERT INTO ".self::FOLDER_IS_SAMPLE_FOLDER_TABLE." (primary_key, sample_id, folder_id) " .
								"VALUES (nextval('".self::FOLDER_IS_SAMPLE_FOLDER_PK_SEQUENCE."'::regclass), ".$sample_id.",".$folder_id.")";		
				
			$res_write = $db->db_query($sql_write);
				
			if ($db->db_affected_rows($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT primary_key FROM ".self::FOLDER_IS_SAMPLE_FOLDER_TABLE." WHERE primary_key = currval('".self::FOLDER_IS_SAMPLE_FOLDER_PK_SEQUENCE."'::regclass)";
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
	 * @param bool
	 */
	public function delete()
	{
		global $db;

		if ($this->primary_key)
		{
			$primary_key_tmp = $this->primary_key;
			
			$this->__destruct();

			$sql = "DELETE FROM ".self::FOLDER_IS_SAMPLE_FOLDER_TABLE." WHERE primary_key = ".$primary_key_tmp."";
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
	public function get_sample_id()
	{
		if ($this->sample_id)
		{
			return $this->sample_id;
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
	 * @param integer $sample_id
	 * @return bool
	 */
	public function set_sample_id($sample_id)
	{
		global $db;
			
		if ($this->primary_key and is_numeric($sample_id))
		{
			$sql = "UPDATE ".self::FOLDER_IS_SAMPLE_FOLDER_TABLE." SET user_id = ".$sample_id." WHERE primary_key = ".$this->primary_key."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->sample_id = $sample_id;
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
			$sql = "UPDATE ".self::FOLDER_IS_SAMPLE_FOLDER_TABLE." SET folder_id = ".$folder_id." WHERE primary_key = ".$this->primary_key."";
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
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function get_entry_by_sample_id($sample_id)
	{
		global $db;
		
		if ($sample_id)
		{		
			$sql = "SELECT primary_key FROM ".self::FOLDER_IS_SAMPLE_FOLDER_TABLE." WHERE sample_id = ".$sample_id."";
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
			$sql = "SELECT primary_key FROM ".self::FOLDER_IS_SAMPLE_FOLDER_TABLE." WHERE folder_id = ".$folder_id."";
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