<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Project Status Access Class
 * @package project
 */
class ProjectStatus_Access
{
	const PROJECT_STATUS_PK_SEQUENCE = 'core_project_status_id_seq';

	private $project_status_id;

	private $name;
	private $analysis;
	private $blocked;
	private $comment;
	
	/**
	 * @param integer $project_status_id
	 */
	function __construct($project_status_id)
	{
		global $db;

		if ($project_status_id == null)
		{
			$this->project_status_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PROJECT_STATUS_TABLE")." WHERE id='".$project_status_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->project_status_id	= $project_status_id;
				
				$this->name					= $data['name'];
				$this->comment				= $data['comment'];
				
				if ($data['analysis'] == "t")
				{
					$this->analysis			= true;
				}
				else
				{
					$this->analysis			= false;
				}
				
				if ($data['blocked'] == "t")
				{
					$this->blocked			= true;
				}
				else
				{
					$this->blocked			= false;
				}
			}
			else
			{
				$this->project_status_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->project_status_id)
		{
			unset($this->project_status_id);
						
			unset($this->name);
			unset($this->analysis);
			unset($this->blocked);
			unset($this->comment);
		}
	}
	
	/**
	 * @param string $name
	 * @return integer
	 */
	public function create($name, $comment)
	{
		global $db;
		
		if ($name)
		{
		
			if ($comment)
			{
				$comment_insert = "'".$comment."'";
			}
			else
			{
				$comment_insert = "NULL";
			}
		
			$sql_write = "INSERT INTO ".constant("PROJECT_STATUS_TABLE")." " .
							"(id,name,analysis,blocked,comment) " .
							"VALUES (nextval('".self::PROJECT_STATUS_PK_SEQUENCE."'::regclass),'".$name."','f','f',".$comment_insert.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PROJECT_STATUS_TABLE")." WHERE id = currval('".self::PROJECT_STATUS_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
								
				self::__construct($data_read['id']);				
								
				return $data_read['id'];
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * @return bool
	 */
	public function delete()
	{
    	global $db;
    	
    	if ($this->project_status_id)
    	{
    		$tmp_project_status_id = $this->project_status_id;
    		
    		$this->__destruct();
    		
    		$sql = "DELETE FROM ".constant("PROJECT_STATUS_TABLE")." WHERE id = ".$tmp_project_status_id."";
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
     * @return bool
     */
    public function get_analysis()
    {
    	if (isset($this->analysis))
    	{
			return $this->analysis;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return bool
     */
    public function get_blocked()
    {
    	if (isset($this->blocked))
    	{
			return $this->blocked;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return string
     */
    public function get_comment()
    {
    	if ($this->comment)
    	{
			return $this->comment;
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

		if ($this->project_status_id and $name)
		{
			$sql = "UPDATE ".constant("PROJECT_STATUS_TABLE")." SET name = '".$name."' WHERE id = ".$this->project_status_id."";
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
	 * @param bool $analysis
	 * @return bool
	 */
	public function set_analysis($analysis)
	{
		global $db;
		
		if ($this->project_status_id and isset($analysis))
		{
			if ($analysis == true)
			{
				$analysis_insert = "t";
			}
			else
			{
				$analysis_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PROJECT_STATUS_TABLE")." SET analysis = '".$analysis_insert."' WHERE id = ".$this->project_status_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->analysis = $analysis;
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
	 * @param bool $blocked
	 * @return bool
	 */
	public function set_blocked($blocked)
	{
		global $db;

		if ($this->project_status_id and isset($blocked))
		{
			if ($blocked == true)
			{
				$blocked_insert = "t";
			}
			else
			{
				$blocked_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PROJECT_STATUS_TABLE")." SET blocked = '".$blocked_insert."' WHERE id = ".$this->project_status_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->blocked = $blocked;
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
	 * @param string $comment
	 * @return bool
	 */
	public function set_comment($comment)
	{
		global $db;

		if ($this->project_status_id and $comment)
		{
			$sql = "UPDATE ".constant("PROJECT_STATUS_TABLE")." SET comment = '".$comment."' WHERE id = ".$this->project_status_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->comment = $comment;
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
			$sql = "SELECT id FROM ".constant("PROJECT_STATUS_TABLE")." WHERE id = ".$id."";
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
