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
 * Data Entity Is Item Access Class
 * @package data
 */
class DataEntityIsItem_Access
{
	private $data_entity_id;
	private $item_id;
	
	/**
	 * @param integer $data_entity_id
	 */
	function __construct($data_entity_id)
	{
		global $db;
		
		if ($data_entity_id == null)
		{
			$this->data_entity_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("DATA_ENTITY_IS_ITEM_TABLE")." WHERE data_entity_id='".$data_entity_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['data_entity_id'])
			{
				$this->data_entity_id	= $data_entity_id;
				$this->item_id		= $data['item_id'];
			}
			else
			{
				$this->data_entity_id	= null;
				$this->item_id		= null;
			}
		}	
	}
	
	function __destruct()
	{
		unset($this->data_entity_id);
		unset($this->item_id);
	}
	
	/**
	 * @param integer $data_entity_id
	 * @param integer $item_id
	 * @return boolean
	 */
	public function create($data_entity_id, $item_id)
	{
		global $db;
		
		if (is_numeric($data_entity_id) and is_numeric($item_id))
		{	
			$sql_write = "INSERT INTO ".constant("DATA_ENTITY_IS_ITEM_TABLE")." (data_entity_id,item_id) " .
					"VALUES (".$data_entity_id.",".$item_id.")";
					
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
			
		if ($this->data_entity_id and $this->item_id) {
			
			$sql = "DELETE FROM ".constant("DATA_ENTITY_IS_ITEM_TABLE")." WHERE data_entity_id = ".$this->data_entity_id." AND item_id = ".$this->item_id."";
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
	public function get_data_entity_id()
	{
		if ($this->data_entity_id)
		{
			return $this->data_entity_id;
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
			$sql = "SELECT data_entity_id FROM ".constant("DATA_ENTITY_IS_ITEM_TABLE")." WHERE item_id='".$item_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['data_entity_id'])
			{
				return $data['data_entity_id'];
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
