<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
 * File Version Access Class
 * @package data
 */
class FileVersion_Access
{
	const FILE_VERSION_PK_SEQUENCE = 'core_file_versions_id_seq';

	private $file_version_id;
	
	private $toid;
	private $name;
	private $version;
	private $size;
	private $checksum;
	private $datetime;
	private $comment;
	private $previous_version_id;
	private $internal_revision;
	private $current;
	private $file_extension;
	private $owner_id;

	/**
	 * @param integer $file_version_id
	 */
	function __construct($file_version_id)
	{
		global $db;
		
		if ($file_version_id == null)
		{
			$this->file_version_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("FILE_VERSION_TABLE")." WHERE id='".$file_version_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->file_version_id		= $file_version_id;
				
				$this->toid					= $data[toid];
				$this->name					= $data[name];
				$this->version				= $data[version];
				$this->size					= $data[size];
				$this->checksum				= $data[checksum];
				$this->datetime				= $data[datetime];
				$this->comment				= $data[comment];
				$this->previous_version_id	= $data[previous_version_id];
				$this->internal_revision	= $data[internal_revision];
				$this->file_extension		= $data[file_extension];
				$this->owner_id				= $data[owner_id];
				
				if ($data[current] == 't')
				{
					$this->current			= true;
				}
				else
				{
					$this->current			= false;
				}
			}
			else
			{
				$this->file_version_id		= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->file_version_id)
		{
			unset($this->file_version_id);
	
			unset($this->toid);
			unset($this->name);
			unset($this->version);
			unset($this->size);
			unset($this->checksum);
			unset($this->datetime);
			unset($this->comment);
			unset($this->previous_version_id);
			unset($this->internal_revision);
			unset($this->current);
			unset($this->file_extension);
		}
	}

	/**
	 * @param integer $toid
	 * @param integer $veriosn
	 * @param integer $size
	 * @param string $checksum
	 * @param string $comment
	 * @param integer $previous_version_id
	 * @param integer $internal_revision
	 * @param bool $current
	 * @param integer $owner_id
	 * @return integer
	 */
	public function create($toid, $name, $version, $size, $checksum, $comment, $previous_version_id, $internal_revision, $current, $owner_id)
	{
		global $db;
		
		if (is_numeric($toid) and $name and is_numeric($version) and $size and $checksum and is_numeric($owner_id))
		{
			if ($comment)
			{
				$comment_insert = "'".$comment."'";
			}
			else
			{
				$comment_insert = "NULL";
			}
			
			if (is_numeric($previous_version_id))
			{
				$previous_version_id_insert = $previous_version_id;
			}
			else
			{
				$previous_version_id_insert = "currval('".self::FILE_VERSION_PK_SEQUENCE."'::regclass)";
			}
			
			if ($current == true)
			{
				$current_insert = "'t'";
			}
			else
			{
				$current_insert = "'f'";
			}
			
			$extension_array = explode(".", $name);
 			$extension_count = substr_count($name, ".");
 			
 			if ($extension_count >= 1)
 			{
 				$extension_insert = "'".$extension_array[$extension_count]."'";
 			}
 			else
 			{
 				$extension_insert = "NULL";
 			}
			
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("FILE_VERSION_TABLE")." (id,toid,name,version,size,checksum,datetime,comment,previous_version_id,internal_revision,current,file_extension,owner_id) " .
					"VALUES (nextval('".self::FILE_VERSION_PK_SEQUENCE."'::regclass),".$toid.",'".$name."',".$version.",".$size.",'".$checksum."','".$datetime."',".$comment_insert.",".$previous_version_id_insert.",".$internal_revision.",".$current_insert.",".$extension_insert.",".$owner_id.")";
					
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE id = currval('".self::FILE_VERSION_PK_SEQUENCE."'::regclass)";
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

		if ($this->file_version_id)
		{
			$file_version_id_tmp = $this->file_version_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("FILE_VERSION_TABLE")." WHERE id = ".$file_version_id_tmp."";
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
		if ($this->file_version_id)
		{
			return $this->file_version_id;
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
	public function get_size()
	{
		if ($this->size)
		{
			return $this->size;
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
	 * @return string
	 */
	public function get_comment()
	{
		if ($this->comment)
		{
			return $this->comment;
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
	 * @return string
	 */
	public function get_file_extension()
	{
		if ($this->file_extension)
		{
			return $this->file_extension;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
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
	 * @param integer $toid
	 * @return bool
	 */
	public function set_toid($toid)
	{		
		global $db;
		
		if ($this->file_version_id and is_numeric($toid))
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET toid = ".$toid." WHERE id = ".$this->file_version_id."";
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;

		if ($this->file_version_id and $name)
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET name = ".$name." WHERE id = ".$this->file_version_id."";
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
	 * @param integer $version
	 * @return bool
	 */
	public function set_version($version)
	{
		global $db;
	
		if ($this->file_version_id and is_numeric($version))
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET version = ".$version." WHERE id = ".$this->file_version_id."";
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
	 * @param integer $size
	 * @return bool
	 */
	public function set_size($size)
	{
		global $db;
	
		if ($this->file_version_id and $size)
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET size = ".$size." WHERE id = ".$this->file_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->size = $size;
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

		if ($this->file_version_id and $checksum)
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET checksum = ".$checksum." WHERE id = ".$this->file_version_id."";
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
			
		if ($this->file_version_id and $datetime)
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->file_version_id."";
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
	 * @param string $comment
	 * @return bool
	 */
	public function set_comment($comment)
	{		
		global $db;

		if ($this->file_version_id and $comment)
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET comment = ".$comment." WHERE id = ".$this->file_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->comment = $comment;
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

		if ($this->file_version_id and is_numeric($previous_version_id))
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET previous_version_id = ".$previous_version_id." WHERE id = ".$this->file_version_id."";
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

		if ($this->file_version_id and is_numeric($internal_revision))
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET internal_revision = ".$internal_revision." WHERE id = ".$this->file_version_id."";
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
		
		if ($this->file_version_id and isset($current))
		{
			if ($current == true)
			{
				$current_insert = "t";
			}
			else
			{
				$current_insert = "f";
			}
			
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET current = '".$current_insert."' WHERE id = ".$this->file_version_id."";
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
	 * @param string $file_extension
	 * @return bool
	 */
	public function set_file_extension($file_extension)
	{	
		global $db;
		
		if ($this->file_version_id and $file_extension)
		{
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET file_extension = '".$file_extension."' WHERE id = ".$this->file_version_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->file_extension = $file_extension;
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
		
		if ($this->file_version_id and is_numeric($owner_id))
		{			
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET owner_id = '".$owner_id."' WHERE id = ".$this->file_version_id."";
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
	 * @param integer $internal_revision
	 * @return bool
	 */
	public function exist_file_version_by_internal_revision($internal_revision)
	{
		global $db;
			
		if ($this->toid and is_numeric($internal_revision))
		{
			$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE internal_revision = ".$internal_revision." AND toid = '".$this->toid."'";
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
			
			$sql = "SELECT * FROM get_all_file_versions(".$toid.", NULL) AS result";
			$res = $db->db_query($sql);
		
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[result]);	
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
			
			$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND id != previous_version_id";
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
	 * @param integer $toid
	 * @return integer
	 */
	public static function get_number_of_root_major_versions_by_toid($toid)
	{
		global $db;

		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT COUNT(id) AS numberofresults FROM ".constant("FILE_VERSION_TABLE")." WHERE toid = ".$toid." AND id = previous_version_id";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[numberofresults])
			{
				return $data[numberofresults];
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
	public static function get_highest_internal_revision_entry_by_toid($toid)
	{
		global $db;

		if (is_numeric($toid))
		{
			$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE toid = ".$toid." " .
							"AND internal_revision = (SELECT MAX(internal_revision) FROM ".constant("FILE_VERSION_TABLE")." WHERE toid = ".$toid.")";				
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data[id])
			{
				return $data[id];
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
			$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE previous_version_id = ".$id." " .
							"AND version = (SELECT MAX(version) FROM ".constant("FILE_VERSION_TABLE")." WHERE previous_version_id = ".$id." AND previous_version_id != id) AND previous_version_id != id";				
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data[id])
			{
				return $data[id];
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
				$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE previous_version_id = id AND toid = ".$toid."" .
						"AND version = (SELECT MAX(version) FROM ".constant("FILE_VERSION_TABLE")." WHERE previous_version_id = id AND toid = ".$toid.")";				
			}
			else
			{
				$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND toid = ".$toid."" .
						"AND version = (SELECT MAX(version) FROM ".constant("FILE_VERSION_TABLE")." WHERE previous_version_id = ".$previous_version_id." AND toid = ".$toid." AND previous_version_id != id) AND previous_version_id != id";				
			}
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data[id])
			{
				return $data[id];
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
			$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE toid = ".$toid." AND internal_revision != ".$internal_revision." " .
						"AND datetime = (SELECT MAX(datetime) FROM ".constant("FILE_VERSION_TABLE")." WHERE toid = ".$toid." AND internal_revision != ".$internal_revision.")";				
			
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data[id])
			{
				return $data[id];
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
			$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE toid = ".$toid." " .
							"AND internal_revision = ".$internal_revision."";				
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data[id])
			{
				return $data[id];
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
			$sql = "SELECT id FROM ".constant("FILE_VERSION_TABLE")." WHERE toid = ".$toid." " .
							"AND current = 't'";				
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
							
			if ($data[id])
			{
				return $data[id];
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
	 * @param intger $toid
	 * @return string
	 */
	public static function get_file_extension_by_toid($toid)
	{
		global $db;

		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT file_extension FROM ".constant("FILE_VERSION_TABLE")." WHERE toid = ".$toid." AND current = 't'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[file_extension])
			{
				return $data[file_extension];
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
			$sql = "UPDATE ".constant("FILE_VERSION_TABLE")." SET owner_id = NULL WHERE owner_id = '".$owner_id."'";				
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