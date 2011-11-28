<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
 * Project Permission Access Class
 * @package project
 */
class ProjectPermission_Access
{
	const PROJECT_PERMISSION_PK_SEQUENCE = 'core_project_permissions_id_seq';

	private $permission_id;

	private $user_id;
	private $organisation_unit_id;
	private $group_id;
	private $project_id;
	private $permission;
	private $owner_id;
	private $intention;

	/**
	 * @param integer $permission_id
	 */
	function __construct($permission_id)
	{
		global $db;

		if ($permission_id == null)
		{
			$this->permission_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE id='".$permission_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->permission_id 		= $permission_id;
				
				$this->user_id				= $data[user_id];
				$this->organisation_unit_id	= $data[organisation_unit_id];
				$this->group_id				= $data[group_id];
				$this->project_id			= $data[project_id];
				$this->permission			= $data[permission];
				$this->owner_id				= $data[owner_id];
				$this->intention			= $data[intention];
			}
			else
			{
				$this->permission_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->primary_key)
		{
			unset($this->permission_id);

			unset($this->user_id);
			unset($this->organisation_unit_id);
			unset($this->group_id);
			unset($this->project_id);
			unset($this->permission);
			unset($this->owner_id);
			unset($this->intention);		
		}
	}
	
	/**
	 * @param integer $user_id
	 * @param integer $organisation_user_id
	 * @param integer $group_id
	 * @param integer $project_id
	 * @param integer $permission
	 * @param integer $owner_id
	 * @param integer $intention
	 * @return integer
	 */
	public function create($user_id, $organisation_unit_id, $group_id, $project_id, $permission, $owner_id, $intention)
	{
		global $db;
		
		if (($user_id xor $organisation_unit_id xor $group_id) and $project_id)
		{
			if ($user_id)
			{
				$user_id_insert = $user_id;
			}
			else
			{
				$user_id_insert = "NULL";
			}
			
			if ($organisation_unit_id)
			{
				$organisation_unit_id_insert = $organisation_unit_id;
			}
			else
			{
				$organisation_unit_id_insert = "NULL";
			}
			
			if ($group_id)
			{
				$group_id_insert = $group_id;
			}
			else
			{
				$group_id_insert = "NULL";
			}
			
			if ($permission)
			{
				$permission_insert = $permission;
			}
			else
			{
				$permission_insert = "0";
			}
			
			if ($owner_id)
			{
				$owner_id_insert = $owner_id;
			}
			else
			{
				$owner_id_insert = "NULL";
			}
			
			if ($intention)
			{
				$intention_insert = $intention;
			}
			else
			{
				$intention_insert = "0";
			}
			
			$sql_write = "INSERT INTO ".constant("PROJECT_PERMISSION_TABLE")." (id,user_id,organisation_unit_id,group_id,project_id,permission,owner_id,intention) " .
					"VALUES (nextval('".self::PROJECT_PERMISSION_PK_SEQUENCE."'::regclass),".$user_id_insert.",".$organisation_unit_id_insert.",".$group_id_insert.",".$project_id.",".$permission_insert.",".$owner_id_insert.",".$intention_insert.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE id = currval('".self::PROJECT_PERMISSION_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read[id]);
				
				return $data_read[id];
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
				
		if ($this->permission_id)
		{
			$tmp_id = $this->permission_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE id = ".$tmp_id."";
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
	public function get_user_id()
	{
		if ($this->user_id)
		{
			return $this->user_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_organisation_unit_id()
	{
		if ($this->organisation_unit_id)
		{
			return $this->organisation_unit_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_group_id()
	{
		if ($this->group_id)
		{
			return $this->group_id;
		}
		else
		{
			return null;
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
	public function get_permission()
	{
		if ($this->permission)
		{
			return $this->permission;
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
	 * @return integer
	 */
	public function get_intention()
	{
		if ($this->intention)
		{
			return $this->intention;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $user_id
	 * @return bool
	 */
	public function set_user_id($user_id)
	{
		global $db;

		if ($this->permission_id and is_numeric($user_id))
		{
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." SET user_id = '".$user_id."' WHERE id = '".$this->permission_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->user_id = $user_id;
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
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public function set_organisation_unit_id($organisation_unit_id)
	{
		global $db;

		if ($this->permission_id and is_numeric($organisation_unit_id))
		{
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." SET organisation_unit_id = '".$organisation_unit_id."' WHERE id = '".$this->permission_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->organisation_unit_id = $organisation_unit_id;
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
	 * @param integer $group_id
	 * @return bool
	 */
	public function set_group_id($group_id)
	{
		global $db;

		if ($this->permission_id and is_numeric($group_id))
		{
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." SET group_id = '".$group_id."' WHERE id = '".$this->permission_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->group_id = $group_id;
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
	 * @param integer $project_id
	 * @return bool
	 */
	public function set_project_id($project_id)
	{
		global $db;

		if ($this->permission_id and is_numeric($project_id))
		{
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." SET project_id = '".$project_id."' WHERE id = '".$this->permission_id."'";
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
	 * @param integer $permission
	 * @return bool
	 */
	public function set_permission($permission)
	{
		global $db;

		if ($this->permission_id and is_numeric($permission))
		{
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." SET permission = '".$permission."' WHERE id = '".$this->permission_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->permission = $permission;
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

		if ($this->permission_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." SET owner_id = '".$owner_id."' WHERE id = '".$this->permission_id."'";
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
		
		if (is_numeric($id))
		{			
			$sql = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE id = ".$id."";
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
	 * @param integer $project_id
	 * @return array
	 */
	public static function list_entries_by_project_id($project_id)
	{
		global $db;
		
		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE project_id = ".$project_id." ORDER BY intention DESC";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @param integer $intention
	 * @return array
	 */
	public static function list_entries_by_project_id_and_intention($project_id, $intention)
	{
		global $db;

		if (is_numeric($project_id) and is_numeric($intention))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE project_id = ".$project_id." AND intention = ".$intention." AND intention IS NOT NULL";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @param integer $intention
	 * @param integer $group_id
	 * @return array
	 */
	public static function list_entries_by_project_id_and_intention_and_group_id($project_id, $intention, $group_id)
	{
		global $db;
			
		if (is_numeric($project_id) and is_numeric($intention) and is_numeric($group_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE project_id = ".$project_id." AND intention = ".$intention." AND intention IS NOT NULL AND group_id = ".$group_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @param integer $intention
	 * @param integer $user_id
	 * @return array
	 */
	public static function list_entries_by_project_id_and_intention_and_user_id($project_id, $intention, $user_id)
	{
		global $db;
			
		if (is_numeric($project_id) and is_numeric($intention) and is_numeric($user_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE project_id = ".$project_id." AND intention = ".$intention." AND intention IS NOT NULL AND user_id = ".$user_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @param integer $organisation_unit_id
	 * @param integer $intention
	 * @return array
	 */
	public static function list_projects_by_organisation_id_and_intention($organisation_unit_id, $intention)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			if ($intention == null)
			{
				$intention_insert = "AND intention IS NULL";
			}
			else
			{
				$intention_insert = "AND intention = ".$intention."";
			}
			
			$return_array = array();
				
			$sql = "SELECT project_id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id." ".$intention_insert."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[project_id]);
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
	 * @param integer $user_id
	 * @return integer
	 */
	public static function count_entries_with_project_id_and_user_id($project_id, $user_id)
	{
		global $db;
			
		if (is_numeric($project_id) and is_numeric($user_id))
		{			
			$sql = "SELECT COUNT(id) AS numberofentries FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE project_id = ".$project_id." and user_id = ".$user_id." AND TRUE = (SELECT * FROM project_permission_user(".$project_id.", ".$user_id."))";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[numberofentries])
			{
				return $data[numberofentries];
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
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function count_entries_with_project_id_and_organisation_unit_id($project_id, $organisation_unit_id)
	{
		global $db;
			
		if (is_numeric($project_id) and is_numeric($organisation_unit_id))
		{
			$sql = "SELECT COUNT(id) AS numberofentries FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE project_id = ".$project_id." and organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[numberofentries])
			{
				return $data[numberofentries];
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
	 * @param integer $group_id
	 * @return integer
	 */
	public static function count_entries_with_project_id_and_group_id($project_id, $group_id)
	{
		global $db;

		if (is_numeric($project_id) and is_numeric($group_id))
		{	
			$sql = "SELECT COUNT(id) AS numberofentries FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE project_id = ".$project_id." and group_id = ".$group_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[numberofentries])
			{
				return $data[numberofentries];
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
	 * @param integer $intention
	 * @return array
	 */
	public static function delete_entries_by_project_id_and_intention($project_id, $intention)
	{
		global $db;

		if (is_numeric($project_id) and is_numeric($intention))
		{
			$return_array = array();
			
			$sql = "DELETE FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE project_id = ".$project_id." AND intention = ".$intention." AND intention IS NOT NULL";
			$res = $db->db_query($sql);
			
			return true;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $user_id
	 * @return bool
	 */
	public static function delete_by_user_id($user_id)
	{
		global $db;
		
		if (is_numeric($user_id))
		{
			$sql = "DELETE FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE user_id = ".$user_id."";
			$res = $db->db_query($sql);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $group_id
	 * @return bool
	 */
	public static function delete_by_group_id($group_id)
	{
		global $db;
		
		if (is_numeric($group_id)) 
		{
			$sql = "DELETE FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE group_id = ".$group_id."";
			$res = $db->db_query($sql);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public static function delete_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{	
			$sql = "DELETE FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);	
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $old_owner_id
	 * @param integer $new_owner_id
	 * @return bool
	 */
	public static function reset_owner_id($old_owner_id, $new_owner_id)
	{
		global $db;
		
		if (is_numeric($old_owner_id) and is_numeric($new_owner_id))
		{
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." SET owner_id = ".$new_owner_id." WHERE owner_id = ".$old_owner_id."";
			$res = $db->db_query($sql);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $permission_id
	 * @return bool
	 */
	public static function is_user_entry($permission_id)
	{
		global $db;
		
		if (is_numeric($permission_id))
		{			
			$sql = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE user_id IS NOT NULL AND id = ".$permission_id."";
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
	 * @param integer $permission_id
	 * @return bool
	 */
	public static function is_group_entry($permission_id)
	{
		global $db;
		
		if (is_numeric($permission_id))
		{			
			$sql = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE group_id IS NOT NULL AND id = ".$permission_id."";
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
	 * @param integer $permission_id
	 * @return bool
	 */
	public static function is_organisation_unit_entry($permission_id)
	{
		global $db;
		
		if (is_numeric($permission_id))
		{			
			$sql = "SELECT id FROM ".constant("PROJECT_PERMISSION_TABLE")." WHERE organisation_unit_id IS NOT NULL AND id = ".$permission_id."";
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
}
?>
