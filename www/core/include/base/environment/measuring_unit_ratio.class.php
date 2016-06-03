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
 * 
 */
// require_once("interfaces/measuring_unit_ratio.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/measuring_unit.access.php");
	require_once("access/measuring_unit_ratio.access.php");
}

/**
 * Measuring Unit Ratio Class
 * @package base
 */
class MeasuringUnitRatio //implements MeasuringUnitRatioInterface
{
	private $measuring_unit_ratio_id;
	private $measuring_unit_ratio;
	private $measuring_unit_ratio_numerator;
	private $measuring_unit_ratio_denominator;
	
	/**
	 * @see MeasuringUnitRatioInterface::__construct()
	 * @param integer $measuring_unit_ratio_id
	 * @throws BaseEnvironmentMeasuringUnitRatioNotFoundException
	 */
	function __construct($measuring_unit_ratio_id)
	{
		if (is_numeric($measuring_unit_ratio_id))
		{
			if (MeasuringUnitRatio_Access::exist_id($measuring_unit_ratio_id) == true)
			{
				$this->measuring_unit_ratio_id = $measuring_unit_ratio_id;
   	   			$this->measuring_unit_ratio = new MeasuringUnitRatio_Access($measuring_unit_ratio_id);
   	   			$this->measuring_unit_ratio_numerator = new MeasuringUnit_Access($this->measuring_unit_ratio->get_numerator_unit_id());
   	   			$this->measuring_unit_ratio_denominator = new MeasuringUnit_Access($this->measuring_unit_ratio->get_denominator_unit_id());
			}
			else
			{
				throw new BaseEnvironmentMeasuringUnitRatioNotFoundException();
			} 
    	}
    	else
    	{
    		$this->measuring_unit_ratio_id = null;
   	   		$this->measuring_unit_ratio = new MeasuringUnitRatio_Access(null);
   	   		$this->measuring_unit_ratio_numerator = null;
   	   		$this->measuring_unit_ratio_denominator = null;
    	}
	}
	
	/**
	 * @see MeasuringUnitRatioInterface::__destruct()
	 */
	function __destruct()
	{
		unset($this->measuring_unit_ratio_id);
		unset($this->measuring_unit_ratio);
		unset($this->measuring_unit_ratio_numerator);
		unset($this->measuring_unit_ratio_denominator);
	}
	
	/**
	 * @see MeasuringUnitRatioInterface::create()
	 * @param integer $numerator_id
	 * @param integer $numerator_exponent
	 * @param integer $denominator_id
	 * @param integer $denominator_exponent
	 * @return integer
	 */
	public function create($numerator_unit_id, $numerator_unit_exponent, $denominator_unit_id, $denominator_unit_exponent)
	{
		if (is_numeric($numerator_unit_id) and is_numeric($numerator_unit_exponent) and is_numeric($denominator_unit_id) and is_numeric($denominator_unit_exponent))
		{
			return $this->measuring_unit_ratio->create($numerator_unit_id, $numerator_unit_exponent, $denominator_unit_id, $denominator_unit_exponent);
		}
		else 
		{
			return null;
		}
	}
	
	/**
	 * @see MeasuringUnitRatioInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		if ($this->measuring_unit_ratio_id and $this->measuring_unit_ratio)
		{
			return $this->measuring_unit_ratio->delete();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see MeasuringUnitRatioInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->measuring_unit_ratio_id and $this->measuring_unit_ratio)
		{
			$numerator_exponent = $this->measuring_unit_ratio->get_numerator_unit_exponent();
			
			if ($numerator_exponent < 0)
			{
				$unit_exponent = $numerator_exponent*-1;
				$numerator_unit_prefix = MeasuringUnit::get_prefix($unit_exponent, false);
			}
			else
			{
				$numerator_unit_prefix = MeasuringUnit::get_prefix($numerator_exponent, true);
			}
			
			$denominator_exponent = $this->measuring_unit_ratio->get_denominator_unit_exponent();
			
			if ($denominator_exponent < 0)
			{
				$unit_exponent = $denominator_exponent*-1;
				$denominator_unit_prefix = MeasuringUnit::get_prefix($unit_exponent, false);
			}
			else
			{
				$denominator_unit_prefix = MeasuringUnit::get_prefix($denominator_exponent, true);
			}
			
			return $numerator_unit_prefix[0]."".$this->measuring_unit_ratio_numerator->get_name()." per ".$denominator_unit_prefix[0]."".$this->measuring_unit_ratio_denominator->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see MeasuringUnitRatioInterface::get_symbol()
	 * @return string
	 */
	public function get_symbol()
	{
		if ($this->measuring_unit_ratio_id and $this->measuring_unit_ratio)
		{
			$numerator_exponent = $this->measuring_unit_ratio->get_numerator_unit_exponent();
			
			if ($numerator_exponent < 0)
			{
				$unit_exponent = $numerator_exponent*-1;
				$numerator_unit_prefix = MeasuringUnit::get_prefix($unit_exponent, false);
			}
			else
			{
				$numerator_unit_prefix = MeasuringUnit::get_prefix($numerator_exponent, true);
			}
			
			$denominator_exponent = $this->measuring_unit_ratio->get_denominator_unit_exponent();
			
			if ($denominator_exponent < 0)
			{
				$unit_exponent = $denominator_exponent*-1;
				$denominator_unit_prefix = MeasuringUnit::get_prefix($unit_exponent, false);
			}
			else
			{
				$denominator_unit_prefix = MeasuringUnit::get_prefix($denominator_exponent, true);
			}
			
			return $numerator_unit_prefix[1]."".$this->measuring_unit_ratio_numerator->get_unit_symbol()."/".$denominator_unit_prefix[1]."".$this->measuring_unit_ratio_denominator->get_unit_symbol();
		}
		else
		{
			return null;
		}
	}
}