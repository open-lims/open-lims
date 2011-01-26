<?php
/**
 * @package sample
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
 * Sample Template Category Access Class
 * @package sample
 */
class SampleTemplateCat_Access
{
	const SAMPLE_TEMPLATE_CAT_TABLE = 'core_sample_template_cats';
	const SAMPLE_TEMPLATE_CAT_PK_SEQUENCE = 'core_sample_template_cats_id_seq';
	
	private $template_cat_id;
	private $name;
	
	/**
	 * @param integer $template_cat_id
	 */
	function __construct($template_cat_id)
	{
		global $db;
			
		if ($template_cat_id == null)
		{
			$this->template_cat_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::SAMPLE_TEMPLATE_CAT_TABLE." WHERE id='".$template_cat_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->template_cat_id	= $template_cat_id;
				
				$this->name				= $data[name];
			}
			else
			{
				$this->template_cat_id	= null;
			}
		}
	}	
	
	function __destruct()
	{
		if ($this->template_cat_id)
		{
			unset($this->template_cat_id);
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
			$sql_write = "INSERT INTO ".self::SAMPLE_TEMPLATE_CAT_TABLE." (id,name) " .
					"VALUES (nextval('".self::SAMPLE_TEMPLATE_CAT_PK_SEQUENCE."'::regclass),'".$name."')";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".self::SAMPLE_TEMPLATE_CAT_TABLE." WHERE id = currval('".self::SAMPLE_TEMPLATE_CAT_PK_SEQUENCE."'::regclass)";
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
    	
    	if ($this->template_cat_id)
    	{
    		$tmp_template_cat_id = $this->template_cat_id;
    		
    		$this->__destruct();
    		
    		$sql = "DELETE FROM ".self::SAMPLE_TEMPLATE_CAT_TABLE." WHERE id = ".$tmp_template_cat_id."";
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
			
		if ($this->template_cat_id and $name)
		{
			$sql = "UPDATE ".self::SAMPLE_TEMPLATE_CAT_TABLE." SET name = '".$name."' WHERE id = ".$this->template_cat_id."";
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
	 * @return bool
	 */
	public static function exist_name($name)
	{
		global $db;
				
		if ($name)
		{
			$name = trim(strtolower($name));
			
			$sql = "SELECT id FROM ".self::SAMPLE_TEMPLATE_CAT_TABLE." WHERE TRIM(LOWER(NAME))='".$name."'";
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
		else{
			return false;
		}
	}
	
	/**
	 * @param integer $id
	 * @return array
	 */
	public static function exist_id($id)
	{
		global $db;
				
		if (is_numeric($id))
		{		
			$sql = "SELECT id FROM ".self::SAMPLE_TEMPLATE_CAT_TABLE." WHERE id='".$id."'";
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
		else{
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
		
		$sql = "SELECT id FROM ".self::SAMPLE_TEMPLATE_CAT_TABLE." ORDER BY id";
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
	
}
?>
