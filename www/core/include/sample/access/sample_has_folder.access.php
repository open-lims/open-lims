<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Sample Has Folder Access Class
 * @package sample
 */
class SampleHasFolder_Access
{
	private $sample_id;
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
			$sql = "SELECT * FROM ".constant("SAMPLE_HAS_FOLDER_TABLE")." WHERE folder_id='".$folder_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['sample_id'])
			{
				$this->sample_id	= $data['sample_id'];
				$this->folder_id	= $folder_id;
			}
			else
			{
				$this->sample_id	= null;
				$this->folder_id	= null;
			}
		}	
	}
	
	function __destruct()
	{
		unset($this->sample_id);
		unset($this->folder_id);
	}
	
	/**
	 * @param integer $sample_id
	 * @param integer $folder_id
	 * @return boolean
	 */
	public function create($sample_id, $folder_id)
	{
		global $db;
		
		if (is_numeric($sample_id) and is_numeric($folder_id))
		{	
			$sql_write = "INSERT INTO ".constant("SAMPLE_HAS_FOLDER_TABLE")." (sample_id,folder_id) " .
					"VALUES (".$sample_id.",".$folder_id.")";
					
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
			
		if ($this->sample_id and $this->folder_id)
		{
			$sql = "DELETE FROM ".constant("SAMPLE_HAS_FOLDER_TABLE")." WHERE sample_id = ".$this->sample_id." AND folder_id = ".$this->folder_id."";
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
	 * @return integer
	 */
	public static function get_entry_by_sample_id($sample_id)
	{
		global $db;
		
		if (is_numeric($sample_id))
		{	
			$sql = "SELECT folder_id FROM ".constant("SAMPLE_HAS_FOLDER_TABLE")." WHERE sample_id='".$sample_id."'";
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
	
	/**
	 * @param integer $folder_id
	 * @return integer
	 */
	public static function get_sample_id_by_folder_id($folder_id)
	{
		global $db;
		
		if (is_numeric($folder_id))
		{
			$sql = "SELECT get_sample_id_by_folder_id(".$folder_id.") AS return_value";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['return_value'])
			{
				return $data['return_value'];
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