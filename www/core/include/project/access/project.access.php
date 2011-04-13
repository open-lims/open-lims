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
 * Project Access Class
 * @package project
 */
class Project_Access
{
	const PROJECT_PK_SEQUENCE = 'core_projects_id_seq';

	private $project_id;

	private $toid_organ_unit;
	private $toid_project;
	private $datetime;
	private $name;
	private $owner_id;
	private $template_id;
	private $quota;
	private $filesize;
	private $deleted;

	/**
	 * @param integer $project_id
	 */
	function __construct($project_id)
	{
		global $db;
			
		if ($project_id == null)
		{
			$this->project_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PROJECT_TABLE")." WHERE id='".$project_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->project_id 		= $project_id;
				
				$this->toid_organ_unit	= $data[toid_organ_unit];
				$this->toid_project		= $data[toid_project];
				$this->datetime			= $data[datetime];
				$this->name				= $data[name];
				$this->owner_id			= $data[owner_id];
				$this->template_id		= $data[template_id];
				$this->quota			= $data[quota];
				$this->filesize			= $data[filesize];
				
				if ($data[deleted] == "t")
				{
					$this->deleted		= true;
				}
				else
				{
					$this->deleted		= false;
				}
			}
			else
			{
				$this->project_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->project_id)
		{
			unset($this->project_id);
						
			unset($this->toid_organ_unit);
			unset($this->toid_project);
			unset($this->datetime);
			unset($this->name);
			unset($this->owner_id);
			unset($this->template_id);
			unset($this->quota);
			unset($this->filesize);
			unset($this->deleted);
		}
	}

	/**
	 * @param integer $organisation_unit_id
	 * @param integer $parent_project_id
	 * @param string $name
	 * @param integer $owner_id
	 * @param integer $template_id
	 * @param integer $project_quota
	 * @return integer
	 */
	public function create($organisation_unit_id, $parent_project_id, $name, $owner_id, $template_id, $project_quota)
	{
		global $db;
		
		if ($organisation_unit_id xor $parent_project_id)
		{
			if (!$project_quota or $project_quota == 0)
			{
				$project_quota_insert = "NULL";
			}
			else
			{
				$project_quota_insert = $project_quota;
			}
		
			$datetime = date("Y-m-d H:i:s");
			
			if ($organisation_unit_id)
			{
				$sql_write = "INSERT INTO ".constant("PROJECT_TABLE")." " .
								"(id,toid_organ_unit,toid_project,datetime,name,owner_id,template_id,quota,filesize,deleted) " .
								"VALUES (nextval('".self::PROJECT_PK_SEQUENCE."'::regclass),".$organisation_unit_id.",NULL,'".$datetime."','".$name."',".$owner_id.",".$template_id.",".$project_quota_insert.", 0, 'f')";
			}
			else
			{
				$sql_write = "INSERT INTO ".constant("PROJECT_TABLE")." " .
								"(id,toid_organ_unit,toid_project,datetime,name,owner_id,template_id,quota,filesize,deleted) " .
								"VALUES (nextval('".self::PROJECT_PK_SEQUENCE."'::regclass),NULL,".$parent_project_id.",'".$datetime."','".$name."',".$owner_id.",".$template_id.",".$project_quota_insert.", 0, 'f')";	
			}

			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE id = currval('".self::PROJECT_PK_SEQUENCE."'::regclass)";
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
    	
    	if ($this->project_id)
    	{
    		$tmp_project_id = $this->project_id;
    		
    		$this->__destruct();

    		$sql = "DELETE FROM ".constant("PROJECT_TABLE")." WHERE id = ".$tmp_project_id."";
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
    public function get_toid_organ_unit()
    {
    	if ($this->toid_organ_unit)
    	{
			return $this->toid_organ_unit;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return integer
     */
    public function get_toid_project()
    {
    	if ($this->toid_project)
    	{
			return $this->toid_project;
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
    public function get_template_id()
    {
    	if ($this->template_id)
    	{
			return $this->template_id;
		}
		else
		{
			return null;
		}
    }

	/**
	 * @return integer
	 */
    public function get_quota()
    {
    	if ($this->quota)
    	{
			return $this->quota;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return integer
     */
    public function get_filesize()
    {
    	if ($this->filesize)
    	{
			return $this->filesize;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return bool
     */
    public function get_deleted()
    {
    	if (isset($this->deleted))
    	{
			return $this->deleted;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @param integer $organ_unit_id
     * @return bool
     */
    public function set_toid_organ_unit($organ_unit_id)
    {
		global $db;

		if ($this->project_id)
		{
			if ($organ_unit_id == null)
			{
				$organ_unit_id_insert = "NULL";
			}
			else
			{
				$organ_unit_id_insert = $organ_unit_id;
			}
			
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET toid_organ_unit = ".$organ_unit_id_insert." WHERE id = ".$this->project_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->organ_unit_id = $organ_unit_id;
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
	public function set_toid_project($project_id)
	{
		global $db;

		if ($this->project_id)
		{
			if ($project_id == null)
			{
				$project_id_insert = "NULL";
			}
			else
			{
				$project_id_insert = $project_id;
			}
			
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET toid_project = ".$project_id_insert." WHERE id = ".$this->project_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->toid_project = $project_id;
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

		if ($this->project_id and $datetime)
		{
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->project_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;

		if ($this->project_id and $name)
		{
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET name = '".$name."' WHERE id = ".$this->project_id."";
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
     * @param integer $owner_id
     * @return bool
     */
    public function set_owner_id($owner_id)
    {
		global $db;

		if ($this->project_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET owner_id = ".$owner_id." WHERE id = ".$this->project_id."";
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
	 * @param integer $template_id
	 * @return bool
	 */
	public function set_template_id($template_id)
	{
		global $db;
		
		if ($this->project_id and is_numeric($template_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET template_id = ".$template_id." WHERE id = ".$this->project_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->template_id = $template_id;
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
	 * @param integer $quota
	 * @return bool
	 */
	public function set_quota($quota)
	{
		global $db;
			
		if ($this->project_id and $quota)
		{
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET quota = '".$quota."' WHERE id = ".$this->project_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->quota = $quota;
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
	 * @param integer $filesize
	 * @return bool
	 */
	public function set_filesize($filesize)
	{
		global $db;

		if ($this->project_id and $filesize)
		{
			
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET filesize = '".$filesize."' WHERE id = ".$this->project_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->filesize = $filesize;
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
     * @param bool $deleted
     * @return bool
     */
    public function set_deleted($deleted)
    {
		global $db;

		if ($this->project_id and isset($deleted))
		{
			if ($deleted == true)
			{
				$deleted_insert = "t";
			}
			else
			{
				$deleted_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PROJECT_TABLE")." SET deleted = '".$deleted_insert."' WHERE id = ".$this->project_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
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
	 * @param integer $organ_unit_id
	 * @return array
	 */
	public static function list_entries_by_toid_organ_unit($organ_unit_id)
	{
		global $db;

		if (is_numeric($organ_unit_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE toid_organ_unit = ".$organ_unit_id."";
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
	 * @return array
	 */
	public static function list_entries_by_toid_project($project_id)
	{
		global $db;
			
		if (is_numeric($project_id))
		{	
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE toid_project = ".$project_id."";
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
	 * @param integer $owner_id
	 * @return array
	 */
	public static function list_entries_by_owner_id($owner_id)
	{
		global $db;

		if (is_numeric($owner_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE owner_id = ".$owner_id."";
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
	 * @param integer $template_id
	 * @return array
	 */
	public static function list_entries_by_template_id($template_id)
	{
		global $db;
	
		if (is_numeric($template_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE template_id = ".$template_id."";
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
	 * @return array
	 */
	public static function list_root_entries()
	{
		global $db;

		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE toid_project IS NULL AND toid_organ_unit IS NOT NULL";
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
    
    /**
     * @param integer $project_id
     * @return bool
     */
    public static function exist_project_by_project_id($project_id)
    {
		global $db;
			
		if (is_numeric($project_id))
		{
			$sql = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE id = ".$project_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
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
	 * @param string $name
	 * @param integer $toid_organ_unit
	 * @return bool
	 */
	public static function exist_project_by_name_and_toid_organ_unit($name, $toid_organ_unit)
	{
		global $db;
			
		if ($name and is_numeric($toid_organ_unit))
		{
			$name = strtolower(trim($name));
			
			$sql = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE LOWER(TRIM(name)) = '".$name."' AND toid_organ_unit = ".$toid_organ_unit."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
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
	 * @param string $name
	 * @param integer $toid_project
	 * @return bool
	 */
	public static function exist_project_by_name_and_toid_project($name, $toid_project)
	{
		global $db;
			
		if ($name and is_numeric($toid_project))
		{
			$name = strtolower(trim($name));
			
			$sql = "SELECT id FROM ".constant("PROJECT_TABLE")." WHERE LOWER(TRIM(name)) = '".$name."' AND toid_project = ".$toid_project."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
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
   	public static function get_used_project_space()
   	{
   		global $db;
		
		$sql = "SELECT SUM(filesize) AS size FROM ".constant("PROJECT_TABLE")."";
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data[size])
		{
			return $data[size];
		}
		else
		{
			return null;
		}
   	}

}

?>
