<?php
/**
 * @package item
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
 * Item Access Class
 * @package item
 */
class Item_Access
{
	const ITEM_PK_SEQUENCE = 'core_items_id_seq';
	
	private $item_id;
	private $datetime;
	
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
			$sql = "SELECT * FROM ".constant("ITEM_TABLE")." WHERE id='".$item_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
			
				$this->item_id		= $item_id;
				$this->datetime		= $data['datetime'];
			
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
			unset($this->datetime);
		}
	}
	
	/**
	 * @return integer
	 */
	public function create()
	{
		global $db;
		
		$datetime = date("Y-m-d H:i:s");
		
		$sql_write = "INSERT INTO ".constant("ITEM_TABLE")." (id,datetime) " .
				"VALUES (nextval('".self::ITEM_PK_SEQUENCE."'::regclass),'".$datetime."')";
		
		$res_write = $db->db_query($sql_write);	
				
		if ($db->db_affected_rows($res_write) == 1)
		{
			$sql_read = "SELECT id FROM ".constant("ITEM_TABLE")." WHERE id = currval('".self::ITEM_PK_SEQUENCE."'::regclass)";
			$res_read = $db->db_query($sql_read);
			$data_read = $db->db_fetch_assoc($res_read);
								
			self::__construct($data_read['id']);
			
			return $data_read['id'];
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
			
			$sql = "DELETE FROM ".constant("ITEM_TABLE")." WHERE id = ".$item_id_tmp."";
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
	 * @return string
	 */
	public function get_datetime()
	{
		if ($this->datetime)
		{
			return $this->datetime;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{	
		global $db;

		if ($this->item_id and $datetime)
		{	
			$sql = "UPDATE ".constant("ITEM_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->item_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->datetime = $datetime;
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
