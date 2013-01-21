<?php
/**
 * @package base
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
 * Country Access Class
 * @package base
 */
class Country_Access
{
	const COUNTRY_PK_SEQUENCE = 'core_countries_id_seq';
	
	private $id;
	
	private $english_name;
	private $local_name;
	private $iso_3166;
	
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
			$sql = "SELECT * FROM ".constant("COUNTRY_TABLE")." WHERE id = ".$id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->id			= $data['id'];
				
				$this->english_name	= $data['english_name'];
				$this->local_name	= $data['local_name'];
				$this->iso_3166		= $data['iso_3166'];
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
			unset($this->english_name);
			unset($this->local_name);
			unset($this->iso_3166);
		}
	}
	
	/**
	 * @param string $english_name
	 * @param string $local_name
	 * @param string $iso_3166
	 * @return integer
	 */
	public function create($english_name, $local_name, $iso_3166)
	{
		global $db;
		
		if ($english_name and $local_name and $iso_3166)
		{
			$sql_write = "INSERT INTO ".constant("COUNTRY_TABLE")." (id,english_name,local_name,iso_3166) " .
							"VALUES (nextval('".self::COUNTRY_PK_SEQUENCE."'::regclass),'".$english_name."','".$local_name."','".$iso_3166."')";
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("COUNTRY_TABLE")." WHERE id = currval('".self::COUNTRY_PK_SEQUENCE."'::regclass)";
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
    		$tmp_id = $this->id;
    		
    		$this->__destruct();

    		$sql = "DELETE FROM ".constant("COUNTRY_TABLE")." WHERE id = ".$tmp_id."";
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
	public function get_english_name()
	{
		if ($this->english_name)
		{
			return $this->english_name;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_local_name()
	{
		if ($this->local_name)
		{
			return $this->local_name;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_iso_3166()
	{
		if ($this->iso_3166)
		{
			return $this->iso_3166;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $english_name
	 * @return bool
	 */
	public function set_english_name($english_name)
	{
		global $db;

		if ($this->id and $english_name)
		{
			$sql = "UPDATE ".constant("COUNTRY_TABLE")." SET english_name = '".$english_name."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->english_name = $english_name;
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
	 * @param string $local_name
	 * @return bool
	 */
	public function set_local_name($local_name)
	{
		global $db;

		if ($this->id and $local_name)
		{
			$sql = "UPDATE ".constant("COUNTRY_TABLE")." SET local_name = '".$local_name."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->local_name = $local_name;
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
	 * @param string $iso_3166
	 * @return bool
	 */
	public function set_iso_3166($iso_3166)
	{
		global $db;

		if ($this->id and $iso_3166)
		{
			$sql = "UPDATE ".constant("COUNTRY_TABLE")." SET iso_3166 = '".$iso_3166."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->iso_3166 = $iso_3166;
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
			$sql = "SELECT id FROM ".constant("COUNTRY_TABLE")." WHERE id = '".$id."'";
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
			
		$return_array = array();
		
		$sql = "SELECT id,english_name,local_name,iso_3166 FROM ".constant("COUNTRY_TABLE")." ORDER BY english_name";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			$temp_array = array();
			$temp_array['id'] = $data['id'];
			$temp_array['english_name'] = $data['english_name'];
			$temp_array['local_name'] = $data['local_name'];
			$temp_array['iso_3166'] = $data['iso_3166'];
			array_push($return_array,$temp_array);
			unset($temp_array);
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