<?php
/**
 * @package organisation_unit
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
 * Organisation Unit Type Access Class
 * @package organisation_unit
 */
class OrganisationUnitType_Access
{

	const ORGANISATION_UNIT_TYPE_TABLE = 'core_organisation_unit_types';
	const ORGANISATION_UNIT_TYPE_PK_SEQUENCE = 'core_organisation_unit_types_id_seq';

	private $type_id;
	
	private $name;
	private $icon;
	
	/**
	 * @param integer $type_id
	 */
	function __construct($type_id)
	{
		global $db;
		
		if ($type_id == null or !is_numeric($type_id))
		{
			$this->type_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::ORGANISATION_UNIT_TYPE_TABLE." WHERE id = ".$type_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				
				$this->type_id	= $data[type_id];

				$this->name		= $data[name];
				$this->icon		= $data[icon];
				
			}
			else
			{
				$this->type_id = null;
			}			
		}
	}
	
	function __destruct()
	{
		if ($this->type_id)
		{
			unset($this->type_id);
			unset($this->name);
			unset($this->icon);
		}
	}
	
	/**
	 * @todo return type-id and run constructor
	 * @param string $name
	 * @param string $icon
	 * @return integer
	 */
	public function create($name, $icon)
	{
		global $db;

		if ($name and $icon)
		{	
	 		$sql_write = "INSERT INTO ".self::ORGANISATION_UNIT_TYPE_TABLE." (id, name, icon) " .
								"VALUES (nextval('".self::ORGANISATION_UNIT_TYPE_PK_SEQUENCE."'::regclass),'".$name."','".$icon."')";		
				
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) != 1)
			{
				return false;
			}
			else
			{
				return true;
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

		if ($this->type_id)
		{	
			$type_id_tmp = $this->type_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".self::ORGANISATION_UNIT_TYPE_TABLE." WHERE id = ".$type_id_tmp."";
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
	public function get_icon()
	{
		if ($this->icon)
		{
			return $this->icon;
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

		if ($this->type_id and $name)
		{
			$sql = "UPDATE ".self::ORGANISATION_UNIT_TYPE_TABLE." SET name = '".$name."' WHERE id = '".$this->type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->name = $name;
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
	
	/**
	 * @param string $icon
	 * @return bool
	 */
	public function set_icon($icon)
	{
		global $db;
			
		if ($this->type_id and $icon)
		{
			$sql = "UPDATE ".self::ORGANISATION_UNIT_TYPE_TABLE." SET icon = '".$icon."' WHERE id = '".$this->type_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->icon = $icon;
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
		
		$sql = "SELECT id FROM ".self::ORGANISATION_UNIT_TYPE_TABLE." ORDER BY name";
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