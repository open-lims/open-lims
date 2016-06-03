<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Project Template Access Class
 * @package project
 */
class ProjectTemplate_Access
{
	private $id;
	
	private $name;
	private $cat_id;
	private $parent_template;
	private $template_id;
	
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
			$sql = "SELECT * FROM ".constant("PROJECT_TEMPLATE_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);			
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id				= $id;
				
				$this->name				= $data['name'];
				$this->cat_id			= $data['cat_id'];
				$this->template_id		= $data['template_id'];
				
				if ($data['parent_template'] == "t")
				{
					$this->parent_template	= true;
				}
				else
				{
					$this->parent_template	= false;
				}
			}
			else
			{
				$this->id = null;
			}
		}
	}	
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			
			unset($this->name);
			unset($this->cat_id);
			unset($this->parent_template);
			unset($this->template_id);
		}
	}
	
	/**
	 * @param integer $id
	 * @param string $name
	 * @param integer $cat_id
	 * @param bool $parent_template
	 * @param integer $template_id
	 * @return bool
	 */
	public function create($id, $name, $cat_id, $parent_template, $template_id)
	{
		global $db;
		
		if (is_numeric($id) and $name and is_numeric($cat_id) and isset($parent_template) and is_numeric($template_id))
		{
			if ($parent_template == true)
			{
				$parent_template_insert = "t";
			}
			else
			{
				$parent_template_insert = "f";
			}
		
			$sql = "INSERT INTO ".constant("PROJECT_TEMPLATE_TABLE")." " .
							"(id,name,cat_id,parent_template,template_id) " .
							"VALUES (".$id.",'".$name."',".$cat_id.",'".$parent_template_insert."',".$template_id.")";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res) == 1)
			{				
				self::__construct($id);
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
    	
    	if ($this->id)
    	{
    		$tmp_template_id = $this->id;
    		
    		$this->__destruct();
    		
    		$sql = "DELETE FROM ".constant("PROJECT_TEMPLATE_TABLE")." WHERE id = ".$tmp_template_id."";
    		$res = $db->db_query($sql);
    		
    		if ($db->row_count($res) == 1)
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
	 * @return integer
	 */
	public function get_cat_id()
	{
		if ($this->cat_id)
		{
			return $this->cat_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_parent_template()
	{
		if (isset($this->parent_template))
		{
			return $this->parent_template;
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
		if ($this->template_id) {
			return $this->template_id;
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
			$sql = "UPDATE ".constant("PROJECT_TEMPLATE_TABLE")." SET name = '".$name."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
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
	 * @param integer $cat_id
	 * @return bool
	 */
	public function set_cat_id($cat_id)
	{
		global $db;

		if ($this->id and is_numeric($cat_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TEMPLATE_TABLE")." SET cat_id = ".$cat_id." WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
			{
				$this->cat_id = $cat_id;
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
	 * @param bool $parent_template
	 * @return bool
	 */
	public function set_parent_template($parent_template)
	{
		global $db;

		if ($this->id and isset($parent_template))
		{
			if ($parent_template == true)
			{
				$parent_template_insert = "t";
			}
			else
			{
				$parent_template_insert = "f";
			}
			 
			$sql = "UPDATE ".constant("PROJECT_TEMPLATE_TABLE")." SET parent_template = '".$parent_template_insert."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
			{
				$this->parent_template = $parent_template;
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

		if ($this->id and is_numeric($template_id))
		{
			$sql = "UPDATE ".constant("PROJECT_TEMPLATE_TABLE")." SET template_id = ".$template_id." WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
	
		if (is_numeric($id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TEMPLATE_TABLE")." WHERE id=".$id."";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
	
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("PROJECT_TEMPLATE_TABLE")." ORDER BY id";
		$res = $db->db_query($sql);
		
		while ($data = $db->fetch($res))
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
	 * @param $cat_id
	 * @return array
	 */
	public static function list_entries_by_cat_id($cat_id)
	{
		global $db;
	
		if (is_numeric($cat_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PROJECT_TEMPLATE_TABLE")." WHERE cat_id = ".$cat_id." ORDER BY id";
			$res = $db->db_query($sql);
			
			while ($data = $db->fetch($res))
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
		else
		{
			return null;
		}
	}
	
}

?>
