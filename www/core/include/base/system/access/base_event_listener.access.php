<?php
/**
 * @package base
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
 * Base Event Listener Access Class
 * @package base
 */
class BaseEventListener_Access
{
	const BASE_EVENT_LISTENER_PK_SEQUENCE = 'core_base_event_listeners_id_seq';
	
	private $id;
	private $include_id;
	private $class_name;
	
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
			$sql = "SELECT * FROM ".constant("BASE_EVENT_LISTENER_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->id 			= $id;
				$this->include_id	= $data['include_id'];
				$this->class_name	= $data['class_name'];
			}
			else
			{
				$this->id			= null;
			}				
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->include_id);
			unset($this->class_name);
		}
	}
	
	/**
	 * @param integer $include_id
	 * @param string $class_name
	 * @return integer
	 */
	public function create($include_id, $class_name)
	{
		global $db;

		if ($class_name and is_numeric($include_id))
		{
	 		$sql_write = "INSERT INTO ".constant("BASE_EVENT_LISTENER_TABLE")." (id, include_id, class_name) " .
								"VALUES (nextval('".self::BASE_EVENT_LISTENER_PK_SEQUENCE."'::regclass),'".$include_id."','".$class_name."')";		
				
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_EVENT_LISTENER_TABLE")." WHERE id = currval('".self::BASE_EVENT_LISTENER_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
							
				$this->__construct($data_read['id']);		
								
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

			$sql = "DELETE FROM ".constant("BASE_EVENT_LISTENER_TABLE")." WHERE id = '".$id_tmp."'";
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
	 * @return string
	 */
	public function get_class_name()
	{
		if ($this->class_name)
		{
			return $this->class_name;
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
	public function set_include_id($include_id)
	{
		global $db;

		if ($this->id and is_numeric($include_id))
		{
			$sql = "UPDATE ".constant("BASE_EVENT_LISTENER_TABLE")." SET include_id = '".$include_id."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
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
	 * @param string $class_name
	 * @return bool
	 */
	public function set_class_name($class_name)
	{
		global $db;

		if ($this->id and $class_name)
		{
			$sql = "UPDATE ".constant("BASE_EVENT_LISTENER_TABLE")." SET class_name = '".$class_name."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->class_name = $class_name;
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
		
		$sql = "SELECT id FROM ".constant("BASE_EVENT_LISTENER_TABLE")."";
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
	
	/**
	 * @return array
	 */
	public static function list_classes()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT class_name FROM ".constant("BASE_EVENT_LISTENER_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data['class_name']);
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
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_by_include_id($include_id)
	{
		global $db;

		if (is_numeric($include_id))
		{
			$sql = "DELETE FROM ".constant("BASE_EVENT_LISTENER_TABLE")." WHERE include_id = '".$include_id."'";
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