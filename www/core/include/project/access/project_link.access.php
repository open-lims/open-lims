<?php
/**
 * @package project
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
 * Project Link Access Class
 * @package project
 */
class ProjectLink_Access
{
	const PROJECT_LINK_PK_SEQUENCE = 'core_project_links_primary_key_seq';

	private $primary_key;

	private $to_project_id;
	private $project_id;

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
			$sql = "SELECT * FROM ".constant("PROJECT_LINK_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['primary_key'])
			{
				$this->primary_key 		= $primary_key;
				
				$this->to_project_id	= $data['to_project_id'];
				$this->project_id		= $data['project_id'];
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
			unset($this->to_project_id);
			unset($this->project_id);
		}
	}
	
	/**
	 * @param integer $to_project_id
	 * @param integer $project_id
	 * @return integer
	 */
	public function create($to_project_id, $project_id)
	{
		global $db;
		
		if (is_numeric($to_project_id) and is_numeric($project_id))
		{
			$sql_write = "INSERT INTO ".constant("PROJECT_LINK_TABLE")." (primary_key,to_project_id,project_id) " .
					"VALUES (nextval('".self::PROJECT_LINK_PK_SEQUENCE."'::regclass),".$to_project_id.",".$project_id.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("PROJECT_LINK_TABLE")." WHERE primary_key = currval('".self::PROJECT_LINK_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read['primary_key']);
				
				return $data_read['primary_key'];
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
						
			$sql = "DELETE FROM ".constant("PROJECT_LINK_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
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
	public function get_to_project_id()
	{
		if ($this->to_project_id)
		{
			return $this->to_project_id;
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
	 * @param integer $to_project_id
	 * @return bool
	 */
	public function set_to_project_id($to_project_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($to_project_id))
		{
			$sql = "UPDATE ".constant("PROJECT_LINK_TABLE")." SET to_project_id = '".$to_project_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->to_project_id = $to_project_id;
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
	
		if ($this->primary_key and is_numeric($project_id))
		{	
			$sql = "UPDATE ".constant("PROJECT_LINK_TABLE")." SET project_id = '".$project_id."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param integer $project_id
	 * @return array
	 */
	public static function list_entries_by_project_id($project_id)
	{
		global $db;

		if (is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("PROJECT_LINK_TABLE")." WHERE to_project_id = ".$project_id." OR project_id = ".$project_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['primary_key']);
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
