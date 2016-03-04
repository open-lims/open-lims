<?php
/**
 * @package base
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
 * Measuring Unit Category Access Class
 * @package base
 */
class MeasuringUnitCategory_Access
{
	const MEASURING_UNIT_PK_SEQUENCE = 'core_base_measuring_unit_categories_id_seq';
	
	private $id;
	private $name;
	private $created_by_user;
	
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
			$sql = "SELECT * FROM ".constant("MEASURING_UNIT_CATEGORY_TABLE")." WHERE id = ".$id."";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id			= $data['id'];
				$this->name			= $data['name'];
				
				if ($data['created_by_user'] == "t")
				{
					$this->created_by_user = true;
				}
				else
				{
					$this->created_by_user = false;
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
			unset($this->created_by_user);
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
			$sql_write = "INSERT INTO ".constant("MEASURING_UNIT_CATEGORY_TABLE")." (id,name,created_by_user) " .
							"VALUES (nextval('".self::MEASURING_UNIT_PK_SEQUENCE."'::regclass),'".$name."','t')";
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("MEASURING_UNIT_CATEGORY_TABLE")." WHERE id = currval('".self::MEASURING_UNIT_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->fetch($res_read);
				
				self::__construct($data_read['id']);
								
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
    		$tmp_id = $this->id;
    		
    		$this->__destruct();

    		$sql = "DELETE FROM ".constant("MEASURING_UNIT_CATEGORY_TABLE")." WHERE id = ".$tmp_id."";
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
	 * @return bool
	 */
	public function get_created_by_user()
	{
		if (isset($this->created_by_user))
		{
			return $this->created_by_user;
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

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_CATEGORY_TABLE")." SET name = '".$name."' WHERE id = ".$this->id."";
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
			
		if (is_numeric($id))
		{
			$sql = "SELECT id FROM ".constant("MEASURING_UNIT_CATEGORY_TABLE")." WHERE id = '".$id."'";
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
			
		$sql = "SELECT id,name FROM ".constant("MEASURING_UNIT_CATEGORY_TABLE")." ORDER BY id";
		$res = $db->db_query($sql);
		
		while ($data = $db->fetch($res))
		{
			array_push($return_array, array("id" => $data['id'], "name" => $data['name']));
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
     * @param integer $measuring_unit_category_id
     * @return bool
     */
    public static function is_deletable($measuring_unit_category_id)
    {
    	global $db;
    	
   		if(is_numeric($measuring_unit_category_id))
		{
			$sql = "SELECT created_by_user FROM ".constant("MEASURING_UNIT_CATEGORY_TABLE")." WHERE id = '".$measuring_unit_category_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['created_by_user'] == "t")
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