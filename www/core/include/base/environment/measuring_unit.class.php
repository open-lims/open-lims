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
	require_once("access/measuring_unit_ratio.access.php");
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
	 * @param integer $exponent
	 * @param bool $positive
	 * @return array
	 */
	public static function get_prefix($exponent, $positive = true)
	{
		if ($exponent < 0)
		{
			$exponent = $exponent*-1;
			$positive = false;
		}
		
		switch ($exponent):
		
			case 3:
				if ($positive == true)
				{
					return array("kilo", "k");
				}
				else
				{
					return array("milli", "m");
				}
			break;
			
			case 6:
				if ($positive == true)
				{
					return array("mega", "M");
				}
				else
				{
					return array("micro", "&micro;");
				}
			break;
			
			case 9:
				if ($positive == true)
				{
					return array("giga", "G");
				}
				else
				{
					return array("nano", "n");
				}
			break;
			
			case 12:
				if ($positive == true)
				{
					return array("tera", "T");
				}
				else
				{
					return array("pico", "p");
				}
			break;
			
			case 15:
				if ($positive == true)
				{
					return array("peta", "P");
				}
				else
				{
					return array("femto", "f");
				}
			break;
			
			case 18:
				if ($positive == true)
				{
					return array("exa", "E");
				}
				else
				{
					return array("atto", "a");
				}
			break;
			
			case 21:
				if ($positive == true)
				{
					return array("zetta", "Z");
				}
				else
				{
					return array("zepto", "z");
				}
			break;
			
			case 24:
				if ($positive == true)
				{
					return array("yota", "Y");
				}
				else
				{
					return array("yocto", "y");
				}	
			break;
			
			default:
				return null;
			break;
		
		endswitch;
	}
	
	/**
	 * @see MeasuringUnitInterface::list_entries()
	 * @return array
	 */
	public static function get_categorized_list()
	{
		$return_array = array();
		$counter = 0;
		
		$unit_array = MeasuringUnit_Access::list_entries_without_category();
			
		if (is_array($unit_array) and count($unit_array) >= 1)
		{
			foreach($unit_array as $unit_key => $unit_value)
			{
				if ($unit_array['min_prefix_exponent'] and $unit_value['min_prefix_exponent'] >= 3)
				{
					$prefix_array = self::get_prefix($i, false);
					if (is_array($prefix_array) and count($prefix_array) == 2)
					{
						$return_array[$counter]['id'] = $unit_value['id'];
						$return_array[$counter]['name'] = $prefix_array[0]."".$unit_value['name']." (".$prefix_array[1]."".$unit_value['unit_symbol'].")";
						$return_array[$counter]['exponent'] = "-".$i;
						$return_array[$counter]['headline'] = false;
						$counter++;
					}
				}
				
				$return_array[$counter]['id'] = $unit_value['id'];
				$return_array[$counter]['name'] = $unit_value['name']." (".$unit_value['unit_symbol'].")";
				$return_array[$counter]['exponent'] = 0;
				$return_array[$counter]['headline'] = false;
				$counter++;
				
				if ($unit_array['max_prefix_exponent'] and $unit_value['max_prefix_exponent'] >= 3)
				{
					$prefix_array = self::get_prefix($i);
					if (is_array($prefix_array) and count($prefix_array) == 2)
					{
						$return_array[$counter]['id'] = $unit_value['id'];
						$return_array[$counter]['name'] = $prefix_array[0]."".$unit_value['name']." (".$prefix_array[1]."".$unit_value['unit_symbol'].")";
						$return_array[$counter]['exponent'] = $i;
						$return_array[$counter]['headline'] = false;
						$counter++;
					}
				}
			}
		}
		
		$category_array = MeasuringUnitCategory_Access::list_entries();

		if (is_array($category_array) and count($category_array) >= 1)
		{
			foreach($category_array as $key => $value)
			{
				$return_array[$counter]['name'] = $value['name'];
				$return_array[$counter]['headline'] = true;
				$counter++;
				
				$unit_array = MeasuringUnit_Access::list_entries_by_category_id($value['id']);
				
				if (is_array($unit_array) and count($unit_array) >= 1)
				{
					foreach($unit_array as $unit_key => $unit_value)
					{
						if ($unit_value['min_prefix_exponent'] and $unit_value['min_prefix_exponent'] >= 3)
						{
							for ($i=$unit_value['min_prefix_exponent'];$i>=3;$i=$i-3)
							{
								$prefix_array = self::get_prefix($i, false);
								if (is_array($prefix_array) and count($prefix_array) == 2)
								{
									$return_array[$counter]['id'] = $unit_value['id'];
									$return_array[$counter]['name'] = $prefix_array[0]."".$unit_value['name']." (".$prefix_array[1]."".$unit_value['unit_symbol'].")";
									$return_array[$counter]['exponent'] = "-".$i;
									$return_array[$counter]['headline'] = false;
									$counter++;
								}
							}
						}
						
						$return_array[$counter]['id'] = $unit_value['id'];
						$return_array[$counter]['name'] = $unit_value['name']." (".$unit_value['unit_symbol'].")";
						$return_array[$counter]['exponent'] = 0;
						$return_array[$counter]['headline'] = false;
						$counter++;
						
						if ($unit_value['max_prefix_exponent'] and $unit_value['max_prefix_exponent'] >= 3)
						{
							for ($i=3;$i<=$unit_value['max_prefix_exponent'];$i=$i+3)
							{
								$prefix_array = self::get_prefix($i);
								if (is_array($prefix_array) and count($prefix_array) == 2)
								{
									$return_array[$counter]['id'] = $unit_value['id'];
									$return_array[$counter]['name'] = $prefix_array[0]."".$unit_value['name']." (".$prefix_array[1]."".$unit_value['unit_symbol'].")";
									$return_array[$counter]['exponent'] = $i;
									$return_array[$counter]['headline'] = false;
									$counter++;
								}
							}
						}
					}
				}
			}
		}
		
		$return_array[$counter]['name'] =  "Ratios";
		$return_array[$counter]['headline'] = true;
		$counter++;
		
		$ratio_array = MeasuringUnitRatio_Access::list_entries();
		
		if (is_array($ratio_array) and count($ratio_array) >= 1)
		{
			foreach($ratio_array as $ratio_key => $ratio_value)
			{
				$numerator_unit = new MeasuringUnit_Access($ratio_value['numerator_unit_id']);
				$numerator_prefix_array = self::get_prefix($ratio_value['numerator_unit_exponent']);
				
				$denominator_unit = new MeasuringUnit_Access($ratio_value['denominator_unit_id']);
				$denominator_prefix_array = self::get_prefix($ratio_value['denominator_unit_exponent']);
				
				
				$return_array[$counter]['id'] = $ratio_value['id'];
				$return_array[$counter]['name'] = $numerator_prefix_array[0]."".$numerator_unit->get_name()." per ".$denominator_prefix_array[1]."".$denominator_unit->get_unit_symbol()." (".$numerator_prefix_array[1]."".$numerator_unit->get_unit_symbol()."/".$denominator_prefix_array[1]."".$denominator_unit->get_unit_symbol().")";
				$return_array[$counter]['exponent'] = "";
				$return_array[$counter]['headline'] = false;
				$counter++;
			}
		}
		
		return $return_array;
	}
}