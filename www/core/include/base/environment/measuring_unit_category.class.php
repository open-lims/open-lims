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
// require_once("interfaces/measuring_unit_category.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/measuring_unit_category.access.php");
}

/**
 * Measuring Unit Category Class
 * @package base
 */
class MeasuringUnitCategory // implements MeasuringUnitCategoryInterface
{
	private $measuring_unit_category;
	private $measuring_unit_category_id;
	
	function __construct($measuring_unit_category_id)
	{
		if (is_numeric($measuring_unit_category_id))
		{
			if (MeasuringUnitCategory_Access::exist_id($measuring_unit_category_id) == true)
			{
				$this->measuring_unit_category_id = $measuring_unit_category_id;
   	   			$this->measuring_unit_category = new MeasuringUnitCategory_Access($measuring_unit_category_id);
			}
			else
			{
				throw new BaseEnvironmentMeasuringUnitCategoryNotFoundException();
			}
    	}
    	else
    	{
    		$this->measuring_unit_category_id = null;
   	   		$this->measuring_unit_category = new MeasuringUnitCategory_Access(null);
    	}
	}
	
	public function create($name)
	{
		if($name)
		{
			if ($this->measuring_unit_category)
			{
				return $this->measuring_unit_category->create($name);
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
	
	public function delete()
	{
		if ($this->measuring_unit_category_id and $this->measuring_unit_category)
		{
			return $this->measuring_unit_category->delete();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return array
	 */
	public static function list_categories()
	{
		return MeasuringUnitCategory_Access::list_entries();
	}
	
	/**
 	 * @todo is it in use?
     * @param integer $measuring_unit_category_id
     * @return bool
     */
    public static function is_deletable($measuring_unit_category_id)
    {
    	return MeasuringUnitCategory_Access::is_deletable($measuring_unit_category_id);
    }
}
?>