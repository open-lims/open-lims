<?php
/**
 * @package item
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Item Has Item Information Access Class
 * @package item
 */
class ItemHasItemInformation_Access
{
	const ITEM_HAS_ITEM_INFORMATION_PK_SEQUENCE = 'core_item_has_item_information_primary_key_seq';

	private $primary_key;
	
	private $item_id;
	private $item_information_id;

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
			$sql = "SELECT * FROM ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				$this->primary_key			= $primary_key;

				$this->item_id				= $data[item_id];
				$this->item_information_id	= $data[item_information_id];			
			}
			else
			{
				$this->primary_key			= null;
			}
		}
    }
    
    function __destruct()
    {
    	if ($this->primary_key)
    	{
			unset($this->primary_key);
			unset($this->item_id);
			unset($this->item_information_id);
		}
    }
    
    /**
     * @param integer $item_id
     * @param integer $item_information_id
     * @return integer
     */
    public function create($item_id, $item_information_id)
    {
    	global $db;
		
		if (is_numeric($item_id) and is_numeric($item_information_id))
		{
			$sql_write = "INSERT INTO ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")." (primary_key,item_id,item_information_id) " .
					"VALUES (nextval('".self::ITEM_HAS_ITEM_INFORMATION_PK_SEQUENCE."'::regclass),".$item_id.",".$item_information_id.")";
					
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")." WHERE primary_key = currval('".self::ITEM_HAS_ITEM_INFORMATION_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
									
				$this->__construct($data_read[primary_key]);
				
				return $data_read[primary_key];
			}
			else{
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

		if ($this->primary_key)
		{
			$primary_key_tmp = $this->primary_key;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")." WHERE primary_key = ".$primary_key_tmp."";
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
     * @return integer
     */
    public function get_item_information_id()
    {
    	if ($this->item_information_id)
    	{
			return $this->item_information_id;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @param integer $item_id
     * @return bool
     */
    public function set_item_id($item_id)
    {
    	global $db;
		
		if ($this->primary_key and is_numeric($item_id))
		{
			$sql = "UPDATE ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")." SET item_id = ".$item_id." WHERE primary_key = ".$this->primary_key."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->item_id = $item_id;
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
     * @param integer $item_information_id
     * @return bool
     */
    public function set_item_information_id($item_information_id)
    {
    	global $db;

		if ($this->primary_key and is_numeric($item_information_id))
		{
			$sql = "UPDATE ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")." SET item_information_id = ".$item_information_id." WHERE primary_key = ".$this->primary_key."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->item_information_id = $item_information_id;
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
     * @return array
     */
    public static function list_entries_by_item_id($item_id)
    {
    	global $db;

		if (is_numeric($item_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")." WHERE item_id = ".$item_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[primary_key]);	
			}
			
			if (is_array($return_array))
			{
				return $return_array;
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
     * @param integer $item_information_id
     * @return array
     */
    public static function list_entries_by_item_information_id($item_information_id)
    {
    	global $db;

		if (is_numeric($item_information_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")." WHERE item_information_id = ".$item_information_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[primary_key]);	
			}
			
			if (is_array($return_array))
			{
				return $return_array;
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
     * @return array
     */
    public static function list_entries()
    {
		global $db;

		$return_array = array();
		
		$sql = "SELECT primary_key FROM ".constant("ITEM_HAS_ITEM_INFORMATION_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data[primary_key]);	
		}
		
		if (is_array($return_array))
		{
			return $return_array;
		}
		else
		{
			return null;
		}
    }
    
}

?>
