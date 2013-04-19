<?php
/**
 * @package base
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
 * Group Access Class
 * @package base
 */
class Group_Access
{
	const GROUP_PK_SEQUENCE = 'core_groups_id_seq';
	
	private $group_id;
	private $name;
	
	/**
	 * @param integer $group_id
	 */
	function __construct($group_id)
	{
		global $db;
		
		if ($group_id == null)
		{
			$this->group_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("GROUP_TABLE")." WHERE id='".$group_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->group_id 		= $group_id;
				$this->name				= $data['name'];
			}
			else
			{
				$this->group_id			= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->group_id)
		{
			unset($this->group_id);
			unset($this->name);
		}
	}
	
	/**
	 * @param string $name
	 * @return integer
	 */
	public function create($name)
	{
		global $db;
		
		if ($name)
		{
			$sql_write = "INSERT INTO ".constant("GROUP_TABLE")." (id,name) " .
						"VALUES (nextval('".self::GROUP_PK_SEQUENCE."'::regclass),'".$name."')";
																	
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) != 1)
			{
				return null;
			}
			else
			{
				$sql_read = "SELECT id FROM ".constant("GROUP_TABLE")." WHERE id = currval('".self::GROUP_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				self::__construct($data_read['id']);
				
				return $data_read['id'];
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

		if ($this->group_id)
		{
			$group_id_tmp = $this->group_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("GROUP_TABLE")." WHERE id = ".$group_id_tmp."";
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;
			
		if ($this->group_id and $name)
		{
			$sql = "UPDATE ".constant("GROUP_TABLE")." SET name = '".$name."' WHERE id = ".$this->group_id."";
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
	 * @param string $name
	 * @return integer
	 */
	public static function get_group_id_by_name($name)
	{
		global $db;
		
		if ($name)
		{					
			$sql = "SELECT id FROM ".constant("GROUP_TABLE")." WHERE LOWER(name) = '".$name."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				return $data['id'];
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
	 * @return integer
	 */
	public static function get_number_of_groups()
	{
		global $db;
									
		$sql = "SELECT COUNT(id) AS result FROM ".constant("GROUP_TABLE")."";
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data['result'])
		{
			return $data['result'];
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
		
		$sql = "SELECT id FROM ".constant("GROUP_TABLE")."";
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
	 * @param string $groupname
	 * @return integer
	 */
	public static function search_groups($groupname)
	{
   		global $db;
   		
   		if ($groupname)
   		{
   			$groupname = str_replace("*","%",$groupname);
   			
   			$return_array = array();
   				
   			$sql = "SELECT id FROM ".constant("GROUP_TABLE")." WHERE LOWER(name) LIKE '".$groupname."'";   			
   			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array, $data['id']);
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
   	 * @param integer $group_id
   	 * @return bool
   	 */
   	public static function exist_group($group_id)
   	{
		
		global $db;
		
		if (is_numeric($group_id))
		{
		
			$return_array = array();	
												
			$sql = "SELECT id FROM ".constant("GROUP_TABLE")." WHERE id = ".$group_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
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
	public static function count_groups()
	{
		global $db;
											
		$sql = "SELECT COUNT(id) AS result FROM ".constant("GROUP_TABLE")."";
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data['result'])
		{
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
}

?>
