<?php
/**
 * @package item
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
 * Item Has Sample Gid Access Class
 * @package item
 */
class ItemHasSampleGid_Access
{
	const ITEM_HAS_SAMPLE_GID_TABLE = 'core_item_has_sample_gid';
	
	private $item_id;
	private $gid;
	
	/**
	 * @param integer $item_id
	 */
	function __construct($item_id)
	{
		global $db;
		
		if ($item_id == null)
		{
			$this->item_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::ITEM_HAS_SAMPLE_GID_TABLE." WHERE item_id='".$item_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[item_id])
			{
				$this->item_id		= $item_id;
				$this->gid			= $data[gid];
			}
			else
			{
				$this->item_id		= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->item_id)
		{
			unset($this->item_id);
			unset($this->gid);
		}
	}
	
	/**
	 * @param integer $item_id
	 * @param integer $gid
	 * @return integer
	 */
	public function create($item_id, $gid)
	{
		global $db;
		
		if (is_numeric($item_id) and is_numeric($gid))
		{	
			$sql_write = "INSERT INTO ".self::ITEM_HAS_SAMPLE_GID_TABLE." (item_id,gid) " .
					"VALUES (".$item_id.",".$gid.")";
					
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT item_id FROM ".self::ITEM_HAS_SAMPLE_GID_TABLE." WHERE item_id = ".$item_id."";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
									
				$this->__construct($data_read[item_id]);
				
				return $data_read[item_id];
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
		
		if ($this->item_id)
		{
			$item_id_tmp = $this->item_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".self::ITEM_HAS_SAMPLE_GID_TABLE." WHERE item_id = ".$item_id_tmp."";
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
	public function get_gid()
	{
		if ($this->gid)
		{
			return $this->gid;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function set_gid($gid)
	{
		global $db;

		if ($this->item_id and is_numeric($gid))
		{
			$sql = "UPDATE ".self::ITEM_HAS_SAMPLE_ID_TABLE." SET gid = ".$gid." WHERE item_id = ".$this->item_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->gid = $gid;
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
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_gid_by_item_id($item_id)
	{
		global $db;
		
		if (is_numeric($item_id))
		{
			$sql = "SELECT gid FROM ".self::ITEM_HAS_SAMPLE_GID_TABLE." WHERE item_id = ".$item_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[gid])
			{
				return $data[gid];	
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
