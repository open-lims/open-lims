<?php
/**
 * @package base
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
 * Base Binary Access Class
 * @package base
 */
class BaseBinary_Access
{
	const BASE_BINARY_PK_SEQUENCE = 'core_binaries_id_seq';
	
	private $id;
	private $path;
	private $file;
	
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
			$sql = "SELECT * FROM ".constant("BASE_BINARY_TABLE")." WHERE id=:id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id 		= $id;
				$this->path		= $data['path'];
				$this->file		= $data['file'];
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
	 		$sql_write = "INSERT INTO ".constant("BASE_BINARY_TABLE")." (id, path, file) " .
								"VALUES (nextval('".self::BASE_BINARY_PK_SEQUENCE."'::regclass),:path,:file)";		
				
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":path", $path, PDO::PARAM_STR);
			$db->bind_value($res_write, ":file", $file, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_BINARY_TABLE")." WHERE id = currval('".self::BASE_BINARY_PK_SEQUENCE."'::regclass)";
				$res_read = $db->prepare($sql_read);
				$db->execute($res_read);
				$data_read = $db->fetch($res_read);
							
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

		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("BASE_BINARY_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id_tmp, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res) == 1)
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

		if ($this->id and $path)
		{
			$sql = "UPDATE ".constant("BASE_BINARY_TABLE")." SET path = :path WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":path", $path, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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

		if ($this->id and $file)
		{
			$sql = "UPDATE ".constant("BASE_BINARY_TABLE")." SET file = :file WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":file", $file, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
?>