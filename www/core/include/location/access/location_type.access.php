<?php
/**
 * @package location
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
 * Location Type Access Class
 * @package location
 */
class LocationType_Access
{
	const LOCATION_TYPE_PK_SEQUENCE = 'core_location_types_id_seq';

	private $location_type_id;
	private $name;
	
	/**
	 * @param integer $location_id
	 */
	function __construct($location_type_id)
	{
		global $db;

		if ($location_type_id == null)
		{
			$this->location_type_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("LOCATION_TYPE_TABLE")." WHERE id='".$location_type_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->location_type_id = $location_type_id;
				$this->name				= $data['name'];
			}
			else
			{
				$this->location_type_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->location_type_id) {
			unset($this->location_type_id);
			unset($this->name);
		}
	}
	
	/**
	 * @param integer $toid
	 * @param string $name
	 * @return integer
	 */
	public function create($toid, $name)
	{
		global $db;
		
		if ($name)
		{
			$sql_write = "INSERT INTO ".constant("LOCATION_TYPE_TABLE")." (id,name) " .
				"VALUES (nextval('".self::LOCATION_TYPE_PK_SEQUENCE."'::regclass),'".$name."')";

			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("LOCATION_TYPE_TABLE")." WHERE id = currval('".self::LOCATION_TYPE_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->location_type_id)
		{
			$tmp_location_type_id = $this->location_type_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("LOCATION_TYPE_TABLE")." WHERE id = ".$tmp_location_type_id."";
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
			
		if ($this->location_type_id and $name)
		{
			$sql = "UPDATE ".constant("LOCATION_TYPE_TABLE")." SET name = '".$name."' WHERE id = '".$this->location_type_id."'";
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
				
		$return_array = array();
		
		$sql = "SELECT id,name FROM ".constant("LOCATION_TYPE_TABLE")." ORDER BY id";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			$tmp_array = array();
			$tmp_array['id'] = $data['id'];
			$tmp_array['name'] = $data['name'];
			array_push($return_array,$tmp_array);
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
