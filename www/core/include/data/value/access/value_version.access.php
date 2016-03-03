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
 * Value Version Access Class
 * @package data
 */
class ValueVersion_Access
{
	const VALUE_VERSION_PK_SEQUENCE = 'core_value_versions_id_seq';

	private $value_version_id;
	
	private $toid;
	private $version;
	private $value;
	private $checksum;
	private $datetime;
	private $language_id;
	private $previous_version_id;
	private $internal_revision;
	private $current;
	private $owner_id;
	private $name;
	
	/**
	 * @param integer $value_version_id
	 */
	function __construct($value_version_id)
	{
		global $db;
		
		if ($value_version_id == null)
		{
			$this->value_version_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("VALUE_VERSION_TABLE")." WHERE id='".$value_version_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->value_version_id		= $value_version_id;
				
				$this->toid					= $data['toid'];
				$this->version				= $data['version'];
				$this->value				= $data['value'];
				$this->checksum				= $data['checksum'];
				$this->datetime				= $data['datetime'];
				$this->language_id			= $data['language_id'];
				$this->previous_version_id	= $data['previous_version_id'];
				$this->internal_revision	= $data['internal_revision'];
				$this->owner_id				= $data['owner_id'];
				$this->name					= $data['name'];
				
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
				$this->value_version_id		= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->value_version_id)
		{
			unset($this->value_version_id);
			unset($this->toid);
			unset($this->version);
			unset($this->value);
			unset($this->checksum);
			unset($this->datetime);
			unset($this->language_id);
			unset($this->previous_version_id);
			unset($this->internal_revision);
			unset($this->current);
			unset($this->owner_id);
			unset($this->name);
		}
	}
	
	/**
	 * @param integer $toid
	 * @param integer $version
	 * @param string $value
	 * @param string $checksum
	 * @param integer $previous_version_id
	 * @param integer $internal_revision
	 * @param bool $current
	 * @param integer $owner_id
	 * @return integer
	 */
	public function create($toid, $version, $value, $checksum, $previous_version_id, $internal_revision, $current, $owner_id)
	{
		global $db;
		
		if (is_numeric($toid) and is_numeric($version) and $value and $checksum and is_numeric($owner_id))
		{
			if (is_numeric($previous_version_id))
			{
				$previous_version_id_insert = $previous_version_id;
			}
			else
			{
				$previous_version_id_insert = "currval('".self::VALUE_VERSION_PK_SEQUENCE."'::regclass)";
			}
			
			if ($current == true)
			{
				$current_insert = "'t'";
			}
			else
			{
				$current_insert = "'f'";
			}
						
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("VALUE_VERSION_TABLE")." (id,toid,version,value,checksum,datetime,language_id,previous_version_id,internal_revision,current,owner_id,name) " .
					"VALUES (nextval('".self::VALUE_VERSION_PK_SEQUENCE."'::regclass),".$toid.",".$version.",'".$value."','".$checksum."','".$datetime."',1,".$previous_version_id_insert.",".$internal_revision.",".$current_insert.",".$owner_id.",NULL)";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE id = currval('".self::VALUE_VERSION_PK_SEQUENCE."'::regclass)";
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

		if ($this->value_version_id)
		{
			$value_version_id_tmp = $this->value_version_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("VALUE_VERSION_TABLE")." WHERE id = ".$value_version_id_tmp."";
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
		if ($this->value_version_id)
		{
			return $this->value_version_id;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_toid()
	{
		if ($this->toid)
		{
			return $this->toid;
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
	 * @return string
	 */
	public function get_value()
	{
		if ($this->value)
		{
			return $this->value;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_checksum()
	{
		if ($this->checksum)
		{
			return $this->checksum;
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
	 * @return integer
	 */
	public function get_language_id()
	{
		if ($this->language_id)
		{
			return $this->language_id;
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
	 * @return bool
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
	 * @return integer
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
	 * @param integer $toid
	 * @return bool
	 */
	public function set_toid($toid)
	{
		global $db;
	
		if ($this->value_version_id and is_numeric($toid))
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET toid = ".$toid." WHERE id = ".$this->value_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->toid = $toid;
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

		if ($this->value_version_id and is_numeric($version))
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET version = ".$version." WHERE id = ".$this->value_version_id."";
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
	 * @param string $value
	 * @return bool
	 */
	public function set_value($value)
	{	
		global $db;

		if ($this->value_version_id and $value)
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET value = '".$value."' WHERE id = ".$this->value_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->value = $value;
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
	 * @param string $string
	 * @param string $language_name
	 * @return bool
	 */
	public function set_text_search_vector($string, $language_name)
	{	
		global $db;
			
		if ($this->value_version_id and $string)
		{
			if ($language_name == null)
			{
				$language_name_insert = "default";
			}
			else
			{
				$language_name_insert = $language_name;
			}
			
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET text_search_vector = to_tsvector('".$language_name_insert."','".$string."') WHERE id = ".$this->value_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
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
	 * @param string $checksum
	 * @return bool
	 */
	public function set_checksum($checksum)
	{
		global $db;
	
		if ($this->value_version_id and $checksum)
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET checksum = '".$checksum."' WHERE id = ".$this->value_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->checksum = $checksum;
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
	
		if ($this->value_version_id and $datetime)
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->value_version_id."";
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
	 * @param integer $language_id
	 * @return bool
	 */
	public function set_language_id($language_id)
	{	
		global $db;

		if ($this->value_version_id and is_numeric($language_id))
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET language_id = '".$language_id."' WHERE id = ".$this->value_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->language_id = $language_id;
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

		if ($this->value_version_id and is_numeric($previous_version_id))
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET previous_version_id = '".$previous_version_id."' WHERE id = ".$this->value_version_id."";
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
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function set_internal_revision($internal_revision)
	{		
		global $db;

		if ($this->value_version_id and is_numeric($internal_revision))
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET internal_revision = '".$internal_revision."' WHERE id = ".$this->value_version_id."";
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
	 * @param bool $current
	 * @return bool
	 */
	public function set_current($current)
	{	
		global $db;
	
		if ($this->value_version_id and isset($current))
		{
			if ($current == true)
			{
				$current_insert = 't';
			}
			else
			{
				$current_insert = 'f';
			}
			
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET current= '".$current_insert."' WHERE id = ".$this->value_version_id."";
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

		if ($this->value_version_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET owner_id = '".$owner_id."' WHERE id = ".$this->value_version_id."";
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;
	
		if ($this->value_version_id and $name)
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET name = '".$name."' WHERE id = ".$this->value_version_id."";
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
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function exist_value_version_by_internal_revision($internal_revision)
	{
		global $db;
			
		if ($this->toid and is_numeric($internal_revision))
		{
			$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE internal_revision = ".$internal_revision." AND toid = '".$this->toid."'";
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
	 * @param integer $value_id
	 * @param integer $internal_revision
	 * @return bool
	 */
	public static function exist_internal_revision($value_id, $internal_revision)
	{
		global $db;

		if (is_numeric($value_id) and is_numeric($internal_revision))
		{
			$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE toid = '".$value_id."' AND internal_revision = '".$internal_revision."'";
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
	 * @param integer $toid
	 * @return array
	 */
	public static function list_entries_by_toid($toid)
	{
		global $db;

		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT * FROM get_all_value_versions(".$toid.", NULL) AS result";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data['result']);	
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
			
			$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND id != previous_version_id";
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
	 * @param integer $toid
	 * @return integer
	 */
	public static function get_number_of_root_major_versions_by_toid($toid)
	{
		global $db;
	
		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(id) AS numberofresults FROM ".constant("VALUE_VERSION_TABLE")." WHERE toid = ".$toid." AND id = previous_version_id";
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
	 * @param integer $id
	 * @return integer
	 */
	public static function get_highest_minor_version_entry_by_id($id)
	{
		global $db;
	
		if (is_numeric($id))
		{
			$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE previous_version_id = ".$id." " .
							"AND version = (SELECT MAX(version) FROM ".constant("VALUE_VERSION_TABLE")." WHERE previous_version_id = ".$id." AND previous_version_id != id) AND previous_version_id != id";				
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
	 * @param integer $toid
	 * @param integer $previous_version_id
	 * @return integer
	 */
	public static function get_highest_major_version_entry_by_toid_and_previous_version_id($toid, $previous_version_id)
	{	
		global $db;
	
		if (is_numeric($toid))
		{
			if (!is_numeric($previous_version_id))
			{
				$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE previous_version_id = id AND toid = ".$toid."" .
						"AND version = (SELECT MAX(version) FROM ".constant("VALUE_VERSION_TABLE")." WHERE previous_version_id = id AND toid = ".$toid.")";				
			}
			else
			{
				$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND toid = ".$toid."" .
						"AND version = (SELECT MAX(version) FROM ".constant("VALUE_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND toid = ".$toid." AND previous_version_id != id) AND previous_version_id != id";				
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
	 * @param integer $toid
	 * @param integer $internal_revision
	 * @return integer
	 */
	public static function get_last_uploaded_version_entry_by_toid($toid, $internal_revision)
	{
		global $db;
	
		if (is_numeric($toid) and is_numeric($internal_revision))
		{
			$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE toid = ".$toid." AND internal_revision != ".$internal_revision." " .
						"AND datetime = (SELECT MAX(datetime) FROM ".constant("VALUE_VERSION_TABLE")." WHERE toid = ".$toid." AND internal_revision != ".$internal_revision.")";				
			
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
	 * @param integer $toid
	 * @param integer $internal_revision
	 * @return integer
	 */
	public static function get_entry_by_toid_and_internal_revision($toid, $internal_revision)
	{
		global $db;

		if (is_numeric($toid) and is_numeric($internal_revision))
		{
			$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE toid = ".$toid." " .
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
	 * @param integer $toid
	 * @return integer
	 */
	public static function get_current_entry_by_toid($toid)
	{
		global $db;

		if (is_numeric($toid))
		{
			$sql = "SELECT id FROM ".constant("VALUE_VERSION_TABLE")." WHERE toid = ".$toid." " .
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
	 * @param integer $owner_id
	 * @return bool
	 */
	public static function set_owner_id_on_null($owner_id)
	{
		global $db;

		if (is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("VALUE_VERSION_TABLE")." SET owner_id = NULL WHERE owner_id = '".$owner_id."'";				
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
