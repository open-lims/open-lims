<?php
/**
 * @package data
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
 * Value Var Case Access Class
 * @package data
 */
class ValueVarCase_Access
{
	const VALUE_VAR_CASE_PK_SEQUENCE = 'core_value_var_cases_id_seq';
	
	private $id;
	private $name;
	private $handling_class;
	private $ignore_this;
	private $include_id;
	
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
			$sql = "SELECT * FROM ".constant("VALUE_VAR_CASE_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->id				= $item_id;
				$this->name				= $data['name'];
				$this->handling_class	= $data['handling_class'];
				$this->include_id		= $data['include_id'];
				
				if ($data['ignore_this'] == 't')
				{
					$this->ignore_this = true;
				}
				else
				{
					$this->ignore_this = false;
				}
			}
			else
			{
				$this->id		= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->name);
			unset($this->handling_class);
			unset($this->include_id);
			unset($this->ignore_this);
		}
	}
	
	/**
	 * @return integer
	 */
	public function create($name, $handling_class, $ignore_this, $include_id)
	{
		global $db;
		
		if ($name and $handling_class and is_numeric($include_id))
		{
			if ($ignore_this == true)
			{
				$ignore_this_insert = "'t'";
			}
			else
			{
				$ignore_this_insert = "'f'";
			}
			
			$sql_write = "INSERT INTO ".constant("VALUE_VAR_CASE_TABLE")." (id,name,handling_class,ignore_this,include_id) " .
					"VALUES (nextval('".self::VALUE_VAR_CASE_PK_SEQUENCE."'::regclass),'".$name."','".$handling_class."',".$ignore_this_insert.",'".$include_id."')";
			
			$res_write = $db->db_query($sql_write);	
					
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("VALUE_VAR_CASE_TABLE")." WHERE id = currval('".self::VALUE_VAR_CASE_PK_SEQUENCE."'::regclass)";
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
			
			$sql = "DELETE FROM ".constant("VALUE_VAR_CASE_TABLE")." WHERE id = ".$id_tmp."";
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
    public function get_handling_class()
    {
    	if ($this->handling_class)
    	{
			return $this->handling_class;
		}
		else
		{
			return null;
		}
    }
    
	/**
     * @return bool
     */
    public function get_ignore_this()
    {
    	if (isset($this->ignore_this))
    	{
			return $this->ignore_this;
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("VALUE_VAR_CASE_TABLE")." SET type = '".$name."' WHERE id = ".$this->id."";
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
	 * @param string $handling_class
	 * @return bool
	 */
	public function set_handling_class($handling_class)
	{
		global $db;

		if ($this->id and $handling_class)
		{
			$sql = "UPDATE ".constant("VALUE_VAR_CASE_TABLE")." SET handling_class = '".$handling_class."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->handling_class = $handling_class;
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
	 * @param bool $ignore_this
	 * @return bool
	 */
	public function set_ignore_this($ignore_this)
	{
		global $db;

		if ($this->id and isset($ignore_this))
		{
			if ($ignore_this == true)
			{
				$ignore_this_insert = "'t'";
			}
			else
			{
				$ignore_this_insert = "'f'";
			}
			
			$sql = "UPDATE ".constant("VALUE_VAR_CASE_TABLE")." SET ignore_this = ".$ignore_this_insert." WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->ignore_this = $ignore_this;
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
	 * @param integer $include_id
	 * @return bool
	 */
	public function set_include_id($include_id)
	{
		global $db;

		if ($this->id and is_numeric($include_id))
		{
			$sql = "UPDATE ".constant("VALUE_VAR_CASE_TABLE")." SET include_id = '".$include_id."' WHERE id = ".$this->id."";
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT name,handling_class FROM ".constant("VALUE_VAR_CASE_TABLE")." WHERE ignore_this = 'f'";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			$return_array[$data['name']] = $data['handling_class'];
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
	public static function get_handling_class_by_name($name)
	{
		global $db;
		
		if ($name)
		{	
			$name = trim(strtolower($name));
			
			$sql = "SELECT handling_class FROM ".constant("VALUE_VAR_CASE_TABLE")." WHERE TRIM(LOWER(name)) = '".$name."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['handling_class'])
			{
				return $data['handling_class'];
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
	 * @param integer $include_id
	 * @return bool
	 */
	public static function delete_by_include_id($include_id)
	{
		global $db;

		if (is_numeric($include_id))
		{
			$sql = "DELETE FROM ".constant("VALUE_VAR_CASE_TABLE")." WHERE include_id = '".$include_id."'";
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
