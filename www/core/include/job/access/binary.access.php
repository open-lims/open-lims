<?php
/**
 * @package job
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
 * @todo move to base (later)
 * Binary Access Class
 * @package job
 */
class Binary_Access
{
	const BINARY_PK_SEQUENCE = 'core_binaries_id_seq';

	private $binary_id;
	private $path;
	private $file;

	/**
	 * @param integer $binary_id
	 */
	function __construct($binary_id)
	{
		global $db;

		if ($binary_id == null)
		{
			$this->binary_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("BINARY_TABLE")." WHERE id='".$binary_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->binary_id 	= $binary_id;
				$this->path 		= $data['path'];
				$this->file 		= $data['file'];
			}
			else
			{
				$this->binary_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->binary_id)
		{
			unset($this->binary_id);
			unset($this->path);
			unset($this->file);
		}
	}
	
	/**
	 * @param string $path
	 * @param string $file
	 * @return integer
	 */
	public function create($path, $file)
	{
		global $db;
		
		if ($path and $file)
		{
			$sql_write = "INSERT INTO ".constant("BINARY_TABLE")." (id,path,file) " .
						"VALUES (nextval('".self::BINARY_PK_SEQUENCE."'::regclass),'".$path."','".$file."')";

			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BINARY_TABLE")." WHERE id = currval('".self::BINARY_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->binary_id)
		{
			$tmp_binary_id = $this->binary_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("BINARY_TABLE")." WHERE id = ".$tmp_binary_id."";
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
	public function get_file()
	{
		if ($this->file)
		{
			return $this->file;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function set_path($path)
	{
		global $db;
			
		if ($this->binary_id and $path)
		{
			$sql = "UPDATE ".constant("BINARY_TABLE")." SET path = '".$path."' WHERE id = '".$this->binary_id."'";
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
	 * @param string $file
	 * @return bool
	 */
	public function set_file($file)
	{
		global $db;
			
		if ($this->binary_id and $file)
		{
			$sql = "UPDATE ".constant("BINARY_TABLE")." SET file = '".$file."' WHERE id = '".$this->binary_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->file = $file;
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