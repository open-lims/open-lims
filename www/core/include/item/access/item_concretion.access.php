<?php
/**
 * @package item
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Item Concretion Access Class
 * @package item
 */
class ItemConcretion_Access
{
	const ITEM_CONCRETION_PK_SEQUENCE = 'core_item_concretion_id_seq';
	
	private $id;
	private $type;
	private $handling_class;
	private $include_id;
	
	/**
	 * @param integer $id
	 */
	function __construct($id)
	{
		global $db;
		
		if ($id == null)
		{
			$this->id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("ITEM_CONCRETION_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id				= $item_id;
				$this->type				= $data['type'];
				$this->handling_class	= $data['handling_class'];
				$this->include_id		= $data['include_id'];
			}
			else
			{
				$this->id		= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->type);
			unset($this->handling_class);
			unset($this->include_id);
		}
	}
	
	/**
	 * @param string $type
	 * @param string $handling_class
	 * @param integer $include_id
	 * @return integer
	 */
	public function create($type, $handling_class, $include_id)
	{
		global $db;
		
		if ($type and $handling_class and is_numeric($include_id))
		{
			$sql_write = "INSERT INTO ".constant("ITEM_CONCRETION_TABLE")." (id,type,handling_class,include_id) " .
					"VALUES (nextval('".self::ITEM_CONCRETION_PK_SEQUENCE."'::regclass),'".$type."','".$handling_class."','".$include_id."')";
			
			$res_write = $db->db_query($sql_write);	
					
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("ITEM_CONCRETION_TABLE")." WHERE id = currval('".self::ITEM_CONCRETION_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->fetch($res_read);
									
				self::__construct($data_read['id']);
				
				return $data_read['id'];
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
			
		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("ITEM_CONCRETION_TABLE")." WHERE id = ".$id_tmp."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res) == 1)
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
    public function get_type()
    {
    	if ($this->type)
    	{
			return $this->type;
		}
		else
		{
			return null;
		}
    }
	
	/**
     * @return string
     */
    public function get_handling_class()
    {
    	if ($this->handling_class)
    	{
			return $this->handling_class;
		}
		else
		{
			return null;
		}
    }
    
	/**
     * @return integer
     */
    public function get_include_id()
    {
    	if ($this->include_id)
    	{
			return $this->include_id;
		}
		else
		{
			return null;
		}
    }
    
	/**
	 * @param string $type
	 * @return bool
	 */
	public function set_type($type)
	{
		global $db;

		if ($this->id and $type)
		{
			$sql = "UPDATE ".constant("ITEM_CONCRETION_TABLE")." SET type = '".$type."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
			{
				$this->type = $type;
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
	 * @param string $handling_class
	 * @return bool
	 */
	public function set_handling_class($handling_class)
	{
		global $db;

		if ($this->id and $handling_class)
		{
			$sql = "UPDATE ".constant("ITEM_CONCRETION_TABLE")." SET handling_class = '".$handling_class."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
			{
				$this->handling_class = $handling_class;
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
	 * @param integer $include_id
	 * @return bool
	 */
	public function set_include_id($include_id)
	{
		global $db;

		if ($this->id and is_numeric($include_id))
		{
			$sql = "UPDATE ".constant("ITEM_CONCRETION_TABLE")." SET include_id = '".$include_id."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
			{
				$this->include_id = $include_id;
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT type,handling_class FROM ".constant("ITEM_CONCRETION_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->fetch($res))
		{
			$return_array[$data['type']] = $data['handling_class'];
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
	 * @param string $type
	 * @return array
	 */
	public static function get_handling_class_by_type($type)
	{
		global $db;
		
		if ($type)
		{
			$sql = "SELECT handling_class FROM ".constant("ITEM_CONCRETION_TABLE")." WHERE type = '".$type."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['handling_class'])
			{
				return $data['handling_class'];
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
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_by_include_id($include_id)
	{
		global $db;

		if (is_numeric($include_id))
		{
			$sql = "DELETE FROM ".constant("ITEM_CONCRETION_TABLE")." WHERE include_id = '".$include_id."'";
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
