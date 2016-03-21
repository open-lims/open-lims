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
 * Measuring Unit Access Class
 * @package base
 */
class MeasuringUnit_Access
{
	const MEASURING_UNIT_PK_SEQUENCE = 'core_base_measuring_units_id_seq';
	
	private $id;
	
	private $base_id;
	private $category_id;
	private $name;
	private $unit_symbol;
	private $min_value;
	private $max_value;
	private $min_prefix_exponent;
	private $max_prefix_exponent;
	private $prefix_calculation_exponent;
	private $calculation;
	private $type;
	private $created_by_user;
	
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
			$sql = "SELECT * FROM ".constant("MEASURING_UNIT_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id							= $id;
				
				$this->base_id						= $data['base_id'];
				$this->category_id					= $data['category_id'];
				$this->name							= $data['name'];
				$this->unit_symbol					= $data['unit_symbol'];
				$this->min_value					= $data['min_value'];
				$this->max_value					= $data['max_value'];
				$this->min_prefix_exponent			= $data['min_prefix_exponent'];
				$this->max_prefix_exponent			= $data['max_prefix_exponent'];
				$this->prefix_calculation_exponent	= $data['prefix_calculation_exponent'];
				$this->calculation					= $data['calculation'];
				$this->type							= $data['type'];
				$this->created_by_user				= $data['created_by_user'];
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
			unset($this->base_id);
			unset($this->category_id);
			unset($this->name);
			unset($this->unit_symbol);
			unset($this->min_value);
			unset($this->max_value);
			unset($this->min_prefix_exponent);
			unset($this->max_prefix_exponent);
			unset($this->prefix_calculation_exponent);
			unset($this->calculation);
			unset($this->type);
			unset($this->created_by_user);
		}
	}
	
	/**
	 * @param integer $base_id
	 * @param integer $category_id
	 * @param string $name
	 * @param string $unit_symbol
	 * @param float $min_value
	 * @param float $max_value
	 * @param integer $min_prefix_exponent
	 * @param integer $max_prefix_exponent
	 * @param integer $prefix_calculation_exponent
	 * @param string $calculation
	 * @param string $type
	 * @return integer
	 */
	public function create($base_id, $category_id, $name, $unit_symbol, $min_value, $max_value, $min_prefix_exponent, $max_prefix_exponent, $prefix_calculation_exponent, $calculation, $type)
	{
		global $db;
		
		if ($name and $unit_symbol)
		{
			$sql_write = "INSERT INTO ".constant("MEASURING_UNIT_TABLE")." (id,base_id,category_id,name,unit_symbol,min_value,max_value,min_prefix_exponent,max_prefix_exponent,prefix_calculation_exponent,calculation,type,created_by_user) " .
					"VALUES (nextval('".self::MEASURING_UNIT_PK_SEQUENCE."'::regclass), :base_id, :category_id, :name, :unit_symbol, :min_value, :max_value, :min_prefix_exponent, :max_prefix_exponent, :prefix_calculation_exponent, :calculation, :type, 't')";
				
			$res_write = $db->prepare($sql_write);
			
			if (is_numeric($base_id))
			{
				$db->bind_value($res_write, ":base_id", $base_id, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res_write, ":base_id", null, PDO::PARAM_NULL);
			}
			
			if (is_numeric($category_id))
			{
				$db->bind_value($res_write, ":category_id", $category_id, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res_write, ":category_id", null, PDO::PARAM_NULL);
			}
			
			if ($calculation)
			{
				$db->bind_value($res_write, ":calculation", $calculation, PDO::PARAM_STR);
			}
			else
			{
				$db->bind_value($res_write, ":calculation", null, PDO::PARAM_NULL);
			}
			
			if (is_numeric($min_value))
			{
				$db->bind_value($res_write, ":min_value", $min_value, PDO::PARAM_STR);
			}
			else
			{
				$db->bind_value($res_write, ":min_value", null, PDO::PARAM_NULL);
			}
			
			if (is_numeric($max_value))
			{
				$db->bind_value($res_write, ":max_value", $max_value, PDO::PARAM_STR);
			}
			else
			{
				$db->bind_value($res_write, ":max_value", null, PDO::PARAM_NULL);
			}
			
			if (is_numeric($min_prefix_exponent))
			{
				$db->bind_value($res_write, ":min_prefix_exponent", $min_prefix_exponent, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res_write, ":min_prefix_exponent", null, PDO::PARAM_NULL);
			}
			
			if (is_numeric($max_prefix_exponent))
			{
				$db->bind_value($res_write, ":max_prefix_exponent", $max_prefix_exponent, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res_write, ":max_prefix_exponent", null, PDO::PARAM_NULL);
			}
			
			if (is_numeric($prefix_calculation_exponent))
			{
				$db->bind_value($res_write, ":prefix_calculation_exponent", $prefix_calculation_exponent, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res_write, ":prefix_calculation_exponent", null, PDO::PARAM_NULL);
			}
			
			if ($type)
			{
				 if($type === "aa")
				 {
				 	$db->bind_value($res_write, ":type", "aa", PDO::PARAM_STR);
				 }
				 else
				 {
				 	$db->bind_value($res_write, ":type", "metric", PDO::PARAM_STR);
				 }
			}
			else
			{
				$db->bind_value($res_write, ":type", null, PDO::PARAM_NULL);
			}
			
			$db->bind_value($res_write, ":name", $name, PDO::PARAM_STR);
			$db->bind_value($res_write, ":unit_symbol", $unit_symbol, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("MEASURING_UNIT_TABLE")." WHERE id = currval('".self::MEASURING_UNIT_PK_SEQUENCE."'::regclass)";
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

    		$sql = "DELETE FROM ".constant("MEASURING_UNIT_TABLE")." WHERE id = :id";
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
     * @return integer
     */
	public function get_base_id()
	{
		if ($this->base_id)
		{
			return $this->base_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
     * @return integer
     */
	public function get_category_id()
	{
		if ($this->category_id)
		{
			return $this->category_id;
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
     * @return float
     */
	public function get_min_value()
	{
		if (is_numeric($this->min_value))
		{
			return $this->min_value;
		}
		else
		{
			return null;
		}
	}
	
	/**
     * @return float
     */
	public function get_max_value()
	{
		if (is_numeric($this->max_value))
		{
			return $this->max_value;
		}
		else
		{
			return null;
		}
	}
	
	/**
     * @return integer
     */
	public function get_min_prefix_exponent()
	{
		if (is_numeric($this->min_prefix_exponent))
		{
			return $this->min_prefix_exponent;
		}
		else
		{
			return null;
		}
	}
	
	/**
     * @return integer
     */
	public function get_max_prefix_exponent()
	{
		if (is_numeric($this->max_prefix_exponent))
		{
			return $this->max_prefix_exponent;
		}
		else
		{
			return null;
		}
	}
	
	/**
     * @return integer
     */
	public function get_prefix_calculation_exponent()
	{
		if (is_numeric($this->prefix_calculation_exponent))
		{
			return $this->prefix_calculation_exponent;
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
	 * @return string
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
	public function get_created_by_user()
	{
		if (isset($this->created_by_user))
		{
			return $this->created_by_user;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $base_id
	 * @return bool
	 */
	public function set_base_id($base_id)
	{
		global $db;

		if ($this->id and is_numeric($base_id))
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET base_id = :base_id WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":base_id", $base_id, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->base_id = $base_id;
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
	 * @param integer $category_id
	 * @return bool
	 */
	public function set_category_id($category_id)
	{
		global $db;

		if ($this->id)
		{			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET category_id = :category_id WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if (is_numeric($category_id))
			{
				$db->bind_value($res, ":category_id", $category_id, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res, ":category_id", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->category_id = $category_id;
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
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET name = :name WHERE id = :id";
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
	 * @param string $unit_symbol
	 * @return bool
	 */
	public function set_unit_symbol($unit_symbol)
	{
		global $db;

		if ($this->id and $unit_symbol)
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET unit_symbol = :unit_symbol WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":unit_symbol", $unit_symbol, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
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
	 * @param float $min_value
	 * @return bool
	 */
	public function set_min_value($min_value)
	{
		global $db;

		if ($this->id)
		{			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET min_value = :min_value WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if (is_numeric($min_value))
			{
				$db->bind_value($res, ":min_value", $min_value, PDO::PARAM_STR);
			}
			else
			{
				$db->bind_value($res, ":min_value", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->min_value = $min_value;
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
	 * @param float $max_value
	 * @return bool
	 */
	public function set_max_value($max_value)
	{
		global $db;

		if ($this->id)
		{			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET max_value = :max_value WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if (is_numeric($max_value))
			{
				$db->bind_value($res, ":max_value", $max_value, PDO::PARAM_STR);
			}
			else
			{
				$db->bind_value($res, ":max_value", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->max_value = $max_value;
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
	 * @param integer $min_prefix_exponent
	 * @return bool
	 */
	public function set_min_prefix_exponent($min_prefix_exponent)
	{
		global $db;

		if ($this->id)
		{			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET min_prefix_exponent = :min_prefix_exponent WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if (is_numeric($min_prefix_exponent))
			{
				$db->bind_value($res, ":min_prefix_exponent", $min_prefix_exponent, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res, ":min_prefix_exponent", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->min_prefix_exponent = $min_prefix_exponent;
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
	 * @param integer $max_prefix_exponent
	 * @return bool
	 */
	public function set_max_prefix_exponent($max_prefix_exponent)
	{
		global $db;

		if ($this->id)
		{			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET max_prefix_exponent = :max_prefix_exponent WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if (is_numeric($max_prefix_exponent))
			{
				$db->bind_value($res, ":max_prefix_exponent", $max_prefix_exponent, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res, ":max_prefix_exponent", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->max_prefix_exponent = $max_prefix_exponent;
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
	 * @param integer $prefix_calculation_exponent
	 * @return bool
	 */
	public function set_prefix_calculation_exponent($prefix_calculation_exponent)
	{
		global $db;

		if ($this->id)
		{			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET prefix_calculation_exponent = :prefix_calculation_exponent WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if (is_numeric($prefix_calculation_exponent))
			{
				$db->bind_value($res, ":prefix_calculation_exponent", $prefix_calculation_exponent, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res, ":prefix_calculation_exponent", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->prefix_calculation_exponent = $prefix_calculation_exponent;
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

		if ($this->id)
		{			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET calculation = :calculation WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if ($calculation)
			{
				$db->bind_value($res, ":calculation", $calculation, PDO::PARAM_STR);
			}
			else
			{
				$db->bind_value($res, ":calculation", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
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
	
	/**
	 * @param string $type
	 * @return bool
	 */
	public function set_type($type)
	{
		global $db;

		if ($this->id)
		{			
			$sql = "UPDATE ".constant("MEASURING_UNIT_TABLE")." SET type = :type WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			
			if ($type)
			{
				$db->bind_value($res, ":type", $type, PDO::PARAM_STR);
			}
			else
			{
				$db->bind_value($res, ":type", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
			
		if (is_numeric($id))
		{
			$sql = "SELECT id FROM ".constant("MEASURING_UNIT_TABLE")." WHERE id = :id";
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
	public static function list_entries_by_category_id($category_id)
	{
		global $db;

		if (is_numeric($category_id))
		{
			$return_array = array();
			
			$sql = "SELECT id,name,min_prefix_exponent,max_prefix_exponent,unit_symbol FROM ".constant("MEASURING_UNIT_TABLE")." WHERE category_id = :category_id ORDER BY id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":category_id", $category_id, PDO::PARAM_INT);
			$db->execute($res);
			
			while ($data = $db->fetch($res))
			{
				$temp_array = array();
				$temp_array['id'] = $data['id'];
				$temp_array['name'] = $data['name'];
				$temp_array['min_prefix_exponent'] = $data['min_prefix_exponent'];
				$temp_array['max_prefix_exponent'] = $data['max_prefix_exponent'];
				$temp_array['unit_symbol'] = $data['unit_symbol'];
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
	
	/**
	 * @return array
	 */
	public static function list_entries_without_category()
	{
		global $db;

		$return_array = array();
		
		$sql = "SELECT id,name,min_prefix_exponent,max_prefix_exponent,unit_symbol FROM ".constant("MEASURING_UNIT_TABLE")." WHERE category_id IS NULL ORDER BY id";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		while($data = $db->fetch($res))
		{
			$temp_array = array();
			$temp_array['id'] = $data['id'];
			$temp_array['name'] = $data['name'];
			$temp_array['min_prefix_exponent'] = $data['min_prefix_exponent'];
			$temp_array['max_prefix_exponent'] = $data['max_prefix_exponent'];
			$temp_array['unit_symbol'] = $data['unit_symbol'];
			array_push($return_array,$temp_array);
			unset($temp_array);
		}
		
		if(is_array($return_array))
		{
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $category_id
	 * @return integer
	 */
	public static function get_category_base_id($category_id)
	{
		global $db;
		
		if(is_numeric($category_id))
		{
			$sql = "SELECT min(id) AS id FROM ".constant("MEASURING_UNIT_TABLE")." WHERE category_id = :category_id AND calculation='B'";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":category_id", $category_id, PDO::PARAM_INT);
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
     * @param integer $measuring_unit_id
     * @return bool
     */
    public static function is_deletable($measuring_unit_id)
    {
    	global $db;
    	
   		if(is_numeric($measuring_unit_id))
		{
			$sql = "SELECT created_by_user FROM ".constant("MEASURING_UNIT_TABLE")." WHERE id = :measuring_unit_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":measuring_unit_id", $measuring_unit_id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['created_by_user'] == "t")
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