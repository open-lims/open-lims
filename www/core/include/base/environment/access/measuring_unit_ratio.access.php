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
 * Measuring Unit Access Class
 * @package base
 */
class MeasuringUnitRatio_Access
{
	const MEASURING_UNIT_RATIO_PK_SEQUENCE = 'core_base_measuring_unit_ratios_id_seq';
	
	private $id;
	
	private $numerator_unit_id;
	private $numerator_unit_exponent;
	private $denominator_unit_id;
	private $denominator_unit_exponent;
	
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
			$sql = "SELECT * FROM ".constant("MEASURING_UNIT_RATIO_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id							= $id;
				
				$this->numerator_unit_id			= $data['numerator_unit_id'];
				$this->numerator_unit_exponent		= $data['numerator_unit_exponent'];
				$this->denominator_unit_id			= $data['denominator_unit_id'];
				$this->denominator_unit_exponent	= $data['denominator_unit_exponent'];
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
			unset($this->numerator_unit_id);
			unset($this->numerator_unit_exponent);
			unset($this->denominator_unit_id);
			unset($this->basdenominator_unit_exponente_id);
		}
	}
	
	/**
	 * @param integer $numerator_unit_id
	 * @param integer $numerator_unit_exponent
	 * @param integer $denominator_unit_id
	 * @param integer $denominator_unit_exponent
	 * @return integer
	 */
	public function create($numerator_unit_id, $numerator_unit_exponent, $denominator_unit_id, $denominator_unit_exponent)
	{
		global $db;
		
		if (is_numeric($numerator_unit_id) and is_numeric($numerator_unit_exponent) and is_numeric($denominator_unit_id) and is_numeric($denominator_unit_exponent))
		{
			$sql_write = "INSERT INTO ".constant("MEASURING_UNIT_RATIO_TABLE")." (id,numerator_unit_id,numerator_unit_exponent,denominator_unit_id,denominator_unit_exponent) " .
							"VALUES (nextval('".self::MEASURING_UNIT_RATIO_PK_SEQUENCE."'::regclass), :numerator_unit_id, :numerator_unit_exponent, :denominator_unit_id, :denominator_unit_exponent)";
			
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":numerator_unit_id", $numerator_unit_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":numerator_unit_exponent", $numerator_unit_exponent, PDO::PARAM_INT);
			$db->bind_value($res_write, ":denominator_unit_id", $denominator_unit_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":denominator_unit_exponent", $denominator_unit_exponent, PDO::PARAM_INT);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("MEASURING_UNIT_RATIO_TABLE")." WHERE id = currval('".self::MEASURING_UNIT_RATIO_PK_SEQUENCE."'::regclass)";
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

    		$sql = "DELETE FROM ".constant("MEASURING_UNIT_RATIO_TABLE")." WHERE id = :id";
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
	public function get_numerator_unit_id()
	{
		if ($this->numerator_unit_id)
		{
			return $this->numerator_unit_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
     * @return integer
     */
	public function get_numerator_unit_exponent()
	{
		if ($this->numerator_unit_exponent)
		{
			return $this->numerator_unit_exponent;
		}
		else
		{
			return null;
		}
	}
	
	/**
     * @return integer
     */
	public function get_denominator_unit_id()
	{
		if ($this->denominator_unit_id)
		{
			return $this->denominator_unit_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
     * @return integer
     */
	public function get_denominator_unit_exponent()
	{
		if ($this->denominator_unit_exponent)
		{
			return $this->denominator_unit_exponent;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $numerator_unit_id
	 * @return bool
	 */
	public function set_numerator_unit_id($numerator_unit_id)
	{
		global $db;

		if ($this->id and is_numeric($numerator_unit_id))
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_RATIO_TABLE")." SET numerator_unit_id = :numerator_unit_id WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":numerator_unit_id", $numerator_unit_id, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->numerator_unit_id = $numerator_unit_id;
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
	 * @param integer $numerator_unit_exponent
	 * @return bool
	 */
	public function set_numerator_unit_exponent($numerator_unit_exponent)
	{
		global $db;

		if ($this->id and is_numeric($numerator_unit_exponent))
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_RATIO_TABLE")." SET numerator_unit_exponent = :numerator_unit_exponent WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":numerator_unit_exponent", $numerator_unit_exponent, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->numerator_unit_exponent = $numerator_unit_exponent;
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
	 * @param integer $denominator_unit_id
	 * @return bool
	 */
	public function set_denominator_unit_id($denominator_unit_id)
	{
		global $db;

		if ($this->id and is_numeric($denominator_unit_id))
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_RATIO_TABLE")." SET denominator_unit_id = ':denominator_unit_id WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":denominator_unit_id", $denominator_unit_id, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->denominator_unit_id = $denominator_unit_id;
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
	 * @param integer $denominator_unit_exponent
	 * @return bool
	 */
	public function set_denominator_unit_exponent($denominator_unit_exponent)
	{
		global $db;

		if ($this->id and is_numeric($denominator_unit_exponent))
		{
			$sql = "UPDATE ".constant("MEASURING_UNIT_RATIO_TABLE")." SET denominator_unit_exponent = ':denominator_unit_exponent WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":denominator_unit_exponent", $denominator_unit_exponent, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->denominator_unit_exponent = $denominator_unit_exponent;
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
			$sql = "SELECT id FROM ".constant("MEASURING_UNIT_RATIO_TABLE")." WHERE id = :id";
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
		
		$sql = "SELECT id,numerator_unit_id,numerator_unit_exponent,denominator_unit_id,denominator_unit_exponent FROM ".constant("MEASURING_UNIT_RATIO_TABLE")." ORDER BY id";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		while ($data = $db->fetch($res))
		{
			$temp_array = array();
			$temp_array['id'] = $data['id'];
			$temp_array['numerator_unit_id'] = $data['numerator_unit_id'];
			$temp_array['numerator_unit_exponent'] = $data['numerator_unit_exponent'];
			$temp_array['denominator_unit_id'] = $data['denominator_unit_id'];
			$temp_array['denominator_unit_exponent'] = $data['denominator_unit_exponent'];
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