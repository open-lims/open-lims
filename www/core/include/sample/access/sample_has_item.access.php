<?php
/**
 * @package sample
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
 * Sample Has Item Access Class
 * @package sample
 */
class SampleHasItem_Access
{
	const SAMPLE_HAS_ITEM_PK_SEQUENCE = 'core_sample_has_items_primary_key_seq';

	private $primary_key;

	private $sample_id;
	private $item_id;
	private $gid;
	private $parent;
	private $parent_item_id;

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
			$sql = "SELECT * FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				$this->primary_key 		= $primary_key;
				
				$this->sample_id		= $data[sample_id];
				$this->item_id			= $data[item_id];
				$this->gid				= $data[gid];
				$this->parent_item_id	= $data[parent_item_id];
				
				if ($data['parent'] == 't')
				{
					$this->parent = true;
				}
				else
				{
					$this->parent = false;
				}
			}
			else
			{
				$this->primary_key = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->primary_key)
		{
			unset($this->primary_key);
			
			unset($this->sample_id);
			unset($this->item_id);
			unset($this->gid);
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @param integer $item_id
	 * @param integer $gid
	 * @return integer
	 */
	public function create($sample_id, $item_id, $gid)
	{
		global $db;
		
		if (is_numeric($sample_id) and is_numeric($item_id))
		{
			if (is_numeric($gid))
			{
				$gid_insert = $gid;	
			}
			else
			{
				$gid_insert = "NULL";
			}
			
			$sql_write = "INSERT INTO ".constant("SAMPLE_HAS_ITEM_TABLE")." (primary_key,sample_id,item_id,gid,parent,parent_item_id) " .
					"VALUES (nextval('".self::SAMPLE_HAS_ITEM_PK_SEQUENCE."'::regclass),".$sample_id.",".$item_id.",".$gid_insert.",'f',NULL)";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE primary_key = currval('".self::SAMPLE_HAS_ITEM_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read[primary_key]);
				
				return $data_read[primary_key];
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
		
		if ($this->primary_key)
		{
			$tmp_primary_key = $this->primary_key;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
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
	 * @return bool
	 */
	public function get_parent()
	{
		if (isset($this->parent))
		{
			return $this->parent;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_parent_item_id()
	{
		if (isset($this->parent_item_id))
		{
			return $this->parent_item_id;
		}
		else
		{
			return false;
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
			$sql = "UPDATE ".constant("SAMPLE_HAS_ITEM_TABLE")." SET sample_id = '".$sample_id."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param integer $item_id
	 * @return bool
	 */
	public function set_item_id($item_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($item_id))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_ITEM_TABLE")." SET item_id = '".$item_id."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param integer $gid
	 * @return bool
	 */
	public function set_gid($gid)
	{
		global $db;

		if ($this->primary_key and is_numeric($gid))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_ITEM_TABLE")." SET gid = '".$gid."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param bool $parent
	 * @return bool
	 */
	public function set_parent($parent)
	{
		global $db;

		if ($this->primary_key and isset($parent))
		{
			if ($parent == true)
			{
				$parent_insert = "t";
			}
			else
			{
				$parent_insert = "f";
			}
			
			$sql = "UPDATE ".constant("SAMPLE_HAS_ITEM_TABLE")." SET parent = '".$parent_insert."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->parent = $parent;
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
	 * @param integer $gid
	 * @return bool
	 */
	public function set_parent_item_id($parent_item_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($parent_item_id))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_ITEM_TABLE")." SET parent_item_id = '".$parent_item_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->parent_item_id = $parent_item_id;
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
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function get_entry_by_item_id_and_sample_id($item_id, $sample_id)
	{
		global $db;
			
		if (is_numeric($item_id) and is_numeric($sample_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id." AND sample_id = ".$sample_id." AND parent_item_id IS NULL";
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
	 * List ths gids of given items of sample
	 * Ignores parent entries
	 * @param integer $item_id
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function get_gid_by_item_id_and_sample_id($item_id, $sample_id)
	{
		global $db;
			
		if (is_numeric($item_id) and is_numeric($sample_id))
		{
			$return_array = array();
			
			$sql = "SELECT gid FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id." AND sample_id = ".$sample_id." AND parent='f' AND parent_item_id IS NULL";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
				
			if (is_numeric($data[gid]))
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
	
	/**
	 * @param integer $parent_item_id
	 * @param integer $sample_id
	 * @return array
	 */
	public static function list_entries_by_parent_item_id_and_sample_id($parent_item_id, $sample_id)
	{
		global $db;

		if (is_numeric($parent_item_id) and is_numeric($sample_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE parent_item_id = ".$parent_item_id." AND sample_id = ".$sample_id."";
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
	 * @param integer $parent_item_id
	 * @return array
	 */
	public static function list_entries_by_parent_item_id($parent_item_id)
	{
		global $db;

		if (is_numeric($parent_item_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE parent_item_id = ".$parent_item_id."";
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
	 * Returns a list of items by a given sample-id and a given gid which are parent entries
	 * @param integer $item_id
	 * @param integer $gid
	 * @return array
	 */
	public static function list_sample_id_by_item_id_and_gid_and_parent($item_id, $gid)
	{
		global $db;
		
		if (is_numeric($item_id) and is_numeric($gid))
		{
			$return_array = array();
			
			$sql = "SELECT sample_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id." AND gid = ".$gid." AND parent = 't'  AND parent_item_id IS NULL";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[sample_id]);
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
	 * @param integer $item_id
	 * @return array
	 */
	public static function list_entries_by_item_id_pk($item_id)
	{
		global $db;

		if (is_numeric($item_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id."";
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
	 * @param integer $item_id
	 * @return array
	 * with adn without sub-items
	 */
	public static function list_entries_by_item_id($item_id, $sub_items)
	{
		global $db;

		if (is_numeric($item_id))
		{
			$return_array = array();
			
			if ($sub_items == true)
			{
				$sql = "SELECT sample_id, gid FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id."";
			}
			else
			{
				$sql = "SELECT sample_id, gid FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id." AND parent_item_id IS NULL";
			}
			
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				$tmp_array = array();
				$tmp_array[id] = $data[sample_id];
				$tmp_array[pos_id] = $data[gid];
				array_push($return_array,$tmp_array);
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
	 * @param integer $sample_id
	 * @return array
	 * without sub-items and parent samples
	 */
	public static function list_items_by_sample_id($sample_id)
	{
		global $db;

		if (is_numeric($sample_id))
		{
			$return_array = array();
			
			$sql = "SELECT item_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE sample_id = ".$sample_id." AND (parent = 'f' OR parent IS NULL) AND parent_item_id IS NULL";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[item_id]);
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
	 * @see SampleItemInterface::list_items_by_sample_id_and_gid()
	 * @param integer $sample_id
	 * @param integer $gid
	 * @return array
	 */
	public static function list_items_by_sample_id_and_gid($sample_id, $gid)
	{
		global $db;

		if (is_numeric($sample_id))
		{
			$return_array = array();
			
			if (is_numeric($gid))
			{
				$sql = "SELECT DISTINCT item_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE sample_id = ".$sample_id." AND gid= ".$gid." AND parent_item_id IS NULL";
			}
			else
			{
				$sql = "SELECT DISTINCT item_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE sample_id = ".$sample_id." AND gid IS NOT NULL AND parent_item_id IS NULL";
			}
			
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[item_id]);
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
		
		$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ITEM_TABLE")."";
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

	/**
   	 * @param integer $sample_id
   	 * @param integer $parent_item_id
   	 * @return bool
   	 */
   	public static function delete_sub_items($parent_item_id, $sample_id = null)
   	{
   		global $db;
		
		if (is_numeric($parent_item_id))
		{
			if (is_numeric($sample_id))
			{
				$sql = "DELETE FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." " .
					" WHERE " .
					" ".constant("SAMPLE_HAS_ITEM_TABLE").".parent_item_id = ".$parent_item_id." AND ".constant("SAMPLE_HAS_ITEM_TABLE").".sample_id = ".$sample_id."";
			}
			else
			{
					$sql = "DELETE FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." " .
					" WHERE " .
					" ".constant("SAMPLE_HAS_ITEM_TABLE").".parent_item_id = ".$parent_item_id."";
			}
			
			$res = $db->db_query($sql);
			
			if ($res !== false)
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
}
?>
