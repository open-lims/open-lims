<?php
/**
 * @package data
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
 * Parameter Version Access Class
 * @package data
 */
class ParameterVersion_Access
{
	const PARAMETER_VERSION_PK_SEQUENCE = 'core_data_parameter_versions_id_seq';

	private $parameter_version_id;
	
	private $parameter_id;
	private $version;
	private $internal_revision;
	private $previous_version_id;
	private $current;
	private $owner_id;
	private $datetime;
	private $name;
	private $parameter_limit_id;

	/**
	 * @param integer $parameter_version_id
	 */
	function __construct($parameter_version_id)
	{
		global $db;
		
		if ($parameter_version_id == null)
		{
			$this->parameter_version_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE id='".$parameter_version_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->parameter_version_id	= $parameter_version_id;
				$this->parameter_id			= $data['parameter_id'];
				$this->version				= $data['version'];
				$this->internal_revision	= $data['internal_revision'];
				$this->previous_version_id	= $data['previous_version_id'];
				$this->owner_id				= $data['owner_id'];
				$this->datetime				= $data['datetime'];
				$this->name					= $data['name'];
				$this->parameter_limit_id	= $data['parameter_limit_id'];
				
				if ($data['current'] == 't')
				{
					$this->current = true;
				}
				else
				{
					$this->current = false;
				}
			}
			else
			{
				$this->parameter_version_id		= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_version_id)
		{
			unset($this->parameter_version_id);
			unset($this->parameter_id);
			unset($this->version);
			unset($this->internal_revision);
			unset($this->previous_version_id);
			unset($this->current);
			unset($this->owner_id);
			unset($this->datetime);
			unset($this->name);
			unset($this->parameter_limit_id);
		}
	}

	/**
	 * @param integer $parameter_id
	 * @param integer $version
	 * @param integer $internal_revision
	 * @param integer $previous_version_id
	 * @param boolean $current
	 * @param integer $owner_id
	 * @param string $name
	 * @return integer
	 */
	public function create($parameter_id, $version, $internal_revision, $previous_version_id, $current, $owner_id, $name, $parameter_limit_id)
	{
		global $db;
		
		if (is_numeric($parameter_id) and is_numeric($version) and is_numeric($internal_revision) and is_numeric($owner_id) and is_numeric($parameter_limit_id))
		{	
			if (is_numeric($previous_version_id))
			{
				$previous_version_id_insert = $previous_version_id;
			}
			else
			{
				$previous_version_id_insert = "currval('".self::PARAMETER_VERSION_PK_SEQUENCE."'::regclass)";
			}
			
			if ($current == true)
			{
				$current_insert = "t";
			}
			else
			{
				$current_insert = "f";
			}
			
			if ($name)
			{
				$name_insert = "'".$name."'";
			}
			else
			{
				$name_insert = "NULL";
			}

			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("PARAMETER_VERSION_TABLE")." (id,parameter_id,version,internal_revision,previous_version_id,current,owner_id,datetime,name,parameter_limit_id) " .
					"VALUES (nextval('".self::PARAMETER_VERSION_PK_SEQUENCE."'::regclass),'".$parameter_id."','".$version."','".$internal_revision."',".$previous_version_id_insert.",'".$current_insert."','".$owner_id."','".$datetime."',".$name_insert.",".$parameter_limit_id.")";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE id = currval('".self::PARAMETER_VERSION_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
									
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

		if ($this->parameter_version_id)
		{
			$parameter_version_id_tmp = $this->parameter_version_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE id = ".$parameter_version_id_tmp."";
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
	public function get_id()
	{
		if ($this->parameter_version_id)
		{
			return $this->parameter_version_id;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_parameter_id()
	{
		if ($this->parameter_id)
		{
			return $this->parameter_id;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return integer
	 */
	public function get_version()
	{
		if ($this->version)
		{
			return $this->version;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_internal_revision()
	{
		if ($this->internal_revision)
		{
			return $this->internal_revision;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_previous_version_id()
	{
		if ($this->previous_version_id)
		{
			return $this->previous_version_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return boolean
	 */
	public function get_current()
	{
		if (isset($this->current))
		{
			return $this->current;
		}
		else
		{
			return false;
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
	public function get_parameter_limit_id()
	{
		if ($this->parameter_limit_id)
		{
			return $this->parameter_limit_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $parameter_id
	 * @return bool
	 */
	public function set_parameter_id($parameter_id)
	{	
		global $db;

		if ($this->parameter_version_id and is_numeric($parameter_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET parameter_id = '".$parameter_id."' WHERE id = ".$this->parameter_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->parameter_id = $parameter_id;
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
	 * @param integer $version
	 * @return bool
	 */
	public function set_version($version)
	{	
		global $db;

		if ($this->parameter_version_id and is_numeric($version))
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET version = '".$version."' WHERE id = ".$this->parameter_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->version = $version;
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
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function set_internal_revision($internal_revision)
	{	
		global $db;

		if ($this->parameter_version_id and is_numeric($internal_revision))
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET internal_revision = '".$internal_revision."' WHERE id = ".$this->parameter_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->internal_revision = $internal_revision;
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
	 * @param integer $previous_version_id
	 * @return bool
	 */
	public function set_previous_version_id($previous_version_id)
	{	
		global $db;

		if ($this->parameter_version_id and is_numeric($previous_version_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET previous_version_id = '".$previous_version_id."' WHERE id = ".$this->parameter_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->previous_version_id = $previous_version_id;
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
	 * @param boolean $current
	 * @return bool
	 */
	public function set_current($current)
	{	
		global $db;
		
		if ($this->parameter_version_id and isset($current))
		{
			if ($current === true)
			{
				$current_insert = "t";
			}
			else
			{
				$current_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET current = '".$current_insert."' WHERE id = ".$this->parameter_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->current = $current;
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

		if ($this->parameter_version_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET owner_id = '".$owner_id."' WHERE id = ".$this->parameter_version_id."";
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{	
		global $db;

		if ($this->parameter_version_id and $datetime)
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->parameter_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->datetime = $datetime;
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

		if ($this->parameter_version_id and $name)
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET name = '".$name."' WHERE id = ".$this->parameter_version_id."";
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
	 * @param integer $parameter_limit_id
	 * @return bool
	 */
	public function set_parameter_limit_id($parameter_limit_id)
	{	
		global $db;

		if ($this->parameter_version_id and is_numeric($parameter_limit_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET parameter_limit_id = '".$parameter_limit_id."' WHERE id = ".$this->parameter_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->parameter_limit_id = $parameter_limit_id;
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
	 * @param integer $value_id
	 * @param integer $internal_revision
	 * @return bool
	 */
	public static function exist_internal_revision($parameter_id, $internal_revision)
	{
		global $db;

		if (is_numeric($parameter_id) and is_numeric($internal_revision))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE parameter_id = '".$parameter_id."' AND internal_revision = '".$internal_revision."'";
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
	 * @param integer $parameter_id
	 * @return array
	 */
	public static function list_entries_by_parameter_id($parameter_id)
	{
		global $db;

		if (is_numeric($parameter_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE parameter_id = '".$parameter_id."'";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
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
	
	/**
	 * @param integer $previous_version_id
	 * @return array
	 */
	public static function list_entries_by_previous_version_id($previous_version_id)
	{
		global $db;

		if (is_numeric($previous_version_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND id != previous_version_id";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
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
	
	/**
	 * @param integer $parameter_id
	 * @param integer $internal_revision
	 * @return integer
	 */
	public static function get_last_uploaded_version_entry_by_parameter_id($parameter_id, $internal_revision)
	{
		global $db;
	
		if (is_numeric($parameter_id) and is_numeric($internal_revision))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE parameter_id = ".$parameter_id." AND internal_revision != ".$internal_revision." " .
						"AND datetime = (SELECT MAX(datetime) FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE parameter_id = ".$parameter_id." AND internal_revision != ".$internal_revision.")";				
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data['id'])
			{
				return $data['id'];
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
	 * @param integer $parameter_id
	 * @return integer
	 */
	public static function get_number_of_root_major_versions_by_parameter_id($parameter_id)
	{
		global $db;
	
		if (is_numeric($parameter_id))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(id) AS numberofresults FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE parameter_id = ".$parameter_id." AND id = previous_version_id";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['numberofresults'])
			{
				return $data['numberofresults'];
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
	 * @param integer $parameter_id
	 * @return integer
	 */
	public static function get_current_entry_by_parameter_id($parameter_id)
	{
		global $db;

		if (is_numeric($parameter_id))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE parameter_id = ".$parameter_id." " .
							"AND current = 't'";				
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data['id'])
			{
				return $data['id'];
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
	 * @param integer $id
	 * @return integer
	 */
	public static function get_highest_minor_version_entry_by_id($id)
	{
		global $db;
	
		if (is_numeric($id))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE previous_version_id = ".$id." " .
							"AND version = (SELECT MAX(version) FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE previous_version_id = ".$id." AND previous_version_id != id) AND previous_version_id != id";				
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data['id'])
			{
				return $data['id'];
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
	 * @param integer $parameter_id
	 * @param integer $previous_version_id
	 * @return integer
	 */
	public static function get_highest_major_version_entry_by_parameter_id_and_previous_version_id($parameter_id, $previous_version_id)
	{	
		global $db;
	
		if (is_numeric($parameter_id))
		{
			if (!is_numeric($previous_version_id))
			{
				$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE previous_version_id = id AND parameter_id = ".$parameter_id."" .
						"AND version = (SELECT MAX(version) FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE previous_version_id = id AND parameter_id = ".$parameter_id.")";				
			}
			else
			{
				$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND parameter_id = ".$parameter_id."" .
						"AND version = (SELECT MAX(version) FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND parameter_id = ".$parameter_id." AND previous_version_id != id) AND previous_version_id != id";				
			}
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data['id'])
			{
				return $data['id'];
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
	 * @param integer $parameter_id
	 * @param integer $internal_revision
	 * @return integer
	 */
	public static function get_entry_by_parameter_id_and_internal_revision($parameter_id, $internal_revision)
	{
		global $db;

		if (is_numeric($parameter_id) and is_numeric($internal_revision))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_VERSION_TABLE")." WHERE parameter_id = ".$parameter_id." " .
							"AND internal_revision = ".$internal_revision."";				
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data['id'])
			{
				return $data['id'];
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
	 * @return bool
	 */
	public static function set_owner_id_on_null($owner_id)
	{
		global $db;

		if (is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_VERSION_TABLE")." SET owner_id = NULL WHERE owner_id = '".$owner_id."'";				
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
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