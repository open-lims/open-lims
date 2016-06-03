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
 * Organisation Unit AJAX IO Class
 * @package base
 */
class AdminGeneralAjax
{	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_languages($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{	
		global $user;
		
		if ($user->is_admin())
		{	
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_languages($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key]['symbol'] = "<img src='images/icons/language.png' alt='N' border='0' />";
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
				
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_languages($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_languages();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_currencies($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_currencies($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key]['symbol'] = "<img src='images/icons/currency.png' alt='N' border='0' />";
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
				
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_currencies($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_currencies();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_timezones($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_timezones($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key]['symbol'] = "<img src='images/icons/timezone.png' alt='N' border='0' />";
					
					if ($list_array[$key]['deviation'] > 0)
					{
						$list_array[$key]['deviation'] = "GMT+".$list_array[$key]['deviation'];
					}
					elseif ($list_array[$key]['deviation'] < 0)
					{
						$list_array[$key]['deviation'] = "GMT".$list_array[$key]['deviation'];
					}
					else
					{
						$list_array[$key]['deviation'] = "GTM+/-0";
					}
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
				
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_timezones($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_timezones();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_measuring_units($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_measuring_units($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key]['symbol'] = "<img src='images/icons/measuring_unit.png' alt='N' border='0' />";
					
					switch($list_array[$key]['type']):
					
						case "aa":
							$list_array[$key]['type'] = "anglo-american";
						break;
						
						case "metric":
							$list_array[$key]['type'] = "metric";
						break;
						
						default:
							$list_array[$key]['type'] = "none";
						break;
					
					endswitch;
					
					if (is_numeric($list_array[$key]['min_prefix_exponent']) and $list_array[$key]['min_prefix_exponent'] > 0)
					{
						for ($i=$list_array[$key]['min_prefix_exponent'];$i>=3;$i=$i-3)
						{
							$prefix_array = MeasuringUnit::get_prefix($i, false);
							if (is_array($prefix_array) and count($prefix_array) == 2)
							{
								if ($list_array[$key]['secondary_units'])
								{
									$list_array[$key]['secondary_units'] = $list_array[$key]['secondary_units'].", ".$prefix_array[1]."".$list_array[$key]['unit_symbol'];
								}
								else
								{
									$list_array[$key]['secondary_units'] = $prefix_array[1]."".$list_array[$key]['unit_symbol'];
								}
							}
						}
					}
					
					if (is_numeric($list_array[$key]['max_prefix_exponent']) and $list_array[$key]['max_prefix_exponent'] > 0)
					{
						for ($i=3;$i<=$list_array[$key]['max_prefix_exponent'];$i=$i+3)
						{
							$prefix_array = MeasuringUnit::get_prefix($i, true);
							if (is_array($prefix_array) and count($prefix_array) == 2)
							{
								if ($list_array[$key]['secondary_units'])
								{
									$list_array[$key]['secondary_units'] = $list_array[$key]['secondary_units'].", ".$prefix_array[1]."".$list_array[$key]['unit_symbol'];
								}
								else
								{
									$list_array[$key]['secondary_units'] = $prefix_array[1]."".$list_array[$key]['unit_symbol'];
								}
							}
						}
					}
					
					$list_array[$key]['edit'] = "<a title='edit' style='cursor: pointer;' id='BaseAdminMeasuringUnitEdit".$list_array[$key]['id']."' class='BaseAdminMeasuringUnitEdit'><img src='images/icons/edit.png' alt='D' /></a>";
					
					if (MeasuringUnit::is_deletable($list_array[$key]['id']) === true)
					{
						$list_array[$key]['delete'] = "<a title='delete' style='cursor: pointer;' id='BaseAdminMeasuringUnitDelete".$list_array[$key]['id']."' class='BaseAdminMeasuringUnitDelete'><img src='images/icons/delete.png' alt='D' /></a>";
					}
					else
					{
						$list_array[$key]['delete'] = "";
					}
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
				
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array�
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_measuring_units($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_measuring_units();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $category_id
	 * @param string $name
	 * @param string $min_value
	 * @param string $max_value
	 * @param string $min_prefix_exponent
	 * @param string $max_prefix_exponent
	 * @param string $prefix_calculcation_exponent
	 * @param string $calculation
	 * @param string $type
	 * @return integer
	 */
	public static function add_measuring_unit($category_id, $name, $symbol, $min_value, $max_value, $min_prefix_exponent, $max_prefix_exponent, $prefix_calculation_exponent, $calculation, $type)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$measuring_unit = new MeasuringUnit(null);
			if ($measuring_unit->create($category_id, $name, $symbol, $min_value, $max_value, $min_prefix_exponent, $max_prefix_exponent, $prefix_calculation_exponent, $calculation, $type))
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param integer $id
	 * @param string $category_id
	 * @param string $name
	 * @param string $min_value
	 * @param string $max_value
	 * @param string $min_prefix_exponent
	 * @param string $max_prefix_exponent
	 * @param string $prefix_calculcation_exponent
	 * @param string $calculation
	 * @param string $type
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function set_measuring_unit($id, $category_id, $name, $symbol, $min_value, $max_value, $min_prefix_exponent, $max_prefix_exponent, $prefix_calculation_exponent, $calculation, $type)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id) and $name and $symbol)
			{
				$measuring_unit = new MeasuringUnit($id);
				
				if ($measuring_unit->set_category_id($category_id) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_name($name) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_unit_symbol($symbol) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_min_value($min_value) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_max_value($max_value) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_min_prefix_exponent($min_prefix_exponent) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_max_prefix_exponent($max_prefix_exponent) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_prefix_calculation_exponent($prefix_calculation_exponent) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_calculation($calculation) == false)
				{
					return "0";
				}
				
				if ($measuring_unit->set_type($type) == false)
				{
					return "0";
				}
				
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @return string
	 * @throws BaseEnvironmentMeasuringUnitIDMissingException
	 * @throws BaseUserAccessDeniedException
	 */
	public static function delete_measuring_unit($id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id))
			{
				$measuring_unit = new MeasuringUnit($id);
				if ($measuring_unit->delete() == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new BaseEnvironmentMeasuringUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 */
	public static function get_measuring_unit($id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id))
			{
				$measuring_unit = new MeasuringUnit($id);
				
				$return_array = array();
				
				$return_array[0] = $measuring_unit->get_name();
				$return_array[1] = $measuring_unit->get_category_id();
				$return_array[2] = $measuring_unit->get_type();
				$return_array[3] = $measuring_unit->get_min_prefix_exponent();
				$return_array[4] = $measuring_unit->get_max_prefix_exponent();
				$return_array[5] = $measuring_unit->get_unit_symbol();
				$return_array[6] = $measuring_unit->get_calculation();
				$return_array[7] = $measuring_unit->get_min_value();
				$return_array[8] = $measuring_unit->get_max_value();
				$return_array[9] = $measuring_unit->get_prefix_calculation_exponent();
				
				return json_encode($return_array);
			}
			else
			{
				return "0";
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_measuring_unit_ratios($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_measuring_unit_ratios($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					if (!$list_array[$key]['numerator_exp'])
					{
						$list_array[$key]['numerator_exp'] = "0";
					}
					
					if (!$list_array[$key]['denominator_exp'])
					{
						$list_array[$key]['denominator_exp'] = "0";
					}
					
					$measuring_unit_ratio = new MeasuringUnitRatio($list_array[$key]['id']);
					$list_array[$key]['name'] = $measuring_unit_ratio->get_symbol();
					
					$list_array[$key]['delete'] = "<a href='#' class='BaseAdminMeasuringUnitRatioDelete' id='BaseAdminMeasuringUnitRatioDelete".$list_array[$key]['id']."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
				
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array�
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_measuring_unit_ratios($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_measuring_unit_ratios();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $numerator
	 * @param string $denominator
	 * @return integer
	 */
	public static function add_measuring_unit_ratio($numerator, $denominator)
	{
		if ($numerator and $denominator)
		{
			$numerator_array = explode("-", $numerator, 2);
			if (is_array($numerator_array) and count($numerator_array) === 2)
			{
				if (is_numeric($numerator_array[1]))
				{
					$numerator_unit_id = $numerator_array[0];
					$numerator_unit_exponent = $numerator_array[1];
				}
				else
				{
					$numerator_unit_id = $numerator_array[0];
					$numerator_unit_exponent = 0;
				}
			}
			elseif(is_array($numerator_array) and count($numerator_array) === 1)
			{
				$numerator_unit_id = $numerator_array[0];
				$numerator_unit_exponent = 0;
			}
			
			$denominator_array = explode("-", $denominator, 2);
			if (is_array($denominator_array) and count($denominator_array) === 2)
			{
				if (is_numeric($denominator_array[1]))
				{
					$denominator_unit_id = $denominator_array[0];
					$denominator_unit_exponent = $denominator_array[1];
				}
				else
				{
					$denominator_unit_id = $denominator_array[0];
					$denominator_unit_exponent = 0;
				}
			}
			elseif(is_array($denominator_array) and count($denominator_array) === 1)
			{
				$denominator_unit_id = $denominator_array[0];
				$denominator_unit_exponent = 0;
			}
			
			$measuring_unit_ratio = new MeasuringUnitRatio(null);
			if ($measuring_unit_ratio->create($numerator_unit_id, $numerator_unit_exponent, $denominator_unit_id, $denominator_unit_exponent) !== null)
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			return "0";
		}
	}
	
	/**
	 * @param string $id
	 * @return string
	 */
	public static function delete_measuring_unit_ratio($id)
	{
		if (is_numeric($id))
		{
			$measuring_unit_ratio = new MeasuringUnitRatio($id);
			if ($measuring_unit_ratio->delete() == true)
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			return "0";
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_measuring_unit_categories($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_measuring_unit_categories($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					if (MeasuringUnitCategory::is_deletable($list_array[$key]['id']) === true)
					{
						$list_array[$key]['delete'] = "<a title='delete' style='cursor: pointer;' id='BaseAdminMeasuringUnitCategoryDelete".$list_array[$key]['id']."' class='BaseAdminMeasuringUnitCategoryDelete'><img src='images/icons/delete.png' alt='D' /></a>";
					}
					else
					{
						$list_array[$key]['delete'] = "";
					}
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
				
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array�
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_measuring_unit_categories($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_measuring_unit_categories();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $name
	 * @return integer
	 */
	public static function add_measuring_unit_category($name)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$measuring_unit_category = new MeasuringUnitCategory(null);
			if ($measuring_unit_category->create($name))
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @return string
	 * @throws BaseEnvironmentMeasuringUnitCategoryIDMissingException
	 * @throws BaseUserAccessDeniedException
	 */
	public static function delete_measuring_unit_category($id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id))
			{
				$measuring_unit_category = new MeasuringUnitCategory($id);
				if ($measuring_unit_category->delete() == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new BaseEnvironmentMeasuringUnitCategoryIDMissingException();	
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_paper_sizes($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = Environment_Wrapper::list_paper_sizes($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
					
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key]['symbol'] = "<img src='images/icons/text.png' alt='' />";
					
					if ($value['standard'] === true)
					{
						$list_array[$key]['name'] = $list_array[$key]['name']." <img src='images/icons/status_ok.png' alt='Standard' />";
					}
					
					$list_array[$key]['width'] = $list_array[$key]['width']." mm";
					$list_array[$key]['height'] = $list_array[$key]['height']." mm";
					$list_array[$key]['margin_left'] = $list_array[$key]['margin_left']." mm";
					$list_array[$key]['margin_right'] = $list_array[$key]['margin_right']." mm";
					$list_array[$key]['margin_top'] = $list_array[$key]['margin_top']." mm";
					$list_array[$key]['margin_bottom'] = $list_array[$key]['margin_bottom']." mm";
					
					$list_array[$key]['edit'] = "<a href='#' class='BaseAdminPaperSizeEdit' id='BaseAdminPaperSizeEdit".$list_array[$key]['id']."'><img src='images/icons/edit.png' alt='' style='border: 0;' /></a>";
					
					if ($value['base'] === false)
					{
						$list_array[$key]['delete'] = "<a href='#' class='BaseAdminPaperSizeDelete' id='BaseAdminPaperSizeDelete".$list_array[$key]['id']."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
					}
				}
			}
			else
			{
				$list_request->empty_message("<span class='italic'>No results found!</span>");
			}
			
			$list_request->set_array($list_array);
		
			return $list_request->get_page($page);
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_paper_sizes($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_paper_sizes();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $name
	 * @param string $width
	 * @param string $height
	 * @param string $margin_left
	 * @param string $margin_right
	 * @param string $margin_top
	 * @param string $margin_bottom
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 */
	public static function add_paper_size($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$paper_size = new PaperSize(null);
			if ($paper_size->create($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom) == true)
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseEnvironmentPaperSizeIDMissingException
	 */
	public static function get_paper_size($id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id))
			{
				$paper_size = new PaperSize($id);
				
				$return_array = array();
				
				$return_array[0] = $paper_size->get_base();
				$return_array[1] = $paper_size->get_name();
				$return_array[2] = $paper_size->get_width();
				$return_array[3] = $paper_size->get_height();
				$return_array[4] = $paper_size->get_margin_left();
				$return_array[5] = $paper_size->get_margin_right();
				$return_array[6] = $paper_size->get_margin_top();
				$return_array[7] = $paper_size->get_margin_bottom();
				
				return json_encode($return_array);
			}
			else
			{
				throw new BaseEnvironmentPaperSizeIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @param string $name
	 * @param string $width
	 * @param string $height
	 * @param string $margin_left
	 * @param string $margin_right
	 * @param string $margin_top
	 * @param string $margin_bottom
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseEnvironmentPaperSizeIDMissingException
	 */
	public static function set_paper_size($id, $name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id) and $name and is_numeric($width) and is_numeric($height) and is_numeric($margin_left) and is_numeric($margin_right) and is_numeric($margin_top) and is_numeric($margin_bottom))
			{
				$paper_size = new PaperSize($id);
				
				if ($paper_size->set_name($name) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_width($width) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_height($height) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_margin_left($margin_left) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_margin_right($margin_right) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_margin_top($margin_top) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_margin_bottom($margin_bottom) == false)
				{
					return "0";
				}
				
				return "1";
			}
			else
			{
				throw new BaseEnvironmentPaperSizeIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseEnvironmentPaperSizeIDMissingException
	 */
	public static function delete_paper_size($id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id))
			{
				$paper_size = new PaperSize($id);
				if ($paper_size->delete() == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new BaseEnvironmentPaperSizeIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
}
?>