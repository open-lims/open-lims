<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * File Image Cache Access Class
 * @package data
 */
class FileImageCache_Access
{
	const FILE_IMAGE_CACHE_PK_SEQUENCE = 'core_file_image_cache_id_seq';

	private $id;
	
	private $file_version_id;
	private $width;
	private $height;
	private $size;
	private $last_access;
	
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
			$sql = "SELECT * FROM ".constant("FILE_IMAGE_CACHE_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->id				= $id;
				
				$this->file_version_id	= $data[file_version_id];
				$this->width			= $data[width];
				$this->height			= $data[height];
				$this->size				= $data[height];
				$this->last_access		= $data[last_access];
			}
			else
			{
				$this->id				= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			
			unset($this->file_version_id);
			unset($this->width);
			unset($this->height);
			unset($this->size);
			unset($this->last_access);
		}
	}
	
	/**
	 * @param integer $file_version_id
	 * @param integer $width
	 * @param integer $height
	 * @param integer $size
	 * @return integer
	 */
	public function create($file_version_id, $width, $height, $size)
	{
		global $db;
		
		if (is_numeric($file_version_id) and is_numeric($width) and is_numeric($height))
		{
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("FILE_IMAGE_CACHE_TABLE")." (id,file_version_id,width,height,size,last_access) " .
					"VALUES (nextval('".self::FILE_IMAGE_CACHE_PK_SEQUENCE."'::regclass),".$file_version_id.",".$width.",".$height.",".$size.",'".$datetime."')";
					
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("FILE_IMAGE_CACHE_TABLE")." WHERE id = currval('".self::FILE_IMAGE_CACHE_PK_SEQUENCE."'::regclass)";
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
			
			$sql = "DELETE FROM ".constant("FILE_IMAGE_CACHE_TABLE")." WHERE id = ".$id_tmp."";
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
	public function get_file_version_id()
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
	public function get_width()
	{
		if ($this->width)
		{
			return $this->width;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_height()
	{
		if ($this->height)
		{
			return $this->height;
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
	public function get_last_access()
	{
		if ($this->last_access)
		{
			return $this->last_access;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @param integer $file_version_id
	 * @return bool
	 */
	public function set_file_version_id($file_version_id)
	{		
		global $db;
		
		if ($this->id and is_numeric($file_version_id))
		{
			$sql = "UPDATE ".constant("FILE_IMAGE_CACHE_TABLE")." SET file_version_id = ".$file_version_id." WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->file_version_id = $file_version_id;
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
	 * @param integer $width
	 * @return bool
	 */
	public function set_width($width)
	{		
		global $db;
		
		if ($this->id and is_numeric($width))
		{
			$sql = "UPDATE ".constant("FILE_IMAGE_CACHE_TABLE")." SET width = ".$width." WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->width = $width;
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
	 * @param integer $height
	 * @return bool
	 */
	public function set_height($height)
	{		
		global $db;
		
		if ($this->id and is_numeric($height))
		{
			$sql = "UPDATE ".constant("FILE_IMAGE_CACHE_TABLE")." SET height = ".$height." WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->height = $height;
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
		
		if ($this->id and is_numeric($size))
		{
			$sql = "UPDATE ".constant("FILE_IMAGE_CACHE_TABLE")." SET size = ".$size." WHERE id = ".$this->id."";
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
	 * @param string $last_access
	 * @return bool
	 */
	public function set_last_access($last_access)
	{		
		global $db;
		
		if ($this->id and $last_access)
		{
			$sql = "UPDATE ".constant("FILE_IMAGE_CACHE_TABLE")." SET last_access = '".$last_access."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->last_access = $last_access;
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
	 * @param integer $file_version_id
	 * @param integer $width
	 * @return integer
	 */
	public static function get_width_cached($file_version_id, $width)
	{
		global $db;
		
		if (is_numeric($file_version_id) and is_numeric($width))
		{
			$sql = "SELECT id FROM ".constant("FILE_IMAGE_CACHE_TABLE")." WHERE file_version_id='".$file_version_id."' AND width='".$width."'";
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
	 * @param integer $file_version_id
	 * @param integer $height
	 * @return integer
	 */
	public static function get_height_cached($file_version_id, $height)
	{
		global $db;
		
		if (is_numeric($file_version_id) and is_numeric($height))
		{
			$sql = "SELECT id FROM ".constant("FILE_IMAGE_CACHE_TABLE")." WHERE file_version_id='".$file_version_id."' AND height='".$height."'";
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
	 * @return integer
	 */
	public static function get_cache_size()
	{
		global $db;
		
		$sql = "SELECT SUM(size) AS result FROM ".constant("FILE_IMAGE_CACHE_TABLE")."";
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data['result'])
		{
			return $data['result'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $file_version_id
	 * @return integer
	 */
	public static function list_all_file_version_entries($file_version_id)
	{
		global $db;
		
		if (is_numeric($file_version_id))
		{
			$return_array = array();
			
			$sql = "SELECT id,height,width FROM ".constant("FILE_IMAGE_CACHE_TABLE")." WHERE file_version_id='".$file_version_id."'";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				$temp_array = array();
				$temp_array['id'] = $data['id'];
				$temp_array['height'] = $data['height'];
				$temp_array['width'] = $data['width'];
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
		else
		{
			return null;
		}
	}
}
?>