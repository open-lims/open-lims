<?php
/**
 * @package method
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
 * Method Is Item Access Class
 * @package method
 */
class MethodIsItem_Access
{
	private $method_id;
	private $item_id;
	
	/**
	 * @param integer $method_id
	 */
	function __construct($method_id)
	{
		global $db;
		
		if ($method_id == null)
		{
			$this->method_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("METHOD_IS_ITEM_TABLE")." WHERE method_id='".$method_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[method_id])
			{
				$this->method_id	= $method_id;
				$this->item_id		= $data[item_id];
			}
			else
			{
				$this->method_id	= null;
				$this->item_id		= null;
			}
		}	
	}
	
	function __destruct()
	{
		unset($this->method_id);
		unset($this->item_id);
	}
	
	/**
	 * @param integer $method_id
	 * @param integer $item_id
	 * @return boolean
	 */
	public function create($method_id, $item_id)
	{
		global $db;
		
		if (is_numeric($method_id) and is_numeric($item_id))
		{	
			$sql_write = "INSERT INTO ".constant("METHOD_IS_ITEM_TABLE")." (method_id,item_id) " .
					"VALUES (".$method_id.",".$item_id.")";
					
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
			
		if ($this->method_id and $this->item_id) {
			
			$sql = "DELETE FROM ".constant("METHOD_IS_ITEM_TABLE")." WHERE method_id = ".$this->method_id." AND item_id = ".$this->item_id."";
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
	public function get_method_id()
	{
		if ($this->method_id)
		{
			return $this->method_id;
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
			$sql = "SELECT method_id FROM ".constant("METHOD_IS_ITEM_TABLE")." WHERE item_id='".$item_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[method_id])
			{
				return $data[method_id];
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
