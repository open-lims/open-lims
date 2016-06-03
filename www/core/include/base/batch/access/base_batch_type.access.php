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
 * Base Batch Type Access Class
 * @package base
 */
class BaseBatchType_Access
{
	const  BASE_BATCH_TYPE_PK_SEQUENCE = 'core_base_batch_types_id_seq';

	private $batch_type_id;
	private $name;
	private $internal_name;
	private $binary_id;
	
	/**
	 * @param integer $batch_type_id
	 */
	function __construct($batch_type_id)
	{
		global $db;

		if ($batch_type_id == null)
		{
			$this->batch_type_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("BASE_BATCH_TYPE_TABLE")." WHERE id= :batch_type_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":batch_type_id", $batch_type_id, PDO::PARAM_INT);
			$db->execute($res);		
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->batch_type_id 	= $batch_type_id;
				$this->name 			= $data['name'];
				$this->internal_name 	= $data['internal_name'];
				$this->binary_id 		= $data['binary_id'];
			}
			else
			{
				$this->batch_type_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->batch_type_id)
		{
			unset($this->batch_type_id);
			unset($this->name);
			unset($this->internal_name);
			unset($this->binary_id);
		}
	}
	
	/**
	 * @param string $name
	 * @param string $internal_name
	 * @param integer $binary_id
	 * @return integer
	 */
	public function create($name, $internal_name, $binary_id)
	{
		global $db;
		
		if ($name and $internal_name and is_numeric($binary_id))
		{
			$sql_write = "INSERT INTO ".constant("BASE_BATCH_TYPE_TABLE")." (id,name,internal_name,binary_id) " .
						"VALUES (nextval('".self::BASE_BATCH_TYPE_PK_SEQUENCE."'::regclass), :name, :internal_name, :binary_id)";

			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":name", $name, PDO::PARAM_STR);
			$db->bind_value($res_write, ":internal_name", $internal_name, PDO::PARAM_STR);
			$db->bind_value($res_write, ":binary_id", $binary_id, PDO::PARAM_INT);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_BATCH_TYPE_TABLE")." WHERE id = currval('".self::BASE_BATCH_TYPE_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->batch_type_id)
		{
			$tmp_batch_type_id = $this->batch_type_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("BASE_BATCH_TYPE_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $tmp_batch_type_id, PDO::PARAM_INT);
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
	public function get_internal_name()
	{
		if ($this->internal_name)
		{
			return $this->internal_name;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_binary_id()
	{
		if ($this->binary_id)
		{
			return $this->binary_id;
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
			
		if ($this->batch_type_id and $name)
		{
			$sql = "UPDATE ".constant("BASE_BATCH_TYPE_TABLE")." SET name = :name WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->batch_type_id, PDO::PARAM_INT);
			$db->bind_value($res, ":name", $name, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
	 * @param string $internal_name
	 * @return bool
	 */
	public function set_internal_name($internal_name)
	{
		global $db;
			
		if ($this->batch_type_id and $internal_name)
		{
			$sql = "UPDATE ".constant("BASE_BATCH_TYPE_TABLE")." SET internal_name = :internal_name WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->batch_type_id, PDO::PARAM_INT);
			$db->bind_value($res, ":internal_name", $internal_name, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->internal_name = $internal_name;
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
	 * @param integer $binary_id
	 * @return bool
	 */
	public function set_binary_id($binary_id)
	{
		global $db;
			
		if ($this->batch_type_id and is_numeric($binary_id))
		{
			$sql = "UPDATE ".constant("BASE_BATCH_TYPE_TABLE")." SET binary_id = :binary_id WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->batch_type_id, PDO::PARAM_INT);
			$db->bind_value($res, ":binary_id", $binary_id, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->binary_id = $binary_id;
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
	 * @param string $internal_name
	 * @return integer
	 */
	public static function get_id_by_internal_name($internal_name)
	{
		global $db;
		
		if ($internal_name)
		{
			$sql = "SELECT id FROM ".constant("BASE_BATCH_TYPE_TABLE")." WHERE internal_name= :internal_name";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":internal_name", $internal_name, PDO::PARAM_STR);
			$db->execute($res);
			$data = $db->fetch($res);
			
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