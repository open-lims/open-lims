<?php
/**
 * @package project
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
 * Project Has Item Access Class
 * @package project
 */
class ProjectHasItem_Access
{
	const PROJECT_HAS_ITEM_PK_SEQUENCE = 'core_project_has_items_primary_key_seq';

	private $primary_key;

	private $project_id;
	private $item_id;
	private $active;
	private $required;
	private $gid;
	private $project_status_id;
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
			$sql = "SELECT * FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[primary_key])
			{
				$this->primary_key 			= $primary_key;
				
				$this->project_id			= $data[project_id];
				$this->item_id				= $data[item_id];
				$this->gid					= $data[gid];
				$this->project_status_id	= $data[project_status_id];
				$this->parent_item_id		= $data[parent_item_id];
				
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
			unset($this->gid);
			unset($this->project_status_id);
		}
	}
	
	/**
	 * @param integer $project_id
	 * @param integer $item_id
	 * @return integer
	 */
	public function create($project_id, $item_id, $gid)
	{
		global $db;
		
		if (is_numeric($project_id) and is_numeric($item_id))
		{
			if (is_numeric($gid))
			{
				$gid_insert = $gid;	
			}
			else
			{
				$gid_insert = "NULL";
			}
			
			$sql_write = "INSERT INTO ".constant("PROJECT_HAS_ITEM_TABLE")." (primary_key,project_id,item_id,active, required, gid, project_status_id) " .
					"VALUES (nextval('".self::PROJECT_HAS_ITEM_PK_SEQUENCE."'::regclass),".$project_id.",".$item_id.",'t','f',".$gid_insert.",NULL)";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE primary_key = currval('".self::PROJECT_HAS_ITEM_PK_SEQUENCE."'::regclass)";
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
						
			$sql = "DELETE FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
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
	 * @return integer
	 */
	public function get_project_status_id()
	{
		if ($this->project_status_id)
		{
			return $this->project_status_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_parent_item_id()
	{
		if ($this->parent_item_id)
		{
			return $this->parent_item_id;
		}
		else
		{
			return null;
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
			$sql = "UPDATE ".constant("PROJECT_HAS_ITEM_TABLE")." SET project_id = '".$project_id."' WHERE primary_key = '".$this->primary_key."'";
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
			$sql = "UPDATE ".constant("PROJECT_HAS_ITEM_TABLE")." SET item_id = '".$item_id."' WHERE primary_key = '".$this->primary_key."'";
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
			
			$sql = "UPDATE ".constant("PROJECT_HAS_ITEM_TABLE")." SET active = '".$active_insert."' WHERE primary_key = '".$this->primary_key."'";
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
			
			$sql = "UPDATE ".constant("PROJECT_HAS_ITEM_TABLE")." SET required = '".$required_insert."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param integer $gid
	 * @return bool
	 */
	public function set_gid($gid)
	{
		global $db;

		if ($this->primary_key and is_numeric($gid))
		{
			$sql = "UPDATE ".constant("PROJECT_HAS_ITEM_TABLE")." SET gid = '".$gid."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param integer $project_status_id
	 * @return bool
	 */
	public function set_project_status_id($project_status_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($project_status_id))
		{
			$sql = "UPDATE ".constant("PROJECT_HAS_ITEM_TABLE")." SET project_status_id = '".$project_status_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->project_status_id = $project_status_id;
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
	 * @param integer $parent_item_id
	 * @return bool
	 */
	public function set_parent_item_id($parent_item_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($parent_item_id))
		{
			$sql = "UPDATE ".constant("PROJECT_HAS_ITEM_TABLE")." SET parent_item_id = '".$parent_item_id."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param integer $project_id
	 * @return integer
	 * Returns with sub-items
	 */
	public static function get_entry_by_item_id_and_project_id($item_id, $project_id)
	{
		global $db;
	
		if (is_numeric($item_id) and is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id." AND project_id = ".$project_id."";
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
	 * @param integer $sample_id
	 * @param integer $project_status_id
	 * @return integer
	 * Returns without sub-items
	 */
	public static function get_gid_by_item_id_and_project_id($item_id, $project_id, $project_status_id)
	{
		global $db;
			
		if (is_numeric($item_id) and is_numeric($project_id) and is_numeric($project_status_id))
		{
			$return_array = array();
			
			$sql = "SELECT gid FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id." AND project_id = ".$project_id." AND project_status_id = ".$project_status_id." AND parent_item_id IS NULL";
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
	 * @param integer $project_id
	 * @return array
	 * Returns with sub-items
	 */
	public static function list_entries_by_parent_item_id_and_project_id($parent_item_id, $project_id)
	{
		global $db;

		if (is_numeric($parent_item_id) and is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE parent_item_id = ".$parent_item_id." AND project_id = ".$project_id."";
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
	 * Returns with sub-items
	 */
	public static function list_entries_by_parent_item_id($parent_item_id)
	{
		global $db;

		if (is_numeric($parent_item_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE parent_item_id = ".$parent_item_id."";
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
	 * Returns with sub-items
	 */
	public static function list_entries_by_item_id($item_id)
	{
		global $db;

		if (is_numeric($item_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE item_id = ".$item_id."";
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
	 * @param integer $project_status_id
	 * @return array
	 * Returns with or without sub-items
	 */
	public static function list_entries_by_project_id_and_project_status_id($project_id, $project_status_id, $sub_items)
	{
		global $db;

		if (is_numeric($project_id) and is_numeric($project_status_id))
		{
			$return_array = array();
			
			if ($sub_items == true)
			{
				$sql = "SELECT item_id FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE project_id = ".$project_id." AND project_status_id = ".$project_status_id."";
			}
			else
			{
				$sql = "SELECT item_id FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE project_id = ".$project_id." AND project_status_id = ".$project_status_id." AND parent_item_id IS NULL";
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
	 * @param integer $project_id
	 * @param bool $sub_items
	 * @return array
	 * Returns with or without subitems
	 */	
	public static function list_entries_by_project_id($project_id, $sub_items)
	{
		global $db;

		if (is_numeric($project_id))
		{
			$return_array = array();
			
			if ($sub_items == true)
			{
				$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE project_id = ".$project_id."";
			}
			else
			{
				$sql = "SELECT primary_key FROM ".constant("PROJECT_HAS_ITEM_TABLE")." WHERE project_id = ".$project_id." AND parent_item_id IS NULL";
			}
			
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
}
?>
