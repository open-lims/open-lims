<?php
/**
 * @package extension
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
 * Extension Access Class
 * @package extension
 */
class Extension_Access
{
	const EXTENSION_PK_SEQUENCE = 'core_extensions_id_seq';

	private $extension_id;
	private $name;
	private $identifier;
	private $folder;
	private $class;
	private $main_file;
	private $version;
	
	/**
	 * @param integer $extension_id
	 */
	function __construct($extension_id)
	{
		global $db;

		if ($extension_id == null)
		{
			$this->extension_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("EXTENSION_TABLE")." WHERE id='".$extension_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->extension_id 	= extension_id;
				$this->name				= $data['name'];
				$this->identifier		= $data['identifier'];
				$this->folder			= $data['folder'];
				$this->class			= $data['class'];
				$this->main_file		= $data['main_file'];
				$this->version			= $data['version'];
			}
			else
			{
				$this->extension_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->extension_id)
		{
			unset($this->extension_id);
			unset($this->name);
			unset($this->identifier);
			unset($this->folder);
			unset($this->class);
			unset($this->main_file);
			unset($this->version);
		}
	}
	
	/**
	 * @param string $name
	 * @param string $identifier
	 * @param string $folder
	 * @param string $class
	 * @return integer
	 */
	public function create($name, $identifier, $folder, $class, $main_file)
	{
		global $db;
		
		if ($name and $identifier and $folder and $class and $main_file)
		{

			$sql_write = "INSERT INTO ".constant("EXTENSION_TABLE")." (id,name,identifier,folder,class,main_file,version) " .
						"VALUES (nextval('".self::EXTENSION_PK_SEQUENCE."'::regclass),'".$name."','".$identifier."','".$folder."','".$class."','".$main_file."',NULL)";

			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("EXTENSION_TABLE")." WHERE id = currval('".self::EXTENSION_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->extension_id)
		{
			$tmp_extension_id = $this->extension_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("EXTENSION_TABLE")." WHERE id = ".$tmp_extension_id."";
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
	public function get_identifier()
	{
		if ($this->identifier)
		{
			return $this->identifier;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_folder()
	{
		if ($this->folder)
		{
			return $this->folder;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_class()
	{
		if ($this->class)
		{
			return $this->class;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_main_file()
	{
		if ($this->main_file)
		{
			return $this->main_file;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;
			
		if ($this->extension_id and $name)
		{
			$sql = "UPDATE ".constant("EXTENSION_TABLE")." SET name = '".$name."' WHERE id = '".$this->extension_id."'";
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
	 * @param string $identifier
	 * @return bool
	 */
	public function set_identifier($identifier)
	{
		global $db;
			
		if ($this->extension_id and $identifier)
		{
			$sql = "UPDATE ".constant("EXTENSION_TABLE")." SET identifier = '".$identifier."' WHERE id = '".$this->extension_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->identifier = $identifier;
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
	 * @param string $folder
	 * @return bool
	 */
	public function set_folder($folder)
	{
		global $db;
			
		if ($this->extension_id and $folder)
		{
			$sql = "UPDATE ".constant("EXTENSION_TABLE")." SET folder = '".$folder."' WHERE id = '".$this->extension_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->folder = $folder;
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
	 * @param string $class
	 * @return bool
	 */
	public function set_class($class)
	{
		global $db;
			
		if ($this->extension_id and $class)
		{
			$sql = "UPDATE ".constant("EXTENSION_TABLE")." SET class = '".$class."' WHERE id = '".$this->extension_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->class = $class;
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
	 * @param string $main_file
	 * @return bool
	 */
	public function set_main_file($main_file)
	{
		global $db;
			
		if ($this->extension_id and $main_file)
		{
			$sql = "UPDATE ".constant("EXTENSION_TABLE")." SET main_file = '".$main_file."' WHERE id = '".$this->extension_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->main_file = $main_file;
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
	 * @param string $version
	 * @return bool
	 */
	public function set_version($version)
	{
		global $db;
			
		if ($this->extension_id and $version)
		{
			$sql = "UPDATE ".constant("EXTENSION_TABLE")." SET version = '".$version."' WHERE id = '".$this->extension_id."'";
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
		
		$return_array = array();
		$counter = 0;
		
		$sql = "SELECT id,name,identifier,folder,class,main_file FROM ".constant("EXTENSION_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			$return_array[$counter]['id'] = $data['id'];
			$return_array[$counter]['name'] = $data['name'];
			$return_array[$counter]['identifier'] = $data['identifier'];
			$return_array[$counter]['folder'] = $data['folder'];
			$return_array[$counter]['class'] = $data['class'];
			$return_array[$counter]['main_file'] = $data['main_file'];
			$counter++;
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
	 * @return array
	 */
	public static function list_folder_entries()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT id,folder FROM ".constant("EXTENSION_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			$return_array[$data['id']] = $data['folder'];
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
	 * @param string $identifier
	 * @return array
	 */
	public static function get_id_by_identifier($identifier)
	{
		global $db;

		if ($identifier)
		{
			$sql = "SELECT id FROM ".constant("EXTENSION_TABLE")." WHERE LOWER(identifier) = '".trim(strtolower($identifier))."'";
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
}
	
?>
