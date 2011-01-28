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
 * Object Is Item Access Class
 * @package data
 */
class ObjectIsItem_Access
{
	const OBJECT_IS_ITEM_TABLE = 'core_object_is_item';
	
	private $object_id;
	private $item_id;
	
	/**
	 * @param integer $object_id
	 */
	function __construct($object_id)
	{
		global $db;
		
		if ($object_id == null)
		{
			$this->object_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::OBJECT_IS_ITEM_TABLE." WHERE object_id='".$object_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[object_id])
			{
				$this->object_id	= $object_id;
				$this->item_id		= $data[item_id];
			}
			else
			{
				$this->object_id	= null;
				$this->item_id		= null;
			}
		}	
	}
	
	function __destruct()
	{
		unset($this->object_id);
		unset($this->item_id);
	}
	
	/**
	 * @param integer $object_id
	 * @param integer $item_id
	 * @return boolean
	 */
	public function create($object_id, $item_id)
	{
		global $db;
		
		if (is_numeric($object_id) and is_numeric($item_id))
		{	
			$sql_write = "INSERT INTO ".self::OBJECT_IS_ITEM_TABLE." (object_id,item_id) " .
					"VALUES (".$object_id.",".$item_id.")";
					
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
			
		if ($this->object_id and $this->item_id) {
			
			$sql = "DELETE FROM ".self::OBJECT_IS_ITEM_TABLE." WHERE object_id = ".$this->object_id." AND item_id = ".$this->item_id."";
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
	public function get_object_id()
	{
		if ($this->object_id)
		{
			return $this->object_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_item_id()
	{
		if ($this->item_id)
		{
			return $this->item_id;
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_entry_by_item_id($item_id)
	{
		global $db;
		
		if (is_numeric($item_id))
		{	
			$sql = "SELECT object_id FROM ".self::OBJECT_IS_ITEM_TABLE." WHERE item_id='".$item_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[object_id])
			{
				return $data[object_id];
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
