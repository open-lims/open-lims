<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * Base Module Link Access Class
 * @package base
 */
class BaseModuleLink_Access
{
	const BASE_MODULE_LINK_PK_SEQUENCE = 'core_base_module_links_id_seq';
	
	private $id;
	private $module_id;
	private $link_type;
	private $link_array;
	private $link_file;
	private $weight;
	private $disabled;
	
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
			$sql = "SELECT * FROM ".constant("BASE_MODULE_LINK_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->id 				= $id;
				$this->module_id		= $data[module_id];
				$this->link_type		= $data[link_type];
				$this->link_array		= $data[link_array];
				$this->link_file		= $data[link_file];
				$this->weight			= $data[weight];
				
				if ($data[disabled] == 't')
				{
					$this->disabled = true;
				}
				else
				{
					$this->disabled = false;
				}
			}
			else
			{
				$this->id			= null;
			}				
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->module_id);
			unset($this->link_type);
			unset($this->link_array);
			unset($this->link_file);
			unset($this->weight);
		}
	}
	
	/**
	 * @param integer $module_id
	 * @param string $dialog_type
	 * @param string $name
	 * @param string $class
	 * @param string $method
	 * @return integer
	 */
	public function create($module_id, $link_type, $link_array, $link_file, $weight)
	{
		global $db;

		if (is_numeric($module_id) and $link_type and $link_array)
		{
	 		if (is_numeric($weight))
	 		{
	 			$weight_insert = $weight;
	 		}
	 		else
	 		{
	 			$weight_insert = "NULL";
	 		}
	 		
	 		if ($link_file)
	 		{
	 			$link_file_insert = "'".$link_file."'";
	 		}
	 		else
	 		{
	 			$link_file_insert = "NULL";
	 		}
			
			$sql_write = "INSERT INTO ".constant("BASE_MODULE_LINK_TABLE")." (id, module_id, link_type, link_array, link_file, weight, disabled) " .
								"VALUES (nextval('".self::BASE_MODULE_LINK_PK_SEQUENCE."'::regclass),'".$module_id."','".$link_type."','".$link_array."',".$link_file_insert.",".$weight_insert.", 'f')";		
				
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_MODULE_LINK_TABLE")." WHERE id = currval('".self::BASE_MODULE_LINK_PK_SEQUENCE."'::regclass)";
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

		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("BASE_MODULE_LINK_TABLE")." WHERE id = '".$id_tmp."'";
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
	public function get_module_id()
	{
		if ($this->module_id)
		{
			return $this->module_id;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_link_type()
	{
		if ($this->link_type)
		{
			return $this->link_type;
		}
		else
		{
			return null;
		}	
	}

	/**
	 * @return string
	 */
	public function get_link_array()
	{
		if ($this->link_array)
		{
			return $this->link_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_link_file()
	{
		if ($this->link_file)
		{
			return $this->link_file;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_weight()
	{
		if ($this->weight)
		{
			return $this->weight;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return bool
	 */
	public function get_disabled()
	{
		if (isset($this->disabled))
		{
			return $this->disabled;
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
	public function set_module_id($module_id)
	{
		global $db;

		if ($this->id and is_numeric($module_id))
		{
			$sql = "UPDATE ".constant("BASE_MODULE_LINK_TABLE")." SET module_id = '".$module_id."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->module_id = $module_id;
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
	 * @param string $link_type
	 * @return bool
	 */
	public function set_link_type($link_type)
	{
		global $db;

		if ($this->id and $link_type)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_LINK_TABLE")." SET link_type = '".$link_type."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->link_type = $link_type;
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
	 * @param string $link_array
	 * @return bool
	 */
	public function set_link_array($link_array)
	{
		global $db;

		if ($this->id and $link_array)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_LINK_TABLE")." SET link_array = '".$link_array."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->link_array = $link_array;
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
	 * @param string $link_file
	 * @return bool
	 */
	public function set_link_file($link_file)
	{
		global $db;

		if ($this->id and $link_file)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_LINK_TABLE")." SET link_file = '".$link_file."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->link_file = $link_file;
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
	 * @param integer $weight
	 * @return bool
	 */
	public function set_weight($weight)
	{
		global $db;

		if ($this->id and is_numeric($weight))
		{
			$sql = "UPDATE ".constant("BASE_MODULE_LINK_TABLE")." SET weight = '".$weight."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->weight = $weight;
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
	 * @param bool $disabled
	 * @return bool
	 */
	public function set_disabled($disabled)
	{
		global $db;

		if ($this->id and isset($disabled))
		{
			if ($disabled == true)
			{
				$disabled_insert = "t";
			}
			else
			{
				$disabled_insert = "f";
			}
			
			$sql = "UPDATE ".constant("BASE_MODULE_LINK_TABLE")." SET disabled = '".$disabled_insert."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->disabled = $disabled;
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
	 * @param integer $module_id
	 * @return integer
	 */
	public static function list_id_by_module_id($module_id)
	{
		global $db;
		
		if (is_numeric($module_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("BASE_MODULE_LINK_TABLE")." WHERE module_id = ".$module_id."";
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
	 * @param string $link_type
	 * @return array
	 */
	public static function list_links_by_type($link_type)
	{
		global $db;

		if ($link_type)
		{
			$result_array = array();
			$counter = 0;
			
			$sql = "SELECT * FROM ".constant("BASE_MODULE_LINK_TABLE")." WHERE TRIM(link_type) = '".trim($link_type)."' AND disabled='f' ORDER BY weight";
			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				$result_array[$counter]['file'] 		= $data['link_file'];
				$result_array[$counter]['array'] 		= unserialize($data['link_array']);
				$counter++;
			}
			
			if (is_array($result_array))
			{
				return $result_array;
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
	 * @param integer $module_id
	 * @return bool
	 */
	public static function delete_by_module_id($module_id)
	{
		global $db;

		if (is_numeric($module_id))
		{
			$sql = "DELETE FROM ".constant("BASE_MODULE_LINK_TABLE")." WHERE module_id = '".$module_id."'";
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