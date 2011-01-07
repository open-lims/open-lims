<?php
/**
 * @package project
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
 * Project Has Item Access Class
 * @package project
 */
class ProjectHasItem_Access
{
	const PROJECT_HAS_ITEM_TABLE = 'core_project_has_items';
	const PROJECT_HAS_ITEM_PK_SEQUENCE = 'core_project_has_items_primary_key_seq';

	private $primary_key;

	private $project_id;
	private $item_id;
	private $active;
	private $required;

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
			$sql = "SELECT * FROM ".self::PROJECT_HAS_ITEM_TABLE." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				$this->primary_key 		= $primary_key;
				
				$this->project_id		= $data[project_id];
				$this->item_id			= $data[item_id];
				
				if ($data[active] == 't')
				{
					$this->active = true;
				}
				else
				{
					$this->active = false;
				}
				
				if ($data[required] == 't')
				{
					$this->required = true;
				}
				else
				{
					$this->required = false;
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
			unset($this->project_id);
			unset($this->item_id);
			unset($this->active);
			unset($this->required);
		}
	}
	
	/**
	 * @param integer $project_id
	 * @param integer $item_id
	 * @return integer
	 */
	public function create($project_id, $item_id)
	{
		global $db;
		
		if (is_numeric($project_id) and is_numeric($item_id))
		{
			$sql_write = "INSERT INTO ".self::PROJECT_HAS_ITEM_TABLE." (primary_key,project_id,item_id,active, required) " .
					"VALUES (nextval('".self::PROJECT_HAS_ITEM_PK_SEQUENCE."'::regclass),".$project_id.",".$item_id.",'t','f')";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".self::PROJECT_HAS_ITEM_TABLE." WHERE primary_key = currval('".self::PROJECT_HAS_ITEM_PK_SEQUENCE."'::regclass)";
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
						
			$sql = "DELETE FROM ".self::PROJECT_HAS_ITEM_TABLE." WHERE primary_key = ".$tmp_primary_key."";
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
	public function get_project_id()
	{
		if ($this->project_id)
		{
			return $this->project_id;
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
	 * @return bool
	 */
	public function get_active()
	{
		if (isset($this->active))
		{
			return $this->active;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_required()
	{
		if (isset($this->required))
		{
			return $this->required;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $project_id
	 * @return bool
	 */
	public function set_project_id($project_id)
	{	
		global $db;

		if ($this->primary_key and is_numeric($project_id))
		{
			$sql = "UPDATE ".self::PROJECT_HAS_ITEM_TABLE." SET project_id = '".$project_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->project_id = $project_id;
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
			$sql = "UPDATE ".self::PROJECT_HAS_ITEM_TABLE." SET item_id = '".$item_id."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param bool $active
	 * @return bool
	 */
	public function set_active($active)
	{	
		global $db;

		if ($this->primary_key and isset($active))
		{
			if ($active == true)
			{
				$active_insert = "t";
			}
			else
			{
				$active_insert = "f";
			}
			
			$sql = "UPDATE ".self::PROJECT_HAS_ITEM_TABLE." SET active = '".$active_insert."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->active = $active;
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
	 * @param bool $required
	 * @return bool
	 */
	public function set_required($required)
	{	
		global $db;
	
		if ($this->primary_key and isset($required))
		{
			if ($required == true)
			{
				$required_insert = "t";
			}
			else
			{
				$required_insert = "f";
			}
			
			$sql = "UPDATE ".self::PROJECT_HAS_ITEM_TABLE." SET required = '".$required_insert."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->required = $required;
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
	 * @param integer $project_id
	 * @return integer
	 */
	public static function get_entry_by_item_id_and_project_id($item_id, $project_id)
	{
		global $db;
	
		if (is_numeric($item_id) and is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".self::PROJECT_HAS_ITEM_TABLE." WHERE item_id = ".$item_id." AND project_id = ".$project_id."";
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
	 * @param integer $item_id
	 * @return array
	 */
	public static function list_entries_by_item_id($item_id)
	{
		global $db;

		if (is_numeric($item_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".self::PROJECT_HAS_ITEM_TABLE." WHERE item_id = ".$item_id."";
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
	 * @param integer $project_id
	 * @return array
	 */	
	public static function list_entries_by_project_id($project_id)
	{
		global $db;

		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".self::PROJECT_HAS_ITEM_TABLE." WHERE project_id = ".$project_id."";
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
		
		$sql = "SELECT primary_key FROM ".self::PROJECT_HAS_ITEM_TABLE."";
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
