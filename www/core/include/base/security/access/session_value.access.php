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
 * Session Value Access Class
 * @package base
 */
class SessionValue_Access
{
	const SESSION_VALUE_PK_SEQUENCE = 'core_session_values_id_seq';
	
	private $id;
	
	private $session_id;
	private $address;
	private $value;
	
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
			$sql = "SELECT * FROM ".constant("SESSION_VALUE_TABLE")." WHERE id= :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['session_id'])
			{
				$this->id			= $id;
			
				$this->session_id 	= $data['session_id'];
				$this->address		= $data['address'];
				$this->value		= $data['value'];
			}
			else
			{
				$this->id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->session_id)
		{
			unset($this->id);
		
			unset($this->session_id);
			unset($this->address);
			unset($this->value);
		}
	}
	
	/**
	 * @param string $session_id
	 * @param string $address
	 * @param string $value
	 * @return bool
	 */
	public function create($session_id, $address, $value)
	{
		global $db;
		
		if ($session_id and $address and $value)
		{
	 		$sql_write = "INSERT INTO ".constant("SESSION_VALUE_TABLE")." (id, session_id, address, value) " .
								"VALUES (nextval('".self::SESSION_VALUE_PK_SEQUENCE."'::regclass), :session_id, :address, :value)";		
				
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":session_id", $session_id, PDO::PARAM_STR);
			$db->bind_value($res_write, ":address", $address, PDO::PARAM_STR);
			$db->bind_value($res_write, ":value", $value, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) != 1)
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

		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("SESSION_VALUE_TABLE")." WHERE id = :id";
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
	public function get_session_id()
	{
		if ($this->session_id)
		{
			return $this->session_id;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_address()
	{
		if ($this->address)
		{
			return $this->address;
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
	 * @param string $value
	 * @return bool
	 */	
	public function set_value($value)
	{
		global $db;
		
		if ($this->session_id and $value)
		{
			$sql = "UPDATE ".constant("SESSION_VALUE_TABLE")." SET value = :value WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":value", $value, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
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
	 * @param string $session_id
	 * @param string $address
	 * @return integer
	 */
	public static function get_id_by_session_id_and_address($session_id, $address)
	{
		global $db;
		
		if ($session_id and $address)
		{
			$sql = "SELECT id FROM ".constant("SESSION_VALUE_TABLE")." WHERE session_id = :session_id AND address = :address";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":session_id", $session_id, PDO::PARAM_STR);
			$db->bind_value($res, ":address", $address, PDO::PARAM_STR);
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
	
	/**
	 * @param string $session_id
	 * @return array
	 */
	public static function list_entries_by_session_id($session_id)
	{
		global $db;
		
		if ($session_id)
		{
			$return_array = array();
			
			$sql = "SELECT id,address,value FROM ".constant("SESSION_VALUE_TABLE")." WHERE session_id = :session_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":session_id", $session_id, PDO::PARAM_STR);
			$db->execute($res);
			while ($data = $db->fetch($res))
			{
				$temp_array = array();
				$temp_array['id'] = $data['id'];
				$temp_array['address'] = $data['address'];
				$temp_array['value'] = $data['value'];
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
