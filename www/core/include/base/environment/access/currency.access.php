<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Currency Access Class
 * @package base
 */
class Currency_Access
{
	const CURRENCY_PK_SEQUENCE = 'core_currencies_id_seq';
	
	private $id;
	
	private $name;
	private $symbol;
	private $iso_4217;
	
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
			$sql = "SELECT * FROM ".constant("CURRENCY_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id			= $data['id'];
				
				$this->name			= $data['name'];
				$this->symbol		= $data['symbol'];
				$this->iso_4217		= $data['iso_4217'];
			}
			else
			{
				$this->id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->name);
			unset($this->symbol);
			unset($this->iso_4217);
		}
	}
	
	/**
	 * @param string $name
	 * @param string $symbol
	 * @param string $iso_4217
	 * @return integer
	 */
	public function create($name, $symbol, $iso_4217)
	{
		global $db;
		
		if ($name and $symbol and $iso_4217)
		{
			$sql_write = "INSERT INTO ".constant("CURRENCY_TABLE")." (id,name,symbol,iso_4217) " .
							"VALUES (nextval('".self::CURRENCY_PK_SEQUENCE."'::regclass), :name, :symbol, :iso_4217)";
			
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":name", $name, PDO::PARAM_STR);
			$db->bind_value($res_write, ":symbol", $symbol, PDO::PARAM_STR);
			$db->bind_value($res_write, ":iso_4217", $iso_4217, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("CURRENCY_TABLE")." WHERE id = currval('".self::CURRENCY_PK_SEQUENCE."'::regclass)";
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

    		$sql = "DELETE FROM ".constant("CURRENCY_TABLE")." WHERE id = :id";
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
	public function get_symbol()
	{
		if ($this->symbol)
		{
			return $this->symbol;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_iso_4217()
	{
		if ($this->iso_4217)
		{
			return $this->iso_4217;
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

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("CURRENCY_TABLE")." SET name = :name WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
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
	 * @param string $symbol
	 * @return bool
	 */
	public function set_symbol($symbol)
	{
		global $db;

		if ($this->id and $symbol)
		{
			$sql = "UPDATE ".constant("CURRENCY_TABLE")." SET symbol = :symbol WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":symbol", $symbol, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->symbol = $symbol;
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
	 * @param string $iso_4217
	 * @return bool
	 */
	public function set_iso_4217($iso_4217)
	{
		global $db;

		if ($this->id and $iso_4217)
		{
			$sql = "UPDATE ".constant("CURRENCY_TABLE")." SET iso_4217 = :iso_4217 WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":iso_4217", $iso_4217, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->iso_4217 = $iso_4217;
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
			
		if (is_numeric($id))
		{
			$sql = "SELECT id FROM ".constant("CURRENCY_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
			
		$return_array = array();
		
		$sql = "SELECT id,name,symbol,iso_4217 FROM ".constant("CURRENCY_TABLE")." ORDER BY name";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		while ($data = $db->fetch($res))
		{
			$temp_array = array();
			$temp_array['id'] = $data['id'];
			$temp_array['name'] = $data['name'];
			$temp_array['symbol'] = $data['symbol'];
			$temp_array['iso_4217'] = $data['iso_4217'];
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
}
?>