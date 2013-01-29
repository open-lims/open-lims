<?php
/**
 * @package template
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
 * XML Cacke Element Access Class
 * @package template
 */
class XmlCache_Access
{
	const XML_CACHE_PK_SEQUENCE = 'core_xml_cache_id_seq';

	private $id;
	
	private $data_entity_id;
	private $path;
	private $checksum;
	
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
			$sql = "SELECT * FROM ".constant("XML_CACHE_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->id				= $id;
				$this->data_entity_id	= $data['data_entity_id'];
				$this->path				= $data['path'];
				$this->checksum			= $data['checksum'];
			}
			else
			{
				$this->id		= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->data_entity_id);
			unset($this->path);
			unset($this->checksum);
		}
	}
	
	/**
	 * @param integer $data_entity_id
	 * @param string $path
	 * @param string $checksum
	 * @return integer
	 */
	public function create($data_entity_id, $path, $checksum)
	{
		global $db;
		
		if (is_numeric($data_entity_id) and $path and $checksum)
		{
			$sql_write = "INSERT INTO ".constant("XML_CACHE_TABLE")." (id,data_entity_id,path,checksum) " .
					"VALUES (nextval('".self::XML_CACHE_PK_SEQUENCE."'::regclass),".$data_entity_id.",'".$path."','".$checksum."')";
					
			$db->db_query($sql_write);	
			
			$sql_read = "SELECT id FROM ".constant("XML_CACHE_TABLE")." WHERE id = currval('".self::XML_CACHE_PK_SEQUENCE."'::regclass)";
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
			
			$sql = "DELETE FROM ".constant("XML_CACHE_TABLE")." WHERE id = ".$id_tmp."";
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
	public function get_data_entity_id_id()
	{
		if ($this->data_entity_id)
		{
			return $this->data_entity_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_path()
	{
		if ($this->path)
		{
			return $this->path;
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
	 * @return integer
	 */
	public function set_data_entity_id_id($data_entity_id)
	{
		global $db;
		
		if ($this->id and is_numeric($data_entity_id))
		{
			$sql = "UPDATE ".constant("XML_CACHE_TABLE")." SET data_entity_id = ".$data_entity_id." WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->data_entity_id = $data_entity_id;
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
	 * @param string
	 * @return bool
	 */
	public function set_path($path)
	{	
		global $db;

		if ($this->id and $path)
		{
			$sql = "UPDATE ".constant("XML_CACHE_TABLE")." SET path = ".$path." WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->path = $path;
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
			
		if ($this->id and $checksum)
		{
			$sql = "UPDATE ".constant("XML_CACHE_TABLE")." SET checksum = ".$checksum." WHERE id = ".$this->id."";
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
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_id_by_data_entity_id($data_entity_id)
	{
		global $db;
		
		if (is_numeric($data_entity_id))
		{
			$sql = "SELECT id FROM ".constant("XML_CACHE_TABLE")." WHERE data_entity_id = ".$data_entity_id."";
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
