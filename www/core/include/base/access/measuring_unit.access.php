<?php
/**
 * @package base
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
 * Measuring Unit Access Class
 * @package base
 */
class MeasuringUnit_Access
{
	const MEASURING_UNIT_PK_SEQUENCE = 'core_measuring_units_id_seq';
	
	private $id;
	
	private $toid;
	private $name;
	private $type;
	private $base;
	private $unit_symbol;
	private $calculation;
	
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
			$sql = "SELECT * FROM ".constant("MEASURING_UNIT_TABLE")." WHERE id = ".$id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->id			= $data[id];
				
				$this->toid			= $data[toid];
				$this->name			= $data[name];
				$this->type			= $data[type];
				$this->unit_symbol	= $data[unit_symbol];
				$this->calculation	= $data[calculation];
				
				if ($data[base] == 't')
				{
					$this->base = true;
				}
				else
				{
					$this->base = false;
				}
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
			unset($this->toid);
			unset($this->name);
			unset($this->type);
			unset($this->base);
			unset($this->unit_symbol);
			unset($this->calculation);
		}
	}
	
	/**
	 * @param integer $toid
	 * @param string $name
	 * @param integer $type
	 * @param string $unit_symbol
	 * @param string $calculation
	 * @return integer
	 */
	public function create($toid, $name, $type, $unit_symbol, $calulcation)
	{
		global $db;
		
		if ($name and (is_numeric($type) or $type === 0) and $unit_symbol)
		{
			if (is_numeric($toid))
			{
				$toid_insert = $toid;
			}
			else
			{
				$toid_insert = "NULL";
			}
			
			if ($calculation)
			{
				$calculation_insert = $calculation;
			}
			else
			{
				$calculation_insert = "NULL";
			}
			
			$sql_write = "INSERT INTO ".constant("MEASURING_UNIT_TABLE")." (id,toid,name,type,base,unit_symbol,calculation) " .
							"VALUES (nextval('".self::MEASURING_UNIT_PK_SEQUENCE."'::regclass),".$toid_insert.",'".$name."','".$type."','f','".$unit_symbol."',".$calculation_insert.")";
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("MEASURING_UNIT_TABLE")." WHERE id = currval('".self::MEASURING_UNIT_PK_SEQUENCE."'::regclass)";
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
    		$tmp_id = $this->id;
    		
    		$this->__destruct();

    		$sql = "DELETE FROM ".constant("MEASURING_UNIT_TABLE")." WHERE id = ".$tmp_id."";
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
	public function get_type()
	{
		if ($this->type)
		{
			return $this->type;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_base()
	{
		if (isset($this->base))
		{
			return $this->base;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_unit_symbol()
	{
		if ($this->unit_symbol)
		{
			return $this->unit_symbol;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_calculation()
	{
		if ($this->calculation)
		{
			return $this->calculation;
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

		if ($this->id and is_numeric($toid))
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET toid = '".$toid."' WHERE id = ".$this->id."";
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

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET name = '".$name."' WHERE id = ".$this->id."";
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
	 * @param integer $type
	 * @return bool
	 */
	public function set_type($type)
	{
		global $db;

		if ($this->id and is_numeric($type))
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET type = '".$type."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->type = $type;
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
	 * @param bool $base
	 * @return bool
	 */
	public function set_base($base)
	{
		global $db;

		if ($this->id and isset($base))
		{
			if ($base == true)
			{
				$base_insert = "t";
			}
			else
			{
				$base_insert = "f";
			}
			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET base = '".$base_insert."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->base = $base;
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
	 * @param string $unit_symbol
	 * @return bool
	 */
	public function set_unit_symbol($unit_symbol)
	{
		global $db;

		if ($this->id and $unit_symbol)
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET unit_symbol = '".$unit_symbol."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->unit_symbol = $unit_symbol;
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
	 * @param string $calculation
	 * @return bool
	 */
	public function set_calculation($calculation)
	{
		global $db;

		if ($this->id and $calculation)
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET calculation = '".$calculation."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->calculation = $calculation;
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