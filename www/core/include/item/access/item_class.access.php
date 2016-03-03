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
 * Item Class Access Class
 * @package item
 */
class ItemClass_Access
{
	const ITEM_CLASS_PK_SEQUENCE = 'core_item_classes_id_seq';

	private $class_id;
	
	private $name;
	private $datetime;
	private $owner_id;
	private $colour;

	/**
	 * @param integer $class_id
	 */
    function __construct($class_id)
    {
    	global $db;
		
		if ($class_id == null)
		{
			$this->class_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("ITEM_CLASS_TABLE")." WHERE id='".$class_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->class_id		= $class_id;
				
				$this->name			= $data['name'];
				$this->datetime		= $data['datetime'];
				$this->owner_id		= $data['owner_id'];
				$this->colour		= $data['colour'];
			}
			else
			{
				$this->class_id		= null;
			}
		}
    }
    
    function __destruct()
    {
    	if ($this->class_id)
    	{
			unset($this->class_id);
			unset($this->name);
			unset($this->datetime);
			unset($this->owner_id);
			unset($this->colour);
		}
    }
    
    /**
     * @param string $name
     * @param integer $owner_id
     * @return integer
     */
    public function create($name, $owner_id)
    {
    	global $db;
		
		if ($name and is_numeric($owner_id))
		{
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("ITEM_CLASS_TABLE")." (id,name,datetime,owner_id,colour) " .
					"VALUES (nextval('".self::ITEM_CLASS_PK_SEQUENCE."'::regclass),'".$name."','".$datetime."','".$owner_id."',NULL)";
					
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("ITEM_CLASS_TABLE")." WHERE id = currval('".self::ITEM_CLASS_PK_SEQUENCE."'::regclass)";
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
	
		if ($this->class_id)
		{
			$class_id_tmp = $this->class_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("ITEM_CLASS_TABLE")." WHERE id = ".$class_id_tmp."";
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
    public function get_name()
    {
    	if ($this->name)
    	{
			return $this->name;
		}
		else
		{
			return null;
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
     * @return integer
     */
    public function get_owner_id()
    {
    	if ($this->owner_id)
    	{
			return $this->owner_id;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return string
     */
    public function get_colour()
    {
    	if ($this->colour)
    	{
			return $this->colour;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @param string $name
     * @return bool
     */
    public function set_name($name)
    {
    	global $db;

		if ($this->class_id and $name)
		{
			$sql = "UPDATE ".constant("ITEM_CLASS_TABLE")." SET name = '".$name."' WHERE id = ".$this->class_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->name = $name;
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
     * @param string $datetime
     * @return bool
     */
    public function set_datetime($datetime)
    {
    	global $db;

		if ($this->class_id and $datetime)
		{
			$sql = "UPDATE ".constant("ITEM_CLASS_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->class_id."";
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
    
    /**
     * @param integer $owner_id
     * @return bool
     */
    public function set_owner_id($owner_id)
    {
    	global $db;

		if ($this->class_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("ITEM_CLASS_TABLE")." SET owner_id = '".$owner_id."' WHERE id = ".$this->class_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->owner_id = $owner_id;
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
     * @param string $colour
     * @return bool
     */
    public function set_colour($colour)
    {
    	global $db;

		if ($this->class_id and $colour)
		{
			$sql = "UPDATE ".constant("ITEM_CLASS_TABLE")." SET colour = '".$colour."' WHERE id = ".$this->class_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->colour = $colour;
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
		
		$sql = "SELECT id FROM ".constant("ITEM_CLASS_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data['id']);	
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