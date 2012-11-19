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
 * Project Has Extension Run Access Class
 * @package project
 */
class ProjectHasExtensionRun_Access
{
	const PROJECT_HAS_EXTENSION_RUN_PK_SEQUENCE = 'core_project_has_extension_runs_primary_key_seq';
	
	private $primary_key;
	private $project_id;
	private $extension_id;
	private $run;
	
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
			$sql = "SELECT * FROM ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['primary_key'])
			{
				$this->primary_key	= $primary_key;
				$this->project_id	= $data['project_id'];
				$this->extension_id	= $data['extension_id'];
				$this->run			= $data['run'];
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
			unset($this->extension_id);
			unset($this->run);
		}
	}
	
	/**
	 * @param integer $project_id
	 * @param integer $extension_id
	 * @param integer $run
	 * @return integer
	 */
	public function create($project_id, $extension_id, $run)
	{
		global $db;
		
		if (is_numeric($project_id) and is_numeric($extension_id) and is_numeric($run))
		{			
			$sql_write = "INSERT INTO ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." (primary_key, project_id, extension_id, run) " .
					"VALUES (nextval('".self::PROJECT_HAS_EXTENSION_RUN_PK_SEQUENCE."'::regclass),".$project_id.",".$extension_id.",".$run.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." WHERE primary_key = currval('".self::PROJECT_HAS_EXTENSION_RUN_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->log_id)
		{
			$tmp_primary_key = $this->primary_key;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
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
	public function get_extension_id()
	{
		if ($this->extension_id)
		{
			return $this->extension_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_run()
	{
		if ($this->run)
		{
			return $this->run;
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
			$sql = "UPDATE ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." SET project_id = '".$project_id."' WHERE primary_key = '".$this->primary_key."'";
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
	 * @param integer $extension_id
	 * @return bool
	 */
	public function set_extension_id($extension_id)
	{	
		global $db;
		
		if ($this->primary_key and is_numeric($extension_id))
		{
			$sql = "UPDATE ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." SET extension_id = '".$extension_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->extension_id = $extension_id;
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
	 * @param integer $run
	 * @return bool
	 */
	public function set_run($run)
	{	
		global $db;
		
		if ($this->primary_key and is_numeric($run))
		{
			$sql = "UPDATE ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." SET run = '".$run."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->run = $run;
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
	 * @param integer $extension_id
	 * @param integer $project_id
	 * @return array
	 */
	public static function list_runs_by_extension_id_and_project_id($extension_id, $project_id)
	{
		global $db;
		
		if (is_numeric($extension_id) and is_numeric($project_id))
		{
			$return_array = array();
			
			$sql = "SELECT run FROM ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." WHERE extension_id='".$extension_id."' AND project_id='".$project_id."'";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['run']);
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
	public static function delete_by_project_id($project_id)
	{
		global $db;
		
		if (is_numeric($project_id))
		{
			$sql = "DELETE FROM ".constant("PROJECT_HAS_EXTENSION_RUN_TABLE")." WHERE project_id='".$project_id."'";
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