<?php
/**
 * @package base
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
 * 
 */
require_once("interfaces/measuring_unit.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/measuring_unit_category.access.php");
	require_once("access/measuring_unit.access.php");
}

/**
 * Measuring Unit Class
 * @package base
 */
class MeasuringUnit implements MeasuringUnitInterface
{
	private $measuring_unit_id;
	private $measuring_unit;
	
	/**
	 * @see MeasuringUnitInterface::__construct()
	 * @param integer $measuring_unit_id
	 * @throws BaseEnvironmentMeasuringUnitNotFoundException
	 */
	function __construct($measuring_unit_id)
	{
		if (is_numeric($measuring_unit_id))
		{
			if (MeasuringUnit_Access::exist_id($measuring_unit_id) == true)
			{
				$this->measuring_unit_id = $measuring_unit_id;
   	   			$this->measuring_unit = new MeasuringUnit_Access($measuring_unit_id);
			}
			else
			{
				throw new BaseEnvironmentMeasuringUnitNotFoundException();
			}
    	}
    	else
    	{
    		$this->measuring_unit_id = null;
   	   		$this->measuring_unit = new MeasuringUnit_Access(null);
    	}
	}
	
	/**
	 * @see MeasuringUnitInterface::__destruct()
	 */
	function __destruct()
	{
		unset($this->measuring_unit_id);
		unset($this->measuring_unit);
	}
	
	/**
	 * @see MeasuringUnitInterface::create()
	 * @param integer $toid
	 * @param string $name
	 * @param integer $type
	 * @param string $unit_symbol
	 * @param stirng $calculation
	 */
	public function create($toid, $name, $type, $unit_symbol, $calulcation)
	{
		if ($this->measuring_unit)
		{
			return $this->measuring_unit->create($toid, $name, $type, $unit_symbol, $calulcation);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see MeasuringUnitInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		if ($this->measuring_unit_id and $this->measuring_unit)
		{
			return $this->measuring_unit->delete();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see MeasuringUnitInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->measuring_unit_id and $this->measuring_unit)
    	{
    		return $this->measuring_unit->get_name();	
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * @see MeasuringUnitInterface::get_unit_symbol()
	 * @return string
	 */
	public function get_unit_symbol()
	{
		if ($this->measuring_unit_id and $this->measuring_unit)
    	{
    		return $this->measuring_unit->get_unit_symbol();	
    	}
    	else
    	{
    		return null;
    	}
	}
	
	
	/**
	 * @see MeasuringUnitInterface::list_entries()
	 * @return array
	 */
	public static function get_categorized_list()
	{
		$return_array = array();
		$counter = 0;
		
		$category_array = MeasuringUnitCategory_Access::list_entries();
				
		foreach($category_array as $key => $value)
		{
			$return_array[$counter]['name'] = $value['name'];
			$return_array[$counter]['headline'] = true;
			$counter++;
			
			$unit_array = MeasuringUnit_Access::list_entries_by_category_id($value['id']);
			
			foreach($unit_array as $unit_key => $unit_value)
			{
				if ($unit_array['min_prefix_exponent'])
				{
					
				}
				
				$return_array[$counter]['name'] = $unit_value['name']." (".$unit_value['unit_symbol'].")";
				$return_array[$counter]['exponent'] = 0;
				$return_array[$counter]['headline'] = false;
				$counter++;
				
				if ($unit_array['max_prefix_exponent'])
				{
					
				}
			}
		}
		
		return $return_array;
	}
}